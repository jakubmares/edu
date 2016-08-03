<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of CompanyVideoManager
 *
 * @author jakubmares
 */
class CompanyVideoManager extends BaseManager {
	const 
			TABLE_NAME = 'company_video',
			COLUMN_ID = 'id',
			COLUMN_VIDEO = 'video',
			COLUMN_ACTIVE = 'active',
			COLUMN_TITLE = 'title',
			COLUMN_COMPANY_ID = 'company_id';
	
	public function insert($values) {
		$values->offsetSet(self::COLUMN_VIDEO, $this->cutCodeFromVideoLink($values->offsetGet(self::COLUMN_VIDEO)));
		return parent::insert($values);
	}

	public function update($id, \Nette\Utils\ArrayHash $values) {
		$values->offsetSet(self::COLUMN_VIDEO, $this->cutCodeFromVideoLink($values->offsetGet(self::COLUMN_VIDEO)));
		return parent::update($id, $values);
	}
	
	/**
	 * 
	 * @param type $id
	 * @return So\CompanyVideo
	 */
	public function getByCompanyId($id){
		$selection = $this->table()->where(self::COLUMN_COMPANY_ID,$id);
		return $this->createSmartObjects($selection);
	}
	
	/**
	 * 
	 * @return So\CompanyVideo
	 */
	public function getLast() {
		return $this->createSmartObject($this->table()->order('id DESC')->fetch());
	}

}
