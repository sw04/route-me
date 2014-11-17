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

class Singleton {
    /**
     * Router exemplar
     */
    protected static $_router;

    public function __construct(){}

    public function __clone(){}

    public static function getInstance() {
        if (null === self::$_router) {
            self::$_router = new Router();
        }

        return self::$_router;
    }
} 