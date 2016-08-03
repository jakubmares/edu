<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of UserRoleManager
 *
 * @author jakubmares
 */
class UserRoleManager extends BaseManager {

	const
			TABLE_NAME = 'user_role',
			COLUMN_ROLE_ID = 'role_id',
			COLUMN_USER_ID = 'user_id';

	
	
	/**
	 * 
	 * @param type $id
	 * @return type
	 */
	public function deleteByUserId($id) {
		return $this->table()->where(self::COLUMN_USER_ID, $id)->delete();
	}

}
