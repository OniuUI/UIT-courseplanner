<?php

class GradeScale{

    private int $idGradeScale;
    private string $gradeScale;

    /**
     * GradeScale constructor.
     */
    public function __construct(){}

    /**
     * @return int
     */
    public function getIdGradeScale(): int {
        return $this->idGradeScale;
    }

    /**
     * @param int $idGradeScale
     */
    public function setIdGradeScale(int $idGradeScale): void {
        $this->idGradeScale = $idGradeScale;
    }

    /**
     * @return string
     */
    public function getGradeScale(): string {
        return $this->gradeScale;
    }

    /**
     * @param string $gradeScale
     */
    public function setGradeScale(string $gradeScale): void {
        $this->gradeScale = $gradeScale;
    }

}