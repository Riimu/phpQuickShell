<?php

require 'vendor/autoload.php';

$loader = new \Riimu\Kit\ClassLoader\ClassLoader();
$loader->addPrefixPath(__DIR__ . DIRECTORY_SEPARATOR . 'src', 'Riimu\QuickShell');
$loader->register();

$shell = new \Riimu\QuickShell\Shell();
$shell->run();

?>