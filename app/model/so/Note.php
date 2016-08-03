<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Note
 *
 * @author jakubmares
 */
class Note extends SmartObject{
	
	const 
			RELATION_CONTACT = 'contact',
			RELATION_OWNER = 'user',
			RELATION_COMPANY = 'company';
	
	private $id;
	private $companyId;
	private $userId;
	private $createdAt;
	private $contactAt;
	private $nextContactAt;
	private $note;
	private $contactNote;
	private $contactId;
	private $done;
	
	public function getId() {
		return $this->id;
	}

	public function getCompanyId() {
		return $this->companyId;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getCreatedAt() {
		return $this->createdAt;
	}

	public function getContactAt() {
		return $this->contactAt;
	}

	public function getNextContactAt() {
		return $this->nextContactAt;
	}

	public function getNote() {
		return $this->note;
	}

	public function getContactNote() {
		return $this->contactNote;
	}

	public function getContactId() {
		return $this->contactId;
	}

	public function getDone() {
		return $this->done;
	}
	
	/**
	 * 
	 * @return Contact
	 */
	public function getContact() {
		return $this->getRef(self::RELATION_CONTACT);
	}

	/**
	 * 
	 * @return User
	 */
	public function getOwner() {
		return $this->getRef(self::RELATION_OWNER);
	}

	/**
	 * 
	 * @return Company
	 */
	public function getCompany(){
		return $this->getRef(self::RELATION_COMPANY);
	}

}
