<?php

/**
 * Interfejs bazodanowy dla PDO
 * @author wookieb
 * @package Db
 */
interface Db {

	/**
	 * Zwraca jeden rekord z zapytania
	 *
	 * @param string $query zapytanie
	 * @param mixed $values wartość bądź wartości do bindowania w zapytaniu
	 * @return array|bool false w przypadku błędu
	 */
	 function getRecord($query, $values = null);

	/**
	 * Przygotowuje zapytanie do wykonania
	 *
	 * @param string $query
	 * @param mixed $values
	 * @return PDOStatement
	 */
    function getStatement($query, $values = null);

	/**
	 * Zwraca wszystkie rekordy z zapytania
	 *
	 * @param string $query
	 * @param mixed $values wartość bądź wartości do bindowanie w zapytaniu
	 * @return array
	 */
	function getRecords($query, $values = null);

	/**
	 * Zwykłe wykonanie zapytania.
	 * Metoda służy do zapytań insert, update, replace lecz nie do zapytań pobierających dane
	 *
	 * @param string $query
	 * @return int
	 */
	function query($query);

	/**
	 * Uaktualnia rekord/y w podanej tabeli
	 *
	 * @param string $table nazwa tabeli w jakiej zmienić rekord/y
	 * @param array $values wartości do zmiany
	 * @param string $where warunek ograniczający update
	 * @return int liczba zmodyfikowanych rekordów
	 */
	function update($table, array $values, $where = null);

	/**
	 * Usuwa rekord/y z podanej tabeli
	 *
	 * @param string $table nazwa tabeli
	 * @param string $where warunek ograniczający
	 * @param array $options wartości przeznaczone do zbindowania
	 * @param string $limit limit usuniętych rekordów
	 * @return int liczba zmodyfikowanych rekordów
	 */
	function delete($table, $where = null, array $options = null, $limit = null);

	/**
	 * Dodaje rekord do tabeli
	 *
	 * @param string $table nazwa tabeli
	 * @param array $values wartości do wstawienia
	 * @param array $onDuplicate wartości do zmiany przy zduplikowanym rekordzie
	 * @return int id dodanego rekordu
	 */
	function insert($table, array $values, array $onDuplicate = null);
}
