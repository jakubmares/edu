<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of CourseVideo
 *
 * @author jakubmares
 */
class CourseVideo extends SmartObject {

	private $id;
	private $courseId;
	private $active;
	private $video;

	public function getId() {
		return $this->id;
	}

	public function getCourseId() {
		return $this->courseId;
	}

	public function getActive() {
		return $this->active;
	}

	public function getVideo() {
		return $this->video;
	}

}
