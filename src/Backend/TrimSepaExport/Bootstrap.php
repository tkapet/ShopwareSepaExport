<?php
include 'Configuration.php';
/**
 * Bootstrap.php
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
 * @version		1.1.2
 */
class Shopware_Plugins_Backend_TrimSepaExport_Bootstrap extends Shopware_Components_Plugin_Bootstrap { 
	
	const PLUGIN_VERSION = '1.2.6';
	
	private $exporter;
	private $cfg;
	
	/**
	 * Get the Plugin Capabilities
	 * @return array of capabilities
	 */
	public function getCapabilities() {
		return array( 
				'install' => true, 
				'update' => true, 
				'enable' => true 
				);
	} 
	
	/**
	 * Get Plugin Label
	 * @return string Plugin Label
	 */
	public function getLabel() {
		return 'Trimension SEPA Zahlungsdaten Export';
	} 
	
	/**
	 * Get Plugin Version
	 * @return string Plugin Version
	 */
	public function getVersion() {
		return self::PLUGIN_VERSION;
	} 
	
	/**
	 * Get Plugin Information
	 * @return array Plugin Information
	 */
	public function getInfo() {
		return array( 
				'version' => $this->getVersion(), 
				'label' => $this->getLabel(), 
				'supplier' => 'Trimension ITS, Jürgen Werner', 
				'autor' => 'Trimension ITS, Jürgen Werner', 
				'description' => file_get_contents($this->Path() . 'description.html'), 
				'support' => 'http://www.trimension.de', 
                'copyright' => 'Copyright © 2014, Trimension ITS',
                'license' => 'All rights reserved.',
				'link' => 'http://www.trimension.de' 
				);
	} 
	
	/**
	 * Plugin Installation Method
	 * @return boolean true
	 */
	public function install() {
		$this->update('install');
		$this->registerEvents();
		return true; 
	}
	
	/**
	 * Plugin Update Method
	 * @param string $oldVersion
	 * @return boolean true if oldversion is valid
	 */
	public function update($oldVersion) {
		$form = $this->Form();
		
		try {
			switch ($oldVersion) {
				case 'install':
					$this->createDatabase();
					$this->createConfigurationForm($form);
					$this->installMailTemplate();
					$this->installPluginSnippets();
					$this->installCronJobs();
					break;
				case '1.0.1':
					$this->updateDatabase_101_102();
					$this->installMailTemplate();
					// falling through
				case '1.0.2':
				case '1.0.3':
					$this->installPluginSnippets();
					$this->updateForm_103_110();
				case '1.1.0':
				case '1.1.1':
				case '1.1.2':
				case '1.1.3':
				case '1.2':
				case '1.2.1':
				case '1.2.2':
				case '1.2.3':
				case '1.2.4':
				case '1.2.5':
					break;
				default:
					return false;
			}
			$this->getConfiguration()->copyConfig();
			return true;
		} catch(Exception $e) {
			$util = new Utilities($this->getConfiguration());
			$util->errorLog("Exception on Plugin Update: " . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * Plugin Uninstall Method
	 * @return boolean true
	 */
	public function uninstall() { 
		try {
			$this->removeDatabase(); 
			$this->removeMailTemplate();
			$this->removePluginSnippets();
			return true;
		} catch(Exception $e) {
			$util = new Utilities($this->getConfiguration());
			$util->errorLog("Exception on Plugin Uninstall: " . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * internal private method
	 * create plugin configuration form
	 */
	private function createConfigurationForm($form) {
		$form = $this->Form();
		$form->setElement( 'text', 'initiator', array( 
				'label' => Messages::CONFIG_LABEL_INITNAME, 
				'description' => Messages::CONFIG_DESC_INITNAME,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP ));
		$form->setElement( 'text', 'initiatorIban', array( 
				'label' => Messages::CONFIG_LABEL_INITIBAN, 
				'description' => Messages::CONFIG_DESC_INITIBAN,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
				'required' => true));
		$form->setElement( 'text', 'initiatorBic', array( 
				'label' => Messages::CONFIG_LABEL_INITBIC, 
				'description' => Messages::CONFIG_DESC_INITBIC, 
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
				'required' => true));
		$form->setElement( 'text', 'creditorId', array( 
				'label' => Messages::CONFIG_LABEL_CREDITOR, 
				'value' => '', 
				'description' => Messages::CONFIG_DESC_CREDITOR,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP ));
		$form->setElement( 'number', 'preliminary', array( 
				'label' => Messages::CONFIG_LABEL_PRELIMINARY, 
				'value' => 3, 
				'description' => Messages::CONFIG_DESC_PRELIMINARY, 
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
				'required' => true));
		$form->setElement( 'checkbox', 'expressSepa', array( 
				'label' => Messages::CONFIG_LABEL_EXPRESS, 
				'value' => false, 
				'description' => Messages::CONFIG_DESC_EXPRESS,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP));
		$form->setElement( 'text', 'msgPrefix', array( 
				'label' => Messages::CONFIG_LABEL_MSGPREFIX, 
				'description' => Messages::CONFIG_DESC_MSGPREFIX,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP ));
		$form->setElement( 'select', 'exportMode', array( 
				'label' => Messages::CONFIG_LABEL_MODE, 
				'value' => ExportMode::AUTO_EACH, 
				'store' => array(
								array(ExportMode::AUTO_EACH, 'Automatischer Export für jede Bestellung'),
								array(ExportMode::AUTO_CRON, 'Automatisch per Cron-Job')), 
				'description' => Messages::CONFIG_DESC_MODE,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP ));
		$form->setElement( 'text', 'filePath', array( 
				'label' => Messages::CONFIG_LABEL_FILEPATH, 
				'value' => 'files/sepa', 
				'description' => Messages::CONFIG_DESC_FILEPATH,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
				'required' => true));
		$form->setElement( 'text', 'logFile', array( 
				'label' => Messages::CONFIG_LABEL_LOGFILE, 
				'value' => 'logs/sepa.log', 
				'description' => Messages::CONFIG_DESC_LOGFILE,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP));
		$form->setElement( 'select', 'mailProtocol', array( 
				'label' => Messages::CONFIG_LABEL_SEND_P, 
				'value' => MailProtocol::ONERROR, 
				'store' => array(
								array(MailProtocol::NEVER, 'keine Email'),
								array(MailProtocol::ONERROR, 'Email bei Fehler'), 
								array(MailProtocol::ALWAYS, 'bei jedem Export')), 
				'description' => Messages::CONFIG_DESC_SEND_P,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP));
		$form->save();
	}
	
	/**
	 * internal private method
	 * create databse table
	 */
	private function createDatabase() {
		$dbman = new DatabaseManager($this->getConfiguration());
		$dbman->createDatabase();
	}
	
	/**
	 * internal private method
	 * remove database table
	 */
	private function removeDatabase() { 
		$dbman = new DatabaseManager($this->getConfiguration());
		$dbman->removeDatabase();
	}
	
	/**
	 * internal private method
	 * alter database protocol table and migrate data
	 */
	private function updateDatabase_101_102() {
		$dbman = new DatabaseManager($this->getConfiguration());
		$dbman->updateDatabase_101_102();
	}
	
	/**
	 * internal private method
	 * change field in the configuration form
	 */
	private function updateForm_103_110() {
		$dbman = new DatabaseManager($this->getConfiguration());
		$form = $this->Form();
		$elem = $form->getElement('sendErrorProtocol');
		$dbman->removeFormElement($elem);
		$form->setElement( 'select', 'mailProtocol', array( 
				'label' => Messages::CONFIG_LABEL_SEND_P, 
				'value' => MailProtocol::ONERROR, 
				'store' => array(
								array(MailProtocol::NEVER, 'keine Email'),
								array(MailProtocol::ONERROR, 'Email bei Fehler'), 
								array(MailProtocol::ALWAYS, 'bei jedem Export')), 
				'description' => Messages::CONFIG_DESC_SEND_P,
				'scope' => Shopware\Models\Config\Element::SCOPE_SHOP));
		$form->save();
	}
	
	/**
	 * internal private method
	 * creates and installs email templates
	 */
	private function installMailTemplate() {
		$content = file_get_contents( $this->Path() . "install/email_template_protocol.txt" );
		 
		$mail = new \Shopware\Models\Mail\Mail();
		$mail->setName( Constants::EMAIL_TEMPLATE_PROT );
		$mail->setFromMail( "{config name=mail}" );
		$mail->setFromName( "{config name=shopName}" );
		$mail->setSubject( "SepaExport Protocol {config name=shopName}" );
		$mail->setContent( $content );
		$mail->setIsHtml( false );
		$mail->setMailtype( 1 );
		$mail->setContext( null );
		 
		Shopware()->Models()->persist( $mail );
		Shopware()->Models()->flush();
	}
	
	/**
	 * internal private method
	 * remove email templates
	 */
	private function removeMailTemplate() {
		$repository = Shopware()->Models()->getRepository('Shopware\Models\Mail\Mail' );
		$mail = $repository->findOneBy( array('name' => Constants::EMAIL_TEMPLATE_PROT));
		if(!is_null($mail)) 
			Shopware()->Models()->remove($mail);
	}
	
	/**
	 * internal private method
	 * create Text Modules
	 */
	private function installPluginSnippets() {
		$dbman = new DatabaseManager($this->getConfiguration());
		$dbman->installSnippet(
				Constants::USAGE_TEXT_NS, 
				Constants::USAGE_TEXT_NAME,
				Constants::USAGE_TEXT_VALUE, 'de_DE');
	}
	
	/**
	 * internal private method
	 * remove Text Modules
	 */
	private function removePluginSnippets() {
		$dbman = new DatabaseManager($this->getConfiguration());
		$dbman->removeSnippet(Constants::USAGE_TEXT_NS, Constants::USAGE_TEXT_NAME);
	}
	
	/**
	 * internal private method
	 * installs the cron job
	 */
	private function installCronJobs() {
		$this->createCronJob( 'TrimensionSepaExport', Constants::CRON_JOB_EVENT, 86400, false );
	}
	
	/**
	 * internal private method
	 * register events
	 */
	private function registerEvents() {
		$this->subscribeEvent( 'Shopware_CronJob_' . Constants::CRON_JOB_EVENT, 'onSepaCumulateExportJob' );
		$this->subscribeEvent( 'Enlight_Controller_Action_PostDispatch_Frontend_Checkout',
									'onEnlightControllerActionPostDispatchFrontendCheckout'	);
		return true;
	}

	/**
	 * Eventhandler Export Trigger from CronJob
	 */
	public function onSepaCumulateExportJob(Shopware_Components_Cron_CronJob $job) {
		if($this->getConfiguration()->isAutoCronMode()) {
			$exporter = new SepaExporter($this->getConfiguration());
			return $exporter->exportOrder();
		}
		return "inaktiv";
	}
	
	/**
	 * Eventhandler triggering Export after Order is committed
	 * @param Enlight_Event_EventArgs $arguments
	 */
	public function onEnlightControllerActionPostDispatchFrontendCheckout(Enlight_Event_EventArgs $arguments) {
		$request = $arguments->getRequest();
		if($request->getActionName() === 'finish' &&
					$this->getConfiguration()->isAutoOrderMode()) {
			$session = Shopware()->Session();
			$exporter = new SepaExporter($this->getConfiguration());
			$exporter->exportOrder($session['sOrderVariables']);
		}
	}
	
	/**
	 * internal private method
	 * creates/gets Configuration Instance
	 */
	private function getConfiguration() {
		if(!isset($this->cfg)) $this->cfg = new Configuration($this->Path());
		return $this->cfg;
	}
	
}
