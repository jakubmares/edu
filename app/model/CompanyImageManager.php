<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of CompanyImageManager
 *
 * @author jakubmares
 */
class CompanyImageManager extends BaseManager {

	const TABLE_NAME = 'company_image',
			COLUMN_ID = 'id',
			COLUMN_TITLE = 'title',
			COLUMN_IMG = 'img',
			COLUMN_ACTIVE = 'active',
			COLUMN_COMPANY_ID = 'company_id',
			//IMAGE SETTINGS
			IMAGE_WIDTH = '540',
			IMAGE_HEIGHT = '405',
			IMAGE_PATH = '/images/company-img',
			IMAGE_PREFIX = 'img-';

	/**
	 * 
	 * @param type $id
	 * @return So\CompanyImage[]
	 */
	public function getByCompanyId($id) {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_COMPANY_ID, $id));
	}
	
		/**
	 * 
	 * @param type $id
	 * @return So\CompanyImage[]
	 */
	public function getActiveByCompanyId($id) {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_COMPANY_ID, $id)->where(self::COLUMN_ACTIVE,true));
	}
	
	public function delete($id){
		$row = $this->getRow($id);
		unlink(ltrim($row->offsetGet(self::COLUMN_IMG),'/'));
		return $row->delete();
		
	}
	
	/**
	 * 
	 * @return So\CompanyImage
	 */
	public function getLast() {
		return $this->createSmartObject($this->table()->order('id DESC')->fetch());
	}

}
