Online tickets extension for Isotope eCommerce
==============================================
*Sell online tickets with Isotope eCommerce*

**This extension requires a high amount of Contao/Isotope know-how and a basic understanding of your home network and
routing domains/ip addresses.**

What it does
------------

### Before the event

You can sell online tickets for your events. After successful order, these online tickets get a QR-code/Barcode and are
home printable for the customer.

If you sell hard tickets and want to track sales, this extension provides tool to print your hard tickets with a
customized barcode.


### During the event

#### Check in

Check in all sold tickets (hard tickets or online tickets) and track venue's degree of capacity.

The included api is fully compatible with the ones of [TicketPay](http://ticketpay.de/). Install the iOS native
[mobile app](http://ticketpay.de/app-demo/) to perform the checkin.

![app](http://ticketpay.de/wp-content/uploads/2013/09/app1.png)

#### Management board / Box office

There is a box office included. Place the frontend module on a protected page in the frontend.
The box office helps you to track the sales and checkins live, track box office sales and revert checkins.

### After the event

Export an excel report with all tickets sold, find out the most important ticket agency, get to know, when visitors
check in most frequently.

How to use it
-------------

### Installation with Composer:

Add these minimum configuration to your composer.json.

```json
{
    "require": {
        "richardhj/contao-onlinetickets": "^0.10.0"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/richardhj/contao-onlinetickets"
        }
    ]
}
```

### Configuration

* Set up events in the backend.
* Create a new product type "online tickets".
* Create new products with the new product type.
* Create a customized isotope document (see below)

### Isotope document template

We are using the templates provided by [`isotope_docuemts`](https://github.com/katgirl/isotope_documents).

To export the online tickets within an isotope document (e.g. the invoice), simple modify the `iso_document_….html5`.
Add the following lines to the end of the template file.

```php
<?php if (null !== ($objTickets = Richardhj\IsotopeOnlineTicketsBundle\Model\Ticket::findByOrder($this->collection->id))): ?><div style="font-size: 72.5%; font-family: Helvetica, sans-serif; float:left; page-break-before:always;">
  <table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-left:100px;" >
  	<tr>
  		<td colspan="3" style="text-align:center;"><img src="http://isotopeecommerce.org/files/layout/logo.png" alt="Isotope eCommerce" height="100"></td>
	</tr>
	<tr>
		<td colspan="3" style="line-height:2;">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3"><strong><span style="font-size: 125%;">Online-Tickets</span><br>zur Bestellung <?php echo $this->collection->document_number; ?></strong></td>
	</tr>
	<tr>
		<td colspan="3" style="line-height:2;">&nbsp;</td>
	</tr>
  	<?php while($objTickets->next()): ?>
  	<tr>
      <td width="40%" style="border-top: 2px solid #c8c8c8;">&nbsp;</td>
      <td width="30%" style="border-top: 2px solid #c8c8c8;">&nbsp;</td>
      <td width="30%" style="border-top: 2px solid #c8c8c8;">&nbsp;</td>
  	</tr>
  	<tr>
      <td colspan="2" style="line-height:2;">&nbsp;</td>
      <td rowspan="3"><img src="<?php echo Richardhj\IsotopeOnlineTicketsBundle\Helper\QrCode::getLocalPath($objTickets->hash); ?>" alt="Ticket Code"></td>
  	</tr>
  	<tr>
  	  <td style="line-height:1.5;"><strong>Veranstaltung</strong></td>
  	  <td style="line-height:1.5;"><strong>Käufer</strong></td>
  	  </tr>
    <tr>
  	  <td><?php
  	  	echo $objTickets->getRelated('event_id')->name . "<br>";
  		echo Date::parse($this->dateFormat, $objTickets->getRelated('event_id')->date) . "<br><br>";
  		echo $objTickets->getRelated('product_id')->teaser ? nl2br($objTickets->getRelated('product_id')->teaser) . "<br><br>" : "";
  		echo "<strong>Ticketpreis:</strong> " . Isotope::formatPriceWithCurrency($objTickets->getRelated('item_id')->price) . "<br>";
  		 ?></td>
  	  <td><?php
  	    echo $arrBillingAddress->company ? $arrBillingAddress->company . "<br>" : "";
        echo $arrBillingAddress->firstname . " "; 
        echo $arrBillingAddress->lastname . "<br>";
        echo $arrBillingAddress->street_1 ? $arrBillingAddress->street_1 . "<br>" : "";
        echo $arrBillingAddress->street_2 ? $arrBillingAddress->street_2 . "<br>" : "";
        echo $arrBillingAddress->street_3 ? $arrBillingAddress->street_3 . "<br>" : "";
        echo $arrBillingAddress->postal . " ";
        echo $arrBillingAddress->city . "<br><br>"; ?></td>
  	  </tr>
    <tr>
      <td style="border-bottom: 2px solid #c8c8c8;">&nbsp;</td>
      <td style="border-bottom: 2px solid #c8c8c8;">&nbsp;</td>
      <td style="border-bottom: 2px solid #c8c8c8;">&nbsp;</td>
  	</tr>
    <?php endwhile; ?>
  </table>
</div><?php endif; ?>
```

### Personalize hard tickets

There are two supported cases for selling identifiable hard tickets:

#### Hard tickets have a white cut-out

Imagine you order hard tickets for you event. You can design it however you want to. You just need to leave a white
cut-out on the ticket, where you can print the barcode on.

In the Contao backend, go to "Events". Configure the current event and click on "Pre printed tickets". Now you enter the
facts of your ticket:
1) The width and height of one single hard ticket
2) The elements you want to print on the ticket, most likely the barcode and/or ticket number. Therefore you have to
enter the position of the white cut-out of your hard ticket as x- and y-coordinate in millimeters.
3) Specify the font to use (for the ticket number) and the size of the QR-Code, barcode etc.

Now, you are supposed to create a new Agency ("Vorverkaufsstelle"). Define, how many tickets they get to sell. Then
click on the button "export pdf" for the particular agency. You get a pdf with the size and elements defined before.
Place your hard tickets in the printer and print the pdf.

#### Self-printed tickets

Imagine you want to print the hard tickets on your own, because you only print a few or don't use that much color, so
that ordered hard tickets of a print shop are not profitable.

After you created an event, create a new Agency ("Vorverkaufsstelle"). Define, how many tickets they get to sell. Then
click on the button "export" for the particular agency. You will get the ticket numbers and bar codes as a CSV-file. Now
the best idea is to download a "Code 3 of 9 font". Install this font on your computer. Then create a new InDesign
document and design your ticket. Cut-out the barcode. Now you use InDesigns function of "Datenzusammenführung", to load
the CSV file within InDesign and create identifiable tickets. Print them.

### Usage of the TicketPay app

You need to point the url `https://api.ticketpay.de/` to your contao installation.
Then you can log in with your contao credentials. The events, orders and tickets will get fetched via this url and you
are ready to perform the checkin.

To check the entrypoint is configured properly, you can request
`https://api.ticketpay.de/api/userLogin?username=test&password=test` and should get the message:

```json
{"Errorcode":1,"Errormessage":"Unbekanntes Terminal"}
```

License
-------

The GNU Lesser General Public License (LGPL) v3.

Keep in mind:

THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING
THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES PROVIDE THE PROGRAM “AS IS” WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR
IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.
THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE DEFECTIVE, YOU
ASSUME THE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.
