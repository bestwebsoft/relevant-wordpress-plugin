=== Relevant - Related Posts Plugin ===
Contributors: bestwebsoft
Donate link: https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=10&product_id=13
Tags: related posts, posts, related, related posts plugin, plugin, related category posts, related tag posts, category, tag
Requires at least: 3.0
Tested up to: 3.6.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to display related posts with similar words in category, tags, title or by adding special meta key for posts.

== Description ==

Related Posts Plugin allows you to display related posts with similar words in category, tags, title or by adding special meta key for posts, and display list of the titles of these posts by the widget or by the shortcode.

<a href="http://wordpress.org/extend/plugins/relevant/faq/" target="_blank">FAQ</a>
<a href="http://support.bestwebsoft.com" target="_blank">Support</a>

= Features =

* Display: It displayes related posts by category, meta key, tags or title.
* Title: You can add your own title shortcode list of similar posts. Just write your own frase in the input with title: "Heading the list of similar posts:". Also you can write HTML attribute to this phrase.
* Display: You can choose the number of posts to show in the input with label: "How many posts to display: ".
* Display: Enter the text to show if there will be no posts to show in the input with label: "Message if do not have related posts:".

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

== Screenshots ==

1. Related Posts Plugin Settings page.
2. RelaTed Posts Plugin in post using shortcode.
3. Related Posts Plugin Widget.

== Changelog ==

= V1.0.2 - 02.10.13 =
* NEW : We added BWS Menu.
* NEW : We added new screenshots.
* Update : Styles were updated.

= V1.0.1 - 04.09.13 =
* NEW : We added plugin description and a screenshot.
* Update : Changed style and description of meta box.

= V1.0.0 - 24.08.13 =
* Update : Improved design of code.

== Upgrade Notice ==

= V1.0.2 =
We added BWS Menu. We added new screenshots. Styles were updated.

= V1.0.1 =
We added plugin description and a screenshot. Changed style and description of meta box.

= V1.0.0 =
Improved design of code.
