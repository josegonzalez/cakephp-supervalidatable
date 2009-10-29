<?php
/**
 * ValidateText Model Behavior
 * 
 * Packages up some validation methods for text comparison
 *
 * @package super_validatable.models.behaviors
 * @author Jose Diaz-Gonzalez
 * @author Thomas Ploch
 * @copyright 
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 **/
class ValidateTextBehavior extends ModelBehavior {
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