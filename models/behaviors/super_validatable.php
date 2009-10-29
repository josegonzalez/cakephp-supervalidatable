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
 * @author Ceeram
 * @copyright 
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 **/
class SuperValidatableBehavior extends ModelBehavior {
/**
* Behavior settings
* 
* @access public
* @var array
*/
	var $settings = array(
		'modulePrefix' => 'validate_'
	); 
/**
 * @param object $Model Model using the behavior
 * @param array $settings Settings to override for model.
 * @access public
 * @return void
 */
	function setup(&$Model, $config) {
		try {
			foreach ($config as $module => $moduleconfig) {
				$behavior = Inflector::camelize($this->settings['modulePrefix'] . $module);
				$Model->Behaviors->attach($behavior, $moduleconfig);
			}
		} catch (Exception $e) {
			// raise an Exception
		}
	}
}
?>