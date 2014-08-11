=== Relevant - Related Posts Plugin ===
Contributors: bestwebsoft
Donate link: https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=1&product_id=94
Tags:  add meta keys for posts, category, display related posts, meta key, plugin, posts, related, releited posts, related category posts, related posts, related posts plugin, related tag posts, related title posts, related words in posts, related posts widget, ralavant, relevant, relevent, relevant plugin, similar words in posts, shortcode, tag, title, widget
Requires at least: 3.0
Tested up to: 3.9.2
Stable tag: 1.0.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to display related posts with similar words in category, tags, title or by adding special meta key for posts.

== Description ==

Related Posts Plugin allows to display a list of post titles by the widget or by the shortcode. It displays related posts with similar words in category, tags, title or by adding special meta key for posts. Related Posts Plugin is simple to use and to customize - this is what you are looking for.

http://www.youtube.com/watch?v=WfTT6xSgrKI

<a href="http://wordpress.org/plugins/relevant/faq/" target="_blank">FAQ</a>
<a href="http://support.bestwebsoft.com" target="_blank">Support</a>

= Features =

* Display: It displayes related posts by category, meta key, tags or title.
* Title: You can add your own title shortcode list of similar posts. Just write your own frase in the input with title: "Heading the list of similar posts:". Also you can write HTML attribute to this phrase.
* Display: You can choose the number of posts to show in the input with label: "How many posts to display: ".
* Display: Enter the text to show if there will be no posts to show in the input with label: "Message if do not have related posts:".

= Recommended Plugins =

The author of the Relevant also recommends the following plugins:

* <a href="http://wordpress.org/plugins/updater/">Updater</a> - This plugin updates WordPress core and the plugins to the recent versions. You can also use the auto mode or manual mode for updating and set email notifications.
There is also a premium version of the plugin <a href="http://bestwebsoft.com/plugin/updater-pro/?k=fea5746dc4c898e318c1ab7b6b792328">Updater Pro</a> with more useful features available. It can make backup of all your files and database before updating. Also it can forbid some plugins or WordPress Core update.

= Translation =

* Russian (ru_RU)
* Ukrainian (uk)

If you create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text in PO and MO files</a> for <a href="http://support.bestwebsoft.com" target="_blank">BestWebSoft</a> and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO files <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in another languages won't be accepted.

If you notice any bugs in the plugins, you can notify us about it and we'll investigate and fix the issue then. Your request should contain URL of the website, issues description and WordPress admin panel credentials.
Moreover we can customize the plugin according to your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share with the other users then. 
We can fix some things for free for the users who provide translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation ==

1. Upload `relevant` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in your WordPress admin panel.
3. You can adjust necessary settings through your WordPress admin panel in "Settings" > "Related Posts Plugin".
4. Create a page or a post and insert shortcode [bws_related_posts] into the text.
5. Add Related Posts Plugin widget to the widget area.

== Frequently Asked Questions ==

= How to use the other language files with the Related Posts Plugin? = 

Here is an example for Russian language files:
1. In order to use another language for WordPress it is necessary to set the WP version to the required language and in configuration wp file - `wp-config.php` in the line `define('WPLANG', '');` write `define('WPLANG', 'ru_RU');`. If everything is done properly the admin panel will be in Russian.
2. Make sure that there are files `ru_RU.po` and `ru_RU.mo` in the plugin (the folder languages in the root of the plugin).
3. If there are no such files it will be necessary to copy other files from this folder (for example, for Italian language) and rename them (you should write `ru_RU` instead of `it_IT` in the both files).
4. The files are edited with the help of the program Poedit - http://www.poedit.net/download.php - please load this program, install it, open the file with the help of this program (the required language file) and for each line in English you should write translation in Russian.
5. If everything is done properly all lines will be in Russian in the admin panel and in the frontend.

= How to add Related Posts with shortcode =

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

Please make sure that the problem hasn't been discussed yet on our forum (<a href="http://support.bestwebsoft.com" target="_blank">http://support.bestwebsoft.com</a>). If no, please provide the following data along with your problem's description:
1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: <a href="https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit" target="_blank">Instuction on System Status</a>

== Screenshots ==

1. Related Posts Plugin Settings page.
2. RelaTed Posts Plugin in post using shortcode.
3. Related Posts Plugin Widget.

== Changelog ==

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

= V1.0.8 =
Security Exploit was fixed.

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
