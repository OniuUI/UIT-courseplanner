<?php

class Register
{
    private $db;


    function __construct(PDO $db)
    {
        $this->db = $db;
    }




    /**
     * TODO: Fix Notice on Array access Bool, Null or Int.
     * TODO: Fix and replace "tempPassword" notation.
     * General method for hashing password and sending data to the database.
     * @param user $bruker
     * @return bool
     */
    public function register(user $bruker)
    {
        try {
            $usernameFetch = $bruker->getUsername();
            $firstnameFetch = $bruker->getFirstname();
            $lastnameFetch = $bruker->getLastname();
            $passwordFetch = $bruker->getTempPassword();
            $emailFetch = $bruker->getEmail();
            $emailvalidateFetch = $bruker->getEmailValidate();
            $standardPermission = 1;



          $prep = $this->db->prepare("SELECT username,email FROM user WHERE username=:uname OR email=:umail");
            $prep->execute(array(':uname'=>$usernameFetch, ':umail'=>$emailFetch));
            $row=$prep->fetch(PDO::FETCH_ASSOC);
            if(!$row['username']==$usernameFetch or !$row['email']==$emailFetch ) {

                $stmt = $this->db->prepare("INSERT INTO user(username,password,email,firstname,lastname,emailValidate, permission) VALUES (:username,:hashpassword,:email,:firstname,:lastname,:emailvalidate, :permission)");
                $stmt->bindParam(':username', $usernameFetch, PDO::PARAM_STR);
                $stmt->bindParam(':permission', $standardPermission, PDO::PARAM_INT);
                $hash_password = password_hash($passwordFetch, PASSWORD_BCRYPT);
                $stmt->bindParam(':hashpassword', $hash_password, PDO::PARAM_STR);
                $stmt->bindParam(':email', $emailFetch, PDO::PARAM_STR);
                $stmt->bindParam(':firstname', $firstnameFetch, PDO::PARAM_STR);
                $stmt->bindParam(':lastname', $lastnameFetch, PDO::PARAM_STR);
                $stmt->bindParam(':emailvalidate', $emailvalidateFetch, PDO::PARAM_STR);
                if ($stmt->execute() or die(print_r($stmt->errorInfo() , false))) {
                    $bruker->setLoggedIn(true);
                    $_SESSION['bruker'] = $bruker;
                    return true;
                }
            }
            else echo'<p class="alert-danger">Username or Email has already been registered!</p>';
            unset ($_SESSION['bruker']);
        } catch (PDOException $e) {
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }


    }
}