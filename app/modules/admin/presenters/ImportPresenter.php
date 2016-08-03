<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model,
	Nette\Database;

/**
 * Description of importPresenter
 *
 * @author jakubmares
 */
class ImportPresenter extends BasePresenter {

	/** @var Database\Context */
	private $db1;

	/** @var Model\Db2 */
	private $db2;

	public function __construct(Database\Context $db1, Model\Db2 $db2) {
		$this->db1 = $db1;
		$this->db2 = $db2;
	}

	private function dealerIdTrans($id) {
		$trans = [3 => 2, 5 => 3];
		return isset($trans[$id]) ? $trans[$id] : null;
	}

	public function actionCompany() {
		$compSel = $this->db2->table('user')->where('type', 2);

		$this->db1->beginTransaction();
		try {
			foreach ($compSel as $row) {
				$user = [
					Model\UserManager::COLUMN_FIRSTNAME => '',
					Model\UserManager::COLUMN_SURNAME => $row->company_name,
					Model\UserManager::COLUMN_EMAIL => $row->email,
					Model\UserManager::COLUMN_PASSWORD_HASH => $row->password,
					Model\UserManager::COLUMN_ACTIVE => $row->active,
					Model\UserManager::COLUMN_CREAED_AT => $row->created_at
				];
				$nUser = $this->db1->table(Model\UserManager::TABLE_NAME)->insert($user);
				$userId = $nUser->id;

				$this->db1->table(Model\UserRoleManager::TABLE_NAME)->insert([Model\UserRoleManager::COLUMN_USER_ID => $userId, Model\UserRoleManager::COLUMN_ROLE_ID => 2]);

				$potencial = null;
				$status = null;
				$type = null;
				$notice = null;
				$dealerId = null;

				$noteRow = $this->db2->table('notebook')->where('ident', $row->ident)->fetch();
				if ($row->ident && $noteRow) {
					$potencial = $noteRow->potencial;
					$status = $noteRow->status;
					$type = $noteRow->typesub;
					$notice = $noteRow->notice;
					$dealerId = $this->dealerIdTrans($noteRow->dealer);
				}

				$company = [
					Model\CompanyManager::COLUMN_ID => $row->id,
					Model\CompanyManager::COLUMN_NAME => $row->company_name,
					Model\CompanyManager::COLUMN_SEOKEY => $row->seokey,
					Model\CompanyManager::COLUMN_IC => $row->ident,
					Model\CompanyManager::COLUMN_DIC => $row->dic,
					Model\CompanyManager::COLUMN_DESCRIPTION => $row->description,
					Model\CompanyManager::COLUMN_ACTIVE => $row->active,
					Model\CompanyManager::COLUMN_USER_ID => $userId,
					Model\CompanyManager::COLUMN_CREATED_AT => $row->created_at,
					Model\CompanyManager::COLUMN_DEALER_ID => $dealerId,
					Model\CompanyManager::COLUMN_LOGO => $row->image ? Model\CompanyManager::IMAGE_PATH . '/' . $row->image : '',
					Model\CompanyManager::COLUMN_PARTNER => $row->partner,
					Model\CompanyManager::COLUMN_WEB => $row->web,
					Model\CompanyManager::COLUMN_IMPORT_URL => $row->import_url ? $row->import_url : '',
					Model\CompanyManager::COLUMN_TOP => $row->vip,
					Model\CompanyManager::COLUMN_POTENCIAL => $potencial ? $potencial : Model\So\Company::POTENCIAL_UNDEFINED,
					Model\CompanyManager::COLUMN_TYPE => $type ? $type : Model\So\Company::TYPE_OTHERS,
					Model\CompanyManager::COLUMN_STATUS => $status ? $status : Model\So\Company::STATUS_ADVERTISE,
					Model\CompanyManager::COLUMN_BANK_ACCOUNT => $row->bank_account,
					Model\CompanyManager::COLUMN_NOTICE => $notice ? $notice : ''
				];
				$nCompany = $this->db1->table(Model\CompanyManager::TABLE_NAME)->insert($company);
				$companyId = $nCompany->id;
				$contacts = [];
				if ($noteRow) {
					foreach ($this->db2->table('notebook_person')->where('notebook_id', $noteRow->id) as $noPer) {
						$contacts[] = [
							Model\ContactManager::COLUMN_EMAIL => $noPer->email,
							Model\ContactManager::COLUMN_TYPE => Model\So\Contact::TYPE_CONTACT_PERSON,
							Model\ContactManager::COLUMN_COMPANY_ID => $companyId,
							Model\ContactManager::COLUMN_NAME => $noPer->name,
							Model\ContactManager::COLUMN_FUNCTION => $noPer->function,
							Model\ContactManager::COLUMN_PHONE => $noPer->phone,
							Model\ContactManager::COLUMN_EX_ID => $noPer->id
						];
					}
				}

				$contacts[] = [
					Model\ContactManager::COLUMN_EMAIL => $row->email,
					Model\ContactManager::COLUMN_TYPE => Model\So\Contact::TYPE_CONTACT_PERSON,
					Model\ContactManager::COLUMN_COMPANY_ID => $row->id,
					Model\ContactManager::COLUMN_NAME => $row->contact_person,
				];

				$contacts[] = [
					Model\ContactManager::COLUMN_EMAIL => $row->email_invoice,
					Model\ContactManager::COLUMN_TYPE => Model\So\Contact::TYPE_ORDER,
					Model\ContactManager::COLUMN_COMPANY_ID => $row->id,
					Model\ContactManager::COLUMN_NAME => 'Objednávky',
				];

				$contacts[] = [
					Model\ContactManager::COLUMN_EMAIL => $row->email_question,
					Model\ContactManager::COLUMN_TYPE => Model\So\Contact::TYPE_QUESTION,
					Model\ContactManager::COLUMN_COMPANY_ID => $row->id,
					Model\ContactManager::COLUMN_NAME => 'Dotazy',
				];

				$contacts[] = [
					Model\ContactManager::COLUMN_EMAIL => $row->email_show,
					Model\ContactManager::COLUMN_TYPE => Model\So\Contact::TYPE_SHOW,
					Model\ContactManager::COLUMN_COMPANY_ID => $row->id,
					Model\ContactManager::COLUMN_NAME => $row->company_name,
					Model\ContactManager::COLUMN_PHONE => $row->phone
				];
				foreach ($contacts as $contact) {
					$this->db1->table(Model\ContactManager::TABLE_NAME)->insert($contact);
				}

				$addresses = [];
				$addresses[] = [
					Model\AddressManager::COLUMN_STREET => $row->address_invoice,
					Model\AddressManager::COLUMN_COMPANY_ID => $companyId,
					Model\AddressManager::COLUMN_TYPE => Model\So\Address::TYPE_BILLING,
					Model\AddressManager::COLUMN_COUNTRY_KEY => 'CZ'
				];

				$addresses[] = [
					Model\AddressManager::COLUMN_STREET => $row->address,
					Model\AddressManager::COLUMN_COMPANY_ID => $companyId,
					Model\AddressManager::COLUMN_TYPE => Model\So\Address::TYPE_BASE,
					Model\AddressManager::COLUMN_COUNTRY_KEY => 'CZ'
				];
				foreach ($addresses as $address) {
					$this->db1->table(Model\AddressManager::TABLE_NAME)->insert($addresses);
				}
			}
			$this->db1->commit();
		} catch (\Exception $e) {
			$this->db1->rollBack();
			throw $e;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionRepireico() {
		$this->db1->beginTransaction();
		try {

			foreach ($this->db1->table(Model\CompanyManager::TABLE_NAME)->where('ic <> 0') as $compRow) {
				$ic = $compRow->ic;
				if (strlen($ic) < 8) {
					for ($index = strlen($ic); $index < 8; $index++) {
						$ic = '0' . $ic;
					}
				}
				$data = [Model\CompanyManager::COLUMN_IC => $ic];
				$this->db1->table(Model\CompanyManager::TABLE_NAME)->where(Model\CompanyManager::COLUMN_ID, $compRow->id)->update($data);
			}
			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionRepireicoold() {
		$this->db2->getDb()->beginTransaction();
		try {

			foreach ($this->db2->table('notebook')->where('ident <> 0') as $compRow) {
				$ic = $compRow->ident;
				if (strlen($ic) < 8) {
					for ($index = strlen($ic); $index < 8; $index++) {
						$ic = '0' . $ic;
					}
					$data = ['ident' => $ic];
					$this->db2->table('notebook')->where('id', $compRow->id)->update($data);
				}
			}
			$this->db2->getDb()->commit();
		} catch (Exception $exc) {
			$this->db2->getDb()->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionNotes() {
		$this->db1->beginTransaction();
		try {
			foreach ($this->db1->table(Model\CompanyManager::TABLE_NAME)->where('ic <> 0') as $compRow) {
				$notes = [];
				$db2 = $this->db2->getDb();
				foreach ($db2->query('SELECT com.* FROM notebook_comment com JOIN notebook n ON n.id = com.notebook_id WHERE n.ident = ?', $compRow->ic)->fetchAll() as $noCo) {
					$cont = $this->db1->table(Model\ContactManager::TABLE_NAME)->where(Model\ContactManager::COLUMN_EX_ID, $noCo->id)->fetch();
					$contId = $cont ? $cont->id : null;

					$notes[] = [
						Model\NoteManager::COLUMN_COMPANY_ID => $compRow->id,
						Model\NoteManager::COLUMN_USER_ID => $this->dealerIdTrans($noCo->dealer),
						Model\NoteManager::COLUMN_CREATED_AT => $noCo->created_at,
						Model\NoteManager::COLUMN_CONTACT_AT => $noCo->dt_contact,
						Model\NoteManager::COLUMN_NEXT_CONTACT_AT => $noCo->next_contact,
						Model\NoteManager::COLUMN_NOTE => $noCo->descr,
						Model\NoteManager::COLUMN_CONTACT_NOTE => $noCo->occasion,
						Model\NoteManager::COLUMN_CONTACT_ID => $contId,
						Model\NoteManager::COLUMN_DONE => $noCo->finished
					];
				}

				if (!empty($notes)) {
					$this->db1->table(Model\NoteManager::TABLE_NAME)->insert($notes);
				}
			}
			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionNotecompany() {
		$icos = [];
		foreach ($this->db1->table(Model\CompanyManager::TABLE_NAME)->where('ic <> 0')->fetchPairs('id', 'ic') as $ic) {
			$icos[] = $ic;
		}
		$this->db1->beginTransaction();
		try {
			foreach ($this->db2->table('notebook')->where('ident NOT', $icos) as $notRow) {

				$company = [
					Model\CompanyManager::COLUMN_NAME => $notRow->name,
					Model\CompanyManager::COLUMN_SEOKEY => \Nette\Utils\Strings::webalize($notRow->name),
					Model\CompanyManager::COLUMN_IC => $notRow->ident,
					Model\CompanyManager::COLUMN_DIC => null,
					Model\CompanyManager::COLUMN_DESCRIPTION => '',
					Model\CompanyManager::COLUMN_ACTIVE => false,
					Model\CompanyManager::COLUMN_USER_ID => null,
					Model\CompanyManager::COLUMN_CREATED_AT => $notRow->created_at,
					Model\CompanyManager::COLUMN_DEALER_ID => $this->dealerIdTrans($notRow->dealer),
					Model\CompanyManager::COLUMN_LOGO => '',
					Model\CompanyManager::COLUMN_PARTNER => false,
					Model\CompanyManager::COLUMN_WEB => $notRow->web,
					Model\CompanyManager::COLUMN_IMPORT_URL => '',
					Model\CompanyManager::COLUMN_TOP => false,
					Model\CompanyManager::COLUMN_POTENCIAL => $notRow->potencial ? $notRow->potencial : Model\So\Company::POTENCIAL_UNDEFINED,
					Model\CompanyManager::COLUMN_TYPE => $notRow->typesub ? $notRow->typesub : Model\So\Company::TYPE_OTHERS,
					Model\CompanyManager::COLUMN_STATUS => $notRow->status ? $notRow->status : Model\So\Company::STATUS_ADVERTISE,
					Model\CompanyManager::COLUMN_BANK_ACCOUNT => '',
					Model\CompanyManager::COLUMN_NOTICE => $notRow->notice
				];
				$nCompany = $this->db1->table(Model\CompanyManager::TABLE_NAME)->insert($company);
				$companyId = $nCompany->id;

				$address = [
					Model\AddressManager::COLUMN_STREET => $notRow->address,
					Model\AddressManager::COLUMN_COMPANY_ID => $companyId,
					Model\AddressManager::COLUMN_TYPE => Model\So\Address::TYPE_BASE,
					Model\AddressManager::COLUMN_COUNTRY_KEY => 'CZ'
				];
				$this->db1->table(Model\AddressManager::TABLE_NAME)->insert($address);


				$contacts = [];
				foreach ($this->db2->table('notebook_person')->where('notebook_id', $notRow->id) as $noPer) {
					$contacts[] = [
						Model\ContactManager::COLUMN_EMAIL => $noPer->email,
						Model\ContactManager::COLUMN_TYPE => Model\So\Contact::TYPE_CONTACT_PERSON,
						Model\ContactManager::COLUMN_COMPANY_ID => $companyId,
						Model\ContactManager::COLUMN_NAME => $noPer->name,
						Model\ContactManager::COLUMN_FUNCTION => $noPer->function,
						Model\ContactManager::COLUMN_PHONE => $noPer->phone,
						Model\ContactManager::COLUMN_EX_ID => $noPer->id
					];
				}

				foreach ($contacts as $contact) {
					$this->db1->table(Model\ContactManager::TABLE_NAME)->insert($contact);
				}
			}
			$this->db1->commit();
		} catch (Exception $ex) {
			$this->db1->rollBack();
			throw $ex;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionCategory() {
		$this->db1->beginTransaction();
		try {
			foreach ($this->db2->table('category_group') as $cat) {
				$nCat = [
					Model\CategoryManager::COLUMN_ID => $cat->id,
					Model\CategoryManager::COLUMN_ACTIVE => $cat->active,
					Model\CategoryManager::COLUMN_NAME => $cat->title,
					Model\CategoryManager::COLUMN_SEOKEY => $cat->seokey,
					Model\CategoryManager::COLUMN_POSITION => $cat->position
				];
				$rCat = $this->db1->table(Model\CategoryManager::TABLE_NAME)->insert($nCat);
				$nFoc = [];
				foreach ($this->db2->table('category')->where('category_group_id', $rCat->id) as $foc) {
					$nFoc[] = [
						Model\FocusManager::COLUMN_ID => $foc->id,
						Model\FocusManager::COLUMN_ACTIVE => $foc->active,
						Model\FocusManager::COLUMN_CATEGORY_ID => $rCat->id,
						Model\FocusManager::COLUMN_NAME => $foc->name,
						Model\FocusManager::COLUMN_POSITION => $foc->position,
						Model\FocusManager::COLUMN_SEOKEY => \Nette\Utils\Strings::webalize($foc->name)
					];
				}
				$this->db1->table(Model\FocusManager::TABLE_NAME)->insert($nFoc);
			}
			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionNews() {
		$this->db1->beginTransaction();
		try {
			foreach ($this->db2->table('news') as $new) {
				$arti[] = [
					Model\ArticleManager::COLUMN_ACTIVE => $new->active,
					Model\ArticleManager::COLUMN_AUTHOR => $new->author,
					Model\ArticleManager::COLUMN_CONTENT => $new->content,
					Model\ArticleManager::COLUMN_CREATED_AT => $new->created_at,
					Model\ArticleManager::COLUMN_IMAGE => '',
					Model\ArticleManager::COLUMN_PEREX => $new->perex,
					Model\ArticleManager::COLUMN_PERSONALITY_ID => null,
					Model\ArticleManager::COLUMN_PUBLISHED_AT => $new->dt_publish,
					Model\ArticleManager::COLUMN_SEOKEY => $new->seokey,
					Model\ArticleManager::COLUMN_TITLE => $new->title,
					Model\ArticleManager::COLUMN_USER_ID => $this->user->getId()
				];
			}
			$this->db1->table(Model\ArticleManager::TABLE_NAME)->insert($arti);
			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionArticles() {
		$this->db1->beginTransaction();
		try {
			foreach ($this->db2->table('person') as $pRow) {

				$person[] = [
					Model\PersonalityManager::COLUMN_ID => $pRow->id,
					Model\PersonalityManager::COLUMN_ACTIVE => $pRow->active,
					Model\PersonalityManager::COLUMN_DEGREES_AFTER => '',
					Model\PersonalityManager::COLUMN_DEGREES_BEFORE => '',
					Model\PersonalityManager::COLUMN_DESCRIPTION => $pRow->content,
					Model\PersonalityManager::COLUMN_FRIRSTNAME => $pRow->first_name,
					Model\PersonalityManager::COLUMN_IMAGE => '/images/personality/' . $pRow->photo,
					Model\PersonalityManager::COLUMN_SEOKEY => $pRow->seokey,
					Model\PersonalityManager::COLUMN_SURNAME => $pRow->last_name
				];
			}
			$this->db1->table(Model\PersonalityManager::TABLE_NAME)->insert($person);


			foreach ($this->db2->table('article') as $row) {
				$arti = [
					Model\ArticleManager::COLUMN_ACTIVE => $row->active,
					Model\ArticleManager::COLUMN_AUTHOR => '',
					Model\ArticleManager::COLUMN_CONTENT => $row->content,
					Model\ArticleManager::COLUMN_CREATED_AT => $row->created_at,
					Model\ArticleManager::COLUMN_IMAGE => '',
					Model\ArticleManager::COLUMN_PEREX => $row->perex,
					Model\ArticleManager::COLUMN_PERSONALITY_ID => $row->person_id,
					Model\ArticleManager::COLUMN_PUBLISHED_AT => $row->dt_publish,
					Model\ArticleManager::COLUMN_SEOKEY => $row->seokey,
					Model\ArticleManager::COLUMN_TITLE => $row->title,
					Model\ArticleManager::COLUMN_USER_ID => $this->user->getId()
				];
				try {
					$this->db1->table(Model\ArticleManager::TABLE_NAME)->insert($arti);
				} catch (\Nette\Database\ForeignKeyConstraintViolationException $e) {
					$arti[Model\ArticleManager::COLUMN_PERSONALITY_ID] = null;
					$this->db1->table(Model\ArticleManager::TABLE_NAME)->insert($arti);
				}
			}


			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionBranch() {
		$this->db1->beginTransaction();
		try {
			$nAddr = [];
			$nCont = [];
			foreach ($this->db2->table('branch') as $row) {

				$nAddr[] = [
					Model\AddressManager::COLUMN_STREET => $row->address,
					Model\AddressManager::COLUMN_COMPANY_ID => $row->user_id,
					Model\AddressManager::COLUMN_TYPE => Model\So\Address::TYPE_BRANCH,
					Model\AddressManager::COLUMN_COUNTRY_KEY => 'CZ'
				];

				$nCont[] = [
					Model\ContactManager::COLUMN_EMAIL => $row->email,
					Model\ContactManager::COLUMN_TYPE => Model\So\Contact::TYPE_SHOW,
					Model\ContactManager::COLUMN_COMPANY_ID => $row->user_id,
					Model\ContactManager::COLUMN_NAME => $row->name,
					Model\ContactManager::COLUMN_FUNCTION => $row->contact_person,
					Model\ContactManager::COLUMN_PHONE => $row->phone
				];
			}
			$this->db1->table(Model\AddressManager::TABLE_NAME)->insert($nAddr);
			$this->db1->table(Model\ContactManager::TABLE_NAME)->insert($nCont);

			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionCompanyimage() {
		$this->db1->beginTransaction();
		try {
			$nRow = [];
			foreach ($this->db2->table('gallery') as $row) {
				$i = 10;
				for ($index = 1; $index <= $i; $index++) {

					$attrImg = 'img' . $index;
					$attrAlt = 'alt' . $index;

					if ($row->{$attrImg}) {
						$nRow[] = [
							Model\CompanyImageManager::COLUMN_COMPANY_ID => $row->user_id,
							Model\CompanyImageManager::COLUMN_ACTIVE => $row->active,
							Model\CompanyImageManager::COLUMN_IMG => '/images/company-img/' . $row->{$attrImg},
							Model\CompanyImageManager::COLUMN_TITLE => $row->{$attrAlt}
						];
					}
				}
			}

			$this->db1->table(Model\CompanyImageManager::TABLE_NAME)->insert($nRow);


			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionCompanyvideo() {
		$this->db1->beginTransaction();
		try {
			$nRow = [];
			foreach ($this->db2->table('video') as $row) {

				$nRow[] = [
					Model\CompanyVideoManager::COLUMN_COMPANY_ID => $row->user_id,
					Model\CompanyVideoManager::COLUMN_ACTIVE => $row->active,
					Model\CompanyVideoManager::COLUMN_VIDEO => $row->code,
					Model\CompanyVideoManager::COLUMN_TITLE => $row->title
				];
			}

			$this->db1->table(Model\CompanyVideoManager::TABLE_NAME)->insert($nRow);


			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionCompanycat() {
		$this->db1->beginTransaction();
		try {

			foreach ($this->db2->table('user_group') as $row) {
				$nRow = [
					Model\CompanyCategoryManager::COLUMN_CATEGORY_ID => $row->category_id,
					Model\CompanyCategoryManager::COLUMN_COMPANY_ID => $row->user_id
				];
				try {
					$this->db1->table(Model\CompanyCategoryManager::TABLE_NAME)->insert($nRow);
				} catch (Database\ForeignKeyConstraintViolationException $exc) {
					echo 'Firma id:' . $row->user_id . ' neexistuje.<br>';
				}
			}



			foreach ($this->db2->table('user_category') as $row2) {
				$nRow2 = [
					Model\CompanyFocusManager::COLUMN_FOCUS_ID => $row2->category_id,
					Model\CompanyFocusManager::COLUMN_COMPANY_ID => $row2->user_id
				];
				try {
					$this->db1->table(Model\CompanyFocusManager::TABLE_NAME)->insert($nRow2);
				} catch (Database\ForeignKeyConstraintViolationException $exc) {
					echo 'Firma id:' . $row->user_id . ' neexistuje.<br>';
				}
			}



			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	public function actionCourse() {
		$this->db1->beginTransaction();
		try {

			$sql = 'SELECT * FROM course c WHERE c.external_id IS NULL GROUP BY c.title , c.language';
			foreach ($this->db2->getDb()->query($sql)->fetchAll() as $row) {
				$user = $this->db2->table('user')->get($row->user_id);
				$nRow = [
					Model\CourseManager::COLUMN_ACTIVE => $row->active,
					Model\CourseManager::COLUMN_COMPANY_ID => $row->user_id,
					Model\CourseManager::COLUMN_DESCRIPTION => $row->description,
					Model\CourseManager::COLUMN_LANGUAGE_ID => $this->transLanguage($row->language),
					Model\CourseManager::COLUMN_LINK_URL => $row->link_url ? $row->link_url : '',
					Model\CourseManager::COLUMN_NAME => $row->title,
					Model\CourseManager::COLUMN_RETRAINING => 0,
					Model\CourseManager::COLUMN_SEOKEY => \Nette\Utils\Strings::webalize(\Extensions\StringHelper::removeSpecialChars($row->title . '-' . $user->company_name))
				];
				try {
					$cRow = $this->db1->table(Model\CourseManager::TABLE_NAME)->insert($nRow);
				} catch (Database\UniqueConstraintViolationException $exc) {
					$nRow[Model\CourseManager::COLUMN_SEOKEY] = $nRow[Model\CourseManager::COLUMN_SEOKEY] . '-' . \Nette\Utils\Random::generate(4);
					$cRow = $this->db1->table(Model\CourseManager::TABLE_NAME)->insert($nRow);
				}
				$courseId = $cRow->id;

				//gallery
				$ngRow = [];
				foreach ($this->db2->table('course_gallery')->where('course_id', $row->id) as $gRow) {
					/* @var $gRow \Nette\Database\Table\ActiveRow */
					$ggRow = $gRow->ref('gallery');
					$i = 10;
					for ($index = 1; $index <= $i; $index++) {

						$attrImg = 'img' . $index;
						$attrAlt = 'alt' . $index;

						if ($ggRow->{$attrImg}) {
							$ngRow[] = [
								Model\CourseImageManager::COLUMN_ACTIVE => true,
								Model\CourseImageManager::COLUMN_COURSE_ID => $courseId,
								Model\CourseImageManager::COLUMN_IMG => '/images/company_img/' . $ggRow->{$attrImg}
							];
						}
					}
				}
				if (count($ngRow) > 0) {
					$this->db1->table(Model\CourseImageManager::TABLE_NAME)->insert($ngRow);
				}

				//focus
				$ncaRow = [];
				foreach ($this->db2->table('course_category')->where('course_id', $row->id) as $caRow) {
					$ncaRow[] = [
						Model\CourseFocusManager::COLUMN_COURSE_ID => $courseId,
						Model\CourseFocusManager::COLUMN_FOCUS_ID => $caRow->category_id
					];
				}
				if (count($ncaRow) > 0) {
					$this->db1->table(Model\CourseFocusManager::TABLE_NAME)->insert($ncaRow);
				}

				//video
				$nvRow = [];
				foreach ($this->db2->table('course_video')->where('course_id', $row->id) as $vRow) {
					$vvRow = $this->db2->table('video')->where('id', $vRow->video_id)->fetch();
					$nvRow[] = [
						Model\CourseVideoManager::COLUMN_ACTIVE => true,
						Model\CourseVideoManager::COLUMN_COURSE_ID => $courseId,
						Model\CourseVideoManager::COLUMN_VIDEO => $vvRow->code
					];
				}
				if (count($nvRow) > 0) {
					$this->db1->table(Model\CourseVideoManager::TABLE_NAME)->insert($nvRow);
				}

				//terms
				foreach ($this->db2->table('course')->where('title', $row->title)->where('language', $row->language) as $tRow) {
					$ntRow = [
						Model\TermManager::COLUMN_ACTIVE => $tRow->active,
						Model\TermManager::COLUMN_ADDRESS_FLAG => Model\So\Term::FLAG_ADDRESS_DEFAULT,
						Model\TermManager::COLUMN_ADDRESS_NOTE => '',
						Model\TermManager::COLUMN_CITY => '',
						Model\TermManager::COLUMN_COUNTRY_KEY => 'CZ',
						Model\TermManager::COLUMN_COURSE_ID => $courseId,
						Model\TermManager::COLUMN_CURRENCY => 'CZK',
						Model\TermManager::COLUMN_DISCOUNT => $tRow->discount,
						Model\TermManager::COLUMN_FROM => $tRow->dt_from,
						Model\TermManager::COLUMN_HOUSE_NUMBER => '',
						Model\TermManager::COLUMN_LATITUDE => 0,
						Model\TermManager::COLUMN_LECTOR_DEGREES_AFTER => '',
						Model\TermManager::COLUMN_LECTOR_DEGREES_BEFORE => '',
						Model\TermManager::COLUMN_LECTOR_DESCRIPTION => '',
						Model\TermManager::COLUMN_LECTOR_FIRSTNAME => '',
						Model\TermManager::COLUMN_LECTOR_IMAGE => '',
						Model\TermManager::COLUMN_LECTOR_SKILLS => '',
						Model\TermManager::COLUMN_LECTOR_SURNAME => $tRow->lector,
						Model\TermManager::COLUMN_LONGITUDE => 0,
						Model\TermManager::COLUMN_NOTERM => $tRow->noterm ? $tRow : 0,
						Model\TermManager::COLUMN_PRICE => $tRow->price,
						Model\TermManager::COLUMN_PRICE_FLAG => Model\So\Term::FLAG_PRICE_DEFAULT,
						Model\TermManager::COLUMN_REGISTRY_NUMBER => '',
						Model\TermManager::COLUMN_STREET => $tRow->address,
						Model\TermManager::COLUMN_TO => $tRow->dt_to,
						Model\TermManager::COLUMN_VAT => 0,
						Model\TermManager::COLUMN_ZIP => '',
					];
					$this->db1->table(Model\TermManager::TABLE_NAME)->insert($ntRow);
				}
			}

			$this->db1->commit();
		} catch (Exception $exc) {
			$this->db1->rollBack();
			throw $exc;
		}
		echo 'DONE';
		$this->terminate();
	}

	private function transLanguage($in) {
		$trans = ['anglickém' => 2,
			'českém' => 1,
			'český' => 1,
			'Český jazyk' => 1,
			'čeština' => 1,
			'ČJ' => 1,
			'cz' => 1
		];
		return isset($trans[$in]) ? $trans[$in] : 1;
	}

	public function actionResizelogo() {
		foreach ($this->db1->table(Model\CompanyManager::TABLE_NAME)->where(Model\CompanyManager::COLUMN_LOGO . ' <> ""') as $row) {

			if (file_exists(ltrim($row->logo, '/'))) {
				$image = \Nette\Utils\Image::fromFile(ltrim($row->logo, '/'));
				$image->resize(Model\CompanyManager::IMAGE_WIDTH, Model\CompanyManager::IMAGE_HEIGHT);
				$image->sharpen();
				$blank = \Nette\Utils\Image::fromBlank(Model\CompanyManager::IMAGE_WIDTH, Model\CompanyManager::IMAGE_HEIGHT,
								\Nette\Utils\Image::rgb(255, 255, 255));
				$blank->place($image, '50%', '50%', 100);
				$ret = $blank->save(ltrim($row->logo, '/'), 100, \Nette\Utils\Image::PNG);
			}
		}

		echo 'DONE';
		$this->terminate();
	}

}
