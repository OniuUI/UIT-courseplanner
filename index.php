<?php

spl_autoload_register(function ($class_name) {
    require_once "php/classes/" . $class_name . '.class.php';});

    //$cCArchive = new CourseCodeArchive($db);
    require_once 'vendor/autoload.php';
    require_once 'login.php';
    @session_start();

/* User And active login variables. */
$loader = new Twig\Loader\FilesystemLoader('templates');


// TODO: UNCOMMENT BEFORE PRODUCTION!!!
//$twig = new Twig\Environment($loader, array('cache' => './compilation_cache'));
// TODO: REMOVE BEFORE PRODUCTION!!!
$twig = new Twig\Environment($loader, array('cache' => false));

if(!isset($_SESSION['bruker'])) {
    echo $twig->render('logInPage.twig');
}

/* User related verification/check -> If bruker session is created
   The user should be able to load php functions hidden behind the login. */

// TODO Implement backend features after login.


    else {//if($_SESSION['bruker']->isLoggedIn()){           //The Else represent runtime state after login, could be replaced by a if since it's not optimal to have the homepage as a fallback option if the session is reset after login.


        echo $twig->render('homepage.twig', array('user' => $_SESSION['bruker']));

      /*  if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['createCourse'])) {
            $courseCode = new CourseCode();

            $courseCode->setCourseCode($_POST['courseCode']);
            $courseCode->setNameNbNo($_POST['name_nb_no']);
            $courseCode->setNameNbNn($_POST['name_nb_nn']);
            $courseCode->setNameEnGb($_POST['name_en_gb']);
            $courseCode->setDegreeIdDegree($_POST['Degree_idDegree']);
            $courseCode->setStudyPointsIdStudyPoints($_POST['StudyPoints_idStudyPoints']);


            $cCArchive->createCourse($courseCode);
            echo $twig->render('courseCreationSuccess.twig', array('course' => $courseCode));
        }

        else if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['deleteCourse']) && isset($_GET['IdCourseCode'])) {
            $id = intval($_GET['idCourseCode']);
            $cCArchive->deleteCourse($id);
         echo $twig->render('courseDeletionSuccess.twig', array('IdCourseCode' => $id)); } */

    }

