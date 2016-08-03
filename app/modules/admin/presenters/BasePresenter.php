<?php

namespace App\Module\Admin\Presenters;

use App\Model,
	Nette\Security\User;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \App\Presenters\BasePresenter {

	/** @var Model\UserManager */
	protected $userMan;

	/** @var Model\CompanyManager */
	protected $companyMan;

	public function injectCompanyManager(Model\CompanyManager $companyMan) {
		$this->companyMan = $companyMan;
	}

	public function injectUserManager(Model\UserManager $userMan) {
		$this->userMan = $userMan;
	}

	protected function startup() {
		parent::startup();
		if (!$this->user->isLoggedIn()) {
			if ($this->user->getLogoutReason() === User::INACTIVITY) {
				$this->flashMessage('Session timeout, you have been logged out');
			}

			$this->redirect(':Front:Sign:in', ['backlink' => $this->storeRequest()]);
		} else {

			if (!$this->user->isAllowed(Model\PermissionManager::RESOURCE_ADMIN)) {
				$this->flashMessage('Access denied');
				$this->redirect(':Front:Homepage:default');
			}
		}
	}

}
