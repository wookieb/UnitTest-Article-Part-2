<?php
class Authorization_Method_Test extends Authorization_Method {

	private $_success = false;
	public function setSuccess($success) {
		$this->_success = (bool)$success;
		return $this;
	}

	public function getSuccess() {
		return $this->_success;
	}
	
	protected function _authorize() {
		if ($this->_success) {
			$this->_data = array('dane');
			return true;
		}
		$this->_error = 'blad';
		return false;
	}

	public function setParametr($parametr) {
		$this->_parameters['parametr'] = $parametr;
		return $this;
	}

	public function getParameters() {
		return $this->_parameters;
	}
}
