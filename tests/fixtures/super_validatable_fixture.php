<?php
class SuperValidatableFixture extends CakeTestFixture {
	var $name = 'Validatable';
	
	var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'password' => array('type' => 'string', 'length' => 40, 'null' => false),
        'pw_confirm' => array('type' => 'string', 'length' => 40, 'null' => false),
        'another_field' =>array('type' => 'string', 'length' => 40, 'null' => false),
	);
}
?>