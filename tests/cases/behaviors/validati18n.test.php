<?php
class Validati18nTest1 extends CakeTestModel {
	var $name = 'Validati18nTest1';
	var $useTable = false;
	var $_schema = array();
	var $validate = array(
        	'ssnr' => array(
        			'rule' => array('lc_ssn'),
        			'message' => 'This ssn is not valid.'),
			'postal_code' => array(
        			'rule' => array('lc_postal'),
        			'message' => 'This postal code is not valid.'),
			'phone_number' => array(
        			'rule' => array('lc_phone'),
        			'message' => 'This phone number is not valid.'),
	);

}

class Validati18nTest2 extends CakeTestModel {
	var $name = 'Validati18nTest2';
	var $useTable = false;
	var $_schema = array();
	var $validate = array(
        	'ssnr' => array(
        			'rule' => array('lc_ssn', 'nl'),
        			'message' => 'This ssn is not valid.'),
			'postal_code' => array(
        			'rule' => array('lc_postal', 'nl'),
        			'message' => 'This postal code is not valid.'),
			'phone_number' => array(
        			'rule' => array('lc_phone', 'nl'),
        			'message' => 'This phone number is not valid.'),
	);
}

class Validati18nBehaviorTest extends CakeTestCase {
	
	function testDefaultSettings(){
		$Model = new Validati18nTest1;
		$Model->Behaviors->attach('Validati18n.Validati18n');
		$data = array('Validati18nTest1' => array('ssnr'=>'987-65-4320'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('ssnr'=>'987-65-432'));
		$Model->set($data);
		$this->assertFalse($Model->validates());
	}
	
	function testCountrySettings() {
		$Model = new Validati18nTest1;
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'nl'));
		$data = array('Validati18nTest1' => array('ssnr'=>'187821321'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('ssnr'=>'187821'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
	}

	function testCountryParameter() {
		$Model = new Validati18nTest2;
		$Model->Behaviors->attach('Validati18n.Validati18n');
		$data = array('Validati18nTest2' => array('ssnr'=>'187821321'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest2' => array('ssnr'=>'187821'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
	}

	function testCountryParameterOverride() {
		$Model = new Validati18nTest2;
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'us'));
		$data = array('Validati18nTest2' => array('ssnr'=>'187821321'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest2' => array('ssnr'=>'187821'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
	}

	function testLcPhone() {
		$Model = new Validati18nTest1;
		
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'nl'));
		$data = array('Validati18nTest1' => array('phone_number'=>'020-5045100'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('phone_number'=>'020-50451009'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');
		
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'it'));
		$data = array('Validati18nTest1' => array('phone_number'=>'347/1233456'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('phone_number'=>'02+343536'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'fr'));
		$data = array('Validati18nTest1' => array('phone_number'=>'04 76 96 12 32'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('phone_number'=>'04 76 96 12 3'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'es'));
		$data = array('Validati18nTest1' => array('phone_number'=>'924227227'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('phone_number'=>'813 4567'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'us'));
		$data = array('Validati18nTest1' => array('phone_number'=>'+1 702 425 5085'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('phone_number'=>'7002 425 5085'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');
	}

	function testLcPostal() {
		$Model = new Validati18nTest1;
		
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'jp'));
		$data = array('Validati18nTest1' => array('postal_code'=>'020-5045'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'0205-504'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');
		
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'nl'));
		$data = array('Validati18nTest1' => array('postal_code'=>'2500 GK'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'0110 AS'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'cs'));
		$data = array('Validati18nTest1' => array('postal_code'=>'123 45'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'95616'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'sk'));
		$data = array('Validati18nTest1' => array('postal_code'=>'95616'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'0989'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'uk'));
		$data = array('Validati18nTest1' => array('postal_code'=>'DT4 8PP'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'DT4-8PP'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'ca'));
		$data = array('Validati18nTest1' => array('postal_code'=>'L4W 1S2'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'LI3 SUC'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');
		
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'it'));
		$data = array('Validati18nTest1' => array('postal_code'=>'10096'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'1046'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'de'));
		$data = array('Validati18nTest1' => array('postal_code'=>'51109'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'051109'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'be'));
		$data = array('Validati18nTest1' => array('postal_code'=>'1804'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'01804'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'au'));
		$data = array('Validati18nTest1' => array('postal_code'=>'2300'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'02300'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'us'));
		$data = array('Validati18nTest1' => array('postal_code'=>'89104'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('postal_code'=>'NV 89104'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');
}

	function testLcSsn() {
		$Model = new Validati18nTest1;
		
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'dk'));
		$data = array('Validati18nTest1' => array('ssnr'=>'111111-3334'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('ssnr'=>'111111-333'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');
		
		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'nl'));
		$data = array('Validati18nTest1' => array('ssnr'=>'123456789'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('ssnr'=>'1234567896'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');

		$Model->Behaviors->attach('Validati18n.Validati18n', array('country'=>'us'));
		$data = array('Validati18nTest1' => array('ssnr'=>'111-33-4333'));
		$Model->set($data);
		$this->assertTrue($Model->validates());

		$data = array('Validati18nTest1' => array('ssnr'=>'111-33-333'));
		$Model->set($data);
		$this->assertFalse($Model->validates());	
		$Model->Behaviors->detach('Validati18n.Validati18n');		
	}

	function endTest() {
		Classregistry::flush();
	}
}
?>