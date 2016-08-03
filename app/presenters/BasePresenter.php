<?php

namespace App\Presenters;

use Nette;
use App\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

	const EMAIL_INFO = 'info@evzdelavani.cz',
			EMAIL_NOREPLY = 'noreply@evzdelavani.cz';
	
	/** @var Model\CompanyManager */
	protected $companyMan;
	
	public function injectCompanyManager(Model\CompanyManager $companyMan){
		$this->companyMan = $companyMan;
	}

	protected static function createList($objects, $keyProp = 'id', $valueProp = 'name') {
		$res = [];
		/* @var $object Nette\Object */
		foreach ($objects as $object) {
			$res[$object->{$keyProp}] = $object->{$valueProp};
		}
		return $res;
	}

	protected function beforeRender() {
		$modules = [
			'Front' => ['name' => 'Front', 'action' => $this->link(':Front:Homepage:default')],
			'Zone' => ['name' => 'Zóna uživatele', 'action' => $this->link(':Zone:Homepage:default')],
			'Admin' => ['name' => 'Administrace', 'action' => $this->link(':Admin:Homepage:default')]];
		$this->template->modules = [];
		foreach ($modules as $module => $value) {
			if ($module == 'Front') {
				$this->template->modules[$module] = $value;
			}elseif($this->user->isAllowed($module)){
				$companies = $this->companyMan->getByUserId($this->user->getId());
				if($module == 'Zone' && count($companies) == 1){
					$company = current($companies);
					$value = ['name' => 'Zóna uživatele', 'action' => $this->link(':Zone:Company:detail',['id'=>$company->getId()])];
				}
				$this->template->modules[$module] = $value;
			}
		}
		return parent::beforeRender();
	}

}
