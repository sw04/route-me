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

    protected $result = '';

    private $_defineClass = '';
    private $_defineMethod = '';
    private $_defineParams = [];

    private $_namespace = '\\';

    public function __construct()
    {
    }

    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }

    public function setController($class)
    {
        $this->_defineClass = $class;
        return $this;
    }

    public function setMethod($method)
    {
        $this->_defineMethod = $method;
        return $this;
    }

    public function setParams(array $params)
    {
        $this->_defineParams = $params;
        return $this;
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

                        $this->_checkDefines(
                            $route['defineClass'],
                            $route['defineMethod'],
                            $route['defineParams']
                        );
                        //EXECUTE
                        echo $this->class;
                        $result = $this->_execute(
                            $this->class,
                            $this->method,
                            $this->params
                        );

                        //actions after
                        $this->_execAction('after', $this->actions);
                        $this->result = $result;
                        return $result;
                    }
                }
            }
        }
        throw new RouterException('Route not found', 404);
    }

    public function clear()
    {
        $this->prefix = '';
        $this->class = '';
        $this->method = '';
        $this->params = [];
        $this->actions = [];
        $this->_defineClass = '';
        $this->_defineMethod = '';
        $this->_defineParams = [];
    }

    public function getResult()
    {
        return $this->result;
    }

    private function _checkDefines($class, $method, $params)
    {
        if ($this->class == '') {
            $this->class = $class;
        }
        if ($this->method == '') {
            $this->method = $method;
        }
        if (!is_array($this->params) or empty($this->params)) {
            $this->params = $params;
        }
    }

    private function _setClass($uri)
    {
        $result = [];
        foreach ($uri as $item) {
            if (!preg_match('/{.*}/', $item)) {
                array_push($result, $item);
            }
        }
        if (count($result) >= 1) {
            return implode('\\', $result);
        }
        return '';
    }

    private function _setMethod($uri)
    {
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

    private function _setParams($route, $uri)
    {
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
        return new Http();
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
                'actions' => $this->actions,
                'defineClass' => $this->_defineClass,
                'defineMethod' => $this->_defineMethod,
                'defineParams' => $this->_defineParams,
            ]
        );
    }

    private function _matchCheck($route, $real)
    {
        if (count($route) == 0 and count($real) > 0) {
            return false;
        }
        $matched = [];
        foreach ($route as $index => $item) {
            $matched[$index] = 0;
            if (array_key_exists($index, $real)) {
                if ($item == $real[$index]) {
                    $matched[$index] = 1;
                }
                $isValue = preg_match('/{.*}/', $item, $regular);
                if ($isValue) {
                    $isOptionalValue = preg_match('/!{.*}/', $item, $regular);
                    if ($isOptionalValue) {
                        $real[$index] = '!{' . $real[$index];
                    } else {
                        $real[$index] = '{' . $real[$index];
                    }
                    $real[$index] .= '}';

                    $is_matched = preg_match('/' . $item . '/', $real[$index], $result);
                    if ($is_matched) {
                        $matched[$index] = 1;
                    }
                }
            } else {
                if (preg_match('/!{.*}/', $item, $regular)) {
                    $matched[$index] = 1;
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

    private function _execute($class_name, $method, $params)
    {
        $class_name = $this->_namespace . $class_name;
        if (!class_exists($class_name)) {
            throw new RouterException('Controller not found', 404);
        }

        $Obj = new $class_name;
        if (!method_exists($Obj, $method)) {
            throw new RouterException('Method not found', 404);
        }

        $reflection = new \ReflectionMethod(
            $class_name,
            $method
        );

        $param_count = $reflection->getNumberOfRequiredParameters();

        if ($param_count > count($params)) {
            throw new RouterException('Required arguments not isset', 500);
        }

        return $result = call_user_func_array(
            [$Obj, $method],
            $params
        );
    }
}
