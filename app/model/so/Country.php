<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Country
 *
 * @author jakubmares
 */
class Country extends SmartObject{
	private $key;
	private $country;
	private $default;
	
	public function getKey() {
		return $this->key;
	}

	public function getCountry() {
		return $this->country;
	}

	public function getDefault() {
		return $this->default;
	}




}
