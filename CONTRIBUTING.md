# Contributing to Board Game Collector
I am open to ideas and suggestions for ways to make Board Game Collector 
better! Please use the [issue tracker](https://github.com/jmichaelward/board-game-collector/issues)
to report bugs and/or submit ideas for new features or enhancements you
would like to see.

For the time being, please note that this is very much a personal project, 
and that I'm not open to code contributions at this time. I primarily use
this repository as an exercise in feature development, and a way for me
to explore different build scripts and coding approaches while I create 
something that will be useful for me.

That said, there will be a point in the future where I'd certainly be 
open to collaborating more broadly on the features of this plugin. If 
there's a feature you feel strongly should be included in the plugin,
please reach out directly by opening an issue and/or dropping me a line
at [jeremy@jmichaelward.com](mailto:jeremy@jmichaelward.com), and I'd be
happy to discuss how to make it happen.

## Forks
With the above said, please feel free to fork this project! This plugin
is completely open source. Generally, I work on it during down time in my
life, so it's somewhat slow-to-move. I'm absolutely all for others 
leveraging my existing work to create something that is useful to them.

## Development setup
To work on this plugin, you'll need the following utilities:
- [Composer](https://getcomposer.org)
- [Yarn](https://yarnpkg.com)

You can setup the plugin in one of two ways - either by requiring it
directly from Composer, or cloning down the repo and then making sure
to install the development dependencies yourself.

At the time of this writing (3/28/2020), the `develop` branch should
be stable enough to run on local environments. You should be able to 
run the following from your project to get the latest development branch:

`composer require jmichaelward/board-game-collector:dev-develop@dev`

If run from the root of your WordPress project, this should correctly install
the plugin in your `plugins` directory, and from there, you should be able
to run `yarn install` and `yarn run build` to generate the remaining plugin 
assets. (Note: running yarn at the time of this writing is not completely necessary,
but there are features on the roadmap that will require this step before long).

Alternately, you can `cd` into your plugin directory, then run:

```
git clone https://github.com/jmichaelward/board-game-collector.git
cd board-game-colllector
composer install
```

This will achieve the same effect as above, except the package dependencies
will be installed inside of your plugin instead of the root of the project.
