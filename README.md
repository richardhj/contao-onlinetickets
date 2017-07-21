Online tickets extension for Isotope eCommerce
==============================================
*Sell online tickets with Isotope eCommerce*

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
### Installtion with Composer:

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
* Create a customized isotope document

### Usage of the TicketPay app

You need to point the url `https://api.ticketpay.de/` to your contao installation or you need to enter the IPv4 address pointing to your contao installtion.
Then you can log in with your contao credentials. The events, orders and tickets will get fetched and you are ready to perform the checkin.
