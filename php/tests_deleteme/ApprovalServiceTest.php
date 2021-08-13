<?php
// TODO: For testing-purposes ONLY! REMOVE BEFORE PRODUCTION

spl_autoload_register(function ($class_name) {
    require_once "../classes/" . $class_name . '.class.php';
});
require_once '../../vendor/autoload.php';

// $db from DB.class.php
$approvalService = new ApprovalService(DB::getDBConnection());

//Welcome to janky-town
echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

//Test if we can get all Approvals-codes, then display them
echo "Trying to display all approvals: " . PHP_EOL;
$result = $approvalService->getAllApprovals();
if(!is_null($result) || !empty($result)){
    printAllCourseDescriptions($result);
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($approvalService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to display all approvals: " . PHP_EOL;
$result = $approvalService->getApprovalsByCourse(1);
    if(!is_null($result) || !empty($result)){
        printAllCourseDescriptions($result);
        echo "Success!" . PHP_EOL;
    } else {
        echo "Failure: " . PHP_EOL;
        var_dump($approvalService->getLastError());
    }

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to display all approvals: " . PHP_EOL;
    $result = $approvalService->getApprovalByID(2);
    if(!is_null($result) || !empty($result)){
        printSingleCourseDescription($result);
        echo "Success!" . PHP_EOL;
    } else {
        echo "Failure: " . PHP_EOL;
        var_dump($approvalService->getLastError());
    }

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to add approval: " . PHP_EOL;
if($approvalService->addApproval("2020-05-05 23-50-50", 2)){
printAllCourseDescriptions($approvalService->getAllApprovals());
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($approvalService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to update approval entry: " . PHP_EOL;
$approvalObject = $approvalService->getApprovalByID(1);
$approvalObject->setApprovalDeadline(date("Y-m-d H:i:s"));
$approvalObject->setApproved(true);
$approvalObject->setApprovedDate(date("Y-m-d H:i:s"));
$approvalObject->setApprovedCourseCoordinator(true);
$approvalObject->setApprovedDateCourseCoordinator(date("Y-m-d H:i:s"));
$approvalObject->setApprovedInstituteLeader(true);
$approvalObject->setApprovedDateInstituteLeader(date("Y-m-d H:i:s"));
$approvalObject->setCourseDescriptionIdCourse(1);
if($approvalService->updateApprovalEntryByObject($approvalObject)){
printAllCourseDescriptions($approvalService->getAllApprovals());
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($approvalService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to update approval status, course-coordinator: " . PHP_EOL;
if($approvalService->updateApprovalStatus(5, ApprovalService::COURSE_COORDINATOR, true)){
    printSingleCourseDescription($approvalService->getApprovalByID(5));
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($approvalService->getLastError());
}

echo "<br />" . PHP_EOL;
echo "<br />" . PHP_EOL;

echo "Trying to update approval status, institute-leader: " . PHP_EOL;
if($approvalService->updateApprovalStatus(5, ApprovalService::INSTITUTE_LEADER, true)){
printSingleCourseDescription($approvalService->getApprovalByID(5));
    echo "Success!" . PHP_EOL;
} else {
    echo "Failure: " . PHP_EOL;
    var_dump($approvalService->getLastError());
}



function printAllCourseDescriptions(array $Approvals): void
{
    if (is_null($Approvals)) {
        echo "\$courseDescriptions was NULL";
        return;
    }
    echo "<table border=\"1\">
    <tr>
    <th>idApproval</th>
    <th>approvalDeadline</th>
    <th>approved</th>
    <th>approvedDate</th>
    <th>approvedCourseCoordinator</th>
    <th>approvedDateCourseCoordinator</th>
    <th>approvedInstituteLeader</th>
    <th>approvedDateInstituteLeader</th>
    <th>CourseDescription_idCourse</th>
  </tr>" . PHP_EOL;

    foreach ($Approvals as $Approval) {
        echo "<tr>
    <td>" . $Approval->getIdApproval() . "</td>
    <td>" . $Approval->getApprovalDeadline() . "</td>
    <td>" . $Approval->getApproved() . "</td>
    <td>" . $Approval->getApprovedDate() . "</td>
    <td>" . $Approval->getApprovedCourseCoordinator() . "</td>
    <td>" . $Approval->getApprovedDateCourseCoordinator() . "</td>
    <td>" . $Approval->getApprovedInstituteLeader() . "</td>
    <td>" . $Approval->getApprovedDateInstituteLeader() . "</td>
    <td>" . $Approval->getCourseDescriptionIdCourse() . "</td>
   </tr>" . PHP_EOL;
    }
    echo "</table>" . PHP_EOL;
}

function printSingleCourseDescription(?Approval $Approval): void
{
    if (is_null($Approval)) {
        echo "\$Approval was NULL";
        return;
    }
    echo "<table border=\"1\">
  <tr>
    <th>idApproval</th>
    <th>approvalDeadline</th>
    <th>approved</th>
    <th>approvedDate</th>
    <th>approvedCourseCoordinator</th>
    <th>approvedDateCourseCoordinator</th>
    <th>approvedInstituteLeader</th>
    <th>approvedDateInstituteLeader</th>
    <th>CourseDescription_idCourse</th>
  </tr>
  <tr>
    <td>" . $Approval->getIdApproval() . "</td>
    <td>" . $Approval->getApprovalDeadline() . "</td>
    <td>" . $Approval->getApproved() . "</td>
    <td>" . $Approval->getApprovedDate() . "</td>
    <td>" . $Approval->getApprovedCourseCoordinator() . "</td>
    <td>" . $Approval->getApprovedDateCourseCoordinator() . "</td>
    <td>" . $Approval->getApprovedInstituteLeader() . "</td>
    <td>" . $Approval->getApprovedDateInstituteLeader() . "</td>
    <td>" . $Approval->getCourseDescriptionIdCourse() . "</td>
   </tr>
  </table>" . PHP_EOL;
}