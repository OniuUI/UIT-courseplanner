<?php

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

spl_autoload_register(function ($class_name) {
    require "classes/" . $class_name . '.class.php';});
session_start();


require_once 'vendor/autoload.php';
$loader = new Twig\Loader\FilesystemLoader('templates');

// TODO: UNCOMMENT BEFORE PRODUCTION!!!
//$twig = new Twig\Environment($loader, array('cache' => './compilation_cache'));
// TODO: REMOVE BEFORE PRODUCTION!!!
$twig = new Twig\Environment($loader, array('cache' => false));

$registerUser = new Register(DB::getDBConnection());


if (!isset($_SESSION['bruker'])) {
    if (isset($_POST['login'])) {
        $_SESSION['bruker'] = new User;
        if ($_SESSION['bruker']->login(DB::getDBConnection() ,$_POST['usernameEmail'], $_POST['password'])) {
            $_SESSION['bruker']->UpdateUserInfo(DB::getDBConnection());
            echo $twig->render('homepage.twig');
            exit;
        }
        else unset($_SESSION['bruker']);
    }


    if (isset($_POST['register'])) {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        if ($_POST['password'] != $_POST['confirm_password']) echo '<p class="alert-danger">Passwords does not match</p>';
        if ($_POST['password'] == $_POST['confirm_password']) {
            $validationToken = md5(uniqid(rand(), 1));

            $user = new User;
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setUsername($username);
            $user->setTempPassword($password);
            $user->setEmail($email);
            $user->setEmailValidate($validationToken);
            $registerUser->register($user);

        }

        else {
            try {
                echo $twig->render('logInPage.twig', array());
            } catch (LoaderError $e) {
                print("Loader error logging inn");
            } catch (RuntimeError $e) {
                print("Runtime error logging inn");
            } catch (SyntaxError $e) {
                print("Syntax error logging inn");
            }
            exit;

        }
    }

}
/* else {
        try {
            echo $twig->render('logInPage.twig', array());
        } catch (LoaderError $e) {
            print("Loader error logging inn");
        } catch (RuntimeError $e) {
            print("Runtime error logging inn");
        } catch (SyntaxError $e) {
            print("Syntax error logging inn");
        }
        exit;


} */
   /* if (isset($_SESSION['innlogget'])) include('homepage.php'); */
?>
