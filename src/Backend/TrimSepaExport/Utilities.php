<?php
/**
 * Utilities.php
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
final class Utilities {
	
	const BIC_PATTERN = "/^[A-Z]{6}([2-9A-Z][0-9A-NP-Z]){1}([0-9A-Z]{3})?$/";
	const BANK_LIST_FILE = "bankidcodes.csv";
	
	private $logFile;
	private $config;
	private static $bankList;
	private static $lock = FALSE;
	
	/**
	 * Default Constructor
	 */
	public function __construct($cfg) {
		$this->config = $cfg;
		$tmpLogFile = $this->config->getConfig('logFile');
		if(!empty($tmpLogFile))
			$this->logFile = Shopware()->DocPath() . $tmpLogFile;
		$this->loadBankList();
	}
	
	/**
	 * cleanups the data string (removes blanks and makes it uppercase)
	 */
	public function cleanup($data) {
		if(is_null($data) || empty($data)) return $data;
		return strtoupper(str_replace(' ', '', $data));
	}
	
	/**
	 * Order Validation
	 * @param object $order
	 * @return boolean
	 */
	public function checkOrder($order) {
		if(empty($order) || !is_object($order)) return false;
		if(empty($order->sUserData) || !is_array($order->sUserData)) return false;
		if(empty($order->sOrderNumber)) return false;
		$pmm = $order->sUserData['additional']['payment']['name'];
		return ((($this->config->getSepaProvider() === SepaProvider::OTT) && ($pmm == 'debit'))
				|| (($this->config->getSepaProvider() === SepaProvider::SWAG) && ($pmm == 'sepa'))
				);
	}
	
	
	/**
	 * creates unique Message ID for Export File
	 * @return string
	 */
	public function createMessageId() {
		$timestamp = new DateTime();
		$prefix = $this->config->getConfig('msgPrefix');
		return '' . $prefix . $timestamp->format('YmdHis');
	}
	
	/**
	 * check iban and bic
	 * 
	 * @param String $iban
	 * @param String $bic
	 */
	public function validateBankAccout($iban, $bic) {
		if(empty($iban)) return Messages::VALIDATION_EMPTY_IBAN;
		$country = substr($this->cleanup($iban), 0, 2);
		if($country !== 'DE' and empty($bic)) return Messages::VALIDATION_INVALID_BANKACCOUNT;
		if(!$this->validateIban($iban)) return Messages::VALIDATION_INVALID_IBAN;
		if(empty($bic)) return Messages::VALIDATION_OK;
		
		if(!$this->validateBic($bic)) return Messages::VALIDATION_INVALID_BIC;
		$bicCountry = substr($this->cleanup($bic),4,2);
		if($country !== $bicCountry) return Messages::VALIDATION_INVALID_PAIR;
		
		if(!empty(self::$bankList) && !isset(self::$bankList[$bic])) return Messages::VALIDATION_INVALID_BIC2;
		
		return Messages::VALIDATION_OK;
	}
	
	/**
	 * validates BIC (format)
	 */
	public function validateBic($bic) {
		if(empty($bic)) return true;
		$bic = $this->cleanup($bic);
		$len = strlen($bic);
		if($len !== 8 && $len !== 11) return false;
		return preg_match(self::BIC_PATTERN, $bic);
	}
	
	/**
	 * validates IBAN (checksum)
	 */
	public function validateIban($iban) {
		if(empty($iban)) return false;
		$iban = $this->cleanup($iban);
		$tmp = substr($iban, 4)
		. strval( ord( $iban{0} ) - 55 )
		. strval( ord( $iban{1} ) - 55 )
		. substr( $iban, 2, 2);
		
		$rest=0;
		for ( $pos=0; $pos<strlen($tmp); $pos+=7 ) {
			$part = strval($rest) . substr($tmp,$pos,7);
			$rest = intval($part) % 97;
		}
		return ($rest === 1);
	}
	
	/**
	 * loads the bank list (Sepa available BICs)
	 */
	public function loadBankList($force = FALSE) {
		if(self::$lock === TRUE) return;
		if(!empty(self::$bankList) && !$force) return;
		self::$lock = TRUE;
		$filepath = $this->config->getDataLocation() . self::BANK_LIST_FILE;
		if(file_exists($filepath)) {
			self::$bankList = array();
			try {
				if (($handle = fopen($filepath, "r")) !== FALSE) {
					while (($record = fgetcsv($handle, 170, ';')) !== FALSE) {
						if(empty($record)) continue;
						if(empty($record[0])) continue;
						if(substr($record[0], 0, 1) === '#') continue;
						self::$bankList[$record[0]] = array('name' => $record[1], 'SDD' => $record[3], 'COR1' => $record[4]);
					}
					fclose($handle);
				}
				$this->logSepa("[INFO] Banklist loaded: " . count(self::$bankList) . " Entries");			
			} catch(Exception $e) {
				self::$bankList = array();
				$this->errorLog("Error loading BankList: " . $e->getMessage());
			}
		} else {
			$this->logSepa("[WARN] missing BankId File" );
		}
		self::$lock == FALSE;
	}
	
	/**
	 * write log entry. 
	 * @param unknown $logmsg
	 */
	public function debugLog($logmsg) {
		error_log(date("[d.m.Y H:i:s.u] ") . "[DEBUG] $logmsg\n", 3, Shopware()->DocPath() . 'logs/error.log');
	}
	
	/**
	 * write log entry. 
	 * @param unknown $logmsg
	 */
	public function errorLog($logmsg) {
		error_log(date("[d.m.Y H:i:s.u] ") . "[ERROR] $logmsg\n", 3, Shopware()->DocPath() . 'logs/error.log');
	}
	
	/**
	 * write log entry. 
	 * @param unknown $logmsg
	 */
	public function logSepa($logmsg) {
		if(isset($this->logFile))
			error_log(date("[d.m.Y H:i:s.u] ") . " $logmsg\n", 3, $this->logFile);
	}
	
	
}

