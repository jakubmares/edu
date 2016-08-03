<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Po;

use Extensions\ESimpleXmlElement;

/**
 * Description of PoTerm
 *
 * @author jakubmares
 */
class Term extends BasePo{

	const
			ATR_ID = 'id',
			ATR_STATE = 'state',
			TAG_DATE = 'date',
			TAG_DATE_ATR_FROM = 'from',
			TAG_DATE_ATR_TO = 'to',
			TAG_PRICE = 'price',
			TAG_PRICE_ATR_AMOUNT = 'amount',
			TAG_PRICE_ATR_CURRENCY = 'currency',
			TAG_PRICE_ATR_VAT = 'vat',
			TAG_PRICE_ATR_DISCOUNT = 'discount',
			TAG_PRICE_ATR_FLAG = 'flag',
			TAG_PRICE_ATR_DISCOUNT_PERCENT = 'discount-percent',
			TAG_ADDRESS = 'address',
			TAG_ADDRESS_ATR_COUNTRY = 'country',
			TAG_ADDRESS_ATR_CITY = 'city',
			TAG_ADDRESS_ATR_STREET = 'street',
			TAG_ADDRESS_ATR_REGISTRY_NUMBER = 'registry_number',
			TAG_ADDRESS_ATR_HOUSE_NUMBER = 'house_number',
			TAG_ADDRESS_ATR_ZIP = 'zip',
			TAG_ADDRESS_ATR_FLAG = 'flag',
			TAG_LECTOR = 'lector',
			TAG_LECTOR_TAG_FIRSTNAME = 'firstname',
			TAG_LECTOR_TAG_SURNAME = 'surname',
			TAG_LECTOR_TAG_DEGREES_BEFORE = 'degrees_before',
			TAG_LECTOR_TAG_DEGREES_AFTER = 'degrees_after',
			TAG_LECTOR_TAG_DESCRIPTION = 'description',
			TAG_LECTOR_TAG_IMAGE = 'image',
			TAG_LECTOR_TAG_IMAGE_ATR_SRC = 'src',
			TAG_LECTOR_TAG_SKILLS = 'skills',
			//CONDITION
			CONDITION_LECTOR_DESCRIPTION = '<strong><p><ul><li>',
			CONDITION_LECTOR_SKILLS = '<strong><p><ul><li>';

	public $external_id;
	public $active;
	public $course_id;
	public $noterm;
	//Date
	public $from;
	public $to;
	//Price
	public $price;
	public $currency;
	public $vat;
	public $discount;
	public $price_flag;
	//Address
	public $address_note;
	public $country_key;
	public $city;
	public $street;
	public $registry_number;
	public $house_number;
	public $zip;
	public $address_flag;
	
	//Lector
	public $lector_firstname;
	public $lector_surname;
	public $lector_degrees_before;
	public $lector_degrees_after;
	public $lector_description;
	public $lector_skills;
	public $lector_image;

	public function __construct($courseId, ESimpleXmlElement $xTerm, $defaultCountry = 'CZ',$defaultCurrency = 'CZK',$noterm = 0) {
		$this->course_id = $courseId;
		$this->external_id = $xTerm->getAttribute(self::ATR_ID);
		$this->active = $xTerm->getAttribute(self::ATR_STATE,true);
		$this->noterm = $noterm;
		$date = $xTerm->getTag(self::TAG_DATE);
		$this->from = $date->getAttribute(self::TAG_DATE_ATR_FROM);
		$this->to = $date->getAttribute(self::TAG_DATE_ATR_TO);
		$address = $xTerm->getTag(self::TAG_ADDRESS);
		$this->address_note = $address->getContent();
		$this->street = $address->getAttribute(self::TAG_ADDRESS_ATR_STREET);
		$this->registry_number = $address->getAttribute(self::TAG_ADDRESS_ATR_REGISTRY_NUMBER);
		$this->house_number = $address->getAttribute(self::TAG_ADDRESS_ATR_HOUSE_NUMBER);
		$this->city = $address->getAttribute(self::TAG_ADDRESS_ATR_CITY);
		$this->zip = $address->getAttribute(self::TAG_ADDRESS_ATR_ZIP);
		$this->address_flag = $address->getAttribute(self::TAG_ADDRESS_ATR_FLAG);
		$countryKey = $address->getAttribute(self::TAG_ADDRESS_ATR_COUNTRY);
		$this->country_key = $countryKey ? strtoupper($countryKey) : $defaultCountry;

		$price = $xTerm->getTag(self::TAG_PRICE);
		$this->price = $price->getAttribute(self::TAG_PRICE_ATR_AMOUNT);
		$currency = $price->getAttribute(self::TAG_PRICE_ATR_CURRENCY);
		$this->currency = $currency?strtoupper($currency):$defaultCurrency;
		$this->vat = $price->getAttribute(self::TAG_PRICE_ATR_VAT);
		$this->price_flag = $price->getAttribute(self::TAG_PRICE_ATR_FLAG);
		
		if ($price->getAttribute(self::TAG_PRICE_ATR_DISCOUNT)) {
			$this->discount = $price->getAttribute(self::TAG_PRICE_ATR_DISCOUNT);
		} elseif ($price->getAttribute(self::TAG_PRICE_ATR_DISCOUNT_PERCENT)) {
			$this->discount = $this->price * $price->getAttribute(self::TAG_PRICE_ATR_DISCOUNT_PERCENT) / 100;
		}

		$lector = $xTerm->getTag(self::TAG_LECTOR);
		$this->lector_degrees_before = $lector->getTagContent(self::TAG_LECTOR_TAG_DEGREES_BEFORE);
		$this->lector_firstname = $lector->getTagContent(self::TAG_LECTOR_TAG_FIRSTNAME);
		$this->lector_surname = $lector->getTagContent(self::TAG_LECTOR_TAG_SURNAME);
		$this->lector_degrees_after = $lector->getTagContent(self::TAG_LECTOR_TAG_DEGREES_AFTER);
		$this->lector_description = $lector->getTagContentHtml(self::TAG_LECTOR_TAG_DESCRIPTION, self::CONDITION_LECTOR_DESCRIPTION);
		$this->lector_skills = $lector->getTagContentHtml(self::TAG_LECTOR_TAG_SKILLS, self::CONDITION_LECTOR_SKILLS);
		$lectorImage = $lector->getTag(self::TAG_LECTOR_TAG_IMAGE);
		$this->lector_image = $lectorImage ? $lectorImage->getAttribute(self::TAG_LECTOR_TAG_IMAGE_ATR_SRC) : '';
	}

}
