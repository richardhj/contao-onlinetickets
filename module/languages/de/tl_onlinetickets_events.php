<?php


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['title_legend'] = 'Titel und Einstellungen';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['preprinted_legend'] = 'Vorgedruckte Tickets';


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['name'][0] = 'Name';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['name'][1] = 'Geben Sie hier den Namen des Events ein.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['users'][0] = 'Benutzer';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['users'][1] = 'Wählen Sie hier die Benutzer aus, die auf Event über die API zugreifen können.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['date'][0] = 'Datum';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['date'][1] = 'Geben Sie hier das Datum des Events ein.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_price'][0] = 'Ticket-Preis';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_price'][1] = 'Geben Sie hier den Preis des Tickets ein, der für dieses Event allgemein zählt.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['preprinted_tickets'][0] = 'Vorgedruckte Tickets';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['preprinted_tickets'][1] = 'Wählen Sie dieses Häkchen aus, um PDF-Ausdrucke für vorgedruckte Tickets zu erstellen.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_width'][0] = 'Breite';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_width'][1] = 'Geben Sie die Breite der Tickets ein.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_height'][0] = 'Höhe';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_height'][1] = 'Geben Sie die Höhe der Tickets ein.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_elements'][0] = 'Elemente für Ausdruck';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_elements'][1] = 'Geben Sie Reihe für Reihe die Elemente an, die auf dem Ausdruck erscheinen sollen.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_element'][0] = 'Element';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_position_x'][0] = 'Position horizontal';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_position_x'][1] = 'Geben Sie die horizontale Position des Elements auf der Seite des Ausdrucks für dieses Element an. Geben Sie 0 ein, um den Seitenrand zu umgehen. Als Einheit wird die oben angezeigte verwendet.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_position_y'][0] = 'Position vertikal';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_position_y'][1] = 'Geben Sie die vertikale Position des Elements auf der Seite des Ausdrucks für dieses Element an. Geben Sie 0 ein, um den Seitenrand zu umgehen. Als Einheit wird die oben angezeigte verwendet.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_family'][0] = 'Schriftfamilie';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_family'][1] = 'Wählen Sie die Schriftfamilie aus.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_style'][0] = 'Schriftstil';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_style'][1] = 'Wählen Sie einen oder mehrere Schriftstile aus.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_size'][0] = 'Schriftgröße';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_size'][1] = 'Geben Sie die Schriftgröße ein.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_fill_number'][0] = 'Ticketnummer auffüllen';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_fill_number'][1] = 'Geben Sie bei Bedarf die Anzahl der Stellen ein, auf die die Ticketnummer mit Nullen aufgefüllt werden soll.';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_barcode_height'][0] = 'Barcode Höhe';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_barcode_height'][1] = 'Geben Sie bei Bedarf die Höhe des Barcodes an.';


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_element_values'] = array
(
	'id'   => 'Ticketnummer',
	'name' => 'Name',
	'date' => 'Datum',
	'C128' => 'Barcode Typ 128',
	'C39'  => 'Barcode Typ 3 of 9'
);

$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_style_values'] = array
(
	'B' => 'fett',
	'I' => 'kursiv',
	'U' => 'unterstrichen',
	'D' => 'durchgestrichen',
	'O' => 'überstrichen'
);


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['new'][0] = 'Neues Event';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['new'][1] = 'Ein neues Event anlegen';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['edit'][0] = 'Event bearbeiten';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['edit'][1] = 'Event ID %u bearbeiten';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['copy'][0] = 'Event duplizieren';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['copy'][1] = 'Event ID %u duplizieren';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['delete'][0] = 'Event löschen';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['delete'][1] = 'Event ID %u löschen';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['show'][0] = 'Eventdetails';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['show'][1] = 'Details des Events ID %u anzeigen';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['agencies'][0] = 'Vorverkaufsstellen';
$GLOBALS['TL_LANG']['tl_onlinetickets_events']['agencies'][1] = 'Die Vorverkaufsstellen für das Event ID %u bearbeiten';
