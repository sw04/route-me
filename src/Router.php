<?php

/**
 * CORE
 *
 * PHP version 5.3
 *
 * @category CORE
 * @package  Base
 * @author   sw04 <ufaseo@gmail.com>
 * @license  http://seoplayer.ru PRIVATE
 * @link     http://seoplayer.ru
 */
namespace Router;

class Router
{
    /**
     * Dump of routes
     */
    protected $routes = [];

    /**
     * Prefix for routes
     */
    protected $prefix;

    /**
     * Actions array
     * sample element:
     *  [
     *      'type'     => '', <-- before or after
     *      'function' => '', <-- to call
     *      'params'   => ''  <-- for functions
     *  ]
     */
    protected $actions = [];

    protected $class = '';

    protected $method = '';

    protected $params = null;

    public function __construct()
    {
    }

    public function setPrefix($prefix)
    {
        $this->prefix = str_replace('/', '', $prefix);
        return $this;
    }

    public function setAction($type, $function, $params = null)
    {
        array_push(
            $this->actions,
            [
                'type' => $type,
                'function' => $function,
                'params' => $params
            ]
        );
        return $this;
    }

    public function any($urls)
    {
        $this->_saveToMatch('ANY', $urls);
        return $this;
    }

    public function get($urls)
    {
        $this->_saveToMatch('GET', $urls);
        return $this;
    }

    public function post($urls)
    {
        $this->_saveToMatch('POST', $urls);
        return $this;
    }


    public function match($uri)
    {
        $request = $this->_initRequest();
        $method = $request->method;

        foreach ($this->routes as $route) {
            if (in_array($route['method'], [$method, 'ANY'])) {
                //uri check
                $urlParsed = $route['urlParsed'];
                if ($route['prefix'] != '') {
                    array_unshift($urlParsed, $route['prefix']);
                }

                $uriParsed = $this->_explodeString($uri);
                $match = $this->_matchCheck($urlParsed, $uriParsed);
                if ($match) {
                    //actions
                    $before = $this->_execAction('before', $this->actions);

                    if ($before) {
                        //class
                        $this->class = $this->_setClass($uriParsed);
                        //method
                        $this->method = $this->_setMethod($urlParsed);
                        //params
                        $this->params = $this->_setParams($urlParsed, $uriParsed);

                        //EXECUTE
                        $result = $this->_execute(
                            $this->class,
                            $this->method,
                            $this->params
                        );

                        //actions after
                        $this->_execAction('after', $this->actions);
                        $this->_sendResponse($result);
                    }
                }
            }
        }
    }

    public function clear()
    {
        $this->prefix = '';
        $this->class = '';
        $this->method = '';
        $this->params = [];
        $this->actions = [];
    }

    private function _sendResponse($result) {
        if (!headers_sent()) {
            if (isset($result['error']) and $result['code'] != 200) {

            } else {
                if ($result != null) {
                    echo json_encode($result);
                }
            }
        }
    }

    private function _setClass($uri) {
        $result = [];
        foreach ($uri as $item) {
            if (!preg_match('/{.*}/', $item)) {
                array_push($result, $item);
            }
        }
        if (count($result) > 1) {
            array_pop($result);
            return implode('\\', $result);
        }
        return '';
    }

    private function _setMethod($uri) {
        $result = '';
        foreach ($uri as $item) {
            if (!preg_match('/{.*}/', $item)) {
                $result = $item;
            } else {
                return $result;
            }
        }
        return $result;
    }

    private function _setParams($route, $uri) {
        $result = [];
        foreach ($route as $index => $item) {
            if (array_key_exists($index, $uri)) {
                if (preg_match('/{.*}/', $item)) {
                    array_push($result, $uri[$index]);
                }
            }
        }
        return $result;
    }

    private function _initRequest()
    {
        return new \Http();
    }

    private function _execAction($type, $actions)
    {
        foreach ($actions as $item) {
            if ($type == $item['type']) {
                if (!call_user_func($item['function'], $item['params'])) {
                    return false;
                }
            }
        }
        return true;
    }

    private function _explodeString($str, $delimiter = '/')
    {
        $result = explode($delimiter, $str);
        return array_slice(array_filter($result), 0);
    }

    private function _saveToMatch($method, $url)
    {
        $urlParsed = $this->_explodeString($url);

        array_push(
            $this->routes,
            [
                'method' => $method,
                'prefix' => $this->prefix,
                'url' => $url,
                'urlParsed' => $urlParsed,
                'actions' => $this->actions
            ]
        );
        $this->clear();
    }

    private function _matchCheck($route, $real)
    {
        $matched = [];
        foreach ($route as $index => $item) {
            $matched[$index] = 0;
            if (array_key_exists($index, $real)) {
                if ($item == $real[$index]) {
                    $matched[$index] = 1;
                }
                $is_value = preg_match('/{.*}/', $item, $regular);
                if ($is_value) {
                    $real[$index] = '{' . $real[$index] . '}';
                    $is_matched = preg_match('/' . $item . '/', $real[$index], $result);
                    if ($is_matched) {
                        $matched[$index] = 1;
                    }
                } else {
                    $this->class .= '\\'.$item;
                }
            }
        }
        foreach ($matched as $item) {
            if ($item == 0) {
                return false;
            }
        }
        return true;
    }

    private function _execute($class_name, $method, $params) {

        if (!class_exists($class_name)) {
            return ['error' => 'controller not defined'];
        }

        $Obj = new $class_name;
        if (!method_exists($Obj, $method)) {
            return ['error' => 'method not defined'];
        }

        $reflection = new \ReflectionMethod(
            $class_name,
            $method
        );

        $param_count = $reflection->getNumberOfRequiredParameters();

        if ($param_count > count($params)) {
            return ['error' => 'not isset required arguments'];
        }

        return $result = call_user_func_array(
            [$Obj, $method],
            $params
        );
    }
}
