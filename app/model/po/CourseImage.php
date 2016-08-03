<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Po;

use Extensions\ESimpleXmlElement;

/**
 * Description of CourseImage
 *
 * @author jakubmares
 */
class CourseImage extends BasePo {

	const ATR_SRC = 'src';

	public $img;
	public $active;
	public $course_id;

	public function __construct($courseId, ESimpleXmlElement $xImage) {
		$this->img = $xImage->getAttribute(self::ATR_SRC);
		$this->active = true;
		$this->course_id = $courseId;
	}

}
