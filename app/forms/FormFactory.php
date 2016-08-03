<?php

namespace App\Forms;

use Nette;
use App\Forms\Form;


abstract class FormFactory extends Nette\Object
{

	/**
	 * @return Form
	 */
	public function create()
	{
		return new Form;
	}

}
