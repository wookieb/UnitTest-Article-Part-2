<?php

/**
 * Przechowalnia danych autoryzacyjnych w sesji
 * Żadne dane nie zostanę przechowane, odczytane dopóki sesja nie wystartuje
 * @author wookieb
 * @version 1.0
 * @package Authorization
 * @subpackage Storage
 */
class Authorization_Storage_Session implements Authorization_Storage_Interface {
	/**
	 * Uchwyt sesji
	 * @var Session
	 */
	private $_session;
	/**
	 * Ścieżka kluczy w tablicy sesji pod jaką zapisać dane autoryzacyjne
	 * @var array
	 */
	private $_keyPath;

	/**
	 * @param Session $session
	 * @param string $authorizationName nazwa autoryzacji
	 */
	public function __construct(Session $session, array $keyPath = array('authorization')) {
		$this->_session = $session;
		$this->setAuthorizationKeyPath($keyPath);
	}

	/**
	 * Ustawią ścieżkę kluczy w tablicy sesji pod jaką zapisać dane autoryzacyjne
	 * @param array $keyPath
	 * @return self
	 */
	public function setAuthorizationKeyPath(array $keyPath) {
		$keyPath = array_filter(array_map('trim', $keyPath));
		if (!$keyPath) {
			throw new InvalidArgumentException(
					'Key path for session authorization storage cannot be empty');
		}
		$this->_keyPath = $keyPath;
		return $this;
	}

	/**
	 * Pobiera ścieżkę kluczy w tablicy sesji pod jaką zapisać dane autoryzacyjne
	 * @return array
	 */
	public function getAuthorizationKeyPath() {
		return $this->_keyPath;
	}

	/**
	 * Zwraca ustawiony uchwyt sesji
	 * @return Session
	 */
	public function getSession() {
		return $this->_session;
	}

	/**
	 * Zwraca info czy są dane autoryzacyjne w sesji
	 * @return boolean
	 */
	public function hasData() {
		return $this->_session->isStarted()
		&& ArrayUtils::getValueOnKeyPath($_SESSION, $this->_keyPath);
	}

	/**
	 * Usuwa dane autoryzacyjne
	 * @return self
	 */
	public function destroy() {
		if ($this->hasData()) {
			ArrayUtils::removeValueOnKeyPath($_SESSION, $this->_keyPath);
		}
		return $this;
	}

	/**
	 * Zwraca dane autoryzacyjne z sesji
	 * @return mixed
	 */
	public function read() {
		if ($this->hasData()) {
			return ArrayUtils::getValueOnKeyPath($_SESSION, $this->_keyPath);
		}
	}

	/**
	 * Zapisuje dane autoryzacyjne do sesji
	 * @param mixed $data
	 * @return self
	 */
	public function write($data) {
		if ($this->_session->isStarted()) {
			if (empty($data)) {
				throw new InvalidArgumentException('Trying to write empty data');
			}
			ArrayUtils::insertValueOnKeyPath($_SESSION, $this->_keyPath, $data);
		}
		return $this;
	}
}