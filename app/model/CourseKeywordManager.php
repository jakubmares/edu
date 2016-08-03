<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of KeywordManager
 *
 * @author jakubmares
 */
class CourseKeywordManager extends BaseManager {

	const
			TABLE_NAME = 'course_keyword',
			COLUMN_ID = 'id',
			COLUMN_KEYWORD = 'keyword',
			COLUMN_COURSE_ID = 'course_id',
			KEYWORD_LENGTH = 100;

	public function deleteCourseKeywords($courseId) {
		return $this->table()->where(self::COLUMN_COURSE_ID, $courseId)->delete();
	}

	public function insertCourseKeywords($courseId, $keywords) {
		if (count($keywords) > 0) {
			$data = [];
			foreach ($keywords as $keyword) {
				$data[] = [self::COLUMN_COURSE_ID => $courseId, self::COLUMN_KEYWORD => substr($keyword, 0, self::KEYWORD_LENGTH)];
			}

			$this->table()->insert($data);
		}
	}

}
