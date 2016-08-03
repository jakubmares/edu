<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Currency
 *
 * @author jakubmares
 */
class Currency extends SmartObject{
	private $currency;
	private $default;
	
	public function getCurrency() {
		return $this->currency;
	}

	public function getDefault() {
		return $this->default;
	}


}
