<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of PartnerManager
 *
 * @author jakubmares
 */
class PartnerManager extends BaseManager{
	const 
		TABLE_NAME = 'partner',
			COLUMN_ID = ' id',
			COLUMN_IMAGE = 'image',
			COLUMN_URL = 'url',
			COLUMN_NAME = 'name',
			COLUMN_ACTIVE = 'active',
			COLUMN_POSITION = 'position',
			IMAGE_WIDTH = '200',
			IMAGE_HEIGHT = '70',
			IMAGE_PATH = '/images/partner',
			IMAGE_PREFIX = 'partner-';
	
	public function getPartners(){
		$selection = $this->table()->order('position');
		return $this->createSmartObjects($selection);
	}
	
	public function getPartnersActive(){
		$selection = $this->table()
				->where('active = ?',true)
				->order('position');
		return $this->createSmartObjects($selection);
	}
}
