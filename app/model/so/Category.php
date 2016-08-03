<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

use App\Model\So\Focus;

/**
 * Description of Category
 *
 * @author jakubmares
 */
class Category extends SmartObject {

	const RELATED_FOCUS = 'focus';

	private $id;
	private $name;
	private $seokey;
	private $position;
	private $active;

	/**
	 * 
	 * @return Focus[]
	 */
	public function getAllFocuses() {
		return $this->getRelated(self::RELATED_FOCUS);
	}

	/**
	 * 
	 * @return Focus[]
	 */
	public function getActiveFocuses() {
		$selection = $this->row->related(self::RELATED_FOCUS)->where('active = ?', true);
		return self::createList($selection);
	}

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

}
