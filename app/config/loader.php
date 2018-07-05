<?php

$loader = new \Phalcon\Loader();

/**
 * 注册全局文件
 */
$loader->registerFiles(
    [
        $config->application->bootstrapDir . 'helpers.php',
    ]
);

/**
 * We're a registering a set of directories taken from the configuration file
 * 自定加载类，要是php文件得单独加载
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->validationDir,
        $config->application->libraryDir,
        $config->application->modelsDir,
    ]
);//注册加载的地方，那个目录要加载就要写在里面

/**
 * 注册命名空间
 */
$loader->registerNamespaces(
    [
        'SbDaRepository' =>  $config->application->repositoryDir,
        'SbDaValidation' =>  $config->application->validationDir,
    ]
);

// Register autoloader
$loader->register();