=== Plugin Name ===
Contributors: versusbassz
Tags: custom, meta, field, display, metabox, show
Requires at least: 5.7
Tested up to: 5.9
Stable tag: 0.3.0-alpha
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Displays properties and custom fields of WordPress entities (posts, users, terms, comments) for debugging/development purposes.

== Description ==

The plugin displays properties and custom fields of WordPress entities (posts, users, terms, comments) for debugging/development purposes.

= Features =
* Supported entities: posts (including custom post types), users, terms, comments
* Displayed data: properties (e.g. `ID`, `guid`, `post_type`) and custom fields (e.g. `_edit_lock`, `_thumbnail_id` )
* Sorting by different conditions (ASC/DESC)
* Searching/filtering (with highlighting of found result)
* Pretty viewing of serialized data
* Auto-updating on Gutenberg's "publish/update" actions
* Manual updating without refreshing a whole page

= How it works =
The plugin displays the metabox on "edit" pages of supported WordPress entities in the WP Admin Panel.
The required role to access the info is `Administrator` for "Single site" mode and `Super Admin` for "Multisite".

= Links =
<a href="https://github.com/versusbassz/entity-viewer/" target="_blank">Github repo</a>,
<a href="https://github.com/versusbassz/entity-viewer/issues/" target="_blank">Github issues</a>

== Changelog ==

TODO
