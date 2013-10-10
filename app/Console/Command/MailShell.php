<?php 
/*
    Setup a cron scheduler from your webserver panel.
    make sure you're able to execute shell command from here.
    command i.e.:
        /home/leads/public_html/app/Console/cake mail batch -q

    *note: path to console depending on your cake configuration
*/
class MailShell extends AppShell {
    public $uses = array('Email');

    public function batch(){
        $this->User = ClassRegistry::init('User');
        ini_set('max_execution_time', 10000); //increase max_execution_time to 10 min if data set is very large
        date_default_timezone_set('America/Los_Angeles');
        $reportdate = date('Y-m-d'); #depending on the cronjob trigger time i.e. daily, hourly

        $emails = $this->Email->find('all', array(
            'conditions'    => array('Email.published' => 1), # TEMP EMAIL ID
            'recursive'     => -1
        ));

        if(!empty($emails)){
        # Look for batch available from Emails (published)
        foreach($emails as $key => $email):
            $model = json_decode($email['Email']['model']);
            $model_name = $model->model;
            $model_id = $model->model_id;

            switch($model_name){
                case 'logs':
                    $batch_logs = $this->batch_logs($reportdate);
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
                    $attachment = '';
                break;
            }
            $model_id = str_replace(',', '.', $model_id);

            if($attachment){
                $fileattach = "http://leads.e-storm.com/emails/download?campaign=$model_id&report=$reportdate"; #to be change according to the server setup. variable must be able to run under shell environment
                $attachment = str_replace(',', '.', $attachment);
            } else {
                echo 'No reports generated for today.';
                exit;
            }

            $replace    = array('{REPORTDATE}', '{ATTACHMENT}', '{REPORTSUMMARY}');
            $replaced   = array($reportdate, $fileattach, $count);

            if($email){
            sleep(10); #delay 10sec for attachment file to be ready
            $from       = $this->User->find('first', array(
                'conditions'    => array('User.id' => $email['Email']['user_id']),         
                'recursive'     => -1, //int
                'fields'        => array('User.email','User.name')
            ));
            $to         = explode(',', $email['Email']['to']);
            $subject    = str_replace($replace, $replaced, $email['Email']['subject']);
            $body       = $email['Email']['body'];
            $footer     = $email['Email']['footer']; 
            $data       = str_replace($replace, $replaced, $body.$footer);

            App::uses('CakeEmail', 'Network/Email');
            $Email = new CakeEmail();
            $Email->template('default', 'default');
            $Email->from(array($from['User']['email'] => $from['User']['name']));
            $Email->to($to);
            if(isset($email['Email']['cc'])){ 
                $cc = explode(',', $email['Email']['cc']);
                $Email->cc($cc); 
            }            
            $Email->subject($subject);
            if($attachment){ $Email->attachments($attachment); }
            $Email->emailFormat('both');
            $mailstatus = $Email->send($data);
            echo $mailstatus ? 'Email sent' : 'Email not sent';
            }
        endforeach;        
        } else {
            echo 'No queue has been found.';
            exit;
        }
    }

    public function batch_leads($campaign_id, $reportdate){
        ini_set('max_execution_time', 10000); //increase max_execution_time to 10 min if data set is very large
        $this->Lead = ClassRegistry::init('Lead');        
        $conditions = " WHERE created
            BETWEEN DATE_SUB('$reportdate 00:00:00', INTERVAL 1 DAY) AND NOW()
            AND track_id NOT LIKE '%test%'
            AND campaign_id IN($campaign_id)";    
        $qcount = "
            SELECT SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( track_id, '9', 1 ) , '8', 1 ) , '7', 1 ) , '6', 1 ) , '5', 1 ) , '4', 1 ) , '3', 1 ) , '2', 1 ) , '1', 1 ) , '0', 1 ) AS tid, COUNT( DISTINCT email ) AS trackcount, email
            FROM leads            
            $conditions
            GROUP BY tid
            ORDER BY tid";        
        $qreport = "
            SELECT *, 
            ( SELECT DISTINCT email ) AS email,
            ( SELECT DISTINCT track_id ) AS cid
            FROM leads
            $conditions
            GROUP BY email, cid
            ORDER BY created";    

        $leads_count = $this->Lead->query($qcount);
        if($leads_count) {            
            //create a file            
            $campaign_id = str_replace(',', '.', $campaign_id);        
            $attachment = "/home/leads/public_html/app/tmp/downloads/".$reportdate."_$campaign_id.csv"; #to be change according to the server setup. variable must be able to run under shell environment
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

            $count = "<h4>Leads Campaign Summary</h4>";
            $count .= '<table><tr><th>Campaign ID</th><th>Total</th></tr>';
            foreach ($leads_count as $lcount_key => $lcount_value) {
                $tcount = ($lcount_value[0]['trackcount'])? $lcount_value[0]['trackcount'] : 0;
                $count .= '<tr><td>'.strtoupper($lcount_value[0]['tid']).'</td>';
                $count .= '<td>'.$tcount.'</td></tr>';
            } 
            $count .= '</table>';

            return array(
                'attachment'    => $attachment, 
                'count'         => $count
            );
        } else {
            return false;
        }


    }

    public function batch_logs ($reportdate){ #Pending
        ini_set('max_execution_time', 10000); //increase max_execution_time to 10 min if data set is very large
        $this->Log = ClassRegistry::init('Log');       
        $qreport = "
            SELECT *
            FROM logs 
            WHERE created
            BETWEEN DATE_SUB('$reportdate 00:00:00', INTERVAL 1 DAY) AND NOW()";    

        $logs    = $this->Log->query($qreport);
        if($logs) {            
            //create a file                 
            $attachment = "/home/leads/public_html/app/tmp/downloads/".$reportdate."_logs.csv"; 
            $csv_file   = fopen($attachment, 'w');
            $th         = array('id','leads_id','campaign_id','referer','ip','logs','type','created');
            $x          = 0;
            foreach ($logs as $key => $val):
                $td = array();
                array_push($td, $val['logs']['id']);
                array_push($td, $val['logs']['leads_id']);
                array_push($td, $val['logs']['campaign_id']);
                array_push($td, $val['logs']['referer']);
                array_push($td, $val['logs']['ip']);
                array_push($td, $val['logs']['logs']);
                array_push($td, $val['logs']['type']);
                array_push($td, $val['logs']['created']);
                if($x <= 0) { fputcsv($csv_file,$th,',','"'); }
                $x++;
                fputcsv($csv_file,$td,',','"');
                unset($td);
            endforeach;
            fclose($csv_file);

            return array(
                'attachment'    => $attachment, 
                'count'         => ''
            );
        } else {
            return false;
        }


    }


}
?>
