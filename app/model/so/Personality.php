<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Personality
 *
 * @author jakubmares
 */
class Personality extends SmartObject {

	private $id;
	private $firstname;
	private $surname;
	private $seokey;
	private $degreesBefore;
	private $degreesAfter;
	private $description;
	private $active;
	private $image;

	public function getId() {
		return $this->id;
	}

	public function getFirstname() {
		return $this->firstname;
	}

	public function getSurname() {
		return $this->surname;
	}

	public function getSeokey() {
		return $this->seokey;
	}

	public function getDegreesBefore() {
		return $this->degreesBefore;
	}

	public function getDegreesAfter() {
		return $this->degreesAfter;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getActive() {
		return $this->active;
	}
	
	public function getImage() {
		return $this->image;
	}

	
	public function getName() {
		return sprintf('%s %s %s %s', $this->degreesBefore, $this->firstname, $this->surname, $this->degreesAfter);
	}
	
	public function __toString() {
		return $this->getName();
	}

}
