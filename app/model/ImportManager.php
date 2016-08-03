<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette\Utils\DateTime,
	Nette\Database\Context,
	Extensions\ESimpleXmlElement,
	App\Model\Po\Course,
	App\Model\Po\Term,
	Tracy\Debugger;

/**
 * Description of ImportManager
 *
 * @author jakubmares
 */
class ImportManager extends BaseManager {

	const
			TABLE_NAME = 'import',
			TABLE_COLUMN_IMPORT_DATE = 'import_date',
			TABLE_COLUMN_COMPANY_ID = 'company_id',
			TABLE_COLUMN_ID = 'id',
			TABLE_COLUMN_LOG = 'log',
			TABLE_COLUMN_EXEC_NOTE = 'exec_note',
			IMPORT_TAG_COURSES = 'courses',
			IMPORT_TAG_COURSE = 'course',
			IMPORT_TAG_COURSE_TAG_TERM = 'term',
			IMPORT_TAG_COURSE_TAG_NOTERM = 'noterm',
			IMPORT_TAG_COURSE_FOCUS = 'focus',
			IMPORT_TAG_COURSE_FOCUS_COUNT = 2,
			IMPORT_TAG_COURSE_IMAGE = 'image',
			IMPORT_TAG_COURSE_IMAGE_COUNT = 1,
			IMPORT_TAG_COURSE_VIDEO = 'video',
			IMPORT_TAG_COURSE_VIDEO_COUNT = 1,
			IMPORT_TAG_COURSE_KEYWORD = 'keyword',
			IMPORT_TAG_COURSE_KEYWORD_COUNT = 20,
			IMPORT_TAG_COURSE_LEVEL = 'level',
			IMPORT_TAG_COURSE_LEVEL_COUNT = 3;

	/** @var CourseManager */
	private $courseMan;

	/** @var TermManager */
	private $termMan;

	/** @var CompanyManager */
	private $companyMan;

	/** @var LanguageManager */
	private $languageMan;

	/** @var CourseKeywordManager */
	private $courseKeywordMan;

	/** @var CourseFocusManager */
	private $courseFocusMan;

	/** @var int */
	private $defaultLanguageId;

	/** @var string */
	private $defaultCurrency;

	/** @var string */
	private $defaultCountryKey;

	/** @var Po\ImportReport */
	private $importReport;

	/** @var CourseImageManager */
	private $imageMan;

	/** @var CourseVideoManager */
	private $videoMan;

	/** @var CourseLevelManager */
	private $levelMan;

	public function __construct(Context $database, CourseManager $courseMan, TermManager $termMan, CompanyManager $companyMan,
			LanguageManager $languageMan, CourseKeywordManager $courseKeywordMan, CurrencyManager $currencyMan, CountryManager $countryMan,
			CourseFocusManager $courseFocusMan, CourseImageManager $imageMan, CourseVideoManager $videoMan, CourseLevelManager $levelMan) {
		parent::__construct($database);
		$this->courseMan = $courseMan;
		$this->termMan = $termMan;
		$this->companyMan = $companyMan;
		$this->languageMan = $languageMan;
		$this->courseKeywordMan = $courseKeywordMan;
		$this->defaultLanguageId = $languageMan->getDefaultLanguageId();
		$this->importReport = new Po\ImportReport();
		$this->defaultCurrency = $currencyMan->getDefaultCurrency();
		$this->defaultCountryKey = $countryMan->getDefaultCountryKey();
		$this->courseFocusMan = $courseFocusMan;
		$this->imageMan = $imageMan;
		$this->videoMan = $videoMan;
		$this->levelMan = $levelMan;
	}

	public function importCompanyCourses() {
		$company = $this->companyMan->getForImport();
		if ($company) {
			$now = new DateTime();
			$data = new \Nette\Utils\ArrayHash();
			$data->offsetSet(CompanyManager::COLUMN_IMAPORT_AT, $now);
			$this->companyMan->update($company->getId(), $data);
			$this->importCourses($company->getImportUrl(), $company->getId());
		}
	}

	protected function importCourses($url, $companyId) {
		try {
			Debugger::timer('time');
			Debugger::timer('parse');
			$xml = ESimpleXmlElement::loadXml($url);
			$parseTime = Debugger::timer('parse');
			$this->importReport->setParseXmlTime($parseTime);
			if (!$xml) {
				throw new Exception(sprintf('Nepodarilo se zpracovat soubor pro import. Id firmy: %s', $companyId));
			}

			$this->courseMan->deactivateCompanyCourses($companyId);

			foreach ($xml->{self::IMPORT_TAG_COURSE} as $xCourse) {
				$this->importCourse($companyId, $xCourse);
			}
			$time = Debugger::timer('time');
			$this->importReport->setTime($time);
			$this->insert($this->importReport->arrayForInsert($companyId));
		} catch (Exception $ex) {
			$this->importReport->setException($ex);
			$this->insert($this->importReport->arrayForInsert($companyId));
			throw $ex;
		}
	}

	protected function importCourse($companyId, ESimpleXmlElement $xCourse) {
		$courseIn = new Course($companyId, $xCourse, $this->defaultLanguageId);
		$course = $this->courseMan->getCourseByExternalId($companyId, $courseIn->external_id);

		//Ulozit kurz
		if ($course) {
			$this->courseMan->update($course->getId(), $courseIn);
			$courseId = $course->getId();
			$this->courseMan;
			$this->importReport->addCourseUpdate();
		} else {
			$courseRow = $this->courseMan->insertCourse($courseIn);
			$courseId = $courseRow->offsetGet(CourseManager::COLUMN_ID);
			$this->importReport->addCourseImport();
		}

		$this->insertFocuses($courseId, $xCourse);
		$this->insertImages($courseId, $xCourse);
		$this->insertVideos($courseId, $xCourse);
		$this->insertKeywords($courseId, $xCourse);
		$this->insertLevels($courseId, $xCourse);

		if (!$this->insetrCourseTerms($courseId, $xCourse)) {
			$this->insertCourseNoterm($courseId, $xCourse);
		}
	}

	private function insertVideos($courseId, ESimpleXmlElement $xCourse) {
		$videos = [];
		foreach ($xCourse->{self::IMPORT_TAG_COURSE_VIDEO} as $xVideo) {
			if (count($videos) > self::IMPORT_TAG_COURSE_VIDEO_COUNT) {
				break;
			}
			$videos[] = new Po\CourseVideo($courseId, $xVideo);
		}
		$this->videoMan->deleteByCourseId($courseId);
		$this->videoMan->insert($videos);
	}

	private function insertImages($courseId, ESimpleXmlElement $xCourse) {
		$images = [];
		foreach ($xCourse->{self::IMPORT_TAG_COURSE_IMAGE} as $xImage) {
			if (count($images) > self::IMPORT_TAG_COURSE_IMAGE_COUNT) {
				break;
			}
			$images[] = new Po\CourseImage($courseId, $xImage);
			break;
		}
		$this->imageMan->deleteByCourseId($courseId);
		$this->imageMan->insert($images);
	}

	private function insertFocuses($courseId, ESimpleXmlElement $xCourse) {
		$focuses = [];
		foreach ($xCourse->{self::IMPORT_TAG_COURSE_FOCUS} as $xFocus) {
			if (count($focuses) > self::IMPORT_TAG_COURSE_FOCUS_COUNT) {
				break;
			}
			$focuses[] = new Po\CourseFocus($courseId, $xFocus);
		}
		$this->courseFocusMan->deleteByCourseId($courseId);
		try {
			$this->courseFocusMan->insert($focuses);
		} catch (\Nette\Database\UniqueConstraintViolationException $ex) {
			$this->importReport->addException($ex);
		} catch (Exception $exc) {
			throw $exc;
		}
	}

	private function insertKeywords($courseId, ESimpleXmlElement $xCourse) {
		$keywords = [];
		/* @var $xml ESimpleXmlElement */
		foreach ($xCourse->{self::IMPORT_TAG_COURSE_KEYWORD} as $xml) {
			if (count($keywords) > self::IMPORT_TAG_COURSE_KEYWORD_COUNT) {
				break;
			}
			$keywords[] = new Po\CourseKeyword($courseId, $xml);
		}
		$this->courseKeywordMan->deleteCourseKeywords($courseId);
		$this->courseKeywordMan->insert($keywords);
	}

	private function insertLevels($courseId, ESimpleXmlElement $xCourse) {
		$levels = [];
		/* @var $xml ESimpleXmlElement */
		foreach ($xCourse->{self::IMPORT_TAG_COURSE_LEVEL} as $xml) {
			if (count($levels) > self::IMPORT_TAG_COURSE_LEVEL_COUNT) {
				break;
			}
			$levels[] = new Po\CourseLevel($courseId, $xml);
		}
		$this->levelMan->deleteByCourseId($courseId);
		$this->levelMan->insert($levels);
	}

	private function insetrCourseTerms($courseId, ESimpleXmlElement $xCourse) {

		$this->termMan->deleteExternalTermsByCourseId($courseId);

		$data = [];
		/* @var $xTerm ESimpleXmlElement */
		foreach ($xCourse->{self::IMPORT_TAG_COURSE_TAG_TERM} as $xTerm) {
			$data[] = new Term($courseId, $xTerm, $this->defaultCountryKey, $this->defaultCurrency);
			$this->importReport->addTermImport();
		}

		return $this->termMan->insertCourseTerms($data);
	}

	private function insertCourseNoterm($courseId, ESimpleXmlElement $xCourse) {
		$this->termMan->deleteExternalTermsByCourseId($courseId);
		$notermIn = new Term($courseId, $xCourse->{self::IMPORT_TAG_COURSE_TAG_NOTERM}, $this->defaultCountryKey, $this->defaultCurrency, true);
		$this->importReport->addNotermImport();
		$this->termMan->deleteExternalTermsByCourseId($courseId);
		return $this->termMan->insert($notermIn);
	}

	public function insert($values) {
		$values->offsetSet(self::TABLE_COLUMN_IMPORT_DATE, new DateTime);
		return parent::insert($values);
	}

}
