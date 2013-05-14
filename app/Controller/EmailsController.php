<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Emails Controller
 *
 * @property Email $Email
 */
class EmailsController extends AppController {

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


	public function batch(){
        $this->User = ClassRegistry::init('User');
        // $this->layout = false;
        // $this->render(false);
		ini_set('max_execution_time', 10000); //increase max_execution_time to 10 min if data set is very large
		date_default_timezone_set('America/Los_Angeles');
		// $reportdate = date('Y-m-d');
		$reportdate = date('2013-04-22');

		$emails = $this->Email->find('all', array(
			'conditions' => array('Email.published' => 1), # TEMP EMAIL ID
			'recursive' => -1
		));


		# Look for batch available from Emails (published)
	foreach($emails as $key => $email):
		$model = json_decode($email['Email']['model']);
		$model_name = $model->model;
		$model_id = $model->model_id;

		switch($model_name){
			case 'logs':
				$batch_logs = $this->batch_logs($model_id);
				$attachment = $batch_logs['attachment'];
				$count = $batch_logs['count'];
			break;
			case 'leads':
				$batch_leads = $this->batch_leads($model_id, $reportdate);				
				$attachment = $batch_leads['attachment'];
				$count = $batch_leads['count'];
			break;
			default:			
				$count = '';
			break;
		}
		$model_id = str_replace(',', '.', $model_id);
		$fileattach = "http://leads.e-storm.com/emails/download?campaign=$model_id&report=$reportdate";
		$attachment = str_replace(',', '.', $attachment);

		$replace 	= array('{REPORTDATE}', '{ATTACHMENT}', '{REPORTSUMMARY}');
		$replaced 	= array($reportdate, $fileattach, $count);

		if($email):
			sleep(10);
		$cc			= explode(',', $email['Email']['cc']);
		$to			= explode(',', $email['Email']['to']);
		$from		= $this->User->find('first', array(
			'conditions' => array('User.id' => $email['Email']['user_id']),			
		    'recursive' => -1, //int
		    'fields' => array('User.email','User.name')
		));
		$subject	= str_replace($replace, $replaced, $email['Email']['subject']);
		$body		= $email['Email']['body'];
		$footer		= $email['Email']['footer']; 
		$data 		= str_replace($replace, $replaced, $body.$footer);

		$Email = new CakeEmail();
		$Email->template('default', 'default');
		$Email->from(array($from['User']['email'] => $from['User']['name']));
		$Email->to($to);
		$Email->cc($cc);
		$Email->subject($subject);
		if(isset($attachment)){
			$Email->attachments($attachment);			
		}
		$Email->emailFormat('both');
		$res = $Email->send($data);
		$this->Session->setFlash($res ? 'Email sent' : 'Email not sent');
        // return false;
        endif;

    endforeach;
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
