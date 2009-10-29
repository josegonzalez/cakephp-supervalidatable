<?php
/**
 * ValidateTime Model Behavior
 * 
 * Packages up some validation methods for time comparison
 *
 * @package super_validatable.models.behaviors
 * @author Jose Diaz-Gonzalez
 * @author Thomas Ploch
 * @copyright 
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 **/
class ValidateTimeBehavior extends ModelBehavior {
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
	function setup(&$model, $config = array()) {}
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
}
?>