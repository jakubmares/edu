<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Components;
use Nette\Application\UI\Control,
	App\Model\CourseManager,
	App\Model\TermManager;
/**
 * Description of CoursesControl
 *
 * @author jakubmares
 */
class CoursesControl extends Control{
	
	/** @var CourseManager */
	private $courseMan;
	
	/** @var TermManager */
	private $termMan;
	
	public function __construct(CourseManager $courseMan, TermManager $termMan) {
		$this->courseMan = $courseMan;
		$this->termMan = $termMan;
	}

	
	public function render(){
		$template = $this->template;
    $template->setFile(__DIR__ . '/templates/courses.latte');
    // vložíme do šablony nějaké parametry
    //$template->param = $value;
    // a vykreslíme ji
    $template->render();
	}
}
