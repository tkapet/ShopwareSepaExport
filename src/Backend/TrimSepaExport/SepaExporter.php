<?php
/**
 * SepaExporter.php
 *
 * Copyright © 2014 by Trimension® ITS, Juergen Werner
 *
 * This plugin is free software; you can redistribute it and/or modify it under the terms of the 
 * GNU Lesser General Public License as published by the Free Software Foundation; either 
 * version 2.1 of the License, or any later version.
 * This plugin library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License along with this library; 
 * if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * All modifications must be marked as not to be the original version; do not remove the copyright
 * and licence information placed in this header; with use of this software you accept the given license terms.
 * 
 * @author		Juergen Werner (werner@trimension.de)
 * @copyright	Trimension ITS, Juergen Werner
 * @license		LGPL-3.0
 * @version		1.1.0
 */
final class SepaExporter {
	
	private $config;
	private $util;
	private $orderStateManager;
	
	/**
	 * Sepa Exporter DefaultConstructor
	 */
	public function __construct($cfg) {
		$this->config = $cfg;
		$this->util = new Utilities($cfg);
		$this->orderStateManager = new OrderStateManager($cfg);
	}
	
	/**
	 * Execute the Export Process
	 * 
	 * @param Object $order The Order Object to be exported, if null, all still not imported Orders will be exported
	 */
	public function exportOrder($order = null) {
		try {
			if(is_null($order)) return $this->exportOrderCron();
			else return $this->exportSingleOrder($order);
		} catch(Exception $e) {
			$this->util->logSepa('[ERROR] Exception while processing Sepa ' . (is_null($order) ? 'Cron' : 'Order') . ' Export. ');
			$errmsg = '[ERROR] Code=' . $e->getCode() . ', File=' . $e->getFile() . ', Line=' . $e->getLine() . ', Msg=' . $e->getMessage();
			$this->util->logSepa($errmsg);
			return Messages::get(Messages::EXPORT_ERROR, $errmsg);
		}
	}
	
	/**
	 * internal private method
	 * export single order
	 */
	private function exportSingleOrder($order) {
		if(!$this->util->checkOrder($order)) return false;
		
		$initRecord = $this->createInitRecord();
		$orderRecord = $this->createOrderRecord($order);
		if($orderRecord === false) return false;
		
		$sepa = new SepaWriter($this->config, $initRecord);
		$protocol = $sepa->createDocument($orderRecord);
		if($protocol === false) return false;
		
		$errors = $protocol->errors();
		if($errors === 0) {
			$fileName = $sepa->writeFile();
			if($fileName !== false) {
				$this->util->logSepa("[INFO] Sepa PaymentData Export completed for Order: " . $order->sOrderNumber . " File:" . $fileName);
				$protocol->setComment($fileName);
			}
		} else {
			$this->util->logSepa("[ERROR] Sepa Single Order Export completed with Errors for Order " . $order->sOrderNumber . ": Mail sent (if active)");
		}
		$protocol->writeProtocol();
		$this->orderStateManager->setOrderState($protocol->getProtocol());
		$protocol->sendEmail($errors);
		
		return true;
	}
	
	/**
	 * internal private method
	 * export all batch-selected orders
	 */
	private function exportOrderCron() {
		$dbMan = new DatabaseManager($this->config);
		$result = $dbMan->getOrders();
		if(empty($result)) return Messages::CRON_EMPTY;
	
		$initRecord = $this->createInitRecord();
		$sepa = new SepaWriter($this->config, $initRecord);
		$protocol = $sepa->createDocument($result);
		if($protocol === false) return Messages::CRON_ERROR_PROCESS;

		$processed = $protocol->count();
		$errors = $protocol->errors();
		
		$fileName = null;
		if($processed > $errors) {
			$fileName = $sepa->writeFile();
			if($fileName === false) return Messages::CRON_ERROR_WRITER;
			$this->util->logSepa("[INFO] Sepa Batch: File written: " . $fileName);
		} else {
			$this->util->logSepa("[WARN] Sepa Batch Order Export completed with Errors: processed: $processed, errors: $errors : No File created.");
		}

		if(!is_null($fileName)) $protocol->setComment($fileName);
		$protocol->writeProtocol();
		$this->orderStateManager->setOrderState($protocol->getProtocol());
		
		$protocol->sendEmail($errors);
		if($errors > 0) {
			$this->util->logSepa("[WARN] Sepa Batch Order Export completed with Errors : processed: $processed, errors: $errors");
			return Messages::get(Messages::CRON_INCOMPLETE, ($errors));
		} else {
			return Messages::get(Messages::CRON_SUCCESS, $processed);
		}
	}
	
	/**
	 * internal private method
	 * create order record
	 */
	private function createOrderRecord($order) {
		$orderNo = $order->sOrderNumber;
		$user = $order->sUserData['billingaddress'];
		$now = new DateTime();
		
		$userId = $user['userID'];
		$dbman = new DatabaseManager($this->config);
		$paymentData = $dbman->getPaymentData($userId, $orderNo);
		if(!$paymentData === false) {
			return array(
					'orderNo' => $orderNo,
					'orderDate' => $now->format('Y-m-d'),
					'amount' => $order->sAmount,
					'currency' => $paymentData['currency'],
					'customerNo' => $user['customernumber'],
					'customerName' => $user['firstname'] . ' ' . $user['lastname'],
					'iban' => $paymentData['iban'],
					'bic' => $paymentData['bic'],
					'mandateRef' => $orderNo
			);
		} else {
			$this->util->logSepa("[ERROR] No paymentData found for Order: $orderNo");
			return false;
		}
	}
	
	/**
	 * internal private method
	 * create initialization array
	 */
	private function createInitRecord() {
		$msgId = $this->util->createMessageId();
		$path = Shopware()->DocPath() . $this->config->getConfig('filePath');
		$creditorId = $this->config->getConfig('creditorId');
		if(empty($creditorId)) $creditorId = Shopware()->Config()->get('sepaSellerId');
		$company = $this->config->getConfig('initiator');
		if(empty($company)) $company = Shopware()->Config()->get('sepaCompany');
		$dbman = new DatabaseManager($this->config);
		$usage = $dbman->getSnippet(Constants::USAGE_TEXT_NS, Constants::USAGE_TEXT_NAME);
		return array(
				'messageId' => $msgId,
				'paymentInfoId' => $msgId,
				'company' => $company,
				'companyIban' => $this->config->getConfig('initiatorIban'),
				'companyBic' => $this->config->getConfig('initiatorBic'),
				'creditorId' => $creditorId,
				'leadTime' => intval($this->config->getConfig('preliminary')),
				'filePath' => $path,
				'express' => (bool)($this->config->getConfig('expressSepa')),
				'txUsageMsg' => $usage
		);
	}
	
}
