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


Creating a new page (view)
==========================

New pages are created within the `views` folder. Use pagename.html as filename.

Functions
=========

Render the Example module.

    <?php module('Example') ?>

Render "second" template from the Example module.

    <?php module('Example', 'second') ?>

Render the Example module with skin "blue".

    <?php module('Example', null, 'blue') ?>
 
Render the Example module with additional attributes.

    <?php module('Example', null, null, array('data-id' => 1)) ?>

Render a partial (e.g. for head or foot). Partials are placed in '''views/partials/''' with .html (e.g. foot.html).

    <?php partial('foot') ?>
    
Minification
============

You can get the minified versions of your CSS/JS by adding the URL Parameter `min`.

    http://localhost/app.css?min
    http://localhost/app.js?min
