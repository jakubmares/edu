<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;
use Nette\Utils\ArrayHash;
/**
 * Description of PersonalityManager
 *
 * @author jakubmares
 */
class PersonalityManager extends BaseManager {

	const
			TABLE_NAME = 'personality',
			COLUMN_ID = 'id',
			COLUMN_SEOKEY = 'seokey',
			COLUMN_FRIRSTNAME = 'firstname',
			COLUMN_SURNAME = 'surname',
			COLUMN_ACTIVE = 'active',
			COLUMN_DEGREES_BEFORE = 'degrees_before',
			COLUMN_DEGREES_AFTER = 'degrees_after',
			COLUMN_DESCRIPTION = 'description',
			COLUMN_IMAGE = 'image',
			IMAGE_WIDTH = '150',
			IMAGE_PATH = '/images/personality',
			IMAGE_PREFIX = 'personality-';
	
	public function insert($values) {
		$values->offsetSet(self::COLUMN_SEOKEY, $this->generateUniqName($values->offsetGet(self::COLUMN_FRIRSTNAME).' '.$values->offsetGet(self::COLUMN_SURNAME)));
		return parent::insert($values);
	}

	public function update($id, ArrayHash $values) {
		$values->offsetSet(self::COLUMN_SEOKEY, $this->generateUniqName($values->offsetGet(self::COLUMN_FRIRSTNAME).' '.$values->offsetGet(self::COLUMN_SURNAME),$id));
		return parent::update($id, $values);
	}
	
	/** 
	 * 
	 * @param type $seokey
	 * @return So\Personality
	 */
	public function getBySeokey($seokey){
		return $this->createSmartObject($this->table()->where(self::COLUMN_SEOKEY,$seokey)->fetch());
	}
	
	/**
	 * 
	 * @param \Extensions\Paginator $paginator
	 * @return So\Personality[]
	 */
	public function getActivePagin(\Extensions\Paginator $paginator){
		$sel = $this->getSelectionActive();
		$sel->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($sel);
	}
	
	/**
	 * 
	 * @return So\Personality[]
	 */
	public function getActive(){
		return $this->createSmartObjects($this->getSelectionActive());
	}
	
	/**
	 * 
	 * @return int
	 */
	public function countActive(){
		return $this->getSelectionActive()->count();
	}
	
	public function getSelectionActive(){
		return $this->table()->where(self::COLUMN_ACTIVE,true);
	}

}
