<?php
/**
 * Constants.php - Konstant Definitions
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
final class Constants {
	const CONFIG_SECTION_SELECT = 'selection';
	const CONFIG_SECTION_ORDER = 'order';
	const CONFIG_SELECTION_PAYMENT_NAME = 'paymentName';
	const CONFIG_SELECTION_PAYMENT_STATE = 'paymentState';
	const CONFIG_SELECTION_ORDER_STATE = 'orderState';
	const CONFIG_SELECTION_INCLUDE_ERROR = 'includeError';
	const CONFIG_ORDER_SUCCESS_STATE = 'orderSuccessState';
	const CONFIG_ORDER_ERROR_STATE = 'orderErrorState';
	const CONFIG_ORDER_SEND_STATE_MAIL = 'sendStateChangeMail';
	
	const EMAIL_TEMPLATE_PROT = 'sSEPAEXPORTPROTOCOL';
	const EMAIL_TEMPLATE_STAT = 'sORDERSTATEMAIL';
	const CRON_JOB_EVENT = 'SepaCumulateExportJob';
	
	const PROTOCOL_TABLE = 'trim_sepa_protocol';
	const SNIPPET_TABLE = 's_core_snippets';
	const FORM_ELEMENT_TABLE = 's_core_config_elements';
	
	const USAGE_TEXT_NS = 'backend/trimension/sepa/main';
	const USAGE_TEXT_NAME = 'writer/usagetext';
	const USAGE_TEXT_VALUE = '$mandateRef+$customerNo+$company+$customerName';
	
	const QUERY_LOCALE = 'SELECT id FROM s_core_locales WHERE locale LIKE  ?';
	const QUERY_SHOPS = 'SELECT id FROM s_core_shops where active = 1 and locale_id = :lid';
	const QUERY_SNIPPET = 'SELECT id,value,dirty FROM s_core_snippets where namespace = :ns and name = :name and shopID = :shop and localeID = :locale';
	
	const QUERY_PLUGIN = 'SELECT id FROM s_core_plugins WHERE namespace = :pns and name = :pname and active = 1';
	const QUERY_PROTOCOL = 'SELECT order_id as orderId, customer_id as customerId, message_id as messageId, mandate_ref as mandateRef, comment as comment, export_ts as exportTs, status as status FROM trim_sepa_protocol WHERE order_id = :pono';
	const QUERY_PAYMENT_OTT = 'SELECT ott_debit_bic as bic, ott_debit_iban as iban FROM s_user_attributes WHERE id = :puid';
	const QUERY_PAYMENT_OTT2 = 'SELECT currency FROM s_order WHERE ordernumber = :pono';
	const QUERY_PAYMENT_SWAG = 'SELECT pmi.bic as bic, pmi.iban as iban, o.currency as currency FROM s_core_payment_instance pmi JOIN s_order o ON o.id = pmi.order_id WHERE pmi.user_id = :puid and o.ordernumber = :pono';
	
	const QUERY_CREATE_PROTOCOL = "CREATE TABLE IF NOT EXISTS trim_sepa_protocol ( 
								id int(11) NOT NULL AUTO_INCREMENT,
								order_id int(10) NOT NULL UNIQUE, 
								customer_id int(10) NOT NULL, 
								message_id varchar(35) NULL, 
								mandate_ref varchar(35) NOT NULL,
								export_ts datetime NOT NULL,
								comment varchar(255) NULL,
								status varchar(10) not null,
								PRIMARY KEY (id),
								INDEX sepa_export_order_ix (order_id) )
							ENGINE=InnoDB 
							DEFAULT CHARSET=utf8 
							COLLATE=utf8_unicode_ci;";
	const QUERY_DROP_PROTOCOL = "DROP TABLE IF EXISTS trim_sepa_protocol";
	
	const QUERY_ALTER_102_1 = "alter table trim_sepa_protocol change file_name comment varchar(255) null;";
	const QUERY_ALTER_102_2 = "alter table trim_sepa_protocol add status varchar(10) not null;";
	const QUERY_ALTER_102_3 = "alter table trim_sepa_protocol modify order_id int(10) not null;";
	const QUERY_ALTER_102_4 = "alter table trim_sepa_protocol modify customer_id int(10) not null;";
	const QUERY_ALTER_102_5 = "alter table trim_sepa_protocol modify message_id varchar(35) null;";
	const QUERY_MIGRATE_102_1 = "update trim_sepa_protocol set status = 'OK';";
	const QUERY_MIGRATE_102_2 = "update trim_sepa_protocol set status = 'ERROR' where comment like '%%invalid data%%';";
						
}

