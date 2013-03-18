Terrific Micro
==============

Powerful template for simple & complex frontend-only projects with a tiny footprint. To unleash the beast, it's useful to read more about the Terrific concept on http://terrifically.org first.

Features
========
* CSS/JS concatenation & minification
* LESS/SASS support (optional)
* Caching (LESS/SASS) for optimal performance

Installation & Requirements
===========================

You just need an Apache with PHP 5+ support. That's fair, isn't it?

1. Unzip https://github.com/rogerdudler/terrific-micro/archive/master.zip in your web root (or subfolder).
2. Start working on your code and call `http://localhost/` to see the results.

Creating modules
================

Modules are created with the following structure in the `modules` folder.

    /Example
    /Example/example.html
    /Example/css/example.css
    /Example/js/example.js
    
Skins (CSS or JS) are created using the following conventions.

    /Example/css/example-skinname.css
    /Example/js/example-skinname.js

Additional content templates are created directly in the module folder.
    
    /Example/example-second.html

Creating pages
==============

Create a new `*.html` file in the `views` folder. Try to keep a flat structure.

    /views/page.html
    /views/content.html
    /views/content-variant.html
    
Your new page can then be called by the following URL (with or without trailing slash)

    http://localhost/page/
    
Render Modules
==============

Pages are meant to be compositions of your modules. Within your `pages`, you can render a `module` with one of the following commands.

Render the Example module.

    <?php module('Example') ?>

Render "second" template from the Example module.

    <?php module('Example', 'second') ?>

Render the Example module with skin "blue".

    <?php module('Example', null, 'blue') ?>
 
Render the Example module with additional attributes.

    <?php module('Example', null, null, array('data-id' => 1)) ?>
    
Render Partials
===============

Render a partial (HTML snippet). Partials are placed in `views/partials/` as `*.html` files (e.g. foot.html).

    <?php partial('foot') ?>
    
Minification
============

You can get the minified versions of your CSS/JS by adding the URL Parameter `min`. This is especially useful when deploying for production.

    http://localhost/app.css?min
    http://localhost/app.js?min
    
Configuration
=============

You're able to configure the include order for concatenation by defining the include patterns in `assets/assets.json`. You can remove `*.less`, `*.scss` or other entries if you don't need them.

    {
        "css": [
            "reset.css",
            "elements.css",
            "*.css",
            "*.less",
            "*.scss"
        ],
        "js": [
            "jquery.min.js",
            "terrific.min.js",
            "*.js"
        ]
    }
    
Optimization
============

If you don't need SASS or LESS support, you can just drop those folders from the `library` folder to save some space.

Includes
========

* TerrificJS (http://terrifically.org)
* JQuery (optional, you can also use zepto)
* YUI CSS Reset (optional)
