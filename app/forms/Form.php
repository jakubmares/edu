<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Forms;
use App\Forms\Controls\DateInput,
 Instante\Bootstrap3Renderer\BootstrapRenderer;
/**
 * Description of Form
 *
 * @author jakubmares
 */
class Form extends \Nette\Application\UI\Form{
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->setRenderer(new BootstrapRenderer());
	}

	/**
	 * 
	 * @param type $name
	 * @param type $label
	 * @param type $format
	 * @return DateInput
	 */
	public function addDate($name, $label = NULL , $format = 'd.m.Y'){
		$control = new DateInput($label, $format);
		return $this[$name] = $control;
	}
	
	public function setValues($values, $erase = FALSE) {
		if(is_subclass_of($values, 'App\Model\So\SmartObject')){
			$values = $values->toArray();
		}
		return parent::setValues($values, $erase);
	}


}
