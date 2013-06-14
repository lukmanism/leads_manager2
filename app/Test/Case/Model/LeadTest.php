<?php
App::uses('Lead', 'Model');

/**
 * Lead Test Case
 *
 */
class LeadTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.lead',
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
		$this->Lead = ClassRegistry::init('Lead');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Lead);

		parent::tearDown();
	}

}
