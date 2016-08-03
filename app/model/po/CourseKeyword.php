<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Po;

/**
 * Description of CourseKeyword
 *
 * @author jakubmares
 */
class CourseKeyword extends BasePo{
	public $keyword;
	public $course_id;
	public function __construct($course_id,  \Extensions\ESimpleXmlElement $xKeyword) {
		$this->course_id = $course_id;
		$this->keyword = $xKeyword->getContent();
	}

}
