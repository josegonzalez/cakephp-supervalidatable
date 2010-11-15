<?php
/**
 * ValidateDefault Model Behavior
 * 
 * packages some default validation rules for comparing different fields
 *
 * @copyright 2009 Marc Ypes, The Netherlands
 * @author Thomas Ploch
 * @author Ceeram
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */ 
class ValidateDefaultBehavior extends ModelBehavior {
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
	function setup(&$model, $config = array()) {}
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
		if (!isset($params['fields']) && !empty($params)) {
			$params = array('fields' => $params);
		}
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
}
?>