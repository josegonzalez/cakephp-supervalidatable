<?php
/**
 * SuperValidatable Model Behavior
 * 
 * Packages up some common validation rules into one neat bundle.
 * Highly experimental, all contributiosn welcome :)
 *
 * @package super_validatable.models.behaviors
 * @author Jose Diaz-Gonzalez
 * @author Thomas Ploch
 * @copyright 
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
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
	var $mapMethods = array(
		'/(days|months|years)InFuture/i' => 'inFuture',
		'/(days|months|years)InPast/i' => 'inPast'
	);
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
 * Compares whether or not a date is some number of days | months | years after a date
 * This is a magic method and can be called via daysInFuture, monthsInFuture and yearsInFuture
 * 
 * @param object $model
 * @param string $method the name of hte magic method
 * @param array  $check the data of the field to be checked 
 * @param integer $value of days | months | years that $check should be in the future
 * @param array $params [optional]
 * 						- 'fields'	array of fields that should be matched against (default: array())
 * 						- 'timezone' string timezone identifier (default: 'UTC')
 * @return boolean if $check is at least $value days | months | years in the future
 * @access public
 * @author Jose Diaz-Gonzales
 * @author Thomas Ploch
 * @link http://snipplr.com/view/2223/get-number-of-days-between-two-dates/
 */
	function inFuture(&$model, $method, $check, $value ,$params = array()) {
		$valid = false;
		// If $check is not a valid date
		if (!Validation::date(reset($check), 'Y-m-d')) return $valid;
		// Get the $mode from method name
		$mode = str_replace('infuture', '', $method);
		/* PHP5
		 * $mode = str_replace(low(__METHOD__), '', $method);
		 */
		// Default config
		$defaultConfig = array(
			'fields' => array(),
			'timezone' => 'UTC'
		);
		// Get options
		extract(am($defaultConfig, $params));
		if (empty($fields)) {
			return $valid;
		}
		// Setting the timezone if possible
		if (function_exists('date_default_timezone_set')) {
			date_default_timezone_set($timezone);
		}
		/*
		 * TODO: add cases for months and years to switch
		 * FIXME: refactor cases into helper functions
		 */
		switch ($mode) {
			case 'days':
				foreach ($fields as $field) {
					// First we need to break these dates into their constituent parts:
					$gd_a = getdate(strtotime($model->data[$model->alias][$field]));
					$gd_b = getdate(strtotime(reset($check)));
					// Now recreate these timestamps, based upon noon on each day
					// The specific time doesn't matter but it must be the same each day
					$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
					$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
					// Subtract these two numbers and divide by the number of seconds in a
					//  day. Round the result since crossing over a daylight savings time
					//  barrier will cause this time to be off by an hour or two.
					$valid = round(abs($a_new - $b_new) / 86400) >= $params['days'];
					if (!$valid) {
						return $valid;
					}
				}
				return $valid;
			default:
				return $valid;
		}
	}
/**
 * Returns true if a checkbox is checked, false otherwise
 * 
 * @param object $model
 * @param array $field
 * @return boolean true if checked, false otherwise
 * @author Jose Diaz-Gonzalez
 * @access public
 */
	function isChecked(&$model, $field) {
		$value = array_values($field);
		$passed = (in_array($value[0], array('0', 0, false))) ? false : true;
		return $passed;
	}

/**
 * Returns whether a field's contents match the contents of every parameter in $model->data
 * 
 * @access public
 * @param object $model
 * @param array $field
 * @param array $params array of fields in $model->data to check
 * @return boolean
 * @author Jose Diaz-Gonzalez
 */
	function notEqualTo(&$model, $check, $params = array()) { 
		$value = array_pop(array_values($check));
		foreach ($params as $param) {
			if (isset($model->data[$model->alias][$param]) and ($value !== $model->data[$model->name][$param])) {
				return false;
			}
		}
		return true;
	}

/**
 * Makes this field required if some other field is marked as true
 * 
 * @param string $check field to check
 * @param string $params other fields that make this field required
 * @return boolean true if all fields have been correctly filled
 * @author Jose Diaz-Gonzalez
 * @access public
 */
	function requiredByFields($check, $params = array()) {
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
 * @return boolean true if at least one field has been filled, false otherwise
 * @author Jose Diaz-Gonzalez
 * @access public
 */
	function validateDependentFields(&$model, $check, $params = array()) {
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
 * 		- fields: array list of fields (default: array())
 * 		- hash: boolean if the fields should be hashed (default: false)
 * 		- hashOptions: options if hash is true:
 * 			- method: string cake's supported hash method (default: 'sha1')
 * 			- salt: boolean if cake's salt should be used (default: true)
 * 			- fields: array list of fields that should be hashed, the others are not hashed (default: array())
 * 		- skip: boolean, if you want to continue on fields that don't exist, set this to true (default: false)
 * @return boolean true if all fields did match, false otherwise
 * @author Thomas Ploch
 * @access public
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
		// Getting options
		extract(am($defaultParams, $params));
		$fieldKey = array_pop(array_keys($check));
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

/**
 * Checks if a string or a list of strings can be matched in a given text
 * 
 * @param object $model
 * @param array $check text in which the fields' data should be matched in
 * @param array $params:
 * 		- fields array list of fields which should be matched against the text (default: array())
 * 		- ordererd boolean if true, the order in which the fields are specified is important (default: false)
 * 		- caseSensitive boolean if true, the case of the strings is important (default: false)
 * @return boolean true if words/fields are contained within text, false if otherwise
 * @author Thomas Ploch
 * @access public
 */
	function isWordsInText(&$model, $check, $params = array()) {
		$valid = false;
		// Default params
		$defaultParams = array(
			'fields' => array(),
			'ordered' => false,
			'caseSensitive' => false
		);
		// Getting option variables
		extract(am($defaultParams, $params));
		$fieldKey = array_pop(array_keys($check));
		// apply $caseSensitive option to check data
		$checkData = strval($model->data[$model->alias][$fieldKey]);
		$caseSensitive or $checkData = low($checkData);
		// ordered mode
		if ($ordered) {
			// compile regular expression
			$expression = '/.*\\/u';
			foreach ($fields as $n => $field) {
				$data = strval($model->data[$model->alias][$field]);
				// apply caseSensitive option
				$caseSensitive or $data = low($data);
				if (!isset($fields[$n+1])) {
					$group = "({$data}" . ".+)";
				} else {
					$group = "({$data}" . ".+)" . "\\\\";
				}
				$expression = preg_replace('/\\\\/', $group, $expression);
			}
			// do validation
			$valid = (bool)preg_match($expression, $checkData);
			return $valid;
		}
		// unordered mode
		foreach ($fields as $field) {
			$data = strval($model->data[$model->alias][$field]);
			// apply caseSensitive option
			$caseSensitive or $data = low($data);
			// compile expression
			$expression = "/^.*({$data})" . '.*$/u';
			// do validation
			$valid = (bool)preg_match($expression, $checkData);
			// break on false
			if (!$valid) {
				return $valid;
			}
		}
		return $valid;
	}
}
?>