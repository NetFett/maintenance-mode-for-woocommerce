=== Maintenance mode for WooCommerce ===
Contributors: netfett
Tags: woocommerce maintenance, maintenance, under construction, maintenance mode
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Requires Plugins: woocommerce
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 9.0
WC tested up to: 10.9.4

Maintenance mode for the Woocommerce Shop

== Description ==

The maintenance mode only puts the WooCommerce Shop into maintenance mode, but not your entire website. Decide for yourself which text or content should be displayed.

Possible reason for the "Coming soon" mode:
Your shop is not quite finished yet, but you want to show your visitors that a shop is about to open on your website.

Possible reason for the "maintenance mode":
You made an error while configuring or maintaining your products, and you can't find the error right away. Then switch your shop to "maintenance mode" and take your time to correct the error.

And the best thing is: it's free!
Yes, it's absolutely free. There is no premium version.

== Installation ==

1. Upload `maintenance-mode-for-woocommerce.zip` to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create a page and fill it with content.
4. You find the settings in the Woocommerce-Menu under "Maintenance mode".

== Changelog ==
= 1.3.0 =
* New: Added WPML multilingual plugin support (automatically serves the translated maintenance page).
* New: Added frontend notice banner for administrators and shop managers when maintenance mode is active.
* Improvement: Infinite redirect loop protection (safeguard preventing ERR_TOO_MANY_REDIRECTS if the maintenance page triggers WooCommerce checks).
* Improvement: Improved WooCommerce detection for WordPress multisite network installations.

= 1.2.4 =
* Cleanup: Removed local translation files in favor of official WordPress.org language packs.
* Improvement: Simplified translation loading logic to better support WordPress.org automatic updates.

= 1.2.3 =
* Security: Added protection against direct file access to all PHP files.
* Improvement: Modernized translation loading logic for better local translation priority and compatibility with WordPress standards.
* Tested with latest WordPress and WooCommerce versions.

= 1.2.2 =
* Tested with new version of Woocommerce 9.7.1 and WordPress 6.8. PHP 8 ready.

= 1.2.1 =
* Tested with new version of Woocommerce and WordPress. PHP 8 ready.

= 1.2 =
* Tested with new version of Woocommerce and WordPress 6.7.
* Bugfix translation and other stuff.

= 1.1.1 =
* NEW: Plugin Polylang supported.

= 1.1 =
* Tested with new version of Woocommerce, WordPress 6.5 and PHP 8.1.

= 1.0.19 =
* Tested with new version of Woocommerce, WordPress 6.4 and PHP 8.1.
* Add error-message, if Woocommerce is not active or installed.

= 1.0.18 =
* HPOS ( High-Performance Order Storage for WooCommerce ) compatible.

= 1.0.17 =
* Tested with new version of Woocommerce 8.1.1, WordPress 6.3.1 and PHP 8.1.

= 1.0.16 =
* Tested with new version of Woocommerce 8.0 and WordPress 6.3. PHP 8 ready.

= 1.0.15 =
* Tested with new version of Woocommerce 6.3.1 and WordPress 5.9.2. PHP 8 ready.

= 1.0.14 =
* Tested with new version of Woocommerce 5.9 and WordPress 5.8.1. PHP 8 ready.

= 1.0.13 =
* Tested with new version of Woocommerce 5.7 and WordPress 5.8.1. PHP 8 ready.

= 1.0.12 =
* Tested with new version of Woocommerce 5.6. PHP 8 ready.

= 1.0.11 =
* Tested with new versions of WP and WC. PHP 8 ready.

= 1.0.10 =
* Tested with new versions of WP and WC. PHP 8 ready.

= 1.0.9 =
* Tested with new versions of WP and WC.

= 1.0.8 =
* New language "spanish".

= 1.0.7 =
* New feature: Select whether logged-in users are also blocked.

= 1.0.6 =
* PHP 8 ready. Tested with new versions of WP and WC.

= 1.0.5 =
* Tested with new versions of WP and WC.

= 1.0.3 =
* Bugfix ( Stop plugin, when WooCommerce is deactivated )

= 1.0.2 =
* Add german translation.

= 1.0.1 =
* Add setting "Admin Bar note".
