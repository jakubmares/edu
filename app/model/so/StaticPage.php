<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;
use App\Model;
/**
 * Description of StaticPage
 *
 * @author jakubmares
 */
class StaticPage extends SmartObject {
	
	const KEY_PODMINKY = 'podminky',
			KEY_KODEX = 'kodex',
			KEY_KONTAKTY = 'kontakty';
	
	private $id;
	private $title;
	private $content;
	private $userId;
	private $updatedAt;
	
	public function getId() {
		return $this->id;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getContent() {
		return $this->content;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getUpdatedAt() {
		return $this->updatedAt;
	}
	
	public function getUser(){
		return $this->getRef(Model\UserManager::TABLE_NAME) ;
	}
	
	public function getIdLabel(){
		$labels = self::idLabels();
		return $labels[$this->id];
	}
	
	public static function idLabels(){
		return [self::KEY_PODMINKY => 'Podmínky',  self::KEY_KONTAKTY=>'Kontakty',  self::KEY_KODEX => 'Kodex'];
	}


}
