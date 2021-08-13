<?php
// TODO: For testing-purposes ONLY! REMOVE BEFORE PRODUCTION

spl_autoload_register(function ($class_name) {
    require_once "../classes/" . $class_name . '.class.php';
});
require_once '../../vendor/autoload.php';

// $db from DB.class.php
$academicContentService = new AcademicContentService(DB::getDBConnection());

//Welcome to janky-town
echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all AcademicContent-entries, then display them
echo "Trying to display all AcademicContent-entries: " . PHP_EOL;
$result = $academicContentService->getAllEntries();
if(!is_null($result) || !empty($result)){
    printAllAcademicContentServiceEntries($result);
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to display last entry for a given CourseDescription-id: " . PHP_EOL;
$result = $academicContentService->getLastEntryForCourseDescription(1);
if(!is_null($result) || !empty($result)){
    printSingleAcademicContentServiceEntry($result);
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to display all entries for a given coursedescription-id: " . PHP_EOL;
$result = $academicContentService->getAllEntriesForCourseDescription(1);
if(!is_null($result) || !empty($result)){
    printAllAcademicContentServiceEntries($result);
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to display last entry for a given coursecode: " . PHP_EOL;
$result = $academicContentService->getLastEntryForCourseCode(1);
if(!is_null($result) || !empty($result)){
    printSingleAcademicContentServiceEntry($result);
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to display all entries for a given coursecode: " . PHP_EOL;
$result = $academicContentService->getAllEntriesForCourseCode(1);
if(!is_null($result) || !empty($result)){
    printAllAcademicContentServiceEntries($result);
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to display last entry made: " . PHP_EOL;
$result = $academicContentService->getLastEntry();
if(!is_null($result) || !empty($result)){
    printSingleAcademicContentServiceEntry($result);
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to display all entries for a given AcademicContentID: " . PHP_EOL;
$result = $academicContentService->getEntry(2);
if(!is_null($result) || !empty($result)){
    printSingleAcademicContentServiceEntry($result);
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to add entry: " . PHP_EOL;
if($academicContentService->addEntry(1,"This entry was added today, and not yesterday. But yesterday is last-days today, so was it really added today and not yesterday? We will never know!")){
    printAllAcademicContentServiceEntries($academicContentService->getAllEntries());
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to delete entry: " . PHP_EOL;
if($academicContentService->deleteEntry($academicContentService->getLastEntry()->getIdAcademicContent())){
    printAllAcademicContentServiceEntries($academicContentService->getAllEntries());
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($academicContentService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;


function printAllAcademicContentServiceEntries(array $academicContents): void
{
    if (is_null($academicContents)) {
        echo "\$courseDescriptions was NULL";
        return;
    }
    echo "<table border=\"1\">
    <tr>
    <th>IdAcademicContent</th>
    <th>DateCreated</th>
    <th>AcademicContent</th>
  </tr>" . PHP_EOL;

    foreach ($academicContents as $academicContent) {
        echo "<tr>
    <td>" . $academicContent->getIdAcademicContent() . "</td>
    <td>" . $academicContent->getDateCreated() . "</td>
    <td>" . $academicContent->getAcademicContent() . "</td>
   </tr>" . PHP_EOL;
    }
    echo "</table>" . PHP_EOL;
}

function printSingleAcademicContentServiceEntry(?AcademicContent $academicContent): void
{
    if (is_null($academicContent)) {
        echo "\$Approval was NULL";
        return;
    }
    echo "<table border=\"1\">
  <tr>
    <th>IdAcademicContent</th>
    <th>DateCreated</th>
    <th>AcademicContent</th>
  </tr>
  <tr>
    <td>" . $academicContent->getIdAcademicContent() . "</td>
    <td>" . $academicContent->getDateCreated() . "</td>
    <td>" . $academicContent->getAcademicContent() . "</td>
   </tr>
  </table>" . PHP_EOL;
}