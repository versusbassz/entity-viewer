# Changelog (Entity Viewer)

## [Unreleased]
- Critical, Requirements - Update required PHP version: 7.3+ -> 7.4+

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
