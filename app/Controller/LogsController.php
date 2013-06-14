<?php
App::uses('AppController', 'Controller');
/**
 * Logs Controller
 *
 * @property Log $Log
 */
class LogsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
        $this->Campaign = ClassRegistry::init('Campaign');
        $user = $this->Auth->user();
        $this->set('user', $user);
        $group = $user['Group']['name'];

        if(!isset($_POST['submitloadreport'])){
	        if($group == 'administrators') {
	        	$campaigns = $this->Campaign->find('list');
	            $this->set('campaigns', $campaigns);
	        } else {
	        	$campaigns = $this->Campaign->find('list', array('conditions' => array('user_id' => $user['id'])));   
	            $this->set('campaigns', $campaigns);
	        }
        } else {
            $campaign_ids = $_POST['campaign_id'];
            $this->paginate = array(
		        'conditions' => array('Log.campaign_id' => $campaign_ids)
		    );	        
	        $this->set('logs', $this->paginate('Log'));
        }
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Log->exists($id)) {
			throw new NotFoundException(__('Invalid log'));
		}
		$options = array('conditions' => array('Log.' . $this->Log->primaryKey => $id));
		$this->set('log', $this->Log->find('first', $options));
	}
}
