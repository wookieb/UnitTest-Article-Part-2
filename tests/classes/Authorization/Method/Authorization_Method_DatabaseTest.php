<?php

/**
 * Test metody autoryzującej na podstawie relacyjnej bazy danych
 * @author Wookieb
 * @version 1.0
 * @package Tests
 * @subpackage Authorization
 * @group MetodyAutoryzacji
 * @group Autoryzacja
 */
class Authorization_Method_DatabaseTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Authorization_Method_Database
	 */
	protected $object;

	/**
	 * @var Context
	 */
	private $_context;

	protected function setUp() {
		$this->_db = $this->getMock('Db');
		$this->object = new Authorization_Method_Database($this->_db, 'user');
		parent::setUp();
	}

	private function _ustawWynikPobieraniaUzytkownikaZBazy($wynik, $login = null, $haslo = null) {
		$query = '/SELECT id, is_active, is_block, group_id\s+FROM user\s+WHERE\s+\(login = :login OR mail = :login\)\s+AND\s+password = :password\s*/is';
		$bindValues = array();
		if ($login !== null) {
			$bindValues[':login'] = $login;
		}
		if ($haslo !== null) {
			$bindValues[':password'] = sha1($haslo);
		}
		$this->_db->expects($this->atLeastOnce())
				->method('getRecord')
				->with(new PHPUnit_Framework_Constraint_PCREMatch($query), $this->equalTo($bindValues))
				->will($this->returnValue($wynik));
	}

	public function testUdanaZmianaPobranieNazwyTabeli() {
		$this->assertSame($this->object, $this->object->setTable('tabela'));
		$this->assertSame('tabela', $this->object->getTable());
	}

	/**
	 * @depends testUdanaZmianaPobranieNazwyTabeli
	 */
	public function testKonstruktora() {
		$this->object = new Authorization_Method_Database($this->_db, 'tabelka');
		$this->assertSame('tabelka', $this->object->getTable());
	}

	public function testPusteDanePoczatkowe() {
		$this->assertNull($this->object->getData());
		$this->assertNull($this->object->getError());
	}

	public function testUdanePobranieNazwyMetody() {
		$this->assertSame('database', $this->object->getName());
	}

	public function testMethodChaining() {
		$this->assertSame($this->object, $this->object->setLogin('login'));
		$this->assertSame($this->object, $this->object->setPassword('password'));
	}

	public function testUdanaAutoryzacjaPoLoginieOrazHasle() {
		$this->_ustawWynikPobieraniaUzytkownikaZBazy(array(
			'id' => 1,
			'is_active' => 1,
			'is_block' => 0,
			'group_id' => 1,
				), 'uzytkownik_1', 'haslo_1'
		);
		
		$this->object->setLogin('uzytkownik_1')
				->setPassword('haslo_1');

		$this->assertTrue($this->object->authorize());
		$data = array(
			'id' => 1,
			'group_id' => 1,
		);
		$this->assertEquals($data, $this->object->getData());
		$this->assertNull($this->object->getError());
	}

	public function testUdanaAutoryzacjaPoEmailuOrazHasle() {
		$this->_ustawWynikPobieraniaUzytkownikaZBazy(array(
			'id' => 1,
			'is_active' => 1,
			'is_block' => 0,
			'group_id' => 1
				), 'email_1@wp.pl', 'haslo_1'
		);
		
		$this->object->setLogin('email_1@wp.pl')
				->setPassword('haslo_1');

		$this->assertTrue($this->object->authorize());
		$data = array(
			'id' => 1,
			'group_id' => 1,
		);
		$this->assertEquals($data, $this->object->getData());
		$this->assertNull($this->object->getError());
	}

	public function testNieudanaAutoryzacjaKontoNieaktywne() {
		$this->_ustawWynikPobieraniaUzytkownikaZBazy(array(
			'id' => 2,
			'is_active' => 0,
			'is_block' => 0,
			'group_id' => 1
				), 'uzytkownik_2', 'haslo_2');
		
		$this->object->setLogin('uzytkownik_2')
				->setPassword('haslo_2');

		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Account inactive', $this->object->getError());
	}

	public function testNieudanaAutoryzacjaKontoZablokowane() {
		$this->_ustawWynikPobieraniaUzytkownikaZBazy(array(
			'id' => 3,
			'is_active' => 1,
			'is_block' => 1,
			'group_id' => 1
				), 'uzytkownik_3', 'haslo_3');
		
		$this->object->setLogin('uzytkownik_3')
				->setPassword('haslo_3');
		
		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Account blocked', $this->object->getError());
	}

	public function testNieudanaAutoryzacjaNiePodanoLoginu() {
		$this->object->setPassword('haslo');
		
		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Login is empty', $this->object->getError());
	}

	public function testNieudanaAutoryzacjaNiePodanoHasla() {
		$this->object->setLogin('login');
		
		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Password is empty', $this->object->getError());
	}

	public function testNieudanaAutoryzacjaPodanePustyLogin() {
		$this->_db->expects($this->never())
				->method('getRecord');

		$this->object->setLogin('')
				->setPassword('haslo');

		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Login is empty', $this->object->getError());
	}

	public function testNieudanaAutoryzacjaPodanoPusteHaslo() {
		$this->_db->expects($this->never())
				->method('getRecord');

		$this->object->setPassword('')
				->setLogin('login');

		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Password is empty', $this->object->getError());
	}

	public function testNieudanaAutoryzacjaPodanoLoginNieistniejacegoKonta() {
		$this->_ustawWynikPobieraniaUzytkownikaZBazy(null, 'uzytkownik_100', 'haslo');
		
		$this->object->setLogin('uzytkownik_100')
				->setPassword('haslo');
		
		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Invalid login or password', $this->object->getError());
	}

	public function testNieudanaAutoryzacjaPodanoEmailNieistniejacegoKonta() {
		$this->_ustawWynikPobieraniaUzytkownikaZBazy(null, 'email_100@wp.pl', 'haslo');
		
		$this->object->setLogin('email_100@wp.pl')
				->setPassword('haslo');
		
		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Invalid login or password', $this->object->getError());
	}

	public function testNieudanaAutoryzacjaNiepoprawneHaslo() {
		$this->_ustawWynikPobieraniaUzytkownikaZBazy(null, 'uzytkownika_1@wp.pl', 'haslo');
		
		$this->object->setLogin('uzytkownika_1@wp.pl')
				->setPassword('haslo');
		
		$this->assertFalse($this->object->authorize());
		$this->assertNull($this->object->getData());
		$this->assertEquals('Invalid login or password', $this->object->getError());
	}

	public function testNieudaneUstawieniePustejNazwyTabeli() {
		$this->setExpectedException('InvalidArgumentException', 'Table name for database authorization method cannot be empty');
		$this->object->setTable(null);
	}

	public function testNieudaneUstawieniePotencjalniePustejNazwyTabeli() {
		$this->setExpectedException('InvalidArgumentException', 'Table name for database authorization method cannot be empty');
		$this->object->setTable('  ');
	}

}