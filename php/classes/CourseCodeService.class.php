<?php
class CourseCodeService
{
    private PDO $db;
    private array $errorMsgs;


    // TODO: Add "archived" status with database update

    /**
     * CourseCodeService.class constructor.
     * Throws InvalidArgumentException if no valid database-object passed.
     * @param $db Database-object to use for the connection
    */
    public function __construct(?PDO $db)
    {
        //Throw exception if we get a NULL object
        if (is_NULL($db)) {
            throw new InvalidArgumentException("No valid database-object passed: \$db=$db");
        } else {
            $this->db = $db;
        }
    }

    /**
     * Get all available courses.
     * Returns an array with CourseCode-objects of all available courses. Returns NULL if it fails.
     * Call GetLastError to get error-message.
     * @return array|null Array Containing CourseCode-objects or NULL
     */
    public function getAllCourseCodes() : ?array {
        $arrCourseCodes = array();
        try {
            // No userdata used, no real need to sanitize...
            // TODO: change from * to what is minimally needed to display a course
            $stmt = $this->db->prepare("select * from `CourseCode` order by `CourseCode`.`idCourseCode`;");

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                //Fetch CourseCode-objects from query-results
                while ($courseCode = $stmt->fetchObject("CourseCode")) {
                    $arrCourseCodes[] = $courseCode;
                }
                return $arrCourseCodes;
            } else {
                $this->errorMsgs = $stmt->errorInfo();
                return NULL;
            }

        } catch (Exception $e) {
            //TODO: Move to error.twig or return it in getLastError?
            print $e->getMessage() . PHP_EOL;
        }

        return NULL;
    }

    /**
     * Get data for a specific coursecode given its' ID.
     * Returns a CourseCode-object on success, NULL on failure.
     * Call GetLastError to get error-message.
     * @param int $idCourseCode Coursecode-ID of which to get data for
     * @return CourseCode|null Object of type CourseCode containing all the data for a given ID or NULL
     */
    public function getCourseCode(int $idCourseCode) : ?CourseCode {
        try{
            //Return NULL if $idCourseCode is less than 1
            if(!is_numeric($idCourseCode) || $idCourseCode < 1){
                $this->errorMsgs = array("\$idCourseCode: " . $idCourseCode . ": invalid number");
                return NULL;
            }
            
            //Prepare query and bind parameters
            // TODO: change from * to what is minimally needed to display a course
            $stmt = $this->db->prepare("select * from `CourseCode` where `idCourseCode` = :idCourseCode");
            $stmt->bindParam(":idCourseCode", $idCourseCode, PDO::PARAM_INT);

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                $result = $stmt->fetchObject("CourseCode");
                if($result){
                    return $result;
                } else {
                    $this->errorMsgs = $stmt->errorInfo();
                    return NULL;
                }
            } else {
                $this->errorMsgs = $stmt->errorInfo();
                return NULL;
            }
            
        } catch (InvalidArgumentException $e) {
            //TODO: Move to error.twig or return it in getLastError?
            print $e->getMessage() . PHP_EOL;
        }
        
        return NULL;
    }

    /**
     * Add a course code.
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param string $courseCode Coursecode to insert
     * @param string $name_nb_no Coursecode name in norwegian bokmaal
     * @param string $name_nb_nn Coursecode in norwegian nynorsk
     * @param string $name_en_gb Coursecode in british english
     * @param int $Degree_idDegree Foreign-key for which degree it applies to, must be >=1
     * @param int $StudyPoints_idStudyPoints Foreign-key for which study-points it applies to, must be >=1
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    // TODO: Add wrapper for addCourseCode, Take Object in as argument.
    public function addCourseCode(string $courseCode, string $name_nb_no, string $name_nb_nn, string $name_en_gb, int $Degree_idDegree, int $StudyPoints_idStudyPoints) : bool {
        try{
            //Check if numeric arguments are valid
            if(!is_numeric($Degree_idDegree) || $Degree_idDegree < 1){
                $this->errorMsgs = array("\$Degree_idDegree: " . $Degree_idDegree . ": invalid number");
                return false;
            }
            if(!is_numeric($StudyPoints_idStudyPoints) || $StudyPoints_idStudyPoints < 1){
                $this->errorMsgs = array("\$StudyPoints_idStudyPoints: " . $StudyPoints_idStudyPoints . ": invalid number");
                return false;
            }

            //Make sure strings are of a valid length
            $strAllowedMaxLength = 45;
            //Length of course-code string should never be larger than 8: XXX-0000
            $courseCode = substr($courseCode, 0, 8);
            $name_nb_no = substr($name_nb_no, 0, $strAllowedMaxLength);
            $name_nb_nn = substr($name_nb_nn, 0, $strAllowedMaxLength);
            $name_en_gb = substr($name_en_gb, 0, $strAllowedMaxLength);

            // TODO: Fix this query to prevent auto-increment of PK on unsuccessful insert or manually check if $courseCode already exists in the table
            $stmt = $this->db->prepare("INSERT INTO `CourseCode` (`idCourseCode`, `courseCode`, `name_nb_no`, `name_nb_nn`, `name_en_gb`, `Degree_idDegree`, `StudyPoints_idStudyPoints`)
                                                  VALUES (DEFAULT, :courseCode, :name_nb_no, :name_nb_nn, :name_en_gb, :Degree_idDegree, :StudyPoints_idStudyPoints); COMMIT;");

            $stmt->bindParam(":courseCode", $courseCode, PDO::PARAM_STR);
            $stmt->bindParam(":name_nb_no", $name_nb_no, PDO::PARAM_STR);
            $stmt->bindParam(":name_nb_nn", $name_nb_nn, PDO::PARAM_STR);
            $stmt->bindParam(":name_en_gb", $name_en_gb, PDO::PARAM_STR);
            $stmt->bindParam(":Degree_idDegree", $Degree_idDegree, PDO::PARAM_INT);
            $stmt->bindParam(":StudyPoints_idStudyPoints", $StudyPoints_idStudyPoints, PDO::PARAM_INT);

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                return true;
            } else {
                $this->errorMsgs = $stmt->errorInfo();
                return false;
            }
            
        } catch (InvalidArgumentException $e) {
            //TODO: Move to error.twig or return it in getLastError?
            print $e->getMessage() . PHP_EOL;
        }
        
        return false;
    }

    public function mergeCourseCode(CourseCode $CourseCode1, CourseCode $CourseCode2, String $newCourseCodeCode) : bool {

        // $courseCodeMerged = $CourseCode1->getCourseCode() . " + " . $CourseCode2->getCourseCode();
        $courseCodeMerged = $newCourseCodeCode;
        $name_nb_no_Merged = $CourseCode1->getNameNbNo() . "/" . $CourseCode2->getNameNbNo();
        $name_nb_nn_Merged = $CourseCode1->getNameNbNn() . "/" . $CourseCode2->getNameNbNn();
        $name_en_gb_Merged = $CourseCode1->getNameEnGb() . "/" . $CourseCode2->getNameEnGb();

        $Degree_idDegree_Merged = 0;
        if ($CourseCode1->getDegreeIdDegree() <= $CourseCode2->getDegreeIdDegree()) {
            $Degree_idDegree_Merged = $CourseCode2->getDegreeIdDegree();
        } else $Degree_idDegree_Merged = $CourseCode1->getDegreeIdDegree();

        $StudyPoints_idStudyPoints_Merged = $CourseCode1->getStudyPointsIdStudyPoints() + $CourseCode2->getStudyPointsIdStudyPoints();

        try{

            // TODO: Fix this query to prevent auto-increment of PK on unsuccessful insert or manually check if $courseCode already exists in the table
            $stmt = $this->db->prepare("INSERT INTO `CourseCode` (`idCourseCode`, `courseCode`, `name_nb_no`, `name_nb_nn`, `name_en_gb`, `Degree_idDegree`, `StudyPoints_idStudyPoints`)
                                                  VALUES (DEFAULT, :courseCode, :name_nb_no, :name_nb_nn, :name_en_gb, :Degree_idDegree, :StudyPoints_idStudyPoints); COMMIT;");

            $stmt->bindParam(":courseCode", $courseCodeMerged, PDO::PARAM_STR);
            $stmt->bindParam(":name_nb_no", $name_nb_no_Merged, PDO::PARAM_STR);
            $stmt->bindParam(":name_nb_nn", $name_nb_nn_Merged, PDO::PARAM_STR);
            $stmt->bindParam(":name_en_gb", $name_en_gb_Merged, PDO::PARAM_STR);
            $stmt->bindParam(":Degree_idDegree", $Degree_idDegree_Merged, PDO::PARAM_INT);
            $stmt->bindParam(":StudyPoints_idStudyPoints", $StudyPoints_idStudyPoints_Merged, PDO::PARAM_INT);

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                return true;
            } else {
                $this->errorMsgs = $stmt->errorInfo();
                return false;
            }

        } catch (InvalidArgumentException $e) {
            //TODO: Move to error.twig or return it in getLastError?
            print $e->getMessage() . PHP_EOL;
        }

        return false;
    }

    /**
     * Delete a course code. You probably shouldn't use this...
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param int $idCourseCode ID of coursecode to delete
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function deleteCourseCode(int $idCourseCode) : bool {
        try{
            //Return NULL if $idCourseCode is less than 1
            if(!is_numeric($idCourseCode) || $idCourseCode < 1){
                $this->errorMsgs = array("\$idCourseCode: " . $idCourseCode . ": invalid number");
                return NULL;
            }

            //Prepare query and bind parameters
            // TODO: Check if there are eny studypoints to delete before trying to delete, as an "empty" delete will still succeed
            $stmt = $this->db->prepare("DELETE FROM `CourseCode` WHERE `CourseCode`.`idCourseCode` = :idCourseCode");
            $stmt->bindParam(":idCourseCode", $idCourseCode, PDO::PARAM_INT);

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                return true;
            } else {
                $this->errorMsgs = $stmt->errorInfo();
                return false;
            }

        } catch (InvalidArgumentException $e) {
            //TODO: Move to error.twig or return it in getLastError?
            print $e->getMessage() . PHP_EOL;
        }

        return false;
    }

    /**
     * Returns array $errorMsgs
     * @return array Array containing the last generated error-message
     */
    public function getLastError() : array {
        // TODO: Format array as a single string
        return $this->errorMsgs;
    }
}