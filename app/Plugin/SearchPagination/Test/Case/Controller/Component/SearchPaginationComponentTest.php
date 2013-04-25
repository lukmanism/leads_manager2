<?php

App::uses('SearchPaginationComponent', 'SearchPagination.Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('Router', 'Routing');

/**
 * @property SearchPaginationComponent $SearchPagination
 */
class TestControllerForSearchPaginationComponentTestCase extends Controller {

	public $uses = array();

	public $components = array(
		'SearchPagination.SearchPagination',
	);

	public $redirected = false;

	public $redirectUrl;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirected = true;
		$this->redirectUrl = $url;
	}

}

/**
 * @property TestControllerForSearchPaginationComponentTestCase $c
 */
class SearchPaginationComponentTest extends CakeTestCase {

	public $url = '/search/pagination';

	protected $_escapedGet;

	public function setUp() {
		parent::setUp();
		// set 'ext' parameter
		if (preg_match('/parseExtensions/i', $this->getName())) {
			Router::parseExtensions();
		}
		Router::connect('/:controller/:action/*');
		$this->c = $this->_getController();
	}

	protected function _getController() {
		$parseEnvironment = false; // Do not use $_GET, $_POST, etc.
		$request = new CakeRequest($this->url, $parseEnvironment);
		$params = Router::parse($request->url);
		$request->addParams($params);

		$ctrl = new TestControllerForSearchPaginationComponentTestCase($request);
		$ctrl->constructClasses();
		return $ctrl;
	}

	public function tearDown() {
		unset($this->controller);
		parent::tearDown();
	}

	public function assertNotRedirected() {
		$this->assertFalse($this->c->redirected);
	}

	protected function _setGetParams($arr) {
		$this->c->request->query = am($this->c->request->query, $arr);
	}

	public function testPrg_empty() {
		$model = 'Article';
		$this->assertTrue(empty($this->c->request->data));
		$this->assertTrue($this->c->SearchPagination->prg($model));
		$this->assertNotRedirected();
	}

	public function testPrg_emptyParams() {
		$model = 'Article';
		$params = array();
		$this->c->request->data[$model] = $params;
		$this->assertFalse(empty($this->c->request->data));
		$this->assertFalse($this->c->SearchPagination->prg($model));
		$this->assertIdentical(array(), $this->c->redirectUrl);
	}

	public function testPrg_someParams() {
		$model = 'Article';
		$params = array('foo' => 'bar',
			'baz' => array(1, 2, 3, 'zoo'));
		$this->c->request->data[$model] = $params;

		$this->assertFalse(empty($this->c->request->data));
		$this->assertFalse($this->c->SearchPagination->prg($model));
		$this->assertIdentical(array('?' => $params), $this->c->redirectUrl);
	}

	public function testPrg_someParams_otherModels() {
		$model = 'Article';
		$params = array('foo' => 'bar',
			'baz' => array(1, 2, 3, 'zoo'));
		$this->c->request->data[$model] = $params;
		$this->c->request->data['Another' . $model] = array('not', 'appear');

		$this->assertFalse(empty($this->c->request->data));
		$this->assertFalse($this->c->SearchPagination->prg($model));
		$this->assertIdentical(array('?' => $params), $this->c->redirectUrl);
	}

	public function testPrg_someParams_modelMismatch() {
		$model = 'Article';
		$this->c->request->data['Another' . $model] = array('not', 'appear');

		$this->assertFalse(empty($this->c->request->data));
		$this->assertFalse($this->c->SearchPagination->prg($model));
		$this->assertIdentical(array(), $this->c->redirectUrl);
	}

	public function testUnifyData() {
		$model = 'Article';
		$params = array('foo' => 'bar',
			'baz' => array(1, 2, 3));

		$this->_setGetParams($params);

		$this->c->SearchPagination->unifyData($model);
		$this->assertIdentical($params, $this->c->request->data[$model]);
	}

	public function testUnifyData_default() {
		$model = 'Article';

		$this->c->SearchPagination->unifyData($model);
		$this->assertIdentical(array(), $this->c->request->data[$model]);
	}

	public function testUnifyData_setDefault() {
		$model = 'Article';
		$default = array('foo' => 'bar',
			'baz' => array(1, 2, 3));

		$this->c->SearchPagination->unifyData($model, $default);
		$this->assertIdentical($default, $this->c->request->data[$model]);
	}

	protected function _assertHelperOrders() {
		$paginatorFound = $searchPaginationFound = false;
		$pluginHelperName = $this->c->SearchPagination->settings['helperName'];
		foreach($this->c->helpers as $k => $v) {
			if($k === 'Paginator' || $v === 'Paginator') {
				$paginatorFound = true;
			}
			if($k === $pluginHelperName || $v === $pluginHelperName) {
				$searchPaginationFound = true;
				if(!$paginatorFound) {
					$this->fail("$pluginHelperName is located before PaginatorHelper");
				} else {
					$this->assertTrue(true);
				}
			}
		}
		if(!$paginatorFound) {
			$this->fail("Paginator Helper is not registered");
		}
		if(!$searchPaginationFound) {
			$this->fail("$pluginHelperName is not registered");
		}
	}

	public function testSetupHelper_emptyHelpers() {
		$params = array('foo' => 'bar',
			'baz' => array(1, 2, 3));

		$this->c->helpers = array();
		$beforeCount = count($this->c->helpers);
		$this->c->SearchPagination->setupHelper($params);
		$this->assertIdentical(
			array('__search_params' => $params),
			$this->c->helpers[$this->c->SearchPagination->settings['helperName']]
		);
		$this->assertEquals($beforeCount + 2, count($this->c->helpers));
		$this->_assertHelperOrders();
	}

	public function testSetupHelper_searchPaginationHelperIsAlreadyInHelpers() {
		$params = array('foo' => 'bar',
			'baz' => array(1, 2, 3));
		$this->c->helpers = array('Html', $this->c->SearchPagination->settings['helperName'], 'Form');
		$beforeCount = count($this->c->helpers);
		$this->c->SearchPagination->setupHelper($params);
		$this->assertIdentical(
			array('__search_params' => $params),
			$this->c->helpers[$this->c->SearchPagination->settings['helperName']]
		);
		$this->assertFalse(in_array($this->c->SearchPagination->settings['helperName'], $this->c->helpers));
		$this->assertEquals($beforeCount + 1, count($this->c->helpers));
		$this->_assertHelperOrders();
	}

	public function testSetupHelper_paginatorHelperIsAlreadyInHelpers() {
		$params = array('foo' => 'bar',
			'baz' => array(1, 2, 3));
		$this->c->helpers = array('Html', 'Paginator', 'Form');
		$beforeCount = count($this->c->helpers);
		$this->c->SearchPagination->setupHelper($params);
		$this->assertIdentical(
			array('__search_params' => $params),
			$this->c->helpers[$this->c->SearchPagination->settings['helperName']]
		);
		$this->assertFalse(in_array($this->c->SearchPagination->settings['helperName'], $this->c->helpers));
		$this->assertEquals($beforeCount + 1, count($this->c->helpers));
		$this->_assertHelperOrders();
	}

	public function testSetupHelper_bothHelpersAreAlreadyInHelpers() {
		$params = array('foo' => 'bar',
			'baz' => array(1, 2, 3));
		$this->c->helpers = array(
			'Html',
			$this->c->SearchPagination->settings['helperName'] => array('__search_params' => array('c' => 'd')),
			'Paginator' => array('a' => 'b'),
		   	'Form'
		);
		$beforeCount = count($this->c->helpers);
		$this->c->SearchPagination->setupHelper($params);
		$this->assertIdentical(
			array('__search_params' => $params),
			$this->c->helpers[$this->c->SearchPagination->settings['helperName']]
		);
		$this->assertIdentical(array('a' => 'b'), $this->c->helpers['Paginator']);
		$this->assertFalse(in_array($this->c->SearchPagination->settings['helperName'], $this->c->helpers));
		$this->assertEquals($beforeCount, count($this->c->helpers));
		$this->_assertHelperOrders();
	}

	public function testSetup_Get_NoParams() {
		$model = 'Article';
		$params = array();
		$default = array('foo' => 'bar');

		$this->_setGetParams($params);

		$data = $this->c->SearchPagination->setup($model, $default);
		$this->assertIdentical($default, $data);
		$this->assertIdentical($default, $this->c->request->data[$model]);

		// default parameters are not succeeded!
		$this->assertIdentical(array('__search_params' => array()), $this->c->helpers[$this->c->SearchPagination->settings['helperName']]);
	}

	public function testSetup_Get_NoParams_When_Router_ParseExtensions() {
		$this->testSetup_Get_NoParams();
	}

	public function testSetup_Get_someParams() {
		$model = 'Article';
		$params = array('baz' => array(1, 2, 3),
			'title' => 'abc');
		$default = array('foo' => 'bar');

		$this->_setGetParams($params);

		$data = $this->c->SearchPagination->setup($model, $default);
		$this->assertIdentical($params, $data);
		$this->assertIdentical($params, $this->c->request->data[$model]);

		// data are succeeded from Controller->params, not ->data.
		$this->assertIdentical(array('__search_params' => $params), $this->c->helpers[$this->c->SearchPagination->settings['helperName']]);
	}

	public function testSetup_Get_someParams_When_Router_ParseExtensions() {
		$this->testSetup_Get_someParams();
	}

	public function testSetup_Post_someParams() {
		$model = 'Article';
		$params = array('baz' => array(1, 2, 3),
			'title' => 'abc');
		$default = array('foo' => 'bar');

		$this->c->request->data[$model] = $params;

		$data = $this->c->SearchPagination->setup($model, $default);
		$this->assertIdentical(array('?' => $params), $this->c->redirectUrl);
	}

	public function testSetup_modelClass() {
		$this->c->modelClass = 'Article';
		$params = array('baz' => array(1, 2, 3),
			'title' => 'abc');
		$default = array('foo' => 'bar');

		$this->c->request->data[$this->c->modelClass] = $params;

		$data = $this->c->SearchPagination->setup(null, $default);
		$this->assertIdentical(array('?' => $params), $this->c->redirectUrl);
	}

}
