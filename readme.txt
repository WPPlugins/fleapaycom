=== Fleapay ===
Contributors: zwilson, fsimmons
Tags: ecommerce, e-commerce, online, store, gateway, merchants, web, developers, designers
Donate link: https://www.fleapay.com/fleapay/e0e4f81702
Requires at least: 2.8
Tested up to: 3.6
License: GPLv2 or later
Stable tag: 0.7.6.1

Fleapay is a shopping cart that creates simple payment buttons -- add an Online Store on your site in minutes!
== Description ==

Fleapay is a simple shopping cart system that works with your existing website and payment gateway. Get Fleapay today and start accepting credit cards from your website.

Create Payment Buttons in minutes!

If you want to use a gateway, such as Authorize.net, Braintree, PayTrace, SecureNet or TransFirst, Fleapay is the FASTEST way to integrate payments into your site.

Features in Fleapay include:

* Provide access to Fleapay API for inserting products into pages and posts
* If your web host is unable to reach Fleapay's servers, the plugin will automatically retry when your connection is back up

PS: You'll need an [Fleapay.com API key](http://www.fleapay.com) to use it.

== Installation ==

Upload the Fleapay plugin to your blog, Activate it, then enter your [Fleapay.com API key](http://www.fleapay.com).

1, 2, 3: You're done!

== Frequently Asked Questions ==

Q: Do I need a merchant/gateway?

A: Currently yes. However, soon Fleapay will not require an existing merchant/gateway and will be able to be used free of any dependencies.

Q: What merchants/gateways are supported?

A: www.authorize.net, www.braintreepayments.com, www.paytrace.com, www.securenet.com, www.transfirst.com...

Q: What gateways are you working on supporting?

A: Coming soon nPayPal Payflow Pro, PayPal Website Payments Pro, TransAction Express, GlobalPay and Vanco Services.

== Screenshots ==

1. Add unlimited products to pages and/or pages. `screenshot-1.png`
2. Search for your Fleapay.com products instantly. `screenshot-2.png`
3. Add and remove any products. `screenshot-3.png`

== Changelog ==

= 0.7.6.1 =
* fixed: debugging default off

= 0.7.6 =
* fixed: WP 3.6 compatibility testing
  fixed: jqueryUI 1.10.3 update; force load google's jqueryUI core to include all plugins

= 0.7.5 =
* fixed: shorter cache interval for searching for products.
  fixed: enable FLEAPAY_DEBUG or set FLEAPAY_CACHE_EXPIRE to zero to have cache disabled for product searching.
  fixed: WP 3.5 compatibility testing

= 0.7 =
* bug: issue with site content fleapay api not present
  fixed: display issue with for donations and other open pricing types. Doesn't display 'Gift', 'Free' or '$0.00' any longer, just the title name
  fixed: spacing issues in code for better readability

= 0.6 =
* bug: conflicts with other plugins when detecting fleapay requirements

= 0.5 =
* added: supports any custom post type

= 0.4 =
* fixed: issue with content not appear when products were not attached to pages/posts

= 0.3 =
* API adjustment. Prices are returned localized.

= 0.2 =
* Style adjustments
* Provided setting to alter button text for front-end, default "Add To Cart"

= 0.1 =
* Provide access to Fleapay API for inserting products into pages and posts

== Upgrade Notice ==

Nothing to report right now! Whew...
