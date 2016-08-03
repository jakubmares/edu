<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Partner
 *
 * @author jakubmares
 */
class Partner extends SmartObject{
	private $id;
	private $url;
	private $name;
	private $image;
	private $active;
	private $position;
	
	public function getId() {
		return $this->id;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getName() {
		return $this->name;
	}
	
	public function getImage() {
		return $this->image;
	}

	public function getActive() {
		return $this->active;
	}

	public function getPosition() {
		return $this->position;
	}



}
