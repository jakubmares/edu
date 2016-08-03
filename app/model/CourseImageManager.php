<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of CourseImageMan
 *
 * @author jakubmares
 */
class CourseImageManager extends BaseManager {

	const TABLE_NAME = 'course_image',
			COLUMN_COURSE_ID = 'course_id',
			COLUMN_ID = 'id',
			COLUMN_ACTIVE = 'active',
			COLUMN_IMG = 'img',
			//IMAGE SETTINGS
			IMAGE_WIDTH = '540',
			IMAGE_HEIGHT = '405',
			IMAGE_PATH = '/images/course',
			IMAGE_PREFIX = 'img-';

	/**
	 * 
	 * @param type $id
	 * @return So\CourseImage[]
	 */
	public function getActiveByCourseId($id) {
		$selection = $this->table()
				->where(self::COLUMN_COURSE_ID, $id)
				->where(self::COLUMN_ACTIVE, true);
		$ret = $this->createSmartObjects($selection);
		return $ret;
	}

	/**
	 * 
	 * @param type $id
	 * @return So\CourseImage[]
	 */
	public function getByCourseId($id) {
		$selection = $this->table()
				->where(self::COLUMN_COURSE_ID, $id);
		return $this->createSmartObjects($selection);
	}

	public function delete($id) {
		$row = $this->getRow($id);
		if (!preg_match('#^(http|https)://#', $row->offsetGet(self::COLUMN_IMG))) {
			unlink(ltrim($row->offsetGet(self::COLUMN_IMG), '/'));
		}
		return $row->delete();
	}

	public function deleteByCourseId($id) {
		$selection = $this->table()->where(self::COLUMN_COURSE_ID, $id);
		foreach ($selection as $row) {
			if (!preg_match('#^(http|https)://#', $row->offsetGet(self::COLUMN_IMG))) {
				unlink(ltrim($row->offsetGet(self::COLUMN_IMG),'/'));
			}
		}
		return $selection->delete();
	}

}
