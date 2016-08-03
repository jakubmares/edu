<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;
use Nette\Utils\ArrayHash,
	Nette\Utils\DateTime,
	App\Model\So\Note;

/**
 * Description of NoteManager
 *
 * @author jakubmares
 */
class NoteManager extends BaseManager{
	const TABLE_NAME = 'note',
			COLUMN_COMPANY_ID = 'company_id',
			COLUMN_ID = 'id',
			COLUMN_USER_ID = 'user_id',
			COLUMN_CREATED_AT = 'created_at',
			COLUMN_CONTACT_AT = 'contact_at',
			COLUMN_NEXT_CONTACT_AT = 'next_contact_at',
			COLUMN_NOTE='note',
			COLUMN_CONTACT_NOTE = 'contact_note',
			COLUMN_CONTACT_ID = 'contact_id',
			COLUMN_DONE = 'done';
	
	/**
	 * 
	 * @param ArrayHash $values
	 * @param type $userId
	 */
	public function insertNote(ArrayHash $values,$userId){
		$values['userId'] = $userId;
		$values['createdAd'] = new DateTime();
		$this->insert($values);
	}
	
	
	public function getByCompanyId($id){
		return $this->createSmartObjects($this->table()->where(self::COLUMN_COMPANY_ID,$id));
	}
	
	/**
	 * 
	 * @param type $id
	 * @return Note
	 */
	public function getLastNoteByCompanyId($id){
		$sel = $this->table()->where(self::COLUMN_COMPANY_ID,$id)
				->order(self::COLUMN_CONTACT_AT . ' DESC ,'.self::COLUMN_ID.' DESC');
		return $this->createSmartObject($sel->fetch());
	}
}
