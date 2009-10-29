<?php
class SuperValidatableFixture extends CakeTestFixture {
	var $name = 'Validatable';
	
	var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'password' => array('type' => 'string', 'length' => 40, 'null' => false),
        'pw_confirm' => array('type' => 'string', 'length' => 40, 'null' => false),
        'another_field' =>array('type' => 'string', 'length' => 40, 'null' => false),
		'date1' => array('type' => 'date', 'null' => false),
		'date2' => array('type' => 'date', 'null' => false),
		'date3' => array('type' => 'date', 'null' => false)
	);
}
?>