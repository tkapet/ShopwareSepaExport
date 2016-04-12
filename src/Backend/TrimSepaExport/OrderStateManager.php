<?php
/**
 * OrderStateManager.php
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
use Shopware\Models\Order\Order as Order,
	Shopware\Models\Order\Status as Status;

final class OrderStateManager {
	
	private $config = null;
	private $util;
	private $dbman;
	
	/**
	 * Default Constructor
	 */
	public function __construct($cfg) {
		$this->config = $cfg;
		$this->util = new Utilities($cfg);
		$this->dbman = new DatabaseManager($cfg);
	}
	
	/**
	 * set the order state from the configuration file for all
	 * exported orders
	 * 
	 * @param array $prot
	 */
	public function setOrderState($prot) {
		if(empty($prot) || !is_array($prot)) return;
		foreach ($prot as $orderNo => $p) {
			$orderId = $p['order_id'];
			$success = $p['status'] === ExportStatus::OK ? TRUE : FALSE;
			$this->setSingleOrderState($orderId, $success);
		}
	}
	
	/**
	 * Set the Order state from Configuration for a given single order
	 * depending on the success-state and sends the status email if configured
	 * 
	 * @param int $orderId
	 * @param boolean $success
	 * @return boolean
	 */
	public function setSingleOrderState($orderId, $success) {
		if(empty($orderId)) return FALSE;
		
		$finalOrderStateId = ($success === TRUE) 
			? $this->config->getOrderSuccessState()
			: $this->config->getOrderErrorState();
		
		if(empty($finalOrderStateId)) return TRUE;
		$finalOrderState = $this->getStatus($finalOrderStateId);
		
		if(empty($finalOrderState) || ($finalOrderState->getGroup() !== Status::GROUP_PAYMENT)) {
			if($success)
				$this->util->errorLog('Configuration Error: invalid Status: Order/orderSuccessState');
			else 
				$this->util->errorLog('Configuration Error: invalid Status: Order/orderErrorState');
			return FALSE;
		}
		
		try {
			$order = $this->getOrder($orderId);
			if(empty($order)) {
				$this->util->errorLog('OrderStateManager: wrong Order Id');
				return FALSE;
			}
			
			$sOrder = Shopware()->Modules()->Order();
			$sOrder->setPaymentStatus($order->getId(), $finalOrderStateId, false, Messages::STATUS_HISTORY_COMMENT);
			
			$sendMail = $this->config->getSendStateChangeMail();
			if($sendMail === FALSE) return TRUE;
			$mail = $this->createStatusMail($orderId, $finalOrderStateId);	
			if($mail !== false) $mail->send();			
			
			return TRUE;
		} catch(Exception $e) {
			$this->util->logSepa("[ERROR] setting order-status : OrderID = $orderId, Status = $finalOrderStateId : " . $e->getMessage());
			return FALSE;
		}
	}
	
    /*
     * Create status mail
     */
    private function createStatusMail($orderId, $statusId) {
        $statusId = (int) $statusId;
        $orderId  = (int) $orderId;
        $templateName = Constants::EMAIL_TEMPLATE_STAT . $statusId;

        if(empty($orderId) || !is_numeric($statusId)) return FALSE;

        $order = $this->dbman->getOrder($orderId);
        $orderDetails = $this->dbman->getOrderDetails($orderId);
        $user = $this->dbman->getOrderCustomer($orderId);

        if ($order === false || $orderDetails === false || $user === false) {
        	$this->util->logSepa("[ERROR] Status-Mail Creation: Data selection error for Order: $orderId");
        	return false;
        }
        
        $mailTo = trim($user['email']);
        if(empty($mailTo)) {
        	$this->util->logSepa("[ERROR] Status-Mail Creation: User-Email is empty for Order: $orderId");
        	return false;
        }

        $repository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $shopId = is_numeric($order['language']) ? $order['language'] : $order['subshopID'];
        if(!empty($shopId)) {
        	$shop = $repository->getActiveById($shopId);
        	$shop->registerResources(Shopware()->Bootstrap());
        } else {
        	$this->util->errorLog("OrderStateManager: ShopID is empty (Order:$orderId");
        	return false;
        }

        $mailModel = Shopware()->Models()->getRepository('Shopware\Models\Mail\Mail')->findOneBy(
            array('name' => $templateName)
        );

        if (!$mailModel) return false;

        $context = array(
            'sOrder'        => $order,
            'sOrderDetails' => $orderDetails,
            'sUser'         => $user,
        );

        $mail = Shopware()->TemplateMail()->createMail($templateName, $context, $shop);
        $mail->clearSubject();
        $mail->setSubject($mail->getPlainSubject());
       	$mail->setBodyText($mail->getPlainBodyText());
        $mail->addTo($mailTo);

        return $mail;
    }
		
	/*
	 * gets the order with the given id from database
	 */
	private function getOrder($id) {
		return Shopware()->Models()->find('Shopware\Models\Order\Order', $id);
	}
	
	/*
	 * gets the status object for the given status id
	 */
	private function getStatus($id) {
		return Shopware()->Models()->find('Shopware\Models\Order\Status', $id);
	}
}
