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
class RequestPaymentsUtilTest extends PHPUnit_Framework_TestCase
{
	
	
	/**
	 * test to build Transaction Request
	 */
	public function testBuildTransactionRequest(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
		
		$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
				array(
						PayUParameters::PAYMENT_METHOD=>'VISA',
						PayUParameters::CREDIT_CARD_NUMBER=>'4005580000029205',
						PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2080/01',
						PayUParameters::PROCESS_WITHOUT_CVV2=>true,
						PayUParameters::ACCOUNT_ID => 11,
						PayUParameters::COUNTRY => PayUCountries::MX,
						PayUParameters::CURRENCY => 'MXN',
						PayUParameters::VALUE => '100',
						PayUParameters::PAYER_NAME => 'nameTest',
						PayUParameters::PAYER_EMAIL => 'email@test.com',
						PayUParameters::PAYER_CNPJ => '123456789',
						PayUParameters::PAYER_CONTACT_PHONE => '987654321',
						PayUParameters::PAYER_DNI => '147258',
						PayUParameters::PAYER_BUSINESS_NAME => 'BusinessNameTest',
						PayUParameters::PAYER_BIRTHDATE => '08-06-22',
						PayUParameters::NOTIFY_URL => 'www.payu.com',
						PayUParameters::RESPONSE_URL => 'www.payu.com'
					));
		
		$request = RequestPaymentsUtil::buildPaymentRequest($parameters, TransactionType::AUTHORIZATION_AND_CAPTURE
				,SupportedLanguages::ES);
		$transaction = $request->transaction;
		$this->assertNotEmpty($transaction);
		
		$order = $transaction->order;
		$this->assertNotEmpty($order);
		$this->assertNotEmpty($order->notifyUrl);
		
		$extraParameters = $transaction ->extraParameters;
		$this->assertNotEmpty($extraParameters);
		
		$key = PayUKeyMapName::RESPONSE_URL;
		$responseURL = $extraParameters->$key;	
		$this->assertNotEmpty($responseURL);
	}
	
	
}
