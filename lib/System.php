<?php

namespace lib;

class System extends Router{
    private $url;
    private $exploder;
    private $area;
    private $controller;
    private $action;
    private $params;
    private $runController;

    public function __construct(){
        $this->setUrl();
        $this->setExploder();
        $this->setArea();
        $this->setController();
        $this->setAction();
        $this->setParams();

    }

    private function setUrl(){
        $this->url = isset($_GET['url']) ? $_GET['url'] : 'home/index';
    }

    private function setExploder(){
        $this->exploder = explode('/', $this->url);
    }

    private function setArea(){
        foreach ($this->routers as $i => $v){
            if ($this->onRaiz && $this->exploder[0] == $i){
                $this->area = $v;
                $this->onRaiz = false;
            }
        }
        $this->area = empty($this->area) ? $this->routerOnRaiz :  $this->area;

        if (!defined('APP_AREA')){
            define('APP_AREA', $this->area);
        }
    }

    public function getArea(){
        return $this->area;
    }

    private function setController(){
        $this->controller = $this->onRaiz ? $this->exploder[0] : (empty($this->exploder[1])) ||
                    is_null($this->exploder[1] || !isset($this->exploder[1]) ? 'home' : $this->exploder[1]);
    }

    public function getController(){
        return $this->controller;
    }

    private function setAction(){
        $this->action = $this->onRaiz ?
            (!isset($this->exploder[1]) || is_null($this->exploder[1]) || empty($this->exploder[1]) ? 'index' : $this->exploder[1]) :
            (!isset($this->exploder[2]) || is_null($this->exploder[2]) || empty($this->exploder[2]) ? 'index' : $this->exploder[2]);
    }

    public function getAction(){
        return $this->action;
    }

    private function setParams(){
        if ($this->onRaiz){
            unset($this->exploder[0], $this->exploder[1]);
        } else {
            unset($this->exploder[0], $this->exploder[1], $this->exploder[2]);
        }

        if (end($this->exploder) == null){
            array_pop($this->exploder);
        }

        if (empty($this->exploder)){
            $this->params = array();
        } else {
            foreach($this->exploder as $val){
                $params[] = $val;
            }
        }
    }

    public function getParams($indice){
        return isset($this->params[$indice]) ? $this->params[$indice] : null;
    }

    private function validarController(){
        if (!(class_exists($this->runController))){
            header("HTTP/1.0 404 Not Found");
            define('ERROR','Não foi localizado o Controller: ' . $this->controller);
            include("content/{$this->area}/shared/404_error.phtml");
            exit();
            // Feito na aula 1 porém excluido na aula 2
            // die('Controller não existe' . $this->runController);
        }
    }

    private function validarAction(){
        if (!(method_exists($this->runController, $this->action))){
            header("HTTP/1.0 404 Not Found");
            define('ERROR','Não foi localizado o Action: ' . $this->action);
            include("content/{$this->area}/shared/404_error.phtml");
            exit();
            // Feito na aula 1 porém excluido na aula 2
            // die('Action não existe' . $this->action);
        }
    }

    public function Run(){
        $this->runController = 'controller\\' . $this->area . '\\' . $this->controller . 'Controller';

        $this->validarController();

        $this->runController = new $this->runController();

        $this->validarAction();

        $act = $this->action;

        $this->runController->act();
    }
}