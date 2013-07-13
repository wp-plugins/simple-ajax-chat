=== Simple Ajax Chat ===

Plugin Name: Simple Ajax Chat
Plugin URI: http://perishablepress.com/simple-ajax-chat/
Description: Displays a fully customizable Ajax-powered chat box anywhere on your site.
Author: Jeff Starr
Author URI: http://monzilla.biz/
Contributors: specialk
Donate link: http://m0n.co/donate
Requires at least: 3.4
Tested up to: 3.5
Version: 20130712
Stable tag: trunk
License: GPL v2 or later
Usage: Visit the plugin's settings page for shortcodes, template tags, and more information.
Tags: chat, box, ajax, forum, private, avatars, filtering, smilies, secure, antispam, html5

Simple Ajax Chat displays a fully customizable Ajax-powered chat box anywhere on your site.

== Description ==

Simple Ajax Chat makes it easy for your visitors to chat with each other on your website. There already are a number of decent chat plugins, but I wanted one that is simple yet fully customizable with all the features AND outputs clean HTML markup.

**Features**

* NEW: strong anti-spam filters
* Plug-n-play functionality
* No configuration required, just include shortcode or template tag
* Display on any post or page with the shortcode
* Display anywhere in your theme template with the template tag
* Includes default CSS styles and enables custom CSS from the settings
* JavaScript/Ajax goodness loads new chats without refreshing the page
* Also works when JavaScript is not available on the user's browser
* Clean markup makes it easy to style the appearance as you please
* New chat messages fade-in with custom duration and color
* Includes manage-chats panel for editing and deleting chats
* Links included in chats include `_blank` target attributes
* Includes complete map of all available CSS hooks
* Includes built-in banned-phrases list
* Automatic smileys supported :)
* On-demand restoration of all default settings
* Super-slick toggling settings page
* Option to play sound alert for chat messages

**Customize everything**

* Customize the update interval for the Ajax-requests
* Customize the fade-duration for new chat messages
* Customize the intro and outro colors for new chats
* Option to require login/registration to participate
* Option to enable/disable URL field for usernames
* Option to use textarea for larger input field
* Customize the default message and admin name
* Customize the appearance with your own CSS
* Option to enable/disable custom styles
* Option to load the JavaScript only when the chat box is displayed
* Add custom content to the chat box and chat form
* Built-in control panel to edit and delete chats
* Built-in blacklist to ban specific phrases from the chat

== Installation ==

**Installation**

Activate the plugin and visit the SAC settings page to customize your options.

Once everything is customized as you like it, display the form anywhere using the shortcode or template tag.

**Upgrading**

If you are upgrading the plugin, be sure to backup your existing SAC settings (as a precaution). 

Then upgrade normally, check that the settings are good, and delete the plugin's only `/images/` directory. Done.

**Shortcode**

Use this shortcode to display the chat box on a post or page:

`[sac_happens]`

**Template tag**

Use this template tag to display the chat box anywhere in your theme template:

`&lt;?php if (function_exists('simple_ajax_chat')) simple_ajax_chat(); ?&gt;`

**Stopping spam**

This plugin works in two modes:

* "Open air" mode - anyone can comment
* "Private" mode - only logged in users may comment

In terms of chat spam, the "open air" mode is much improved at blocking spam, but some spam still gets through the filters. As a general rule, the longer your chat forum is online, the more of a target it will be for spammers.

If you absolutely don't want any spam, run the plugin in "private" mode. In private mode, the chat forum will require login to view and use, and no spam should make it through.

Alternately/optionally you may use the included .htaccess file to add some simple rules to block users by IP and other variables.

**Other notes**

If the chat form looks messed up on your theme, try disabling the checkbox for "Enable custom styles?"

If that doesn't help, you can include your own custom CSS. To do so, replace the "Custom CSS styles" with your own, and then enable the "Enable custom styles?" setting. Alternately, you may include custom CSS via your theme's stylesheet.

== Upgrade Notice ==

To upgrade, simply upload the new version and you should be good to go.

== Screenshots ==

Screenshots available at the [SAC Homepage](http://perishablepress.com/simple-ajax-chat/#screenshots).

Live Demo available at [WP-Mix](http://wp-mix.com/chat/).

== Changelog ==

= 20130712 =

* Reorganized file/directory structure
* Separated Ajax stuff from core plugin
* Implemented strong anti-spam measures
* Many functions rewritten to maximize native WP functionality
* Improved audio support for chat alerts, fixed Safari bug
* Fixed: case-insensitive banned phrases
* Fixed: default options not working on install
* Fixed: a bunch of annoying PHP Notices
* Added .sac-reg-req for registration message div#sac-panel
* Updated CSS skeleton with new selector (@ "/resources/sac.css")
* Fixed: enable/disable links for usernames now works properly
* General code check n clean
* added comments to the .htaccess file (no active rules are included)

= 20130104 =

* Added JavaScript to set up sound-alerts (fixes undefined variable error)

= 20130103 =

* Added margins to submit buttons (now required in WP 3.5)
* Added "div#sac-panel p {}" to default CSS
* Added links to demo in readme.txt file
* Updated all instances of $wpdb->prepare with new syntax
* Added option for sound to play for new chat messages (note: chat-sound technique is borrowed from "Pierre's Wordspew")

= 20121206 =

* Edited line 217 to define variable and fix "timeout" error
* Enhanced markup for custom content
* Custom content may be added before and/or after the chat form and/or the list of chat messages

= 20121119 =

* Fixed PHP Warning: [function.stristr]: Empty delimiter (line 282)
* Removed fieldset border in default form styles (plugin settings)
* Added placeholders for name, URL, and chat message

= 20121110 =

* Initial release.

== Frequently Asked Questions ==

To ask a question, visit the [SAC Homepage](http://perishablepress.com/simple-ajax-chat/) or [contact me](http://perishablepress.com/contact/).

== Donations ==

I created this plugin with love for the WP community. To show support, consider purchasing one of my books: [The Tao of WordPress](http://wp-tao.com/), [Digging into WordPress](http://digwp.com/), or [.htaccess made easy](http://htaccessbook.com/).

Links, tweets and likes also appreciated. Thanks! :)
