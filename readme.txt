=== CSP-ANTS&ST ===
Contributors: pcescato
Tags: csp, content-security-policy, nonces, security headers, sha256 hashes
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 7.3
Stable tag: 1.1.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html

Add a nonce to each script and style tags, sha256 hashes to inline events, and set them in CSP header.

== Description ==

For a perfectly secured website, you have to avoid 'unsafe-eval' and 'unsafe-inline' in your content-security-policy header.
This plugin add nonces to script/style tags and add those nonces to the content-security-policy header, so your website will be more secure, even if there are other actions to perform in order to have a very strong protection.
= Features =

There are no settings, it's a plug and play plugin.
This plugin automaticallly:
- add a nonce to each script and style tag and a sha256 hash to online events (onload / onclick)
- generate Content Security Policy header with all nonces and hashes + basics (base-uri 'self', google fonts, gravatar, maxcdn.bootstrapcdn…)

Tested / Works with no cache system, WP Rocket on Plesk (Nginx/Apache webserver) and Lscache (Openlitespeed/Litespeed webserver)
Should work elsewhere, just say me and I'll add your setup to this list.

= Requirements =

* WordPress 5.0 or higher.
    	
== Installation ==

* Extract the zip file and just drop the contents in the <code>wp-content/plugins/</code> directory of your WordPress installation or install it directly from your dashboard and then activate the plugin from Plugins page.
* There's not options page, simply install and activate.

== Frequently Asked Questions ==

= Is there something to do after install? =

Yes, just activate it!
  
== Changelog ==

= 1.0 =

* Initial release