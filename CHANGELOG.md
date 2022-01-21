# Changelog (Entity Viewer)

## [Unreleased]
- Critical - The plugin has been renamed: "Meta viewer" -> "Entity viewer"
- Critical, Requirements - Update required PHP version: 7.3+ -> 7.4+
- Critical, Requirements - Update minimal required WordPress core version: 5.6.4 -> 5.7.0
- New - Display properties of WP entities in the metabox
- New - Add tabs to metabox to switch visibility of properties and custom fields
- New - Support "Multisite" mode
- Enhancement - Add pretty displaying of "null" values
- Enhancement - Provide a hint for a user if a search query was found in a raw value but wasn't highlighted in a "pretty" value
- Enhancement - Disable text selection on unnecessary elements
- Enhancement - Add details to phrases in the admin notices about incompatible PHP/WP versions
- Misc - Prepare the plugin for release on wordpress.org

- Fix - Handle the case of incorrect JSON (syntax error) on AJAX response
- Fix - Fix non-valid layout of the metabox
- Fix - Fix unnecessary "subscribe" actions in the logic of compatibility with Gutenberg

- Dev - Rework the initialization logic of the plugin
- Dev, i18n - Implement i18n support
- New, Dev - Add "vsm/plugin_enabled", "vsm/is_plugin_allowed", "vsm/is_i18n_enabled" filters
- Dev - Add an environment for development of the plugin
- Dev - Add e2e tests

## 0.2.1

Release date: 2021-06-27  
[Release page](https://github.com/versusbassz/entity-viewer/releases/tag/0.2.1) |
[Milestone issues](https://github.com/versusbassz/wp-meta-viewer/milestone/7?closed=1)

- Fix - Highlight search results in id,name colums also
- Fix - Enable Gutenberg compatibility logic only on pages where it exists (editing of post, pages, etc)

## 0.2.0

Release date: 2021-06-27  
[Release page](https://github.com/versusbassz/entity-viewer/releases/tag/0.2.0) |
[Milestone issues](https://github.com/versusbassz/wp-meta-viewer/milestone/2?closed=1)

- New - Add "Refresh data" button (updating data of the metabox dynamically)
- Enhancement - The interface of the plugin has been rewritten on ReactJS
- Enhancement - Update metabox content on Gutenberg "Publish/Update" actions
- Enhancement - Highlight search results

- Security - Update `glob-parent` npm dependency
- Dev - Add browserlist
- Dev - Migrate from `node-sass` to `sass` (Dart implementation)

## 0.1.0
Just to release the old state (2018, summer) of the plugin with some enchantments

Release date: 2021-06-01  
[Release page](https://github.com/versusbassz/entity-viewer/releases/tag/0.1.0) |
[Milestone issues](https://github.com/versusbassz/wp-meta-viewer/milestone/1?closed=1)

- New - Dynamic search (with hidding of not relevant fields)
- New - Add an admin notice for installations with old PHP versions
- New - Add minimal supported WordPress core version + admin notice

- Maintenance - Update required PHP version: 5.4+ -> 7.3+
- Fix - Replace `<pre>` tag for values with a styled div (because of incorrect formatting of long values)

- Dev - Build CSS/JS code with webpack
- Dev - Add README.md
