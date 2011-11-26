<?php

/**
 * Abstrakcyjna klasa opisująca metodę autoryzacji
 * @author wookieb
 * @version 1.0
 * @package Authorization
 * @subpackage Method
 */
abstract class Authorization_Method {
	/**
	 * Nazwa metody autoryzacji
	 * @var string
	 */
	protected $_name;
	/**
	 * Lista parametrów do potwierdzenia przy autoryzacji
	 * Np login i hasło
	 * @var array
	 */
	protected $_parameters = array();
	/**
	 * Dane potwierdzająca autoryzację zapisywane w przechowalni autoryzacji
	 * @var array
	 */
	protected $_data;
	/**
	 * Błąd autoryzacji
	 * @var string
	 */
	protected $_error;

	/**
	 * Zwraca nazwę aktualnej metody autoryzacji
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Ustawia dane użytkownika
	 * @return mixed
	 */
	public function getData() {
		return $this->_data;
	}

	/**
	 * Ostatni błąd autoryzacji
	 * @return string
	 */
	public function getError() {
		return $this->_error;
	}

	/**
	 * Metoda autoryzująca
	 * Po jej wywołaniu lista parametrów autoryzacyjnych zostanie
	 * USUNIĘTA ze względów bezpieczeństwa
	 * Ostatni błąd autoryzacji oraz dane autoryzacyjne zostaną usunięte
	 */
	public function authorize() {
		$this->_error = null;
		$this->_data = null;
		$result = $this->_authorize();
		$this->_parameters = array();
		return $result;
	}

	/**
	 * Właściwa metoda sprawdzająca parametry autoryzacyjne
	 */
	abstract protected function _authorize();
}