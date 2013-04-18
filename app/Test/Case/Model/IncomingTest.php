<?php
App::uses('Incoming', 'Model');

/**
 * Incoming Test Case
 *
 */
class IncomingTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.incoming',
		'app.campaign',
		'app.user',
		'app.log'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Incoming = ClassRegistry::init('Incoming');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Incoming);

		parent::tearDown();
	}

}
