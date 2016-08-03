<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of CountryManager
 *
 * @author jakubmares
 */
class CountryManager extends BaseManager{
	const 
			TABLE_NAME = 'country',
			TABLE_COLUMN_KEY = 'key',
			TABLE_COLUMN_COUNTRY = 'country',
			TABLE_COLUMN_DEFAULT = 'default';
	
	public function getDefaultCountryKey(){
		$row = $this->table()->where(self::TABLE_COLUMN_DEFAULT,true)->fetch();
		return $row->offsetGet(self::TABLE_COLUMN_KEY);
	}
}
