<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of AddressManager
 *
 * @author jakubmares
 */
class AddressManager extends BaseManager{
	const 
			TABLE_NAME = 'address',
			COLUMN_ID = 'id',
			COLUMN_CITY = 'city',
			COLUMN_STREET = 'street',
			COLUMN_REGISTRY_NUMBER = 'registry_number',
			COLUMN_HOUSE_NUMBER = 'house_number',
			COLUMN_ZIP = 'zip',
			COLUMN_NOTE = 'note',
			COLUMN_LATITUDE = 'latitude',
			COLUMN_LONGITUDE = 'longitude',
			COLUMN_COMPANY_ID = 'company_id',
			COLUMN_TYPE = 'type',
			COLUMN_COUNTRY_KEY = 'country_key';
	
	/**
	 * 
	 * @param type $id
	 * @return So\Address[]
	 */
	public function getAddressesPresentableByCompanyId($id){
		$selection = $this->table()->where(self::COLUMN_COMPANY_ID,$id)
				->where(self::COLUMN_TYPE,[So\Address::TYPE_BASE,  So\Address::TYPE_BRANCH])
				->order(self::COLUMN_TYPE);
		return $this->createSmartObjects($selection);
	}
	
	/**
	 * 
	 * @param type $id
	 * @return So\Address[]
	 */
	public function getByCompanyId($id){
		return $this->createSmartObjects($this->table()->where(self::COLUMN_COMPANY_ID,$id));
	}
}
