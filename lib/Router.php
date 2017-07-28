<?php

namespace lib;

class Router{

    protected $routers = array(
        'site' => 'site',
        'admin' => 'admin'
    );

    private $urlBase = APP_ROOT;

    protected $routerOnRaiz = 'site';

    protected $onRaiz = true;
}