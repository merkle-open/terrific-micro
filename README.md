# Readme - Terrific Micro

Terrific Micro is a powerful template for simple & complex frontend projects with a tiny footprint. 
It gives you a proven but still flexible structure to develop your frontend code.
To unleash the beast, it's useful to read more about the Terrific concept on [terrifically.org](http://terrifically.org) first.

## Table of contents

* [Quick Start](#quick-start)
* [Daily Work - Components & Pages](#daily-work---creating-components--pages)
* [Assets](#assets)
* [Conventions](#conventions)
* [Security](#security)
* [Credits](#example-project-includes)

## Quick Start

You just need an Apache with PHP 5.3+ support. 
Enable mod_rewrite and optionally mod_deflate and add the directive `AllowOverride All` for your directory.

1. Clone repo in a `project`-folder of your web root:

        git clone https://github.com/namics/terrific-micro.git project

2. Start working on your code and see the results:

        http://localhost/project/
        http://localhost/project/app.css
        http://localhost/project/app.js

### Features

* Simple project structure
* CSS/JS concatenation & minification
* LESS/SCSS support
* Caching (LESS/SCSS) for optimal performance
* GUI to create components

## Daily Work - Creating Components & Pages

### Creating Components

Components are created in the `components` folder. A component is an encapsulated block of markup with corresponding styles and scripts. 
A terrific module for example uses the following structure:

    /Example
    /Example/example.html
    /Example/css/example.css
    /Example/js/example.js

Terrific Skins (css or js) are created using the following conventions:

    /Example/css/skins/example-skinname.css
    /Example/js/skins/example-skinname.js

Create additional content templates directly in the component folder:

    /Example/example-second.html

### Creating Components & Skins by GUI

But the easiest way to do so is using the included GUI. 
Try it out by requesting http://localhost/project/terrific/ (don't forget the trailing slash)

#### Components Configuration

You can use more than one type of components. Components are configured in `config.json` (`micro/components`).

    "component-type": {
        "template": "project/templates/module"  // template folder to copy by creating a component
        "path": "components/modules",           // path for the newly created component
        "component_prefix": "mod",              // class prefix of component
        "skin_prefix": "skin",                  // class prefix of skin (optional)
    }

The GUI component creator replaces `_component` and `_skin` in file names with the appropriate names. 
For file contents there is a bunch of placeholders available: 

    {{component}} {{component-css}} {{component-js}} {{component-file}} {{component-id}} {{component-prefix}}
    {{skin}} {{skin-css}} {{skin-js}} {{skin-file}} {{skin-id}} {{skin-prefix}}
    {{user}} {{email}}

### Creating pages

Create a new `*.html` file in the `views` folder. You can use subfolders (one level).

    /views/index.html
    /views/content.html
    /views/content/variant.html

Your new page can then be called by the according URL (with or without an extension). Subfolders are represented with a dash.

    http://localhost/project/index
    http://localhost/project/content
    http://localhost/project/content-variant

### Render Components

Pages are meant to be compositions of your components. Within your `pages`, you can render a component. The component helper gives you two options: 
You may write all the markup in your template (= basic mode). 
Or you let the helper write the wrapping container with different inputs (= advanced mode).

* for basic mode use one or two parameters
* from three parameters, the advanced mode is used

Use the exact upper and lowercase letters of your component folder for the first parameter.

#### Basic Mode: Template with complete Markup

Render the Example component. (file: `example.html`)

    <?php component('Example'); ?>

Render "second" template from the Example component. (file: `example-second.html`)

    <?php component('Example', 'second'); ?>

#### Advanced Mode: Template without wrapper container

Render the Example component with skin "skinname". (file: `example-wrapme.html`)

    <?php component('Example', 'wrapme', 'skinname'); ?>

Render the Example component with additional attributes. (file: `example-wrapme.html`)

    <?php component('Example', 'wrapme', null, array('data-id' => 1)); ?> (file: `example-wrapme.html`)

Render the Example component with different skins and additional attributes.

    <?php component('Example', 'wrapme', array('skinname','skinname2'), array('data-id' => 1, 'connectors' => 'con1', 'contenteditable' => 'true')); ?> (file: `example-wrapme.html`)

Render the Example component without special markup. (file: `example-wrapme.html`)

    <?php component('Example', 'wrapme', true); ?>

### Render Partials

Render a partial (HTML snippet). Partials are placed in `views/_partials/` as `*.html` files (e.g. head.html).

    <?php partial('head'); ?>

You can send variables as an array and use them in the partial

    <?php partial('head', array('title' => 'Page Title')) ?>
    <?php echo $data['title']; ?>

## Assets

The main functionality of Terrific Micro ist the assets concatenator for CSS & JavaScript files. 
These files will be updated on each request, so you always get the newest version.

### Assets Configuration

You're able to configure the include order of your assets for concatenation by defining patterns in `config.json`.

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
            "assets/js/jquery-1.11.1.min.js",
            "assets/js/terrific-2-1.0.js",
            "assets/js/*.js",
            "components/modules/*/js/*.js",
            "components/modules/*/js/skins/*.js"
        ]
    }

#### Pattern

The File matching patterns follows the rules of standard glob patterns. 
Glob patterns are like simplified regular expressions that shells use. 
Keep the patterns simple. Usually we only need the asterisk `*` which matches zero or more characters. 
More on standard glob patterns: [php.net](http://www.php.net/manual/en/function.glob.php) and [coburn.info](http://cowburn.info/2010/04/30/glob-patterns/)

#### Special Pattern Prefixes

* You can negate a pattern by starting with an exclamation point `!`.
  `!` = exclude pattern
* Define all your dependencies for the compiling-process with the prefix `+`
  `+` = exclude file but prepend to every compile call of same file extension.

The order of these special patterns does not matter.

#### Examples

* `"!components/modules/Test*"    Exclude all modules starting with `Test`
* `"!**/*-test.*"`                Exclude all filenames ending with `-test`.
* `"+assets/css/mixins.less"`     Exclude `assets/css/mixins.less` but prepend to every compile call of every .less file

#### Note on less & scss @import

Terrific Micro does not use a watcher for joining assets. The resources are combined with each request. 
Due to the less & scss caching-mechanism this is very fast. But be warned if you want to use less or scss @import.  
They work as long as you configure them relative to the file but changes in the imported files are not tracked. 
So you have to clean the cache manually either by changing the main file who imports or by disabling the cache completely with the boolean `$nocache = true;` in `project/index.project.php`

#### Other Asset Files

Any number of assets can be configured.

    "brand.css": [
        "assets/css/reset.css",
        ...

### Asset Minification

You can get the minified versions of your CSS/JS by adding the URL parameter `min`.

    http://localhost/project/app.css?min
    http://localhost/project/app.js?min

### JavaScript Debugging

For JavaScript debugging, the assets can be loaded individually by adding the URL parameter `debug` (e.g. for remote debugging in PhpStorm).

    http://localhost/project/app.js?debug

## Conventions

### Resource linking

Use relative pathes everywhere to stay best portable. Link all of the resources relative to the `project`-folder without a leading slash e.g

    <link rel="stylesheet" href="app.css" type="text/css" />
    <link rel="shortcut icon" href="assets/img/icon/favicon.ico" type="image/x-icon" />
    <script src="app.js"></script>
    <img src="components/modules/Example/img/example.png" alt="" />
    background: url(assets/img/bg/texture.png) scroll 0 0 no-repeat;
    <a href="content.html">Contentpage</a>

### Upper & lower case letters

Use all lowercase if possible. 
Exeptions:

* Components Folders are our terrific classes, so they are case sensitive.
* TerrificJS uses upper case for its namespace `Tc` & Class names `Tc.Module.Example`

Use the component helper with the exact name of the folder:

    <?php component('NavMain'); ?>

Note that mixed ComponentNames are represented in CSS with dashes.

    Navigation   -> Tc.Module.Navigation   -> mod-navigation
    NavMain      -> Tc.Module.NavMain      -> mod-nav-main
    AdminNavMain -> Tc.Module.AdminNavMain -> mod-admin-nav-main

### Indentation

Terrific Micro uses tabs for indentation and spaces for alignment.

## Security

So that the directory can be used in any project structure, some precautions were taken. 
Most arrangements are made ​​for the Apache web server using `.htaccess`, but can be easily adjusted as necessary.

### Terrific GUI

All calls to `/terrific/` are only for localhost. On a server, nothing will be shown. 
See `app/terrific/public/.htaccess`

### Directory Listing

The directory listing is turned off by `.htaccess`

## Example Project Includes

* [jQuery 1.11.1](http://jquery.com/)
* [TerrificJS 2.1.0](http://terrifically.org/api/)
* [YUI CSS Reset 3.17.2](http://yuilibrary.com/yui/docs/cssreset/)
* Favicon & Home-Icons from Terrific Micro (replace with your own )
* Component `Example` and some styles in assets/css (you don't need them)

## Used PHP Libraries

* [Less.php](http://lessphp.gpeasy.com)
* [scssphp](http://leafo.net/scssphp)
* [CssMin](https://code.google.com/p/cssmin/)
* [JShrink](https://github.com/tedious/JShrink)

## Contributing

* For Bugs & Features please use [github](https://github.com/namics/terrific-micro/issues)
* Feel free to fork or branch and push your code to the repo. The best you discuss your ideas.

## Credits

Terrific Micro was initiated by [Roger Dudler](https://github.com/rogerdudler) and is now maintained by Namics AG

## License

Released under the [MIT license](LICENSE)
