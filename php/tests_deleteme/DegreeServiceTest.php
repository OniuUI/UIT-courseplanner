<?php
// TODO: For testing-purposes ONLY! REMOVE BEFORE PRODUCTION

spl_autoload_register(function ($class_name) {
    require_once "../classes/" . $class_name . '.class.php';
});
require_once '../../vendor/autoload.php';

// $db from DB.class.php
$degreeService = new DegreeService(DB::getDBConnection());

//Welcome to janky-town

//Test if we can get a single degree,
$degree = $degreeService->getDegree(2);
echo $degree->getDegree() . "<br />" . PHP_EOL;

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Try to add a degree
if($degreeService->addDegree("Phd.")){
    echo "Adding Succeeded!" . PHP_EOL;
} else {
    echo "Adding failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($degreeService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all degrees, then display them
$arrDegree = array();
$arrDegree=$degreeService->getAllDegrees();
foreach($arrDegree as $degree){
    echo $degree->getIdDegree(). " " .
        $degree->getDegree() . "<br />" .
        PHP_EOL;
}


echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//TEst if we can delete a coursecode
if($degreeService->deleteDegree(8)){
    echo "Deletion Succeeded!" . PHP_EOL;
} else {
    echo "Deletion failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($degreeService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}


echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all degrees, then display them
$arrDegree = array();
$arrDegree=$degreeService->getAllDegrees();
foreach($arrDegree as $degree){
    echo $degree->getIdDegree(). " " .
        $degree->getDegree() . "<br />" .
        PHP_EOL;
}