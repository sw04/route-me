<?php
/**
 * HttpRequest class
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

class Http {

    public $method;
    public $uri;

    public function __construct() {
        $this->_setMethod();
        $this->_setUri();
    }

    private function _setMethod() {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : NULL;
        if ($method === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])
            && preg_match('#^[A-Z]+\z#', $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])
        ) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }
        $this->method = $method;
    }

    private function _setUri() {
        //remove all after ? symbol
        $len = strpos($_SERVER['REQUEST_URI'], '?');
        if ($len == 0) {
            $len = strlen(($_SERVER['REQUEST_URI']));
        }
        $request = substr($_SERVER['REQUEST_URI'], 0, $len);
        //add slash in end
        if (substr($request, -1) != '/') {
            $request .= '/';
        }
        $this->uri = $request;
    }
}
