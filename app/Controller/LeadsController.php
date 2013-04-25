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
        $this->set('user', $user);
        $group = $user['Group']['name'];

        if(!isset($_GET['cid'])){
	        if($group == 'administrators') {
	        	$campaigns = $this->Campaign->find('list');
	            $this->set('campaigns', $campaigns);
	        } else {
	        	$campaigns = $this->Campaign->find('list', array('conditions' => array('user_id' => $user['id'])));   
	            $this->set('campaigns', $campaigns);
	        }
        } else {
            $campaign_ids = explode('.', $_GET['cid']);
            $leads_header = $this->Lead->find('first', array('conditions' => array('Lead.campaign_id' => $campaign_ids)));
	        $leads = json_decode($leads_header['Lead']['lead']);
	        $leads = get_object_vars($leads);
	        $leads = array_keys($leads);
        	$rows   = array();
        	$row    = array();

	        foreach ($leads as $key => $value) {
	            array_push($row, $value);
	        }           
	        array_push($rows, $row);
            $this->set('cheader', $rows);
            // $this->set('campaign_id', $campaign_ids);

            $this->paginate = array(
		        'conditions' => array('Lead.campaign_id' => $campaign_ids),
         		'order' => array('Lead.created' => 'DESC')
		    );	        
	        $this->set('leads', $this->paginate('Lead'));
        }
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
 * add method
 *
 * @return void
 */
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
	                "campaign_id"  	=> $campaign_id, 
	                "ip"       		=> $client_ip
	            ));

	                $this->Lead->create();
	                if ($this->Lead->save($leads, array('validate' => false))) {

		                if(!empty($postURL)){  #cURL post, then logged
		                    $this->Log = ClassRegistry::init('Log');
		                    $log = $this->Lead->postExternal($postURL);
		                    $logs = array( 
		                        'Log'           => array(
		                        'leads_id'      => $this->Lead->id,
		                        'campaign_id'   => $campaign_id,
		                        'referer'       => str_replace('http://', '', $_SERVER['HTTP_REFERER']),
		                        'ip'            => $client_ip,
		                        'logs'          => trim($log['logs']),
		                        'type'          => $log['type']
		                    ));
		                    // var_dump($logs);
		                    if(!empty($log['logs'])){
		                    	$this->Log->saveAll($logs, array('validate' => false)); #save to logs
		                    }
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



}
