;===================================================================
; Trimension Sepa Export Plugin
; Configuration File
;
; Author: Juergen Werner / Trimension ITS, 29.08.2014
;===================================================================

[selection]
; only orders containing one of the following order-states
; will be exported (only cron mode)
; to enable all states comment out all entries or place
;orderState = *
orderState[] = 0
orderState[] = 1
orderState[] = 3
orderState[] = 5
orderState[] = 6
orderState[] = 7

; only orders set to one of the following payment-states
; will be exported (only cron mode)
; to enable all states comment out all entries or place
;paymentState = *
paymentState[] = 10
paymentState[] = 17

; the configured paymentname for sepa-debit can be overwritten
; if the default name was changed
;paymentName = debit

; failed exports are marked in protocol and will be ignored while
; subsequent exports. setting this element to true will include
; such failed exports in the next export run (only cron mode)
;includeError = true

[order]
; this state will be set if the order was exported successfully
; this id must point to a valid payment-state
; Deactivate this entry if you don't want to automatically change 
; the payment-state
orderSuccessState = 12

; this state will be set if the order export failed
; this id must point to a valid payment-state
; Deactivate this entry if you don't want to automatically change 
; the payment-state
orderErrorState = 21

; set this value to true if you want to activate the Status-Changed Email
; Emails will be sent if for the previous defined Status-Ids valid 
; Email-Templates exist and only if the Order-Payment-State was changed
sendStateChangeMail = true
