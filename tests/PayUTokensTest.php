<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../Payu.php';
require_once dirname(__FILE__).'/PayUTestUtil.php';


/**
 * Test cases to token request class
 * @author PayU Latam
 * @since 1.0.0
 * @version 1.0
 *
 */
class PayUTokensTest extends PHPUnit_Framework_TestCase
{
	
	
	/**
	 * test to create a token
	 */
	public function testCreateToken(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
		
		$parameters = PayUTestUtil::buildParametersCreateToken();
		
		$response = PayUTokens::create($parameters);
		
		$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
		$this->assertNotNull($response->creditCardToken);
		$this->assertNotNull($response->creditCardToken->creditCardTokenId);
				
	}
	
	/**
	 * test get token
	 */
	public function testGetTokenWithTokenId(){
		
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
		
		$responseCreditCardToken = PayUTestUtil::createToken();
		
		$parametersBasicTokenRequest = PayUTestUtil::buildBasicParametersToken();

		
		$parameters = array_merge($parametersBasicTokenRequest, array(PayUParameters::TOKEN_ID=>$responseCreditCardToken->creditCardToken->creditCardTokenId));
		
		$response = PayUTokens::find($parameters);

		$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
		$this->assertNotNull($response->creditCardTokenList);
		$this->assertGreaterThan(0, count($response->creditCardTokenList));
		$this->assertEquals($responseCreditCardToken->creditCardToken->creditCardTokenId, $response->creditCardTokenList[0]->creditCardTokenId);
		
	}
	
	/**
	 * test get with tokenId Invalid
	 * @expectedException PayUException
	 */
	public function testGetTokenWithTokenIdInvalid(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
	
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$responseCreditCardToken = PayUTestUtil::createToken();
	
		$parametersBasicTokenRequest = PayUTestUtil::buildBasicParametersToken();
	
		$parameters = array_merge($parametersBasicTokenRequest, array(PayUParameters::TOKEN_ID=>"1231312132-1231321321-12312132-12312"));
	
		$response = PayUTokens::find($parameters);
	
	}
	
	/**
	 * test get with incomplete parameteres
	 * @expectedException InvalidArgumentException
	 */
	public function testGetTokenWithOutTokenId(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
	
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$responseCreditCardToken = PayUTestUtil::createToken();
	
		$parameters = array(PayUParameters::TOKEN_ID=>'');
			
		$response = PayUTokens::find($parameters);
	
	}
	
	
	/**
	 * test get token filtered by start date and end date
	 */
	public function testGetTokenWithDates(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
	
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$responseCreditCardToken = PayUTestUtil::createToken();
	
		$parametersBasicTokenRequest = PayUTestUtil::buildBasicParametersToken();
	

		$startDate = PayUTestUtil::getLastWeekDate();
		$endDate = PayUTestUtil::getNextWeekDate();
		$parametersFilter = array(PayUParameters::START_DATE=>$startDate, PayUParameters::END_DATE=>$endDate);
		
		$parameters = array_merge($parametersBasicTokenRequest, $parametersFilter);
	
		$response = PayUTokens::find($parameters);
	
		$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
		$this->assertNotNull($response->creditCardTokenList);
		$this->assertGreaterThan(0, count($response->creditCardTokenList));
	}
	
	
	/**
	 * test remove token
	 */
	public function testRemoveToken(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
	
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$responseCreditCardToken = PayUTestUtil::createToken();
	
		$parametersBasicTokenRequest = PayUTestUtil::buildBasicParametersToken();
	
	
		$parameters = array_merge($parametersBasicTokenRequest,array(PayUParameters::TOKEN_ID => $responseCreditCardToken->creditCardToken->creditCardTokenId));
	
		$response = PayUTokens::remove($parameters);
	
		$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
		$this->assertNotNull($response->creditCardToken);
		$this->assertNotNull($response->creditCardToken->creditCardTokenId);
	
	}
	
	/**
	 * test remove token with token remove
	 * @expectedException PayUException
	 */
	public function testRemoveTokenWithTokenRemove(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
	
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$responseCreditCardToken = PayUTestUtil::createToken();
	
		$parametersBasicTokenRequest = PayUTestUtil::buildBasicParametersToken();
	
	
		$parameters = array_merge($parametersBasicTokenRequest,array(PayUParameters::TOKEN_ID => $responseCreditCardToken->creditCardToken->creditCardTokenId));
	
		$response = PayUTokens::remove($parameters);
		
		$response = PayUTokens::remove($parameters);
	
	}
	
	/**
	 * test remove token whit different PayerId
	 * @expectedException PayUException
	 */
	public function testRemoveTokenWithDifferentPayerId(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
	
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$responseCreditCardToken = PayUTestUtil::createToken();
	
		$parametersBasicTokenRequest = PayUTestUtil::buildBasicParametersToken();
		
		$parametersBasicTokenRequest[PayUParameters::PAYER_ID]= "Payer_id_555";
	
		$parameters = array_merge($parametersBasicTokenRequest,array(PayUParameters::TOKEN_ID => $responseCreditCardToken->creditCardToken->creditCardTokenId));
	
		$response = PayUTokens::remove($parameters);
	
	}
	
	/**
	 * test remove token whit PayerId Empty
	 * @expectedException InvalidArgumentException
	 */
	public function testRemoveTokenWithPayerIdEmpty(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
	
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$responseCreditCardToken = PayUTestUtil::createToken();
	
		$parametersBasicTokenRequest = PayUTestUtil::buildBasicParametersToken();
	
		$parametersBasicTokenRequest[PayUParameters::PAYER_ID]= "";
	
		$parameters = array_merge($parametersBasicTokenRequest,array(PayUParameters::TOKEN_ID => $responseCreditCardToken->creditCardToken->creditCardTokenId));
	
		$response = PayUTokens::remove($parameters);
	
	}
	
}
