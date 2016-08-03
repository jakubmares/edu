<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette\Mail,
	Joseki\Application\Responses\PdfResponse;

/**
 * Description of OrderManager
 *
 * @author jakubmares
 */
class OrderManager extends BaseManager {

	const TABLE_NAME = 'order',
			COLUMN_ID = 'id',
			COLUMN_TERM_ID = 'term_id',
			COLUMN_NAME = 'name',
			COLUMN_EMAIL = 'email',
			COLUMN_PHONE = 'phone',
			COLUMN_BILLING_INFO = 'billing_info',
			COLUMN_MEMBER_COUNT = 'member_count',
			COLUMN_NOTE = 'note',
			COLUMN_CREATED_AT = 'created_at',
			COLUMN_COURSE_NAME = 'course_name',
			COLUMN_COMPANY_ID = 'company_id',
			COLUMN_SENT_TO = 'sent_to',
			COLUMN_TERM_FROM = 'term_from',
			COLUMN_TERM_TO = 'term_to';
	const ORDER_PATH = 'orders';

	/** @var MailService */
	private $mailer;

	public function __construct(\Nette\Database\Context $database, MailService $mailer) {
		parent::__construct($database);
		$this->mailer = $mailer;
	}

	public function insertAndSendEmails($values, Mail\Message $userMessage, Mail\Message $companyMessage, \mPDF $attachment) {
		$this->database->beginTransaction();
		try {
			$ret = parent::insert($values);
			$filename = self::ORDER_PATH . '/' . uniqid('objednavka-' . $values->offsetGet(OrderManager::COLUMN_NAME) . '-' . $values->offsetGet(OrderManager::COLUMN_TERM_ID) . '-') . '.pdf';
			$attachment->Output($filename, 'F');

			$userMessage->addAttachment($filename);
			$companyMessage->addAttachment($filename);

			$usMeCopy = $this->createMessageCopy($userMessage);
			$usMeCopy->addAttachment($filename);
			$coMeCopy = $this->createMessageCopy($companyMessage);
			$coMeCopy->addAttachment($filename);
			
			$this->mailer->send($userMessage);
			$this->mailer->send($companyMessage);
			$this->mailer->send($usMeCopy);
			$this->mailer->send($coMeCopy);
			$this->database->commit();
		} catch (Exception $exc) {
			$this->database->rollBack();
			throw $exc;
		}
		return $ret;
	}

	private function createMessageCopy(Mail\Message $message) {
		$copy = new Mail\Message();
		$copy->setFrom(\App\Presenters\BasePresenter::EMAIL_NOREPLY, 'evzdelavani.cz')
				->setSubject('evzdelavani.cz - kopie objednávky kurzu')
				->setHtmlBody($message->getHtmlBody())
				->addTo(\App\Presenters\BasePresenter::EMAIL_INFO);
		return $copy;
	}

}
