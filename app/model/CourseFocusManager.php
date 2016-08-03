<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of CourseFocus
 *
 * @author jakubmares
 */
class CourseFocusManager extends BaseManager {

	const
			TABLE_NAME = 'course_focus',
			COLUMN_COURSE_ID = 'course_id',
			COLUMN_FOCUS_ID = 'focus_id',
			FOCUSES_PER_COURSE = '2';
	
	public function deleteByCourseId($id) {
		return $this->table()->where(self::COLUMN_COURSE_ID, $id)->delete();
	}

}
