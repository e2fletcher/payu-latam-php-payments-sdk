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
 * Utility class for test
 *
 * @author PayU Latam
 * @since 1.0.0
 * @version 1.0.0, 17/10/2013
 *
 */
class PayUTestUtil{

	const API_LOGIN = '012345678901';

	const API_KEY = '012345678901';

	const MERCHANT_ID = '1';


	const PAYMENTS_CUSTOM_URL = 'https://qa.api.payulatam.com/payments-api/4.0/service.cgi';
	//const PAYMENTS_CUSTOM_URL = 'https://stg.api.payulatam.com/payments-api/4.0/service.cgi';
	//const PAYMENTS_CUSTOM_URL = 'http://localhost:8080/ppp-web-payments-api/4.0/service.cgi';

	const REPORTS_CUSTOM_URL = 'https://qa.api.payulatam.com/reports-api/4.0/service.cgi';
	//const REPORTS_CUSTOM_URL = 'https://stg.api.payulatam.com/reports-api/4.0/service.cgi';
	//const REPORTS_CUSTOM_URL = 'http://localhost:8080/ppp-web-reports-api/4.0/service.cgi';

	//const SUBSCRIPTION_CUSTOM_URL = 'https://qa.api.payulatam.com/payments-api/rest/v4.3';
	const SUBSCRIPTION_CUSTOM_URL = 'https://stg.api.payulatam.com/payments-api/rest/v4.3';
	//const SUBSCRIPTION_CUSTOM_URL = 'http://localhost:8080/ppp-web-payments-api/rest/v4.3';

	const PAYMENT_PLAN_CUSTOM_URL = 'https://qa.api.payulatam.com/payments-api/rest/v4.3';
	//const PAYMENT_PLAN_CUSTOM_URL = 'https://stg.api.payulatam.com/payments-api/rest/v4.3';
	//const PAYMENT_PLAN_CUSTOM_URL = 'http://localhost:8080/ppp-web-payments-api/rest/v4.3';


	/**
	 * Do a Authorization or Authorization and capture for testing
	 * @param string $transactionType
	 * @param boolean $requiredTransactionState, the required transaction state * for any
	 * @param string $overrideParameters
	 * @throws RuntimeException
	 * @throws Exception
	 * @return Object withe the response of api request
	 */
	public static function processTransaction($transactionType, $requiredTransactionState, $overrideParameters = null){
		try{
			PayU::$apiLogin = PayUTestUtil::API_LOGIN;
			PayU::$apiKey = PayUTestUtil::API_KEY;
			PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
			Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);

			$parameters = PayUTestUtil::buildSuccessParametersCreditCard($overrideParameters);

			if(TransactionType::AUTHORIZATION  == $transactionType){
				$response = PayUPayments::doAuthorization($parameters);
			}else if(TransactionType::AUTHORIZATION_AND_CAPTURE == $transactionType){
				$response = PayUPayments::doAuthorizationAndCapture($parameters);
			}else{
				throw new RuntimeException(sprintf("transaction type %s not supported",$transactionType));
			}

			if($response->code != PayUResponseCode::SUCCESS){
				throw new Exception(sprintf('Request code not was %s was  [%s] ', PayUResponseCode::SUCCESS, $response->code));
			}

			if($requiredTransactionState != '*' && $response->transactionResponse->state != $requiredTransactionState){
				throw new Exception(sprintf('Transaction state not was [%s] ',$requiredTransactionState));
			}

			return $response;

		}catch (Exception $e){
			$message = $e->getMessage();
			throw new Exception('Error processing authorization orignal message [' . $message . ']',null,$e);
		}
	}

	public static function processTransactionBrasil($transactionType, $requiredTransactionState, $overrideParameters = null){
		try{
			PayU::$apiLogin = PayUTestUtil::API_LOGIN;
			PayU::$apiKey = PayUTestUtil::API_KEY;
			PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
			Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);

			$parameters = PayUTestUtil::buildSuccessParametersCreditCardBrasil($overrideParameters);

			if(TransactionType::AUTHORIZATION  == $transactionType){
				$response = PayUPayments::doAuthorization($parameters);
			}else if(TransactionType::AUTHORIZATION_AND_CAPTURE == $transactionType){
				$response = PayUPayments::doAuthorizationAndCapture($parameters);
			}else{
				throw new RuntimeException(sprintf("transaction type %s not supported",$transactionType));
			}

			if($response->code != PayUResponseCode::SUCCESS){
				throw new Exception(sprintf('Request code not was %s was  [%s] ', PayUResponseCode::SUCCESS, $response->code));
			}

			if($requiredTransactionState != '*' && $response->transactionResponse->state != $requiredTransactionState){
				throw new Exception(sprintf('Transaction state not was [%s] ',$requiredTransactionState));
			}

			return $response;

		}catch (Exception $e){
			$message = $e->getMessage();
			throw new Exception('Error processing authorization orignal message [' . $message . ']',null,$e);
		}
	}

	/**
	 * Build basic parameters for any payment request
	 */
	public static function buildBasicParameters(){

		$parameters = array(PayUParameters::REFERENCE_CODE => 'referenceCode-' . rand(10000,999999999),
				PayUParameters::PAYER_NAME=> 'PayerName-' . rand(10000,9999999) . ' PayerSurname-' .rand(10000,9999999),
				PayUParameters::COUNTRY => PayUCountries::PA,
				PayUParameters::ACCOUNT_ID => "8",
				PayUParameters::CURRENCY => "USD",
				PayUParameters::DESCRIPTION => 'description-' . rand(10000,9999999),
				PayUParameters::VALUE => rand(1000,1500) . '.'.rand(10,99),
				PayUParameters::INSTALLMENTS_NUMBER => '1',
				);


		return $parameters;
	}

	/**
	 * Build basic parameters for any payment request
	 */
	public static function buildBasicParametersBrasil(){

		$parameters = array(PayUParameters::REFERENCE_CODE => 'referenceCode-' . rand(10000,999999999),
				PayUParameters::PAYER_NAME=> 'PayerName-' . rand(10000,9999999) . ' PayerSurname-' .rand(10000,9999999),
				PayUParameters::COUNTRY => PayUCountries::BR,
				PayUParameters::ACCOUNT_ID => "3",
				PayUParameters::CURRENCY => "BRL",
				PayUParameters::DESCRIPTION => 'description-' . rand(10000,9999999),
				PayUParameters::VALUE => 200,
				PayUParameters::INSTALLMENTS_NUMBER => '2',
				PayUParameters::PAYER_DNI => '19649722645',
				PayUParameters::PAYER_POSTAL_CODE => '13500000'
		);

		return $parameters;
	}

	/**
	 * Build basic parameters for any payment request
	 */
	public static function buildBasicParametersToken(){

		$parameters = array(PayUParameters::PAYER_NAME => 'PayerName-' . rand(10000,9999999) . ' PayerSurname-' .rand(10000,9999999),
							PayUParameters::PAYER_ID => 'payerId_123'
		);

		return $parameters;
	}

	/**
	 * Builds the parameters
	 * @return array with the parameters to create a credit card token
	 */
	public static function buildParametersCreateToken(){
		$parametersBasicTokenRequest = PayUTestUtil::buildBasicParametersToken();
		$parametersCreditCard = PayUTestUtil::buildSuccessParametersCreditCard();

		return array_merge($parametersBasicTokenRequest,$parametersCreditCard);
	}

	/**
	 * Create a credit card token
	 * @return the credit card token created
	 * @throws Exception if the response code isn't success
	 */
	public static function createToken(){
		$response = PayUTokens::create(PayUTestUtil::buildParametersCreateToken());

		if($response->code != PayUResponseCode::SUCCESS){
			throw new Exception(sprintf('Request code not was %s was  [%s] to create a credit card token', PayUResponseCode::SUCCESS, $response->code));
		}

		return $response;

	}


	/**
	 * Returns a map of parameters to generate a success transaction with credit card
	 * @param array $overrideParameters
	 * @return a array with the parameters built
	 */
	public static function buildSuccessParametersCreditCard($overrideParameters = null){

		$parametersBasic = PayUTestUtil::buildBasicParameters();

		$parametersCreditCard = array(
				PayUParameters::CREDIT_CARD_NUMBER => '4024007137771894',
				PayUParameters::CREDIT_CARD_SECURITY_CODE => '495',
				PayUParameters::CREDIT_CARD_EXPIRATION_DATE => '2016/01',
				PayUParameters::PAYMENT_METHOD => PaymentMethods::VISA,
		);

		$parameters = array_merge($parametersBasic,$parametersCreditCard);

		if(isset($overrideParameters)){
			$parameters = array_replace($parameters,$overrideParameters);
		}

		return $parameters;
	}

	public static function buildSuccessParametersCreditCardBrasil($overrideParameters = null){

		$parametersBasic = PayUTestUtil::buildBasicParametersBrasil();

		$parametersCreditCard = array(
				PayUParameters::CREDIT_CARD_NUMBER => '5500678441838361',
				PayUParameters::CREDIT_CARD_SECURITY_CODE => '495',
				PayUParameters::CREDIT_CARD_EXPIRATION_DATE => '2016/01',
				PayUParameters::PAYMENT_METHOD => PaymentMethods::MASTERCARD,
		);

		$parameters = array_merge($parametersBasic,$parametersCreditCard);

		if(isset($overrideParameters)){
			$parameters = array_replace($parameters,$overrideParameters);
		}

		return $parameters;
	}


	/**
	 * Returns a map of parameters to generate a success transaction with cash
	 * @param array $paymentMethod a cash payment method
	 * @param array $overrideParameters
	 * @return a array with the parameters built
	 */
	public static function buildSuccessParametersCash($paymentMethod, $overrideParameters = null){

		$parametersBasic = PayUTestUtil::buildBasicParameters();

    	$parametersCash = array(PayUParameters::EXPIRATION_DATE=>PayUTestUtil::getNextWeekDate(),
    						PayUParameters::ACCOUNT_ID => '11',
    						PayUParameters::PAYER_DNI => '52494',
    						PayUParameters::PAYMENT_METHOD=>$paymentMethod
    	);

		$parameters = array_merge($parametersBasic,$parametersCash);

		if(isset($overrideParameters)){
			$parameters = array_replace($parameters,$overrideParameters);
		}

		return $parameters;
	}

	/**
	 * Returns a array of parameters to generate a success plan
	 * @param array $parameters extra parameters
	 * @param array $overrideParameters parameters to override the parameters built in the function
	 * @return array with the parameters built
	 */
	public static function buildSuccessParametersPlan($parameters = NULL, $overrideParameters = NULL){

		$now = new DateTime();

		$parametersPlan = array(
			PayUParameters::PLAN_DESCRIPTION => 'PHP Api Plan' ,
			PayUParameters::PLAN_CODE => 'PHP-Api-Plan-' . rand ( 999 , 9999999 ) . $now->getTimestamp(),
			PayUParameters::PLAN_INTERVAL => 'MONTH',
			PayUParameters::PLAN_INTERVAL_COUNT => '1',
			PayUParameters::PLAN_CURRENCY => 'COP',
			PayUParameters::PLAN_VALUE => '50000',
			PayUParameters::ACCOUNT_ID => '2',
			PayUParameters::PLAN_ATTEMPTS_DELAY => '2',
			PayUParameters::PLAN_MAX_PAYMENTS => '1',
		);

		$parameters = PayUTestUtil::buildParameters($parametersPlan, $parameters, $overrideParameters);
		return  $parameters;
	}

	/**
	 * Returns a array of parameters to generate a success credit card
	 * @param array $parameters extra parameters
	 * @param array $overrideParameters parameters to override the parameters built in the function
	 * @return array with the parameters built
	 */
	public static function buildSubscriptionParametersCreditCard($parameters = NULL, $overrideParameters = NULL){

		$parametersCreditCard = array(
			PayUParameters::CREDIT_CARD_NUMBER => '4929577907116575',
			PayUParameters::CREDIT_CARD_EXPIRATION_DATE => '2015/01',
			PayUParameters::PAYMENT_METHOD => 'VISA',

			PayUParameters::PAYER_NAME => 'Payer test name' ,
			PayUParameters::PAYER_STREET => ' Street 1 ',
			PayUParameters::PAYER_STREET_2 => 'Street 1 ',
			PayUParameters::PAYER_STREET_3 => 'Street 1 ',
			PayUParameters::PAYER_CITY => 'City',
			PayUParameters::PAYER_STATE => 'State',
			PayUParameters::PAYER_COUNTRY => PayUCountries::CO,
			PayUParameters::PAYER_POSTAL_CODE => '12345',
			PayUParameters::PAYER_PHONE => '123456789',
		);

		$parameters = PayUTestUtil::buildParameters($parametersCreditCard, $parameters, $overrideParameters);
		return  $parameters;

	}


	/**
	 * Returns a array of parameters to generate a success Bill item
	 * @param array $parameters extra parameters
	 * @param array $overrideParameters parameters to override the parameters built in the function
	 * @return array with the parameters built
	 */
	public static function buildRecurringBillItemParameters($parameters = NULL, $overrideParameters = NULL){

		$parametersRecurringBillItem = array(
				PayUParameters::DESCRIPTION => 'Test Item',
				PayUParameters::ITEM_VALUE => '5000',
				PayUParameters::CURRENCY => 'COP',
				PayUParameters::ITEM_TAX => '1000',
				PayUParameters::ITEM_TAX_RETURN_BASE => '100'
		);

		$parameters = PayUTestUtil::buildParameters($parametersRecurringBillItem, $parameters, $overrideParameters);
		return  $parameters;
	}


	/**
	 * Returns a array of parameters to generate a success customer
	 * @param array $parameters extra parameters
	 * @param array $overrideParameters parameters to override the parameters built in the function
	 * @return array with the parameters built
	 */
	public static function buildSubscriptionParametersCustomer($parameters = NULL, $overrideParameters = NULL){
		$parametersCustomer = array(
			PayUParameters::CUSTOMER_NAME => 'Test Test',
			PayUParameters::CUSTOMER_EMAIL => 'test@test.com'
		);
		$parameters = PayUTestUtil::buildParameters($parametersCustomer, $parameters, $overrideParameters);
		return  $parameters;
	}


	/**
	 * Returns a array of parameters to generate a success subscription
	 * @param array $parameters extra parameters
	 * @param array $overrideParameters parameters to override the parameters built in the function
	 * @return array with the parameters built
	 */
	public static function buildSubscriptionParameters($parameters = NULL, $overrideParameters = NULL){
		$parametersSubscription = array(
			PayUParameters::QUANTITY => '5',
			PayUParameters::INSTALLMENTS_NUMBER => '2',
			PayUParameters::TRIAL_DAYS => '2',
		);
		$parameters = PayUTestUtil::buildParameters($parametersSubscription, $parameters, $overrideParameters);
		return  $parameters;
	}

	/**
	 * Returns a array of parameters to generate a success bank account
	 * @param array $parameters extra parameters
	 * @param array $overrideParameters parameters to override the parameters built in the function
	 * @return array with the parameters built
	 */
	public static function buildParametersBankAccount($parameters = NULL, $overrideParameters = NULL){

		$parametersSubscription = array(
				PayUParameters::BANK_ACCOUNT_CUSTOMER_NAME => 'test user',
				PayUParameters::ACCOUNT_ID => '1',
				PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER => '123456789',
				PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER_TYPE => 'CC',
				PayUParameters::BANK_ACCOUNT_BANK_NAME => 'BANCOLOMBIA',
				PayUParameters::BANK_ACCOUNT_TYPE => 'SAVING',
				PayUParameters::BANK_ACCOUNT_NUMBER => '987654321',
				PayUParameters::COUNTRY => 'CO',
		);
		$parameters = PayUTestUtil::buildParameters($parametersSubscription, $parameters, $overrideParameters);
		return  $parameters;
	}


	/**
	 * Returns a array of parameters to generate a success bank account to brazil
	 * @param array $parameters extra parameters
	 * @param array $overrideParameters parameters to override the parameters built in the function
	 * @return array with the parameters built
	 */
	public static function buildParametersBankAccountBrazil($parameters = NULL, $overrideParameters = NULL){

		$parametersSubscription = array(
				PayUParameters::BANK_ACCOUNT_CUSTOMER_NAME => 'test user Brazil',
				PayUParameters::ACCOUNT_ID => '3',
				PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER => '78965874',
				PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER_TYPE => 'CNPJ',
				PayUParameters::BANK_ACCOUNT_BANK_NAME => 'SANTANDER',
				PayUParameters::BANK_ACCOUNT_TYPE => 'CURRENT',
				PayUParameters::BANK_ACCOUNT_NUMBER => '96325871',
				PayUParameters::BANK_ACCOUNT_ACCOUNT_DIGIT => '3',
				PayUParameters::BANK_ACCOUNT_AGENCY_DIGIT => '2',
				PayUParameters::BANK_ACCOUNT_AGENCY_NUMBER => '4568',
				PayUParameters::COUNTRY => 'BR',
		);
		$parameters = PayUTestUtil::buildParameters($parametersSubscription, $parameters, $overrideParameters);
		return  $parameters;
	}



	/**
	 * Process the parameters
	 * @param array $newParameters the new parameters
	 * @param array $parameters extra parameters to add the array
	 * @param array $overrideParameters the parameters to override the $newParameters
	 * @return array with the parameters built
	 */
	private static function buildParameters($newParameters, $parameters = NULL, $overrideParameters = NULL){

		if(!isset($newParameters)){
			throw new InvalidArgumentException('the newParameters argument cann\'t be null');
		}

		if(!isset($parameters)){
			$parameters = array();
		}

		$parameters = array_merge($parameters, $newParameters);

		if(isset($overrideParameters)){
			$parameters = array_replace($parameters,$overrideParameters);
		}
		return $parameters;
	}



	/**
	 * Returns a today date plus seven days
	 */
	public static function getNextWeekDate(){
		return PayUTestUtil::getDateFromToday ( PayUConfig::PAYU_DATE_FORMAT, 7);
	}

	/**
	 * Returns a today date sub seven days
	 */
	public static function getLastWeekDate(){
		return PayUTestUtil::getDateFromToday ( PayUConfig::PAYU_DATE_FORMAT, -7);
	}

	/**
	 * Returns a today date sub/plus the days argument and the date will be format by the dateFormat argument
	 * @param string $dateFormat the date format of the date
	 * @param int $days number the days to be added to the day if it is a negative
	 * number it will be subtracted from today
	 */
	public static function getDateFromToday($dateFormat, $days){
		$date = new DateTime();
		if( $days >= 0){
			$date->add(new DateInterval("P".$days."D"));
		}else{
			$date->sub(new DateInterval("P".(-1 * $days)."D"));
		}
		return $date->format($dateFormat);
	}



}
