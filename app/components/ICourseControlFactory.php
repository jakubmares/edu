<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Components;
/**
 *
 * @author jakubmares
 */
interface ICourseControlFactory {

	/**
	 * @return CoursesControl
	 */
	public function create();
}
