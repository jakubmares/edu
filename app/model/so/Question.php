<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;
use App\Model;
/**
 * Description of Question
 *
 * @author jakubmares
 */
class Question extends SmartObject {

	private $id;
	private $name;
	private $email;
	private $question;
	private $createdAt;
	private $courseName;
	private $sentTo;
	private $companyName;
	private $courseId;

	public function getId() {
		return $this->id;
	}
	
	public function getCourseId() {
		return $this->courseId;
	}

	public function getName() {
		return $this->name;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getQuestion() {
		return $this->question;
	}
	
	public function getCreatedAt() {
		return $this->createdAt;
	}

	public function getCourseName() {
		return $this->courseName;
	}

	public function getSentTo() {
		return $this->sentTo;
	}

	public function getCompanyName() {
		return $this->companyName;
	}
	
	/**
	 * 
	 * @return Course
	 */
	public function getCourse(){
		return $this->getRef(Model\CourseManager::TABLE_NAME);
	}


}
