<?php
// TODO: For testing-purposes ONLY! REMOVE BEFORE PRODUCTION

spl_autoload_register(function ($class_name) {
    require_once "../classes/" . $class_name . '.class.php';
});
require_once '../../vendor/autoload.php';

// $db from DB.class.php
$courseDescriptionService = new CourseDescriptionService(DB::getDBConnection());

//Welcome to janky-town!
//TODO: Test all functionality of CourseDescriptionService


echo "<!doctype html>

<html lang=\"en\">
<head>
  <meta charset=\"utf-8\">
  <title>TestPage - CourseDescriptionService</title>
</head>
<body>" . PHP_EOL;


printAllCourseDescriptions($courseDescriptionService->getAllCourseDescriptions());

echo "</br> </br>" . PHP_EOL;

printSingleCourseDescription($courseDescriptionService->getCourseDescription(77));

echo "</br> </br>" . PHP_EOL;

echo "Trying to add a coursecode: ";
//Try to add a coursedescription
$courseDescription = new CourseDescription();
$courseDescription->setYear(2020);
$courseDescription->setSingleCourse(true);
$courseDescription->setContinuation(true);
$courseDescription->setSemesterFall(true);
$courseDescription->setSemesterSpring(true);
$courseDescription->setArchived(CourseDescription::ARCHIVED_FALSE);
$courseDescription->setCreatedByIdUser(1);
$courseDescription->setLanguageIdLanguage(1);
$courseDescription->setExamTypeIdExamType(1);
$courseDescription->setGradeScaleIdGradeScale(1);
$courseDescription->setTeachingLocationIdTeachingLocation(1);
$courseDescription->setArchivedByIdUser(1);

if($courseDescriptionService->addCourseDescriptionByObject($courseDescription)){
    echo "Adding Succeeded!" . PHP_EOL;
} else {
    echo "Adding failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseDescriptionService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}
printAllCourseDescriptions($courseDescriptionService->getAllCourseDescriptions());

echo "</br> </br>" . PHP_EOL;

echo "Trying to update a coursecode: ";
if($courseDescriptionService->updateCourseDescription(1,1,2020, true, true, true, true, 1, 1, 1, 2)){
    echo "Updating succeeded!";
} else {
    echo "Updating failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseDescriptionService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}
printAllCourseDescriptions($courseDescriptionService->getAllCourseDescriptions());
echo "</br> </br>" . PHP_EOL;

echo "Trying to archive a coursecode: ";
if($courseDescriptionService->archiveCourse(1, 2)){
    echo "Archiving succeeded!";
} else {
    echo "Archiving failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseDescriptionService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}
printAllCourseDescriptions($courseDescriptionService->getAllCourseDescriptions());
echo "</br> </br>" . PHP_EOL;

echo "Trying to un-archive a coursecode: ";
if($courseDescriptionService->unArchiveCourse(1,2)){
    echo "Un-archiving succeeded!";
} else {
    echo "Un-archiving failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseDescriptionService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}
printAllCourseDescriptions($courseDescriptionService->getAllCourseDescriptions());
echo "</br> </br>" . PHP_EOL;

echo "Trying to Update a coursecode-entry: ";
if($courseDescriptionService->updateCourseDescriptionEntry(3,2019,false, false, false, false, CourseDescription::ARCHIVED_UPDATED,2,2,2,2,2)){
    echo "Updating succeeded!";
} else {
    echo "Updating failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseDescriptionService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}
printAllCourseDescriptions($courseDescriptionService->getAllCourseDescriptions());
echo "</br> </br>" . PHP_EOL;

echo "Trying to delete a coursecode-entry: ";
if($courseDescriptionService->deleteCourseCode(3)){
    echo "Deletion succeeded!";
} else {
    echo "Deletion failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseDescriptionService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}
printAllCourseDescriptions($courseDescriptionService->getAllCourseDescriptions());
echo "</br> </br>" . PHP_EOL;


echo "Trying to copy a coursecode-entry to a new year: ";
if($courseDescriptionService->copyCourseToNewYear(1,1,2030)){
    echo "Copy succeeded!";
} else {
    echo "Copy failed! Cause of failure: <br />" . PHP_EOL;
    foreach ($courseDescriptionService->getLastError() as $errorMsg){
        echo $errorMsg . " " . PHP_EOL;
    }
}
printAllCourseDescriptions($courseDescriptionService->getAllCourseDescriptions());
echo "</br> </br>" . PHP_EOL;


echo "All tests concluded! </br>
</body>
</html>" . PHP_EOL;

function printAllCourseDescriptions(array $courseDescriptions) : void {
    if (is_null($courseDescriptions)){
        echo "\$courseDescriptions was NULL";
        return;
    }
    echo "<table border=\"1\">
  <tr>
    <th>idCourse</th>
    <th>year</th>
    <th>dateCreated</th>
    <th>dateChanged</th>
    <th>singleCourse</th>
    <th>continuation</th>
    <th>semesterFall</th>
    <th>semesterSpring</th>
    <th>archived</th>
    <th>CreatedBy_idUser</th>
    <th>Language_idLanguage</th>
    <th>ExamType_idExamType</th>
    <th>GradeScale_idGradeScale</th>
    <th>TeachingLocation_idTeachingLocation</th>
    <th>ArchivedBy_idUser</th>
  </tr>" . PHP_EOL;

    foreach ($courseDescriptions as $description) {
        echo "<tr>
                <td>" . $description->getIdCourse() . "</td>
                <td>" . $description->getYear() . "</td>
                <td>" . $description->getDateCreated() . "</td>
                <td>" . $description->getDateChanged() . "</td>
                <td>" . $description->isSingleCourse() . "</td>
                <td>" . $description->isContinuation() . "</td>
                <td>" . $description->isSemesterFall() . "</td>
                <td>" . $description->isSemesterSpring() . "</td>
                <td>" . $description->getArchived() . "</td>
                <td>" . $description->getCreatedByIdUser() . "</td>
                <td>" . $description->getLanguageIdLanguage() . "</td>
                <td>" . $description->getExamTypeIdExamType() . "</td>
                <td>" . $description->getGradeScaleIdGradeScale() . "</td>
                <td>" . $description->getTeachingLocationIdTeachingLocation() . "</td>
                <td>" . $description->getArchivedByIdUser() . "</td>
             </tr>" . PHP_EOL;
    }
    echo "</table>" . PHP_EOL;
}

function printSingleCourseDescription(?CourseDescription $courseDescription) : void {
    if (is_null($courseDescription)){
        echo "\$courseDescription was NULL";
        return;
    }
    echo "<table border=\"1\">
  <tr>
    <th>idCourse</th>
    <th>year</th>
    <th>dateCreated</th>
    <th>dateChanged</th>
    <th>singleCourse</th>
    <th>continuation</th>
    <th>semesterFall</th>
    <th>semesterSpring</th>
    <th>archived</th>
    <th>CreatedBy_idUser</th>
    <th>Language_idLanguage</th>
    <th>ExamType_idExamType</th>
    <th>GradeScale_idGradeScale</th>
    <th>TeachingLocation_idTeachingLocation</th>
    <th>ArchivedBy_idUser</th>
  </tr>
  <tr>
    <td>" . $courseDescription->getIdCourse() . "</td>
    <td>" . $courseDescription->getYear() . "</td>
    <td>" . $courseDescription->getDateCreated() . "</td>
    <td>" . $courseDescription->getDateChanged() . "</td>
    <td>" . $courseDescription->isSingleCourse() . "</td>
    <td>" . $courseDescription->isContinuation() . "</td>
    <td>" . $courseDescription->isSemesterFall() . "</td>
    <td>" . $courseDescription->isSemesterSpring() . "</td>
    <td>" . $courseDescription->getArchived() . "</td>
    <td>" . $courseDescription->getCreatedByIdUser() . "</td>
    <td>" . $courseDescription->getLanguageIdLanguage() . "</td>
    <td>" . $courseDescription->getExamTypeIdExamType() . "</td>
    <td>" . $courseDescription->getGradeScaleIdGradeScale() . "</td>
    <td>" . $courseDescription->getTeachingLocationIdTeachingLocation() . "</td>
    <td>" . $courseDescription->getArchivedByIdUser() . "</td>
   </tr>
  </table>" . PHP_EOL;
}






