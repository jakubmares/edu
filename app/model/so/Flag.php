<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Flag
 *
 * @author jakubmares
 */
class Flag extends SmartObject {

	private $id;
	private $name;

	public function getId() {
		return $this->id;
	}


	public function getName() {
		return $this->name;
	}

}
