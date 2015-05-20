<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->formsDir,
        $config->application->pluginsDir,
        $config->application->libraryDir,
    )
);

$loader->registerNamespaces(
    array(
        'SDT\Tags'           =>  $config->application->helperDir,
        'SDT\Elements'       =>  $config->application->elementDir
    )
);

$loader->register();
