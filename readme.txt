=== Adjacent Archive Links ===
Contributors: justincwatt
Donate link: http://justinsomnia.org/2012/11/adjacent-archive-links-for-wordpress/
Tags: archive, archives, date archives, template tag, navigation
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds two new template tags to output previous and next links on date archive pages.

== Description ==

After activating the plugin, you will have two new template tags at your disposal:

`<?php previous_archive_link( 'format', 'link' ); ?>`
`<?php next_archive_link( 'format', 'link' ); ?>`

They adapt their output depending on whether the date archive is a day, a month,
or a year, and they will only output a link for an adjacent time period when you
have published posts. Both tags take two string parameters, *format* and *link*.

The *format* parameter defines what comes before and after the link. `%link` 
will be replaced with whatever is declared in the link parameter below. `previous_archive_link` 
defaults to "`&laquo; %link`" and `next_archive_link` defaults to "`%link &raquo;`".

The *link* parameter defines the link text. Both tags default to "`%date`", the 
adjacent archive page's localized date.

== Installation ==

1. Extract the zip file, drop the contents in your wp-content/plugins/ directory, and then activate from the Plugins page.
1. Edit your theme file (e.g. archive.php) and add the template tags `<?php previous_archive_link(); ?>` and `<?php next_archive_link(); ?>`

== Frequently Asked Questions ==

= How do I make my date archives show all posts for that time period in chronological order, rather than just 10 at a time? =

Just add `query_posts($query_string . '&showposts=1000&order=asc');` to the archive.php template right under the header call.

== Screenshots ==

1. This is an example of how it looks on a date archive page customized to only show post titles in chronological order.

== Changelog ==
= 1.0 =
* Initial version

== Upgrade Notice ==
= 1.0 =
Initial version
