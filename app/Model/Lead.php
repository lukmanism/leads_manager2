<?php
App::uses('AppModel', 'Model');
/**
 * Lead Model
 *
 * @property Campaign $Campaign
 */
class Lead extends AppModel {
	public $validate;

/**
 * Validation rules
 *
 * @var array
 */

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Campaign' => array(
			'className' => 'Campaign',
			'foreignKey' => 'campaign_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    public function emailDuplicate($check,$verdict) { # checks email duplication (post array, true/false)
        $campaign = $_GET['campaign'];
        $check = $this->emailSplitter($check);
        $result = $this->query("SELECT email FROM leads WHERE email = '".$check."' AND campaign_id = '".$campaign."' LIMIT 1");
        if(empty($result)) {
            return $verdict = 1;    
            break;       
        } else {
            $result1 = $result[0]['leads']['email'];   
                if($this->emailFormat($result1)){
                    return $result1 != $check; // returns true if no email duplication
                    break;
                }
        }
    }

    public function trackid() {
        return true;
    }

    public function phone($check,$val,$format) {
        if(is_array($check)) $check = $this->emailSplitter($check);
        $phone_regex    = "/^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/";
        $us_regex       = "/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/";
        if($format == 'us'){
            if(!preg_match($us_regex,$check)) { return false; } else { return true; }            
        } else {            
            if(!preg_match($phone_regex,$check)) { return false; } else { return true; }
        }
    }

    public function postal($check,$val,$format) {
        if(is_array($check)) $check = $this->emailSplitter($check);
        $postal_regex   = "/(^\d{5}(-\d{4})?$)|(^[ABCEGHJKLMNPRSTVXYabceghjklmnpstvxy]{1}\d{1}[A-Za-z]{1} ?\d{1}[A-Za-z]{1}\d{1})$/";
        $us_regex       = "^\d{5}(?:[-\s]\d{4})?$";
        if($format == 'us'){
            if(!preg_match($postal_regex,$check)) { return false; } else { return true; }            
        } else {            
            if(!preg_match($postal_regex,$check)) { return false; } else { return true; }
        }
    }

    function emailFormat($email) { # checks email formatting
        if(is_array($email)) $email = $this->emailSplitter($email); 

		$email_regex 	= '/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/';		
        return preg_match($email_regex,$email);
    }

    function emailSplitter($email) {
    	foreach ($email as $key => $value) {
    		return $value;
    		break;
    	}
    }

    public function postExternal ($posturl,$repost){
        $postdata = file_get_contents('php://input');
        
        $leadpost = curl_init();//open connection    
        curl_setopt($leadpost,CURLOPT_URL,$posturl);//set the url, number of POST vars, POST data
        curl_setopt($leadpost,CURLOPT_POSTFIELDS,$postdata);     
        $result = curl_exec($leadpost);//execute post    
        $curl_errno = curl_errno($leadpost); // Added logger
        $curl_error = curl_error($leadpost); 
        curl_close($leadpost);//close connection 

        $cleanout = ob_get_clean();

        if ($curl_errno > 0) {
            $logs = array(
                "logs"          => $curl_error,
                "type"          => 'ERROR'
            ); 
        } else {
          if(empty($result)) { // echo "No data received.";
            $logs = array(
                "logs"          => $this->extractPageText($cleanout),
                "type"          => 'CURL NOTICE'
            );   
          } else { // echo "Data received: $result\n";
            $logs = array(
                "logs"          => $this->extractPageText($cleanout),
                "type"          => 'NOTICE'
            );   
          }
        }

        return $logs;
    }


    public function extractPageText ($raw_text){         
        /* Get the file's character encoding from a <meta> tag */
        preg_match( '@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s+charset=([^\s"]+))?@i', $raw_text, $matches );
        @$encoding = $matches[3];
         
        /* Convert to UTF-8 before doing anything else */
        $utf8_text = iconv( $encoding, "utf-8", $raw_text );
         
        /* Strip HTML tags and invisible text */
        $utf8_text = strip_tags( $utf8_text );
         
        /* Decode HTML entities */
        $utf8_text = html_entity_decode( $utf8_text, ENT_QUOTES, "UTF-8" );

        return  $utf8_text;
    }  

    public function displayMethod ($thatmethod, $thatmessage){
        switch($thatmethod){
            case 0:
                $message = json_encode($thatmessage);
            break;
            case 1:
            $referer = (stristr($_SERVER['HTTP_REFERER'], '?'))? $_SERVER['HTTP_REFERER'].'&' : $_SERVER['HTTP_REFERER'].'?';
            $message = '';        
                foreach ($thatmessage as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $message .= $key2;
                        foreach ($value2 as $key => $value) {
                            $message .= '='.str_replace(' ', '+', $value).'&';
                        }
                    }
                }
            $message = $referer.rtrim($message, "&");
            break;
        }
        return $message;
    }  
}
