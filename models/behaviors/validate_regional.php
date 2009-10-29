<?php
/**
 * Behavior for country based validation
 *
 * @copyright 2009 Marc Ypes, The Netherlands
 * @author Ceeram
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */ 
class ValidateReionalBehavior extends ModelBehavior {
/**
* Behavior settings
* 
* @access public
* @var array
*/
	public $settings = array(); 
/**
* Default setting values
*
* @access private
* @var array
*/ 	
	private $defaults = array('country'=>'us');
/**
* Country based regexes
*
* @access private
* @var array
*/
	private $regex = array(
		'au' => array(
			'phone' => null,
			'postal' => '/^[0-9]{4}$/i',
			'ssn' => null),
		'be' => array(
			'phone' => null,
			'postal' => '/^[1-9]{1}[0-9]{3}$/i',
			'ssn' => null),
		'ca' => array(
			'phone' => null,
			'postal' => '/\\A\\b[ABCEGHJKLMNPRSTVXY][0-9][A-Z] [0-9][A-Z][0-9]\\b\\z/i',
			'ssn' => null),
		'cs' => array(
			'phone' => null,
			'postal' => '/^[1-7]\d{2} ?\d{2}$/i',
			'ssn' => null),
		'dk' => array(
			'phone' => null,
			'postal' => null,
			'ssn' => '/\\A\\b[0-9]{6}-[0-9]{4}\\b\\z/i'),
		'de' => array(
			'phone' => null,
			'postal' => '/^[0-9]{5}$/i',
			'ssn' => null),
		'es' => array(
			'phone' => '/^\\+?(34[-. ]?)?\\(?(([689]{1})(([0-9]{2})\\)?[-. ]?|([0-9]{1})\\)?[-. ]?([0-9]{1}))|70\\)?[-. ]?([0-9]{1}))([0-9]{2})[-. ]?([0-9]{1})[-. ]?([0-9]{1})[-. ]?([0-9]{2})$/',
			'postal' => null,
			'ssn' => null),
		'fr' => array(
			'phone' => '/^0[1-6]{1}(([0-9]{2}){4})|((\s[0-9]{2}){4})|((-[0-9]{2}){4})$/',
			'postal' => null,
			'ssn' => null),
		'it' => array(
			'phone' => '/^([0-9]*\-?\ ?\/?[0-9]*)$/',
			'postal' => '/^[0-9]{5}$/i',
			'ssn' => null),
		'jp' => array(
			'phone' => null,
			'postal' => '/^[0-9]{3}-[0-9]{4}$/',
			'ssn' => null),
		'nl' => array(
			'phone' => '/^0(6[\s-]?[1-9]\d{7}|[1-9]\d[\s-]?[1-9]\d{6}|[1-9]\d{2}[\s-]?[1-9]\d{5})$/',
			'postal' => '/^[1-9][0-9]{3}\s?[A-Z]{2}$/i',
			'ssn' => '/\\A\\b[0-9]{9}\\b\\z/i'),
		'sk' => array(
			'phone' => null,
			'postal' => '/^[0,8,9]\d{2} ?\d{2}$/i',
			'ssn' => null),
		'uk' => array(
			'phone' => null,
			'postal' => '/\\A\\b[A-Z]{1,2}[0-9][A-Z0-9]? [0-9][ABD-HJLNP-UW-Z]{2}\\b\\z/i',
			'ssn' => null),
		'us' => array(
			'phone' => '/^(?:\+?1)?[-. ]?\\(?[2-9][0-8][0-9]\\)?[-. ]?[2-9][0-9]{2}[-. ]?[0-9]{4}$/',
			'postal' => '/\\A\\b[0-9]{5}(?:-[0-9]{4})?\\b\\z/i',
			'ssn' => '/\\A\\b[0-9]{3}-[0-9]{2}-[0-9]{4}\\b\\z/i')
		);
/**
 * @param object $Model Model using the behavior
 * @param array $settings Settings to override for model.
 * @access public
 * @return void
 */
	function setup(&$Model, $config = null) {
		if (is_array($config)) {
			$this->settings[$Model->alias] = array_merge($this->defaults, $config);            
		} else {
			$this->settings[$Model->alias] = $this->defaults;
		}
	}
/**
 * Validation rule for phonenumbers
 * 
 * @param object $Model Model using the behavior
 * @param array $check
 * @param array $country Override the country from default or settings
 * @access public
 * @return boolean
 */
	function lc_phone(&$Model, $check, $country = null) {
		$check = array_values($check);
		$check = $check[0];
		if(!is_string($country)){
			$country = $this->settings[$Model->alias]['country'];
		}
		if($this->regex[$country]['phone']) {
			return preg_match($this->regex[$country]['phone'], $check);
		}
		return false;
	}
/**
 * Validation rule for zip codes
 * 
 * @param object $Model Model using the behavior
 * @param array $check
 * @param array $country Override the country from default or settings
 * @access public
 * @return boolean
 */
	function lc_postal(&$Model, $check, $country = null) {
		$check = array_values($check);
		$check = $check[0];
		if(!is_string($country)){
			$country = $this->settings[$Model->alias]['country'];
		}
		if($this->regex[$country]['postal']) {
			return preg_match($this->regex[$country]['postal'], $check);
		}
		return false;
	}
/**
 * Validation rule for social security numbers
 * 
 * @param object $Model Model using the behavior
 * @param array $check
 * @param array $country Override the country from default or settings
 * @access public
 * @return boolean
 */
	function lc_ssn(&$Model, $check, $country = null) {
		$check = array_values($check);
		$check = $check[0];
		if(!is_string($country)){
			$country = $this->settings[$Model->alias]['country'];
		}
		if($this->regex[$country]['ssn']) {
			return preg_match($this->regex[$country]['ssn'], $check);
		}
		return false;
	}
}
?>