<?php

/**
 * Test obiektu łączącego funkcjonalności metod autoryzacji oraz przechowalni
 * Ogólnie kontroluje autoryzację
 * @author Wookieb
 * @version 1.0
 * @package Tests
 * @subpackage Authorization
 * @group Autoryzacja
 */
class AuthorizationTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var Authorization
	 */
	protected $object;
	/**
	 * @var Authorization_Storage_Interface
	 */
	private $_mockStorage;

	public function __construct() {
		$this->_mockStorage = $this->getMock('Authorization_Storage_Interface');
	}

	protected function setUp() {
		$this->object = new Authorization($this->_mockStorage);
	}

	/**
	 * @return Authorization_Method
	 */
	private function _createAuthorizationMethodMock() {
		$mock = $this->getMock('Authorization_Method', array('_authorize', 'authorize', 'getData', 'hasData', 'getError'));
		return $mock;
	}

	public function testZmianaPobraniePrzechowalni() {
        $mockStorage = $this->_mockStorage;
		$this->assertSame($this->object, $this->object->setStorage($mockStorage));
		$this->assertSame($mockStorage, $this->object->getStorage());
	}

	public function testUdanaAutoryzacja() {
		$method = $this->_createAuthorizationMethodMock();
		// autoryzacja powinna zwrócić TRUE
		$method->expects($this->once())
				->method('authorize')
				->will($this->returnValue(true));

		$data = array('dane');
		// read na metodzie zwróci $data
		$method->expects($this->once())
				->method('getData')
				->will($this->returnValue($data));

		// write na przechowalni z parametrem $data
		$this->_mockStorage->expects($this->once())
				->method('write')
				->with($this->equalTo($data));

		$this->assertTrue($this->object->authorize($method));
	}

	public function testNieudanaAutoryzacja() {
		$method = $this->_createAuthorizationMethodMock();
		// autoryzacja powinna zwrócić TRUE
		$method->expects($this->once())
				->method('authorize')
				->will($this->returnValue(false));

		// read na metodzie zwróci $data
		$method->expects($this->never())
				->method('getData');

		// write na przechowalni z parametrem $data
		$this->_mockStorage->expects($this->never())
				->method('write');

		$this->assertFalse($this->object->authorize($method));
	}

	public function testZamykanieAutoryzacji() {
		$this->_mockStorage->expects($this->once())
				->method('destroy');
		$this->assertSame($this->object, $this->object->close());
	}
	
	public function testUdanePobieranieIstnieniaAutoryzacji() {
		$this->_mockStorage->expects($this->once())
				->method('hasData')
				->will($this->returnValue(true));
		$this->assertTrue($this->object->isAuthorized());
	}

	public function testUdanePobieranieBrakuAutoryzacji() {
		$this->_mockStorage->expects($this->once())
				->method('hasData')
				->will($this->returnValue(false));
		$this->assertFalse($this->object->isAuthorized());
	}

	public function testUdanePobieranieDanych() {
		$data = array('siakieś dane');
		$this->_mockStorage->expects($this->once())
				->method('read')
				->will($this->returnValue($data));
		$this->assertEquals($data, $this->object->getData());
	}
}
