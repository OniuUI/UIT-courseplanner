<?php
// TODO: For testing-purposes ONLY! REMOVE BEFORE PRODUCTION

spl_autoload_register(function ($class_name) {
    require_once "../classes/" . $class_name . '.class.php';
});
require_once '../../vendor/autoload.php';

// $db from DB.class.php
$courseCodeService = new CourseCodeService(DB::getDBConnection());

//Welcome to janky-town

//Test to merge two courses
$courseCode1 = $courseCodeService->getCourseCode(1);
$courseCode2 = $courseCodeService->getCourseCode(2);
$newCourseCode = "Hybrid-9001";
if($courseCodeService->mergeCourseCode($courseCode1, $courseCode2, $newCourseCode)){
    echo "Merge Succeeded!" . PHP_EOL;
} else {
    echo "Merge failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseCodeService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get a single coursecode,
$courseCode = $courseCodeService->getCourseCode(3);
echo $courseCode->getNameNbNo() . "<br />" . PHP_EOL;

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Try to add a coursecode
if($courseCodeService->addCourseCode("ITE-1815", "SysUtvikling", "SysUtvikling", "SysDev", 1,2)){
    echo "Adding Succeeded!" . PHP_EOL;
} else {
    echo "Adding failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseCodeService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all course-codes, then display them
$CourseCodes = array();
$CourseCodes=$courseCodeService->getAllCourseCodes();
foreach($CourseCodes as $Course){
    echo $Course->getIdCourseCode() . " " .
        $Course->getCourseCode() . " " .
        $Course->getNameNbNo() . " " .
        $Course->getNameNbNn() . " " .
        $Course->getNameEnGb() . " " .
        $Course->getDegreeIdDegree() . " " .
        $Course->getStudyPointsIdStudyPoints() . " " . "<br />" .
        PHP_EOL;
}


echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//TEst if we can delete a coursecode
if($courseCodeService->deleteCourseCode(5)){
    echo "Deletion Succeeded!" . PHP_EOL;
} else {
    echo "Deletion failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseCodeService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

$CourseCodes=$courseCodeService->getAllCourseCodes();
foreach($CourseCodes as $Course){
    echo $Course->getIdCourseCode() . " " .
        $Course->getCourseCode() . " " .
        $Course->getNameNbNo() . " " .
        $Course->getNameNbNn() . " " .
        $Course->getNameEnGb() . " " .
        $Course->getDegreeIdDegree() . " " .
        $Course->getStudyPointsIdStudyPoints() . " " . "<br />" .
        PHP_EOL;
}