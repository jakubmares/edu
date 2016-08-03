<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;
use App\Model;
/**
 * Description of File
 *
 * @author jakubmares
 */
class File extends SmartObject {

	const TYPE_PRICE_LIST = 'price_list',
			TYPE_IMAGE = 'image',
			TYPE_DOWNLOAD = 'download';

	private $id;
	private $type;
	private $path;
	private $title;
	private $userId;
	private $updatedAt;
	
	public static function typeLabels(){
		return [self::TYPE_PRICE_LIST => 'Ceník',  self::TYPE_IMAGE => 'Obrázek' ,  self::TYPE_DOWNLOAD => 'Ke stažení'];
	}
	
	public function getId() {
		return $this->id;
	}

	public function getType() {
		return $this->type;
	}

	public function getPath() {
		return $this->path;
	}

	public function getTitle() {
		return $this->title;
	}
	
	public function getUserId() {
		return $this->userId;
	}

	public function getUpdatedAt() {
		return $this->updatedAt;
	}

	
	public function getTypeLabel(){
		$labels = self::typeLabels();
		return $labels[$this->type];
	}

	/**
	 * 
	 * @return User
	 */
	public function getUser(){
		return $this->getRef(Model\UserManager::TABLE_NAME);
	}
}
