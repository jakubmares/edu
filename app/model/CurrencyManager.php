<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of CurrencyManager
 *
 * @author jakubmares
 */
class CurrencyManager extends BaseManager{
	const TABLE_NAME = 'currency',
			TABLE_COLUMN_CURRENCY = 'currency',
			TABLE_COLUMN_DEFAULT = 'default';
	
	/**
	 * 
	 * @return string
	 */
	public function getDefaultCurrency(){
		$row = $this->table()->where(self::TABLE_COLUMN_DEFAULT , true)->fetch();
		return $row->offsetGet(self::TABLE_COLUMN_CURRENCY);
	}
}
