<?php
App::uses('AppController', 'Controller');
/**
 * Campaigns Controller
 *
 * @property Campaign $Campaign
 */
class CampaignsController extends AppController {
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }
/**
 * index method
 *
 * @return void
 */
	public function index() {
        $this->set('user', $this->Auth->user());
		// $this->Campaign->recursive = 0;
		// $this->set('campaigns', $this->paginate());
        // collect request parameters
	}

    public function ajax() {
        $this->autoRender = false; 
        $start  = isset($_REQUEST['start'])  ? $_REQUEST['start']  :  0;
        $count  = isset($_REQUEST['limit'])  ? $_REQUEST['limit']  : 50;
        $sort   = isset($_REQUEST['sort'])   ? json_decode($_REQUEST['sort'])   : null;
        $filters = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : null;

        $sortProperty = $sort[0]->property; 
        $sortDirection = $sort[0]->direction;

        // GridFilters sends filters as an Array if not json encoded
        if (is_array($filters)) {
            $encoded = false;
        } else {
            $encoded = true;
            $filters = json_decode($filters);
        }

        $where = ' 0 = 0 ';
        $qs = '';

        // loop through filters sent by client
        if (is_array($filters)) {
            for ($i=0;$i<count($filters);$i++){
                $filter = $filters[$i];

                // assign filter data (location depends if encoded or not)
                if ($encoded) {
                    $field = $filter->field;
                    $value = $filter->value;
                    $compare = isset($filter->comparison) ? $filter->comparison : null;
                    $filterType = $filter->type;
                } else {
                    $field = $filter['field'];
                    $value = $filter['data']['value'];
                    $compare = isset($filter['data']['comparison']) ? $filter['data']['comparison'] : null;
                    $filterType = $filter['data']['type'];
                }

                switch($filterType){
                    case 'string' : $qs .= " AND ".$field." LIKE '%".$value."%'"; Break;
                    case 'list' :
                        if (strstr($value,',')){
                            $fi = explode(',',$value);
                            for ($q=0;$q<count($fi);$q++){
                                $fi[$q] = "'".$fi[$q]."'";
                            }
                            $value = implode(',',$fi);
                            $qs .= " AND ".$field." IN (".$value.")";
                        }else{
                            $qs .= " AND ".$field." = '".$value."'";
                        }
                    Break;
                    case 'boolean' : $qs .= " AND ".$field." = ".($value); Break;
                    case 'numeric' :
                        switch ($compare) {
                            case 'eq' : $qs .= " AND ".$field." = ".$value; Break;
                            case 'lt' : $qs .= " AND ".$field." < ".$value; Break;
                            case 'gt' : $qs .= " AND ".$field." > ".$value; Break;
                        }
                    Break;
                    case 'date' :
                        switch ($compare) {
                            case 'eq' : $qs .= " AND ".$field." = '".date('Y-m-d',strtotime($value))."'"; Break;
                            case 'lt' : $qs .= " AND ".$field." < '".date('Y-m-d',strtotime($value))."'"; Break;
                            case 'gt' : $qs .= " AND ".$field." > '".date('Y-m-d',strtotime($value))."'"; Break;
                        }
                    Break;
                }
            }
            $where .= $qs;
        }

        $query = "SELECT id, name, alias, external, method, user_id, note, created FROM campaigns WHERE ".$where;
        $query .= " ORDER BY ".$sortProperty." ".$sortDirection;
        $query .= " LIMIT ".$start.",".$count;
        $result = $this->Campaign->query($query);

            $countQuery = "SELECT COUNT(id) as count FROM campaigns WHERE ".$where;
            $countQ = $this->Campaign->query($countQuery);
            $count = $countQ[0][0]['count'];

        $rows = array();
        for ($i=0; $i < $count ; $i++) { 
            if(!empty($result[$i]['campaigns']))
            array_push($rows, $result[$i]['campaigns']);
        }

        echo json_encode(array(
            "total"=>$count,
            "data"=>$rows
        ));
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

                @$required = $this->Campaign->required($key['required']);
                $fieldtype = $this->Campaign->format($key['fieldtype'],@$key['fieldprop']);
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
