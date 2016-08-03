<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Date
 *
 * @author jakubmares
 */

namespace Extensions;

use Nette\Utils\DateTime;

class Date extends DateTime {

	const ATOM_DATE = 'Y-m-d';

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->format(self::ATOM_DATE);
	}

}
