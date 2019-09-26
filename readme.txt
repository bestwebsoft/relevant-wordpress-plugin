=== Relevant - Related, Featured, Latest, and Popular Posts by BestWebSoft ===
Contributors: bestwebsoft
Donate link: https://bestwebsoft.com/donate/
Tags: related posts, relevant posts, popular posts, latest posts, featured posts, posts plugin, post widgets, add meta keys for posts, most visited posts, latest blog posts, feachured post, featured posts plugin
Requires at least: 3.9
Tested up to: 5.2.3
Stable tag: 1.3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add related, featured, latest, and popular posts to your WordPress website. Connect your blog readers with a relevant content.

== Description ==

Relevant plugin displays related, latest, popular, and featured posts on your WordPress website. Customize widgets, change the appearance, number of popular posts, and much more.

Highlight and display relevant information for your readers!

https://www.youtube.com/watch?v=WfTT6xSgrKI

= Features =

* Choose different position for related, featured, latest, and popular posts:
	* Before content
	* After content
* Use separate shortcodes to add related, featured, latest, and popular posts to:
	* Posts
	* Pages
	* Custom post types
* Add related, featured, latest, and popular posts to the widgets area
* Change related, featured, latest, and popular posts:
	* Title
	* Number of posts
* Display related posts in posts and pages based on:
	* Categories
	* Tags
	* Title
	* Meta keyword
* Set custom size for featured image
* Display latest posts from a certain category
* Display popular posts for current category
* Mark any post or page as a featured
* Set featured posts:
	* Inner content width
	* Section width
* Customize featured posts color:
	* Section background
	* Text background
	* Section title
	* Description
	* Learn more link
* Changing featured posts with reloading
* Set the default image if post image is missing
* Customize Read More text and Set the excerpt length for related, featured, latest and popular posts
* Display additional related, featured, latest and popular post info:
	* Date
	* Author
	* Reading time
	* Comments number
	* Featured image
	* Excerpt
* Display additional popular post info:
	* Number of views
* Sort popular posts by number of:
	* Comments
	* Views
* Compatible with latest WordPress version
* Incredibly simple settings for fast setup without modifying code
* Detailed step-by-step documentation and videos

If you have a feature suggestion or idea you'd like to see in the plugin, we'd love to hear about it! [Suggest a Feature](https://support.bestwebsoft.com/hc/en-us/requests/new)

= Documentation & Videos =

* [[Doc] Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)
* [[Video] Installation Instruction](https://www.youtube.com/watch?v=jcCbaAy_uOc)

= Help & Support =

Visit our Help Center if you have any questions, our friendly Support Team is happy to help — <https://support.bestwebsoft.com/>

= Translation =

* Russian (ru_RU)
* Ukrainian (uk)

Some of these translations are not complete. We are constantly adding new features which should be translated. If you would like to create your own language pack or update the existing one, you can send [the text of PO and MO files](https://codex.wordpress.org/Translating_WordPress) to [BestWebSoft](https://support.bestwebsoft.com/hc/en-us/requests/new) and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO [files Poedit](https://www.poedit.net/download.php).

= Recommended Plugins =

* [Updater](https://bestwebsoft.com/products/wordpress/plugins/updater/?k=fea5746dc4c898e318c1ab7b6b792328) - Automatically check and update WordPress website core with all installed plugins and themes to the latest versions.

== Installation ==

1. Upload `relevant` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in your WordPress admin panel.
3. You can adjust necessary settings through your WordPress admin panel in "Relevant Posts".
4. Create a page or a post and insert shortcode [bws_related_posts], [bws_featured_post], [bws_latest_posts] or [bws_popular_posts] into the text.
5. Add Plugin widgets to the widget area.

[View a Step-by-step Instruction on Relevant - Related, Featured, Latest and Popular Posts Plugin Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)

https://www.youtube.com/watch?v=jcCbaAy_uOc

== Frequently Asked Questions ==

= What "Categories", "Tags", "Title" and "Meta Key" mean for "Search Related Words in" option on the Related Posts settings page? =

The plugin displays related post depending on your choice:

- if you select "Categories" option, then related posts will be displayed by similar words in categories;
- if you select "Tags" option, then related posts will be displayed by similar words in tags;
- if you select "Title" option, then related posts will be displayed by similar words in the posts and pages titles;
- if you select "Meta Key" option, then related post will be displayed by meta key.

= I chose "Meta Key" in "Search Related Words in" option on Related Posts settings page, but it displays nothing =

After you choose "Meta Key" option, you should complete the following:

1) Go to "All Posts";
2) Choose the post you would like to be displayed by our plugin;
3) Click "edit" button;
4) Find "Related Posts" section;
5) Check "Key" radio button;
6) Update/Publish the post;
7) Repeat all these steps for each post you would like to display as related.

= I have adjusted the Popular Posts settings, and yet nothing is displayed =

You have probably selected 'Views' in 'Order Posts by Number of' option on the plugin settings page. Since the plugin hasn't collected the necessary data yet, there is nothing to display. Once the users start visiting your pages, the plugin will start tracking, and the posts will be displayed in the widget (in the frontend).

= How can I add Featured Post block to my website? =

If you would like to add Featured Posts to your page or post, open the necessary posts or page in the Edit mode and mark "Enable to display this post in the Featured Posts block." checkbox.

There are several ways to add the block, please use one of them:
1. Go to Related Posts settings page, find "Block position" option and mark "Before content" and/or "After content" checkboxes.
2. Copy and paste the following shortcode into your post or page: [bws_featured_post].
3. Copy and paste the following code to the necessary place in your theme:

`<?php do_action( 'ftrdpsts_featured_posts' ); ?>`

= I set the featured image size but the plugin scaled it. How can I crop the image? =

You can manually regenerate thumbnails in any convenient way. We recommend you to use WordPress plugins in order to avoid the mistakes. Please read more here: [Regenerate Thumbnails](https://make.wordpress.org/training/handbook/user-lessons/help-regenerate-thumbnails/)

= I completed the steps you described, but Featured Post block is not displaying yet. Why? =

Please select the necessary posts you would like to display (open the necessary posts or page in the Edit mode and mark "Enable to display this post in the Featured Posts block." checkbox).

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<https://support.bestwebsoft.com>). If no, please provide the following data along with your problem's description:
- The link to the page where the problem occurs.
- The name of the plugin and its version. If you are using a pro version - your order number.
- The version of your WordPress installation.
- Copy and paste into the message your system status report. Please read more here: [Instruction on System Status](https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/)

== Screenshots ==

1. Related Posts Widget.
2. Latest Posts Widget.
3. Popular Posts Widget.
4. The displaying of Featured Posts block with Custom plugin settings (default theme).
5. The displaying of Featured Posts block with Custom plugin settings (BestWebSoft theme).
6. Related Posts Settings page.
7. Featured Posts settings.
8. Latest Posts Settings page.
9. Popular Posts Settings page.
10. Relevant widgets settings.
11. Adding the shortcode into the content.

== Changelog ==

= V1.3.8 - 26.09.2019 =
* Bugfix : The bug with the incorrect count of the views number has been fixed.
* Bugfix : The bug with the custom image sizes displaying has been fixed.

= V1.3.7 - 04.09.2019 =
* Update: The deactivation feedback has been changed. Misleading buttons have been removed.

= V1.3.6 - 01.07.2019 =
* Bugfix : The bug related to "Search Related Words" and "Search on Pages" options has been fixed.

= V1.3.5 - 09.05.2019 =
* Update : The function for returning an array of objects has been added.

= V1.3.4 - 18.12.2018 =
* Bugfix : The bug related to the featured image size, views number and content block width has been fixed. 

= V1.3.3 - 13.09.2018 =
* NEW : Ability to display posts not older than the indicated time period has been added.

= V1.2.3 - 14.06.2018 =
* NEW : Ability to configure widgets using all plugin settings has been added.
* NEW : Ability to change featured image size has been added.
* Bugfix : The bug related to the old PHP version was fixed.

= V1.2.2 - 25.01.2018 =
* NEW : Display additional related, featured and latest post info.
* NEW : Customize Read More text and Set the excerpt length for related, featured.

= V1.2.1 - 12.06.2017 =
* NEW : Latest Posts block has been added.
* NEW : Popular Posts block has been added.
* NEW : Featured Posts block has been added.
* NEW : Ability to add popular posts before and after content.
* Update : The plugin settings page has been updated.

= V1.2.0 - 17.04.2017 =
* Bugfix : Multiple Cross-Site Scripting (XSS) vulnerability was fixed.

= V1.1.9 - 12.10.2016 =
* Update : BWS plugins section is updated.

= V1.1.8 - 26.08.2016 =
* Bugfix : The error with undefined variables has been fixed.

= V1.1.7 - 11.07.2016 =
* Update : 'widget_title' filter was added.
* Update : We updated all functionality for wordpress 4.5.3.

= V1.1.6 - 18.04.2016 =
* NEW : Ability to add custom styles.

= V1.1.5 - 08.12.2015 =
* Bugfix : The bug with plugin menu duplicating was fixed.

= V1.1.4 - 01.10.2015 =
* NEW : You can include pages into related searching.
* NEW : A button for Related Posts shortcode inserting to the content was added.
* Update : Textdomain was changed.
* Update : We updated all functionality for wordpress 4.3.1.

= V1.1.3 - 28.07.2015 =
* New : Ability to restore settings to defaults.
* Update : We updated all functionality for wordpress 4.2.3.

= V1.1.2 - 26.05.2015 =
* Bugfix : We fixed a notice about Undefined index title.
* Update : We updated all functionality for wordpress 4.2.2.

= V1.1.1 - 01.04.2015 =
* Update : We updated all functionality for wordpress 4.1.1
* Bugfix : Plugin optimization is done.

= V1.1.0 - 12.01.2015 =
* Update : We updated all functionality for wordpress 4.1.

= V1.0.9 - 28.11.2014 =
* Bugfix : The bug with Related Posts Plugin Widget is fixed.
* Update : We updated all functionality for wordpress 4.0.1.

= V1.0.8 - 07.08.2014 =
* Bugfix : Security Exploit was fixed.
* NEW : Ability to show posts thumbnails.

= V1.0.7 - 20.05.2014 =
* Update : We updated all functionality for wordpress 3.9.1.
* NEW : The Ukrainian language file is added to the plugin.

= V1.0.6 - 10.04.2014 =
* Update : BWS plugins section is updated.
* Bugfix : Plugin optimization is done.

= V1.0.5 - 21.02.2014 =
* Update : Screenshots are updated.
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 3.8.1.
* Bugfix : Problem with posts marked by Meta Key is fixed.

= V1.0.4 - 26.12.2013 =
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 3.8.

= V1.0.3 - 08.10.2013 =
* NEW : Add checking installed wordpress version.
* Update : We updated all functionality for wordpress 3.7.1.
* Update : Activation of radio button or checkbox by clicking on its label.

= V1.0.2 - 02.10.2013 =
* NEW : We added BWS Menu.
* NEW : We added new screenshots.
* Update : Styles were updated.

= V1.0.1 - 04.09.2013 =
* NEW : We added plugin description and a screenshot.
* Update : Changed style and description of meta box.

= V1.0.0 - 24.08.2013 =
* Update : Improved design of code.

== Upgrade Notice ==

= V1.3.8 =
* Bugs fixed.

= V1.3.7 =
* Usability improved.

= V1.3.6 =
* Bugs fixed.

= V1.3.5 =
* Functionality improved.

= V1.3.4 =
* Bugs fixed.

= V1.3.3 =
* Functionality expanded.

= V1.2.3 =
* New features added.
* Bugs fixed.

= V1.2.2 =
* New features added.

= V1.2.1 =
* New features added.
* Usability improved.

= V1.2.0 =
* Bugs fixed.

= V1.1.9 =
* Plugin optimization completed.

= V1.1.8 =
* Bugs fixed

= V1.1.7 =
'widget_title' filter was added. We updated all functionality for wordpress 4.5.3.

= V1.1.6 =
Ability to add custom styles.

= V1.1.5 =
The bug with plugin menu duplicating was fixed.

= V1.1.4 =
You can include pages into related searching. A button for Related Posts shortcode inserting to the content was added. Textdomain was changed. We updated all functionality for wordpress 4.3.1.

= V1.1.3 =
Ability to restore settings to defaults. We updated all functionality for wordpress 4.2.3.

= V1.1.2 =
We fixed a notice about Undefined index title. We updated all functionality for wordpress 4.2.2.

= V1.1.1 =
We updated all functionality for wordpress 4.1.1. Plugin optimization is done.

= V1.1.0 =
We updated all functionality for wordpress 4.1.

= V1.0.9 =
The bug with Related Posts Plugin Widget is fixed. We updated all functionality for wordpress 4.0.1.

= V1.0.8 =
Security Exploit was fixed. Ability to show posts thumbnails.

= V1.0.7 =
We updated all functionality for wordpress 3.9.1. The Ukrainian language file is added to the plugin.

= V1.0.6 =
BWS plugins section is updated. Plugin optimization is done.

= V1.0.5 =
Screenshots are updated. BWS plugins section is updated. We updated all functionality for wordpress 3.8.1. Problem with posts marked by Meta Key is fixed.

= V1.0.4 =
BWS plugins section is updated. We updated all functionality for wordpress 3.8.

= V1.0.3 =
Add checking installed wordpress version. We updated all functionality for wordpress 3.7.1. Activation of radio button or checkbox by clicking on its label.

= V1.0.2 =
We added BWS Menu. We added new screenshots. Styles were updated.

= V1.0.1 =
We added plugin description and a screenshot. Changed style and description of meta box.

= V1.0.0 =
Improved design of code.
