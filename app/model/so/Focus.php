<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Focus
 *
 * @author jakubmares
 */
class Focus extends SmartObject{
	
	const RELATION_CATEGORY = 'category';
	
	private $id;
	private $name;
	private $seokey;
	private $position;
	private $active;
	private $categoryId;
	
	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getSeokey() {
		return $this->seokey;
	}

	public function getPosition() {
		return $this->position;
	}

	public function getActive() {
		return $this->active;
	}

	public function getCategoryId() {
		return $this->categoryId;
	}
	
	/**
	 * 
	 * @return Category
	 */
	public function getCategory(){
		return $this->getRef(self::RELATION_CATEGORY);
	}
	
	public function __toString() {
		return $this->name;
	}



}
