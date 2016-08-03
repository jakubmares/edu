<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use App\Model\So\Category;

/**
 * Description of CategoryManager
 *
 * @author jakubmares
 */
class CategoryManager extends BaseManager {

	const
			TABLE_NAME = 'category',
			COLUMN_ID = 'id',
			COLUMN_NAME = 'name',
			COLUMN_SEOKEY = 'seokey',
			COLUMN_POSITION = 'position',
			COLUMN_ACTIVE = 'active';

	
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
	 * @return Category[]
	 */
	public function getActive() {
		$selection = $this->table()->where(self::COLUMN_ACTIVE, 1)
				->order('position');
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @return Category[]
	 */
	public function getAll() {
		return $this->createSmartObjects($this->table()->order('position'));
	}
	
	/**
	 * 
	 * @param type $id
	 * @return Category
	 */
	public function get($id){
		return parent::get($id);
	}
	
	/**
	 * 
	 * @param type $id
	 * @return Category
	 */
	public function getByFocusId($id){
		return $this->createSmartObject($this->table()->where(':focus.id',$id)->fetch());
	}
	
	/**
	 * 
	 * @param type $seokey
	 * @return Category
	 */
	public function getBySeokey($seokey){
		$row = $this->table()->where(self::COLUMN_SEOKEY,$seokey)->fetch();
		if(!$row){
			throw new \Exception('Kategorie nenalezena');
		}
		return $this->createSmartObject($row);
	}
	

}
