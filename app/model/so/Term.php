<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

use App\Model;
/**
 * Description of Term
 *
 * @author jakubmares
 */
class Term extends SmartObject implements IAddress {

	const
			RELATION_COURSE = 'course',
			LAST_MINUTE_INTERVAL = '21D',
			FLAG_ADDRESS_DEFAULT = 0,
			FLAG_ADDRESS_ONLINE = 'online',
			FLAG_PRICE_CUSTOM = 'custom',
			FLAG_PRICE_DEFAULT = 0;

	private $id;
	private $courseId;
	private $active;
	private $noterm;
	private $from;
	private $to;
	//Price
	private $price;
	private $vat;
	private $discount;
	private $flag;
	private $currency;
	private $priceFlag;
	//Address
	private $city;
	private $street;
	private $registryNumber;
	private $houseNumber;
	private $zip;
	private $addressNote;
	private $latitude;
	private $longitude;
	private $addressFlag;
	private $countryKey;
	//Lector
	public $lectorFirstname;
	public $lectorSurname;
	public $lectorDegreesBefore;
	public $lectorDegreesAfter;
	public $lectorDescription;
	public $lectorSkills;
	public $lectorImage;

	public function getId() {
		return $this->id;
	}

	public function getCourseId() {
		return $this->courseId;
	}

	public function getActive() {
		return $this->active;
	}

	public function getNoterm() {
		return $this->noterm;
	}

	/**
	 * 
	 * @return \Nette\Utils\DateTime
	 */
	public function getFrom() {
		return $this->from;
	}

	/**
	 * 
	 * @return \Nette\Utils\DateTime
	 */
	public function getTo() {
		return $this->to;
	}

	public function getPrice() {
		return $this->price;
	}

	public function getVat() {
		return $this->vat;
	}

	public function getDiscount() {
		return $this->discount;
	}

	public function getFlag() {
		return $this->flag;
	}

	public function getCurrency() {
		return $this->currency;
	}

	public function getPriceFlag() {
		return $this->priceFlag;
	}

	public function getCity() {
		return $this->city;
	}

	public function getStreet() {
		return $this->street;
	}

	public function getRegistryNumber() {
		return $this->registryNumber;
	}

	public function getHouseNumber() {
		return $this->houseNumber;
	}

	public function getZip() {
		return $this->zip;
	}

	public function getAddressNote() {
		return $this->addressNote;
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function getAddressFlag() {
		return $this->addressFlag;
	}

	public function getLectorFirstname() {
		return $this->lectorFirstname;
	}

	public function getLectorSurname() {
		return $this->lectorSurname;
	}

	public function getLectorDegreesBefore() {
		return $this->lectorDegreesBefore;
	}

	public function getLectorDegreesAfter() {
		return $this->lectorDegreesAfter;
	}

	public function getLectorDescription() {
		return $this->lectorDescription;
	}

	public function getLectorSkills() {
		return $this->lectorSkills;
	}

	public function getLectorImage() {
		return $this->lectorImage;
	}

	public function getCountryKey() {
		return $this->countryKey;
	}

	public function getLector() {
		return $this->lectorSurname?sprintf('%s %s %s %s', $this->lectorDegreesBefore, $this->lectorFirstname, $this->lectorSurname, $this->lectorDegreesAfter):'';
	}

	public function getNoDiscountPrice() {
		return $this->countPriceVat($this->price);
	}

	public function getTotalPrice() {
		return $this->countPriceVat($this->price - $this->discount);
	}

	public function isPriceCustom() {
		return $this->priceFlag == self::FLAG_PRICE_CUSTOM;
	}

	public function getPriceFlagLabel() {
		$labels = self::flagsPrice();
		return isset($labels[$this->priceFlag]) ? $labels[$this->priceFlag] : $labels[self::FLAG_PRICE_DEFAULT];
	}

	public function getAddressFlagLabel() {
		$labels = self::flagsAddress();
		return isset($labels[$this->addressFlag]) ? $labels[$this->addressFlag] : $labels[self::FLAG_ADDRESS_DEFAULT];
	}

	/**
	 * 
	 * @return Course
	 */
	public function getCourse() {
		return $this->getRef(Model\CourseManager::TABLE_NAME);
	}

	public function getName() {
		return $this->getCourse()->getName();
	}

	public function getStreetAd() {
		$numbers = [];
		if ($this->registryNumber) {
			$numbers[] = $this->registryNumber;
		}
		if ($this->houseNumber) {
			$numbers[] = $this->houseNumber;
		}
		$number = implode('/', $numbers);
		return sprintf('%s %s', $this->street, $number);
	}

	public function getAddress() {
		$numbers = [];
		if ($this->registryNumber) {
			$numbers[] = $this->registryNumber;
		}
		if ($this->houseNumber) {
			$numbers[] = $this->houseNumber;
		}
		$number = implode('/', $numbers);
		return sprintf('%s %s, %s', $this->street, $number, $this->city);
	}

	/**
	 * 
	 * @return \DateInterval
	 */
	public static function getLastminuteInterval() {
		return new \DateInterval("P" . self::LAST_MINUTE_INTERVAL);
	}

	private function countPriceVat($price) {
		return ($price) * (1 + ($this->vat / 100));
	}

	public static function flagsPrice() {
		return [self::FLAG_PRICE_DEFAULT => 'Pevná cena', self::FLAG_PRICE_CUSTOM => 'Cena dohodou'];
	}

	public static function flagsAddress() {
		return [self::FLAG_ADDRESS_DEFAULT => 'Běžný kurz', self::FLAG_ADDRESS_ONLINE => 'Online kurz'];
	}

	public function isAllowToOrder() {
		$now = new \Nette\Utils\DateTime();

		return ($this->getFrom() > $now && $this->getActive()) ? true : false;
	}

}
