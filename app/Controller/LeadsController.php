<?php
App::uses('AppController', 'Controller');
/**
 * Leads Controller
 *
 * @property Lead $Lead
 */
class LeadsController extends AppController {

	public function beforeFilter() {
	    parent::beforeFilter();
	    $this->Auth->allow('incoming');
	}


/**
 * index method
 *
 * @return void
 */
	public function index() {
        $this->Campaign = ClassRegistry::init('Campaign');
        $user = $this->Auth->user();
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
            $leads_header = $this->Lead->find('first', array('conditions' => array('Lead.campaign_id' => $campaign_ids)));
	        $leads = json_decode($leads_header['Lead']['lead']);
	        $leads = get_object_vars($leads);
	        $leads = array_keys($leads);
        	$rows   = array();
        	$row    = array('id');

	        foreach ($leads as $key => $value) {
	            array_push($row, $value);
	        }           
	        array_push($row, 'campaign', 'ip', 'created');
	        array_push($rows, $row);
            $this->set('campaignview', $rows);
            $this->set('campaign_id', $campaign_ids);
        }
	}
    
    public function ajax() { 
        $this->Campaign = ClassRegistry::init('Campaign');
        $this->autoRender = false; 
        $user = $this->Auth->user();
        $group = $user['Group']['name'];
        $c_id = stristr($_GET['campaign_id'], ',') ? explode(',', $_GET['campaign_id']): $_GET['campaign_id'];

        if($group == 'administrators') {
        	$campaigns = $this->Campaign->find('list');
        	@$incomings = $this->Lead->find('all', array('conditions' => array('campaign_id' => $c_id)));
        } elseif($group == 'managers') {
        	$campaigns = $this->Campaign->find('list', array('conditions' => array('user_id' => $user['id'])));
        	@$incomings = $this->Lead->find('all', array('conditions' => array('campaign_id' => $c_id)));
        }

        $rows   = array();
        foreach ($incomings as $key => $incoming ):            
	        $leads  = json_decode($incoming['Lead']['lead']); 
	        $row    = array($incoming['Lead']['id']);

		        foreach ($leads as $key2 => $lead) {
		            array_push($row, $lead);
		        }           
	        array_push($row, $incoming['Lead']['campaign_id'], $incoming['Lead']['ip'], $incoming['Lead']['created']);
	        array_push($rows, $row);
        endforeach;

        $json = isset($rows)? json_encode($rows) : '';
        echo '{ "aaData": '.$json.'}';
    }


    public function csv() {
		$date = date('Ymd'); 
        $user = $this->Auth->user('username');
        $this->autoRender = false;
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$date.' - '.$user.' - leads.csv"');
        if (isset($_POST['csv'])) {
            $csv = $_POST['csv'];
            echo $csv;
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
		if (!$this->Lead->exists($id)) {
			throw new NotFoundException(__('Invalid lead'));
		}
		$options = array('conditions' => array('Lead.' . $this->Lead->primaryKey => $id));
		$this->set('lead', $this->Lead->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	// public function incoming() { #add 
	// 	if ($this->request->is('post')) {
	// 		$this->Lead->create();
	// 		if ($this->Lead->save($this->request->data)) {
	// 			$this->Session->setFlash(__('The lead has been saved'));
	// 			$this->redirect(array('action' => 'index'));
	// 		} else {
	// 			$this->Session->setFlash(__('The lead could not be saved. Please, try again.'));
	// 		}
	// 	}
	// 	$campaigns = $this->Lead->Campaign->find('list');
	// 	$this->set(compact('campaigns'));
	// }

	public function incoming() {
        $this->autoRender = false; 
        if ($this->request->is('post')) {
	        $this->Campaign = ClassRegistry::init('Campaign');
	        $client_ip  = $this->request->clientIp();
	        $campaign_id   = $_GET['campaign'];    

	        $campaign = $this->Campaign->find('first', array(
	        	'conditions' 	=> array('Campaign.id' => $campaign_id),
	        	'recursive' 	=> -1,
	        	'fields' 		=> array('Campaign.external', 'Campaign.rules', 'Campaign.method'),
	        ));

	        $postURL    = $campaign['Campaign']['external'];
	        $rules    	= $campaign['Campaign']['rules'];
	        $method    	= $campaign['Campaign']['method'];
	        
	        $this->Lead->set($this->request->data);
	        $validates = json_decode($rules, true);
	        $this->Lead->validate = $validates;

	        if ($this->Lead->validates()) { // it validated logic
	            $posted = $this->request->data;
	            $emailfield     = json_decode($rules);
	            foreach ($emailfield as $key => $value) {
	                if(stristr($key,'email')){ # look for email field. must have '*email*' keyword in
	                    $emailfield = $key;
	                    break;
	                }
	            }
	            $email = $posted[$emailfield]; # user predefine in campaign table
	            $redirect = isset($posted['redirect']) ? "http://".$posted['redirect'] : array('action' => 'posted'); # fetch 'redirect' value for conversion page redirect

	            # reformat lead entry
	            $keyvalidate = array_keys($validates);
	            $repost = array();
	            foreach ($keyvalidate as $key => $value) {
	            	$repost[$value] = @$posted[$value];
	            }

	            $leads = array( 
	                'Lead'  => array(
	                "lead"     		=> json_encode($repost), # compact lead values into one field
	                "email"     	=> $email, 
	                "campaign_id"  => $campaign_id, 
	                "ip"       	=> $client_ip
	            ));

	                $this->Lead->create();
	                if ($this->Lead->save($leads, array('validate' => false))) {

		                if(!empty($postURL)){  #cURL post, then logged
		                    $this->Log = ClassRegistry::init('Log');
		                    $log = $this->Lead->postExternal($postURL);
		                    $logs = array( 
		                        'Log'           => array(
		                        'leads_id'      => $this->Lead->id,
		                        'campaign_id'   => $campaign,
		                        'referer'       => str_replace('http://', '', $_SERVER['HTTP_REFERER']),
		                        'ip'            => $client_ip,
		                        'logs'          => trim($log['logs']),
		                        'type'          => $log['type']
		                    ));
		                    $this->Log->saveAll($logs['Log']); #save to logs
		                }
	                    $message = array(array('module' => array('insert'),'status' => array('success')));

	                    if($method == 0){
	                        echo $this->Lead->displayMethod($method,$message);
	                    } else {
	                        $this->redirect($redirect);
	                    }
	                } else {
	                    $message = array(array('module' => array('insert'),'status' => array('failed')));
	                    if($method == 0){
	                        echo $this->Lead->displayMethod($method,$message);
	                    } else {
	                        $this->redirect($this->Lead->displayMethod($method,$message));
	                    }
	                }
	            // }
	        } else { // didn't validate logic
	            $message = array(array('module' => array('validation'),'status' => array('failed')));
	            array_push($message, $this->Lead->validationErrors);
	            if($method == 0){
	                echo $this->Lead->displayMethod($method,$message);
	            } else {
	                $this->redirect($this->Lead->displayMethod($method,$message));
	            }
	        }
	    } else {
	    	echo '[{"module":["incoming"],"status":["disabled"]]';
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
		if (!$this->Lead->exists($id)) {
			throw new NotFoundException(__('Invalid lead'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Lead->save($this->request->data)) {
				$this->Session->setFlash(__('The lead has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The lead could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Lead.' . $this->Lead->primaryKey => $id));
			$this->request->data = $this->Lead->find('first', $options);
		}
		$campaigns = $this->Lead->Campaign->find('list');
		$this->set(compact('campaigns'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Lead->id = $id;
		if (!$this->Lead->exists()) {
			throw new NotFoundException(__('Invalid lead'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Lead->delete()) {
			$this->Session->setFlash(__('Lead deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Lead was not deleted'));
		$this->redirect(array('action' => 'index'));
	}


}
