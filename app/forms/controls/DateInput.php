<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author jakubmares
 */

namespace App\Forms\Controls;

use Nette\Forms\Controls\BaseControl,
	Nette\Forms\Form,
	Nette\Utils\Html,
	Nette\Utils\DateTime;

class DateInput extends BaseControl {

	/** @var DateTime|null */
	private $date;
	private $format;
	private $atributes = ['class' => 'form-control date'];

	public function __construct($label = NULL, $format = 'd.m.Y') {
		parent::__construct($label);
		$this->format = $format;
	}

	public function setValue($value) {
		$this->date = $value ? DateTime::from($value) : null;
	}

	/**
	 * @return DateTime|NULL
	 */
	public function getValue() {
		return $this->date;
	}

	public function setAttribute($name, $value = TRUE) {
		$this->atributes[$name] = isset($this->atributes[$name]) ? $this->atributes[$name] . ' ' . $value : $value;
		return $this;
	}

	public function loadHttpData() {
		$value = $this->getHttpData(Form::DATA_LINE, '[date]');
		$this->date = $value ? DateTime::from($value) : null;
	}

	/**
	 * Generates control's HTML element.
	 */
	public function getControl() {
		$name = $this->getHtmlName();
		$value = $this->date ? $this->date->format($this->format) : '';
		$input = Html::el('input')->addAttributes($this->atributes)
				->name($name . '[date]')
				->id($this->getHtmlId())
				->value($value);
		$group = Html::el('div')->addAttributes(['class'=>'input-group']);
		$in = Html::el()->add($input);
		$addon = Html::el('div')->addAttributes(['class'=>'input-group-addon']);
		$addon->add(Html::el('span')->addAttributes(['class'=>'glyphicon glyphicon-calendar']));
		$group->add($addon);
		$group->add($in);
		return $group;
	}

}
