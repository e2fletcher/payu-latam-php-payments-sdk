<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../Payu.php';
require_once dirname(__FILE__).'/PayUTestUtil.php';


/**
 * Test cases to report request class
 * @author PayU Latam
 * @since 1.0.0
 * @version 1.0
 *
 */
class PayUReportsTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * test to ping request
	 */
    public function testDoPing(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);

    	 
    	$response = PayUReports::doPing();
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    }
    
    /**
     * test to ping request
	 * @expectedException PayUException 
     */
    
    
    public function testDoPingInvalid(){
    	PayU::$apiLogin = 'invalidLogin';
    	PayU::$apiKey = 'invalidKey';
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);
    
    	$response = PayUReports::doPing();
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    }
    
    
    /**
     * test to get order detail by id
     */
    public function testGetOrderDetail(){
    	
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);
    	
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION_AND_CAPTURE, '*');
    	
    	$parameters = array(
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
    	
    	$response = PayUReports::getOrderDetail($parameters);
    	
    	$this->assertNotNull($response);
    	$this->assertEquals($authorizationResponse->transactionResponse->orderId, $response->id);
    	 
    }
    
    
    /**
     * test to get an order with invalid id
	 * @expectedException PayUException 
     */
    public function testGetOrderDetailWithInvalidId(){
    	 
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	 
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);
    
    	$parameters = array(
    			PayUParameters::ORDER_ID => '123111123',
    	);
    	 
    	$response = PayUReports::getOrderDetail($parameters);
    }
    
    
    
    /**
     * test to get order detail by referenceCode
     */
    public function testGetOrderDetailByReferenceCode(){
    	 
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);    	
    	
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);
    	
    	$parameters = array(PayUParameters::REFERENCE_CODE => 'referenceCode-' . rand(10000,999999999));

    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, '*',$parameters);
    	
    	$parameters = array(
    			PayUParameters::REFERENCE_CODE => $parameters[PayUParameters::REFERENCE_CODE],
    	);
    	 
    	$response = PayUReports::getOrderDetailByReferenceCode($parameters);
    	 
    	$this->assertNotNull($response);
    	$this->assertEquals($parameters[PayUParameters::REFERENCE_CODE], $response[0]->referenceCode);
    
    }
    
    
    /**
     * test to get order detail by referenceCode
     * @expectedException PayUException
     */
    public function testGetOrderDetailByReferenceCodeInvalid(){
    
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);    	
    	 
		
    	$parameters = array(PayUParameters::REFERENCE_CODE => 'InvalidReference');
    
    	$response = PayUReports::getOrderDetailByReferenceCode($parameters);
    
    }
    
    
    
    /**
     * test to get order detail by transaction id
     */
    public function testGetTransactionResponse(){
    	
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, '*');
    	
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    	);
    	
    	$response = PayUReports::getTransactionResponse($parameters);
    	
    	$this->assertNotNull($response);

    }
    
    
    /**
     * test to get order detail by transaction id invalid
     * @expectedException InvalidArgumentException
     */
    public function testGetOrderDetailByTransactionIdInvalid(){
    
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);    
    
    	$parameters = array(PayUParameters::TRANSACTION_ID => 'InvalidTransactionId');
    
    	$response = PayUReports::getOrderDetailByReferenceCode($parameters);
    
    }
    
    /**
     * test to get order detail by transaction id valid
     * 
     */
    
    public function testGetOrderDetailByTransactionIdValid(){
    
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);

    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION_AND_CAPTURE, '*');
    	 
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    	);
    	 
    	$response = PayUReports::getTransactionResponse($parameters);
    }    
    
    /**
     * test get order details from invalid order id
     */
    public function testGetOrderDetailInvalidId(){
    	
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$language = SupportedLanguages::EN;
    	Environment::setReportsCustomUrl(PayUTestUtil::REPORTS_CUSTOM_URL);    	
    	
    	$parameters = array(
    			PayUParameters::ORDER_ID => '1',
    	);
    	
    	$this->setExpectedException('PayUException');
    	$response = PayUReports::getOrderDetail($parameters);
    	 
    }
    	
    
    
    
}
