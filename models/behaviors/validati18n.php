<?php
/**
 * Behavior for country based validation
 *
 * @copyright	2009 Marc Ypes, The Netherlands
 * @author		Ceeram
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 */ 
class Validati18nBehavior extends ModelBehavior {
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
		switch ($country) {
			case 'nl':
				$regex = '/^0(6[\s-]?[1-9]\d{7}|[1-9]\d[\s-]?[1-9]\d{6}|[1-9]\d{2}[\s-]?[1-9]\d{5})$/';
				break;
			case 'it':
				$regex = '/^([0-9]*\-?\ ?\/?[0-9]*)$/';
				break; 
			case 'fr':
				$regex = '/^0[1-6]{1}(([0-9]{2}){4})|((\s[0-9]{2}){4})|((-[0-9]{2}){4})$/'; 
				break; 
			case 'es':
				$regex = '/^\\+?(34[-. ]?)?\\(?(([689]{1})(([0-9]{2})\\)?[-. ]?|([0-9]{1})\\)?[-. ]?([0-9]{1}))|70\\)?[-. ]?([0-9]{1}))([0-9]{2})[-. ]?([0-9]{1})[-. ]?([0-9]{1})[-. ]?([0-9]{2})$/';
				break;
			case 'us':
			default:
				$regex  = '/^(?:\+?1)?[-. ]?\\(?[2-9][0-8][0-9]\\)?[-. ]?[2-9][0-9]{2}[-. ]?[0-9]{4}$/';
			break;
		}
		return preg_match($regex, $check);
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
		switch ($country) {
			case 'jp': 
				$regex   = '/^[0-9]{3}-[0-9]{4}$/'; 
				break;
			case 'nl': 
				$regex   = '/^[1-9][0-9]{3}\s?[A-Z]{2}$/i'; 
				break;
			case 'cs': 
				$regex  = '/^[1-7]\d{2} ?\d{2}$/i'; 
				break; 
			case 'sk': 
				$regex  = '/^[0,8,9]\d{2} ?\d{2}$/i'; 
				break;
			case 'uk':
				$regex  = '/\\A\\b[A-Z]{1,2}[0-9][A-Z0-9]? [0-9][ABD-HJLNP-UW-Z]{2}\\b\\z/i';
				break;
			case 'ca':
				$regex  = '/\\A\\b[ABCEGHJKLMNPRSTVXY][0-9][A-Z] [0-9][A-Z][0-9]\\b\\z/i';
				break;
			case 'it':
			case 'de':
				$regex  = '/^[0-9]{5}$/i';
				break;
			case 'be':
				$regex  = '/^[1-9]{1}[0-9]{3}$/i';
				break;
			case 'au':
				$regex  = '/^[0-9]{4}$/i';
				break;
			case 'us':
			default:
				$regex  = '/\\A\\b[0-9]{5}(?:-[0-9]{4})?\\b\\z/i';
				break;
		}
		return preg_match($regex, $check);
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
		switch ($country) {
			case 'dk':
				$regex  = '/\\A\\b[0-9]{6}-[0-9]{4}\\b\\z/i';
				break;
			case 'nl':
				$regex  = '/\\A\\b[0-9]{9}\\b\\z/i';
				break;
			case 'us':
			default:
				$regex  = '/\\A\\b[0-9]{3}-[0-9]{2}-[0-9]{4}\\b\\z/i';
				break;
		}
		return preg_match($regex, $check);
	}
}
?>