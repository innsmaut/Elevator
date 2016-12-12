<?php

class Elevator
{
    private static $instance;
    private $passengers = 0;
    private $volume = 3;
    private $speed = 1;
    public $location = 4;
    public $destination = 4;
    public $isMoving = false;
    private $canAcceptPassengers = true;
    private $sc;
    
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
    
    public function registerSystemController(SystemController $sc){
        $this->sc = $sc;
    }
    
    public function setDestination($destination){
        $this->destination = $destination;
    }

    public function behave(){
        $length = $this->location - $this->destination;
        if ($length == 0){
            $this->isMoving = false;
            $this->sc->passengersCanGo = true;
        } else {
            $this->move($length);
            $this->isMoving = true;
            $this->sc->passengersCanGo = false;
        }
    }
    public function move($length){
        if (abs($length) > $this->speed){
            ($length > 0)?
                $this->location -= $this->speed:
                $this->location += $this->speed;
        } else {
            $this->location = $this->destination;
        }
    }

    public function passengerIn(){
        if ($this->canAcceptPassengers && !$this->isMoving){
            $this->passengers++;
            if ($this->passengers === $this->volume){
                $this->canAcceptPassengers = false;
            }
            return true;
        } else return false;
    }

    public function passengerOut(){
        if ($this->passengers > 0 && !$this->isMoving){
            $this->passengers--;
            $this->canAcceptPassengers = true;
            return true;
        } else return false;
    }
    
    public function report(){
        return [
            'isMoving' => $this->isMoving,
            'stage' => $this->location,
            'destination' => $this->destination,
            'passengers' => $this->passengers
        ]; 
    }
    
    public function __destruct(){
        $_SESSION[__CLASS__] = static::$instance;
    }
}