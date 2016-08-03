<?php

namespace App\Module\Admin\Presenters;

use Nette;
use App\Model;


class HomepagePresenter extends BasePresenter
{
	
	/** @var Model\CourseManager @inject */
	public $courseMan;
	
	/** @var Model\TermManager @inject */
	public $termMan;
	
	/** @var Model\QuestionManager @inject */
	public $questionMan;
	
	/** @var Model\OrderManager @inject */
	public $orderMan;
	
	/** @var Model\PersonalityManager @inject */
	public $personalityMan;
	/** @var Model\ArticleManager @inject */
	public $articleMan;

	public function renderDefault()
	{
		$this->template->usersCount = $this->userMan->count();
		$this->template->usersActiveCount = $this->userMan->countActive();
		$this->template->companyCount = $this->companyMan->count();
		$this->template->companyActiveCount = $this->companyMan->countActive();
		
		$this->template->courseCount = $this->courseMan->count();
		$this->template->courseActiveCount = $this->courseMan->countActive();
		$this->template->termCount = $this->termMan->count();
		$this->template->termActiveCount = $this->termMan->countActive();
		$this->template->questionCount = $this->questionMan->count();
		$this->template->orderCount = $this->orderMan->count();
		
		$this->template->personalityCount = $this->personalityMan->count();
		$this->template->personalityActiveCount = $this->personalityMan->countActive();
		
		$this->template->articleCount = $this->articleMan->countAdricles();
		$this->template->articleActiveCount = $this->articleMan->countActiveArticles();
		
		$this->template->interviewCount = $this->articleMan->countInterviews();
		$this->template->interviewActiveCount = $this->articleMan->countActiveInterviews();
		
	}

}
