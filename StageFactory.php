<?php

class StageFactory
{
    private static $instance;
    private $id = 0;

    private function __construct(){
        $_SESSION[__CLASS__] = static::$instance;
    }

    public static function getInstance() {
        if (isset($_SESSION[__CLASS__])){
            static::$instance = $_SESSION[__CLASS__];
        }
        if ( !isset( static::$instance ) ) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function getStage(){
        $stage = new Stage($this->id);
        $this->id++;
        return $stage;
    }

    public function __destruct(){
        $_SESSION[__CLASS__] = static::$instance;
    }
}