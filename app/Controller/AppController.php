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
    }


    public function batch_leads($campaign_id, $reportdate){
        ini_set('max_execution_time', 10000); //increase max_execution_time to 10 min if data set is very large
        $this->Lead = ClassRegistry::init('Lead');        
        $conditions = " AND campaign_id IN($campaign_id)";
    
        $qcount = "
            SELECT SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( track_id, '9', 1 ) , '8', 1 ) , '7', 1 ) , '6', 1 ) , '5', 1 ) , '4', 1 ) , '3', 1 ) , '2', 1 ) , '1', 1 ) , '0', 1 ) AS tid, COUNT( DISTINCT email ) AS trackcount, email
            FROM leads
            WHERE created
            BETWEEN DATE_SUB('$reportdate', INTERVAL 1 DAY) AND NOW()
            $conditions
            GROUP BY tid
            ORDER BY tid 
        ";        
        $qreport = "
            SELECT *, 
            ( SELECT DISTINCT email ) AS email,
            ( SELECT DISTINCT track_id ) AS cid
            FROM leads
            WHERE created
            BETWEEN DATE_SUB( '$reportdate', INTERVAL 1 DAY ) AND NOW()
            $conditions
            GROUP BY email, cid
            ORDER BY created
        ";
    
        //create a file
        $campaign_id = str_replace(',', '.', $campaign_id);
        $attachment = "../../app/tmp/downloads/".$reportdate."_$campaign_id.csv";
        $csv_file   = fopen($attachment, 'w');
        $results    = $this->Lead->query($qreport);

        $th         = array();
        $x          = 0;
        foreach ($results as $lreport_key => $lreport_value):
            $td = array();
            $leads = json_decode($lreport_value['leads']['lead']);
            foreach (@$leads as $leadkey => $leadval) {
                if($x <= 0) { array_push($th, $leadkey); }
                array_push($td, $leadval);
            }
            if($x <= 0) { fputcsv($csv_file,$th,',','"'); }
            $x++;
            fputcsv($csv_file,$td,',','"');
            unset($td);
        endforeach;
        fclose($csv_file);

        $leads_count = $this->Lead->query($qcount);

        $count = "<h4>Leads Campaign Summary</h4>";
        $count .= '<table><tr><th>Campaign ID</th><th>Total</th></tr>';
        foreach ($leads_count as $lcount_key => $lcount_value) {
            $count .= '<tr><td>'.strtoupper($lcount_value[0]['tid']).'</td>';
            $count .= '<td>'.$lcount_value[0]['trackcount'].'</td></tr>';
        } 
        $count .= '</table>';

        return array(
            'attachment'    => $attachment, 
            'count'         => $count
        );
    }

    public function batch_logs (){
        return true;
    }
}

CakePlugin::load('Acl', array('bootstrap' => true));