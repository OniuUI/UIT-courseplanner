<?php
// TODO: Make class "generic" (pass parameter to constructor) to denote which
class AcademicContentService{
    private PDO $db;
    private array $errorMsgs;

    /**
     * DegreeService.class constructor.
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
     * Get all available AcademicContent-entries.
     * Returns an array with AcademicContent-objects of all available AcademicContent-entries. Returns NULL if it fails.
     * Call GetLastError to get error-message.
     * @return array|null Array Containing AcademicContent-objects or NULL
     */
    public function getAllEntries() : ?array {
        $arrEntries = array();
        try {
            // No userdata used, no real need to sanitize...
            $stmt = $this->db->prepare("select * from `AcademicContent` order by `AcademicContent`.`idAcademicContent`;");

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                //Fetch Degree-objects from query-results
                while ($entry = $stmt->fetchObject("AcademicContent")) {
                    $arrEntries[] = $entry;
                }
                return $arrEntries;
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
     * Get the latest AcademicContent-entry for a given CourseDescription-ID
     * Returns the latest AcademicContent-object for a given CourseDescription-ID
     * Call GetLastError to get error-message.
     * @param int $idCourseDescription CourseDescription-ID to fetch the latest entry for
     * @return AcademicContent|null AcademicContent-object for the given CourseDescription-ID
     */
    public function getLastEntryForCourseDescription(int $idCourseDescription) : ?AcademicContent {
        $entries = $this->getAllEntriesForCourseDescription($idCourseDescription);
        if(!is_null($entries)){
            return reset($entries);
        }
        return null;
    }

    /**
     * Get all AcademicContent-entries for a given CourseDescription-ID
     * Returns an array containing AcademicContent-objects for a given CourseDescription-ID
     * Call GetLastError to get error-message.
     * @param int $idCourseDescription CourseDescription-id to fetch entries for
     * @return array|null Array of All relevant AcademicContent-objects for the given CourseDescription-ID
     */
    public function getAllEntriesForCourseDescription(int $idCourseDescription) : ?array {
        $arrEntries = array();
        try {
            //Return NULL if $idCourseCode is less than 1
            if(!is_numeric($idCourseDescription) || $idCourseDescription < 1){
                $this->errorMsgs = array("\$idCourseDescription: " . $idCourseDescription . ": invalid number");
                return NULL;
            }
            $stmt = $this->db->prepare("select `AcademicContent`.`idAcademicContent`, `AcademicContent`.`academicContent`, `AcademicContent`.`dateCreated` 
                                                    from `AcademicContent` left join `AcademicContent_has_CourseDescription` on `AcademicContent_has_CourseDescription`.`AcademicContent_idAcademicContent` = `AcademicContent`.`idAcademicContent`
                                                    where `AcademicContent_has_CourseDescription`.`CourseDescription_idCourse` = :idCourseDescription 
                                                    order by `AcademicContent`.`dateCreated` desc;");

            $stmt->bindParam(":idCourseDescription", $idCourseDescription, PDO::PARAM_INT);
            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                //Fetch Degree-objects from query-results
                while ($entry = $stmt->fetchObject("AcademicContent")) {
                    $arrEntries[] = $entry;
                }
                return $arrEntries;
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
     * Get the latest AcademicContent-entry for a given CourseCode-ID
     * Returns the latest AcademicContent-object for a given CourseCode-ID
     * Call GetLastError to get error-message.
     * @param int $idCourseCode CourseCode-id to fetch the latest entry for
     * @return AcademicContent|null AcademicContent-object for the given CourseCode-ID
     */
    public function getLastEntryForCourseCode(int $idCourseCode) : ?AcademicContent {
        $entries = $this->getAllEntriesForCourseCode($idCourseCode);
        if(!is_null($entries)){
            return reset($entries);
        }
        return null;
    }

    /**
     * Get all AcademicContent-entries for a given CourseCode-ID
     * Returns an array containing AcademicContent-objects for a given CourseCode-ID
     * Call GetLastError to get error-message.
     * @param int $idCourseCode CourseCode-id to fetch entries for
     * @return array|null Array of All relevant AcademicContent-objects for the given CourseCode-ID
     */
    public function getAllEntriesForCourseCode(int $idCourseCode) : ?array {
        $arrEntries = array();
        try {
            //Return NULL if $idCourseCode is less than 1
            if(!is_numeric($idCourseCode) || $idCourseCode < 1){
                $this->errorMsgs = array("\$idCourseCode: " . $idCourseCode . ": invalid number");
                return NULL;
            }

            $stmt = $this->db->prepare("select `AC`.`idAcademicContent`, `AC`.`academicContent`, `AC`.`dateCreated`
                                                    from `AcademicContent` as `AC`
                                                        left join `AcademicContent_has_CourseDescription` as `AC_has_CD`
                                                        on `AC_has_CD`.`AcademicContent_idAcademicContent` = `AC`.`idAcademicContent`
                                                    where `AC_has_CD`.`CourseDescription_idCourse`
                                                    in (
                                                        select `CC_has_CD`.`CourseDescription_idCourse`
                                                        from `CourseCode_has_CourseDescription` as `CC_has_CD`
                                                        where `CC_has_CD`.`CourseCode_idCourseCode` = :idCourseCode
                                                    )
                                                    group by `AC`.`idAcademicContent`, `AC`.`academicContent`, `AC`.`dateCreated`
                                                    order by `AC`.`dateCreated` desc;");

            $stmt->bindValue(":idCourseCode", $idCourseCode, PDO::PARAM_INT);

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                //Fetch Degree-objects from query-results
                while ($entry = $stmt->fetchObject("AcademicContent")) {
                    $arrEntries[] = $entry;
                }
                return $arrEntries;
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
     * Get data for the latest AcademicContent-entry.
     * Returns a AcademicContent-object on success, NULL on failure.
     * Call GetLastError to get error-message.
     * @return AcademicContent|null Object of type AcademicContent containing all the data for for the latest entry
     */
    public function getLastEntry() : ?AcademicContent {
        try{
            //Prepare query and bind parameters
            $stmt = $this->db->prepare("select * from `AcademicContent` order by `AcademicContent`.`dateCreated` desc");

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                $result =  $stmt->fetchObject("AcademicContent");
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
     * Get data for a specific AcademicContent-entry given its' ID.
     * Returns a AcademicContent-object on success, NULL on failure.
     * Call GetLastError to get error-message.
     * @param int $idAcademicContent AcademicContent-ID of which to get data for
     * @return AcademicContent|null Object of type AcademicContent containing all the data for a given ID or NULL
     */
    public function getEntry(int $idAcademicContent) : ?AcademicContent {
        try{
            //Return NULL if $idCourseCode is less than 1
            if(!is_numeric($idAcademicContent) || $idAcademicContent < 1){
                $this->errorMsgs = array("\$idAcademicContent: " . $idAcademicContent . ": invalid number");
                return NULL;
            }

            //Prepare query and bind parameters
            $stmt = $this->db->prepare("select * from `AcademicContent` where `idAcademicContent` = :idAcademicContent");
            $stmt->bindParam(":idAcademicContent", $idAcademicContent, PDO::PARAM_INT);

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                $result =  $stmt->fetchObject("AcademicContent");
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
     * Add an academicContent-entry and associated entry in connecting table
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param int $idCourseDecription Id of course-description the entry belongs to
     * @param string $academicContent string-entry to insert
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function addEntry(int $idCourseDecription, string $academicContent) : bool {
        try{
            if(!is_numeric($idCourseDecription) || $idCourseDecription < 1){
                $this->errorMsgs = array("\$idCourseDecription: " . $idCourseDecription . ": invalid number");
                return NULL;
            }

            //Make sure $degree is of a valid length
            $academicContent = substr($academicContent, 0, 300);

            $this->db->beginTransaction();
            // TODO: Fix this query to prevent auto-increment of PK on unsuccessful insert or manually check if $degree already exists in the table
            $stmt = $this->db->prepare("INSERT INTO `AcademicContent` (`idAcademicContent`, `academicContent`, `dateCreated` ) VALUES (DEFAULT, :academicContent, DEFAULT);");

            $stmt->bindParam(":academicContent", $academicContent, PDO::PARAM_STR);

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                $idAcademicContent = $this->db->lastInsertId();
                //close current query before proceeding to the next
                $stmt->closeCursor();

                $stmtConnectingTable = $this->db->prepare("INSERT INTO `AcademicContent_has_CourseDescription` (`AcademicContent_idAcademicContent`, `CourseDescription_idCourse`) VALUES (:AcademicContent_idAcademicContent, :CourseDescription_idCourse);");
                $stmtConnectingTable->bindParam(":AcademicContent_idAcademicContent", $idAcademicContent, PDO::PARAM_INT);
                $stmtConnectingTable->bindParam(":CourseDescription_idCourse", $idCourseDecription, PDO::PARAM_INT);

                //Execute query, and set return-status + any potential error-message
                if($stmtConnectingTable->execute()){
                    $this->db->commit();
                    return true;
                } else {
                    $this->errorMsgs = $stmtConnectingTable->errorInfo();
                    var_dump($this->errorMsgs);
                    $this->errorMsgs[] = $this->db->rollBack()?"Database-write failed. Database rollback succeeded.":"Fatal error! Could not rollback failed database-write!";
                    return false;
                }

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
     * Delete a AcademicContent-entry and all entries associated with it in the connecting table.
     * You probably shouldn't use this...
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param int $idAcademicContent ID of AcademicContent-entry to delete
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function deleteEntry(int $idAcademicContent) : bool {
        try{

            //Return NULL if $idAcademicContent is less than 1
            if(!is_numeric($idAcademicContent) || $idAcademicContent < 1){
                $this->errorMsgs = array("\$idAcademicContent: " . $idAcademicContent . ": invalid number");
                return NULL;
            }

            $this->db->beginTransaction();

            //Prepare query and bind parameters
            // TODO: Check if there are any entry to delete before trying to delete, as an "empty" delete will still succeed
            $stmtConnectingTable = $this->db->prepare( "DELETE FROM `AcademicContent_has_CourseDescription` WHERE `AcademicContent_has_CourseDescription`.`AcademicContent_idAcademicContent` = :idAcademicContent;");
            $stmtConnectingTable->bindParam(":idAcademicContent", $idAcademicContent, PDO::PARAM_INT);

            if($stmtConnectingTable->execute()){
                //close current query,
                $stmtConnectingTable->closeCursor();

                $stmt = $this->db->prepare("DELETE FROM `AcademicContent` WHERE `AcademicContent`.`idAcademicContent` = :idAcademicContent;");
                $stmt->bindParam(":idAcademicContent", $idAcademicContent, PDO::PARAM_INT);

                //Execute query, and set return-status + any potential error-message
                if($stmt->execute()){
                    $this->db->commit();
                    return true;
                } else {
                    $this->errorMsgs = $stmt->errorInfo();
                    $this->errorMsgs[] = $this->db->rollBack()?"Database-write failed. Database rollback succeeded.":"Fatal error! Could not rollback failed database-write!";
                    return false;
                }
            } else {
                $this->errorMsgs = $stmtConnectingTable->errorInfo();
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