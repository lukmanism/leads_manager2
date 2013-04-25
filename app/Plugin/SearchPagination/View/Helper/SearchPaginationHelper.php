<?php

/**
 * SearchPagination helper
 *
 * You don't have to load this helper in your controllers by hand,
 * since the SearchPaginationComponent will do if necessary.
 *
 * @author Takayuki Miwa <i@tkyk.name>
 * @package SearchPagination
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class SearchPaginationHelper extends AppHelper {

/**
 * @var array 
 */
	public $helpers = array('Paginator');

/**
 * @var array  search parameters passed from SearchPaginationComponent.
 */
	protected $_searchParams;

/**
 * Constructor
 * 
 * @param array $settings helper options
 */
	public function __construct(View $View, $settings=array()) {
		parent::__construct($View, $settings);
		if (!empty($settings['__search_params'])) {
			$this->_searchParams = $settings['__search_params'];
		}
	}

/**
 * beforeRender callback.
 * 
 * Passes the search parameters to PaginatorHelper.
 */
	public function beforeRender($viewFile) {
		if (!empty($this->_searchParams)) {
			if (
				!isset($this->Paginator->options['url']) ||
				!is_array($this->Paginator->options['url'])
			) {
				$this->Paginator->options['url'] = array();
			}
			$this->Paginator->options['url']['?'] = $this->_searchParams;
		}
	}

}
