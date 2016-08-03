<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Advice
 *
 * @author jakubmares
 */
class Advice extends SmartObject {

	const
			RELATION_USER = 'user',
			RELATION_COMPANY = 'company';

	private $id;
	private $companyId;
	private $userId;
	private $content;
	private $validFrom;
	private $validTo;
	private $valid;
	private $header;
	private $position;

	public function getId() {
		return $this->id;
	}

	public function getCompanyId() {
		return $this->companyId;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getContent() {
		return $this->content;
	}

	public function getValidFrom() {
		return $this->validFrom;
	}

	public function getValidTo() {
		return $this->validTo;
	}

	public function getValid() {
		return $this->valid;
	}
	
	public function getHeader() {
		return $this->header;
	}
	
	public function getPosition() {
		return $this->position;
	}	
		
	/**
	 * 
	 * @return Company
	 */
	public function getCompany(){
		return $this->getRef(self::RELATION_COMPANY);
	}
	
	/**
	 * 
	 * @return User
	 */
	public function getUser(){
		return $this->getRef(self::RELATION_USER);
	}

}
