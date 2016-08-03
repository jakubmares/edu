<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Po;
use Extensions;
/**
 * Description of Focus
 *
 * @author jakubmares
 */
class CourseFocus extends BasePo{
	
	const ATR_ID = 'id';

	public $course_id;
	public $focus_id;
	
	public function __construct($courseId ,  Extensions\ESimpleXmlElement $xFocus) {
		$this->focus_id = $xFocus->getAttribute(self::ATR_ID);
		$this->course_id = $courseId;
	}

}
