<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->libraryDir,
        $config->application->modelsDir,
    ]
)->register();//注册加载的地方，那个目录要加载就要写在里面
