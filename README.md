# BoardGameGeek Data - A WordPress Plugin
BoardGameGeek Data is a custom WordPress plugin that connects to the
BoardGameGeek API. It retrieves information about a user's game collection,
checks whether that data exists locally, and if not, inserts a new post
into the database and attaches its metadata and image.

This plugin is being developed as part of a weekly board game night app,
and is being used as an exploration in the WordPress REST API, which is
native in WordPress as of version 4.7.

## Requirements
The plugin saves data to a custom post type named `bgw_game`, and uses a
custom taxonomy named `bgw_game_status`. These post types and taxonomies
are not registered within this plugin itself, so if you wish to use this
plugin, you'll need to register those post types and taxonomies yourself.
