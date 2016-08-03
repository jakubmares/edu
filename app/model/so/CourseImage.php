<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;
use App\Model;
/**
 * Description of CourseImage
 *
 * @author jakubmares
 */
class CourseImage extends SmartObject {

	private $id;
	private $courseId;
	private $active;
	private $img;

	public function getId() {
		return $this->id;
	}

	public function getCourseId() {
		return $this->courseId;
	}

	public function getActive() {
		return $this->active;
	}

	public function getImg() {
		return str_replace('company_img', 'company-img', $this->img);
	}
	


}
