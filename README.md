Online tickets extension for Isotope eCommerce
==============================================
*Sell online tickets with Isotope eCommerce*

**This extension requires a high amount of Contao/Isotope know-how and a basic understanding of your home network and routing domains/ip addresses.**

What it does
============

### Before the event

You can sell online tickets for your events. After successful order, these online tickets get a QR-code and are home printable for the customer.

If you sell hardtickets and want to track sales, this extension provides tool to print your hardtickets with cusotmized barcodes.


### During the event

#### Check in

Check in all sold tickets (hardtickets or online tickets) and track venue's degree of capacity.

The included api is fully compatible with the ones of [TicketPay](http://ticketpay.de/). Install the iOS native [mobile app](http://ticketpay.de/app-demo/) to perform the checkin.

![app](http://ticketpay.de/wp-content/uploads/2013/09/app1.png)

#### Management board / Box office

There is a management board included. Place the according frontend module on a protected page in the frontend.
The management board helps you to track the sales, checkins live, track box office sales, revert checkins.

### After the event

Export an excel report with all tickets sold, find out the most important ticket agency, get to know, when visitors check in most frequently.

How to use it
=============

### Installation with Composer:

Add these minimum configuration to your composer.json.

```json
{
    "require": {
        "richardhj/contao-onlinetickets": "^0.9.1"
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

To export the online tickets within an isotope document (e.g. the invoice), simple modify the `iso_document_….html5`. Add the following lines to the end of the template file.

```php
<?php if (null !== ($objTickets = Richardhj\Isotope\OnlineTickets\Model\Ticket::findByOrder($this->collection->id))): ?><div style="font-size: 72.5%; font-family: Helvetica, sans-serif; float:left; page-break-before:always;">
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
      <td rowspan="3"><img src="<?php echo Richardhj\Isotope\OnlineTickets\Helper\QrCode::getLocalPath($objTickets->hash); ?>" alt="Ticket Code"></td>
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

### Usage of the TicketPay app

You need to point the url `https://api.ticketpay.de/` to your contao installation or you need to enter the IPv4 address pointing to your contao installtion.
Then you can log in with your contao credentials. The events, orders and tickets will get fetched and you are ready to perform the checkin.

To check the entrypoint is configured properly, you can request `https://api.ticketpay.de/api/UserLogin?username=test&password=test` or `192.168.0.…/api/UserLogin?username=test&password=test` (one of the endpoints should work) and should get the message:
```json
{"Errorcode":1,"Errormessage":"Zugangsdaten nicht richtig"}
```