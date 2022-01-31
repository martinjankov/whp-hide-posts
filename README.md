=== WordPress Hide Posts ===
Contributors: martin7ba
Tags: hide posts, hide, show, visibility, filter, woocommerce, hide products, rss feed, rest api
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows you to hide any posts on the home page, category page, search page, tags page, authors page, RSS Feed, REST API, Post Navigation and Native Recent Posts Widget.

Also you can hide Woocommerce products from Shop page as well as from Product category pages.

It has option to hide custom post types on their archive or any other page.

To enable it for Woocommerce products or any other Custom Post type go to Settings -> Hide Posts and select the post type.

== Installation ==

1. Upload the `whp-hide-posts` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enable Hide post functionality for additional post types by going to Settings -> Hide Posts

== Changelog ==

= 1.0.0 =
_Release Date - 07 February 2022_

- Code base optimized and refactored
- Added option to hide CPT on their own archive page
- Added option to hide any post type on archive page other than category, tag. Ex: Custom Taxonomy archive page.
- Added option to hide posts on the default WordPress Recent Posts Widget
- Added option to hide posts in REST API calls

= 0.5.3 =
_Release Date - 15 August 2020_

- Fixed jQuery Migrate Helper warning showing in console

= 0.5.2 =
_Release Date - 12 August 2020_

- Added option to select all hide options in post metabox
- Add new column in posts list table that shows on which pages the post is hidden
- Added option in Settigns -> Hide Posts to disable the showing of the said column

= 0.5.1 =
_Release Date - 19 May 2020_

- Added option to hide posts default WordPress post navigation
- Fix for hiding menu items bug

= 0.5.0 =
_Release Date - 17 April 2020_

- Removed option to hide post from REST API added in version 0.4.3 due to conflict with Guttenberg save / update post.
  The conflict happens because Guttenberg is using the REST API to save post and load additional data the hide post on REST API was causing conficts with the data.

= 0.4.4 =
_Release Date - 14 April 2020_

- Added option to hide post on date archive page

= 0.4.3 =
_Release Date - 06 April 2020_

- Added option to hide posts from REST API all posts query: /wp-json/wp/v2/posts/
  Note: Single post entry in REST API remains available /wp-json/wp/v2/posts/[post_id]

= 0.4.2 =
_Release Date - 13 February 2020_

- Bug fix

= 0.4.1 =
_Release Date - 13 February 2020_

- Workaround added for issue showing warnings when is_front_page() function is called in pre_get_posts filter. This is related to wordpress core and can be tracked at [#27015](https://core.trac.wordpress.org/ticket/27015) and [#21790](https://core.trac.wordpress.org/ticket/21790)

= 0.4.0 =
_Release Date - 21 December 2019_

- Added option to hide posts on the blog page as selected in Settings -> Reading (Posts Page)

= 0.3.2 =
_Release Date - 13 December 2019_

- Bug fix for checking if Woocommerce is active on mutlinetwork site

= 0.3.1 =
_Release Date - 07 December 2019_

- Added option to hide posts in RSS Feed
- Added options to hide Woocommece products on Store (Shop) page and on Product category pages

= 0.2.1 =
_Release Date - 13 November 2019_

- Compatibility checkup with Wordpress 5.3
- Added option to enable the Hide Post functionality for additional post types (Check Settings -> Hide Posts)
- Added uninstall.php file to clean up options and post meta added by the plugin

= 0.1.1 =
_Release Date - 05 September 2019_

- Code and compatibility updates.
- Added translateable strings.

= 0.0.1 =
_Release Date - 11 October 2017_

- Public release of 'Wordpress Hide Posts'
