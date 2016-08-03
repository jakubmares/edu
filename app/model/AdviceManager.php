<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of AdviceManager
 *
 * @author jakubmares
 */
class AdviceManager extends BaseManager {

	const
			TABLE_NAME = 'advice',
			COLUMN_ID = 'id',
			COLUMN_COMPANY_ID = 'company_id',
			COLUMN_USER_ID = 'user_id',
			COLUMN_CONTENT = 'content',
			COLUMN_HEADER = 'header',
			COLUMN_VALID_FROM = 'valid_from',
			COLUMN_VALID_TO = 'valid_to',
			COLUMN_VALID = 'valid',
			COLUMN_POSITION = 'position';

	
	public function getAdvicesForHomepage($limit = 3){
		$curdate = new \Nette\Utils\DateTime();
		$selection = $this->table()
				->where('valid = ? AND valid_from <= ? AND valid_to >= ?',true,$curdate,$curdate)
				->order('position, valid_from')
				->limit($limit);
		return $this->createSmartObjects($selection);
	}
}
