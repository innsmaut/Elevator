<?php

function __autoload($class){
    require_once( $class.".php");
}

if (session_status() !== PHP_SESSION_ACTIVE){
    session_id('SID');
    session_start();
    $_SESSION['passengers'] = array();
    $_SESSION['stages'] = array();
}

$app = SystemController::getInstance([
    'elevator' => Elevator::getInstance(),
    'PassengerFactory' => PassengerFactory::getInstance(),
    'StageFactory' => StageFactory::getInstance(),
    'stagesCount' => 4
]);

if (isset($_REQUEST['getNewValues'])){
    unset($_REQUEST['getNewValues']);
    $app->behave();
    $result = $app->getGlobalStatus();
    echo json_encode($result);
} elseif (isset($_REQUEST['getGlobalStatus'])) {
    unset($_REQUEST['getGlobalStatus']);
    $result = $app->getGlobalStatus();
    echo json_encode($result);
} elseif (isset($_REQUEST['addNewPassenger'])) {
    unset($_REQUEST['addNewPassenger']);
    $result = $app->registerPassenger();
    echo json_encode($result);
} elseif (isset($_REQUEST['resetThisSession'])) {
    unset($_REQUEST['resetThisSession']);
    session_destroy();
    echo json_encode('session restarted');
} else {
    $layout = include_once 'layout.html';
    return $layout;
}


