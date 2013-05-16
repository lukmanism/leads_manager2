<?php
App::uses('AppController', 'Controller');
/**
 * Emails Controller
 *
 * @property Email $Email
 */
class EmailsController extends AppController {

	public function beforeFilter() {
	    parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {		
        $this->set('curuser', $this->Auth->user());
		$this->Email->recursive = 0;
		$this->set('emails', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Email->exists($id)) {
			throw new NotFoundException(__('Invalid email'));
		}
		$options = array('conditions' => array('Email.' . $this->Email->primaryKey => $id));
		$this->set('email', $this->Email->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
        $this->Campaign = ClassRegistry::init('Campaign');
		#admin only access
		if ($this->request->is('post')) {
			$this->Email->create();
			if ($this->Email->save($this->request->data)) {
				$this->Session->setFlash(__('The email has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The email could not be saved. Please, try again.'));
			}
		}
		$campaigns = $this->Campaign->find('list');
		$users = $this->Email->User->find('list', array('conditions' => array('User.group_id' => 1),'fields' => array('User.username') ));
		$this->set(compact('campaigns', 'users'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
        $this->Campaign = ClassRegistry::init('Campaign');
		if (!$this->Email->exists($id)) {
			throw new NotFoundException(__('Invalid email'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Email->save($this->request->data)) {
				$this->Session->setFlash(__('The email has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The email could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Email.' . $this->Email->primaryKey => $id));
			$this->request->data = $this->Email->find('first', $options);
		}
		$campaigns = $this->Campaign->find('list');
		$users = $this->Email->User->find('list', array('conditions' => array('User.group_id' => 1),'fields' => array('User.username') ));
		$this->set(compact('campaigns', 'users'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Email->id = $id;
		if (!$this->Email->exists()) {
			throw new NotFoundException(__('Invalid email'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Email->delete()) {
			$this->Session->setFlash(__('Email deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Email was not deleted'));
		$this->redirect(array('action' => 'index'));
	}


	public function download(){
        $this->layout = false;
        $this->render(false);

        $report = $_GET['report'];
        $campaign = $_GET['campaign'];
        #Download file from url
        if(isset($report)){
	        $filename     = "../../app/tmp/downloads/".$report."_".$campaign.".csv";
	        header('Content-Type: application/csv');
		    header('Content-disposition: attachment;filename='.$report."_".$campaign.".csv");
		    readfile($filename);
        } else {
        	return false;
        }
	}

}
