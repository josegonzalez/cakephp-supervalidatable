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

	function inNYC(&$model, $field, $nyc_id = null) {
		$items = array(
			'Brooklyn' => array('11201', '11203', '11204', '11205', '11206', '11207', '11208', '11209', '11210', '11211', '11212', '11213', '11214', '11215', '11216', '11217', '11218', '11219', '11220', '11221', '11222', '11223', '11224', '11225', '11226', '11228', '11229', '11230', '11231', '11232', '11233', '11234', '11235', '11236', '11237', '11238', '11239'),
			'Manhattan' => array('10001', '10002', '10003', '10004', '10005', '10006', '10007', '10009', '10010', '10011', '10012', '10013', '10014', '10016', '10017', '10018', '10019', '10020', '10021', '10022', '10023', '10024', '10025', '10026', '10027', '10028', '10029', '10030', '10031', '10032', '10033', '10034', '10035', '10036', '10037', '10038', '10039', '10040', '10041', '10044', '10048', '10069', '10103', '10111', '10112', '10115', '10119', '10128', '10152', '10153', '10154', '10162', '10165', '10167', '10169', '10170', '10171', '10172', '10173', '10177', '10271', '10278', '10279', '10280', '10282'),
			'Queens' => array('11004', '11101', '11102', '11103', '11104', '11105', '11106', '11354', '11355', '11356', '11357', '11358', '11360', '11361', '11362', '11363', '11364', '11365', '11366', '11367', '11368', '11369', '11371', '11372', '11373', '11374', '11375', '11377', '11378', '11379', '11385', '11411', '11412', '11413', '11414', '11415', '11416', '11417', '11418', '11419', '11420', '11421', '11422', '11423', '11426', '11427', '11428', '11429', '11430', '11432', '11433', '11434', '11435', '11436', '11691', '11692', '11693', '11694', '11697', ),
			'Staten Island' => array('10301', '10302', '10303', '10304', '10305', '10306', '10307', '10308', '10309', '10310', '10312', '10314'),
			'The Bronx' => array('10451', '10452', '10453', '10454', '10455', '10456', '10457', '10458', '10459', '10460', '10461', '10462', '10463', '10464', '10465', '10466', '10467', '10468', '10469', '10470', '10471', '10472', '10473', '10474', '10475', '11370')
		);
		if (isset($nyc_id) and ($model->data[$model->alias]['state_id'] != $nyc_id)) {
			return false;
		}
		foreach ($items as $item) {
			if (in_array($field['zip_code'], $item)) {
				return true;
			}
		}
		return false;
	}

	function inState(&$model, $check, $state_id) {
		return (array_pop(array_values($check)) === $state_id);
	}

	public function stateValidation(&$model, &$field, $params = array()) {
		return true;
		$passed = true;
		if ($model->data[$model->alias]['country_id'] != $params['country_id']) {
			if (empty($model->data[$model->alias][$field])) {
				$passed = false;
			}
		}
		return $passed;
	}

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
}
?>