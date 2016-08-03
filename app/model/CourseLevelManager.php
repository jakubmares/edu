<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of CourseLevelManager
 *
 * @author jakubmares
 */
class CourseLevelManager extends BaseManager{
	const TABLE_NAME = 'course_level',
			COLUMN_COURSE_ID = 'course_id',
			COLUMN_LEVEL_ID = 'level_id';
	
	public function deleteByCourseId($id){
		return $this->table()->where(self::COLUMN_COURSE_ID,$id)->delete();
	}
	
	public function insertByCourseId($levelsId,$courseId){
		$data = [];
		foreach ($levelsId as $levelId) {
			$data[] = [self::COLUMN_COURSE_ID=>$courseId,  self::COLUMN_LEVEL_ID=>$levelId];
		}
		return $this->insert($data);
	}
}
