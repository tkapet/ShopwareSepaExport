<?php
/**
 * ProtocolManager.php - Protocol Manager
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
final class ProtocolManager {
	
	const PT_ORDERID_COL = 'order_id';
	const PT_ORDERNO_COL = 'order_no';
	const PT_CUSTOMER_COL = 'customer_id';
	const PT_MESSAGE_COL = 'message_id';
	const PT_MANDATE_COL = 'mandate_ref';
	const PT_COMMENT_COL = 'comment';
	const PT_STATUS_COL = 'status';
	const PT_EXPORT_COL = 'export_ts';
	
	private $db;
	private $data;
	private $util;
	private $config;
	
	/**
	 * Default Constructor
	 */
	public function __construct($cfg) {
		$this->config = $cfg;
		$this->util = new Utilities($cfg);
		$this->db = Shopware()->Db();
		$this->data = array();
	}
	
	/**
	 * Adds new Protocol Record
	 * @param unknown $orderNo
	 * @param unknown $customerId
	 * @param unknown $messageId
	 * @param unknown $mandateRef
	 */
	public function addProtocol($orderId, $orderNo, $customerId, $messageId, $mandateRef, $status, $comment = '') {
		$exportTs = new DateTime();
		$this->data[$orderNo] = array(
				self::PT_ORDERID_COL => $orderId,
				self::PT_ORDERNO_COL => $orderNo,
				self::PT_CUSTOMER_COL => $customerId,
				self::PT_MESSAGE_COL => $messageId,
				self::PT_MANDATE_COL => $mandateRef,
				self::PT_EXPORT_COL => $exportTs->format('Y-m-d H:i:s'),
				self::PT_STATUS_COL => $status,
				self::PT_COMMENT_COL => $comment
				);
	}
	
	/**
	 * returns the number of processed records
	 * @return number
	 */
	public function count() {
		return count($this->data);
	}
	
	/** 
	 * returns the number of invalid not correctly processed records
	 */
	public function errors() {
		$error = 0;
		foreach ($this->data as $orderNo => $prot) {
			if($prot['status'] === ExportStatus::ERROR) $error++;
		}
		return $error;
	}
	
	/**
	 * sets the comment-field with filename for all successfully exported protocol entries
	 * @param unknown $filename
	 */
	public function setComment($filename) {
		foreach ($this->data as $orderNo => $prot) {
			if($prot['status'] === ExportStatus::OK) {
				$prot[self::PT_COMMENT_COL] = $filename;
				$this->data[$orderNo] = $prot;
			}
		}
	}
	
	/**
	 * writes all exported Data including status to the Database
	 */
	public function writeProtocol() {
		foreach ($this->data as $orderNo => $prot) {
			$this->saveRecord($orderNo, $prot);
		}
	}
	
	/**
	 * returns the internal protocol array
	 * @return Ambigous <multitype:, unknown>
	 */
	public function getProtocol() {
		return $this->data;
	}
	
	/**
	 * internal private method
	 * Save the protocol record
	 */
	private function saveRecord($orderNo, $protocol) {
		$record = $this->loadProtocol($orderNo);
		if(empty($record)) {
			$p = array(
				self::PT_ORDERID_COL => $protocol[self::PT_ORDERNO_COL],
				self::PT_CUSTOMER_COL => $protocol[self::PT_CUSTOMER_COL],
				self::PT_MESSAGE_COL => $protocol[self::PT_MESSAGE_COL],
				self::PT_MANDATE_COL => $protocol[self::PT_MANDATE_COL],
				self::PT_EXPORT_COL => $protocol[self::PT_EXPORT_COL],
				self::PT_STATUS_COL => $protocol[self::PT_STATUS_COL],
				self::PT_COMMENT_COL => $protocol[self::PT_COMMENT_COL]
			);
			$this->db->insert(Constants::PROTOCOL_TABLE, $p);
		} else {
			$update = array(
				self::PT_MESSAGE_COL => $protocol[self::PT_MESSAGE_COL],
				self::PT_COMMENT_COL => $protocol[self::PT_COMMENT_COL],
				self::PT_STATUS_COL => $protocol[self::PT_STATUS_COL],
				self::PT_EXPORT_COL => $protocol[self::PT_EXPORT_COL]
			);
			$where['order_id = ?'] = $orderNo;
			$this->db->update(Constants::PROTOCOL_TABLE, $update, $where);
		}
	}
	
	/**
	 * internal private method
	 * loads Protocol Entry from Database
	 */
	private function loadProtocol($orderId) {
		return $this->db->fetchOne(Constants::QUERY_PROTOCOL, array('pono' => $orderId));
	}

	/**
	 * Sends Protocol to the Admin if errors are detected
	 */
	public function sendEmail($errors) {
		$send = $this->config->getConfig('mailProtocol');
		if($send === MailProtocol::NEVER) return;
		if($send === MailProtocol::ONERROR && $errors === 0) return;
		
		$context = array();
		$context['sProtocol'] = $this->data; 
		try {
			$admin = Shopware()->Config()->get('mail');
			$mail = Shopware()->TemplateMail()->createMail(Constants::EMAIL_TEMPLATE_PROT, $context);
			$mail->addTo($admin);
			$mail->send();
		} catch(Exception $e) {
			$util->logSepa("[ERROR] send protocol mail : " . $e->getMessage());
		}
	}
}
