Terrific Micro Framework
========================

Powerful template for simple & complex frontend projects with a tiny footprint.

Features
========
* CSS/JS concatenation
* CSS/JS minification support
* LESS support (optional)
* SASS support (optional)
* Caching (LESS/SASS) for optimal performance

Includes
========

* TerrificJS
* JQuery (optional, you can also use zepto)
* YUI Reset (optional, you can use your own as well)

Creating modules
================

Modules are created with the following structure in the `modules` folder.

    /Example
    /Example/example.html
    /Example/css/example.css
    /Example/js/example.js
    
Skins (CSS or JS) are created using the following conventions.

    /Example/css/example.skinname.css
    /Example/js/example.skinname.js

Creating a new page
===================

Create a new `*.html` file in the `views` folder.

    /views/page.html
    
Your new page can then be called by the following URL

    http://localhost/page
    
Render Modules
==============

Within you pages, you can render your modules with one of the following commands.

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

Render a partial (HTML snippets). Partials are placed in `views/partials/` as `*.html` files (e.g. foot.html).

    <?php partial('foot') ?>
    
Minification
============

You can get the minified versions of your CSS/JS by adding the URL Parameter `min`.

    http://localhost/app.css?min
    http://localhost/app.js?min
