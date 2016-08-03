<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Po;

use Extensions\ESimpleXmlElement;

/**
 * Description of PoCourse
 *
 * @author jakubmares
 */
class Course extends BasePo {

	const
			ATR_ID = 'id',
			TAG_NAME = 'name',
			TAG_DESCRIPTION = 'description',
			TAG_LANGUAGE = 'language',
			TAG_LANGUAGE_ATR_ID = 'id',
			TAG_RETRAINING = 'retraining',
			TAG_RETRAINING_ATR_ENABLE = 'enable',
			TAG_URL = 'url',
			
			//CONDITION
			CONDITION_DESCRIPTION = '<strong><p><ul><li>';

	public $external_id;
	public $company_id;
	public $name;
	public $description;
	public $language_id;
	public $retraining;
	public $link_url;
	public $active;



	/**
	 * 
	 * @param int $companyId
	 * @param ESimpleXmlElement $xCourse
	 * @param int $defaultLanguageId
	 */
	public function __construct($companyId, ESimpleXmlElement $xCourse, $defaultLanguageId) {
		$this->active = true;
		$this->company_id = $companyId;
		$this->external_id = $xCourse->getAttribute(self::ATR_ID);
		$this->name = $xCourse->getTagContent(self::TAG_NAME);
		$this->description = $xCourse->getTagContentHtml(self::TAG_DESCRIPTION, self::CONDITION_DESCRIPTION);

		$language = $xCourse->getTag(self::TAG_LANGUAGE);
		$this->language_id = $language ? $language->getAttribute(self::TAG_LANGUAGE_ATR_ID) : $defaultLanguageId;

		$retraining = $xCourse->getTag(self::TAG_RETRAINING);
		$this->retraining = $retraining ? $retraining->getAttribute(self::TAG_RETRAINING_ATR_ENABLE) : false;

		$this->link_url = $xCourse->getTagContentUrl('url');
	}



}
