<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;
use App\Model\RoleManager;
/**
 * Description of Role
 *
 * @author jakubmares
 */
class Role extends SmartObject{
	
	private $id;
	private $role;
	private $roleId;
	private $default;


	public function getId() {
		return $this->id;
	}

	public function getRole() {
		return $this->role;
	}
	
	public function getRoleId() {
		return $this->roleId;
	}
	
	public function getDefault() {
		return $this->default;
	}
	
	public function __toString() {
		return $this->role;
	}
	
	/**
	 * 
	 * @return Role
	 */
	public function getParent(){
		return $this->getRef(RoleManager::TABLE_NAME);
	}
	
	/**
	 * 
	 * @return string|null
	 */
	public function getParentRole(){
		if($this->roleId){
			return $this->getParent()->getRole();
		}  else {
			return null;
		}
	}
}
