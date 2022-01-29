# Entity viewer

It's a WordPress plugin that displays properties and custom fields of WordPress entities (posts, users, terms, comments) for debugging/development purposes.

## Features
- Supported entities: posts (including custom post types), users, terms, comments
- Displayed data: properties (e.g. `ID`, `guid`, `post_type`) and custom fields (e.g. `_edit_lock`, `_thumbnail_id` )
- Sorting by different conditions (ASC/DESC)
- Searching/filtering (with highlighting of found result)
- Pretty viewing of serialized data
- Auto-updating on Gutenberg's "publish/update" actions
- Manual updating without refreshing a whole page

## How it works
The plugin displays the metabox on "edit" pages of supported WordPress entities in the WP Admin Panel.  
The required role to access the info is `Administrator` for "Single site" mode and `Super Admin` for "Multisite".

## Links
- Wordpress.org page: https://wordpress.org/plugins/entity-viewer/
- [Changelog](https://github.com/versusbassz/entity-viewer/blob/main/CHANGELOG.md)
- [Roadmap](https://github.com/versusbassz/entity-viewer/milestones?direction=asc&sort=title&state=open)

## Versioning and stability
The project follows https://semver.org/

## License
The license of the project is GPL v2 (or later)
