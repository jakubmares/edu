<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Contact
 *
 * @author jakubmares
 */
class Contact extends SmartObject {

	const TYPE_CONTACT_PERSON = 'CONTACT';
	const TYPE_SHOW = 'SHOW';
	const TYPE_QUESTION = 'QUESTION';
	const TYPE_ORDER = 'ORDER';
	
	const RELATION_COMPANY = 'company';

	private $id;
	private $email;
	private $type;
	private $companyId;
	private $name;
	private $phone;
	private $function;

	public static function types() {
		return [self::TYPE_CONTACT_PERSON => 'Kontaktní osoba',
			self::TYPE_ORDER => 'Objednávky',
			self::TYPE_QUESTION => 'Dotazy',
			self::TYPE_SHOW => 'Zobrazovaný kontakt',
		];
	}

	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}

	
	public function getEmail() {
		return $this->email;
	}
	
	public function getPhone() {
		return $this->phone;
	}

	
	public function getType() {
		return $this->type;
	}

	public function getCompanyId() {
		return $this->companyId;
	}
	
	public function getLabel(){
		$types = $this->types();
		return $types[$this->type];
	}
	
	public function getFunction() {
		return $this->function;
	}

		
	/**
	 * 
	 * @return Company
	 */
	public function getCompany(){
		return $this->getRef(self::RELATION_COMPANY);
	}

}
