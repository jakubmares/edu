<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\sdt;

/**
 * Description of BreadCrumb
 *
 * @author jakubmares
 */
class BreadCrumb extends Sdt {

	private $name;
	private $destination;
	private $params;
	
	public function __construct($name, $destination , $params = []) {
		$this->name = $name;
		$this->destination = $destination;
		$this->params = $params;
	}

	public function getName() {
		return $this->name;
	}

	public function getDestination() {
		return $this->destination;
	}

	public function getParams() {
		return $this->params;
	}

}
