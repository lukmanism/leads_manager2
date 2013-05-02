<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $components = array(
        'Acl',
        'Auth' => array(
            'authorize' => array(
                'Actions' => array('actionPath' => 'controllers')
            )
        ),
        'Session'
    );
    var $helpers = array('Html', 'Form', 'Session');

    function beforeFilter() {
        //Configure AuthComponent
        // $this->Auth->authorize = 'actions';
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
        $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
        $this->Auth->loginRedirect = array('controller' => 'leads', 'action' => 'index');
        $this->Auth->allow();
    }

    public function ajax() {
        $this->autoRender = false; 
        $model  = isset($_REQUEST['model'])  ? $_REQUEST['model']  :  0;
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


        $query = "SELECT * FROM $model WHERE $where";
        $query .= " ORDER BY $sortProperty $sortDirection";
        $query .= " LIMIT $start, $count";
        $countQuery = "SELECT COUNT(id) as count FROM $model WHERE $where";

        switch($model) {
            case 'campaigns':
                $result = $this->Campaign->query($query);
                $countQ = $this->Campaign->query($countQuery);
            break;
            case 'leads':
                $result = $this->Lead->query($query);
                $countQ = $this->Lead->query($countQuery);
            break;
            case 'logs':
                $result = $this->Log->query($query);
                $countQ = $this->Log->query($countQuery);
            break;
        }


        $count = $countQ[0][0]['count'];

        $rows = array();

        if($model == 'leads'){
            for ($i=0; $i < $count ; $i++) { 
                if(!empty($result[$i][$model])){
                    $thisrow['id']              = $result[$i][$model]['id'];
                    $leads = json_decode($result[$i][$model]['lead']);
                    foreach (@$leads as $leadkey => $leadval) {
                        @$thisrow[$leadkey] = $leadval;
                    }              
                    @$thisrow['campaign_id']    = $result[$i][$model]['campaign_id'];
                    @$thisrow['email']          = $result[$i][$model]['email'];
                    @$thisrow['ip']             = $result[$i][$model]['ip'];
                    @$thisrow['created']        = $result[$i][$model]['created'];
                    array_push($rows, $thisrow);
                }
            }
        } else {
            for ($i=0; $i < $count ; $i++) { 
                if(!empty($result[$i][$model]))
                array_push($rows, $result[$i][$model]);
            }
        }

        echo json_encode(array(
            "total"=>$count,
            "data"=>$rows
        ));
    }

    public function array_format(){

        print_r($result[$i][$model]).'</br>';

        // if(array_key_exists('lead', $result[$i][$model])) {   
        if($result[$i][$model]['lead']) {   
            // echo $result[$i][$model];       
            echo '+';   
            // $leads = json_decode($result[$i][$model]['lead']);
            // foreach (@$leads as $leadkey => $leadval) {
            //     $rows[$i][$leadkey] = $leadval;
            // }  
        } else {   
            echo '-';
            // array_push($rows, $result[$i][$model]);  
        } 
    }

}

CakePlugin::load('Acl', array('bootstrap' => true));