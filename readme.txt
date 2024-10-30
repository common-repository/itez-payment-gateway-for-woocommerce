=== Bitcoin Widgets ===
Contributors: Itez.com
Tags: bitcoin, crypto, widget, cryptocurrency, woocommerce, itez
Requires at least: 4.6
Tested up to: 5.5.3
Stable tag: 1.0
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Using WooCommerce you can accept payment for orders in BTC.

== Description ==

Simple gateway which allows your customers to pay for goods with a bank card, and you can receive payment for purchases in Bitcoin to your crypto-wallet automatically.

Current options of widget:

*   Fiat Currencies Support: USD, RUR, EUR, UAH, INR. KRW, TRY, GBP, BRL, AUD, HKD, TWD.
*   Wallet address for payout

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to "WooCommerce" -> "Settings" -> "Payments"
4. Click on the "Itez Pay" method
5. In the section that opens, you will see the following fields:
5.1. Enable / Disable - a checkbox that activates or deactivates the payment method
5.2. Title - the name of the payment method that the user will see when choosing payment methods in the checkout
5.3. Description - a description of the payment method that the user will see when choosing payment methods in the checkout
5.4. BTC wallet address - your BTC wallet, where funds from buyers will be received
5.5. Store currency - the currency in which the user will pay
5.6. Itez Secret (Key) - unique partner key, you must request from Itez technical support
5.7. Partner Token - partner token, you must request from Itez technical support
6. After making changes, they must be saved by clicking on the appropriate button below the fields.

7. Note: when receiving the key and token, you must pass the URL to which the callbacks will be sent. The URL will look like this: https: //www.yourstore.com/?wc-api=itezpay, where www.yourstore.com must be replaced with your store URL


== Frequently Asked Questions ==

= How fast is Bitcoin transferred to my wallet? =

The operation is automatic and the time of enrollment depends on the network. This usually happens within 30-60 minutes.

= What is the processing fee? =

You don't pay any commissions.

= Where can I get the secret key and api key? =

To do this, you need to write to Itez technical support (support@itez.com) with the address of your website, your email address and the address for callbacks. Read more in the technical documentation https://docs.itez.com/en/api/#callbacks.

== Screenshots ==

1. Standart gateway view

== Changelog ==

= 1.0 =
* Initial release

== Used services ==

Service link: https://itez.com/
Service description: This service help you to receive some information about buying cryptocurrency