<?php

/**
 * @author wookieb
 * @package Session
 */
interface Session {

	/**
	 * Uruchamia sesje
	 * @return self
	 */
	function start();

	/**
	 * Zatrzymuje sesje
	 * @return self
	 */
	function stop();

	/**
	 * Czy sesja wystartowała?
	 * @return bool
	 */
	function isStarted();
}
