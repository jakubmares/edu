<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use App\Model\So\Focus;

/**
 * Description of FocusManager
 *
 * @author jakubmares
 */
class FocusManager extends BaseManager {

	const
			TABLE_NAME = 'focus',
			RELATED_TABLE_NAME = 'course_focus',
			COLUMN_CATEGORY_ID = 'category_id',
			COLUMN_ACTIVE = 'active',
			COLUMN_SEOKEY = 'seokey',
			COLUMN_POSITION = 'position',
			COLUMN_ID = 'id',
			COLUMN_NAME = 'name';

	
	public function insertRow(\Nette\Utils\ArrayHash $array) {
		$seokey = $array->offsetGet(self::COLUMN_SEOKEY);
		$array->offsetSet(self::COLUMN_SEOKEY,  \Nette\Utils\Strings::webalize($seokey));
		return parent::insert($array);
	}

	public function update($id, \Nette\Utils\ArrayHash $values) {
		$seokey = $values->offsetGet(self::COLUMN_SEOKEY);
		$values->offsetSet(self::COLUMN_SEOKEY,  \Nette\Utils\Strings::webalize($seokey));
		return parent::update($id, $values);
	}

	
	/**
	 * 
	 * @param type $id
	 * @return Focus
	 */
	public function getFocus($id) {
		return $this->get($id);
	}

	/**
	 * 
	 * @param type $id
	 * @return Focus[]
	 */
	public function getFocusesByCategoryId($id) {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_CATEGORY_ID, $id));
	}
	
	public function getActiveFocusesByCategoryId($id) {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_CATEGORY_ID, $id)->where(self::COLUMN_ACTIVE,1));
	}


	public function getActiveFocuses(){
		return $this->createSmartObjects($this->table()->where(self::COLUMN_ACTIVE, 1));
	}
	
		/**
	 * 
	 * @param type $seokey
	 * @return Focus
	 */
	public function getFocusBySeokey($seokey){
		$row = $this->table()->where(self::COLUMN_SEOKEY,$seokey)->fetch();
		if(!$row){
			throw new Exception('Zamereni nenalezeno');
		}
		return $this->createSmartObject($row);
	}
	
	/**
	 * 
	 * @param type $id
	 * @return Focus[]
	 */
	public function getFocusesByCourseId($id){
		$selection = $this->table()->where(':course_focus.course_id',$id)
				->where(self::COLUMN_ACTIVE,true)
				->order(self::COLUMN_POSITION);
		return $this->createSmartObjects($selection);
	}
	
		/**
	 * 
	 * @param type $id
	 * @return Focus
	 */
	public function getFocusByCourseId($id){
		$selection = $this->table()->where(':course_focus.course_id',$id)
				->where(self::COLUMN_ACTIVE,true)
				->order(self::COLUMN_POSITION);
		return $this->createSmartObject($selection->fetch());
	}

}
