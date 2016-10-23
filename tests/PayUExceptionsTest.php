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