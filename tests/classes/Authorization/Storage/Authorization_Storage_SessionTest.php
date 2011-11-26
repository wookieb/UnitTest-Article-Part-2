<?php

/**
 * Test przechowalni danych autoryzacyjnych w sesji
 * @author Wookieb
 * @version 1.0
 * @package Tests
 * @subpackage Authorization
 * @group PrzechowalnieAutoryzacji
 * @group Autoryzacja
 */
class Authorization_Storage_SessionTest extends PHPUnit_Framework_TestCase {
	/**
	 * Testowany obiekt
	 * @var Authorization_Storage_Session
	 */
	protected $object;
	/**
	 * Kontekst testu
	 * @var Context
	 */
	private $_context;
	/**
	 * Obiekt sesji
	 * @var Session
	 */
	private $_session;

	/**
	 * Przygotowanie testu
	 */
	protected function setUp() {
		$this->_session= $this->getMock('Session');
		$this->object = new Authorization_Storage_Session($this->_session, array('authorization'));
		$_SESSION = array();
        parent::__construct();
	}

	protected function _ustawZeSesjaWystartowala() {
		$this->_session->expects($this->any())
				->method('isStarted')
				->will($this->returnValue(true));
	}

	protected function _ustawZeSesjaNieWystartowala() {
		$this->_session->expects($this->any())
				->method('isStarted')
				->will($this->returnValue(false));
	}

	public function testNieudanaZmianaSciezkiKluczyPrzyPustejTablicy() {
		$this->setExpectedException('InvalidArgumentException', 'Key path for session authorization storage cannot be empty');
		$this->object->setAuthorizationKeyPath(array());
	}

	public function testNieudanaZmianaSciezkaKluczyPrzyPotencjalniePustejTablicy() {
		$this->setExpectedException('InvalidArgumentException', 'Key path for session authorization storage cannot be empty');
		$this->object->setAuthorizationKeyPath(array('', 0));
	}

	public function testZmianaPobieranieSciezkiKluczy() {
		$this->assertSame($this->object, $this->object->setAuthorizationKeyPath(array('key', 'path')));
		$this->assertSame(array('key', 'path'), $this->object->getAuthorizationKeyPath());
	}

	public function testUdanyOdczytFlagiCzySaDane() {
		$this->_ustawZeSesjaWystartowala();
		$_SESSION['authorization'] = 1;
		$this->assertTrue($this->object->hasData());
	}

	public function testNieudanyOdczytFlagiCzySaDanePrzyNieWystartowanejSesji() {
		$this->_ustawZeSesjaNieWystartowala();
		$this->assertFalse($this->object->hasData());
	}

	public function testNieudanyOdczytFlagiCzySaDanePrzyBrakuDanychWSesji() {
		$this->_ustawZeSesjaWystartowala();
		$this->assertArrayNotHasKey('authorization', $_SESSION);
		$this->assertFalse($this->object->hasData());
	}

	public function testNieudaneUsuniecieDanychPrzyNieWystartowanejSesji() {
		$_SESSION['authorization'] = 1;
		$this->_ustawZeSesjaNieWystartowala();
		$this->assertSame($this->object, $this->object->destroy());
		$this->assertArrayHasKey('authorization', $_SESSION);
	}

	public function testNieudaneUsuniecieDanychPrzyIchBraku() {
		$this->_ustawZeSesjaWystartowala();
		$this->assertArrayNotHasKey('authorization', $_SESSION);
		$this->assertSame($this->object, $this->object->destroy());
		$this->assertArrayNotHasKey('authorization', $_SESSION);
	}

	public function testUdaneUsuniecieDanych() {
		$this->_ustawZeSesjaWystartowala();
		$_SESSION['authorization'] = 1;
		$this->assertTrue($this->object->hasData());
		$this->assertSame($this->object, $this->object->destroy());
		$this->assertArrayNotHasKey('authorization', $_SESSION);
		$this->assertFalse($this->object->hasData());
	}

	public function testNieudaneOdczytanieDanychPrzyNieWystartowanejSesji() {
		$_SESSION['authorization'] = 1;
		$this->_ustawZeSesjaNieWystartowala();
		$this->assertNull($this->object->read());
	}

	public function testNieudaneOdczytanieDanychPrzyIchBraku() {
		$this->_ustawZeSesjaWystartowala();
		$this->assertArrayNotHasKey('authorization', $_SESSION);
		$this->assertNull($this->object->read());
	}

	public function testUdaneOdczytanieDanych() {
		$_SESSION['authorization'] = 1;
		$this->_ustawZeSesjaWystartowala();
		$this->assertSame(1, $this->object->read());
	}

	public function testNieudanyZapisDanychPrzyNieRozpoczeciuSesji() {
		$_SESSION['authorization'] = 1;
		$this->_ustawZeSesjaNieWystartowala();
		$this->assertSame($this->object, $this->object->write(2));
		$this->assertSame(1, $_SESSION['authorization']);
	}

	public function testNieudanyZapisPustychDanych() {
		$_SESSION['authorization'] = 1;
		$this->_ustawZeSesjaWystartowala();
		$this->setExpectedException('InvalidArgumentException', 'Trying to write empty data');
		$this->assertSame($this->object, $this->object->write(0));
	}

	public function testUdanyZapisDanych() {
		$this->_ustawZeSesjaWystartowala();
		$this->assertFalse($this->object->hasData());
		$this->assertSame($this->object, $this->object->write(2));
		$this->assertSame(2, $_SESSION['authorization']);
		$this->assertTrue($this->object->hasData());
	}

	/**
	 * @depends testZmianaPobieranieSciezkiKluczy
	 */
	public function testKonstruktora() {
		$this->object = new Authorization_Storage_Session($this->_session, array('key', 'path'));
		$this->assertSame(array('key', 'path'), $this->object->getAuthorizationKeyPath());
		$this->assertSame($this->_session, $this->object->getSession());
	}
}
