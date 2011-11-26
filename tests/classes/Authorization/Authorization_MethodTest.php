<?php

/**
 * Test abstrakcyjnej klasy metody autoryzacji
 * @author Wookieb
 * @version 1.0
 * @package Tests
 * @subpackage Authorization
 * @group MetodyAutoryzacji
 * @group Autoryzacja
 */
class Authorization_MethodTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var Authorization_Method
	 */
	protected $object;

	protected function setUp() {
		$this->object = $this->getMockForAbstractClass('Authorization_Method');
	}

	public function testUdanePobraniePustejNazwyMetody() {
		$this->assertNull($this->object->getName());
	}

	public function testUdanePobraniePustychDanychAutoryzacyjnych() {
		$this->assertNull($this->object->getData());
	}

	public function UdanePobraniePustegoBledu() {
		$this->assertNull($this->object->getError());
	}

	public function testUdanaAutoryzacja() {
		$this->object->expects($this->once())
				->method('_authorize')
				->will($this->returnValue(true));
		$this->assertTrue($this->object->authorize());
	}

	public function testNieudanaAutoryzacja() {
		$this->object->expects($this->once())
				->method('_authorize')
				->will($this->returnValue(false));
		$this->assertFalse($this->object->authorize());
	}

	public function testPoprawneCzyszczenieOstatniegoBleduAutoryzacjiPrzyKolejnejProbieAutoryzacji() {
		$metoda = new Authorization_Method_Test();
		$metoda->setSuccess(false);

		$metoda->authorize();
		$this->assertNotEmpty($metoda->getError());

		$metoda->setSuccess(true);
		$metoda->authorize();
		$this->assertNull($metoda->getError());
	}

	public function testPoprawneCzyszczenieDanychAutoryzacyjnychPrzyKolejnejProbieAutoryzacji() {
		$metoda = new Authorization_Method_Test();
		$metoda->setSuccess(true);

		$metoda->authorize();
		$this->assertNotEmpty($metoda->getData());

		$metoda->setSuccess(false);
		$metoda->authorize();
		$this->assertNull($metoda->getData());
	}

	public function testPoprawneCzyszczenieParametrowAutoryzacjiPoPierwszejProbieAutoryzacji() {
		$metoda = new Authorization_Method_Test();
		$metoda->setParametr('true');
		$metoda->authorize();
		$this->assertEmpty($metoda->getParameters());
	}
}