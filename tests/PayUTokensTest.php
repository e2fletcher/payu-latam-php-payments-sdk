<?php
require_once dirname(__FILE__). '/../vendor/autoload.php';
require_once dirname(__FILE__). '/PayUTestUtil.php';

use PayuSDK\PayU;
use PayuSDK\PayUBankAccounts;
use PayuSDK\PayUCreditCards;
use PayuSDK\PayUCustomers;
use PayuSDK\PayUPayments;
use PayuSDK\PayURecurringBill;
use PayuSDK\PayURecurringBillItem;
use PayuSDK\PayUReports;
use PayuSDK\PayUSubscriptionPlans;
use PayuSDK\PayUSubscriptions;
use PayuSDK\PayUTokens;

use PayuSDK\Api\Environment;
use PayuSDK\Api\PaymentMethods;
use PayuSDK\Api\PayUCommands;
use PayuSDK\Api\PayUConfig;
use PayuSDK\Api\PayUCountries;
use PayuSDK\Api\PayUHttpRequestInfo;
use PayuSDK\Api\PayUKeyMapName;
use PayuSDK\Api\PayUPaymentMethodType;
use PayuSDK\Api\PayUResponseCode;
use PayuSDK\Api\PayUTransactionResponseCode;
use PayuSDK\Api\RequestMethod;
use PayuSDK\Api\SupportedLanguages;
use PayuSDK\Api\TransactionType;

use PayuSDK\Exceptions\ConnectionException;
use PayuSDK\Exceptions\PayUErrorCodes;
use PayuSDK\Exceptions\PayUException;

use PayuSDK\Util\CommonRequestUtil;
use PayuSDK\Util\HttpClientUtil;
use PayuSDK\Util\PayUApiServiceUtil;
use PayuSDK\Util\PayUParameters;
use PayuSDK\Util\PayUReportsRequestUtil;
use PayuSDK\Util\PayURequestObjectUtil;
use PayuSDK\Util\PayUSubscriptionsRequestUtil;
use PayuSDK\Util\PayUSubscriptionsUrlResolver;
use PayuSDK\Util\PayUTokensRequestUtil;
use PayuSDK\Util\RequestPaymentsUtil;
use PayuSDK\Util\SignatureUtil;
use PayuSDK\Util\UrlResolver;

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
