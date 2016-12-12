<?php

class PassengerFactory
{
    private static $instance;
    private $id = 0;

    private function __construct(){
        $_SESSION['PassengerFactory'] = static::$instance;
    }

    public static function getInstance() {
        if (isset($_SESSION['PassengerFactory'])){
            static::$instance = $_SESSION['PassengerFactory'];
        }
        if ( !isset( static::$instance ) ) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    
    public function getPassenger(){
        $passenger = new Passenger($this->id);
        $this->id++;
        return $passenger;
    }

    public function __destruct(){
        $_SESSION['PassengerFactory'] = static::$instance;
    }
}