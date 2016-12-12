<?php

class Singletone
{
    protected static $instance;

    private function __clone() { }
    private function __construct() { }
    private function __wakeup() { }

    final public static function getInstance() {
        if ( !isset( static::$instance ) ) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}