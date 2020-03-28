# Board Game Collector
Board Game Collector is a custom WordPress plugin that connects to the
BoardGameGeek API. It retrieves information about a user's game collection,
creates or updates a related custom post type in the database, saves its
metadata, and attaches the box art as a featured image.

### Contributing
Please see [CONTRIBUTING.md](CONTRIBUTING.md) for information about contributing
to this plugin, including steps for setting up your development environment,
opening issues, etc.

### Usage
Once activated, the plugin registers a custom post type, taxonomy, and
settings page all under the "Games" tab of your admin that will be used
to track your game data. By visiting Games > BGG Settings, you can
enter your BoardGameGeek user name to begin syncing your collection
to WordPress.

Board Game Collector registers a WP-CLI command, which have been
made available to you after running `composer install`. Once you've
configured your settings page, running `wp bgc update` _should_ go and
fetch all of your games, their images, and their current status
(e.g., own,previously owned, wishlist, and so on) from BoardGameGeek.
At the time of this writing, what you want to do with that data after
that is up to you. That said, as of 4/12/19, I'm revisiting this repo
again with a refreshed sense of purpose, and am hoping to make it
better and more usable for myself, and hopefully for you, too! You can
keep an eye on the (issues)[https://github.com/jmichaelward/board-game-collector/issues]
page here on GitHub for a sense of upcoming features.
