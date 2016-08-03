<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Front\Presenters;

use App\Model,
	Extensions\Sitemap;

/**
 * Description of CronPresenter
 *
 * @author jakubmares
 */
class CronPresenter extends \App\Presenters\BasePresenter {

	const
			IMAGES = 30;

	/** @var Model\ImportManager */
	private $importMan;

	/** @var Model\CourseManager */
	private $courseMan;

	/** @var Model\ArticleManager */
	private $articleMan;

	/** @var  Model\PersonalityManager */
	private $personalityMan;

	/** @var Model\CategoryManager */
	private $categoryMan;

	public function __construct(Model\ImportManager $importMan, Model\CourseManager $courseMan, Model\ArticleManager $articleMan,
			Model\PersonalityManager $personalityMan, Model\CategoryManager $categoryMan) {
		$this->importMan = $importMan;
		$this->courseMan = $courseMan;
		$this->articleMan = $articleMan;
		$this->personalityMan = $personalityMan;
		$this->categoryMan = $categoryMan;
		parent::__construct();
	}

	public function actionImport() {
		$this->importMan->importCompanyCourses();
		$this->terminate();
	}

	public function actionImages() {
		$this->courseMan->importExternalImages(self::IMAGES);
		$this->terminate();
	}

	public function actionDelete() {
		$this->courseMan->deleteUnusedImages();
		//$this->terminate();
	}

	public function actionSitemap() {
		$sitemap = Sitemap::create();
		$sitemap->addUrl($this->link('//Homepage:default'), Sitemap::CHANGEFREQ_DAILY, 0.9);
		$sitemap->addUrl($this->link('//Homepage:forDownload'), Sitemap::CHANGEFREQ_MONTHLY);
		$sitemap->addUrl($this->link('//Homepage:partners'), Sitemap::CHANGEFREQ_MONTHLY);
		$sitemap->addUrl($this->link('//Article:articles'), Sitemap::CHANGEFREQ_WEEKLY);
		$sitemap->addUrl($this->link('//Article:interviews'), Sitemap::CHANGEFREQ_WEEKLY);

		$sitemap->addUrl($this->link('//Company:default'), Sitemap::CHANGEFREQ_WEEKLY);
		$sitemap->addUrl($this->link('//Course:default'), Sitemap::CHANGEFREQ_DAILY);
		$sitemap->addUrl($this->link('//Course:lastminute'), Sitemap::CHANGEFREQ_DAILY, 0.9);
		$sitemap->addUrl($this->link('//Personality:personalityList'), Sitemap::CHANGEFREQ_WEEKLY);

		//Articles
		foreach ($this->articleMan->getActiveArticles() as $article) {
			$sitemap->addUrl($this->link('//Article:article', $article->seokey), Sitemap::CHANGEFREQ_MONTHLY);
		}
		//Interviews
		foreach ($this->articleMan->getActiveInterviews() as $interview) {
			$sitemap->addUrl($this->link('//Article:interview', $interview->seokey), Sitemap::CHANGEFREQ_MONTHLY);
		}
		//Companies
		foreach ($this->companyMan->getActive() as $company) {
			$sitemap->addUrl($this->link('//Company:company', $company->seokey), Sitemap::CHANGEFREQ_MONTHLY);
		}
		//Courses
		foreach ($this->courseMan->getActive() as $course) {
			$sitemap->addUrl($this->link('//Course:course', $course->seokey), Sitemap::CHANGEFREQ_DAILY, 0.8);
		}

		//Personality
		foreach ($this->personalityMan->getActive() as $personality) {
			$sitemap->addUrl($this->link('//Personality:personality', $personality->seokey), Sitemap::CHANGEFREQ_MONTHLY);
		}

		//Focuses
		//category
		foreach ($this->categoryMan->getActive() as $category) {
			$sitemap->addUrl($this->link('//Course:category', $category->seokey), Sitemap::CHANGEFREQ_DAILY, 0.7);
			foreach ($category->getActiveFocuses() as $focus) {
				$sitemap->addUrl($this->link('//Course:focus', [$category->seokey, $focus->seokey]), Sitemap::CHANGEFREQ_DAILY, 0.6);
			}
		}


//		Header('Content-type: text/xml');
//		echo $sitemap->asXML();
		$sitemap->asXML('sitemap.xml');
		$this->terminate();
	}

}
