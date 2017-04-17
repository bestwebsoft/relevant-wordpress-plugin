=== Relevant - Related Posts by BestWebSoft ===
Contributors: bestwebsoft
Donate link: https://bestwebsoft.com/donate/
Tags: related posts, relevant posts, relevant, relevant plugin,add meta keys for posts, display related posts, related category posts, related posts widget, related tag posts, related title posts, related words in posts, related posts plugin
Requires at least: 3.8
Tested up to: 4.7.3
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add related posts to WordPress website posts or widgets. Link your readers to relevant content.

== Description ==

Simple plugin which adds related post widget to your WordPress website posts and widgets. Display related posts based on category, tags, title or meta keywords.

Show related posts to your visitors!

https://www.youtube.com/watch?v=WfTT6xSgrKI

= Features =

* Choose the number of related posts to display
* Change related posts list title
* Change empty state message if related posts are missing
* Display related posts in posts and pages based on:
	* Categories
	* Tags
	* Title
	* Meta keyword
* Add related posts via shortcode
* Add related posts to widgets
* Enable related posts widget in category/tag/index/home page
* Show posts thumbnails
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

* [Updater](https://bestwebsoft.com/products/wordpress/plugins/updater/?k=fea5746dc4c898e318c1ab7b6b792328) - Automatically check and update WordPress core with all installed plugins to the latest versions. Manual mode, email notifications and backups of all your files and database before updating.
* [Featured Posts](https://bestwebsoft.com/products/wordpress/plugins/featured-posts/) - Add featured posts to WordPress posts or widgets. Highlight important information.
* [Latest Posts](https://bestwebsoft.com/products/wordpress/plugins/latest-posts/) - Add latest posts or latest posts for selected categories widgets to WordPress website.
* [Popular Posts](https://bestwebsoft.com/products/wordpress/plugins/popular-posts/) - Track views, comments and add most popular posts to Wordpress widgets.

== Installation ==

1. Upload `relevant` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in your WordPress admin panel.
3. You can adjust necessary settings through your WordPress admin panel in "BWS Panel" > "Related Posts Plugin".
4. Create a page or a post and insert shortcode [bws_related_posts] into the text.
5. Add Related Posts Plugin widget to the widget area.

[View a Step-by-step Instruction on Relevant - Related Posts Plugin Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)

https://www.youtube.com/watch?v=jcCbaAy_uOc

== Frequently Asked Questions ==

= How to add Related Posts with shortcode? =

Everything you need is to place the code [bws_related_posts] into the page or post.

= I placed shortcode [bws_related_posts] in text widget and it doesn't work =

You don't need to use shortcode in widget - Related Posts Plugin has it's own widget. You just need to drag it into the wigdet area.

= What does mean radio buttons Category, Tags, Title & Key on the settings page? = 

Depending of your choise plugin display related post:

- if you select option Category - related post will display by similar words in categories;
- if you select option Tags related post will display by similar words in tags;
- if you select option Title related post will display by similar words in title of the posts and pages;
- if you select option Meta Key related post will display by meta key.

= I choosed Meta Key in the settings, but it diplayes nothing =

After you choosed "Meta Key" you should do follow:

- go to the "All posts";
- choose post which you want to display by our plugin;
- click "edit";
- bellow the form of post, you will see the next field: "Check 'Key' if you want to display this post with Related Posts Plugin";
- check the radio button "Key";
- save the post;
- repeat all this step for each post you want to display as related.

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<https://support.bestwebsoft.com>). If no, please provide the following data along with your problem's description:

1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: [Instruction on System Status](https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit)

== Screenshots ==

1. Related Posts Plugin in post using shortcode.
2. Related Posts Plugin Widget.
3. Related Posts Plugin Settings page.

== Changelog ==

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
