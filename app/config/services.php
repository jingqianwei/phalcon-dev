<?php

use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Events\Manager as EventsManager;
use MongoDB\Driver\Manager as MongoManager;
use Phalcon\Db\Profiler as DbProfiler;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Flash\Direct as Flash;
use Predis\Client as RedisClient;
use Phalcon\Mvc\View;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    if (!is_array($config->toArray()) || count($config->toArray()) == 0) {
        return false;
        //throw new \Exception("the database config is error");
    }

    $eventsManager = new EventsManager(); //实例化一个事件
    $profiler = new DbProfiler(); //分析底层sql性能，并记录日志
    $eventsManager->attach('db', function ($event, $connection) use ($profiler) {
        if($event->getType() == 'beforeQuery') {
            //在sql发送到数据库前启动分析
            $profiler->startProfile($connection->getSQLStatement());
        }

        if($event->getType() == 'afterQuery') {
            //在sql执行完毕后停止分析
            $profiler->stopProfile();
            //获取分析结果
            $profile = $profiler->getLastProfile();
            $sql = $profile->getSQLStatement();
            $executeTime = $profile->getTotalElapsedSeconds();
            $logDir = BASE_PATH . '/logs/query'; //日志目录
            mk_dirs($logDir);
            $logger = init_log_data($logDir . '/sql-' . date('Y-m-d') . '.log');
            $logger->begin(); //开启日志事务
            //记录日志
            $logger->info(
                var_export([
                    'sql' => $sql,
                    'time' => '执行花费时间为：' . $executeTime . 's',
                ], true)
            );
            $logger->commit(); //保存消息到文件中
        }
    });

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    /* 注册监听事件 */
    $connection->setEventsManager($eventsManager);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Set Redis Cache
 */
$di->setShared('redis', function () {
    $config = $this->getConfig();

    $redisConfig = $config->redis->toArray();

    try {
        $sentinels = ['tcp://'.$redisConfig['host'].':'.$redisConfig['port']];

        $options = [
            'parameters' => [
                'password' => $redisConfig['password'],
                'database' => $redisConfig['database'],
            ],
        ];

        $client = new RedisClient($sentinels, $options);
        return $client;
    } catch (Exception $ex) {
        return false;
    }
});

/**
 * Set mongodb database php7的mongodb扩展用法，如果是php5.*就用mongo扩展即可
 */
$di->setShared('mongodb', function () {
    $config = $this->getConfig();

    $mongodbConfig = $config->mongodb->toArray();

    try {
        $url = 'mongodb://'.$mongodbConfig['host'].':'.$mongodbConfig['port'];

        $manager = new MongoManager($url);

        return $manager;
    } catch (Exception $ex) {
        return false;
    }
});
