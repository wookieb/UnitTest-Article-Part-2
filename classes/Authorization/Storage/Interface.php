<?php

/**
 * Interfejs opisujący przechowalnie autoryzacji
 * @author wookieb
 * @version 1.0
 * @package Authorization
 * @subpackage Storage
 */
interface Authorization_Storage_Interface {

	/**
	 * Zwraca zapisane dane autoryzacyjne
	 */
	public function read();

	/**
	 * Zapisane dane autoryzacyjne
	 */
	public function write($data);

	/**
	 * Usuwa dane autoryzacyjne
	 */
	public function destroy();

	/**
	 * Sprawdza czy są dane sesyjne
	 */
	public function hasData();
}
