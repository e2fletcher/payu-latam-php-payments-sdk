<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../Payu.php';
require_once dirname(__FILE__).'/PayUTestUtil.php';


/**
 * Test cases for procesing payments class
 * @author PayU Latam
 * @since 1.0.0
 * @version 1.0
 *
 */
class PayUPaymentsTest extends PHPUnit_Framework_TestCase
{
	
    /**
     * test request AuthorizationAndCapture without creditcard token
     */
    public function testDoAuthorizationAndCapture(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	
    	
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);

    }
    
    /**
     * test authorization and capture with payer birthdate
     */
    public function testDoAuthorizationAndCaptureMexico(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'VISA',
    					PayUParameters::CREDIT_CARD_NUMBER=>'4005580000029205',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
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
    					PayUParameters::PAYER_BIRTHDATE => '1980-06-22'
    			));
    	
    	 
    	 
    	 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    }
    
    /**
     * test request with invalid payer birthday format
     * @expectedException InvalidArgumentException
     */
    public function testDoAuthorizationAndCaptureInvalidBirthdayFormat(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'VISA',
    					PayUParameters::CREDIT_CARD_NUMBER=>'4005580000029205',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
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
    					PayUParameters::PAYER_BIRTHDATE => '80/06/22'
    			));
    	 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    }
    
    
    
    
    public function testDoAuthorizationAndCaptureCencosud(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);

    	
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	 
    	$parameters[PayUParameters::PAYMENT_METHOD] = 'CENCOSUD';
    	$parameters[PayUParameters::CREDIT_CARD_NUMBER] = '6034931111111111';
    	$parameters[PayUParameters::COUNTRY] = PayUCountries::AR;
    	$parameters[PayUParameters::ACCOUNT_ID] = '9';
    	$parameters[PayUParameters::CURRENCY] = 'ARS';
    	$parameters[PayUParameters::VALUE] = '1000';
    	
    	 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	 
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
    
    }
    
    
    
    /**
     * test request without creditcard number or tokenid
     */
    public function testDoAuthenticationFailed(){
    	PayU::$apiLogin = '0123';
    	PayU::$apiKey = '0123';
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    
    	$this->setExpectedException('PayUException');
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
    }
    
    
    /**
     * test request without creditcard number or tokenid
     * @expectedException InvalidArgumentException
     */
    public function testDoAuthorizationAndCaptureWithoutCreditCardNumber(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	 
    	unset($parameters[PayUParameters::CREDIT_CARD_NUMBER]);
    	 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::EN); 
    	 
    }
    
    /**
     * test request with number different for the franchise of the Card
     * @expectedException PayUException
     */
    public function testDoAuthorizationAndCaptureWithDifferentCreditCardNumberForTheCard(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
     
    	$parameters = array(
    			
    			PayUParameters::REFERENCE_CODE => 'referenceCode-' . rand(10000,999999999),
    			PayUParameters::PAYER_NAME=> 'PayerName-' . rand(10000,9999999) . ' PayerSurname-' .rand(10000,9999999),
    			PayUParameters::COUNTRY => PayUCountries::PA,
    			PayUParameters::ACCOUNT_ID => "8",
    			PayUParameters::CURRENCY => "USD",
    			PayUParameters::DESCRIPTION => 'description-' . rand(10000,9999999),
    			PayUParameters::VALUE => rand(1000,1500) . '.'.rand(10,99),
    			PayUParameters::INSTALLMENTS_NUMBER => '3',
    			PayUParameters::CREDIT_CARD_NUMBER => '5483259050882569',
    			PayUParameters::CREDIT_CARD_SECURITY_CODE => '495',
    			PayUParameters::CREDIT_CARD_EXPIRATION_DATE => '2016/01',
    			PayUParameters::PAYMENT_METHOD => PaymentMethods::VISA,
    	);
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::EN);
    	
    }
    
    /**
     * Test authorization and capture with Payer and Buyer Data
     */
    public function testDoAuthorizationAndCaptureWithPayerAndBuyer(){
    	
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	
    	$parametersBasic = PayUTestUtil::buildSuccessParametersCreditCard();
    	
    	$parametersBuyer = array(
    			PayUParameters::BUYER_NAME => 'Buyer Full Name',
    			PayUParameters::BUYER_DNI => '137946852',
    			PayUParameters::BUYER_STREET => 'Street Line 1',
    			PayUParameters::BUYER_STREET_2 => 'Suite 320',
    			PayUParameters::BUYER_STREET_3 => 'Private',
    			PayUParameters::BUYER_CITY => 'Bogota',
    			PayUParameters::BUYER_STATE => 'BG',
    			PayUParameters::BUYER_COUNTRY => 'CO',
    			PayUParameters::PAYMENT_METHOD => PaymentMethods::VISA,
    	);
    	
    	$parameters = array_merge($parametersBasic,$parametersBuyer);
    	 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
    	
    }
    
    /**
     * test request with format invalid expiration date
     * @expectedException PayUException
     */
    
    public function testDoAuthorizationAndCaptureWithExpirationDateInvalidFormat(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);  	 
  	
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	
    	$parameters[PayUParameters::CREDIT_CARD_EXPIRATION_DATE]= '01/2016';
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::EN);
    	 
    }
    
    /**
     * test request with different currency for the account id
     * 
     */
    
    public function testDoAuthorizationAndCaptureWithDifferentCurrencyForAccountId(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	 
    	$parameters[PayUParameters::CURRENCY]= "ARS";
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::EN);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);      	
    
    }
    
    
    /**
     * test request with parameters incomplete 
     * @expectedException InvalidArgumentException
     */
    
    public function testDoAuthorizationAndCaptureWithParametersIncomplete(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    
    	unset($parameters[PayUParameters::CURRENCY]);
    	unset($parameters[PayUParameters::VALUE]);
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::EN);
    
    }
    
    /**
     * test request without Account Id
     * @expectedException PayUException
     */
    
    public function testDoAuthorizationAndCaptureWithoutAccountId(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    
    	$parameters[PayUParameters::ACCOUNT_ID] = "";
    	    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::EN);
    
    }
    
    /**
    * test request with creditcard number invalid 
    * @expectedException PayUException
    */
    
    public function testDoAuthorizationAndCaptureWithCreditCardNumberInvalid(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    
    	$parameters[PayUParameters::CREDIT_CARD_NUMBER] = "Invalid_CreditCard_Number";
    		
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::EN);
    
    }
    
    /**
     * test request with reference code invalid
     * @expectedException InvalidArgumentException
     */
    
    public function testDoAuthorizationAndCaptureWithReferenceCodeInvalid(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    
    	$parameters[PayUParameters::REFERENCE_CODE] = "";
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::EN);
    
    }
    
    
    /**
     * test request capture with valid parameters
     * 
     */
    
    public function testDoAuthorization_CaptureWithValidParameters(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
      	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    	
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
    	
    	$response = PayUPayments::doCapture($parameters);
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);	
    
    }
    
    /**
     * test request capture with invalid authorization transaction
     * @expectedException PayUException
     */
    
    public function testDoAuthorization_CaptureWithInvalidAuthorizationTransaction(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    	 
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => '0000000-0000000-000000000000',
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);

    	$response = PayUPayments::doCapture($parameters);
    }
    
    
    /**
     * test request refund with valid authorization transaction
     * 
     */
    
    public function testDoRefund_Authorization_Capture(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
    
    	$response = PayUPayments::doCapture($parameters);
   	    	    	
    	$response = PayUPayments::doRefund($parameters);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);
    	
    }
    
    /**
     * test request refund with invalid parameters
     * @expectedException InvalidArgumentException
     */
    
    public function testDoRefund_Authorization_CaptureWithInvalidParameters(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
    
    	$response = PayUPayments::doCapture($parameters);
    	
    	$parameters[PayUParameters::TRANSACTION_ID] = "";
    
    	$response = PayUPayments::doRefund($parameters);
   	 
    }
    
    /**
     * test request void authorization transaction for Brasil
     * 
     */
    
    public function testDoVoid_AuthorizationForBrasil(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$authorizationResponse = PayUTestUtil::processTransactionBrasil(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
         
    	$response = PayUPayments::doVoid($parameters);

    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);
    }

    /**
     * test request void authorization transaction for Panam�
     *
     */
    
    public function testDoVoid_AuthorizationForPanama(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
   	
    	$response = PayUPayments::doVoid($parameters);
    
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);
    }
    
    /**
     * test request void authorization transaction for Panam� with order_id empty
     * @expectedException  InvalidArgumentException
     */
    
    public function testDoVoid_AuthorizationForPanamaWithOrderIdEmpty(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
    
    	$parameters[PayUParameters::ORDER_ID] = "";
    	 
    	$response = PayUPayments::doVoid($parameters);
    
    }
    
    
    
    /**
     * test authorization and capture with an invalid payment method
     */
    public function testDoAuthorizationAndCaptureInvalidPaymentMethod(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	    	
		$parameters = PayUTestUtil::buildSuccessParametersCreditCard();	
    	 
    	$parameters[PayUParameters::PAYMENT_METHOD] = "VISA_FAKE";

    	$this->setExpectedException('InvalidArgumentException');
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	 
		    	 
    }
    
    
    /**
     * test authorization and capture with invalid parameters
     */
    public function testDoAuthorizationAndCaptureInvalidParameters(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	    	
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	unset($parameters[PayUParameters::CURRENCY]);
    
    	$this->setExpectedException('InvalidArgumentException');
    	$result = PayUPayments::doAuthorizationAndCapture($parameters);
    }
    
    
    /**
     * test authorization and capture with invalid parameters
     */
    public function testDoAuthorizationAndCaptureInvalidCurrencyFormat(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	    	 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	$parameters[PayUParameters::VALUE] = "100.012345";
    
    	$this->setExpectedException('PayUException');
    	$result = PayUPayments::doAuthorizationAndCapture($parameters);
    }
    
    
    
    /**
     * test request Authorization without creditcard token
     */
    public function testDoAuthorization(){
    	
    	$parameters = array(PayUParameters::PAYER_NAME=>'ADÃO CONSTANÇA-7687556 Payer Surname374625-1544718 Payer Surname7159520');
    	
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	    	 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard($parameters);
    	 
    	$result = PayUPayments::doAuthorization($parameters);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);

    
    }

    
    /**
     * test request Capture without creditcard token
     */
    public function testDoCapture(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    	 
		$parameters = array(
				PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
				PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
		);

    	$response = PayUPayments::doCapture($parameters);    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);
    }
    
    
    /**
     * test request Void without creditcard token
     */
    public function testDoVoid(){
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED);
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
    
    	$response = PayUPayments::doVoid($parameters);
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);
    }
    
    
    /**
     * test request Void without creditcard token
     */
    public function testDoRefund(){
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	$parameters = array(PayUParameters::VALUE=>'120');
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION_AND_CAPTURE, PayUTransactionResponseCode::APPROVED, $parameters);
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    			PayUParameters::CURRENCY => "USD",
    			PayUParameters::VALUE=>'2'
    	);
    
    	$response = PayUPayments::doRefund($parameters);
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);
    }
    
    
    
    /**
     * test authorization and capture with Oxxo
     */
    public function testOxxo(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	
    	$parameters = array(PayUParameters::ACCOUNT_ID=>'11',
    			PayUParameters::PAYER_DNI=>'52494',
    			PayUParameters::COUNTRY=>PayUCountries::MX,
    			PayUParameters::VALUE => '100');
    	 
    	$parameters =PayUTestUtil::buildSuccessParametersCash(PaymentMethods::OXXO, $parameters);
    	
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals('PENDING',$response->transactionResponse->state);
    }
    
    /**
     * test authorization and capture for Oxxo whith parameters invalid
     * @expectedException InvalidArgumentException
     */
    public function testOxxoWhithParameteresInvalid(){
    
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = array(PayUParameters::ACCOUNT_ID=>'11',
    			PayUParameters::PAYER_DNI=>'',
    			PayUParameters::COUNTRY =>"MX");
    
    	$parameters =PayUTestUtil::buildSuccessParametersCash(PaymentMethods::OXXO, $parameters);
    
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
    
    }
    
    /**
     * test authorization and capture with BCP
     */
    public function testBCP(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = array(PayUParameters::ACCOUNT_ID=>'500198',
    			PayUParameters::PAYER_DNI=>'52494',
    			PayUParameters::COUNTRY=>PayUCountries::PE);
    
    	$parameters =PayUTestUtil::buildSuccessParametersCash(PaymentMethods::BCP, $parameters);
    	 
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals('PENDING',$response->transactionResponse->state);
    }
    
    /**
     * test authorization and capture for BCP whith parameters invalid
     * @expectedException InvalidArgumentException
     */
    public function testBCPWhithParameteresInvalid(){
    
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = array(PayUParameters::ACCOUNT_ID=>'500198',
    			PayUParameters::PAYER_DNI=>'',
    			PayUParameters::COUNTRY =>"PE");
    
    	$parameters =PayUTestUtil::buildSuccessParametersCash(PaymentMethods::BCP, $parameters);
    
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
    
    }
    
    /**
     * test authorization and capture with Baloto
     */
    public function testBaloto(){
    	
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	
    	$parameters = array(PayUParameters::ACCOUNT_ID=>'1',
    						PayUParameters::PAYER_DNI=>'52494',
    						PayUParameters::COUNTRY=>PayUCountries::CO,
    						PayUParameters::VALUE => '20000',
    						PayUParameters::CURRENCY => 'COP',
    	);
    
    	$parameters =PayUTestUtil::buildSuccessParametersCash(PaymentMethods::EFECTY, $parameters);
    	
    	 
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals('PENDING',$response->transactionResponse->state);
    	
    }
    
    public function testBankReferenced(){
    	 
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = array(PayUParameters::ACCOUNT_ID=>'1',
    			PayUParameters::PAYER_DNI=>'52494',
    			PayUParameters::COUNTRY=>PayUCountries::CO,
    			PayUParameters::VALUE => '50000',
    			PayUParameters::CURRENCY => 'COP',
    	);
    
    	$parameters =PayUTestUtil::buildSuccessParametersCash(PaymentMethods::BANK_REFERENCED, $parameters);
    	 
    
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
    
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals('PENDING',$response->transactionResponse->state);
    	 
    }
    
    
    
    
    /**
     * test authorization and capture for Baloto whith parameters invalid
     * @expectedException InvalidArgumentException
     */
    public function testBalotoWhithParameteresInvalid(){
    	 
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = array(PayUParameters::ACCOUNT_ID=>'1',
    			PayUParameters::PAYER_DNI=>'',
    			PayUParameters::COUNTRY=>PayUCountries::CO);
    
    	$parameters =PayUTestUtil::buildSuccessParametersCash(PaymentMethods::BALOTO, $parameters);
    	 
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
        	 
    }
    
        
    /**
     * test authorization and capture with Ripsa
     */
    public function testRipsa(){
    	 
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = array(PayUParameters::ACCOUNT_ID=>'9',
    			PayUParameters::PAYER_DNI=>'52494',
    			PayUParameters::COUNTRY=>PayUCountries::AR);
    
    	$parameters =PayUTestUtil::buildSuccessParametersCash(PaymentMethods::RIPSA, $parameters);
    	 
    
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
    
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals('PENDING',$response->transactionResponse->state);
    	 
    }
      
    
    /**
     * test get payment methods
     */
	public function testListPaymentMethods(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
		
		$response = PayUPayments::getPaymentMethods();
		
		$this->assertTrue(is_array($response->paymentMethods));
		$this->assertGreaterThan(0, count($response->paymentMethods));
		
		$paymentMethod =  $response->paymentMethods[0];
		$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
		$this->assertNotNull($paymentMethod->id);		
		$this->assertNotNull($paymentMethod->description);
		$this->assertNotNull($paymentMethod->country);
	}
	
	/**
	 * test get payment method available
	 *
	 */
	public function testPaymentMethodAvailable(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$response = PayUPayments::getPaymentMethodAvailability("VISA");
		$paymentMethod = $response->paymentMethod;
	
		$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
		$this->assertNotNull($paymentMethod->description);
		$this->assertNotNull($paymentMethod->type);
	
	}
	
	/**
	 * test get payment method not available
	 * @expectedException PayUException
	 */
	public function testPaymentMethodNotAvailable(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		$response = PayUPayments::getPaymentMethodAvailability("FICTITIOUS");
	}
	
	/**
	 * test list pse banks by Country
	 */
	public function testListPseBanks(){
		
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
		
		$parameters = array(PayUParameters::COUNTRY => PayUCountries::CO);
		
		$response = PayUPayments::getPSEBanks($parameters);
		
		$this->assertTrue(is_array($response->banks));
		$this->assertGreaterThan(0, count($response->banks));
		
		$prueba = 5;
		
		var_dump($prueba);
		
		$bank =  $response->banks[0];
		$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
		$this->assertNotNull($bank->id);
		$this->assertNotNull($bank->description);
		$this->assertNotNull($bank->pseCode);
		
	}
    
	
	/**
	 * test to ping request
	 */
    public function testDoPing(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$isTest = true;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$response = PayUPayments::doPing();
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    }
    
    
    /**
     * test do authorization with token
     */
    public function testDoAuthorizationWithToken(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	
    	$responseCreditCardToken = PayUTestUtil::createToken();
    	$parametersToken = array(PayUParameters::TOKEN_ID=>$responseCreditCardToken->creditCardToken->creditCardTokenId,
    							 PayUParameters::PAYMENT_METHOD => 'EFECTY');
        
    	Var_Dump($parametersToken[PayUParameters::TOKEN_ID]);
                
    	$parameters = array_merge(PayUTestUtil::buildBasicParameters(), $parametersToken);
    	
    	$response = PayUPayments::doAuthorization($parameters);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertNotEmpty($response->transactionResponse->orderId);
    	$this->assertNotEmpty($response->transactionResponse->transactionId);
    	 
    }
    
     /**
     * test do authorization without token
     * @expectedException InvalidArgumentException
     */
    public function testDoAuthorizationWithoutToken(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	 
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$responseCreditCardToken = PayUTestUtil::createToken();
    	$parametersToken = array(PayUParameters::TOKEN_ID=>'',
    			PayUParameters::PAYMENT_METHOD => PaymentMethods::VISA);
    
    	Var_Dump($parametersToken[PayUParameters::TOKEN_ID]);
    
    	$parameters = array_merge(PayUTestUtil::buildBasicParameters(), $parametersToken);
    	 
    	$response = PayUPayments::doAuthorization($parameters);
    	 
    }
    
        
    /**
     * test do authorization and capture with token
     */
    public function testDoAuthorizationAndCaptureWithToken(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	 
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$responseCreditCardToken = PayUTestUtil::createToken();
    	$parametersToken = array(PayUParameters::TOKEN_ID=>$responseCreditCardToken->creditCardToken->creditCardTokenId,
    			PayUParameters::CREDIT_CARD_SECURITY_CODE=>'123',
    			PayUParameters::PAYMENT_METHOD => PaymentMethods::VISA);
    
    	$parameters = array_merge(PayUTestUtil::buildBasicParameters(), $parametersToken);
    	 
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertNotEmpty($response->transactionResponse->orderId);
    	$this->assertNotEmpty($response->transactionResponse->transactionId);
    
    }
    
    /**
     * test do authorization and capture without token
     * @expectedException InvalidArgumentException
     */
    public function testDoAuthorizationAndCaptureWithOutToken(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$responseCreditCardToken = PayUTestUtil::createToken();
    	$parametersToken = array(PayUParameters::TOKEN_ID=>'',
    		         			PayUParameters::PAYMENT_METHOD => PaymentMethods::VISA);
    
    	$parameters = array_merge(PayUTestUtil::buildBasicParameters(), $parametersToken);
    
    	$response = PayUPayments::doAuthorizationAndCapture($parameters);
   
    }
    
    
    
    /**
     * test do authorization and capture with token
     */
    public function testDoAuthorizationAndCaptureWithoutSecurityCode(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    												array(
    														PayUParameters::PAYMENT_METHOD=>'MASTERCARD',
    														PayUParameters::CREDIT_CARD_NUMBER=>'5557797953382568',
    														PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    														PayUParameters::CREDIT_CARD_SECURITY_CODE=>NULL,
    														PayUParameters::PROCESS_WITHOUT_CVV2=>TRUE,
    														PayUParameters::ACCOUNT_ID => 1,
    														PayUParameters::COUNTRY => PayUCountries::CO,
    														PayUParameters::CURRENCY => 'COP',
    														PayUParameters::VALUE => '1000'
    													));
    	
    	
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    }
    
    /**
     * test do authorization and capture with PROCESS_WITHOUT_CVV2 false and CREDIT_CARD_SECURITY_CODE Null
     * @expectedException InvalidArgumentException
     */
    public function testDoAuthorizationAndCaptureWithSecurityCodeNullWithoutCVV2False(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'MASTERCARD',
    					PayUParameters::CREDIT_CARD_NUMBER=>'5557797953382568',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::PROCESS_WITHOUT_CVV2=>FALSE,
    					PayUParameters::CREDIT_CARD_SECURITY_CODE=>NULL,
    					PayUParameters::ACCOUNT_ID => 1,
    					PayUParameters::COUNTRY => PayUCountries::CO,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '1000'
    			));
    	 
    	 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	 
    }
    
    /**
     * test do authorization and capture with PROCESS_WITHOUT_CVV2 false and CREDIT_CARD_SECURITY_CODE
     */
    public function testDoAuthorizationAndCaptureWithSecurityCodeWithoutCVV2False(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'MASTERCARD',
    					PayUParameters::CREDIT_CARD_NUMBER=>'5557797953382568',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::PROCESS_WITHOUT_CVV2=>FALSE,
    					PayUParameters::ACCOUNT_ID => 8,
    					PayUParameters::COUNTRY => PayUCountries::CO,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '20000'
    			));
    
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    
    }
    
    
    /**
     * test do authorization and capture with PROCESS_WITHOUT_CVV2 true and CREDIT_CARD_SECURITY_CODE
     */
    public function testDoAuthorizationAndCaptureWithSecurityCodeWithoutCVV2True(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'MASTERCARD',
    					PayUParameters::CREDIT_CARD_NUMBER=>'5557797953382568',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::PROCESS_WITHOUT_CVV2=>TRUE,
    					PayUParameters::ACCOUNT_ID => 8,
    					PayUParameters::COUNTRY => PayUCountries::PA,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '100000'
    			));
    
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    
    }
    
    /**
     * test do authorization with PROCESS_WITHOUT_CVV2 false and CREDIT_CARD_SECURITY_CODE Null
     * @expectedException InvalidArgumentException
     */
    public function testDoAuthorizationWithSecurityCodeNullWithoutCVV2False(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'MASTERCARD',
    					PayUParameters::CREDIT_CARD_NUMBER=>'5557797953382568',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::PROCESS_WITHOUT_CVV2=>FALSE,
    					PayUParameters::CREDIT_CARD_SECURITY_CODE=>NULL,
    					PayUParameters::ACCOUNT_ID => 1,
    					PayUParameters::COUNTRY => PayUCountries::CO,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '100000'
    			));
    
    	$result = PayUPayments::doAuthorization($parameters, SupportedLanguages::ES);
    
    }
    
    /**
     * test do authorization with PROCESS_WITHOUT_CVV2 false and CREDIT_CARD_SECURITY_CODE
     */
    public function testDoAuthorizationWithSecurityCodeWithoutCVV2False(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'MASTERCARD',
    					PayUParameters::CREDIT_CARD_NUMBER=>'5557797953382568',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::PROCESS_WITHOUT_CVV2=>FALSE,
    					PayUParameters::ACCOUNT_ID => 8,
    					PayUParameters::COUNTRY => PayUCountries::CO,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '200000'
    			));
    
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    
    }
    
    /**
     * test do authorization with PROCESS_WITHOUT_CVV2 true and CREDIT_CARD_SECURITY_CODE Empty
     */
    public function testDoAuthorizationWithSecurityCodeEmptyWithoutCVV2True(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'MASTERCARD',
    					PayUParameters::CREDIT_CARD_NUMBER=>'5557797953382568',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::PROCESS_WITHOUT_CVV2=>TRUE,
    					PayUParameters::ACCOUNT_ID => 8,
    					PayUParameters::COUNTRY => PayUCountries::CO,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '100000'
    			));
    	 
    	unset($parameters[PayUParameters::CREDIT_CARD_SECURITY_CODE]);
    
    	$result = PayUPayments::doAuthorization($parameters, SupportedLanguages::ES);
    
    }
    
    
    
    /**
     * test do authorization with PROCESS_WITHOUT_CVV2 true and CREDIT_CARD_SECURITY_CODE Empty
     */
    public function testDoAuthorizationWithSecurityCodeEmptyWithoutCVV2TrueVISACredibanco(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'VISA',
    					PayUParameters::CREDIT_CARD_NUMBER=>'4005580000029205',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::PROCESS_WITHOUT_CVV2=>TRUE,
    					PayUParameters::ACCOUNT_ID => 1,
    					PayUParameters::COUNTRY => PayUCountries::CO,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '100.11'
    			));
    
    	unset($parameters[PayUParameters::CREDIT_CARD_SECURITY_CODE]);
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    
    }
    
    /**
     * test do authorization with PROCESS_WITHOUT_CVV2 true and CREDIT_CARD_SECURITY_CODE Empty
     */
    public function testDoAuthorizationVISACredibanco(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'VISA',
    					PayUParameters::CREDIT_CARD_NUMBER=>'4005580000029205',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::CREDIT_CARD_SECURITY_CODE=>495,
    					PayUParameters::ACCOUNT_ID => 1,
    					PayUParameters::COUNTRY => PayUCountries::CO,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '100'
    			));
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    
    }
    
    
    /**
     * test do cancellation credibanco
     */
    public function testDoVoidToCredibanco(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	
    	$overrideParameters = array(
    					PayUParameters::PAYMENT_METHOD=>'VISA',
    					PayUParameters::CREDIT_CARD_NUMBER=>'4005580000029205',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::CREDIT_CARD_SECURITY_CODE=>495,
    					PayUParameters::ACCOUNT_ID => 1,
    					PayUParameters::COUNTRY => PayUCountries::CO,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '100'
    			);
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION, PayUTransactionResponseCode::APPROVED,$overrideParameters);
    	
    
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
    	 
    	$response = PayUPayments::doVoid($parameters);
    
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);
    }
    
    
    /**
     * test do refund credibanco
     */
    public function testDoRefundToCredibanco(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$overrideParameters = array(
    			PayUParameters::PAYMENT_METHOD=>'VISA',
    			PayUParameters::CREDIT_CARD_NUMBER=>'4005580000029205',
    			PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    			PayUParameters::CREDIT_CARD_SECURITY_CODE=>495,
    			PayUParameters::ACCOUNT_ID => 1,
    			PayUParameters::COUNTRY => PayUCountries::CO,
    			PayUParameters::CURRENCY => 'COP',
    			PayUParameters::VALUE => '100'
    	);
    
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION_AND_CAPTURE, PayUTransactionResponseCode::APPROVED,$overrideParameters);
    	 
    
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    	);
    
    	
    	$response = PayUPayments::doRefund($parameters);
    
    	$this->assertEquals(PayUResponseCode::SUCCESS, $response->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED,$response->transactionResponse->state);
    }
    

    
    
    
    /**
     * test do authorization with PROCESS_WITHOUT_CVV2 true and CREDIT_CARD_SECURITY_CODE
     */
    public function testDoAuthorizationWithSecurityCodeWithoutCVV2True(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard(
    			array(
    					PayUParameters::PAYMENT_METHOD=>'MASTERCARD',
    					PayUParameters::CREDIT_CARD_NUMBER=>'5557797953382568',
    					PayUParameters::CREDIT_CARD_EXPIRATION_DATE=>'2015/01',
    					PayUParameters::PROCESS_WITHOUT_CVV2=>TRUE,
    					PayUParameters::ACCOUNT_ID => 8,
    					PayUParameters::COUNTRY => PayUCountries::PA,
    					PayUParameters::CURRENCY => 'COP',
    					PayUParameters::VALUE => '100000'
    			));
        
    	$result = PayUPayments::doAuthorization($parameters, SupportedLanguages::ES);
    
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
     }
    /**
     * test request AuthorizationAndCapture with deviceSessionId
     */
    public function testDoAuthorizationAndCaptureWithDeviceSessionId(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    		 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	
    	$parameters[PayUParameters::DEVICE_SESSION_ID]='ASDFASDF12341234213234csasdfas';  	
    		 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    		 
    		 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
    	
  	} 	

  	/**
  	 * test request AuthorizationAndCapture with deviceSessionId Empty
  	 */
  	public function testDoAuthorizationAndCaptureWithDeviceSessionIdEmpty(){
  		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
  		PayU::$apiKey = PayUTestUtil::API_KEY;
  		PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
  		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
  		 
  		$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
  		 
  		$parameters[PayUParameters::DEVICE_SESSION_ID]='';
  		 
  		$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
  		 
  		 
  		$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
  		$this->assertNotEmpty($result->transactionResponse->orderId);
  		$this->assertNotEmpty($result->transactionResponse->transactionId);
  		$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
  		$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
  		 
  	}
  	
  	/**
  	 * test request AuthorizationAndCapture with 2 white spaces in DeviceSessionId
  	 */
  	public function testDoAuthorizationAndCaptureWithTwoWhiteSpacesDeviceSessionId(){
  		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
  		PayU::$apiKey = PayUTestUtil::API_KEY;
  		PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
  		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
  			
  		$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
  			
  		$parameters[PayUParameters::DEVICE_SESSION_ID]='  ';
  			
  		$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
  			
  		$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
  		$this->assertNotEmpty($result->transactionResponse->orderId);
  		$this->assertNotEmpty($result->transactionResponse->transactionId);
  		$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
  		$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
  			
  	}
  	
  	/**
  	 * test request AuthorizationAndCapture Setting on DeviceSessionID field 256 Characters
  	 * @expectedException PayUException
  	 */
  	
  	public function testDoAuthorizationAndCaptureSetting256CharactersOnSpacesDeviceSessionId(){
  		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
  		PayU::$apiKey = PayUTestUtil::API_KEY;
  		PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
  		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
  			
  		$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
  			
  		$parameters[PayUParameters::DEVICE_SESSION_ID]='1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
  			
  		$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
  			
  		$this->assertEquals(PayUResponseCode::ERROR, $result->code);
  		$this->assertNotEmpty($result->transactionResponse->orderId);
  		$this->assertNotEmpty($result->transactionResponse->transactionId);
  		$this->assertEquals(PayUTransactionResponseCode::ERROR, $result->transactionResponse->state);
  		$this->assertEquals(PayUTransactionResponseCode::ERROR, $result->transactionResponse->responseCode);
  			
  	}
  	
   	/**
   	 * test request AuthorizationAndCapture with IpAddress and UserAgent Valids
   	 */
   	public function testDoAuthorizationAndCaptureWithIpAddressAndUserAgent(){
   		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    		 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    		 
    	$parameters[PayUParameters::IP_ADDRESS]='192.168.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1';
    	$parameters[PayUParameters::USER_AGENT]='1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
    	    		 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    		 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
    		 
    }
    
    /**
     * test request AuthorizationAndCapture with IpAddress and UserAgent Emptys
     */
    public function testDoAuthorizationAndCaptureWithIpAddressAndUserAgentEmptys(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	 
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    	 
    	$parameters[PayUParameters::IP_ADDRESS]='';
    	$parameters[PayUParameters::USER_AGENT]='';
    	 
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    	 
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
    	 
    }
    
    /**
     * test request AuthorizationAndCapture Setting on UserAgent field 1025 Characters
	 * @expectedException PayUException
     */
  
    public function testDoAuthorizationAndCaptureSetting1025CharactersOnUserAgentEmptys(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    
    	$parameters[PayUParameters::IP_ADDRESS]='192.168.1.1';
    	$parameters[PayUParameters::USER_AGENT]='11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    
    	$this->assertEquals(PayUResponseCode::ERROR, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::ERROR, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::ERROR, $result->transactionResponse->responseCode);
    
    }

    /**
     * test request AuthorizationAndCapture Setting on IP Address field 40 Characters
     * @expectedException PayUException
     */
    
    public function testDoAuthorizationAndCaptureSetting40CharactersOnIpAddress(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    
    	$parameters[PayUParameters::IP_ADDRESS]='192.168.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.';
    	$parameters[PayUParameters::USER_AGENT]='Chrome' . microtime();
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    
    	$this->assertEquals(PayUResponseCode::ERROR, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::ERROR, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::ERROR, $result->transactionResponse->responseCode);
    
    }

    /**
     * test request AuthorizationAndCapture Setting On IpAddress and UserAgent Two White Spaces
     */
    public function testDoAuthorizationAndCaptureSettingOnIpAddressAndUserAgentTwoWhiteSpaces(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    
    	$parameters = PayUTestUtil::buildSuccessParametersCreditCard();
    
    	$parameters[PayUParameters::IP_ADDRESS]='  ';
    	$parameters[PayUParameters::USER_AGENT]='  ';
    
    	$result = PayUPayments::doAuthorizationAndCapture($parameters, SupportedLanguages::ES);
    
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->state);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $result->transactionResponse->responseCode);
    
    }    
    
    
    /**
     * Test request AuthorizationAndCapture with PSE payment method
     * @author angela.aguirre
     */
    public function testDoAuthorizationAndCaptureWithPSEPaymentMethod(){
    	PayU::$apiLogin = PayUTestUtil::API_LOGIN;
    	PayU::$apiKey = PayUTestUtil::API_KEY;
    	PayU::$merchantId = PayUTestUtil::MERCHANT_ID;
    	PayU::$language = SupportedLanguages::ES;
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	
    	$parameters = array(
    	
    			// Payer Data
    			PayUParameters::PAYER_NAME => "Angela Aguirre",
    			PayUParameters::PAYER_EMAIL => "angela.aguirre@payulatam.com",
    			PayUParameters::PAYER_CONTACT_PHONE => "5559631",
    			PayUParameters::PAYER_DNI => "123456789",
    	
    			// Payer Address (Shipping Address)
    			PayUParameters::PAYER_STREET => "Street Number 1",
    			PayUParameters::PAYER_STREET_2 => "Street Number 2",
    			PayUParameters::PAYER_STREET_3 => "Street Number 3",
    			PayUParameters::PAYER_POSTAL_CODE => "00000",
    			PayUParameters::PAYER_CITY => "Colombia City",
    			PayUParameters::PAYER_STATE => "State",
    			PayUParameters::PAYER_COUNTRY => "CO",
    			PayUParameters::PAYER_PHONE => "5558855",
    	
    			// Buyer Data
    			PayUParameters::BUYER_NAME => "Payu",
    			PayUParameters::BUYER_EMAIL => "angela.aguirre@payulatam.com",
    			PayUParameters::BUYER_CONTACT_PHONE => "5559631",
    			PayUParameters::BUYER_DNI => "123456789",
    	
    			// Buyer Address (Billing Address)
    			PayUParameters::BUYER_STREET => "Street Number 1",
    			PayUParameters::BUYER_STREET_2 => "Street Number 2",
    			PayUParameters::BUYER_STREET_3 => "Street Number 3",
    			PayUParameters::BUYER_POSTAL_CODE => "00000",
    			PayUParameters::BUYER_CITY => "Colombia City",
    			PayUParameters::BUYER_STATE => "State",
    			PayUParameters::BUYER_COUNTRY => "CO",
    			PayUParameters::BUYER_PHONE => "5558855",
    	
    			PayUParameters::INSTALLMENTS_NUMBER => "1",
    			PayUParameters::COUNTRY => PayUCountries::CO,
    			PayUParameters::ACCOUNT_ID => "1",
    			PayUParameters::PAYER_COOKIE  => "cookie_".time(),
    	
    			// Valores
    			PayUParameters::CURRENCY => "COP",
    			PayUParameters::REFERENCE_CODE => "PHP-SDK-Pagador",
    			PayUParameters::DESCRIPTION => "Pruebas SDK PHP",
    			PayUParameters::VALUE => "1000.00",
    	
    			// Datos del pago PSE
    			PayUParameters::PSE_FINANCIAL_INSTITUTION_CODE => "1022",
    			PayUParameters::PSE_FINANCIAL_INSTITUTION_NAME => "Banco Union Colombiano",
    			PayUParameters::PAYER_PERSON_TYPE=>"N",
    			PayUParameters::PAYER_DOCUMENT_TYPE=>"CC",
    			PayUParameters::IP_ADDRESS=>"127.0.0.1",
    			PayUParameters::USER_AGENT=>"Mozilla/4.0(compatible; MSIE 5.15; Mac_PowerPC)",
    			PayUParameters::PAYMENT_METHOD => PaymentMethods::PSE);
    	
    	$result = PayUPayments::doAuthorizationAndCapture($parameters);
    
    	$this->assertEquals(PayUResponseCode::SUCCESS, $result->code);
    	$this->assertNotEmpty($result->transactionResponse->orderId);
    	$this->assertNotEmpty($result->transactionResponse->transactionId);
    	$this->assertEquals("PENDING", $result->transactionResponse->state);
    	$this->assertEquals("PENDING_AWAITING_PSE_CONFIRMATION", $result->transactionResponse->responseCode);
    	
    	var_dump($result);
    	    	    
    }
    

    /**
     * test request Void with reason
     */
    public function testDoVoidWithReason(){
    	
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION_AND_CAPTURE, PayUTransactionResponseCode::APPROVED);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $authorizationResponse->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $authorizationResponse->transactionResponse->state);
    	
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    			PayUParameters::REASON => "Reason For Cancellation (Void): Testing PHP SDK - Johan Navarrete"
    	);
    
    	$voidResponse = PayUPayments::doVoid($parameters);
    	$this->assertEquals(PayUResponseCode::SUCCESS, $voidResponse->code);
    	
    	//If it breaks. verify that the account is not enabled for online cancellations.
    	$this->assertEquals(PayUTransactionResponseCode::PENDING_CANCELATION_REVIEW, $voidResponse->transactionResponse->state);
    }
    
   /**
     * test request Refound with reason
     */
    public function testDoRefoundWithReason(){
    	
    	Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
    	$authorizationResponse = PayUTestUtil::processTransaction(TransactionType::AUTHORIZATION_AND_CAPTURE, PayUTransactionResponseCode::APPROVED);
    	
    	$this->assertEquals(PayUResponseCode::SUCCESS, $authorizationResponse->code);
    	$this->assertEquals(PayUTransactionResponseCode::APPROVED, $authorizationResponse->transactionResponse->state);
    	
    	$parameters = array(
    			PayUParameters::TRANSACTION_ID => $authorizationResponse->transactionResponse->transactionId,
    			PayUParameters::ORDER_ID => $authorizationResponse->transactionResponse->orderId,
    			PayUParameters::REASON => "Reason For Cancellation (Refound): Testing PHP SDK - Johan Navarrete"
    	);
    
    	$refoundResponse = PayUPayments::doRefund($parameters);
    	$this->assertEquals(PayUResponseCode::SUCCESS, $refoundResponse->code);
    	
    	//If it breaks. verify that the account is not enabled for online cancellations.
    	$this->assertEquals(PayUTransactionResponseCode::PENDING_CANCELATION_REVIEW, $refoundResponse->transactionResponse->state);
    }
}


