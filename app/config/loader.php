<?php

$loader = new \Phalcon\Loader();

/**
 * 注册文件
 */
$loader->registerFiles(
    [
        $config->application->bootstrapDir . 'helpers.php',
    ]
);

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->libraryDir,
        $config->application->modelsDir,
    ]
);//注册加载的地方，那个目录要加载就要写在里面

// Register autoloader
$loader->register();