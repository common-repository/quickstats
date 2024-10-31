=== Quick Stats ===
Tags: statistics, logging
Contributors: jimqode
Requires at least: 2.0.2
Tested up to: 2.1
Stable tag: trunk

== Description ==

Quick Stats is a configurable and embarassingly easy to install statistics
plugin for wordpress that shows live statistics about referrer data, content
hit count and browser versions back to a configurable number of hits.
Individual hits are also shown with ip address and hostname. Visitors can be
tracked in near real-time using an RSS reader software.  

Quick Stats is not meant to be a full featured log analyzer. It is intended to show realtime statistics on a recent time window. It does all the analysis on demand and shows them on an easily accesible single page.

== What's new in 1.1 ==

* RSS tracker. You can now track your visitors in near real-time using an rss
  reader software.
  
* Annoying undeletable blank Filtered IPs bug is fixed (Thanks to Mitch Powell
  for pointing it)

== Installation ==

1. Upload `quickstats.php` and quickstats directory to your plugins folder, usually `wp-content/plugins/`
2. Activate the plugin on the plugin screen




== Frequently Asked Questions ==

= Cannot create QuickStats table. Database user does not have CREATE priviledge. =

You are getting this error because QuickStats needs to create a table on your
wordpress database but does not have needed privileges. Grant your wordpress
mysql user CREATE privilege, or create the table manually by running the query
below: 

CREATE TABLE `wp_jqstats` (
  `id` int(100) NOT NULL auto_increment,
  `url` varchar(150) NOT NULL default '',
  `ip` varchar(40) NOT NULL default '',
  `host` varchar(250) NOT NULL default '',
  `date` varchar(15) NOT NULL default '',
  `browser` varchar(200) NOT NULL default '',
  `time` varchar(20) NOT NULL default '',
  `referer` varchar(255) NOT NULL default '',
  `domain` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
);

= Where do I see the statistics? =

Administration Menu -> Manage -> Show Stats

= Where do I configure this? =

Administration Menu -> Options -> Quick Stats

= How does the RSS feature work? = 

Just click on the RSS icon on the options window to subscribe to tracker feed.
You must subscribe again if you generate a new Magic Number.

= What is the Magic Number? =

Magic Number is a basic security measure to assure only authorized people
subscribe to the tracker RSS feed. Magic Number is passed in the URL of the
tracker feed one must basically know this number to read the feed.

You can generate a new random Magic Number using the generate button in
options window.




== Screenshots ==

1. Quick Stats configuration screen
2. Quick Stats statistics screen
