<?php
App::uses('AppController', 'Controller');
/**
 * Campaigns Controller
 *
 * @property Campaign $Campaign
 */
class CampaignsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Campaign->recursive = 0;
		$this->set('campaigns', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Campaign->recursive = -1;
		if (!$this->Campaign->exists($id)) {
			throw new NotFoundException(__('Invalid campaign'));
		}
		$options = array('conditions' => array('Campaign.' . $this->Campaign->primaryKey => $id));
		$this->set(
			'campaign', 
			$this->Campaign->find('first', $options)
		);
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
        $this->User = ClassRegistry::init('User');
        $user = $this->Auth->user();
		$this->set('user', $user);
		$this->set('userlist', $this->User->find('all', array('fields' => array('User.id', 'User.username'), 'recursive' => -1)));

        if ($this->request->is('post')) {
            $this->Campaign->create();

            $posted    = $this->request->data;
            $name      = $posted['Campaign']['name'];
            $alias     = str_replace(" ", "", strtolower($posted['Campaign']['alias']));
            $external  = $posted['Campaign']['external'];
            $rules     = $posted['Campaign']['rules'];
            $method     = $posted['Campaign']['method'];
            $user_id   = $posted['Campaign']['user_id'];
            $note      = $posted['Campaign']['note'];

            foreach ($rules as $key) {
                $allrules[$key['fieldname']] = array();

                @$required = CampaignsController::required($key['required']);
                $fieldtype = CampaignsController::format($key['fieldtype'],@$key['fieldprop']);
                if($key['fieldtype'] == 'email') {                    
                    $rule = array('rule' => array('email', true), 'message' => 'Please supply a valid email address.');
                    array_push($allrules[$key['fieldname']], $rule);
                }


                @$fieldtypearray['rule_format'] = (is_null($fieldtype))? '' : $fieldtype;
                @$requiredarray['rule_required'] = (is_null($required))? '' : $required;

                if(!empty($fieldtype)){
                    array_push($allrules[$key['fieldname']], @$fieldtypearray['rule_format']);                    
                }
                if(!empty($required)){
                    array_push($allrules[$key['fieldname']], @$requiredarray['rule_required']);
                }

            }

            $leads = array(
            	"name" 		=> $name, 
            	"alias" 	=> $alias, 
            	"external" 	=> $external, 
            	"rules" 	=> json_encode($allrules), 
            	"method" 	=> $method, 
            	"user_id" 	=> $user_id, 
            	"note" 		=> $note
            );

            if ($this->Campaign->save($leads, array('validate' => false))) {
                $this->Session->setFlash('Your campaign has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add your campaign.');
            }
        }
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
        $this->User = ClassRegistry::init('User');
        $user = $this->Auth->user();
		$this->set('user', $user);
		$this->set('userlist', $this->User->find('all', array('fields' => array('User.id', 'User.username'), 'recursive' => -1)));
		$this->Campaign->recursive = -1;
        $this->Campaign->id = $id;
        $this->set('campaigns', $this->Campaign->read());
        if ($this->request->is('get')) {
            $this->request->data = $this->Campaign->read();
        } else {
            $posted    = $this->request->data;
            $name      = $posted['Campaign']['name'];
            $alias     = str_replace(" ", "", strtolower($posted['Campaign']['alias']));
            $external  = $posted['Campaign']['external'];
            $rules     = $posted['Campaign']['rules'];
            $method    = $posted['Campaign']['method'];
            $user_id   = $posted['Campaign']['user_id'];
            $note      = $posted['Campaign']['note'];
            // var_dump($rules);
            foreach ($rules as $key) {
                @$allrules[$key['fieldname']] = array();
                @$required = $this->Campaign->required($key['required']);
                @$fieldtype = $this->Campaign->format($key['fieldtype'],@$key['fieldprop']);
                if($key['fieldtype'] == 'email') {                    
                    @$rule = array('rule' => array('email', true), 'message' => 'Please supply a valid email address.');
                    array_push($allrules[$key['fieldname']], $rule);
                }
                @$fieldtypearray['rule_format'] = (is_null($fieldtype))? '' : $fieldtype;
                @$requiredarray['rule_required'] = (is_null($required))? '' : $required;

                if(!empty($fieldtype)){
                    array_push($allrules[$key['fieldname']], @$fieldtypearray['rule_format']);                    
                }
                if(!empty($required)){
                    array_push($allrules[$key['fieldname']], @$requiredarray['rule_required']);
                }
            }

            // exit;
            $leads = array(
                "name"      => $name, 
                "alias"     => $alias, 
                "external"  => $external, 
                "rules"     => json_encode($allrules), 
                "method"    => $method, 
                "user_id"   => $user_id, 
                "note"      => $note
            );
            if ($this->Campaign->save($leads)) {
                $this->Session->setFlash('Your campaign has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to update your campaign.');
            }
        }
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Campaign->id = $id;
		if (!$this->Campaign->exists()) {
			throw new NotFoundException(__('Invalid campaign'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Campaign->delete()) {
			$this->Session->setFlash(__('Campaign deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Campaign was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
