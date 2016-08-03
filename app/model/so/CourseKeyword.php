<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of CourseKeyword
 *
 * @author jakubmares
 */
class CourseKeyword extends SmartObject {

	private $id;
	private $keyword;
	private $courseId;

	public function getId() {
		return $this->id;
	}

	public function getKeyword() {
		return $this->keyword;
	}

	public function getCourseId() {
		return $this->courseId;
	}

}
