<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Language
 *
 * @author jakubmares
 */
class Language extends SmartObject{
	private $id;
	private $code;
	private $name;
	private $default;
	
	public function getId() {
		return $this->id;
	}

	public function getCode() {
		return $this->code;
	}

	public function getName() {
		return $this->name;
	}

	public function getDefault() {
		return $this->default;
	}

	public function __toString() {
		return $this->name;
	}

}
