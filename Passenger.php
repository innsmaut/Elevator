<?php

class Passenger
{
    public $id;
    private $onStage = 0;
    private $destination = 0;
    private $inElevator = false;
    private $isWaitingElev = false;
    private $canMove = false;
    private $status = 100;
    private $statusStep = 35;
    private $sociopathy = 2;
    private $data;
    private $sc;

    public function __construct($id){
        $this->id = $id;
        $_SESSION['passengers'][$this->id] = $this;
    }

    public function getId(){
        return $this->id;
    }

    private function calculateCanMove($elevatorStage){
        if (($this->isWaitingElev && $this->onStage == $elevatorStage) ||
            ($this->inElevator && $this->destination == $elevatorStage)){
            $this->canMove = true;
        } else $this->canMove = false;
    }

    public function registerSystemController(SystemController $sc){
        $this->sc = $sc;
    }

    private function calculateStatus($peoplesOnStage){
        $multiplier = $peoplesOnStage - $this->sociopathy;
        if ($multiplier > 0) {
            $this->status -= $multiplier * $this->statusStep;
        } else {
            $this->status += $this->statusStep;
        }

        if ($this->status > 100) $this->status = 100;
        if ($this->status < 0) $this->status = 0;
    }

    public function report(){
        return [
            'id' => $this->id,
            'status' => $this->status,
            'stage' => $this->onStage,
            'inElevator' => $this->inElevator,
            'isWaitingElev' => $this->isWaitingElev,
            'destination' => $this->destination
        ];
    }

    private function calculateDestination(){
        do {
            $this->destination = mt_rand(0, count($this->data['stages']) - 1);
        } while ($this->destination == $this->onStage);
    }

    private function callElevator(){
        if ($this->destination == $this->onStage){
            $this->calculateDestination();
        }
        $this->sc->callForElevator($this->onStage);
        $this->isWaitingElev = true;
    }

    private function boardElevator(){
        if ($this->sc->passengerLeftStage($this->onStage, $this->destination)){
            $this->inElevator = true;
            $this->isWaitingElev = false;
        }
    }

    private function leaveElevator(){
        $this->onStage = $this->data['elevatorLocation'];
        if ($this->sc->passengerBoardStage($this->onStage)){
            $this->inElevator = false;
        }
    }

    private function move(){
        if ($this->inElevator) $this->leaveElevator();
        if ($this->isWaitingElev) $this->boardElevator();
    }

    public function behave($data){
        $this->data = $data;
        if ($data['inElevator'] == $this->inElevator) {
            $this->calculateCanMove($this->data['elevatorLocation']);
            if ($this->canMove){
                $this->move();
            } else {
                $this->calculateStatus($this->data['stages'][$this->onStage]);
                if ($this->status == 0 && $this->inElevator == false){
                    $this->callElevator();
                }
            }
        }
    }

    public function __destruct(){
        $_SESSION['passengers'][$this->id] = $this;
    }

}