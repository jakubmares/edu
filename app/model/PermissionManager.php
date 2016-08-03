<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette,
	App\Model\RoleManager;

/**
 * Description of PermissionManager
 *
 * @author jakubmares
 */
class PermissionManager extends \Nette\Security\Permission {

	const TABLE_NAME = 'permission',
			COLUMN_ROLE_ID = 'role_id',
			COLUMN_RESOURCE = 'resource',
			COLUMN_PRIVILEGE = 'privilege',
			RESOURCE_ZONE = 'Zone',
			RESOURCE_ADMIN = 'Admin',
			PRIVILEGE_CREATE = 'create',
			PRIVILEGE_EDIT = 'edit',
			PRIVILEGE_DELETE = 'delete',
			PRIVILEGE_VIEW = 'view';

	/** @var Nette\Database\Context */
	private $database;

	/** @var RoleManager */
	private $roleMan;

	public function __construct(Nette\Database\Context $database, RoleManager $roleMan) {
		$this->database = $database;
		$this->roleMan = $roleMan;
		$this->setRoles();
		$this->setResources();
		$this->setAllows();
	}

	private function setRoles() {
		foreach ($this->roleMan->getAll() as $role) {
			$this->addRole($role->getRole(), $role->getParentRole());
		}
	}

	private function setResources() {
		foreach (self::resourcesList() as $resource => $value) {
			$this->addResource($resource);
		}
	}

	public function setAllows() {
		/* @var $row \Nette\Database\Table\ActiveRow */
		foreach ($this->database->table(self::TABLE_NAME) as $row) {
			$roleRow = $row->ref(RoleManager::TABLE_NAME);
			$resource = $row->offsetGet(self::COLUMN_RESOURCE);
			$privilege = $row->offsetGet(self::COLUMN_PRIVILEGE)?$row->offsetGet(self::COLUMN_PRIVILEGE):  Nette\Security\Permission::ALL;
			$this->allow($roleRow->offsetGet(RoleManager::COLUMN_ROLE),$resource ,$privilege);
		}
	}

	public static function resourcesList() {
		return [self::RESOURCE_ZONE => 'Zona', self::RESOURCE_ADMIN => 'Administrace'];
	}

	public static function privilegesList() {
		return [
			self::PRIVILEGE_CREATE => 'vytvorit',
			self::PRIVILEGE_EDIT => 'editovat',
			self::PRIVILEGE_DELETE => 'smazat',
			self::PRIVILEGE_VIEW => 'zobrazit'];
	}

}
