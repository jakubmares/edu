<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Po;
use Extensions\ESimpleXmlElement;
/**
 * Description of CourseLevel
 *
 * @author jakubmares
 */
class CourseLevel extends BasePo{
	const ATR_ID = 'id';
	public $course_id;
	public $level_id;
	
	public function __construct($course_id,  ESimpleXmlElement $xLevel) {
		$this->course_id = $course_id;
		$this->level_id = $xLevel->getAttribute(self::ATR_ID);
	}

}
