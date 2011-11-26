<?php

/**
 * Metoda autoryzacji bazującej na bazie danych
 * @author wookieb
 * @version 1.0
 * @package Authorization
 * @subpackage Method
 */
class Authorization_Method_Database extends Authorization_Method {

	/**
	 * Uchwyt relacyjnej bazy danych
	 * @var Db
	 */
	private $_db;

	/**
	 * Nazwa tabeli przechowującej użytkowników
	 * @var string
	 */
	private $_table;

	/**
	 * Nazwa metody autoryzacji
	 * @var string
	 */
	protected $_name = 'database';

	/**
	 *
	 * @param Db $db uchwyt bazy danych
	 * @param string $table nazwa tabeli
	 */
	public function __construct(Db $db, $table) {
		$this->_db = $db;
		$this->setTable($table);
	}

	/**
	 * Ustawia nazwę tabeli przechowującej użytkowników
	 * @param string $table
	 * @return self
	 */
	public function setTable($table) {
		$table = trim($table);
		if (!$table) {
			throw new InvalidArgumentException(
					'Table name for database authorization method cannot be empty');
		}
		$this->_table = $table;
		return $this;
	}

	/**
	 * Zwraca ustawioną nazwę tabeli użytkowników
	 * @return string
	 */
	public function getTable() {
		return $this->_table;
	}

	/**
	 * Ustawia login
	 * @param string $login
	 * @return self
	 */
	public function setLogin($login) {
		$this->_parameters['login'] = trim($login);
		return $this;
	}

	/**
	 * Ustawia hasło i od razu je hashuje
	 * @param string $password
	 * @return self
	 */
	public function setPassword($password) {
		$password = trim($password);
		$this->_parameters['password'] = $password ? sha1($password) : null;
		return $this;
	}

	/**
	 * Sprawdza poprawność występowania parametrów autoryzacyjnych
	 * @return boolean
	 */
	private function _checkParametersExists() {
		if (empty($this->_parameters['login'])) {
			$this->_error = 'Login is empty';
			return false;
		}

		if (empty($this->_parameters['password'])) {
			$this->_error = 'Password is empty';
			return false;
		}
		return true;
	}

	/**
	 * Pobiera dane użytkownika o podanym loginie (emaila) oraz haśle
	 * @param string $login login, email
	 * @param string $password hash sha1 z rzeczywistego hasła
	 * @return mixed 
	 */
	private function _getAuthorizationData($login, $password) {

		$query = 'SELECT id, is_active, is_block, group_id
        FROM ' . $this->_table . '
        WHERE
            (login = :login OR mail = :login)
            AND
            password = :password
        ';

		$data = $this->_db->getRecord($query, array(
			':login' => $login,
			':password' => $password
		));
		return $data;
	}

	/**
	 * Rzeczywista metoda sprawdzająca poprawność danych podanych do logowania
	 * @return boolean
	 */
	protected function _authorize() {
		if (!$this->_checkParametersExists()) {
			return false;
		}
		$data = $this->_getAuthorizationData(
				$this->_parameters['login'], $this->_parameters['password']);
		if (!$data) {
			$this->_error = 'Invalid login or password';
			return false;
		}
		if ($data['is_block']) {
			$this->_error = 'Account blocked';
			return false;
		}

		if (!$data['is_active']) {
			$this->_error = 'Account inactive';
			return false;
		}
		unset($data['is_active']);
		unset($data['is_block']);
		$this->_data = $data;
		return true;
	}

}