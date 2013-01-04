=== Simple Ajax Chat ===

Plugin Name: Simple Ajax Chat
Plugin URI: http://perishablepress.com/simple-ajax-chat/
Description: Displays a fully customizable Ajax-powered chat box anywhere on your site.
Author: Jeff Starr
Author URI: http://monzilla.biz/
Contributors: specialk
Donate link: http://digwp.com/book/
Requires at least: 3.4
Tested up to: 3.5
Version: 20130103
Stable tag: 20130103
License: GPLv2 or later
Usage: Visit the plugin's settings page for shortcodes, template tags, and more information.
Tags: chat, box, ajax, forum

Simple Ajax Chat displays a fully customizable Ajax-powered chat box anywhere on your site.

== Description ==

Simple Ajax Chat makes it easy for your visitors to chat with each other on your website. There already are a number of decent chat plugins, but I wanted one that is simple yet fully customizable with all the features AND outputs clean HTML markup.

**Features**

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

Activate the plugin and visit the SAC settings page to customize your options.

Once everything is customized as you like it, display the form anywhere using the shortcode or template tag.

**Shortcode**

Use this shortcode to display the chat box on a post or page:

`[sac_happens]`

**Template tag**

Use this template tag to display the chat box anywhere in your theme template:

`&lt;?php if (function_exists('simple_ajax_chat')) simple_ajax_chat(); ?&gt;`

== Upgrade Notice ==

To upgrade, simply upload the new version and you should be good to go.

== Screenshots ==

Screenshots available at the [SAC Homepage](http://perishablepress.com/simple-ajax-chat/#screenshots).

Live Demo available at [WP-Mix](http://wp-mix.com/chat/).

== Changelog ==

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

I created this plugin with love for the WP community. To show support, consider purchasing my new book, [.htaccess made easy](http://htaccessbook.com/), or my WordPress book, [Digging into WordPress](http://digwp.com/).

Links, tweets and likes also appreciated. Thanks! :)
