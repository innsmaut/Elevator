<?php

class SystemController
{
    private static $instance;
    public $passengersCanGo = false;
    public $passengersCanBoardElev = false;
    private $destinationQueue = array();
    public $passengers = array();
    private $elevator;
    private $passengerFactory;
    private $stageFactory;
    private $stagesCount;
    private $stages = array();
    private $initStage = 0;

    private function __construct($args){
        $this->elevator = $args['elevator'];
        $this->elevator->registerSystemController($this);
        $this->passengerFactory = $args['PassengerFactory'];
        $this->stageFactory = $args['StageFactory'];
        $this->stagesCount = $args['stagesCount'];
        for ($i = 0; $i < $this->stagesCount; $i++){
            array_push($this->stages, $this->stageFactory->getStage());
        }
        $_SESSION[__CLASS__] = static::$instance;
    }

    public static function getInstance($args) {
        if (isset($_SESSION[__CLASS__])){
            static::$instance = $_SESSION[__CLASS__];
        }
        if ( !isset( static::$instance ) ) {
            static::$instance = new static($args);
        }
        return static::$instance;
    }

    public function callForElevator($stageId){
        $this->stages[$stageId]->setCallIsOn(true);
    }

    public function registerStage(){
        array_push($this->stages, $this->stageFactory->getStage());
    }

    public function registerPassenger(){
        $passenger = $this->passengerFactory->getPassenger();
        $passenger->registerSystemController($this);
        array_push($this->passengers, $passenger);
        $this->stages[$this->initStage]->passengerCome();
        return $passenger->report();
    }

    public function passengerLeftStage($stage, $destination){ //passenger boards elevator
        if ($this->elevator->passengerIn()){
            $this->stages[$stage]->passengerGone();
            array_push($this->destinationQueue, $destination);
            $this->destinationQueue = array_unique($this->destinationQueue);
            return true;
        } else return false;
    }

    public function passengerBoardStage($stage){
        if ($this->elevator->passengerOut()) {
            $this->stages[$stage]->passengerCome();
            return true;
        } else return false;
    }

    public function gatherData(){
        $data = array();
        $data['elevatorLocation'] = $this->elevator->location;
        $data['stages'] = array();
        foreach ($this->stages as $stage){
            $data['stages'][$stage->getId()] = $stage->getPeoplesOnStage();
        }
        return $data;
    }

    private function passengersBehave(){
        $data = $this->gatherData();
        foreach ($this->passengers as $passenger){
            $data['inElevator'] = true;
            $passenger->behave($data);
        }
        foreach ($this->passengers as $passenger){
            $data['inElevator'] = false;
            $passenger->behave($data);
        }
    }

    private function destinationFromStages(){
        $stage = $this->elevator->location;
        for ($i = 1; $i <= $this->stagesCount; $i++){
            if (isset($this->stages[$stage + $i]) && ($this->stages[$stage + $i]->getCallIsOn())) {
                return $stage + $i;
            }
            if (isset($this->stages[$stage - $i]) && ($this->stages[$stage - $i]->getCallIsOn())) {
                return $stage - $i;
            }
        }
        return $stage;
    }

    private function elevatorBehave(){
        if ($this->passengersCanGo){
            $destination = array_pop($this->destinationQueue);
            if ($destination === null){
                $destination = $this->destinationFromStages();
            }
            $this->elevator->setDestination($destination);
        }
        $this->elevator->behave();
    }

    public function behave(){
        if ($this->passengersCanGo){
            foreach ($this->stages as $stage){
                $stage->setCallIsOn(false);
            }
        }
        $this->passengersBehave();
        $this->elevatorBehave();
        return $this;
    }

    public function getGlobalStatus(){
        $gS = array();
        $gS['passengers'] = array();
        foreach ($this->passengers as $passenger){
            $gS['passengers'][$passenger->getId()] = $passenger->report();
        }
        $gS['stages'] = array();
        foreach ($this->stages as $stage){
            $gS['stages'][$stage->getId()] = $stage->getCallIsOn();
        }
        $gS['elevator'] = $this->elevator->report();
        $gS['data'] = $this->gatherData();
        $gS['passengersCanGo'] = $this->passengersCanGo;
        $gS['destinationQueue'] = $this->destinationQueue;
        return $gS;
    }

    public function __destruct(){
        $_SESSION[__CLASS__] = static::$instance;
    }
}