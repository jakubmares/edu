<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of CourseVideoMan
 *
 * @author jakubmares
 */
class CourseVideoManager extends BaseManager {

	const TABLE_NAME = 'course_video',
			COLUMN_COURSE_ID = 'course_id',
			COLUMN_ID = 'id',
			COLUMN_ACTIVE = 'active',
			COLUMN_VIDEO = 'video';

	public function getActiveByCourseId($id) {
		$selection = $this->table()
				->where(self::COLUMN_COURSE_ID, $id)
				->where(self::COLUMN_ACTIVE, true);
		return $this->createSmartObjects($selection);
	}
	
	public function insertVideo($values){
		$values->offsetSet(self::COLUMN_VIDEO, \Extensions\VideoHelper::cutYoutubeCode($values->offsetGet(self::COLUMN_VIDEO)));
		return $this->insert($values);
	}

	public function update($id, \Nette\Utils\ArrayHash $values) {
		$values->offsetSet(self::COLUMN_VIDEO, $this->cutCodeFromVideoLink($values->offsetGet(self::COLUMN_VIDEO)));
		return parent::update($id, $values);
	}
	
	public function deleteByCourseId($id){
		return $this->table()->where(self::COLUMN_COURSE_ID,$id)->delete();
	}

}
