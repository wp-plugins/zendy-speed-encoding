=== Zendy Speed: Encoding ===
Contributors: ZendyLabs
Tags: encoding, compression, vary, accept-encoding, caching, pagespeed, performance, fast
Requires at least: 4.0
Tested up to: 4.2.4
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Improve your Google PageSpeed grade! Zendy Speed: Encoding specifies a "Vary: Accept-Encoding" Header.

== Description ==
Improve your Google PageSpeed grade! Zendy Speed: Encoding specifies a "Vary: Accept-Encoding" Header to increase the likelyhood that requests will use compression.

== Installation ==
1. Upload the `zendy-speed-encoding` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. That's it, Enjoy!

== Frequently Asked Questions ==
= What does Zendy Speed: Encoding do? =

Zendy Speed: Encoding specifies a "Vary: Accept-Encoding" Header to increase the likelyhood that requests will use compression.

= Where can I check how good my website performance is? =

We recommend [https://developers.google.com/speed/pagespeed/insights/](Google's PageSpeed Insights) and [http://tools.pingdom.com/fpt/](Pingdom's Full Page Test Tools)

== Screenshots ==

== Changelog ==

= 2.0 =
* Initial public release
* Meta: Refactored plugin for public use

= 1.0.1 =
* Fix: htaccess file is now created if it doesn't exist

= 1.0 =
* Internal use only
* Feature: Add code to htaccess file to add a Vary: Accept-Encoding header
