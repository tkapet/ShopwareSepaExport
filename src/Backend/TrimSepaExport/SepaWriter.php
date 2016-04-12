<?php
/**
 * SepaWriter.php
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

define("DOC_SCHEMA_LOC", 'urn:iso:std:iso:20022:tech:xsd:pain.008.003.02 pain.008.003.02.xsd');
define("DOC_SCHEMA_LOC_URL", 'http://www.w3.org/2001/XMLSchema-instance');
define("DOC_SCHEMA_NS", 'urn:iso:std:iso:20022:tech:xsd:pain.008.003.02');
define("ELEMENT_ROOT", 'Document');					// Root Element Document
define("ELEMENT_FRAME", 'CstmrDrctDbtInitn');		// Root Element CustomerDirectDebitInitiation
define("ELEMENT_GROUP_HEADER", 'GrpHdr');			// Group Header (Container)
define("ELEMENT_PAYMENT_INFO", 'PmtInf');			// Payment Info (Container)
define("ELEMENT_MESSAGE_ID", 'MsgId');				// Message Identification
define("ELEMENT_CREATION_TS", 'CreDtTm');			// Creation Timestamp 
define("ELEMENT_NUMBER_OF_TX", 'NbOfTxs');			// Number of Transactions
define("ELEMENT_INITIATING_PARTY", 'InitgPty');		// Initating Party (Container)
define("ELEMENT_NAME", 'Nm');						// Name
define("ELEMENT_PAYMENT_INFO_ID", 'PmtInfId');		// PaymentInfo Identification
define("ELEMENT_PAYMENT_METHOD", 'PmtMtd');			// Payment Method (Fix 'DD')
define("ELEMENT_CONTROL_SUM", 'CtrlSum');			// Total Amount of cashing
define("ELEMENT_PAYMENT_TYPE_INFO", 'PmtTpInf');	// PaymentTypeInformation (Container)
define("ELEMENT_SERVICE_LEVEL", 'SvcLvl');			// Service Level (Container)
define("ELEMENT_CODE", 'Cd');						// Code (Fix Value: ServiceLevel: SEPA, LocalInstrument: CORE)
define("ELEMENT_LOCAL_INSTRUMENT", 'LclInstrm');	// Local Instrument (Container)
define("ELEMENT_SEQUENCE_TYPE", 'SeqTp');			// Sequence Type (FNAL,FRST,OOFF,RCUR)
define("ELEMENT_REQUESTED_COLL_DATE", 'ReqdColltnDt');	// Requested Collection Date (Payment Date)
define("ELEMENT_CREDITOR", 'Cdtr');					// Creditor (Container)
define("ELEMENT_CREDITOR_ACCOUNT", 'CdtrAcct');		// Creditor Account (Container)
define("ELEMENT_ID", 'Id');							// Identification
define("ELEMENT_IBAN", 'IBAN');						// International Bank Account Number
define("ELEMENT_CREDITOR_AGENT", 'CdtrAgt');		// Creditor Agent (Bank) (Container)
define("ELEMENT_FINANCIAL_INSTITUTION_ID", 'FinInstnId');	// (Container) see BIC
define("ELEMENT_BIC", 'BIC');						// Bank Identification Code
define("ELEMENT_CHARGE_BEERAR", 'ChrgBr');			// Fix Value SLEV
define("ELEMENT_CREDITOR_SCHEME_ID", 'CdtrSchmeId');	// (Container) 
define("ELEMENT_PRIVATE_ID", 'PrvtId');				// Private Identifiaction (Container)
define("ELEMENT_OTHER", 'Othr');					// Other (Container)
define("ELEMENT_SCHEME_NAME", 'SchmeNm');			// Scheme Name (Container)
define("ELEMENT_PROPRIETARY", 'Prtry');				// Proprietary (Fix Value: SEPA)
define("ELEMENT_DIRECT_DEBIT_TX_INFO", 'DrctDbtTxInf');	// Direct Debit Transaction Info (Container)
define("ELEMENT_PAYMENT_ID", 'PmtId');				// Payment Identification (Container)
define("ELEMENT_END2END_ID", 'EndToEndId');			// Unique Key for each Payment
define("ELEMENT_INSTRUCTED_AMOUNT", 'InstdAmt');	// Payment Amount for each Debtor
define("ELEMENT_DIRECT_DEBIT_TX", 'DrctDbtTx');		// Direct Debit Transaction (Container)
define("ELEMENT_MANDATE_RELATED_INFO", 'MndtRltdInf');	// Mandate Information (Container)
define("ELEMENT_MANDATE_ID", 'MndtId');				// Mandate Ref Identification Number
define("ELEMENT_DATE_OF_SIGN", 'DtOfSgntr');		// Date of Mandate Signing
define("ELEMENT_AMENDMENT_INDICATOR", 'AmdmntInd');	// Change/Modification (Value: true/false)
define("ELEMENT_AMENDMENT_INFO_DETAILS", 'AmdmntInfDtls');	// only present if Indicator is true (Container)
define("ELEMENT_ORIG_CREDITOR_SCHEME_ID", 'OrgnlCdtrSchmeId');	// (Container)
define("ELEMENT_DEBTOR_AGENT", 'DbtrAgt');			// Debtor Agent (Bank) (Container)
define("ELEMENT_DEBTOR", 'Dbtr');					// Debtor (Container)
define("ELEMENT_DEBTOR_ACCOUNT", 'DbtrAcct');		// Debtor Account (Container)
define("ELEMENT_ULTIMATE_DEBTOR", 'UltmtDbtr');		// (Container)
define("ELEMENT_REMITTANCE_INFO", 'RmtInf');		// (Container)
define("ELEMENT_UNSTRUCTURED", 'Ustrd');			// Text for Reason and additional Info of Transaction
define("ATTRIBUTE_CURRENCY", 'Ccy');				// Currency Attribute

define("CURRENCY_EURO", 'EUR');

define("VALUE_PAYMENT_METHOD", 'DD');			// Fix Value for Payment Method (Direct Debit)
define("VALUE_PROPRIETARY_CODE", 'SEPA');		// Fix Value for Service Level Code
define("VALUE_LI_CODE", 'CORE');				// Fix Value for Local Instrument
define("VALUE_LI_CODE_EX", 'COR1');				// Fix Value for Local Instrument
define("VALUE_ST_FIRST", 'FRST');				// Value for Sequence Type (first direct debit for this repeatable mandate)
define("VALUE_ST_FINAL", 'FNAL');				// Value for Sequence Type (final/last direct debit for repeatable mandate)
define("VALUE_ST_ONEOFF", 'OOFF');				// Value for Sequence Type (mandate for single debit)
define("VALUE_ST_RECURRENT", 'RCUR');			// Value for Sequence Type (subsequent dd for repeatable mandate)
define("VALUE_CB", 'SLEV');						// Fix Value for Charge Beerar

/**
 * Sepa File Writer
 * creates Files based on the Specification 'pain.008.003.02'
 * 
 * @author Juergen Werner
 */
class SepaWriter extends XMLWriter {
	
	const VALID_CHARSET_PATTERN = "/^[ 0-9a-zA-ZäöüÄÖÜß,\+\(\)\.\":\?\-]*$/";
	private $umlaute = array('_','ä','ö','ü','Ä','Ö','Ü','ß');
	private $replace = array('-','ae','oe','ue','AE','OE','UE','ss');
	private $usagePlaceholder = array('$mandateRef','$orderNo','$customerNo','$customerName','$company','$date','$year','$month');
	
	private $util;
	private $messageId;
	private $paymentInfoId;
	private $company;
	private $requestDate;
	private $companyIban;
	private $companyBic;
	private $creditorId;
	private $filePath;
	private $localInstrument;
	private $txUsageMsg;
	private $protocol;
	
	/**
	 * SepaWriter Default-Constructor.
	 * @param array $init Array of Fixed Company and Message Data
	 * @access public
	 */
	public function __construct($cfg, $init) {
		date_default_timezone_set('Europe/Berlin');
		$this->util = new Utilities($cfg);
		$this->protocol = new ProtocolManager($cfg);
		$this->openMemory();
		$this->setIndent(true);
		$this->setIndentString(' ');
		if($this->check($init)) {
			$this->messageId = $init['messageId'];
			if(isset($init['paymentInfoId'])) {
				$this->paymentInfoId = $init['paymentInfoId'];
			} else {
				$this->paymentInfoId = $this->messageId;
			}
			$this->company = $init['company'];
			$this->companyIban = $init['companyIban'];
			$this->companyBic = $init['companyBic'];
			$this->creditorId = $init['creditorId'];
			if(isset($init['leadTime'])) {
				$today = new DateTime();
				$days = $init['leadTime'];
				$wd = $today->format('N');
				if($wd == 7) $days += 1;
				else if($wd > 2) $days += 2;
				$this->requestDate = $today->add(new DateInterval('P'.$days.'D'))->format('Y-m-d');
			}
			if(isset($init['filePath'])) {
				$this->filePath = $init['filePath'];
			}
			if(isset($init['txUsageMsg'])) {
				$this->txUsageMsg = $init['txUsageMsg'];
			} else {
				$this->txUsageMsg = Constants::USAGE_TEXT_VALUE;
			}
			$this->localInstrument = ($init['express'] === TRUE) ? VALUE_LI_CODE_EX : VALUE_LI_CODE;
		}
	}

	/**
	 * Creates the Sepa-XML Document from the given Transaction Records
	 * 
	 * @param array $records the order-information (one record for single, array of records for multiple transactions)
	 * @return boolean Status of creating Document
	 */
	public function createDocument($records) {
		if($this->check($records) && $this->isValid()) {
			$container = (is_array($records[0])) ?  $records : array($records);
			$transactions = $this->validateTransactions($container);
			
			$this->startDocument('1.0', 'UTF-8');
			$this->startElement(ELEMENT_ROOT);
			$this->writeAttributeNs('xsi', 'schemaLocation', DOC_SCHEMA_LOC_URL, DOC_SCHEMA_LOC);
			$this->writeAttribute('xmlns', DOC_SCHEMA_NS);
			$this->startElement(ELEMENT_FRAME);
			
			$this->createGroupHeader($transactions);
			$status = $this->createPaymentInfo($transactions);
			
			$this->endElement();	// Frame
			$this->endElement();	// Document
			$this->endDocument();
			return $status;
		} else {
			$this->util->logSepa("[ERROR] invalid Configuration, cannot create sepa document");
			return false;
		}
	}
	
	/**
	 * internal private method
	 * create payment info block
	 */
	private function createPaymentInfo($transactions) {
		$this->startElement(ELEMENT_PAYMENT_INFO);
		$this->setElement(ELEMENT_PAYMENT_INFO_ID, $this->paymentInfoId);
		$this->setElement(ELEMENT_PAYMENT_METHOD, VALUE_PAYMENT_METHOD);
		$this->setElement(ELEMENT_NUMBER_OF_TX, "" . $this->getNumberOfTransactions($transactions));
		$this->setElement(ELEMENT_CONTROL_SUM, $this->calculateSum($transactions));
		
		$this->createPaymentTypeInfo();
		$this->setElement(ELEMENT_REQUESTED_COLL_DATE, $this->requestDate);
		$this->createCreditor();
		
		foreach($transactions as $transaction) {
			$tx = $transaction['tx'];
			$status = $transaction['status'];
			if($status === Messages::VALIDATION_OK) {
				$this->startElement(ELEMENT_DIRECT_DEBIT_TX_INFO);
				$this->createTransactionInfo($tx);
				$this->endElement();	// DirectDebitTxInfo
				$this->protocol->addProtocol($tx['orderId'], $tx['orderNo'], $tx['customerNo'], $this->messageId, $tx['mandateRef'], ExportStatus::OK);
			} else {
				$this->util->logSepa("[ERROR] " . $status . " : Order # " . $tx['orderNo']);
				$this->protocol->addProtocol($tx['orderId'], $tx['orderNo'], $tx['customerNo'], null, $tx['mandateRef'], ExportStatus::ERROR, $status);
			}
		}
		
		$this->endElement();	// PaymentInfo
		return $this->protocol;
	}
	
	/**
	 * internal private method
	 * create transaction info block
	 */
	private function createTransactionInfo($data) {
		$this->startElement(ELEMENT_PAYMENT_ID);
		$this->setElement(ELEMENT_END2END_ID, $data['mandateRef']);
		$this->endElement();	// PaymentId
		if(empty($data['currency'])) $data['currency'] = CURRENCY_EURO;
		$this->setElement(ELEMENT_INSTRUCTED_AMOUNT, $data['amount'], ATTRIBUTE_CURRENCY, $data['currency']);
		
		$this->createTransactionBlock($data);
		$this->createDebtor($data);
		
		$this->startElement(ELEMENT_REMITTANCE_INFO);
		$rtxt = $this->createTransactionUsageText($data);
		$this->setElement(ELEMENT_UNSTRUCTURED, $rtxt);		
		$this->endElement();	// RemittanceInfo
	}
	
	/**
	 * internal private method
	 * create debitor blocks
	 */
	private function createDebtor($data) {
		$this->startElement(ELEMENT_DEBTOR_AGENT);
		$this->startElement(ELEMENT_FINANCIAL_INSTITUTION_ID);
		$bic = $this->util->cleanup($data['bic']);
		$this->setElement(ELEMENT_BIC, $bic);
		$this->endElement();	// Financial Institution
		$this->endElement();	// DebtorAgent
		
		$this->startElement(ELEMENT_DEBTOR);
		$this->setElement(ELEMENT_NAME, $this->prepare($data['customerName']));
		$this->endElement();	// Debtor
		
		$this->startElement(ELEMENT_DEBTOR_ACCOUNT);
		$this->startElement(ELEMENT_ID);
		$iban = $this->util->cleanup($data['iban']);
		$this->setElement(ELEMENT_IBAN, $iban);
		$this->endElement();	// Id
		$this->endElement();	// DebtorAccount
	}
	
	/**
	 * internal private method
	 * create transaction block
	 */
	private function createTransactionBlock($data) {
		$this->startElement(ELEMENT_DIRECT_DEBIT_TX);
		$this->startElement(ELEMENT_MANDATE_RELATED_INFO);
		$this->setElement(ELEMENT_MANDATE_ID, $data['mandateRef']);
		$this->setElement(ELEMENT_DATE_OF_SIGN, $data['orderDate']);
		$this->setElement(ELEMENT_AMENDMENT_INDICATOR, 'true');
		
		$this->startElement(ELEMENT_AMENDMENT_INFO_DETAILS);
		$this->startElement(ELEMENT_ORIG_CREDITOR_SCHEME_ID);
		$this->setElement(ELEMENT_NAME, $this->prepare($this->company));
		$this->startElement(ELEMENT_ID);
		$this->startElement(ELEMENT_PRIVATE_ID);
		$this->startElement(ELEMENT_OTHER);
		
		$this->setElement(ELEMENT_ID, $this->creditorId);
		$this->startElement(ELEMENT_SCHEME_NAME);
		$this->setElement(ELEMENT_PROPRIETARY, VALUE_PROPRIETARY_CODE);
		
		$this->endElement();	// SchemeName
		$this->endElement();	// Other
		$this->endElement();	// PrivateId
		$this->endElement();	// Id
		$this->endElement();	// CreditorSchemeId
		$this->endElement();	// AmendmendDetailInfo
		$this->endElement();	// MandateRelatedInformation
		$this->endElement();	// DirectDebitTx
	}
	
	/**
	 * internal private method
	 * create creditor blocks
	 */
	private function createCreditor() {
		$this->startElement(ELEMENT_CREDITOR);
		$this->setElement(ELEMENT_NAME, $this->prepare($this->company));
		$this->endElement();	// Creditor
		
		$this->startElement(ELEMENT_CREDITOR_ACCOUNT);
		$this->startElement(ELEMENT_ID);
		$this->setElement(ELEMENT_IBAN, $this->companyIban);
		$this->endElement();	// Id
		$this->endElement();	// CreditorAccount
		
		$this->startElement(ELEMENT_CREDITOR_AGENT);
		$this->startElement(ELEMENT_FINANCIAL_INSTITUTION_ID);
		$this->setElement(ELEMENT_BIC, $this->companyBic);
		$this->endElement();	// Financial Institution
		$this->endElement();	// CreditorAgent

		$this->setElement(ELEMENT_CHARGE_BEERAR, VALUE_CB);
		
		$this->startElement(ELEMENT_CREDITOR_SCHEME_ID);
		$this->startElement(ELEMENT_ID);
		$this->startElement(ELEMENT_PRIVATE_ID);
		$this->startElement(ELEMENT_OTHER);
		$this->setElement(ELEMENT_ID, $this->creditorId);
		$this->startElement(ELEMENT_SCHEME_NAME);
		$this->setElement(ELEMENT_PROPRIETARY, VALUE_PROPRIETARY_CODE);
		$this->endElement();	// SchemeName
		$this->endElement();	// Other
		$this->endElement();	// PrivateId
		$this->endElement();	// Id
		$this->endElement();	// CreditorSchemeId
	}
	
	/**
	 * internal private method
	 * create payment type info block
	 */
	private function createPaymentTypeInfo() {
		$this->startElement(ELEMENT_PAYMENT_TYPE_INFO);
		$this->startElement(ELEMENT_SERVICE_LEVEL);
		$this->setElement(ELEMENT_CODE, VALUE_PROPRIETARY_CODE);
		$this->endElement();	// ServiceLevel
		$this->startElement(ELEMENT_LOCAL_INSTRUMENT);
		$this->setElement(ELEMENT_CODE, $this->localInstrument);
		$this->endElement();	// LocalInstrument
		$this->setElement(ELEMENT_SEQUENCE_TYPE, VALUE_ST_ONEOFF);
		$this->endElement();	// PaymentTypeInfo
	}
	
	/**
	 * internal private method
	 * create group header
	 */
	private function createGroupHeader($transactions) {
		$this->startElement(ELEMENT_GROUP_HEADER);
		$this->setElement(ELEMENT_MESSAGE_ID, $this->messageId);	

		$ts = new DateTime();
		$this->setElement(ELEMENT_CREATION_TS, $ts->format('Y-m-d\TH:i:s.000\Z'));
		$this->setElement(ELEMENT_NUMBER_OF_TX, "" . $this->getNumberOfTransactions($transactions));
		$this->startElement(ELEMENT_INITIATING_PARTY);
		$this->setElement(ELEMENT_NAME, $this->prepare($this->company));
		$this->endElement();
		$this->endElement();
	}
		
	/**
	 * internal private method
	 * create transaction information string
	 */
	private function createTransactionUsageText($data) {
		$now = new DateTime();
		$ix = intval($now->format('m'), 10) - 1;
		$mm = array('Januar','Februar','Maerz','April','Mai','Juni','Juli'
				,'August','September','Oktober','November','Dezember');
		$mon = $mm[$ix];
		
		$usageData = array(
				$data['mandateRef'],
				$data['orderNo'],
				$data['customerNo'],
				$data['customerName'],
				$this->company,
				$now->format('d.m.Y'),
				$now->format('Y'),
				$mon
				);
		$usage = str_replace($this->usagePlaceholder, $usageData, $this->txUsageMsg);
		$usage = $this->prepare($usage);
		$usage = substr($usage, 0, 140);
		return $usage;
	}
	
	/**
	 * Writes the created Document to a File
	 * 
	 * @param string $fileName optional Filename. If null, te
	 * @return string|boolean resulting pathname if successfully written, or false
	 */
	public function writeFile($fileName = '') {
		$fn = empty($fileName) ? $this->messageId . '.xml' : $fileName;
		if(!empty($this->filePath)) $fn = $this->filePath . '/' . $fn;
		try {
			$fh = fopen($fn, 'w');
			$stringData = $this->getDocument();
			fwrite($fh, $stringData);
			fclose($fh);
			return $fn;
		} catch (Exception $e) {
			$this->util->logSepa("[ERROR] Error writing File: $fn : " . $e->getMessage());
			return false;
		}
	}
	
	/** 
	 * Returns the created Document as String
	 */
	public function getDocument(){
		return $this->outputMemory();
	}
	
	/**
	 * internal private method
	 * set element
	 */
	private function setElement($elementName, $content, $attributeName = null, $attributeValue = null) {
		$this->startElement($elementName);
		if(!empty($attributeName)) 
			$this->writeAttribute($attributeName, $attributeValue);
		$this->text($content);
		$this->endElement();
	}
	
	/**
	 * internal private method
	 * calculates amount over transactions
	 */
	private function calculateSum($records) {
		$sum = 0.0;
		foreach ($records as $record) {
			if($record['status'] === Messages::VALIDATION_OK) {
				$tx = $record['tx'];
				$sum += $tx['amount'];
			}
		}
		return "$sum";
	}
	
	/**
	 * internal private method
	 * validation check
	 */
	private function isValid() {
		$err = array();
		if(empty($this->messageId)) $err[] = 'Message ID is not set';
		if(empty($this->paymentInfoId)) $err[] = 'PaymentInfo ID is not set';
		if(empty($this->company)) $err[] = 'Company Name is not set';
		if(!$this->checkCharset($this->company)) $err[] = 'CompanyName contains invalid character(s).';
		
		if(empty($this->requestDate)) $err[] = 'Request Date is empty';
		if(empty($this->creditorId)) $err[] = 'Creditor ID is not set';

		if(empty($this->companyIban)) 
			$err[] = 'Company IBAN is not set';
		else if(!$this->util->validateIban($this->companyIban)) 
			$err[] = 'invalid Company IBAN : ' . $this->companyIban;
		
		if(empty($this->companyBic)) 
			$err[] = 'Company BIC is not set';
		else if(!$this->util->validateBic($this->companyBic)) 
			$err[] = 'invalid Company BIC : ' . $this->companyBic;

		if(!$this->validateUsageMessage($this->txUsageMsg)) 
			$err[] = 'Transaction Usage Text contains invalid character(s).';
		
		if(empty($err)) return true;
		
		$this->util->logSepa("[ERROR] Sepa Init : " . print_r($err, true));
		return false;
	}
	
	/**
	 * internal private method
	 * validation transaction usage message
	 */
	private function validateUsageMessage() {
		$usage = str_replace($this->usagePlaceholder, '?', $this->txUsageMsg);
		return $this->checkCharset($usage);
	}
	
	/**
	 * internal private method
	 * validation transaction record
	 */
	private function validateOrder($tx) {
		if( !$this->check($tx)) return Messages::VALIDATION_EMPTY_RECORD;
		if(empty($tx['orderNo'])) return Messages::VALIDATION_EMPTY_ORDERNO;
		if(empty($tx['orderDate'])) return Messages::VALIDATION_EMPTY_ORDERDATE;
		if(empty($tx['amount'])) return Messages::VALIDATION_EMPTY_AMOUNT;
		if(empty($tx['customerNo'])) return Messages::VALIDATION_EMPTY_CUSTNO;
		if(empty($tx['customerName'])) return Messages::VALIDATION_EMPTY_CUSTOMER;
		if(empty($tx['mandateRef'])) return Messages::VALIDATION_EMPTY_MANDATE;
		if(empty($tx['iban'])) return Messages::VALIDATION_EMPTY_IBAN;
		$iban = $tx['iban'];
		$bic = $tx['bic'];
		return $this->util->validateBankAccout($iban, $bic);
	}
	
	/**
	 * internal private method
	 * validation transaction record list
	 */
	private function validateTransactions($records) {
		$validatedRecords = array();
		foreach($records as $record) {
			$transaction = array(
				'tx' => $record,
				'status' => $this->validateOrder($record)	
			);
			$validatedRecords [] = $transaction;
		}
		return $validatedRecords;
	}

	/**
	 * internal private method
	 * gets the number of valid records
	 */
	private function getNumberOfTransactions($transactions) {
		$n = 0;
		foreach ($transactions as $transaction) {
			if($transaction['status'] === Messages::VALIDATION_OK) $n++;
		}
		return $n;
	}

	/**
	 * internal private method
	 * parameter check
	 */
	private function check($param) {
		return (!empty($param) && is_array($param));
	}
	
	/**
	 * internal private method
	 * prepare string encoding
	 */
	private function prepare($name) {
		return str_replace($this->umlaute, $this->replace, $name);
	}

	/**
	 * internal private method
	 * check character set
	 */
	private function checkCharset($str) {
		return preg_match(self::VALID_CHARSET_PATTERN, $str);
	}

}

