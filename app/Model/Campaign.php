<?php
App::uses('AppModel', 'Model');
/**
 * Campaign Model
 *
 * @property User $User
 * @property Lead $Lead
 * @property Log $Log
 */
class Campaign extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'alias' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'method' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Lead' => array(
			'className' => 'Lead',
			'foreignKey' => 'campaign_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Log' => array(
			'className' => 'Log',
			'foreignKey' => 'campaign_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

    public function required($val) {
        if(isset($val)){
            $required = array('rule' => 'notEmpty', 'message' => 'This field cannot be left blank');
        } else {
            $required = "";
        }
        return $required;
    }

    public function format($val,$prop=null) {
        switch($val){
            case 'alphaNumeric':
                $rule = array('rule' => array('alphaNumeric', true), 'message' => 'Field must only contain letters and numbers.');
            break;
            case 'numeric':
                $rule = array('rule' => array('numeric', true), 'message' => 'Field must only contain numbers only.');
            break;
            case 'blank':
                $rule = array('rule' => array('blank'));
            break;
            case 'boolean':
                $rule = array('rule' => array('boolean'));
            break;
            case 'email':
                $rule = array('rule' => 'emailDuplicate', 'message' => 'Email address has been registered.');
            break;
            case 'phone':
                $rule = array('rule' => array('phone', null, "$prop"));
            break;
            case 'postal':
                $rule = array('rule' => array('postal', null, "$prop"));
            break;
            case 'custom':
                $rule = array('rule' => array('custom', null, "$prop"));
            break;
            default:
                $rule = "";
            break;
        }
        return $rule;
    }
}
