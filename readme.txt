=== Simple Ajax Chat ===

Plugin Name: Simple Ajax Chat
Plugin URI: http://perishablepress.com/simple-ajax-chat/
Description: Displays a fully customizable Ajax-powered chat box anywhere on your site.
Author: Jeff Starr
Author URI: http://monzilla.biz/
Contributors: specialk
Donate link: http://m0n.co/donate
Requires at least: 3.7
Tested up to: 4.0
Version: 20140923
Stable tag: trunk
License: GPL v2 or later
Usage: Visit the plugin's settings page for shortcodes, template tags, and more information.
Tags: chat, box, ajax, forum, private, avatars, filtering, smilies, secure, antispam, html5

Simple Ajax Chat displays a fully customizable Ajax-powered chat box anywhere on your site.

== Description ==

Simple Ajax Chat makes it easy for your visitors to chat with each other on your website. There already are a number of decent chat plugins, but I wanted one that is simple yet fully customizable with all the features AND outputs clean HTML markup.

**Features**

* Strong anti-spam filters
* Plug-n-play functionality
* Designed to be the simplest possible *persistent* chat
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
* Timestamp for each chat message
* Display chat messages in ascending or descending order
* NEW: option to use logged-in username as the chat name

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

= 20140923 =

* Tested on latest version of WordPress (4.0)
* Increased minimum version requirement to WP 3.7
* Added conditional check to min-version function
* Added option to display logged-in username as chat name
* Improved logic of simple_ajax_chat()
* Improved logic of sac_addData()
* Improved logic in core and admin files
* Increased default username max-length
* Fine-tuned plugin settings page
* Removed vestigial killswitch variable
* Fixed issue where special characters were not displaying correctly
* Replaced hardcoded paths with WP tags (e.g., wp-content directory)
* Replaced $user_nickname global with wp_get_current_user()
* Minified portions of the SAC JavaScript file for better performance
* Added conditional check for $sac_lastID is numeric
* Now using sanitize_text_field() for IPs
* Replaced htmlspecialchars() with sanitize_text_field()
* Replaced sac_special_chars() with esc_url() for user URL
* Replaced htmlentities(), stripslashes(), sac_clean() with sanitize_text_field()
* Replaced PHP tags with WP tags in sac_special_chars()
* Updated mo/po translation files

= 20140305 =

* New feature: added setting to display chats in ascending or descending order (beta)
* Improved logic for creating chat db table, fixes "mysql_list_tables" deprecated error
* Added various CSS selectors to chat messages for custom styling
* Added support for localization/translation

= 20140123 =

* Tested with latest WordPress (3.8)
* Added trailing slash to load_plugin_textdomain()
* Fixed 3 incorrect _e() tags in simple-sjax-chat-admin.php
* Edited setting description for "Require log in?" for accuracy

= 20131107 =

* Removed `delete_option('sac_delete');` from uninstall.php
* Replaced `application/x-javascript` with `` in sac.php
* Replaced `add_plugin_links` with `add_sac_links` in simple-ajax-core.php

= 20131106 =

* Replaced original header codes and WP includes in sac.php

= 20131105 =

* Removed 3x "&Delta;" from die() for better security
* Added "rate this plugin" link on Plugins and SAC settings screens
* Replaced 3x "wpdb->escape" with "esc_sql" in simple-ajax-chat-core.php
* Filter server variables with built-in simple-ajax-chat-admin.php (lines 65/66)
* Improved security when submitted chat fails (simple-ajax-chat.php)
* Specified no border for smileys in filter_smilies()
* Added localized timestamp of last chat to span.name in sac.php
* Localized "ago" in sac-admin, sac-core, and sac-form
* Localized sac_time_since() in simple-ajax-core.php
* Improved header codes and WP includes in sac.php
* Fixed bug where chats don't work if audio is disabled
* Added uninstall.php to remove options and chat table upon uninstall
* Enhanced functionality of plugin settings page
* Tested with latest version of WordPress (3.7)
* General code maintenance and cleanup
* Added support for localization

= 20130725 =

* Tightened form security
* Tightened plugin security
* Updated deprecated functions
* Resolved some PHP Notices

= 20130713 =

* Improved localization support
* Replaced some deprecated template tags

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

Question: "Can we auto delete after some minutes all the chats?"

Answer: Yes, please see this post: [WordPress Cron Tips](http://wp-mix.com/wordpress-cron-tips/)

Question: "I'm interested to know if your chat plugin has the option to respond to chats via an iPhone app or another chat software. I didn't see how the chats are received."

Answer: Yep, the chat plugin works great on iPhones, Android devices, and more.. the functionality is achieved using Ajax.

Question: "In some cases a backslash is added before a single apostrophe "`'`", how do I fix?"

Answer: In the plugin settings, add a backslash "`\`" to the exclude list ("Banned Phrases" panel).

Question: "How do I change the maximum number of characters/messages?"

Answer: This is possible by editing the variables in `simple-ajax-core.php` (see "plugin variables"). Note: the planned "Pro" version of the plugin will include plugin settings for controlling these variables.

Question: "Is it possible to whitelist SAC plugin files?"

Answer: Yes, check out [Simple Ajax Chat .htaccess whitelist](http://wp-mix.com/simple-ajax-chat-htaccess-whitelist/) and/or [Whitelist POST access with .htaccess](http://wp-mix.com/whitelist-post-access-htaccess/)

To ask a question, visit the [SAC Homepage](http://perishablepress.com/simple-ajax-chat/) or [contact me](http://perishablepress.com/contact/).

== Donations ==

I created this plugin with love for the WP community. To show support, consider purchasing one of my books: [The Tao of WordPress](http://wp-tao.com/), [Digging into WordPress](http://digwp.com/), or [.htaccess made easy](http://htaccessbook.com/).

Links, tweets and likes also appreciated. Thanks! :)
