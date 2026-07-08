<?php

$loader = require 'vendor/autoload.php';

// When the core lib is installed via a path repository (symlink), composer
// incorrectly maps its autoload-dev namespace to this project's directories.
// Remove the conflicting mapping so PHPUnit doesn't try to load core lib tests.
$loader->setPsr4('LauLamanApps\\ApplePassbook\\Tests\\', []);
