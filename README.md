SEPA Zahlungsdaten Export

Dieses Plugin erweitert die bestehenden Lastschrift-Zahlarten und extrahiert die Zahlungsdaten einer Bestellung, bei der die Zahlart "(SEPA-)Lastschrift" gewählt wurde. Aus den extrahierten Daten wird eine XML-Datei erzeugt, welche die Formatspezifikation des DFÜ-Abkommens Anlage 3 in der Version 2.7 (pain.008.003.02) erfüllt und direkt bei der Bank eingereicht werden kann.

Je nach Konfiguration des Plugins kann der Export durch verschiedene Ereignisse ausgelöst werden:

• Automatisch bei jeder Bestellung, bei der (Sepa-)Lastschrift als Zahlungsart gewählt wurde<br>
• Automatisch per Cronjob (z.B. einmal täglich als Sammelexport)

Aktuell unterstützt das Plugin die in Shopware integrierte Sepa-Lastschrift und die Ottscho SEPA Lastschrift-Erweiterung.

Die Daten für den Export werden der Bestellung entnommen. Absenderdaten werden der Grundkonfiguration entnommen, können aber in der Plugin-Konfiguration überschrieben werden. Bankdaten (IBAN/BIC) werden vor dem Export bereinigt und validiert. Als Option kann die Sepa-Express-Lastschrift aktiviert werden, was kürzere Bank-Vorlaufzeiten erfordert. Diese Option kann ggf. zu erhöhten Kosten der Lastschrift führen. Bitte prüfen Sie, ob Ihre Bank diese Option zur Verfügung stellt und passen die Vorlaufzeit entsprechend an. Aus der Vorlaufzeit wird unter Berücksichtigung der Wochenenden das Fälligkeitsdatum ermittelt.

Jede exportierte Bestellung wird in der Datenbank protokolliert. Fehler beim Export werden in ein eigenes Logfile geschrieben und ebenfalls protokolliert.

Das Plugin verwendet die Bestellnummer als Mandatsreferenznummer. Die Message-Id, die eindeutig sein muss, wird aus einem konfigurierbaren Prefix und dem aktuellen Zeitstempel gebildet. Diese Message-Id wird auch als Dateiname verwendet.

Der Verwendungszweck ist in einem Textbaustein abgelegt und kann dort angepasst werden. Es können hier folgende Platzhalter verwendet werden: 

$mandateRef Mandatsreferenz-Nummer<br>
$orderNo Bestell-Nummer<br>
$customerNo Kundennummer<br>
$customerName Kundenname (Vorname Nachname)<br>
$company Firmenname<br>
$date aktuelles Datum<br>
$year aktuelles Jahr<br>
$month aktueller Monat<br>
