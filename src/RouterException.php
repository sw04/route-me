<?php
/**
 * Exception class
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


class RouterException extends \Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}