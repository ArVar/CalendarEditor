CalendarEditor
==============

A frontend editor for the Contao core module "Calendar".

Ein Frontend Editor für das Contao Core Modul "Calendar".

Über die Erweiterung
--------------------

CalendarEditor ist eine Erweiterung für das Kalender Modul "Calendar" vom Contao CMS.
Sie basiert im Wesentlichen auf der von Daniel Gaußmann entwickelten Erweiterung "calendar_editor".
Es wurden Anpassungen durchgeführt, sodass die Erweiterung auf Contao 3.0x läuft.
Alle Funktionen konnten noch nicht auf vollständige Kompatibilität getestet werden.

Dieses Repository dient der Weiterentwicklung, Anpassung an zukünftige Contao Versionen und dem Hinzufügen von Features.


Installation
------------

Die Erweiterung kann über ein laufendes Contao direkt installiert werden. 
Dazu muss man einfach den Modul-Ordner in das /system/modules/ Verzeichnis der Contao Installation kopieren
und ein Datenbank-Update im Backend machen.


Dokumentation und Support
-------------------------

Anmerkung zur Verwendung vom EventEditHook: 
Die Felder sollten als DCA tabellen vorhanden sein und die EventEditHook.php den Bedürfnissen entsprechend angepasst werden.
Falls das custom/enhaced Tamplate verwendet wird, sollten dort die Felder entsprechend angepasst werden.

