<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of RoleManager
 *
 * @author jakubmares
 */
class RoleManager extends BaseManager {

	const 
			TABLE_NAME = 'role',
			COLUMN_ID = 'id',
			COLUMN_ROLE = 'role',
			COLUMN_ROLE_ID = 'role_id',
			COLUMN_DEFAULT = 'default',
			ROLE_ADMIN = 'admin',
			ROLE_MEMBER = 'member',
			ROLE_DEALER = 'dealer',
			ROLE_GUEST = 'guest';

	public function getAll() {
		return $this->createSmartObjects($this->table()->order(self::COLUMN_ROLE_ID));
	}

}
