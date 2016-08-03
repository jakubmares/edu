<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

use App\Model\So\User,
	App\Model\So\Contact,
	App\Model\So\Course,
	App\Model,
	App\Model\AddressManager,
	App\Model\CourseManager;

/**
 * Description of Company
 *
 * @author jakubmares
 */
class Company extends SmartObject {

	const
			RELATION_DEALER = 'user',
			RELATION_DEALER_COLUMN = 'dealer_id',
			RELATION_OWNER = 'user',
			RELATION_CONTACT = 'contact',
			RELATION_NOTE = 'note',
			RELATION_ADDRESS = 'address',
			RELATION_COURSE = 'course',
			POTENCIAL_TOP = 1,
			POTENCIAL_BIG = 2,
			POTENCIAL_MIDDLE = 3,
			POTENCIAL_SMALL = 4,
			POTENCIAL_UNDEFINED = 99,
			STATUS_ADVERTISE = 1,
			STATUS_IN_NEGOTIATIONS = 2,
			STATUS_NO_ADVERTISE = 3,
			STATUS_BARTER = 4,
			STATUS_NO_ADDRESSED = 5,
			STATUS_ADVERTISED = 6,
			STATUS_KOS = 7,
			STATUS_NOT_INTEREST = 8,
			TYPE_TRAINING_COMPANY = 1,
			TYPE_LANGUAGE_SCHOOL = 2,
			TYPE_LECTOR = 3,
			TYPE_UNIVERSITI = 4,
			TYPE_SPORT_COURSES = 6,
			TYPE_CHILD_SCHOOL = 7,
			TYPE_CARSCHOOL = 8,
			TYPE_REQUALIFICATION = 9,
			TYPE_HOBBY_COURSES = 10,
			TYPE_ELEARNING = 11,
			TYPE_OTHERS = 5;

	public static function types() {
		return array(
			self::TYPE_TRAINING_COMPANY => 'vzdělávací společnost',
			self::TYPE_LANGUAGE_SCHOOL => 'jazyková škola',
			self::TYPE_LECTOR => 'lektor',
			self::TYPE_UNIVERSITI => 'vysoká škola',
			self::TYPE_SPORT_COURSES => 'sportovní kurzy',
			self::TYPE_CHILD_SCHOOL => 'kurzy pro děti',
			self::TYPE_CARSCHOOL => 'autoškola',
			self::TYPE_REQUALIFICATION => 'rekvalifikace',
			self::TYPE_HOBBY_COURSES => 'hobby kurzy',
			self::TYPE_ELEARNING => 'e-learning',
			self::TYPE_OTHERS => 'jiný subjekt',
		);
	}

	public static function potencials() {
		return array(
			self::POTENCIAL_TOP => 'TOP',
			self::POTENCIAL_BIG => 'Velká',
			self::POTENCIAL_MIDDLE => 'Střední',
			self::POTENCIAL_SMALL => 'Malá',
			self::POTENCIAL_UNDEFINED => 'neuvedeno',
		);
	}

	public static function statuses() {
		return array(
			self::STATUS_ADVERTISE => 'inzeruje',
			self::STATUS_IN_NEGOTIATIONS => 'v jednání',
			self::STATUS_NO_ADVERTISE => 'neinzeruje',
			self::STATUS_BARTER => 'bartr',
			self::STATUS_NO_ADDRESSED => 'neosloveno',
			self::STATUS_ADVERTISED => 'inzeroval',
			self::STATUS_KOS => 'KOS',
			self::STATUS_NOT_INTEREST => 'nemá zájem',
		);
	}

	private $id;
	private $name;
	private $seokey;
	private $ic;
	private $dic;
	private $description;
	private $active;
	private $userId;
	private $createdAt;
	private $dealerId;
	private $logo;
	private $partner;
	private $web;
	private $importUrl;
	private $importAt;
	private $top;
	private $potencial;
	private $type;
	private $status;
	private $bankAccount;

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getSeokey() {
		return $this->seokey;
	}

	public function getIc() {
		return $this->ic;
	}

	public function getDic() {
		return $this->dic;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getActive() {
		return $this->active;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getCreatedAt() {
		return $this->createdAt;
	}

	public function getDealerId() {
		return $this->dealerId;
	}

	public function getLogo() {
		return $this->logo;
	}

	public function getPartner() {
		return $this->partner;
	}

	public function getWeb() {
		return $this->web;
	}
	
	public function getBankAccount() {
		return $this->bankAccount;
	}

		
	/**
	 * 
	 * @return \Nette\Http\Url
	 */
	public function getWebUrl(){
		return new \Nette\Http\Url($this->web);
	}

	public function getImportUrl() {
		return $this->importUrl;
	}

	public function getImportAt() {
		return $this->importAt;
	}

	public function setImportAt($imaportAt) {
		$this->importAt = $imaportAt;
	}

	public function getTop() {
		return $this->top;
	}
	
	public function getPotencial() {
		return $this->potencial;
	}

	public function getType() {
		return $this->type;
	}

	public function getStatus() {
		return $this->status;
	}
	
	public function getPotencialLabel() {
		return self::potencials()[$this->potencial];
	}

	public function getTypeLabel() {
		return self::types()[$this->type];
	}

	public function getStatusLabel() {
		return self::statuses()[$this->status];
	}

	
	/** @return User */
	public function getDealer() {
		return $this->getRef(self::RELATION_DEALER, self::RELATION_DEALER_COLUMN);
	}

	/**
	 * 
	 * @return Contact[]
	 */
	public function getContacts() {
		return $this->getRelated(self::RELATION_CONTACT);
	}

	/**
	 * 
	 * @return Address[]
	 */
	public function getAddresses() {
		return $this->getRelated(self::RELATION_ADDRESS);
	}

	public function getAddressBase() {
		$row = $this->row->related(self::RELATION_ADDRESS)->where(AddressManager::COLUMN_TYPE, Address::TYPE_BASE)->fetch();
		return self::create($row);
	}

	public function getAddressBilling() {
		$row = $this->row->related(self::RELATION_ADDRESS)->where(AddressManager::COLUMN_TYPE, Address::TYPE_BILLING)->fetch();
		return self::create($row);
	}

	/**
	 * 
	 * @return Note[]
	 */
	public function getNotes() {
		return $this->getRelated(self::RELATION_NOTE);
	}

	/** @return User */
	public function getOwner() {
		return $this->getRef(self::RELATION_OWNER);
	}

	/**
	 * 
	 * @return Course[]
	 */
	public function getCourses() {
		return $this->getRelated(self::RELATION_COURSE);
	}

	public function getTopCourses() {
		$selection = $this->row->related(self::RELATION_COURSE)->where(CourseManager::COLUMN_ACTIVE, true)->limit(5);
		return self::createList($selection);
	}
	
	public function getLastContactNote(){
		$row = $this->row->related(self::RELATION_NOTE)->order(Model\NoteManager::COLUMN_CONTACT_AT . ' DESC')->fetch();
		return self::create($row);
	}

	public function __toString() {
		return $this->getName();
	}

}
