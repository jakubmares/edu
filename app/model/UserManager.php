<?php

namespace App\Model;

use Nette,
	Nette\Security\Passwords,
	Nette\Utils\ArrayHash,
	App\Model\So\User;

/**
 * Users management.
 */
class UserManager extends BaseManager implements Nette\Security\IAuthenticator {

	const
			TABLE_NAME = 'user',
			COLUMN_ID = 'id',
			COLUMN_FIRSTNAME = 'firstname',
			COLUMN_SURNAME = 'surname',
			COLUMN_EMAIL = 'email',
			COLUMN_ACTIVE = 'active',
			COLUMN_LAST_LOGIN = 'last_login',
			COLUMN_PASSWORD_HASH = 'password',
			COLUMN_CREAED_AT = 'created_at';

	/** @var UserRoleManager */
	private $userRoleMan;

	public function __construct(Nette\Database\Context $database, UserRoleManager $userRoleMan) {
		parent::__construct($database);
		$this->userRoleMan = $userRoleMan;
	}

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($username, $password) = $credentials;

		$row = $this->table(self::TABLE_NAME)->where(self::COLUMN_EMAIL, $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update(array(
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		}

		$user = $this->createSmartObject($row);

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row->offsetGet(self::COLUMN_ID), $user->getRolesName(), $arr);
	}

	/**
	 * 
	 * @param ArrayHash $values
	 * @return IRow|int|bool Returns IRow or number of affected rows for Selection or table without primary key
	 * @throws DuplicateNameException
	 */
	public function insert($values) {
		$pass = Passwords::hash($values->offsetGet(self::COLUMN_PASSWORD_HASH));
		$values->offsetSet(self::COLUMN_PASSWORD_HASH, $pass);
		try {
			$row = parent::insert($values);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
		return $row;
	}

	/**
	 * 
	 * @param ArrayHash $values
	 * @param type $rolesId
	 * @return IRow|int|bool Returns IRow or number of affected rows for Selection or table without primary key
	 * @throws \App\Model\Exception
	 */
	public function insertWithRoles(ArrayHash $values, $rolesId) {
		$this->database->beginTransaction();
		$roles = [];
		try {
			$row = $this->insert($values);
			foreach ($rolesId as $roleId) {
				$roles[] = [
					UserRoleManager::COLUMN_USER_ID => $row->offsetGet(self::COLUMN_ID),
					UserRoleManager::COLUMN_ROLE_ID => $roleId
				];
			}
			$this->userRoleMan->insert($roles);
			$this->database->commit();
		} catch (Exception $exc) {
			$this->database->rollBack();
			throw $exc;
		}
		return $row;
	}

	/**
	 * 
	 * @param type $id
	 * @param ArrayHash $values
	 * @return bool
	 * @throws DuplicateNameException
	 */
	public function update($id, ArrayHash $values) {
		$values->offsetUnset(self::COLUMN_PASSWORD_HASH);
		try {
			$ret = parent::update($id, $values);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
		return $ret;
	}

	/**
	 * @todo osetrit odebrani role dealer v pripade, ze je dealer prirazen k nejake spolecnosti
	 * @param type $id
	 * @param ArrayHash $values
	 * @param type $rolesId
	 * @return bool
	 * @throws \App\Model\Exception
	 */
	public function updateWithRoles($id, ArrayHash $values, $rolesId) {
		$this->database->beginTransaction();
		$roles = [];
		foreach ($rolesId as $roleId) {
			$roles[] = [
				UserRoleManager::COLUMN_USER_ID => $id,
				UserRoleManager::COLUMN_ROLE_ID => $roleId
			];
		}

		try {
			$row = $this->update($id, $values);
			$this->userRoleMan->deleteByUserId($id);
			$this->userRoleMan->insert($roles);
			$this->database->commit();
		} catch (Exception $exc) {
			$this->database->rollBack();
			throw $exc;
		}
		return $row;
	}

	public function updatePass($id, $pass) {
		$data = new ArrayHash();
		$data->offsetSet(self::COLUMN_PASSWORD_HASH, Passwords::hash($pass));
		return parent::update($id, $data);
	}

	/**
	 * 
	 * @return User[]
	 */
	public function getActive() {
		$selection = $this->table()->where(self::COLUMN_ACTIVE, true);
		return $this->createSmartObjects($selection);
	}

	public function getDealers() {
		$selection = $this->table()->where(':user_role.role.role', 'dealer');
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param type $email
	 * @return string new Pass
	 * @throws Exception
	 */
	public function renewPass($email) {
		$row = $this->table(self::TABLE_NAME)->where(self::COLUMN_EMAIL, $email)->where(self::COLUMN_ACTIVE, true)->fetch();
		if (!$row) {
			throw new Exception('Uzivatel s timto emailem nenalezen');
		}

		$newPass = uniqid();
		$this->updatePass($row->offsetGet(self::COLUMN_ID), $newPass);
		return $newPass;
	}

	/**
	 * 
	 * @param type $email
	 * @return boolean
	 */
	public function isEmailExist($email) {
		return $this->table(self::TABLE_NAME)->where(self::COLUMN_EMAIL, $email)->where(self::COLUMN_ACTIVE, true) ? true : false;
	}

	/**
	 * 
	 * @param type $id
	 * @param type $pass
	 * @return boolean
	 */
	public function isPass($id, $pass) {
		$row = $this->table(self::TABLE_NAME)->get($id);
		return Passwords::verify($pass, $row[self::COLUMN_PASSWORD_HASH]) ? true : false;
	}

	public function countActive() {
		return $this->table()->where(self::COLUMN_ACTIVE, true)->count();
	}

}

class DuplicateNameException extends \Exception {
	
}
