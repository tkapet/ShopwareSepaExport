<?php
/**
 * DbHelper.php
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
final class DatabaseManager {
	private $db;
	private $util;
	private $config;
	
	/**
	 * Default Constructor
	 */
	public function __construct($cfg) {
		$this->config = $cfg;
		$this->db = Shopware()->Db();
		$this->util = new Utilities($cfg);
	}
	
	/**
	 * Loads the selected Orders for Cron-Job Exporter
	 */
	public function getOrders() {
		$select = $this->createOrderQuery();
		$stmt = $this->db->query($select);
		return $stmt->fetchAll();
	}
	
	/**
	 * Loads a single Order with additional Information Set
	 * (Attributes for Status Email)
	 * 
	 * @param Integer $orderId
	 * @return boolean|Array
	 */
	public function getOrder($orderId) {
		if(empty($orderId)) return false;
		
		$select =  $this->db->select();
		$select->from(array('o' => 's_order'), array());
		$select->join(array('s' => 's_core_states'), 'o.status = s.id', array());
		$select->join(array('c' => 's_core_states'), 'o.cleared = c.id', array());
		$select->join(array('p' => 's_core_paymentmeans'), 'o.paymentID = p.id', array());
		$select->join(array('d' => 's_premium_dispatch'), 'o.dispatchID = d.id', array());
		$select->join(array('cu' => 's_core_currencies'), 'o.currency = cu.currency', array());
		$select->where('o.id = ?', $orderId);
		$select->columns( array (
				'id' => 'o.id',
				'orderID' => 'o.id',
				'ordernumber' => 'o.ordernumber',
				'order_number' => 'o.ordernumber',
				'userID' => 'o.userID',
				'customerID' => 'o.userID',
				'invoice_amount' => 'o.invoice_amount',
				'invoice_amount_net' => 'o.invoice_amount_net',
				'invoice_shipping' => 'o.invoice_shipping',
				'invoice_shipping_net' => 'o.invoice_shipping_net',
				'ordertime' => 'o.ordertime',
				'status' => 'o.status',
				'statusID' => 'o.status',
				'cleared' => 'o.cleared',
				'clearedID' => 'o.cleared',
				'paymentID' => 'o.paymentID',
				'transactionID' => 'o.transactionID',
				'comment' => 'o.comment',
				'customercomment' => 'o.customercomment',
				'net' => 'o.net',
				'netto' => 'o.net',
				'partnerID' => 'o.partnerID',
				'temporaryID' => 'o.temporaryID',
				'referer' => 'o.referer',
				'cleareddate' => 'o.cleareddate',
				'cleared_date' => 'o.cleareddate',
				'trackingcode' => 'o.trackingcode',
				'language' => 'o.language',
				'currency' => 'o.currency',
				'currencyFactor' => 'o.currencyFactor',
				'subshopID' => 'o.subshopID',
				'dispatchID' => 'o.dispatchID',
				'currencyID' => 'cu.id',
				'cleared_description' => 'c.description',
				'status_description' => 's.description',
				'payment_description' => 'p.description',
				'dispatch_description' => 'd.name',
				'currency_description' => 'cu.name',
				'dispatchID' => 'o.dispatchID',
				'dispatchID' => 'o.dispatchID',
				'dispatchID' => 'o.dispatchID',
				'dispatchID' => 'o.dispatchID'
		));
				
		$stmt = $this->db->query($select);
		$order = $stmt->fetchAll();
		if(empty($order) || !is_array($order)) return false;
		$order = $order[0];
					
		$select =  $this->db->select();
		$select->from(array('oa' => 's_order_attributes'));
		$select->where('oa.orderID = ?', $orderId);
		$stmt = $this->db->query($select);
		$attributes = $stmt->fetchAll();
		$attributes = $attributes[0];
		
        unset($attributes['id']);
		unset($attributes['orderID']);
		$order['attributes'] = $attributes;
	
		return $order;
	}
	
	/**
	 * Loads the Order Details for a given OrderID
	 * 
	 * @param int $orderId
	 * @return boolean|Array
	 */
	public function getOrderDetails($orderId) {
		if(empty($orderId)) return false;
		
		$select =  $this->db->select();
		$select->from(array('od' => 's_order_details'), array());
		$select->join(array('tx' => 's_core_tax'), 'tx.id = od.taxID', array());
		$select->where('od.orderID = ?', $orderId);
		$select->columns( array (
				'orderdetailsID' => 'od.id',
				'orderID' => 'od.orderID',
				'ordernumber' => 'od.ordernumber',
				'articleID' => 'od.articleID',
				'articleordernumber' => 'od.articleordernumber',
				'price' => 'od.price',
				'quantity' => 'od.quantity',
				'name' => 'od.name',
				'status' => 'od.status',
				'shipped' => 'od.shipped',
				'shippedgroup' => 'od.shippedgroup',
				'releasedate' => 'od.releasedate',
				'modus' => 'od.modus',
				'esdarticle' => 'od.esdarticle',
				'taxID' => 'od.taxID',
				'tax' => 'tx.tax',
				'tax_rate' => 'od.tax_rate',
				'esd' => 'od.esdarticle'
		));
		
		$stmt = $this->db->query($select);
		$details = $stmt->fetchAll();
		if(empty($details) || !is_array($details)) return false;

		foreach ($details as &$orderDetail) {
            $attributes = $this->getOrderDetailAttributes($orderDetail['orderdetailsID']);
            unset($attributes['id']);
            unset($attributes['detailID']);
            $orderDetail['attributes'] = $attributes;
        }
		return $details;	
	}
	
	/*
	 * loads the order details attributes to complete the order details
	 */
	private function getOrderDetailAttributes($detailId) {
		$select =  $this->db->select();
		$select->from(array('oda' => 's_order_details_attributes'));
		$select->where('oda.detailID = ?', $detailId);
		
		$stmt = $this->db->query($select);
		$attributes = $stmt->fetchAll();
		return $attributes[0];
	}
	
	/**
	 * Loads the OrderCustomer with additional Information for a given Order
	 * (set for Status Email Templates)
	 * 
	 * @param int $orderId
	 * @return boolean|array
	 */
	public function getOrderCustomer($orderId) {
		if(empty($orderId)) return false;
		
		$select =  $this->db->select();
		$select->from(array('b' => 's_order_billingaddress'), array());
		$select->join(array('s' => 's_order_shippingaddress'), 's.orderID = b.orderID', array());
		$select->join(array('ub' => 's_user_billingaddress'), 'ub.userID = b.userID', array());
		$select->join(array('u' => 's_user'), 'b.userID = u.id');
		$select->join(array('bc' => 's_core_countries'), 'bc.id = b.countryID', array());
		$select->join(array('sc' => 's_core_countries'), 'sc.id = s.countryID', array());
		$select->join(array('g' => 's_core_customergroups'), 'u.customergroup = g.groupkey', array());
		$select->join(array('bca' => 's_core_countries_areas'), 'bc.areaID = bca.id', array());
		$select->join(array('sca' => 's_core_countries_areas'), 'sc.areaID = sca.id', array());
		$select->join(array('ba' => 's_order_billingaddress_attributes'), 'b.id = ba.billingID', array());
		$select->join(array('sa' => 's_order_shippingaddress_attributes'), 's.id = sa.shippingID', array());
		$select->where('b.orderID = ?', $orderId);
		$select->columns( array (
				'billing_company' => 'b.company',
				'billing_department' => 'b.department',
				'billing_salutation' => 'b.salutation',
				'customernumber' => 'ub.customernumber',
				'billing_firstname' => 'b.firstname',
				'billing_lastname' => 'b.lastname',
				'billing_street' => 'b.street',
				'billing_streetnumber' => 'b.streetnumber',
				'billing_zipcode' => 'b.zipcode',
				'billing_city' => 'b.city',
				'phone' => 'b.phone',
				'billing_phone' => 'b.phone',
				'fax' => 'b.fax',
				'billing_fax' => 'b.fax',
				'billing_countryID' => 'b.countryID',
				'billing_stateID' => 'b.stateID',
				'billing_country' => 'bc.countryname',
				'billing_countryiso' => 'bc.countryiso',
				'billing_countryarea' => 'bca.name',
				'billing_countryen' => 'bc.countryen',
				'ustid' => 'b.ustid',
				'billing_text1' => 'ba.text1',
				'billing_text2' => 'ba.text2',
				'billing_text3' => 'ba.text3',
				'billing_text4' => 'ba.text4',
				'billing_text5' => 'ba.text5',
				'billing_text6' => 'ba.text6',
				'orderID' => 'b.orderID',
				'shipping_company' => 's.company',
				'shipping_department' => 's.department',
				'shipping_salutation' => 's.salutation',
				'shipping_firstname' => 's.firstname',
				'shipping_lastname' => 's.lastname',
				'shipping_street' => 's.street',
				'shipping_streetnumber' => 's.streetnumber',
				'shipping_zipcode' => 's.zipcode',
				'shipping_city' => 's.city',
				'shipping_stateID' => 's.stateID',
				'shipping_countryID' => 's.countryID',
				'shipping_country' => 'sc.countryname',
				'shipping_countryiso' => 'sc.countryiso',
				'shipping_countryarea' => 'sca.name',
				'shipping_countryen' => 'sc.countryen',
				'shipping_text1' => 'sa.text1',
				'shipping_text2' => 'sa.text2',
				'shipping_text3' => 'sa.text3',
				'shipping_text4' => 'sa.text4',
				'shipping_text5' => 'sa.text5',
				'shipping_text6' => 'sa.text6',
				'birthday' => 'ub.birthday',
				'preisgruppe' => 'g.id',
				'billing_net' => 'g.tax'
			));
				
		$stmt = $this->db->query($select);
		$customer = $stmt->fetchAll();
		return $customer[0];
	}
	
	/*
	 * create the Order-Query for Batch-Export
	 */
	private function createOrderQuery() {
		if($this->config->getSepaProvider() === SepaProvider::SWAG) {
			$paymentName = 'sepa';
			$bic = 'dbt.bic';
			$iban = 'dbt.iban';
		} else {
			$paymentName = 'debit';
			$bic = 'att.ott_debit_bic';
			$iban = 'att.ott_debit_iban';
		}
		$paymentName = $this->config->getSelectionPaymentName($paymentName);
		$os = $this->config->getSelectionOrderState();
		$cs = $this->config->getSelectionPaymentState();
		$includeError = $this->config->getSelectionIncludeError();
		
		$select =  $this->db->select();
		$select->from(array('ord' => 's_order'), array());
		$select->join(array('pmt' => 's_core_paymentmeans'), 'pmt.id = ord.paymentId', array());
		$select->join(array('usr' => 's_user_billingaddress'), 'usr.id = ord.userId', array());
		if($this->config->getSepaProvider() === SepaProvider::SWAG)
			$select->join(array('dbt' => 's_core_payment_data'), 'usr.id = dbt.user_id', array());
		else
			$select->join(array('att' => 's_user_attributes'), 'att.userID = ord.userId', array());
		$select->joinLeft(array('prt' => 'trim_sepa_protocol'), 'ord.ordernumber = prt.order_id', array());
		$select->columns( array (
				'orderId' => 'ord.id',
				'orderNo' => 'ord.ordernumber',
				'orderDate' => 'DATE(ord.ordertime)',
				'amount' => 'ord.invoice_amount',
				'currency' => 'ord.currency',
				'customerNo' => 'usr.customernumber',
				'customerName' => "CONCAT_WS(' ', usr.firstname, usr.lastname )",
				'iban' => $iban,
				'bic' => $bic,
				'mandateRef' => 'ord.ordernumber'
				));
		$select->where('pmt.name = ?', $paymentName);
		if($os !== FALSE)
			$select->where('ord.status IN (?)', $os);
		if($cs !== FALSE)
			$select->where('ord.cleared IN (?)', $cs);
		if($includeError === TRUE)
			$select->where("prt.order_id is NULL or prt.status = 'ERROR'");
		else 
			$select->where("prt.order_id is NULL or prt.status = 'RPT'");
		return $select;
	}
	
	/**
	 * loads additional Paymentdata needed for Export
	 * @param string $userId	UserId
	 * @param string $orderNo	OrderNumber
	 * @return array|boolean paymentdata record or fals if not found
	 */
	public function getPaymentData($userId, $orderNo) {
		if($this->config->getSepaProvider() === SepaProvider::OTT) {
			$result = $this->db->fetchRow(Constants::QUERY_PAYMENT_OTT, array('puid' => $userId));
			if(!empty($result)) {
				$r2 = $this->db->fetchRow(Constants::QUERY_PAYMENT_OTT2, array('pono' => $orderNo));
				$result['currency'] = $r2['currency'];
			}
		} else {
			$result = $this->db->fetchRow(Constants::QUERY_PAYMENT_SWAG, array('puid' => $userId, 'pono' => $orderNo));
		}
		if(!empty($result)) return $result;
		else return false;
	}
	
	/**
	 * save the given snippet (install)
	 * @param unknown $ns	Namespace
	 * @param unknown $name	Name
	 * @param unknown $value Value
	 */
	public function installSnippet($ns, $name, $value, $locale) {
		$now = new DateTime();
		$localeId = $this->getLocaleId($locale);
		$shops = $this->getActiveShops($localeId);
		foreach($shops as $shop) {
			$shopId = $shop['id'];
			$snippet = $this->db->fetchRow(Constants::QUERY_SNIPPET,
					array('ns' => $ns, 'name' => $name, 'shop' => $shopId, 'locale' => $localeId));
			if(empty($snippet)) { // insert
				$record['namespace'] = $ns;
				$record['name'] = $name;
				$record['localeID'] = $localeId;
				$record['shopID'] = $shopId;
				$record['value'] = $value;
				$record['dirty'] = 0;
				$record['created'] = $now->format('Y-m-d H:i:s');
				$record['updated'] = $now->format('Y-m-d H:i:s');
				$this->db->insert(Constants::SNIPPET_TABLE, $record);
			} else {	// update
				if($snippet['dirty'] === 0) {
					$update = array('value' => $value, 'updated' => $now->format('Y-m-d H:i:s'));
					$where['id = ?'] = $snippet['id'];
					$this->db->update(Constants::SNIPPET_TABLE, $update, $where);
				}
			}
		}
	
	}
	
	/*
	 * gets the LocaleId
	 */
	private function getLocaleId($lang) {
		return $this->db->fetchOne(Constants::QUERY_LOCALE, array($lang));
	}
	
	/*
	 * gets an array of valid ShopIds for the given locale
	 */
	private function getActiveShops($localeId) {
		return $this->db->fetchAll(Constants::QUERY_SHOPS, array('lid' => $localeId));
	}
	
	/**
	 * delete the given snippet (uninstall)
	 * @param unknown $ns
	 * @param unknown $name
	 */
	public function removeSnippet($ns, $name) {
		$where['namespace = ?'] = $ns;
		$where['name = ?'] = $name;
		$where['dirty = ?'] = 0;
		$this->db->delete(Constants::SNIPPET_TABLE, $where);
	}
	
	/**
	 * get the snippet directly from the Database (backwards compatibility)
	 * 
	 * @param String $ns	namespace
	 * @param String $name	name
	 * @return String
	 */
	public function getSnippet($ns, $name, $locale = null) {
		if(is_null($locale)) $locale = $this->config->getDefaultLocale();
		$localeId = $this->getLocaleId($locale);
		$shopId = $this->getActiveShops($localeId)[0]['id'];
		$snippet = $this->db->fetchRow(Constants::QUERY_SNIPPET,
				array('ns' => $ns, 'name' => $name, 'shop' => $shopId, 'locale' => $localeId));
		return $snippet['value'];
		// from SW-Version 4.3
// 		return Shopware()->Snippets()->getNamespace(Constants::USAGE_TEXT_NS)->get(Constants::USAGE_TEXT_NAME);
	}
	
	/**
	 * remove Form Element because of SW Bug 9244
	 * @param unknown $elemId
	 */
	public function removeFormElement($elem) {
		if(empty($elem)) return;
		if($elem instanceof  \Shopware\Models\Config\Element) {
			$where['id = ?'] = $elem->getId();
			$this->db->delete(Constants::FORM_ELEMENT_TABLE, $where);
		}
	}
	
	/**
	 * check Plugin if installed and active
	 * @param string $namespace Namespace
	 * @param string $name PluginName
	 * @return boolean
	 */
	public function checkPlugin($namespace, $name) {
		$result = $this->db->fetchOne(Constants::QUERY_PLUGIN, array('pns' => $namespace, 'pname' => $name));
		return !empty($result);
	}
	
	/**
	 * create Plugins Databse Structure 
	 */
	public function createDatabase() {
		$this->db->query(Constants::QUERY_CREATE_PROTOCOL);
	}
	
	/**
	 * remove Plugins Database Structure
	 */
	public function removeDatabase() {
		$this->db->query(Constants::QUERY_DROP_PROTOCOL);
	}
	
	/**
	 * alter Plugins Database migrate data
	 */
	public function updateDatabase_101_102() {
		$this->db->query(Constants::QUERY_ALTER_102_1);
		$this->db->query(Constants::QUERY_ALTER_102_2);
		$this->db->query(Constants::QUERY_ALTER_102_3);
		$this->db->query(Constants::QUERY_ALTER_102_4);
		$this->db->query(Constants::QUERY_ALTER_102_5);
		$this->db->query(Constants::QUERY_MIGRATE_102_1);
		$this->db->query(Constants::QUERY_MIGRATE_102_2);
	}
	
}
