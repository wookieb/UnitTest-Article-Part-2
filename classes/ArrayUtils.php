<?php

/**
 * Pomaga w operacjach na tablicach
 * 
 * @author wookieb
 * @package ArrayUtils
 */
class ArrayUtils {

	/**
	 * Mapuje tablica po wybranym kluczu
	 *
	 * @param array $array tablica/obiekt to przeiterowania
	 * @param string $key Nazwa klucza do pobrania
	 * @param bool $saveKeys Czy zachować klucze
	 * @return array
	 */
	public static function getValuesFromKey($array, $key, $saveKeys = false) {
		$tmpArray = array();
		foreach ($array as $arrKey => $element) {
			if (is_array($element) && isset($element[$key])) {
				if ($saveKeys) {
					$tmpArray[$arrKey] = $element[$key];
				}
				else {
					$tmpArray[] = $element[$key];
				}
			}
		}
		return $tmpArray;
	}

	public static function standardMap(array $tab, $idKey, $valueKey) {
		$newTab = array();
		foreach ($tab as $values) {
			if (isset($values[$idKey]) && isset($values[$valueKey])) {
				$newTab[$values[$idKey]] = $values[$valueKey];
			}
		}
		return $newTab;
	}

	public static function standardMapImplode(array $tab, $idKey, $implodeSeparator = ' ') {
		$args = func_get_args();
		$valueKeys = array_slice($args, 3);
		if (!$valueKeys) {
			throw new BadMethodCallException('Undefined values keys');
		}

		$newTab = array();
		foreach ($tab as $values) {
			if (isset($values[$idKey])) {
				$tmpValues = array();

				foreach ($valueKeys as $valueKey) {
					if (isset($values[$valueKey])) {
						$tmpValues[] = $values[$valueKey];
					}
				}
				$newTab [$values[$idKey]] = implode($implodeSeparator, $tmpValues);
			}
		}
		return $newTab;
	}

	public static function getValueOnKeyPath(array $tab, array $path) {
		$tmpTab = $tab;
		foreach ($path as $element) {
			if (is_array($tmpTab) && isset($tmpTab[$element])) {
				$tmpTab = $tmpTab[$element];
			}
			else {
				return;
			}
		}
		return $tmpTab;
	}

	public static function removeValueOnKeyPath(array &$tab, array $path) {
		end($path);
		$lastKey = key($path);
		$tmpTab = &$tab;

		foreach ($path as $key => $element) {
			if (is_array($tmpTab)) {
				if ($key == $lastKey) {
					if (isset($tmpTab[$element])) {
						unset($tmpTab[$element]);
						return true;
					}
					return true;
				}
				else if (isset($tmpTab[$element])) {
					$tmpTab = &$tmpTab[$element];
				}
			}
			else {
				break;
			}
		}
		return false;
	}

	/**
	 * Wstawia wartość do tablicy $tab w podanej ścieżce $path
	 *
	 * @param array $tab tablica do której wstawić wartość
	 * @param array $path ścieżka w tablicy
	 * @param mixed $value wartość do wstawienia
	 * @return array
	 */
	public static function insertValueOnKeyPath(array &$tab, array $path, $value) {
		$actualValue = &$tab;

		end($path);
		$lastKey = key($path);
		foreach ($path as $key => $pathName) {
			$isLast = false;
			if ($lastKey === $key) {
				$isLast = true;
			}

			// jeżeli nazwa jest pusta oznacza to, że mamy wkleić
			if (empty($pathName)) {
				$pathName = count($actualValue);
			}

			if (!isset($actualValue[$pathName]) && !$isLast) {
				$actualValue[$pathName] = array();
			}
			// jest taki klucz no i oznacza to ze mamy wstawic wartosc
			if ($isLast) {
				$actualValue[$pathName] = $value;
			}
			else {
				$actualValue = &$actualValue[$pathName];
			}
		}
		return $tab;
	}
}
