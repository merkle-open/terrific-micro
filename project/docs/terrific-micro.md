# Readme - Terrific Micro

<img align="right" src="https://raw.githubusercontent.com/namics/terrific-micro/master/assets/img/icon/terrific-micro.png">

Terrific Micro is a PHP application for simple and complex frontend development with a tiny footprint.  
It provides a proven but flexible structure to develop your frontend code, even in a large team.  
Keep track of your code with a modularized frontend. This app and the suggested [`terrific` concept](http://terrifically.org) could help.  
Terrific Micro is simple, fast and flexible. Use this app for all your frontend work, but it's not intended to use in production environment.

## Table of contents

* [Quick Start](#quick-start)
* [Daily Work - Components & Pages](#daily-work---creating-components--pages)
* [Assets](#assets)
* [Conventions](#conventions)
* [Security](#security)
* [Commandline](#commandline)
* [Extending](#extending)
* [Contributing](#contributing)
* [Credits & License](#example-project-includes)

## Quick Start

You only need an Apache web server 2.2+ with PHP 5.4+ support.  
Enable `mod_rewrite` and optionally `mod_deflate` and add the directive `AllowOverride All` for your directory. (Apache 2.4 needs `mod_access_compat` enabled.)

1. Clone repo to a project folder in your web root:

        git clone https://github.com/namics/terrific-micro.git projectfolder
        rm -rf projectfolder/.git

2. Start working on your code and see the results:

        http://localhost/projectfolder/
        http://localhost/projectfolder/app.css
        http://localhost/projectfolder/app.js

You see, we don't need a virtual host.

### Features

* Simple project structure
* CSS/JS concatenation and minification
* LESS/SCSS support
* Caching (LESS/SCSS) for optimal performance
* GUI to create components

## Daily Work - Creating Components & Pages

### Creating Components

Components are created in the `components` folder. A component is an encapsulated block of markup with corresponding styles and scripts.  
For a better overview it is useful to define different types of components. It is recommended to make subfolders like `atoms`, `molecules` & `organisms`
A terrific module uses the following structure:

    /Example
    /Example/example.html
    /Example/css/example.css
    /Example/js/example.js

Terrific Skins (css or js) are created using the following conventions:

    /Example/css/skins/example-skinname.css
    /Example/js/skins/example-skinname.js

Create additional content templates directly in the component folder:

    /Example/example-variant.html

### Creating Components & Skins by GUI

But the easiest way to do so is using the included GUI.  
Try it out by requesting http://localhost/projectfolder/terrific/ (don't forget the trailing slash)

#### Components Configuration

You can use more than one type of components. Components are configured in `config.json` (`micro/components`).

    "component-type": {
        "template": "project/templates/module", // template folder to copy by creating a component
        "path": "components/modules",           // path for the newly created component
        "component_prefix": "mod",              // class prefix of component
        "skin_prefix": "skin"                   // class prefix of skin (optional)
    }

The GUI component creator replaces `_component` and `_skin` in file names with the appropriate names.  
For file contents there are a bunch of placeholders available: 

    {{component}} {{component-css}} {{component-js}} {{component-file}} {{component-id}} {{component-prefix}}
    {{skin}} {{skin-css}} {{skin-js}} {{skin-file}} {{skin-id}} {{skin-prefix}}
    {{user}} {{email}}

### Creating pages

Create a new `*.html` file in the `views` folder. You can make as many subfolders as you want.

    /views/index.html
    /views/content.html
    /views/content/variant.html

Your new page can then be called by the according URL (with or without an extension). Subfolders are represented with a dash.

    http://localhost/projectfolder/index
    http://localhost/projectfolder/content
    http://localhost/projectfolder/content-variant

### Render Components

Pages are meant to be compositions of your components. Therefore you can render components in pages. The component helper gives you two options:  
You can write all markup in your template (= basic mode).  
Or you let the helper write the wrapping container with different inputs (= advanced mode). 

* for basic mode use one or two parameters
* for advanced mode use three or more parameters

Use the component's name as the first parameter. Be aware, the component name is case-sensitive and represents the folder name!

#### Basic Mode: Template with complete Markup

Render the Example component. (file: `example.html`)

    <?php component('Example'); ?>

Render the "variant" template from the Example component. (file: `example-variant.html`)

    <?php component('Example', 'variant'); ?>

#### Advanced Mode: Template without wrapper container

Render the Example component with skin "skinname". (file: `example-wrapme.html`)

    <?php component('Example', 'wrapme', 'skinname'); ?>

Render the Example component with additional attributes. (file: `example-wrapme.html`)

    <?php component('Example', 'wrapme', null, array('data-id' => 1)); ?> 

Render the Example component with different skins and additional attributes. (file: `example-wrapme.html`)

    <?php component('Example', 'wrapme', array('skinname','skinname2'), array('data-id' => 1, 'data-connectors' => 'cart', 'contenteditable' => 'true')); ?> (file: `example-wrapme.html`)

Render the Example component without special markup. (file: `example-wrapme.html`)

    <?php component('Example', 'wrapme', true); ?>

### Render Partials

Render a partial (HTML snippet). Partials are placed in `views/_partials/` as `*.html` files (e.g. `head.html`).

    <?php partial('head'); ?>

You can apply variables as an array and use them in the partial

    <?php partial('head', array('title' => 'Page Title')) ?>
    <?php echo $data['title']; ?>

## Assets

Terrific Micro's main feature is asset concatenation for CSS and JavaScript files. If changed, the files will be updated on every request, therefore you'll always get the latest version.

### Assets Configuration

You can configure the include order of your assets by defining patterns in `config.json`.

    "assets": {
        "app.css": [
            "+assets/css/variables.less",
            "!assets/css/somefile.*",
            "assets/css/cssreset.css",
            "assets/css/*.*",
            "components/modules/*/css/*.*",
            "components/modules/*/css/skins/*.*"
        ],
        "app.js": [
            "!assets/js/somefile.js",
            "assets/js/jquery-1.12.2.min.js",
            "assets/js/terrific-2-1.0.js",
            "assets/js/*.js",
            "components/modules/*/js/*.js",
            "components/modules/*/js/skins/*.js"
        ]
    }

#### Pattern

The matching patterns follow the standard glob patterns.  
Glob patterns are similar to regular expression but simplified. They are used by several shells.  
You should always try to keep the patterns simple. Usually you only need the asterisk `*` which matches zero or more characters.

You can read more on standard glob patterns on [php.net](http://www.php.net/manual/en/function.glob.php) and [coburn.info](http://cowburn.info/2010/04/30/glob-patterns/).

#### Special Pattern Prefixes

* You can negate a pattern by starting with an exclamation mark `!`.
  `!` = exclude pattern
* Define all your dependencies for the compiling-process with the `+` prefix
  `+` = exclude file but prepend it to every compile call for files with the same file extension.

The order of these special patterns does not matter.

#### Examples

* `"!components/modules/Test*"    Exclude all modules starting with `Test`
* `"!**/*-test.*"`                Exclude all filenames ending with `-test`.
* `"+assets/css/mixins.less"`     Exclude `assets/css/mixins.less` but prepend to every compile call of every .less file

#### Note on less & scss @import

Terrific Micro does not use a watcher for asset concatenation. The resources are concatenated with each request.  
Due to less and scss caching-mechanisms in place, this process is quite fast. You should be warned about the usage of @import, though. As long as you configure them relatively to your `projectfolder`, they work quite well. But changes in imported files are not tracked by Terrific Micro and therefore changes do not invalidate the cache. This means you either have to clean manually by changing the main file or you can disable caching by setting `$nocache = true;` in `project/index.project.php`.

#### Other Asset Files

You can configure as many different assets as you wish.

    "brand.css": [
        "assets/css/reset.css",
        ...

### Asset Minification

Minified versions of the CSS and/or JS files can be requested by adding the URL parameter `min`.

    http://localhost/projectfolder/app.css?min
    http://localhost/projectfolder/app.js?min

### JavaScript Debugging

The assets can be loaded individually by adding the URL parameter `debug`. This is pretty useful for things like remote debugging in PhpStorm.

    http://localhost/projectfolder/app.js?debug

## Conventions

### Resource linking

To stay portable you should favour the use of relative paths. Link to resources relatively to the `projectfolder` **without** a leading slash.

    <link rel="stylesheet" href="app.css" type="text/css" />
    <link rel="shortcut icon" href="assets/img/icon/favicon.ico" type="image/x-icon" />
    <script src="app.js"></script>
    <img src="components/modules/Example/img/example.png" alt="" />
    background: url(assets/img/bg/texture.png) scroll 0 0 no-repeat;
    <a href="content.html">Contentpage</a>

### Upper & lower case letters

Use all lowercase if possible. 
Exceptions:

* Component folders must match terrific classes, therefore they are case-sensitive.
* TerrificJS uses upper case for its namespace `Tc` and class names `Tc.Module.Example`

Use the component helper with the *exact* component name:

    <?php component('NavMain'); ?>

Note that camel case ComponentNames are represented in CSS with dashes.

    Navigation   -> Tc.Module.Navigation   -> mod-navigation
    NavMain      -> Tc.Module.NavMain      -> mod-nav-main
    AdminNavMain -> Tc.Module.AdminNavMain -> mod-admin-nav-main

### Indentation

Terrific Micro uses tabs for indentation and spaces for alignment.

## Commandline

You can access Terrific Micro via your commandline and your php binary of choice.

    php app/bin/micro

You can add an additional argument to get the output for either

* a specific view
* a specific CSS file set in config.json
* a specific JS file set in config.json

Here are some examples:

    // Generate index view
    php app/bin/micro view:index

    // Generate content-variant view
    php app/bin/micro view:content-variant

    // Generate content view with additional GET parameters foo=bar&baz=foo
    php app/bin/micro view:content "foo=bar&baz=foo"

    // Generate app.css
    php app/bin/micro css:app

    // Generate minified app.css
    php app/bin/micro css:app min

    // Generate app.js
    php app/bin/micro js:app

    // Generate minified app.js
    php app/bin/micro js:app min

    // Generate debug app.js
    php app/bin/micro js:app debug

## Security

To enable the use of Terrific Micro in any project structure some access restricting rules were set.  
These rules were made with the Apache web server in mind by using `.htaccess` files. These files can easily be adjusted as necessary.

### Terrific GUI

All requests to `/terrific/` except from `localhost` will be blocked (`403 Forbidden`). 
To adjust this behaviour, see `app/terrific/public/.htaccess`.

### Directory Listing

The directory listing is turned off in `.htaccess`.

## Extending

Terrific Micro is very easy to extend. You may add your own functions or overwrite existing ones. See some examples in `index.project.php`.  
And there is a couple of helpful extensions on (GitHub)[http://namics.github.io/terrific-micro-extensions/]. For example a lorem ipsum text and markup generator, a png sprite generator or a feature rich grunt exporter.

## Contributing

* For Bugs & Features please use [github](https://github.com/namics/terrific-micro/issues)
* Feel free to fork and send PRs. That's the best way to discuss your ideas.

## Example Project Includes

* [jQuery 1.12.2](http://jquery.com/)
* [TerrificJS 2.1.0](https://github.com/brunschgi/terrificjs/tree/2.1.0)
* [YUI CSS Reset 3.18.1](http://yuilibrary.com/yui/docs/cssreset/)
* Favicon & Home-Icons from Terrific Micro (replace with your own)
* Component `Example` and some styles in assets/css (you don't need them)

## Used PHP Libraries

* [Less.php](http://lessphp.gpeasy.com)
* [scssphp](http://leafo.net/scssphp)
* [CssMin](https://github.com/natxet/CssMin)
* [JShrink](https://github.com/tedious/JShrink)

## Credits

Terrific Micro was initiated by [Roger Dudler](https://github.com/rogerdudler) and is developed and maintained by Namics AG

## License

Released under the [MIT license](LICENSE)
