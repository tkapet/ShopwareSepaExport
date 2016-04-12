<?php
/**
 * Configuration.php
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
include 'SepaProvider.php';
include 'ExportMode.php';
include 'ExportStatus.php';
include 'MailProtocol.php';
include 'Messages.php';
include 'Constants.php';
include 'SepaExporter.php';
include 'SepaWriter.php';
include 'DatabaseManager.php';
include 'ProtocolManager.php';
include 'Utilities.php';
include 'OrderStateManager.php';

/**
 * Configuration Class
 * @author Juergen Werner
 */
final class Configuration {
	
	const CONFIG_FILE = 'config.ini';
	const INSTALL_DIR = 'install/';
	const DATA_DIR	  = 'data/';
	
	private $pluginConfig;
	private $customized;
	private $provider;
	private $dbManager;
	private $pluginRoot;
	
	/**
	 * Default Constructor
	 */
	public function __construct($root) {
		$this->pluginConfig = Shopware()->Plugins()->Backend()->TrimSepaExport()->Config();
		$this->pluginRoot = $root;
	}
	
	/**
	 * Getter gets Plugin Configuration Value
	 * @param string $key
	 */
	public function getConfig($key) {
		return $this->pluginConfig->get($key);
	}
	
	/**
	 * Get Plugin Configuration from ini
	 * @param String $section
	 * @param String $key
	 */
	public function getCustomized($section, $key) {
		if(empty($this->customized))
			$this->customized = parse_ini_file(self::CONFIG_FILE, true);
		if($this->customized === FALSE) return null;
		$cfg = $this->customized[$section];
		if(!empty($cfg)) return $cfg[$key];
		else return null;
	}
	
	/**
	 * gets the Payment Name from the selection block in Custom Config
	 */
	public function getSelectionPaymentName($default) {
		$pn = $this->getCustomized(Constants::CONFIG_SECTION_SELECT, Constants::CONFIG_SELECTION_PAYMENT_NAME);
		return ($pn == null) ? $default : $pn;
	}
	
	/**
	 * gets the Payment States from the selection block in Custom Config
	 */
	public function getSelectionPaymentState() {
		$ps =  $this->getCustomized(Constants::CONFIG_SECTION_SELECT, Constants::CONFIG_SELECTION_PAYMENT_STATE);
		return ($ps == null || $ps === '*') ? FALSE : $ps;
	}
	
	/**
	 * gets the Order States from the selection block in Custom Config
	 */
	public function getSelectionOrderState() {
		$os =  $this->getCustomized(Constants::CONFIG_SECTION_SELECT, Constants::CONFIG_SELECTION_ORDER_STATE);
		return ($os == null || $os === '*') ? FALSE : $os;
	}
	
	/**
	 * gets the IncludeError Setting from the selection block in Custom Config
	 */
	public function getSelectionIncludeError() {
		$ie =  $this->getCustomized(Constants::CONFIG_SECTION_SELECT, Constants::CONFIG_SELECTION_INCLUDE_ERROR);
		return ($ie ? TRUE : FALSE);
	}
	
	/**
	 * gets the configured destination State if Orderprocessing failed
	 * @return Ambigous <boolean, NULL, unknown>
	 */
	public function getOrderErrorState() {
		$state =  $this->getCustomized(Constants::CONFIG_SECTION_ORDER, Constants::CONFIG_ORDER_ERROR_STATE);
		return (empty($state)) ? FALSE : $state;
	}
	
	/**
	 * gets the destination payment state if orderprocessing succeeded
	 * @return Ambigous <boolean, NULL, unknown>
	 */
	public function getOrderSuccessState() {
		$state =  $this->getCustomized(Constants::CONFIG_SECTION_ORDER, Constants::CONFIG_ORDER_SUCCESS_STATE);
		return (empty($state)) ? FALSE : $state;
	}
	
	/**
	 * get SendStateChangeMail Configuration Entry. Mail will only be send if this
	 * Configuration item is enabled and set true
	 * @return boolean
	 */
	public function getSendStateChangeMail() {
		$ssm =  $this->getCustomized(Constants::CONFIG_SECTION_ORDER, Constants::CONFIG_ORDER_SEND_STATE_MAIL);
		return ($ssm ? TRUE : FALSE);
	}
	
	/**
	 * Gets the active Sepa Provider
	 */
	public function getSepaProvider() {
		if(empty($this->provider)) {
			$dbman = new DatabaseManager($this);
			if($dbman->checkPlugin('Backend', 'OttSepaFields')) {
				$this->provider = SepaProvider::OTT;
			} else {
				$this->provider = SepaProvider::SWAG;
			}
		}
		return $this->provider;
	}
	
	/**
	 * checks if single order export mode
	 * @return boolean true if Single Order Mode
	 */
	public function isAutoOrderMode() {
		return $this->getConfig('exportMode') === ExportMode::AUTO_EACH;
	}
	
	/**
	 * checks if CronJob Export Mode
	 * @return boolean true if Cron
	 */
	public function isAutoCronMode() {
		return $this->getConfig('exportMode') === ExportMode::AUTO_CRON;
	}
	
	/**
	 * copy config file if not exist
	 */
	public function copyConfig() {
		$src = $this->pluginRoot . self::INSTALL_DIR . 'config.tpl';
		$dst = $this->pluginRoot . self::CONFIG_FILE;
		if(file_exists($dst)) return;
		copy($src,$dst);
	}
	
	/**
	 * Get the Data Location
	 * @return string
	 */
	public function getDataLocation() {
		return $this->pluginRoot . self::DATA_DIR;
	}
	
	/**
	 * determins the Default Locale
	 */
	public function getDefaultLocale() {
		return 'de_DE';
	}
	
}
