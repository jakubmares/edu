<?php

namespace App\Module\Zone\Presenters;

use Nette,
	Nette\Security\User,
	App\Model,
	App\Model\CompanyManager;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \App\Presenters\BasePresenter {

	/** @var CompanyManager */
	protected $companyMan;

	public function injectCompanyManager(CompanyManager $companyMan) {
		$this->companyMan = $companyMan;
	}

	protected function startup() {
		parent::startup();
		if (!$this->user->isLoggedIn()) {
			if ($this->user->getLogoutReason() === User::INACTIVITY) {
				$this->flashMessage('Session timeout, you have been logged out');
			}

			$this->redirect(':Front:Sign:in', ['backlink' => $this->storeRequest()]);
		} else {

			if (!$this->user->isAllowed(Model\PermissionManager::RESOURCE_ZONE)) {
				$this->flashMessage('Access denied');
				$this->redirect(':Front:Homepage:default');
			}
		}
	}

	protected function beforeRender() {

		if ($this->user->isInRole(Model\RoleManager::ROLE_ADMIN)) {
			$companies = $this->companyMan->getActive();
		} elseif ($this->user->isInRole(Model\RoleManager::ROLE_DEALER)) {
			$companies = $this->companyMan->getByDealerId($this->getUser()->id);
		} else {
			$companies = $this->companyMan->getByUserId($this->getUser()->id);
		}
		$this->template->companies = $companies;
		return parent::beforeRender();
	}

}
