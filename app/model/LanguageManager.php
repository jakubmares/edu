<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of LanguageManager
 *
 * @author jakubmares
 */
class LanguageManager extends BaseManager {

	const TABLE_NAME = 'language',
			COLUMN_DEFAULT = 'default',
			COLUMN_ID = 'id',
			COLUMN_CODE = 'code',
			COLUMN_NAME = 'name';

	public function getDefaultLanguageId() {
		$row = $this->table()->where(self::COLUMN_DEFAULT, true)->fetch();
		return $row->offsetGet(self::COLUMN_ID);
	}

}
