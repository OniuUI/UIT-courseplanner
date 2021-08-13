<?php
class User
{
    // TODO: implement type-hinting
    private $firstname;
    private $lastname;

    private $loggedIn;
    private $emailValidate;
    private $username;
    private $email;
    private $permission;
    private $userID;
    private $AdminMode;
    private $TempPassword;



    function __construct()
    {


    }



    /**
     *
     * TODO: Replace this, security issue when storing passwords in clear text in local user session.
     * @return mixed
     */
    public function getTempPassword()
    {
        return $this->TempPassword;

    }

    /**
     * TODO: Replace this, security issue when storing passwords in clear text in local user session.
     * @param $TempPassword
     */
    public function setTempPassword($TempPassword): void
    {
        $this->TempPassword = $TempPassword;
    }

    /**
     * @return mixed
     */
    public function getAdminMode()
    {
        return $this->AdminMode;
    }

    /**
     * @param mixed $AdminMode
     */
    public function setAdminMode($AdminMode): void
    {   if($AdminMode == 'true' || 'false'){
        $this->AdminMode = $AdminMode;
    }
    }

    /**
     * @return mixed
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param mixed $permission
     */
    public function setPermission($permission): void
    {
        $this->permission = $permission;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID): void
    {
        $this->userID = $userID;
    }


    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    /**
     * @param bool $loggedIn
     */
    public function setLoggedIn($loggedIn): void
    {
        $this->loggedIn = $loggedIn;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getFullName(){
        $fullname = ($this->getFirstname() . ' ' .  $this->getLastname());
        return $fullname;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }


    /**
     * @return mixed
     */
    public function getEmailValidate()
    {
        return $this->emailValidate;
    }

    /**
     * @param $emailValidate
     */
    public function setEmailValidate($emailValidate): void
    {
        $this->emailValidate = $emailValidate;
    }

    /**
     * Unset's session when method is called.
     */
    public function logOut(){
        unset ($_SESSION['bruker']);
        $this->loggedIn = false;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @param $db
     */
    public function UpdateUserInfo($db){
        $prep = $db->prepare("UPDATE user SET lastlogin=:logintime WHERE username=:uname");
        $username = $this->getUsername();
        $date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        $prep->bindParam(':uname', $username, PDO::PARAM_STR);
        $prep->bindParam(':logintime', $date, PDO::PARAM_STR);
        $prep->execute() or die(print_r($prep->errorInfo(), false));
    }


        /*
        TODO: Implement methods as permission check based on institute role.
        $row = $prep->fetch(PDO::FETCH_ASSOC);
             if ($row['permission'] > 1) {
             $this->setFirstname($row['firstname']);
             $this->setLastname($row['lastname']);
         }
      */


    /**
     * TODO: Fix Notice on Array access Bool, Null or Int.
     * @param PDO $db
     * @param $usernameEmail
     * @param $password
     * @return bool
     */
    public function login(PDO $db,$usernameEmail, $password)
    {
        $prep = $db->prepare("SELECT permission, username, email FROM user WHERE username=:uname OR email=:umail");
        $prep->execute(array(':uname' => $usernameEmail, ':umail' => $usernameEmail));
        $row = $prep->fetch(PDO::FETCH_ASSOC);
        if(!$row['username'] || !$row['email'] == $usernameEmail){
            echo '<p class="alert-danger">User does not exist, please try again or register an account.</p>';
            return false;
        }

        //TODO: Implement security check for permission based on institute role.
       // if ($row['permission'] == 1) echo '<p class="alert-danger">You need to verify your email before logging in!</p>';
       // if ($row['permission'] > 1) {
            $this->setUsername($row['username']);

            $stmt = $db->prepare("SELECT id, firstname, lastname, password, permission FROM user WHERE username=:usernameEmail OR email=:usernameEmail");
            $stmt->bindParam(':usernameEmail', $usernameEmail, PDO::PARAM_STR);


            $stmt->execute();
            $this->loggedIn = true;
            if ($rad = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $rad["password"])) {
                    $this->setUserID($rad['id']);
                    $this->setPermission($rad['permission']);
                    $this->setLoggedIn('true');
                    $this->setFirstname($rad['firstname']); $this->setLastname($rad['lastname']);
                    return true;
                } else return false;
            } else return false;
        }
    //}
}
?>
