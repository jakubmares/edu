<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette\Mail,
	Nette\Database;

/**
 * Description of QuestionManager
 *
 * @author jakubmares
 */
class QuestionManager extends BaseManager {

	const TABLE_NAME = 'question',
			COLUMN_ID = 'id',
			COLUMN_NAME = 'name',
			COLUMN_EMAIL = 'email',
			COLUMN_QUESTION = 'question',
			COLUMN_CREATED_AT = 'created_at',
			COLUMN_COURSE_ID = 'course_id',
			COLUMN_COURSE_NAME = 'course_name',
			COLUMN_COMPANY_NAME = 'company_name',
			COLUMN_SENT_TO = 'sent_to';

	/** @var MailService */
	private $mailer;

	public function __construct(Database\Context $database, MailService $mailer) {
		parent::__construct($database);
		$this->mailer = $mailer;
	}

	public function insertAndSendEmail($values, Mail\Message $message) {
		$this->database->beginTransaction();
		try {
			parent::insert($values);
			$ret = $this->mailer->send($message);

			$copy = new Mail\Message();
			$copy->setFrom(\App\Presenters\BasePresenter::EMAIL_NOREPLY, 'evzdelavani.cz')
					->setSubject('evzdelavani.cz - kopie dotazu na kurz')
					->setHtmlBody($message->getHtmlBody())
					->addTo(\App\Presenters\BasePresenter::EMAIL_INFO);
			$this->mailer->send($copy);

			$this->database->commit();
		} catch (Exception $exc) {
			$this->database->rollBack();
			throw $exc;
		}
		return $ret;
	}

}
