<?php
/**
 * Messages.php - Contains View Message Definitions
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
 * @version		1.0.0
 */
final class Messages {
	const CONFIG_LABEL_INITNAME = 'Auftraggeber Name (opt.)';
	const CONFIG_LABEL_INITIBAN = 'Auftraggebers IBAN';
	const CONFIG_LABEL_INITBIC = 'Auftraggeber BIC';
	const CONFIG_LABEL_CREDITOR = 'Gläubiger Identifikation (opt.)';
	const CONFIG_LABEL_PRELIMINARY = 'Bank-Vorlaufzeit (Tage)';
	const CONFIG_LABEL_MSGPREFIX = 'Message-Id Prefix';
	const CONFIG_LABEL_FILEPATH = 'Dateiablage';
	const CONFIG_LABEL_LOGFILE = 'Log-File';
	const CONFIG_LABEL_MODE = 'Export Modus';
	const CONFIG_LABEL_EXPRESS = 'Sepa Express Lastschrift verwenden';
	const CONFIG_LABEL_SEND_P = 'Automatischer Email Versand';

	const CONFIG_DESC_INITNAME = 'Der Auftraggeber wird aus den Grundeinstellungen (Sepa-Konfiguration) entnommen. Hier kann optional ein davon abweichender Auftraggeber eingetragen werden';
	const CONFIG_DESC_INITIBAN = 'Die IBAN des Auftraggebers.';
	const CONFIG_DESC_INITBIC = 'Der BIC des Auftraggebers.';
	const CONFIG_DESC_CREDITOR = 'Die Gläubiger-Id wird aus den Grundeinstellungen (Sepa-Konfiguration) entnommen. Hier kann eine davon abweichende GID eingetragen werden.';
	const CONFIG_DESC_PRELIMINARY = 'Die Zeit in Bankarbeitstagen, die für die Berechnung der Fälligkeit (von heute an) genutzt wird.';
	const CONFIG_DESC_MSGPREFIX = 'Die Message-Id wird aus dem aktuellen Zeitstempel erzeugt (YYMMTTHHMMSS). Hier kann ein Prefix für die Message-Id definiert werden. Das kann notwendig sein, da die Message Id auch als Filename verwendet wird.';
	const CONFIG_DESC_FILEPATH = 'Der Pfad zum Speichern der Sepa-Export Dateien (relativ zum DocumentRoot)';
	const CONFIG_DESC_LOGFILE = 'Der Pfad des Logfiles (relativ zum DocumentRoot)';
	const CONFIG_DESC_MODE = 'Der Modus bestimmt, wann und wie ein Export initiiert wird';
	const CONFIG_DESC_EXPRESS = 'Sepa Express Lastschrift erlauben kürzere Vorlaufzeiten, sind aber ggf. kostenpflichtig. Bitte beachten Sie, daß die Vorlaufzeit entsprechend dieser Wahl korrekt gesetzt wird';
	const CONFIG_DESC_SEND_P = 'Nach Abschluss des Exports wird eine automatische Email mit dem Export-Protokoll versand. Es wird die Email-Vorlage sSEPAEXPORTPROTOCOL verwendet';
		
	const CRON_EMPTY = 'Keine Bestellungen gefunden.';
	const CRON_SUCCESS = 'Es wurden %d Bestellungen verarbeitet';
	const CRON_ERROR_WRITER = 'Fehler beim Speichern der ExportDatei. (siehe Logfile)';
	const CRON_INCOMPLETE = 'Unvollständige Verarbeitung. %d Bestellungen konnten nicht verarbeitet werden';
	const CRON_ERROR_PROCESS = 'Verarbeitungsfehler, siehe Log-File';
	const EXPORT_ERROR = "Fehler: '%s'";
	const STATUS_HISTORY_COMMENT = 'Status Update by Sepa Export';

	const VALIDATION_EMPTY_RECORD = "Empty Order Record";
	const VALIDATION_EMPTY_ORDERNO = "OrderNumber ist not set";
	const VALIDATION_EMPTY_ORDERDATE = "OrderDate ist not set";
	const VALIDATION_EMPTY_CUSTNO = "CustomerNumber ist not set";
	const VALIDATION_EMPTY_CUSTOMER = "Customer Name ist not set";
	const VALIDATION_EMPTY_AMOUNT = "Missing Amount";
	const VALIDATION_EMPTY_MANDATE = "Missing Mandate Reference";
	const VALIDATION_EMPTY_IBAN = "Missing IBAN";
	const VALIDATION_EMPTY_BIC = "Missing BIC";
	const VALIDATION_INVALID_IBAN = "Invalid IBAN (wrong checksum)";
	const VALIDATION_INVALID_BIC = "Invalid BIC Format";
	const VALIDATION_INVALID_BIC2 = "Invalid BIC Code";
	const VALIDATION_INVALID_PAIR = "IBAN and BIC refers to different countries";
	const VALIDATION_INVALID_BANKACCOUNT ="Foreign IBAN requires a valid BIC, BIC must not be empty";
	const VALIDATION_OK = "OK";
	
	/**
	 * Getter for Parameterized Constant-Strings
	 */
	public static function get($c, $p) {
		return sprintf($c, $p);
	}
}
