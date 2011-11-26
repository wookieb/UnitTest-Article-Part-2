<?php

/**
 * Metoda autoryzacji
 * @author wookieb
 * @package User
 */
class Authorization {
	/**
	 * Przechowalnia danych autoryzacji
	 * @var Authorization_Storage_Interface
	 */
	protected $_storage;

	/**
	 * @param Authorization_Storage_Interface $storage
	 */
	public function __construct(Authorization_Storage_Interface $storage) {
		$this->setStorage($storage);
	}

	/**
	 * Ustawia przechowalnię danych autoryzacyjnych
	 * @param Authorization_Storage_Interface $storage
	 * @return Authorization
	 */
	public function setStorage(Authorization_Storage_Interface $storage) {
		$this->_storage = $storage;
		return $this;
	}

	/**
	 * Zwraca ustawiony obiekt przechowalni dnaych autoryzacyjnych
	 * @return Authorization_Storage_Interface
	 */
	public function getStorage() {
		return $this->_storage;
	}

	/**
	 * Rzeczywista metoda autoryzująca według reguł metody autoryzacyjnej podanej w parametrze
	 * @param Authorization_Method $method
	 * @return boolean
	 */
	public function authorize(Authorization_Method $method) {
		if ($method->authorize()) {
			$this->_storage->write($method->getData());
			return true;
		}
		return false;
	}

	/**
	 * Zamyka aktualną autoryzację
	 * @return self
	 */
	public function close() {
		$this->_storage->destroy();
		return $this;
	}

	/**
	 * Zwraca dane autoryzacyjne
	 * @return mixed
	 */
	public function getData() {
		return $this->_storage->read();
	}

	/**
	 * Zwraca info czy jest autoryzacja
	 * @return boolean
	 */
	public function isAuthorized() {
		return (bool)$this->_storage->hasData();
	}
}

/**
 * coZwracaMetodaAuthorize
 */