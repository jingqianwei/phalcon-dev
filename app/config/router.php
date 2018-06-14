<?php

$router = $di->getRouter();

//定义命名路由
$router->add(
    "/wx/miniapp",//路由名称
    array(
        "controller" => "wxminiapp", //控制器
        "action"     => "index", //动作
    )
);

//定义命名路由
$router->add(
    "/mongodb/find",//路由名称
    array(
        "controller" => "mongodb", //控制器
        "action"     => "find", //动作
    )
);

//通配符定义的路由
$router->add(
    '/:module/:controller/:action/:params',
    array(
        'module' => 1,
        'controller' => 2,
        'action' => 3,
        'params' => 4,
    )
);

$router->handle();


