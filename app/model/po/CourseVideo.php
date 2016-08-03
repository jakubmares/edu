<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Po;
use Extensions\ESimpleXmlElement;

/**
 * Description of CourseVideo
 *
 * @author jakubmares
 */
class CourseVideo extends BasePo{
	const ATR_SRC = 'src';
	
	public $video;
	public $active;
	public $courseId;
	
	public function __construct($courseId,ESimpleXmlElement $xVideo){
		$this->video = \Extensions\VideoHelper::cutYoutubeCode($xVideo->getAttribute(self::ATR_SRC));
		$this->active = true;
		$this->courseId = $courseId;
	}
}
