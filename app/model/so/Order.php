<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Order
 *
 * @author jakubmares
 */
class Order extends SmartObject {

	private $id;
	private $termId;
	private $name;
	private $email;
	private $phone;
	private $billingInfo;
	private $memberCount;
	private $note;
	private $createdAt;
	private $courseName;
	private $companyId;
	private $sentTo;
	private $termFrom;
	private $termTo;

	public function getId() {
		return $this->id;
	}

	public function getTermId() {
		return $this->termId;
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

	public function getBillingInfo() {
		return $this->billingInfo;
	}

	public function getMemberCount() {
		return $this->memberCount;
	}

	public function getNote() {
		return $this->note;
	}

	public function getCreatedAt() {
		return $this->createdAt;
	}

	public function getCourseName() {
		return $this->courseName;
	}

	public function getCompanyId() {
		return $this->companyId;
	}

	public function getSentTo() {
		return $this->sentTo;
	}
	
	public function getTermFrom() {
		return $this->termFrom;
	}

	public function getTermTo() {
		return $this->termTo;
	}



}
