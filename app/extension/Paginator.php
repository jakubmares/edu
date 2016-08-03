<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Extensions;

/**
 * Description of Paginator
 *
 * @author jakubmares
 */
class Paginator extends \Nette\Utils\Paginator {

	private $pagin;

	public function getPaginBefore() {



		$ret = [];
		for ($i = $this->fromBefore(); $i < $this->getPage(); $i++) {
			$ret[] = $i;
		}
		return $ret;
	}

	public function getPaginAfter() {

		$ret = [];
		for ($i = $this->getPage() + 1; $i <= $this->toAfter(); $i++) {
			$ret[] = $i;
		}
		return $ret;
	}

	private function toAfter() {
		$ret = $this->getPage() + ($this->pagin / 2);
		if ($ret > ($this->getPageCount())) {
			$ret = $this->getPageCount();
		}elseif($this->getPage() < ($this->pagin / 2)){
			$ret = $ret + (($this->pagin / 2)-$this->getPage());
		}

		return $ret;
	}

	private function fromBefore() {
		$ret = $this->getPage() - ($this->pagin / 2);
		if ($this->getPage() <= ($this->pagin / 2)) {
			$ret = 1;
		} elseif ((($this->pagin / 2) + $this->getPage()) >= $this->getPageCount()) {
			$ret = $this->getPageCount() - $this->pagin;
		}

		return $ret;
	}

	public function setPagin($pagin) {
		$this->pagin = $pagin;
	}

}
