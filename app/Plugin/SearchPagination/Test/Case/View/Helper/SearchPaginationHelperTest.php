<?php

App::uses('View', 'View');
App::uses('PaginatorHelper', 'View/Helper');
App::uses('SearchPaginationHelper', 'SearchPagination.View/Helper');

/**
 * @property View $v
 * @property PaginatorHelper $p
 * @property SearchPaginationHelper $h
 */
class SearchPaginationHelperTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->v = new View(null);
		$this->p = new PaginatorHelper($this->v);
		$this->p->request = new CakeRequest(null, false);
		$this->p->request->addParams(array('named' => array(), 'pass' => array()));
	}

	public function tearDown() {
		unset($this->p);
		unset($this->h);
		parent::tearDown();
	}

	protected function _init($params) {
		$this->h = new SearchPaginationHelper($this->v, array('__search_params' => $params));
		$this->h->Paginator = $this->p;
	}

	public function testBeforeRender() {
		$params = array(
			'foo' => 'bar',
			'baz' => array(1, 2, 3)
		);
		$viewFile = "not_used.ctp";
		$this->_init($params);

		$this->p->beforeRender($viewFile);
		$this->h->beforeRender($viewFile);
		$this->assertEquals(array('?' => $params), $this->p->options['url']);
	}

	public function testBeforeRenderEmptyOptions() {
		$params = array();
		$viewFile = "not_used.ctp";
		$this->_init($params);

		$this->p->beforeRender($viewFile);
		$this->h->beforeRender($viewFile);
		$this->assertEquals(array(), $this->p->options['url']);
	}

	public function testBeforeRenderMergeUrlOptions() {
		$params = array(
			'foo' => 'bar',
			'baz' => array(1, 2, 3)
		);
		$viewFile = "not_used.ctp";
		$this->_init($params);

		$this->p->request->params['pass'] = array(2);
		$this->p->request->params['named'] = array('foo' => 'bar');
		$this->p->beforeRender($viewFile);

		$this->assertEquals(
			array(2, 'foo' => 'bar'),
			$this->p->options['url']
		);

		$this->h->beforeRender($viewFile);
		$this->assertEquals(
			array(2, 'foo' => 'bar', '?' => $params),
			$this->p->options['url']
		);
	}

}
