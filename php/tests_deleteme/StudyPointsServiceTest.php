<?php
// TODO: For testing-purposes ONLY! REMOVE BEFORE PRODUCTION

spl_autoload_register(function ($class_name) {
    require_once "../classes/" . $class_name . '.class.php';
});
require_once '../../vendor/autoload.php';

// $db from DB.class.php
$studyPointsService = new StudyPointsService(DB::getDBConnection());

//Welcome to janky-town

//Test if we can get a single studypoint,
$studyPoints = $studyPointsService->getStudyPoints(2);
echo $studyPoints->getStudyPoints(). "<br />" . PHP_EOL;

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Try to add studypoints
if($studyPointsService->addStudyPoints(60)){
    echo "Adding Succeeded!" . PHP_EOL;
} else {
    echo "Adding failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($studyPointsService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all studypoints, then display them
$arrStudyPoints = array();
$arrStudyPoints=$studyPointsService->getAllStudyPoints();
foreach($arrStudyPoints as $StudyPoint){
    echo $StudyPoint->getIdStudyPoints(). " " .
        $StudyPoint->getStudyPoints() . "<br />" .
        PHP_EOL;
}


echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//TEst if we can delete a studypoint
if($studyPointsService->deleteStudyPoints(10)){
    echo "Deletion Succeeded!" . PHP_EOL;
} else {
    echo "Deletion failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($studyPointsService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}


echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all studypoints, then display them
$arrStudyPoints = array();
$arrStudyPoints=$studyPointsService->getAllStudyPoints();
foreach($arrStudyPoints as $StudyPoint){
    echo $StudyPoint->getIdStudyPoints(). " " .
        $StudyPoint->getStudyPoints() . "<br />" .
        PHP_EOL;
}