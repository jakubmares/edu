<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette\Mail;

/**
 * Description of MailService
 *
 * @author jakubmares
 */
class MailService extends \Nette\Object {

	/** @var Mail\IMailer */
	private $mailer;

	public function __construct($smtp = false,$host = null, $port = null,Mail\IMailer $mailer) {
		if ($smtp) {
			$this->mailer = new Mail\SmtpMailer(['host' => $host, 'port' => $port, 'persistent' => true]);
		} else {
			$this->mailer = $mailer;
		}
	}

	public function send(Mail\Message $mail) {
		$this->mailer->send($mail);
	}

}
