<?php

class CourseCoordinator{

    private int $idCourseCoordinator;
    private string $courseCoordinator;

    /**
     * CourseCoordinator constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getIdCourseCoordinator(): int {
        return $this->idCourseCoordinator;
    }

    /**
     * @param int $idCourseCoordinator
     */
    public function setIdCourseCoordinator(int $idCourseCoordinator): void {
        $this->idCourseCoordinator = $idCourseCoordinator;
    }

    /**
     * @return string
     */
    public function getCourseCoordinator(): string {
        return $this->courseCoordinator;
    }

    /**
     * @param string $courseCoordinator
     */
    public function setCourseCoordinator(string $courseCoordinator): void {
        $this->courseCoordinator = $courseCoordinator;
    }


}