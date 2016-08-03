<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

use App\Model;

/**
 * Description of User
 *
 * @author jakubmares
 */
class User extends SmartObject {

	private $id;
	private $firstname;
	private $surname;
	private $email;
	private $active;

	public function getId() {
		return $this->id;
	}

	public function getFirstname() {
		return $this->firstname;
	}

	public function getSurname() {
		return $this->surname;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getActive() {
		return $this->active;
	}

	/**
	 * 
	 * @return Role[]
	 */
	public function getRoles() {
		$ret = [];
		/* @var $row \Nette\Database\Table\ActiveRow */
		foreach ($this->row->related(Model\UserRoleManager::TABLE_NAME) as $row) {
			$ret[] = self::create($row->ref(Model\RoleManager::TABLE_NAME));
		}
		return $ret;
	}
	
	/**
	 * 
	 * @return array of string
	 */
	public function getRolesName(){
		$ret = [];
		foreach ($this->getRoles() as $role) {
			$ret[] = $role->getRole();
		}
		return $ret;
	}
	
	public function getRolesId(){
		$ret = [];
		foreach ($this->getRoles() as $role) {
			$ret[] = $role->getId();
		}
		return $ret;
	}

	public function getName() {
		return $this->firstname . ' ' . $this->surname;
	}

	public function __toString() {
		return $this->getName();
	}

}
