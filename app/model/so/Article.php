<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Article
 *
 * @author jakubmares
 */
class Article extends SmartObject {

	const
			RELATION_PERSONALITY = 'personality',
			RELATION_USER = 'user';

	private $id;
	private $personalityId;
	private $title;
	private $perex;
	private $content;
	private $userId;
	private $createdAt;
	private $active;
	private $image;
	private $author;
	private $publishedAt;
	private $seokey;

	public function getId() {
		return $this->id;
	}

	public function getPersonalityId() {
		return $this->personalityId;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getPerex() {
		return $this->perex;
	}

	public function getContent() {
		return $this->content;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getCreatedAt() {
		return $this->createdAt;
	}

	public function getActive() {
		return $this->active;
	}

	public function getImage() {
		return $this->image?$this->image:'/images/icon_newsletter.png';
	}

	public function getAuthor() {
		return $this->author;
	}

	public function getPublishedAt() {
		return $this->publishedAt;
	}
	
	public function getSeokey() {
		return $this->seokey;
	}

	
	/**
	 * 
	 * @return Personality
	 */
	public function getPersonality() {
		return $this->getRef(self::RELATION_PERSONALITY);
	}
	
	/**
	 * 
	 * @return User
	 */
	public function getUser(){
		return $this->getRef(self::RELATION_USER);
	}

}
