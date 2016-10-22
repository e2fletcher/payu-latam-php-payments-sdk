<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../Payu.php';
require_once __DIR__.'./PayUTestUtil.php';


/**
 * Test cases for HttpClientUtil class
 * @author PayU Latam
 * @since 1.0.0
 * @version 1.0
 *
 */
class PayUExceptionsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * test throws Payuexception to try to delete a customer with wrong id
	 * @expectedException PayUException
	 */
	public function testPayUException(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters = array(PayUParameters::CUSTOMER_ID => 'aaaaaaaaa');
		
		$response = PayUCustomers::delete($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->description);
	}
	
	/**
	 * test throws connection exception
	 * @expectedException ConnectionException
	 */
	public function testConnectionException(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl('http://fake.payupayupayu.com');
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$subscription = PayUSubscriptions::createSubscription($parameters);
	}
	
	
	/**
	 * test throws connection exception
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidArgumentException(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = array();
		$subscription = PayUSubscriptions::createSubscription($parameters);
	}
	
	
	/**
	 * test a invalid request
	 */
	public function testInvalidRequest(){
		$this->setExpectedException("PayUException");
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl("http://google.com");
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
	}
	
	

}