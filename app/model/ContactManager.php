<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use App\Model\So\Contact;

/**
 * Description of ContactManager
 *
 * @author jakubmares
 */
class ContactManager extends BaseManager {

	const TABLE_NAME = 'contact',
			COLUMN_COMPANY_ID = 'company_id',
			COLUMN_ID = 'id',
			COLUMN_EMAIL = 'email',
			COLUMN_TYPE = 'type',
			COLUMN_NAME = 'name',
			COLUMN_PHONE = 'phone',
			COLUMN_FUNCTION = 'function',
			COLUMN_EX_ID = 'ex_id';

	/**
	 * 
	 * @param type $companyId
	 * @param type $type
	 * @return Contact[]
	 */
	public function getContactsByType($companyId, $type) {
		$selection = $this->table()
				->where(self::COLUMN_COMPANY_ID, $companyId)
				->where(self::COLUMN_TYPE, $type);
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param type $id
	 * @return Contact[]
	 */
	public function getByCompanyId($id) {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_COMPANY_ID, $id));
	}
	
	/**
	 * 
	 * @param type $id
	 * @param array $types
	 * @return type
	 */
	public function getByCompanyIdAndTypes($id,$types){
		return $this->createSmartObjects($this->table()->where(self::COLUMN_COMPANY_ID, $id)->where(self::COLUMN_TYPE,$types));
	}
	
	/**
	 * 
	 * @param type $id
	 * @return Contact
	 */
	public function getContactQuestionByCompanyId($id){
		$selection = $this->table()
				->where(self::COLUMN_COMPANY_ID, $id)
				->where(self::COLUMN_TYPE, Contact::TYPE_QUESTION);
		return $this->createSmartObject($selection->fetch());
	}
	
	/**
	 * 
	 * @param type $id
	 * @return Contact
	 */
	public function getContactOrderByCompanyId($id){
		$selection = $this->table()
				->where(self::COLUMN_COMPANY_ID, $id)
				->where(self::COLUMN_TYPE, Contact::TYPE_ORDER);
		return $this->createSmartObject($selection->fetch());
	}

}
