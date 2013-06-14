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

        $rlead = array('[', ']', '][');
        $rleaded = array('%"', '"%', '": "');

        if($group == 'administrators') {
        	$campaigns = $this->Campaign->find('list');
            $this->set('campaigns', $campaigns);
        } else {
        	$campaigns = $this->Campaign->find('list', array('conditions' => array('user_id' => $user['id']), 'recursive' => -1));   
            $this->set('campaigns', $campaigns);
        }
        	
        $campaign_ids 	= (!empty($_GET['cid'])) 	? array('Lead.campaign_id' => explode('.', $_GET['cid'])) : '';

        if(isset($_GET['mod'])) {
        	$getid 		= (!empty($_GET['id'])) 		? array('Lead.id' => $_GET['id']) : '';
        	$email 		= (!empty($_GET['email'])) 		? array('Lead.email' => $_GET['email']) : '';
        	$ip 		= (!empty($_GET['ip'])) 		? array('Lead.ip' => $_GET['ip']) : '';
        	$created 	= (!empty($_GET['created'])) 	? array('Lead.created' => $_GET['created']) : '';
        	$lead 		= (!empty($_GET['lead'])) 		? array('Lead.lead LIKE' => str_replace($rlead, $rleaded, $_GET['lead'])) : '';
        }

        if(isset($_GET['cid'])){ # Report Results
            $leads_header = $this->Lead->find('first', array(
            	'conditions' => array($campaign_ids), 'recursive' => -1
            	));
	        $leads 	= json_decode($leads_header['Lead']['lead']);
	        $leads 	= get_object_vars($leads);
	        $leads 	= array_keys($leads);
        	$rows	= array();
        	$row    = array();

	        foreach ($leads as $key => $value) {
	            array_push($row, $value);
	        }           
	        array_push($rows, $row);
            $this->set('cheader', $rows);

            $this->paginate = array(
		        'conditions' => array(@$getid, @$lead, @$campaign_ids, @$email, @$ip, @$created),
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
        $rplcReferer 		= array('http://', 'https://');
        $rplcReferered 		= array('', '');
        if ($this->request->is('post')) {
	        $client_ip  	= $this->request->clientIp();
        	$pagesource 	= str_replace($rplcReferer,$rplcReferered,$_SERVER["HTTP_REFERER"]);
	        $campaign_id   	= $_GET['campaign'];    
	        $campaign 		= $this->Lead->Campaign->find('first', array(
	        	'conditions' 	=> array('Campaign.id' => $campaign_id),
	        	'recursive' 	=> -1,
	        	'fields' 		=> array('Campaign.external', 'Campaign.rules', 'Campaign.method'),
	        ));
	        $postURL    = $campaign['Campaign']['external'];
	        $rules    	= $campaign['Campaign']['rules'];
	        $method    	= $campaign['Campaign']['method'];	        
	        $this->Lead->set($this->request->data);
	        $validates 	= json_decode($rules, true);

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
	            foreach ($validates as $key => $value) {
	            	@$rule = (!is_array($value[0]['rule']))? $value[0]['rule']: $value[0]['rule'][0];
	                if($rule == 'email'){ # look for email field
	                    $emailfield = $key;
	                } elseif($rule == 'trackid') { # look for trackid field
	                    $trackidfield = $key;
	                }
	            }
	            $email = $posted[$emailfield]; # user predefine in campaign table
	            $trackid = $posted[$trackidfield]; # user predefine in campaign table
	            $redirect = isset($posted['redirect']) ? "http://".$posted['redirect'] : array('action' => 'posted'); # fetch 'redirect' value for conversion page redirect
	            # reformat lead entry
	            $keyvalidate = array_keys($validates);
	            $repost = array();
	            foreach ($keyvalidate as $key => $value) {
	            	$repost[$value] = @$posted[$value];
	            }
	            $leads = array( 
	                'Lead'  => array(
	                'lead'     		=> json_encode($repost), # compact lead values into one field
	                'email'     	=> $email,
	                'campaign_id'  	=> $campaign_id,
	                'track_id'  	=> $trackid,
	                'ip'       		=> $client_ip,
	                'source' 		=> $pagesource
	            ));
                $this->Lead->create();
                if ($this->Lead->save($leads, array('validate' => false))) {
	                if(!empty($postURL)){  #cURL post, then logged
	                    $this->Log = ClassRegistry::init('Log');
	                    $log = $this->Lead->postExternal($postURL);
        				$logreferer = str_replace($rplcReferer,$rplcReferered,$_SERVER["HTTP_REFERER"]);
	                    $logs = array( 
	                        'Log'           => array(
	                        'leads_id'      => $this->Lead->id,
	                        'campaign_id'   => $campaign_id,
	                        'referer'       => $logreferer,
	                        'ip'            => $client_ip,
	                        'logs'          => trim($log['logs']),
	                        'type'          => $log['type']
	                    ));
	                    if(!empty($log['logs'])){
	                    	$this->Log->saveAll($logs, array('validate' => false)); #save to logs
	                    }
	                }
                    $message = array(array('module' => array('insert'),'status' => array('success')));

                    if($method == 0){
                        echo $this->Lead->displayMethod($method,$message);
                    } else {
                        $this->redirect($redirect); #conversion page redirect
                    }
                } else {
                    $message = array(array('module' => array('insert'),'status' => array('failed')));
                    if($method == 0){
                        echo $this->Lead->displayMethod($method,$message);
                    } else {
                        $this->redirect($this->Lead->displayMethod($method,$message));
                    }
                }
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
