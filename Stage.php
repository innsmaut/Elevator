<?php

class Stage
{
    private $peoplesOnStage = 0;
    public $callIsOn = false;
    public $id;

    public function __construct($id){
        $this->id = $id;
        $_SESSION['stages'][$this->id] = $this;
    }

    public function getId(){
        return $this->id;
    }
    
    public function passengerCome(){
        $this->peoplesOnStage++;
    }

    public function passengerGone(){
        $this->peoplesOnStage--;
    }

    public function getPeoplesOnStage(){
        return $this->peoplesOnStage;
    }

    public function setCallIsOn($callIsOn){
        $this->callIsOn = $callIsOn;
    }

    public function getCallIsOn(){
        return $this->callIsOn;
    }

    public function __destruct(){
        $_SESSION['stages'][$this->id] = $this;
    }
}