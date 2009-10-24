<?php
/**
 * SuperValidatable Model Behavior
 * 
 * Packages up some common validation rules into one neat bundle.
 * Highly experimental, all contributiosn welcome :)
 *
 * @package app.models.behaviors
 * @author Jose Diaz-Gonzalez
 * @copyright CakePHP Community
 **/
class SuperValidatableBehavior extends ModelBehavior {

/**
 * Contains configuration settings for use with individual model objects.
 * Individual model settings should be stored as an associative array,
 * keyed off of the model name.
 *
 * @var array
 * @access public
 * @see Model::$alias
 */
	var $settings = array();

/**
 * Allows the mapping of preg-compatible regular expressions to public or
 * private methods in this class, where the array key is a /-delimited regular
 * expression, and the value is a class method.  Similar to the functionality of
 * the findBy* / findAllBy* magic methods.
 *
 * @var array
 * @access public
 */
	var $mapMethods = array();


/**
 * Initiate Validatable Behavior
 *
 * @param object $model
 * @param array $config
 * @return void
 * @access public
 */
	function setup(&$model, $config = array()) {

	}

/**
 * Compares whether or not a date is some number of days after a date
 *
 * @param string $check a valid date string
 * @param string $params 
 * 						-'days' 	minimum number of days in the future,
 * 						-'field'	field to compare to
 * @return boolean true if dates differ by at least X days, false in all other cases
 * @author Jose Diaz-Gonzalez
 * @link http://snipplr.com/view/2223/get-number-of-days-between-two-dates/
 */
	public function daysInFuture($check, $params = array()) {
		if (Validation::date(reset($check), 'ymd')) {
			if (function_exists('date_default_timezone_set')) {
				date_default_timezone_set('US/Eastern');
			}
			// First we need to break these dates into their constituent parts:
			if (isset($params['field'])) {
				$gd_a = getdate(strtotime($model->data[$model->alias][$params['field']]));
			} else {
				$gd_a = getdate(strtotime(date('Y-m-d')));
			}
			$gd_b = getdate(strtotime(reset($check)));

			// Now recreate these timestamps, based upon noon on each day
			// The specific time doesn't matter but it must be the same each day
			$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
			$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );

			// Subtract these two numbers and divide by the number of seconds in a
			//  day. Round the result since crossing over a daylight savings time
			//  barrier will cause this time to be off by an hour or two.
			return round(abs($a_new - $b_new) / 86400) >= $params['days'];
		}
		return false;
	}
/**
 * Returns true if a checkbox is checked, fals otherwise
 * 
 * @param object $model
 * @param array $field
 * @return boolean
 */
	function isChecked(&$model, $field) {
		$value = array_values($field);
		$passed = (in_array($value[0], array('0', 0, false))) ? false : true;
		return $passed;
	}

	public function notEqualTo(&$model, $field = array(), $params = array()) { 
		foreach ($field as $key => $value) {
			foreach ($params as $param) {
				if (isset($model->data[$model->alias][$param]) and ($value == $model->data[$model->name][$param])) {
					return false;
				}
			}
		}
		return true;
	}
/**
 * Makes this field required if some other field is marked as true
 *
 * @param string $check field to check
 * @param string $params other fields that make this field required
 * @return void
 * @author Jose Diaz-Gonzalez
 */
	public function requiredByFields($check, $params = array()) {
		foreach ($params as $param) {
			if (in_array($model->data[$model->name][$param], array(true, '1')) and empty($check)) {
				return false;
			}
		}
		return true;
	}
/**
 * Validates that at least one field is not empty
 *
 * @param string $check field to check
 * @param string $params array of fields that are related
 * @return boolean true if at least one field has been filled
 * @author Jose Diaz-Gonzalez
 */
	public function validateDependentFields(&$model, $check, $params = array()) {
		$fieldKey = array_pop(array_keys($check));
		$i = count($params['fields']) + 1;
		$j = count($params['fields']);
		foreach ($params['fields'] as $fieldname) {
			if (empty($model->data[$model->alias][$fieldname])) {
				$i--;
			} else if(!Validation::minLength($model->data[$model->alias][$fieldname], $params['minLength']) or !Validation::maxLength($model->data[$model->alias][$fieldname], $params['maxLength'])){
				$j--;
			}
		}
		if (empty($model->data[$model->alias][$fieldKey])) {$i--;}
		if (Validation::minLength($model->data[$model->alias][$fieldKey], $params['minLength'])) {$j--;}
		if (($i === 0) or ($j === 0)) {
			return false;
		}
		return true;
	}
/**
 * With this you can validate a field against one or more other fields.
 * 
 * @param object $model
 * @param array $check field to confirm
 * @param array $params parameter:
 * 	- fields: array list of fields (default: array())
 *  - hash: boolean if the fields should be hashed (default: false)
 *  - hashOptions: options if hash is true:
 *  	- method: string cake's supported hash method (default: 'sha1')
 *  	- salt: boolean if cake's salt should be used (default: true)
 *  	- fields: array list of fields that should be hashed, the others are not hashed (default: array())
 *  - skip: boolean, if you want to continue on fields that don't exist, set this to true (default: false)
 * @return boolean true if all fields did match, false otherwise
 */
	function confirmFields(&$model, $check, $params = array()) {
		$valid = false;
		// default params
		$defaultParams = array(
			'fields' => array(),
			'hash' => false,
			'hashOptions' => array(
				'method' => 'sha1',
				'salt' => true,
				'fields' => array()
			),
			'skip' => false
		);
		
		$params = am($defaultParams, $params);
		$fieldKey = array_pop(array_keys($check));
		extract($params);
		foreach ($fields as $field) {
			// If skip option is false return on unset fields
			if (!isset($model->data[$model->alias][$field]) && $skip !== true) {
				return false;
			// If skipping is activated continue
			} elseif (!isset($model->data[$model->alias][$field]) && $skip === true) {
				continue;
			} else {
				// hash if hash is true and either hash all if hashOptions['fields'] is empty, or only those in there
				if ($hash === true && (empty($hashOptions['fields']) || in_array($fields, $hashOptions['fields']))) {
					$confirm = Security::hash($model->data[$model->alias][$field], $hashOptions['method'], $hashOptions['salt']);
				} else {
					$confirm = $model->data[$model->alias][$field];
				}
				// do validation
				$valid = ($confirm == $model->data[$model->alias][$fieldKey]);
				// break on false
				if (!$valid) {
					return $valid;
				}	
			}
		}
		return $valid;
	}
}
?>