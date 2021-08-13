<?php

class CourseDescriptionService{
    private PDO $db;
    private array $errorMsgs;

    /**
     * CourseDescriptionService.class constructor.
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
     * Get all available courses-descriptions. USE WITH CAUTION!!!
     * Returns an array with CourseDescription-objects of all available CourseDescriptions. Returns NULL if it fails.
     * Call GetLastError to get error-message.
     * @return array|null Array Containing CourseDescription-objects or NULL
     */
    public function getAllCourseDescriptions() : ?array {
        $arrCourseDescriptions = array();
        try {
            // No userdata used, no real need to sanitize...
            // TODO: change from * to what is minimally needed to display a coursedescription
            $stmt = $this->db->prepare("select * from `CourseDescription` order by `CourseDescription`.`idCourse`;");

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                //Fetch CourseDescription-objects from query-results
                while ($courseDescription = $stmt->fetchObject("CourseDescription")) {
                    $arrCourseDescriptions[] = $courseDescription;
                }
                return $arrCourseDescriptions;
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
     * Get data for a specific CourseDescription given its' ID.
     * Returns a CourseDescription-object on success, NULL on failure.
     * Call GetLastError to get error-message.
     * @param int $idCourse Coursecode-ID of which to get data for
     * @return CourseDescription|null Object of type CourseCode containing all the data for a given ID or NULL
     */
    public function getCourseDescription(int $idCourse) : ?CourseDescription {
        try{
            //Return NULL if $idCourseCode is less than 1
            if(!is_numeric($idCourse) || $idCourse < 1){
                $this->errorMsgs = array("\$idCourse: " . $idCourse . ": invalid number");
                return NULL;
            }

            //Prepare query and bind parameters
            // TODO: change from * to what is minimally needed to display a course-description
            $stmt = $this->db->prepare("select * from `CourseDescription` where `CourseDescription`.`idCourse` = :idCourse");
            $stmt->bindParam(":idCourse", $idCourse, PDO::PARAM_INT);

            //Execute query, and set return-status + any potential error-message
            if($stmt->execute()){
                $result =  $stmt->fetchObject("CourseDescription");
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
     * @param int $year Year for which the course applies to
     * @param bool $singleCourse Whether or not the course can be taken as a single-course
     * @param bool $continuation Can the course be continuated?
     * @param bool $semesterFall Course executed in fall-semester?
     * @param bool $semesterSpring Course executed in spring-semester?
     * @param int $archived Is the course archived?
     * @param int $CreatedBy_idUser UserID of the creator for the course-description
     * @param int $Language_idLanguage Which language the course is taught in
     * @param int $ExamType_idExamType Type of exam for the course
     * @param int $GradeScale_idGradeScale Type of gradescale used to grade the exam
     * @param int $TeachingLocation_idTeachingLocation Where this course is taught
     * @param int $ArchivedBy_idUser UserID of the user who archived it
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function addCourseDescription(int $year, bool $singleCourse, bool $continuation, bool $semesterFall, bool $semesterSpring, int $archived, int $CreatedBy_idUser, int $Language_idLanguage, int $ExamType_idExamType, int $GradeScale_idGradeScale, int $TeachingLocation_idTeachingLocation, int $ArchivedBy_idUser): bool{
        try {
            // dateCreated and dateModified should be equal when creating a course
            // TODO: Can dateCreated and dateModified be set in the query?
            $dateCreated = date("Y-m-d H:i:s");
            $dateChanged=$dateCreated;

            //Check if numeric arguments are valid
            $arrNumericalArguments = array(
                "\$year" => $year,
                "\$CreatedBy_idUser" => $CreatedBy_idUser,
                "\$Language_idLanguage" => $Language_idLanguage,
                "\$ExamType_idExamType" => $ExamType_idExamType,
                "\$GradeScale_idGradeScale" => $GradeScale_idGradeScale,
                "\$TeachingLocation_idTeachingLocation" => $TeachingLocation_idTeachingLocation,
                "\$ArchivedBy_idUser" => $ArchivedBy_idUser
            );
            foreach ($arrNumericalArguments as $key => $value) {
                if (!is_numeric($value) || $value < 1) {
                    $this->errorMsgs = array("$key: " . $value . ": invalid number");
                    return false;
                }
            }
            if (!is_numeric($archived) || $archived < 0) {
                $this->errorMsgs = array("\$archived: " . $archived . ": invalid number");
                return false;
            }

            // TODO: Fix this query to prevent auto-increment of PK on unsuccessful insert or manually check if $courseDescription already exists in the table
            //Prepare query and bind parameters
            $stmt = $this->db->prepare("INSERT INTO `CourseDescription` (`idCourse`, `year`, `dateCreated`, `dateChanged`, `singleCourse`, `continuation`, `semesterFall`, `semesterSpring`, `archived`, `CreatedBy_idUser`, `Language_idLanguage`, `ExamType_idExamType`, `GradeScale_idGradeScale`, `TeachingLocation_idTeachingLocation`, `ArchivedBy_idUser`)
                                                  VALUES (DEFAULT, :year, :dateCreated, :dateChanged, :singleCourse, :continuation, :semesterFall, :semesterSpring, :archived, :CreatedBy_idUser,:Language_idLanguage, :ExamType_idExamType, :GradeScale_idGradeScale, :TeachingLocation_idTeachingLocation, :ArchivedBy_idUser); COMMIT;");

            $stmt->bindParam(":year", $year, PDO::PARAM_INT);
            $stmt->bindParam(":dateCreated", $dateCreated, PDO::PARAM_STR);
            $stmt->bindParam(":dateChanged", $dateChanged, PDO::PARAM_STR);
            $stmt->bindParam(":singleCourse", $singleCourse, PDO::PARAM_BOOL);
            $stmt->bindParam(":continuation", $continuation, PDO::PARAM_BOOL);
            $stmt->bindParam(":semesterFall", $semesterFall, PDO::PARAM_BOOL);
            $stmt->bindParam(":semesterSpring", $semesterSpring, PDO::PARAM_BOOL);
            $stmt->bindParam(":archived", $archived, PDO::PARAM_INT);
            $stmt->bindParam(":CreatedBy_idUser", $CreatedBy_idUser, PDO::PARAM_INT);
            $stmt->bindParam(":Language_idLanguage", $Language_idLanguage, PDO::PARAM_INT);
            $stmt->bindParam(":ExamType_idExamType", $ExamType_idExamType, PDO::PARAM_INT);
            $stmt->bindParam(":GradeScale_idGradeScale", $GradeScale_idGradeScale, PDO::PARAM_INT);
            $stmt->bindParam(":TeachingLocation_idTeachingLocation", $TeachingLocation_idTeachingLocation, PDO::PARAM_INT);
            $stmt->bindParam(":ArchivedBy_idUser", $ArchivedBy_idUser, PDO::PARAM_INT);

            //Execute query, and set return-status + any potential error-message
            if ($stmt->execute()) {
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
     * Add a course code.
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param CourseDescription $courseDescription CourseDescription-object to add
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function addCourseDescriptionByObject(?CourseDescription $courseDescription): bool {

        if(is_null($courseDescription)){
            $this->errorMsgs = array("\$courseDescription was NULL");
            return false;
        }

        return $this::addCourseDescription(
            $courseDescription->getYear(),
            $courseDescription->isSingleCourse(),
            $courseDescription->isContinuation(),
            $courseDescription->isSemesterFall(),
            $courseDescription->isSemesterSpring(),
            $courseDescription->getArchived(),
            $courseDescription->getCreatedByIdUser(),
            $courseDescription->getLanguageIdLanguage(),
            $courseDescription->getExamTypeIdExamType(),
            $courseDescription->getGradeScaleIdGradeScale(),
            $courseDescription->getTeachingLocationIdTeachingLocation(),
            $courseDescription->getArchivedByIdUser());
    }

    /**
     * Copy a course code to a new year.
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param int $idCourse Course to copy
     * @param int $idUser User performing the copy, wil be set as course-creator
     * @param int $year Year for which the course wil apply
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function copyCourseToNewYear(int $idCourse, int $idUser, int $year) : bool{
        if (!is_numeric($idCourse) || $idCourse < 1) {
            $this->errorMsgs = array("\$idCourse: " . $idCourse . ": invalid number");
            return false;
        }
        if (!is_numeric($idUser) || $idUser < 1) {
            $this->errorMsgs = array("\$idUser: " . $idUser . ": invalid number");
            return false;
        }
        if (!is_numeric($year) || $year < 1) {
            $this->errorMsgs = array("\$year: " . $year . ": invalid number");
            return false;
        }
        $courseDescription = $this->getCourseDescription($idCourse);

        $courseDescription->setYear($year);
        $courseDescription->setCreatedByIdUser($idUser);
        $courseDescription->setArchived(CourseDescription::ARCHIVED_FALSE);

        if($this->archiveCourse($idUser, $idCourse)){
            return $this->addCourseDescriptionByObject($courseDescription);
        }
        return false;
    }

    /**
     * Update a CourseDescription-entry.
     * Creates a new CourseDescription entry with updated values, Archives previous instance with status 2 (Archived, updated)
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param int $idUser ID of user that updated the course
     * @param int $idCourse ID of course to be updated
     * @param int $year Year for which the course applies to
     * @param bool $singleCourse Whether or not the course can be taken as a single-course
     * @param bool $continuation Can the course be continuated?
     * @param bool $semesterFall Course executed in fall-semester?
     * @param bool $semesterSpring Course executed in spring-semester?
     * @param int $Language_idLanguage Which language the course is taught in
     * @param int $ExamType_idExamType Type of exam for the course
     * @param int $GradeScale_idGradeScale Type of gradescale used to grade the exam
     * @param int $TeachingLocation_idTeachingLocation Where this course is taught
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    // TODO: Make more generic(eg. if no argument(NULL) supplied, use old value), add wrapper for copying to new year
    public function updateCourseDescription(int $idUser, int $idCourse, int $year, bool $singleCourse, bool $continuation, bool $semesterFall, bool $semesterSpring,  int $Language_idLanguage, int $ExamType_idExamType, int $GradeScale_idGradeScale, int $TeachingLocation_idTeachingLocation) : bool {
        try{

            $courseDescription = $this::getCourseDescription($idCourse);
            if(is_null($courseDescription)){
                return false;
            }

            $courseDescription->setYear($year);
            $courseDescription->setDateChanged(date("Y-m-d H:i:s"));
            $courseDescription->setSingleCourse($singleCourse);
            $courseDescription->setContinuation($continuation);
            $courseDescription->setSemesterFall($semesterFall);
            $courseDescription->setSemesterSpring($semesterSpring);
            $courseDescription->setArchived(CourseDescription::ARCHIVED_UPDATED);
            $courseDescription->setLanguageIdLanguage($Language_idLanguage);
            $courseDescription->setExamTypeIdExamType($ExamType_idExamType);
            $courseDescription->setGradeScaleIdGradeScale($GradeScale_idGradeScale);
            $courseDescription->setTeachingLocationIdTeachingLocation($TeachingLocation_idTeachingLocation);
            $courseDescription->setArchivedByIdUser($idUser);

            return $this::addCourseDescriptionByObject($courseDescription);

        } catch (Exception $e) {
            //TODO: Move to error.twig or return it in getLastError?
            print $e->getMessage() . PHP_EOL;
        }

        return false;
    }

    /**
     * Archives the selected CourseDescription with status $archiveStatus
     * @param int $idUser ID of the user setting the archive-status
     * @param int $idCourse ID of the CourseDescription to be archived
     * @param int $archiveStatus Status to set for archive status
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    private function setCourseDescriptionArchiveStatus(int $idUser, int $idCourse, int $archiveStatus) : bool {
        try{
            if (!is_numeric($idUser) || $idUser < 0) {
                $this->errorMsgs = array("\$idUser: " . $idUser . ": invalid number");
                return false;
            }
            if (!is_numeric($idCourse) || $idCourse < 0) {
                $this->errorMsgs = array("\$idCourse: " . $idCourse . ": invalid number");
                return false;
            }
            if (!is_numeric($archiveStatus) || $archiveStatus < 0) {
                $this->errorMsgs = array("\$archiveStatus: " . $archiveStatus . ": invalid number");
                return false;
            }

            $stmt = $this->db->prepare("UPDATE `CourseDescription`
                            SET `archived` = :archived,
                                `ArchivedBy_idUser` = :ArchivedBy_idUser
                            WHERE `CourseDescription`.`idCourse` = :idCourse; COMMIT;");

            $stmt->bindParam(":idCourse", $idCourse, PDO::PARAM_INT);
            $stmt->bindParam(":archived", $archiveStatus, PDO::PARAM_INT);
            $stmt->bindParam(":ArchivedBy_idUser", $idUser, PDO::PARAM_INT);

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
     * Archives the selected CourseDescription
     * @param int $idUser ID of the user setting the archive-status
     * @param int $idCourse ID of the CourseDescription to be archived
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function archiveCourse(int $idUser, int $idCourse) : bool {
        return $this->setCourseDescriptionArchiveStatus($idUser, $idCourse, CourseDescription::ARCHIVED_TRUE);
    }

    /**
     * Un-archives the selected CourseDescription
     * @param int $idUser ID of the user setting the un-Archived-status
     * @param int $idCourse ID of the CourseDescription to be un-archived
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function unArchiveCourse(int $idUser, int $idCourse) : bool {
        return $this->setCourseDescriptionArchiveStatus($idUser, $idCourse, CourseDescription::ARCHIVED_FALSE);
    }

    /**
     * Update a coursecode-entry directly. You are probably looking for updateCourseDescription(), which archives the old entry...
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param int $idCourse ID of course to update
     * @param int $year Year for which the course applies to
     * @param bool $singleCourse Whether or not the course can be taken as a single-course
     * @param bool $continuation Can the course be continuated?
     * @param bool $semesterFall Course executed in fall-semester?
     * @param bool $semesterSpring Course executed in spring-semester?
     * @param int $archived Is the course archived?
     * @param int $Language_idLanguage Which language the course is taught in
     * @param int $ExamType_idExamType Type of exam for the course
     * @param int $GradeScale_idGradeScale Type of gradescale used to grade the exam
     * @param int $TeachingLocation_idTeachingLocation Where this course is taught
     * @param int $ArchivedBy_idUser ID of the user setting the archive-status
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function updateCourseDescriptionEntry(int $idCourse, int $year, bool $singleCourse, bool $continuation, bool $semesterFall, bool $semesterSpring, int $archived, int $Language_idLanguage, int $ExamType_idExamType, int $GradeScale_idGradeScale, int $TeachingLocation_idTeachingLocation, int $ArchivedBy_idUser) : bool {
        try{

            // TODO: Can dateChanged be set in the query?
            $dateChanged = date("Y-m-d H:i:s");

            //Check if numeric arguments are valid
            $arrNumericalArguments = array(
                "\$idCourse" => $idCourse,
                "\$year" => $year,
                "\$Language_idLanguage" => $Language_idLanguage,
                "\$ExamType_idExamType" => $ExamType_idExamType,
                "\$GradeScale_idGradeScale" => $GradeScale_idGradeScale,
                "\$TeachingLocation_idTeachingLocation" => $TeachingLocation_idTeachingLocation,
                "\$ArchivedBy_idUser" => $ArchivedBy_idUser
            );
            foreach ($arrNumericalArguments as $key => $value) {
                if (!is_numeric($value) || $value < 1) {
                    $this->errorMsgs = array("$key: " . $value . ": invalid number");
                    return false;
                }
            }
            if (!is_numeric($archived) || $archived < 0) {
                $this->errorMsgs = array("\$archived: " . $archived . ": invalid number");
                return false;
            }

            // TODO: Fix this query to prevent auto-increment of PK on unsuccessful insert or manually check if $courseCode already exists in the table
            $stmt = $this->db->prepare("UPDATE `CourseDescription`
                            SET `year` = :year,
                                `dateChanged` = :dateChanged,
                                `singleCourse` = :singleCourse,
                                `continuation` = :continuation,
                                `semesterFall` = :semesterFall,
                                `semesterSpring` = :semesterSpring,
                                `archived` = :archived,
                                `Language_idLanguage` = :Language_idLanguage,
                                `ExamType_idExamType` = :ExamType_idExamType,
                                `GradeScale_idGradeScale` = :GradeScale_idGradeScale,
                                `TeachingLocation_idTeachingLocation` = :TeachingLocation_idTeachingLocation,
                                `ArchivedBy_idUser` = :ArchivedBy_idUser
                            WHERE `CourseDescription`.`idCourse` = :idCourse; COMMIT;");

            $stmt->bindParam(":idCourse", $idCourse, PDO::PARAM_INT);
            $stmt->bindParam(":year", $year, PDO::PARAM_INT);
            $stmt->bindParam(":dateChanged", $dateChanged, PDO::PARAM_STR);
            $stmt->bindParam(":singleCourse", $singleCourse, PDO::PARAM_BOOL);
            $stmt->bindParam(":continuation", $continuation, PDO::PARAM_BOOL);
            $stmt->bindParam(":semesterFall", $semesterFall, PDO::PARAM_BOOL);
            $stmt->bindParam(":semesterSpring", $semesterSpring, PDO::PARAM_BOOL);
            $stmt->bindParam(":archived", $archived, PDO::PARAM_INT);
            $stmt->bindParam(":Language_idLanguage", $Language_idLanguage, PDO::PARAM_INT);
            $stmt->bindParam(":ExamType_idExamType", $ExamType_idExamType, PDO::PARAM_INT);
            $stmt->bindParam(":GradeScale_idGradeScale", $GradeScale_idGradeScale, PDO::PARAM_INT);
            $stmt->bindParam(":TeachingLocation_idTeachingLocation", $TeachingLocation_idTeachingLocation, PDO::PARAM_INT);
            $stmt->bindParam(":ArchivedBy_idUser", $ArchivedBy_idUser, PDO::PARAM_INT);

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
     * Delete a CourseDescription. You probably shouldn't use this...
     * Returns true if successful, false if failed.
     * Call GetLastError to get error-message.
     * @param int $idCourse ID of coursecode to delete
     * @return bool True if succeeded, false if failed. In case of false, call GetLastError to get last error-message
     */
    public function deleteCourseCode(int $idCourse) : bool {
        try{
            //Return NULL if $idCourseCode is less than 1
            if(!is_numeric($idCourse) || $idCourse < 1){
                $this->errorMsgs = array("\$idCourseCode: " . $idCourse . ": invalid number");
                return NULL;
            }

            //Prepare query and bind parameters
            // TODO: Check if there are eny studypoints to delete before trying to delete, as an "empty" delete will still succeed
            $stmt = $this->db->prepare("DELETE FROM `CourseDescription` WHERE `CourseDescription`.`idCourse` = :idCourse");
            $stmt->bindParam(":idCourse", $idCourse, PDO::PARAM_INT);

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