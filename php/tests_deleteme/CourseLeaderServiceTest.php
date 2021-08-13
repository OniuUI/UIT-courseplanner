<?php
// TODO: For testing-purposes ONLY! REMOVE BEFORE PRODUCTION

spl_autoload_register(function ($class_name) {
    require_once "../classes/" . $class_name . '.class.php';
});
require_once '../../vendor/autoload.php';

// $db from DB.class.php
$courseLeaderService = new CourseLeaderService(DB::getDBConnection());

//Welcome to janky-town

//Test if we can get a single courseleader,
$courseLeader = $courseLeaderService->getCourseLeader(2);
echo $courseLeader->getUserIdUser() . PHP_EOL;

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Try to add a courseleader
if($courseLeaderService->addCourseLeader(2, 2)){
    echo "Adding Succeeded!" . PHP_EOL;
} else {
    echo "Adding failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseLeaderService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all course-leaders, then display them
$CourseLeaders = array();
$CourseLeaders=$courseLeaderService->getAllCourseLeaders();
foreach($CourseLeaders as $Leader){
    echo $Leader->getIdCourseLeader() . " " .
        $Leader->getCourseCodeIdCourseCode() . " " .
        $Leader->getUserIdUser() . "<br /> " .
        PHP_EOL;
}


echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can delete a courseleader
if($courseLeaderService->deleteCourseLeader(9)){
    echo "Deletion Succeeded!" . PHP_EOL;
} else {
    echo "Deletion failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseLeaderService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all course-leaders, then display them. Again
$CourseLeaders = array();
$CourseLeaders=$courseLeaderService->getAllCourseLeaders();
foreach($CourseLeaders as $Leader){
    echo $Leader->getIdCourseLeader() . " " .
        $Leader->getCourseCodeIdCourseCode() . " " .
        $Leader->getUserIdUser() . "<br /> " .
        PHP_EOL;
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can update a courseleader
if($courseLeaderService->updateCourseLeader(8, 3)){
    echo "Update Succeeded!" . PHP_EOL;
} else {
    echo "Update failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseLeaderService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}


echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all course-leaders, then display them. Again
$CourseLeaders = array();
$CourseLeaders=$courseLeaderService->getAllCourseLeaders();
foreach($CourseLeaders as $Leader){
    echo $Leader->getIdCourseLeader() . " " .
        $Leader->getCourseCodeIdCourseCode() . " " .
        $Leader->getUserIdUser() . "<br /> " .
        PHP_EOL;
}