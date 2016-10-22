<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../Payu.php';
require_once dirname(__FILE__).'/PayUTestUtil.php';


/**
 * Test cases to subscriptions api
 * @author PayU Latam
 * @since 1.0.0
 * @version 1.0
 *
 */
class PayUSubscriptionsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * subscription object for multipurposes test
	 * @var unknown
	 */
	protected $subscription;

	/**
	 * test to create a subscription
	 */
	public function testCreateSubscription(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters		
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters		
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		
		$response = PayUSubscriptions::createSubscription($parameters);
		
		$this->assertNotNull($response->id);
 		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
 		$this->assertNotNull($response->plan->id);
 		$this->assertNotNull($response->customer->id);
 		$this->assertCount( 1, $response->customer->creditCards);
 		$this->assertEquals($parameters[PayUParameters::INSTALLMENTS_NUMBER], $response->installments);
 		$this->assertEquals($parameters[PayUParameters::QUANTITY], $response->quantity);
 		
	}
	
	/**
	 * test to create a customer without creditcard
	 */
	public function testCreateCustomerWhithoutCreditCard(){
		
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		
		$response = PayUCustomers::create($parameters);
		
		$this->assertNotNull($response->id);
		$this->assertEquals($response->fullName, $parameters[PayUParameters::CUSTOMER_NAME]);
		$this->assertEquals($response->email, $parameters[PayUParameters::CUSTOMER_EMAIL]);
	}
	
	/**
	 * test to create a plan
	 */
	public function testCreatePlan(){

		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$parameters = array();
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		
		$parameters[PayUParameters::PLAN_VALUE]  = '50000';
		$parameters[PayUParameters::PLAN_TAX]  = '10000';
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE]  = '40000';
		$parameters[PayUParameters::PLAN_MAX_PAYMENT_ATTEMPTS]  = '2';
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS]  = '2';
		
		
		$response = PayUSubscriptionPlans::create($parameters);
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->planCode);
		$this->assertEquals($parameters[PayUParameters::PLAN_DESCRIPTION], $response->description);
		$this->assertEquals($parameters[PayUParameters::PLAN_INTERVAL], $response->interval);
		$this->assertEquals($parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS], $response->maxPendingPayments);
		$this->assertEquals($parameters[PayUParameters::PLAN_MAX_PAYMENT_ATTEMPTS], $response->maxPaymentAttempts);
		$this->assertEquals($parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS], $response->maxPendingPayments);
		$this->assertCount(3, $response->additionalValues);
		
	}
	
	/**
	 * test to create a plan with accountId Empty
	 * @expectedException InvalidArgumentException
	 */
	public function testCreatePlanWithAccountIdEmpty(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		$parameters[PayUParameters::ACCOUNT_ID] = '';
		$parameters[PayUParameters::PLAN_VALUE]  = '50000';
		$parameters[PayUParameters::PLAN_TAX]  = '10000';
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE]  = '40000';
		$parameters[PayUParameters::PLAN_MAX_PAYMENT_ATTEMPTS]  = '2';
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS]  = '2';
		
		$response = PayUSubscriptionPlans::create($parameters);

	}
	
	/**
	 * test to create a plan with planvalue Invalid
	 * @expectedException PayUException
	 */
	public function testCreatePlanWithPlanValueInvalid(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		$parameters[PayUParameters::PLAN_VALUE]  = '-50000';
		$parameters[PayUParameters::PLAN_TAX]  = '10000';
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE]  = '40000';
		$parameters[PayUParameters::PLAN_MAX_PAYMENT_ATTEMPTS]  = '2';
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS]  = '2';
	
		$response = PayUSubscriptionPlans::create($parameters);
	
	}
	
	/**
	 * test to create a plan with plan interval WEEK
	 */
	public function testCreatePlanWithPlanIntervalWEEK(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
	
		$parameters[PayUParameters::PLAN_VALUE]  = '50000';
		$parameters[PayUParameters::PLAN_TAX]  = '10000';
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE]  = '40000';
		$parameters[PayUParameters::PLAN_MAX_PAYMENT_ATTEMPTS]  = '2';
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS]  = '2';
		$parameters[PayUParameters::PLAN_INTERVAL] = 'WEEK';
	
	
		$response = PayUSubscriptionPlans::create($parameters);
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->planCode);
		$this->assertEquals($parameters[PayUParameters::PLAN_DESCRIPTION], $response->description);
		$this->assertEquals($parameters[PayUParameters::PLAN_INTERVAL], $response->interval);
		$this->assertEquals($parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS], $response->maxPendingPayments);
		$this->assertEquals($parameters[PayUParameters::PLAN_MAX_PAYMENT_ATTEMPTS], $response->maxPaymentAttempts);
		$this->assertEquals($parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS], $response->maxPendingPayments);
		$this->assertCount(3, $response->additionalValues);
	
	}
	
	/**
	 * test to create a credit card
	 */
	public function testCreateCreditCard(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
	
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard();
		
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		
		$response = PayUCreditCards::create($parameters);
		$this->assertNotNull($response->token);
	}

	/**
	 * test to create a customer with credit card
	 */
	public function testCreateCustomerWithCreditCard(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$response = PayUCustomers::createCustomerWithCreditCard($parameters);

		$this->assertNotNull($response->id);
		$this->assertEquals($response->fullName, $parameters[PayUParameters::CUSTOMER_NAME]);
		$this->assertEquals($response->email, $parameters[PayUParameters::CUSTOMER_EMAIL]);
		$this->assertCount( 1, $response->creditCards);
		$this->assertNotNull($response->creditCards[0]->token);
	}
	
	/**
	 * test to create a customer with credit card
	 */
	public function testCreateCustomerWithCreditCardCencosud(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$parameters[PayUParameters::PAYMENT_METHOD] = 'CENCOSUD';
		$parameters[PayUParameters::CREDIT_CARD_NUMBER] = '6034931111111111'; 
	
		$response = PayUCustomers::createCustomerWithCreditCard($parameters);
	
		$this->assertNotNull($response->id);
		$this->assertEquals($response->fullName, $parameters[PayUParameters::CUSTOMER_NAME]);
		$this->assertEquals($response->email, $parameters[PayUParameters::CUSTOMER_EMAIL]);
		$this->assertCount( 1, $response->creditCards);
		$this->assertNotNull($response->creditCards[0]->token);
	}
	
	
	/**
	 * test to create a customer with credit card document
	 */
	public function testCreateCustomerWithCreditCardDocument(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		$parameters[PayUParameters::CREDIT_CARD_DOCUMENT] = '12312131313';
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$response = PayUCustomers::createCustomerWithCreditCard($parameters);
	
		$this->assertNotNull($response->id);
		$this->assertEquals($response->fullName, $parameters[PayUParameters::CUSTOMER_NAME]);
		$this->assertEquals($response->email, $parameters[PayUParameters::CUSTOMER_EMAIL]);
		$this->assertEquals($response-> creditCards[0] -> document, $parameters[PayUParameters::CREDIT_CARD_DOCUMENT]);
		$this->assertCount( 1, $response->creditCards);
		$this->assertNotNull($response->creditCards[0]->token);
	}
	
	
	/**
	 * test to create a customer with expiration date invalid
	 * @expectedException PayUException
	 */
	public function testCreateCustomerWithExpirationDateInvalid(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		$parameters[PayUParameters::CREDIT_CARD_EXPIRATION_DATE]= '2014/01';
	
		$response = PayUCustomers::createCustomerWithCreditCard($parameters);
	
	}
	
	/**
	 * test to create a customer with name and email empty
	 * @expectedException InvalidArgumentException
	 */
	public function testCreateCustomerWithNameEmailEmpty(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		$parameters[PayUParameters::CUSTOMER_NAME] = '';
		$parameters[PayUParameters::CUSTOMER_EMAIL] = '';
		
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$response = PayUCustomers::createCustomerWithCreditCard($parameters);
	
	}
	

	/**
	 * test to create a customer with name composed of numbers and special characters
	*/
	public function testCreateCustomerWithNameComposedNumbersAndSpecialCharacters(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		$parameters[PayUParameters::CUSTOMER_NAME] = '#~@#123441234~€€¬¬¬7';
			
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
			
		$response = PayUCustomers::createCustomerWithCreditCard($parameters);
		
		$this->assertNotNull($response->id);
		$this->assertEquals($response->fullName, $parameters[PayUParameters::CUSTOMER_NAME]);
		$this->assertEquals($response->email, $parameters[PayUParameters::CUSTOMER_EMAIL]);
		$this->assertNotNull($response->creditCards[0]->token);
	
	}
	
	
	/**
	 * test to create a subscription with minimal information
	 */
	public function testCreateBasicSubscription(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCard = PayUCreditCards::create(PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams));
		
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::PLAN_CODE] = $plan->planCode;
		$parameters[PayUParameters::TOKEN_ID] = $creditCard->token;
		
		
		$response = PayUSubscriptions::createSubscription($parameters);
		
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertNotNull($response->plan->id);
		$this->assertNotNull($response->customer->id);
		$this->assertEquals($parameters[PayUParameters::INSTALLMENTS_NUMBER], $response->installments);
		$this->assertEquals($parameters[PayUParameters::QUANTITY], $response->quantity);
	}
	
	/**
	 * test to create a subscription with different credentials for the plan
	 * @expectedException PayUException
	 */
	public function testCreateBasicSubscriptionWithDifferentCredentialsForPlan(){
		PayU::$apiLogin = '9d9adf443a5227e';
		PayU::$apiKey = '012345678901';
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCard = PayUCreditCards::create(PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams));
	
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::PLAN_CODE] = $plan->planCode;
		$parameters[PayUParameters::TOKEN_ID] = $creditCard->token;
	
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertNotNull($response->plan->id);
		$this->assertNotNull($response->customer->id);
		$this->assertEquals($parameters[PayUParameters::INSTALLMENTS_NUMBER], $response->installments);
		$this->assertEquals($parameters[PayUParameters::QUANTITY], $response->quantity);
	}
	
	
	
	/**
	 * test to create a subscription with Plan and Customer Existing
	 */
	
	public function testCreateSubscriptionWithExistingCustomerAndPlan(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
	
		// Customer parameters
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
	
		// Plan parameters
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan($parameters));
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		$parameters[PayUParameters::PLAN_CODE] = $plan->planCode;
	
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertNotNull($response->plan->id);
		$this->assertNotNull($response->customer->id);
		$this->assertCount( 1, $response->customer->creditCards);
		$this->assertEquals($parameters[PayUParameters::INSTALLMENTS_NUMBER], $response->installments);
		$this->assertEquals($parameters[PayUParameters::QUANTITY], $response->quantity);
	}
	
	/**
	 * test to create a subscription with an existing customer
	 */
	public function testCreateSubscriptionWithExistingCustomer(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();		
		// Customer parameters
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$response = PayUSubscriptions::createSubscription($parameters);
		
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertNotNull($response->plan->id);
		$this->assertNotNull($response->customer->id);
		$this->assertCount( 1, $response->customer->creditCards);
		$this->assertEquals($parameters[PayUParameters::INSTALLMENTS_NUMBER], $response->installments);
		$this->assertEquals($parameters[PayUParameters::QUANTITY], $response->quantity);
	}
	
	/**
	 * test to create a subscription with an existing plan
	 */
	public function testCreateSubscriptionWithExistingPlan(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
				
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);		
		
		// Plan parameters
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan($parameters));
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);

		$response = PayUSubscriptions::createSubscription($parameters);
		
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
		$this->assertNotNull($response->customer->id);
		$this->assertCount( 1, $response->customer->creditCards);
		$this->assertEquals($parameters[PayUParameters::INSTALLMENTS_NUMBER], $response->installments);
		$this->assertEquals($parameters[PayUParameters::QUANTITY], $response->quantity);
	}
	
	/**
	 * test to create a subscription with a new plan
	 */
	public function  testCreateSubscriptionNewPlan() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
			
		// Customer parameters
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
	
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		
		// Credit card parameters
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCard = PayUCreditCards::create(PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams));
		
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::TOKEN_ID] = $creditCard->token;
		

		$response = PayUSubscriptions::createSubscription($parameters);
		
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertNotNull($response->plan->id);
		$this->assertNotNull($response->customer->id);
		$this->assertCount( 1, $response->customer->creditCards);
		$this->assertEquals($parameters[PayUParameters::INSTALLMENTS_NUMBER], $response->installments);
		$this->assertEquals($parameters[PayUParameters::QUANTITY], $response->quantity);
		
	}
	
	/**
	 * test to create a subscription with a PayerId Invalid
	 * @expectedException PayUException
	 */
	public function  testCreateSubscriptionWithPayerIdInvalid() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
			
		// Customer parameters
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
	
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
	
		// Credit card parameters
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCard = PayUCreditCards::create(PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams));
	
		$parameters[PayUParameters::CUSTOMER_ID] = 'Payer_id_invalid';
		$parameters[PayUParameters::TOKEN_ID] = $creditCard->token;
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
	}
	
	/**
	 * test to create a subscription with other token for the PayerId of the subscription
	 * @expectedException PayUException
	 */
	public function  testCreateSubscriptionWithDifferentTokenForPayerIdOfSubscription() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
			
		// Customer parameters
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$customer2 = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
	
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
	
		// Credit card parameters
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer2->id);
		$creditCard = PayUCreditCards::create(PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams));
	
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::TOKEN_ID] = $creditCard->token;
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
	}
	
	/**
	 * test to create a subscription with token for the PayerId of the subscription
	 */
	public function  testCreateSubscriptionWithTokenForPayerIdOfSubscription() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		Environment::setPaymentsCustomUrl(PayUTestUtil::PAYMENTS_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
			
		// Customer parameters
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
			
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
	
		// Credit card parameters
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCard = PayUCreditCards::create(PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams));
	
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::TOKEN_ID] = $creditCard->token;
	
		$response = PayUSubscriptions::createSubscription($parameters);
		
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertNotNull($response->plan->id);
		$this->assertNotNull($response->customer->id);
		$this->assertCount( 1, $response->customer->creditCards);
		$this->assertEquals($parameters[PayUParameters::INSTALLMENTS_NUMBER], $response->installments);
		$this->assertEquals($parameters[PayUParameters::QUANTITY], $response->quantity);
	
	}
	
	
	
	/**
	 * test to get a customer
	 */
	public function testGetCustomer(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		
		
		$parameters = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$response = PayUCustomers::find($parameters);
		
		$this->assertEquals($response->id, $customer->id);
		$this->assertEquals($response->fullName, $customer->fullName);
		$this->assertEquals($response->email, $customer->email);
	}
	
	/**
	 * test to get a customer with invalid customerId
	 * @expectedException PayUException
	 */
	public function testGetCustomerWithInvalidCustomerId(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters[PayUParameters::CUSTOMER_ID] = 'Payer_zXXXXXX111';
		
		$response = PayUCustomers::find($parameters);
	
	}
	
	/**
	 * test to get a credit card
	 */
	public function testGetCreditCard(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCardParams = PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams);
		$creditCard = PayUCreditCards::create($creditCardParams);
		
	
		$parameters = array(PayUParameters::TOKEN_ID => $creditCard->token);
		$response = PayUCreditCards::find($parameters);
	
		$this->assertEquals($creditCard->token, $response->token);
		$this->assertEquals($creditCardParams[PayUParameters::CUSTOMER_ID], $response->customerId);
		$this->assertEquals($creditCardParams[PayUParameters::PAYMENT_METHOD], $response->type);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_NAME], $response->name);
	}
	
	
	/**
	 * test to get a customer credit card List
	 */
	public function testGetCustomerCreditCardList(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		//Create a customer
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$this->assertNotNull($customer);
		$this->assertNotNull($customer->id);
		
		//Prepare Credit Card Parameters
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		
		//Create 3 credit cards to customer
		for ($i = 1; $i <= 3; $i++) {
			$creditCardParams = PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams, 
					array(PayUParameters::CREDIT_CARD_EXPIRATION_DATE => '2030/0'.$i));
			$creditCard = PayUCreditCards::create($creditCardParams);
			
			$this->assertNotNull($creditCard);
			$this->assertNotNull($creditCard->token);
			$tokens[$i-1] = $creditCard->token;
		}
	
		//Find the credit Cards
		$parameters = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$response = PayUCreditCards::findList($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->creditCardList);
		$this->assertCount(3,$response->creditCardList);
		
		//Evaluate credit cards found
		for ($i = 0; $i < count($response->creditCardList); $i++) {
			$creditCardSelected = $response->creditCardList[$i];
			$this->assertTrue(in_array($creditCardSelected->token, $tokens));
			$this->assertNotNull($creditCardSelected->customerId);
			$this->assertEquals($customer->id, $creditCardSelected->customerId);
			$this->assertNotNull($creditCardSelected->number);
			$this->assertNotNull($creditCardSelected->type);
			$this->assertNotNull($creditCardSelected->name);
			$this->assertNotNull($creditCardSelected->address);
		}
	}
	
	/**
	 * test to get a customer without credit cards
	 * @expectedException PayUException
	 */
	public function testGetCustomerWithOutCreditCards(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		//Create a customer
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$this->assertNotNull($customer);
		$this->assertNotNull($customer->id);

		//Find the credit Cards
		$parameters = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$response = PayUCreditCards::findList($parameters);

	}
	
	/**
	 * test to get a credit Cards with invalid customer id
	 * @expectedException PayUException
	 */
	public function testGetCreditCardsWithInvalidCustomer(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		//Find the credit Cards
		$parameters = array(PayUParameters::CUSTOMER_ID => "INVALID_CUSTOMER_ID");
		$response = PayUCreditCards::findList($parameters);
	
	}
	
	/**
	 * test to get a credit Cards with out customer id
	 * @expectedException InvalidArgumentException
	 */
	public function testGetCreditCardsWithOutCustomerId(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array(PayUParameters::ACCOUNT_ID => "INVALID_PARAMETER");
		$response = PayUCreditCards::findList($parameters);
	
	}
	
	/**
	 * test to get a credit card with token invalid
	 * @expectedException PayUException
	 */
	public function testGetCreditCardWithTokenInvalid(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCardParams = PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams);
		$creditCard = PayUCreditCards::create($creditCardParams);
	
	
		$parameters[PayUParameters::TOKEN_ID]= '021212465-121321321-45465465-AD';
		$response = PayUCreditCards::find($parameters);
	
	}
	
	/**
	 * test to get a credit card with token empty
	 * @expectedException InvalidArgumentException
	 */
	public function testGetCreditCardWithTokenEmpty(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCardParams = PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams);
		$creditCard = PayUCreditCards::create($creditCardParams);
	
	
		$parameters[PayUParameters::TOKEN_ID]= '';
		$response = PayUCreditCards::find($parameters);
	
	}
	
	
	
	/**
	 * test to get a plan
	 */
	public function testGetPlan(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$planParameters = PayUTestUtil::buildSuccessParametersPlan();
		$plan = PayUSubscriptionPlans::create($planParameters);
		
		$parameters = array(PayUParameters::PLAN_CODE => $plan->planCode);
		$response = PayUSubscriptionPlans::find($parameters);
	
		$this->assertEquals($plan->planCode, $response->planCode);
		$this->assertEquals($plan->description, $response->description);
		$this->assertEquals($plan->interval, $response->interval);
		$this->assertEquals($plan->maxPendingPayments, $response->maxPendingPayments);
		$this->assertEquals($plan->maxPaymentAttempts, $response->maxPaymentAttempts);
		$this->assertEquals($plan->maxPendingPayments, $response->maxPendingPayments);
	}
	
	
	/**
	 * test to list a plan by merchant
	 * using offset parameter
	 */
	public function testListPlanByMerchant(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		
		$parameters = array(PayUParameters::LIMIT=>3,PayUParameters::OFFSET=>5);
		$response = PayUSubscriptionPlans::listPlans($parameters);
	
		$this->assertNotNull($response->subscriptionPlanList);
		$this->assertCount(3, $response->subscriptionPlanList);
		$firstPlanFound = $response->subscriptionPlanList[0];
		
		$this->assertNotNull($firstPlanFound->id);
		$this->assertNotNull($firstPlanFound->planCode);
		$this->assertNotNull($firstPlanFound->description);
		
	}
	
	
	/**
	 * test to list a plan by merchant without offset
	 */
	public function testListPlanByMerchantWithoutOffset(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
	
		$parameters = array(PayUParameters::LIMIT=>5);
		$response = PayUSubscriptionPlans::listPlans($parameters);
	
		$this->assertNotNull($response->subscriptionPlanList);
		$this->assertCount(5, $response->subscriptionPlanList);
		$firstPlanFound = $response->subscriptionPlanList[0];
	
		$this->assertNotNull($firstPlanFound->id);
		$this->assertNotNull($firstPlanFound->planCode);
		$this->assertNotNull($firstPlanFound->description);
	
	}
	
	

	/**
	 * test to list a plan by account id
	 */
	public function testListPlanByAccount(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		
		$parameters = array(PayUParameters::ACCOUNT_ID => 1, 
							PayUParameters::LIMIT=>5 , 
							PayUParameters::OFFSET=>0);
		
		$response = PayUSubscriptionPlans::listPlans($parameters);
	
		$this->assertNotNull($response->subscriptionPlanList);
		$this->assertCount(5, $response->subscriptionPlanList);
		$firstPlanFound = $response->subscriptionPlanList[0];
		
		$this->assertNotNull($firstPlanFound->id);
		$this->assertNotNull($firstPlanFound->planCode);
		$this->assertNotNull($firstPlanFound->description);
	}
	
	
	
	/**
	 * test to get a plan with codePlan Invalid
	 * @expectedException PayUException
	 */
	public function testGetPlanWithCodePlanInvalid(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$planParameters = PayUTestUtil::buildSuccessParametersPlan();
		$plan = PayUSubscriptionPlans::create($planParameters);
	
		$parameters = array(PayUParameters::PLAN_CODE => $plan->planCode);
		$parameters[PayUParameters::PLAN_CODE] = 'PlanCode_Invalid';
		$response = PayUSubscriptionPlans::find($parameters);
	
	}
	
	
	/**
	 * test to update a credit card
	 */
	public function testUpdateCreditCard(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCardParams = PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams);
		$creditCard = PayUCreditCards::create($creditCardParams);
		
		$creditCardParams = array(
				PayUParameters::TOKEN_ID => $creditCard->token,
				PayUParameters::PAYER_NAME => 'Updated Payer test name' ,
				PayUParameters::PAYER_STREET => 'Updated Street 100',
				PayUParameters::PAYER_STREET_2 => 'Updated Street 9',
				PayUParameters::PAYER_STREET_3 => 'Updated Street 18',
				PayUParameters::PAYER_CITY => 'Updated City',
				PayUParameters::PAYER_STATE => 'Updated State',
				PayUParameters::PAYER_COUNTRY => PayUCountries::CO,
				PayUParameters::PAYER_POSTAL_CODE => 'Updated 12345',
				PayUParameters::PAYER_PHONE => '987654321',
		);
		
		$creditCard = PayUCreditCards::update($creditCardParams);
		
		$parameters = array(PayUParameters::TOKEN_ID => $creditCard->token);
		$creditCardUpdated = PayUCreditCards::find($parameters);
		
		
		$this->assertEquals($creditCard->token, $creditCardUpdated->token);
		$this->assertEquals($customer->id, $creditCardUpdated->customerId);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_NAME], $creditCardUpdated->name);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_STREET], $creditCardUpdated->address->line1);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_STREET_2], $creditCardUpdated->address->line2);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_STREET_3], $creditCardUpdated->address->line3);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_CITY], $creditCardUpdated->address->city);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_STATE], $creditCardUpdated->address->state);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_POSTAL_CODE], $creditCardUpdated->address->postalCode);		
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_PHONE], $creditCardUpdated->address->phone);		
		
	}
	
	/**
	 * test to update a credit card
	 */
	public function testUpdateCreditCardDocument(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCardParams = PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams);
		$creditCardParams[PayUParameters::CREDIT_CARD_DOCUMENT] = '50505050505050';
		$creditCard = PayUCreditCards::create($creditCardParams);
	
		$creditCardParams = array(
				PayUParameters::TOKEN_ID => $creditCard->token,
				PayUParameters::PAYER_NAME => 'Updated Payer test name' ,
				PayUParameters::PAYER_STREET => 'Updated Street 100',
				PayUParameters::PAYER_STREET_2 => 'Updated Street 9',
				PayUParameters::PAYER_STREET_3 => 'Updated Street 18',
				PayUParameters::PAYER_CITY => 'Updated City',
				PayUParameters::PAYER_STATE => 'Updated State',
				PayUParameters::PAYER_COUNTRY => PayUCountries::CO,
				PayUParameters::PAYER_POSTAL_CODE => 'Updated 12345',
				PayUParameters::PAYER_PHONE => '987654321',
				PayUParameters::CREDIT_CARD_DOCUMENT => '01010101010101',
		);
	
		$creditCard = PayUCreditCards::update($creditCardParams);
	
		$parameters = array(PayUParameters::TOKEN_ID => $creditCard->token);
		$creditCardUpdated = PayUCreditCards::find($parameters);
	
	
		$this->assertEquals($creditCard->token, $creditCardUpdated->token);
		$this->assertEquals($customer->id, $creditCardUpdated->customerId);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_NAME], $creditCardUpdated->name);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_STREET], $creditCardUpdated->address->line1);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_STREET_2], $creditCardUpdated->address->line2);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_STREET_3], $creditCardUpdated->address->line3);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_CITY], $creditCardUpdated->address->city);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_STATE], $creditCardUpdated->address->state);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_POSTAL_CODE], $creditCardUpdated->address->postalCode);
		$this->assertEquals($creditCardParams[PayUParameters::PAYER_PHONE], $creditCardUpdated->address->phone);
	
	}	

	/**
	 * test to update a customer
	 */
	public function testUpdateCustomer(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		
		$customerParameters = array(
			PayUParameters::CUSTOMER_ID => $customer->id,
			PayUParameters::CUSTOMER_NAME => 'Updated Test Test',
			PayUParameters::CUSTOMER_EMAIL => 'updatedTest@test.com'
		);
		
		
		$customer = PayUCustomers::update($customerParameters);
		$parametersFind = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$customerUpdated = PayUCustomers::find($parametersFind);
		
		$this->assertEquals($customer->id, $customerUpdated->id);
		$this->assertEquals($customer->fullName, $customerUpdated->fullName);
		$this->assertEquals($customer->email, $customerUpdated->email);
		
	}
	
	/**
	 * test to update a plan
	 */
	public function testUpdatePlan(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$parameters = array();
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		$plan = PayUSubscriptionPlans::create($parameters);
		
		
		$planCode = $plan->planCode;
		
		
		$parameters = array(
				PayUParameters::PLAN_DESCRIPTION => 'Updated'.$plan->description,
				PayUParameters::PLAN_CODE => $plan->planCode,
				PayUParameters::PLAN_CURRENCY => 'COP',
				PayUParameters::PLAN_VALUE => $plan->additionalValues[0]->value + 50000,
				PayUParameters::PLAN_ATTEMPTS_DELAY => '3',
		);
		
		
		$plan = PayUSubscriptionPlans::update($parameters);
		
		$parameters = array(PayUParameters::PLAN_CODE => $planCode);
		$planUpdated = PayUSubscriptionPlans::find($parameters);
		
		$this->assertEquals($planCode, $planUpdated->planCode);
		$this->assertEquals($plan->description, $planUpdated->description);
		$this->assertEquals($plan->interval, $planUpdated->interval);
		$this->assertEquals($plan->intervalCount, $planUpdated->intervalCount);
		$this->assertEquals($plan->additionalValues[0]->value, $planUpdated->additionalValues[0]->value);
		$this->assertEquals($plan->paymentAttemptsDelay, $planUpdated->paymentAttemptsDelay);
		$this->assertEquals($plan->maxPaymentsAllowed, $planUpdated->maxPaymentsAllowed);
		
	}
	
	/**
	 * test to update a plan with planCode Invalid
	 * @expectedException PayUException
	 */
	public function testUpdatePlanWithPlanCodeInvalid(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		$plan = PayUSubscriptionPlans::create($parameters);
	
	
		$planCode = $plan->planCode;
	
	
		$parameters = array(
				PayUParameters::PLAN_DESCRIPTION => 'Updated'.$plan->description,
				PayUParameters::PLAN_CODE => $plan->planCode,
				PayUParameters::PLAN_CURRENCY => 'COP',
				PayUParameters::PLAN_VALUE => $plan->additionalValues[0]->value + 50000,
				PayUParameters::PLAN_ATTEMPTS_DELAY => '3',
		);
	
		$parameters[PayUParameters::PLAN_CODE] = 'Plan_Code_Invalid';
	
		$plan = PayUSubscriptionPlans::update($parameters);
		
	}
	
	/**
	 * test to update a plan with planCode Empty
	 * @expectedException InvalidArgumentException
	 */
	public function testUpdatePlanWithPlanCodeEmpty(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		$plan = PayUSubscriptionPlans::create($parameters);
	
	
		$planCode = $plan->planCode;
	
	
		$parameters = array(
				PayUParameters::PLAN_DESCRIPTION => 'Updated'.$plan->description,
				PayUParameters::PLAN_CODE => $plan->planCode,
				PayUParameters::PLAN_CURRENCY => 'COP',
				PayUParameters::PLAN_VALUE => $plan->additionalValues[0]->value + 50000,
				PayUParameters::PLAN_ATTEMPTS_DELAY => '3',
		);
	
		$parameters[PayUParameters::PLAN_CODE] = '';
	
		$plan = PayUSubscriptionPlans::update($parameters);
	
	}
	
	/**
	 * test to delete a plan
	 */
	public function testDeletePlan(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan());
		
		$parameters = array(PayUParameters::PLAN_CODE => $plan->planCode);

		$response = PayUSubscriptionPlans::delete($parameters);
		
		$this->assertTrue($response);
	}
	
	/**
	 * test to delete a plan with planCode Deleted
	 * @expectedException PayUException
	 */
	public function testDeletePlanWithPlanCodeDeleted(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan());
	
		$parameters = array(PayUParameters::PLAN_CODE => $plan->planCode);
	
		$response = PayUSubscriptionPlans::delete($parameters);
		
		$response = PayUSubscriptionPlans::delete($parameters);
	
	}
	

	/**
	 * test to delete a customer
	 */
	public function testDeleteCustomer(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters = array(PayUParameters::CUSTOMER_ID => $customer->id);
		
		$response = PayUCustomers::delete($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->description);
		
	}
	
	/**
	 * test to delete a customer with customerId deleted
	 * @expectedException PayUException
	 */
	public function testDeleteCustomerWithCustomerIdDeleted(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters = array(PayUParameters::CUSTOMER_ID => $customer->id);
	
		$response = PayUCustomers::delete($parameters);
		$response = PayUCustomers::delete($parameters);
		
	}
	
	
	
	/**
	 * test to delete a credit card token
	 */
	public function testDeleteCreditCardToken(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$creditCard = PayUCreditCards::create($parameters);
		
		$parameters = array(
				PayUParameters::TOKEN_ID => $creditCard->token,
				PayUParameters::CUSTOMER_ID => $customer->id
		);

		$response = PayUCreditCards::delete($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->description);
	}
	
	/**
	 * test to delete a credit card token with PayerId Deleted
	 * @expectedException PayUException
	 */
	public function testDeleteCreditCardTokenWithPayerIdDeleted(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;		
		$creditCard = PayUCreditCards::create($parameters);
		$response = PayUCustomers::delete($parameters);
			
		$parameters = array(
				PayUParameters::TOKEN_ID => $creditCard->token,
				PayUParameters::CUSTOMER_ID => $customer->id
		);
		
		$response = PayUCreditCards::delete($parameters);
	
		$this->assertNotNull($response);
		$this->assertNotNull($response->description);
	}
	
	
	
	/**
	 * test to cancel a subscription
	 */
	public function testCancelSubscription(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);

		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		
		$response = PayUSubscriptions::createSubscription($parameters);
		
		$parameters = array(PayUParameters::SUBSCRIPTION_ID => $response->id);
		
		$response = PayUSubscriptions::cancel($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->description);
		
	}
	
	/**
	 * test to cancel a subscription whith subscription canceled
	 * @expectedException PayUException
	 */
	public function testCancelSubscriptionWithSubscriptionCanceled(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$parameters = array(PayUParameters::SUBSCRIPTION_ID => $response->id);
	
		$response = PayUSubscriptions::cancel($parameters);
		$response = PayUSubscriptions::cancel($parameters);
	
	}	
	
	/**
	 * test to create a recurring bill item
	 */
	public function testCreateRecurringBillItem(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);

		$subscription = PayUSubscriptions::createSubscription($parameters);
		
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id));
		
		$recurrentBillItem = PayURecurringBillItem::create($parameters);
	
		$this->assertNotNull($recurrentBillItem);
		$this->assertNotNull($recurrentBillItem->description);
		$this->assertNotNull($recurrentBillItem->additionalValues);
		$this->assertCount(3,$recurrentBillItem->additionalValues);
		$this->assertNotNull($recurrentBillItem->subscriptionId);
	
	}
	
	/**
	 * test to create a recurring bill item discount
	 */
	public function testCreateRecurringBillItemDiscount(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
		$parameters[PayUParameters::ITEM_VALUE] = '-4000';
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id), $parameters);
		
		unset($parameters[PayUParameters::ITEM_TAX]);
		unset($parameters[PayUParameters::ITEM_TAX_RETURN_BASE]);
		
		$recurrentBillItem = PayURecurringBillItem::create($parameters);
	
		$this->assertNotNull($recurrentBillItem);
		$this->assertNotNull($recurrentBillItem->description);
		$this->assertNotNull($recurrentBillItem->additionalValues);
		$this->assertNotNull($recurrentBillItem->subscriptionId);
	
	}
	
	
	/**
	 * test to deleted a recurring bill item discount
	 */
	public function testDeletedRecurringBillItemDiscount(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
		$parameters[PayUParameters::ITEM_VALUE] = '-4000';
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id));
		$recurrentBillItem = PayURecurringBillItem::create($parameters);
		unset($parameters[PayUParameters::ITEM_TAX]);
		unset($parameters[PayUParameters::ITEM_TAX_RETURN_BASE]);
		
		$parameters = array(PayUParameters::RECURRING_BILL_ITEM_ID => $recurrentBillItem->id, $parameters);
		
		$response = PayURecurringBillItem::delete($parameters);
	
		$this->assertNotNull($recurrentBillItem);
		$this->assertNotNull($recurrentBillItem->description);
		$this->assertNotNull($recurrentBillItem->additionalValues);
		$this->assertCount(3,$recurrentBillItem->additionalValues);
		$this->assertNotNull($recurrentBillItem->subscriptionId);
	
	}
	
	/**
	 * test to create a recurring bill item discount with item tax and item tax return base
	 * @expectedException PayUException
	 */
	public function testValidationMessageCreateRecurringBillItemDiscount(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
	
		$parameters[PayUParameters::ITEM_VALUE] = '-4000';
		$parameters[PayUParameters::ITEM_TAX] = '600';
		$parameters[PayUParameters::ITEM_TAX_RETURN_BASE] = '400';
	
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id), $parameters);
	
		$recurrentBillItem = PayURecurringBillItem::create($parameters);
	
	}
	
	/**
	 * test to get recurring bill item by id
	 */
	public function testGetRecurringBillItemById(){
		
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
	
		$description = 'Test find recurringBillItem '.$subscription->id;
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id),
																	array(PayUParameters::DESCRIPTION => $description));
		$recurrentBillItemCreated = PayURecurringBillItem::create($parameters);
		
		$parameters = array(PayUParameters::RECURRING_BILL_ITEM_ID => $recurrentBillItemCreated->id);
		
		$recurrentBillItemFound = PayURecurringBillItem::find($parameters);
	
		$this->assertEquals($recurrentBillItemCreated->id, $recurrentBillItemFound->id);
		$this->assertEquals($recurrentBillItemCreated->description, $recurrentBillItemFound->description);
		$this->assertEquals(count($recurrentBillItemCreated->additionalValues), count($recurrentBillItemFound->additionalValues));
		$this->assertEquals($recurrentBillItemCreated->subscriptionId, $recurrentBillItemFound->subscriptionId);
		$this->assertEquals($recurrentBillItemCreated->recurringBillId, $recurrentBillItemFound->recurringBillId);
	}
	
	/**
	* test to get recurring bill item without id
	* @expectedException InvalidArgumentException
	*/
	public function testGetRecurringBillItemWithoutId(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);

		$parameters = array(PayUParameters::ACCOUNT_ID => "INCORRECT_PARAMETER");
	
		$recurrentBillItemFound = PayURecurringBillItem::find($parameters);
	}
	
	/**
	 * test to get recurring bill item with invalid id
     * @expectedException PayUException
	 */
	public function testGetRecurringBillItemWithInvalidId(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array(PayUParameters::RECURRING_BILL_ITEM_ID => "INCORRECT_BILL_ITEM_ID");
	
		$recurrentBillItemFound = PayURecurringBillItem::find($parameters);
	}
	
	

	/**
	 * test to get recurring bill item list by subscription id
	 */
	public function testGetRecurringBillItemListBySubscriptionId(){
		
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
	
		$description = 'Test description';
		
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id),
				array(PayUParameters::DESCRIPTION => $description));
		
		$recurrentBillItemsCreated = array();
		//Create 3 Bill Items
		for ($i = 1; $i <= 3; $i++) {
			$recurrentBillItem = PayURecurringBillItem::create($parameters);
			$this->assertNotNull($recurrentBillItem);
			$this->assertNotNull($recurrentBillItem->id);
			array_push($recurrentBillItemsCreated, $recurrentBillItem->id);
		}
	
		
		$parameters = array(PayUParameters::SUBSCRIPTION_ID => $subscription->id);
		$response = PayURecurringBillItem::findList($parameters);
	
		$this->assertNotNull($response);
		$this->assertNotNull($response->recurringBillItemList);
		$this->assertCount(4,$response->recurringBillItemList);
		
		//Evaluate recurring bill items found
		for ($i = 1; $i < count($response->recurringBillItemList); $i++) {
			$recurringBillItemSelected = $response->recurringBillItemList[$i];
			$this->assertTrue(in_array($recurringBillItemSelected->id, $recurrentBillItemsCreated));
			$this->assertNotNull($recurringBillItemSelected->subscriptionId);
			$this->assertEquals($subscription->id, $recurringBillItemSelected->subscriptionId);
			$this->assertEquals($description, $recurringBillItemSelected->description);
		}
	}
	
	/**
	 * test to get recurring bill item with invalid subscription id
	 * @expectedException PayUException
	 */
	public function testGetRecurringBillItemListWithInvalidSubscriptionId(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array(PayUParameters::SUBSCRIPTION_ID => "INCORRECT_SUBSCRIPTION_ID");
	
		$recurrentBillItemFound = PayURecurringBillItem::findList($parameters);
	}
	
	/**
	 * test to get recurring bill item list by description
	 */
	public function testGetRecurringBillItemListByDescription(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
	
		$description = $subscription->id . ' Test description';
	
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id),
				array(PayUParameters::DESCRIPTION => $description));
	
		$recurrentBillItemsCreated = array();
		//Create 3 Bill Items
		for ($i = 1; $i <= 3; $i++) {
			$recurrentBillItem = PayURecurringBillItem::create($parameters);
			$this->assertNotNull($recurrentBillItem);
			$this->assertNotNull($recurrentBillItem->id);
			array_push($recurrentBillItemsCreated, $recurrentBillItem->id);
		}
	
	
		$parameters = array(PayUParameters::DESCRIPTION => $description);
		$response = PayURecurringBillItem::findList($parameters);
	
		$this->assertNotNull($response);
		$this->assertNotNull($response->recurringBillItemList);
		$this->assertCount(3,$response->recurringBillItemList);
	
		//Evaluate recurring bill items found
		for ($i = 0; $i < count($response->recurringBillItemList); $i++) {
			$recurringBillItemSelected = $response->recurringBillItemList[$i];
			$this->assertTrue(in_array($recurringBillItemSelected->id, $recurrentBillItemsCreated));
			$this->assertNotNull($recurringBillItemSelected->subscriptionId);
			$this->assertEquals($subscription->id, $recurringBillItemSelected->subscriptionId);
			$this->assertEquals($description, $recurringBillItemSelected->description);
		}
	}
	
	/**
	 * test to get recurring bill item list by description and subscription id
	 */
	public function testGetRecurringBillItemListByDescriptionAndSubscriptionId(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
	
		$description = $subscription->id . ' Test description';
	
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id),
				array(PayUParameters::DESCRIPTION => $description));
	
		$recurrentBillItemsCreated = array();
		//Create 3 Bill Items
		for ($i = 1; $i <= 3; $i++) {
			$recurrentBillItem = PayURecurringBillItem::create($parameters);
			$this->assertNotNull($recurrentBillItem);
			$this->assertNotNull($recurrentBillItem->id);
			array_push($recurrentBillItemsCreated, $recurrentBillItem->id);
		}
	
	
		$parameters = array(PayUParameters::DESCRIPTION => $description, PayUParameters::SUBSCRIPTION_ID => $subscription->id);
		$response = PayURecurringBillItem::findList($parameters);
	
		$this->assertNotNull($response);
		$this->assertNotNull($response->recurringBillItemList);
		$this->assertCount(3,$response->recurringBillItemList);
	
		//Evaluate recurring bill items found
		for ($i = 0; $i < count($response->recurringBillItemList); $i++) {
			$recurringBillItemSelected = $response->recurringBillItemList[$i];
			$this->assertTrue(in_array($recurringBillItemSelected->id, $recurrentBillItemsCreated));
			$this->assertNotNull($recurringBillItemSelected->subscriptionId);
			$this->assertEquals($subscription->id, $recurringBillItemSelected->subscriptionId);
			$this->assertEquals($description, $recurringBillItemSelected->description);
		}
	}
	
	
	/**
	 * test to get recurring bill item list by description
	 * @expectedException InvalidArgumentException
	 */
	public function testGetRecurringBillItemListWithOutDescriptionOrSubcriptionId(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$parameters = array();
		$response = PayURecurringBillItem::findList($parameters);
	}
	
	
	/**
	 * test to get recurring bill item whith recurring bill item deleted
	 * @expectedException PayUException
	 */
	public function testGetRecurringBillItemWithRecurringbillItemDeleted(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
	
		$description = 'Test find recurringBillItem '.$subscription->id;
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id),
				array(PayUParameters::DESCRIPTION => $description));
		$recurrentBillItem = PayURecurringBillItem::create($parameters);
	
		$parameters = array(PayUParameters::RECURRING_BILL_ITEM_ID => $recurrentBillItem->id);
	
		$recurrentBillItemFound = PayURecurringBillItem::delete($parameters);
		
		$recurrentBillItemFound = PayURecurringBillItem::find($parameters);
	
	}
	
	/**
	 * test to update a recurring bill item
	 */
	public function testUpdateRecurringBillItem(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$subscription = PayUSubscriptions::createSubscription($parameters);
	
		$description = 'Test find recurringBillItem '.$subscription->id;
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id),
				array(PayUParameters::DESCRIPTION => $description));
		$recurrentBillItem = PayURecurringBillItem::create($parameters);
		
		
		$parameters = array(
				PayUParameters::RECURRING_BILL_ITEM_ID => $recurrentBillItem->id,
				PayUParameters::SUBSCRIPTION_ID => $subscription->id,
				PayUParameters::DESCRIPTION => 'Updated Test Item',
				PayUParameters::ITEM_VALUE => '6000',
				PayUParameters::CURRENCY => 'COP',
				PayUParameters::ITEM_TAX => '2000',
				PayUParameters::ITEM_TAX_RETURN_BASE => '200'
		);
		
		$recurrentBillItem = PayURecurringBillItem::update($parameters);
		$parameters = array(PayUParameters::RECURRING_BILL_ITEM_ID => $recurrentBillItem->id);
		$recurrentBillItemUpdated = PayURecurringBillItem::find($parameters);

		$this->assertEquals($recurrentBillItem->id, $recurrentBillItemUpdated->id);
		$this->assertEquals($recurrentBillItem->description, $recurrentBillItemUpdated->description);
		$this->assertEquals(count($recurrentBillItem->additionalValues), count($recurrentBillItemUpdated->additionalValues));
		$this->assertEquals($recurrentBillItem->subscriptionId, $recurrentBillItemUpdated->subscriptionId);
		
		$this->assertEquals($recurrentBillItem->additionalValues[0]->value, $recurrentBillItemUpdated->additionalValues[0]->value);
		$this->assertEquals($recurrentBillItem->additionalValues[1]->value, $recurrentBillItemUpdated->additionalValues[1]->value);
		$this->assertEquals($recurrentBillItem->additionalValues[2]->value, $recurrentBillItemUpdated->additionalValues[2]->value);
	}
	
	
	/**
	 * test to delete a recurring bill item
	 */
	public function testDeleteRecurringBillItem(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$subscription = PayUSubscriptions::createSubscription($parameters);
		$parameters = PayUTestUtil::buildRecurringBillItemParameters(array(PayUParameters::SUBSCRIPTION_ID => $subscription->id));
		$recurrentBillItem = PayURecurringBillItem::create($parameters);

		$parameters = array(PayUParameters::RECURRING_BILL_ITEM_ID => $recurrentBillItem->id);
		$response = PayURecurringBillItem::delete($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->description);
	}
	
	/**
	 * Verifies that fetch only one customer associated to a plan by plan id
	 */
	public function testGetCustomerListByPlanId(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		
		// Plan parameters
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan($parameters));
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$response = PayUSubscriptions::createSubscription($parameters);
		
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
	
	
		$parameters = array(PayUParameters::PLAN_ID => $plan->id);
		$response = PayUCustomers::findCustomerListByPlanIdOrPlanCode($parameters);
		
		$this->assertNotNull($response);
		$this->assertEquals(1, count($response->customerList));
	}
	
	/**
	 * Verifies that fetch only one customer associated to a plan by plan code
	 */
	public function testGetCustomerListByPlanCode(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
	
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
		$expectedFullName = CommonRequestUtil::getParameter($parameters, PayUParameters::CUSTOMER_NAME);
	
		// Plan parameters
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan($parameters));
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
	
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
	
	
		$parameters = array(PayUParameters::PLAN_CODE => $plan->planCode);
		$response = PayUCustomers::findCustomerListByPlanIdOrPlanCode($parameters);
	
		$this->assertNotNull($response);
		$this->assertEquals(1, count($response->customerList));
		
		$customer = $response->customerList[0];
		$this->assertEquals($expectedFullName, $customer->fullName);
		
	}
	
	/**
	 * Checks that the given result brings paginated results 
	 */
	public function testGetCustomerListUsingPagination(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$queryLimit = 2;
		
		$parameters = array(PayUParameters::OFFSET => 0, PayUParameters::LIMIT => $queryLimit);
		$response = PayUCustomers::findCustomerListByPlanIdOrPlanCode($parameters);
	
		$this->assertNotNull($response);
		$this->assertEquals($queryLimit, count($response->customerList));
	}


	/**
	 * test to get a customer with his subscriptions
	 */
	public function testGetCustomerWithSubscription(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
	
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCard = PayUCreditCards::create(PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams));
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::PLAN_CODE] = $plan->planCode;
		$parameters[PayUParameters::TOKEN_ID] = $creditCard->token;
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan());
		$creditCardParams = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$creditCard = PayUCreditCards::create(PayUTestUtil::buildSubscriptionParametersCreditCard($creditCardParams));
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::PLAN_CODE] = $plan->planCode;
		$parameters[PayUParameters::TOKEN_ID] = $creditCard->token;
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$parameters = array(PayUParameters::CUSTOMER_ID => $customer->id);
		$response = PayUCustomers::find($parameters);
	
		$this->assertEquals($response->id, $customer->id);
		$this->assertEquals($response->fullName, $customer->fullName);
		$this->assertEquals($response->email, $customer->email);
	}
	
	/**
	 * test to create a custoemer with bank account
	 */
	public function testCreateCustomerWithBankAccount(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = PayUTestUtil::buildParametersBankAccount();
		$parameters = array_merge($parameters, PayUTestUtil::buildSubscriptionParametersCustomer());
	
		$customer = PayUCustomers::createCustomerWithBankAccount($parameters);
	
		$this->assertNotNull($customer);
		$this->assertNotNull($customer->id);
		$this->assertEquals(1, count($customer->bankAccounts));
		$this->assertEquals($customer->id, $customer->bankAccounts[0]->customerId);
		$this->assertNotNull($customer->bankAccounts[0]->id);
	}
	

	
	
	
	
	
	/**
	 * Gets recurring bill list test
	 */
	public function testGetRecurringBillList() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		
		$startDate = PayUTestUtil::getDateFromToday(PayUConfig::PAYU_DAY_FORMAT, 0);
		$endDate = PayUTestUtil::getDateFromToday(PayUConfig::PAYU_DAY_FORMAT, 1);
		
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		$customer = PayUCustomers::create($parameters);
		
		
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		$parameters[PayUParameters::TRIAL_DAYS] = 0;
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		
		$subscription = PayUSubscriptions::createSubscription($parameters);
		
		$parameters = array();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
 		$parameters[PayUParameters::RECURRING_BILL_DATE_BEGIN] = $startDate;
 		$parameters[PayUParameters::RECURRING_BILL_DATE_FINAL] = $endDate;
		$parameters[PayUParameters::RECURRING_BILL_PAYMENT_METHOD_TYPE] = PayUPaymentMethodType::CREDIT_CARD;
		$parameters[PayUParameters::RECURRING_BILL_STATE] = 'PENDING';
		$parameters[PayUParameters::SUBSCRIPTION_ID] = $subscription->id;
		$parameters[PayUParameters::LIMIT] = '10';
		$parameters[PayUParameters::OFFSET] = '0';
		
		
		$result = PayURecurringBill::listRecurringBills($parameters);
		$this->assertNotNull($result->recurringBillList);
		$this->assertGreaterThan(0, $result->recurringBillList);
		
		$recurringBillFound = $result->recurringBillList[0];
		$this->assertNotNull($recurringBillFound->id);
		$this->assertEquals($subscription->id, $recurringBillFound->subscriptionId);
		$this->assertNotNull($recurringBillFound->state);
		$this->assertNotNull($recurringBillFound->amount);
		$this->assertNotNull($recurringBillFound->currency);
		$this->assertNotNull($recurringBillFound->dateCharge);
		
	}
	
	/**
	 * test to query recurring bills without filters
	 */
	public function testGetRecurringBillListWithoutFilters(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array(PayUParameters::OFFSET=>0, PayUParameters::LIMIT=>5);
		$result = PayURecurringBill::listRecurringBills($parameters);
		
		$this->assertNotNull($result->recurringBillList);
		$this->assertGreaterThan(0, $result->recurringBillList);
		
	}
	
	
	/**
	 * test to query recurring bills without filters
	 */
	public function testGetRecurringBillListPending(){
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		$parameters = array (
				PayUParameters::OFFSET => 0,
				PayUParameters::LIMIT => 5,
				PayUParameters::RECURRING_BILL_STATE => 'PENDING' 
		);
		
		$result = PayURecurringBill::listRecurringBills($parameters);
	
		$this->assertNotNull($result->recurringBillList);
		$this->assertGreaterThan(0, $result->recurringBillList);
		
		foreach($result->recurringBillList as $recurringBill){
			$this->assertEquals($parameters[PayUParameters::RECURRING_BILL_STATE],$recurringBill->state);
		}
	
	}
	
	/**
	 * test to get recurring bill by id
	 */
	public function testGetRecurringBillById(){

		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		
		$startDate = PayUTestUtil::getDateFromToday(PayUConfig::PAYU_DAY_FORMAT, 0);
		$endDate = PayUTestUtil::getDateFromToday(PayUConfig::PAYU_DAY_FORMAT, 1);
		
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer();
		$customer = PayUCustomers::create($parameters);
		
		
		
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
		$parameters[PayUParameters::TRIAL_DAYS] = 0;
		// Plan parameters
		$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
		// Credit card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		
		$subscription = PayUSubscriptions::createSubscription($parameters);
		
		$parameters = array();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::RECURRING_BILL_DATE_BEGIN] = $startDate;
		$parameters[PayUParameters::RECURRING_BILL_DATE_FINAL] = $endDate;
		$parameters[PayUParameters::RECURRING_BILL_PAYMENT_METHOD_TYPE] = PayUPaymentMethodType::CREDIT_CARD;
		$parameters[PayUParameters::RECURRING_BILL_STATE] = 'PENDING';
		$parameters[PayUParameters::SUBSCRIPTION_ID] = $subscription->id;
		$parameters[PayUParameters::LIMIT] = '10';
		$parameters[PayUParameters::OFFSET] = '0';
		
		
		$result = PayURecurringBill::listRecurringBills($parameters);
		$recurringBillCreated = $result->recurringBillList[0]; 
		
		$parameters = array(PayUParameters::RECURRING_BILL_ID => $recurringBillCreated->id);
		$recurringBillFound = PayURecurringBill::find($parameters);
		
		
		$this->assertEquals($recurringBillCreated->id, $recurringBillFound->id);
		$this->assertEquals($recurringBillCreated->subscriptionId, $recurringBillFound->subscriptionId);
		$this->assertEquals($recurringBillCreated->state, $recurringBillFound->state);
		$this->assertEquals($recurringBillCreated->amount, $recurringBillFound->amount);
		$this->assertEquals($recurringBillCreated->currency, $recurringBillFound->currency);
		$this->assertEquals($recurringBillCreated->dateCharge, $recurringBillFound->dateCharge);
	}
	
	
	/**
	 * test to query a recurring bill with invalid id
	 * @expectedException PayUException 
	 */
	public function testGetRecurringBillWithInvalidId(){
		
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		$parameters = array(PayUParameters::RECURRING_BILL_ID => "123abc");
		$recurringBillFound = PayURecurringBill::find($parameters);
		
	}
	
	/**
	 * Create subscription test with existing plan and bank account new
	 */
	public function testCreateSubscriptionNewPlanAndNewBankAccountAndExistingCustomer() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		//Crete the Customer
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
	
		$parameters = array();
		// Plan parameters
		//$parameters = PayUTestUtil::buildSuccessParametersPlan($parameters);
	
		// Subscription parameters
		$parameters[PayUParameters::QUANTITY] = "5";
		$parameters[PayUParameters::INSTALLMENTS_NUMBER] =  "2";
		$parameters[PayUParameters::TRIAL_DAYS] =  "2";
		$parameters[PayUParameters::TERMS_AND_CONDITIONS_ACEPTED] =  "true";
		// Customer parameters
		$parameters[PayUParameters::CUSTOMER_ID] =  $customer->id;
		// Plan parameters
		$parameters[PayUParameters::PLAN_DESCRIPTION] =  "Plan-SDK-PHP-Test";
	
		$now = new DateTime();
		$parameters[PayUParameters::PLAN_CODE] = "Plan-SDK-PHP-" . $now->getTimestamp();
		$parameters[PayUParameters::PLAN_INTERVAL] =  "MONTH";
		$parameters[PayUParameters::PLAN_INTERVAL_COUNT] =  "12";
		$parameters[PayUParameters::PLAN_CURRENCY] =  "BRL";
		$parameters[PayUParameters::PLAN_VALUE] =  "200";
		$parameters[PayUParameters::PLAN_TAX] =  "10";
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE] =  "30";
		$parameters[PayUParameters::ACCOUNT_ID] =  "3";
		$parameters[PayUParameters::PLAN_ATTEMPTS_DELAY] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PAYMENTS] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS] =  "2";
	
		// Bank account parameters
		$parameters[PayUParameters::CUSTOMER_ID] =  $customer->id;
		$parameters[PayUParameters::BANK_ACCOUNT_CUSTOMER_NAME] = "User Test";
		$parameters[PayUParameters::ACCOUNT_ID] =  "3";
		$parameters[PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER] =  "78964874";
		$parameters[PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER_TYPE] = "CNPJ";
		$parameters[PayUParameters::BANK_ACCOUNT_BANK_NAME] =  "SANTANDER";
		$parameters[PayUParameters::BANK_ACCOUNT_TYPE] =  "CURRENT";
		$parameters[PayUParameters::BANK_ACCOUNT_NUMBER] =  "96325891";
		$parameters[PayUParameters::BANK_ACCOUNT_ACCOUNT_DIGIT] =  "2";
		$parameters[PayUParameters::BANK_ACCOUNT_AGENCY_DIGIT] =  "3";
		$parameters[PayUParameters::BANK_ACCOUNT_AGENCY_NUMBER] =  "4518";
		$parameters[PayUParameters::COUNTRY] =  "BR";
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertEquals($parameters[PayUParameters::PLAN_DESCRIPTION], $response->plan->description);
	
		$this->assertNotNull($response->customer->bankAccounts[0]->id);
	
	}	
	
	/**
	 * Create subscription test with new plan, customer and Credit Card
	 */
	public function testCreateSubscriptionNewPlanAndNewCustomerAndNewCreditCard() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		//Crete the Customer
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
	
		// Subscription parameters
		$parameters[PayUParameters::QUANTITY] = "5";
		$parameters[PayUParameters::INSTALLMENTS_NUMBER] =  "2";
		$parameters[PayUParameters::TRIAL_DAYS] =  "2";
	
		// Plan parameters
		$parameters[PayUParameters::ACCOUNT_ID] =  "1";
		$parameters[PayUParameters::PLAN_DESCRIPTION] =  "Plan-SDK-PHP-Test-TC";
		$now = new DateTime();
		$parameters[PayUParameters::PLAN_CODE] = "Plan-SDK-PHP-" . $now->getTimestamp();
		$parameters[PayUParameters::PLAN_INTERVAL] =  "MONTH";
		$parameters[PayUParameters::PLAN_INTERVAL_COUNT] =  "12";
		$parameters[PayUParameters::PLAN_CURRENCY] =  "COP";
		$parameters[PayUParameters::PLAN_VALUE] =  "200";
		$parameters[PayUParameters::PLAN_TAX] =  "10";
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE] =  "30";
		$parameters[PayUParameters::PLAN_ATTEMPTS_DELAY] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PAYMENTS] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS] =  "2";
	
		//Credit Card parameters
		$parameters[PayUParameters::CREDIT_CARD_NUMBER] = '4929577907116575';
		$parameters[PayUParameters::CREDIT_CARD_EXPIRATION_DATE] = '2015/01';
		$parameters[PayUParameters::PAYMENT_METHOD] = 'VISA';
			
		$parameters[PayUParameters::PAYER_NAME] = 'User Credit Card Test Name';
		$parameters[PayUParameters::PAYER_STREET] = 'CALLE 0 # 00-00';
		$parameters[PayUParameters::PAYER_CITY] = 'Arauca';
		$parameters[PayUParameters::PAYER_STATE] = 'Arauca';
		$parameters[PayUParameters::PAYER_COUNTRY] = PayUCountries::CO;
		$parameters[PayUParameters::PAYER_POSTAL_CODE] = '12345';
		$parameters[PayUParameters::PAYER_PHONE] = '123456789';
	
		$response = PayUSubscriptions::createSubscription($parameters);
		$this->subscription = $response;
	
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertEquals($parameters[PayUParameters::PLAN_DESCRIPTION], $response->plan->description);
		$this->assertNotNull($this->subscription->customer->creditCards[0]->token);
	}
	
	/**
	 * Create subscription test with new plan, customer and new bank account
	 */
	public function testCreateSubscriptionNewPlanAndNewCustomerAndNewBankAccount() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		//Crete the Customer
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
	
		// Subscription parameters
		$parameters[PayUParameters::QUANTITY] = "5";
		$parameters[PayUParameters::INSTALLMENTS_NUMBER] =  "2";
		$parameters[PayUParameters::TRIAL_DAYS] =  "2";
	
		// Plan parameters
		$parameters[PayUParameters::PLAN_DESCRIPTION] =  "Plan-SDK-PHP-Test";
		$now = new DateTime();
		$parameters[PayUParameters::PLAN_CODE] = "Plan-SDK-PHP-" . $now->getTimestamp();
		$parameters[PayUParameters::PLAN_INTERVAL] =  "MONTH";
		$parameters[PayUParameters::PLAN_INTERVAL_COUNT] =  "12";
		$parameters[PayUParameters::PLAN_CURRENCY] =  "BRL";
		$parameters[PayUParameters::PLAN_VALUE] =  "200";
		$parameters[PayUParameters::PLAN_TAX] =  "10";
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE] =  "30";
		$parameters[PayUParameters::ACCOUNT_ID] =  "3";
		$parameters[PayUParameters::PLAN_ATTEMPTS_DELAY] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PAYMENTS] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS] =  "2";
	
		// Bank account parameters
		$parameters[PayUParameters::BANK_ACCOUNT_CUSTOMER_NAME] = "User Test";
		$parameters[PayUParameters::ACCOUNT_ID] =  "3";
		$parameters[PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER] =  "78964874";
		$parameters[PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER_TYPE] = "CNPJ";
		$parameters[PayUParameters::BANK_ACCOUNT_BANK_NAME] =  "SANTANDER";
		$parameters[PayUParameters::BANK_ACCOUNT_TYPE] =  "CURRENT";
		$parameters[PayUParameters::BANK_ACCOUNT_NUMBER] =  "96325891";
		$parameters[PayUParameters::BANK_ACCOUNT_ACCOUNT_DIGIT] =  "2";
		$parameters[PayUParameters::BANK_ACCOUNT_AGENCY_DIGIT] =  "3";
		$parameters[PayUParameters::BANK_ACCOUNT_AGENCY_NUMBER] =  "4518";
		$parameters[PayUParameters::COUNTRY] =  "BR";
		$parameters[PayUParameters::TERMS_AND_CONDITIONS_ACEPTED] =  TRUE;
	
		$response = PayUSubscriptions::createSubscription($parameters);
		$this->subscription = $response;
	
		print_r($response);
		$this->assertNotNull($this->subscription);
		$this->assertNotNull($this->subscription->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $this->subscription->plan->planCode);
		$this->assertEquals($parameters[PayUParameters::PLAN_DESCRIPTION], $this->subscription->plan->description);
	
		$this->assertNotNull($this->subscription->customer->bankAccounts[0]->id);
	}
	
	/**
	 * Create subscription test with new plan, customer and new bank account but Terms and Conditions not accepted
	 * @expectedException PayUException
	 */
	public function testCreateSubscriptionNewPlanAndNewCustomerAndNewBankAccountAndTermsConditionsNotAccepted() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		//Crete the Customer
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
	
		// Subscription parameters
		$parameters[PayUParameters::QUANTITY] = "5";
		$parameters[PayUParameters::INSTALLMENTS_NUMBER] =  "2";
		$parameters[PayUParameters::TRIAL_DAYS] =  "2";

		// Plan parameters
		$parameters[PayUParameters::PLAN_DESCRIPTION] =  "Plan-SDK-PHP-Test";
		$now = new DateTime();
		$parameters[PayUParameters::PLAN_CODE] = "Plan-SDK-PHP-" . $now->getTimestamp();
		$parameters[PayUParameters::PLAN_INTERVAL] =  "MONTH";
		$parameters[PayUParameters::PLAN_INTERVAL_COUNT] =  "12";
		$parameters[PayUParameters::PLAN_CURRENCY] =  "BRL";
		$parameters[PayUParameters::PLAN_VALUE] =  "200";
		$parameters[PayUParameters::PLAN_TAX] =  "10";
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE] =  "30";
		$parameters[PayUParameters::ACCOUNT_ID] =  "3";
		$parameters[PayUParameters::PLAN_ATTEMPTS_DELAY] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PAYMENTS] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS] =  "2";
	
		// Bank account parameters
		$parameters[PayUParameters::BANK_ACCOUNT_CUSTOMER_NAME] = "User Test";
		$parameters[PayUParameters::ACCOUNT_ID] =  "3";
		$parameters[PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER] =  "78964874";
		$parameters[PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER_TYPE] = "CNPJ";
		$parameters[PayUParameters::BANK_ACCOUNT_BANK_NAME] =  "SANTANDER";
		$parameters[PayUParameters::BANK_ACCOUNT_TYPE] =  "CURRENT";
		$parameters[PayUParameters::BANK_ACCOUNT_NUMBER] =  "96325891";
		$parameters[PayUParameters::BANK_ACCOUNT_ACCOUNT_DIGIT] =  "2";
		$parameters[PayUParameters::BANK_ACCOUNT_AGENCY_DIGIT] =  "3";
		$parameters[PayUParameters::BANK_ACCOUNT_AGENCY_NUMBER] =  "4518";
		$parameters[PayUParameters::COUNTRY] =  "BR";
		//Terms and Conditions not accepted
		$parameters[PayUParameters::TERMS_AND_CONDITIONS_ACEPTED] =  FALSE;
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertEquals($parameters[PayUParameters::PLAN_DESCRIPTION], $response->plan->description);
	
		$this->assertNotNull($response->customer->bankAccounts[0]->id);
	}	
	
	/**
	 * Create subscription test with new plan, customer and two Payment methods (Bank Account and Credit Card) with error 
	 * @expectedException PayUException
	 */
	public function testCreateSubscriptionNewPlanAndNewCustomerAndTwoPaymentMethods() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		$parameters = array();
		//Crete the Customer
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
	
		// Subscription parameters
		$parameters[PayUParameters::QUANTITY] = "5";
		$parameters[PayUParameters::INSTALLMENTS_NUMBER] =  "2";
		$parameters[PayUParameters::TRIAL_DAYS] =  "2";
	
		// Plan parameters
		$parameters[PayUParameters::PLAN_DESCRIPTION] =  "Plan-SDK-PHP-Test";
		$now = new DateTime();
		$parameters[PayUParameters::PLAN_CODE] = "Plan-SDK-PHP-" . $now->getTimestamp();
		$parameters[PayUParameters::PLAN_INTERVAL] =  "MONTH";
		$parameters[PayUParameters::PLAN_INTERVAL_COUNT] =  "12";
		$parameters[PayUParameters::PLAN_CURRENCY] =  "BRL";
		$parameters[PayUParameters::PLAN_VALUE] =  "200";
		$parameters[PayUParameters::PLAN_TAX] =  "10";
		$parameters[PayUParameters::PLAN_TAX_RETURN_BASE] =  "30";
		$parameters[PayUParameters::ACCOUNT_ID] =  "3";
		$parameters[PayUParameters::PLAN_ATTEMPTS_DELAY] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PAYMENTS] =  "2";
		$parameters[PayUParameters::PLAN_MAX_PENDING_PAYMENTS] =  "2";
	
		//Credit Card parameters
		$parameters[PayUParameters::CREDIT_CARD_NUMBER] = '4929577907116575';
		$parameters[PayUParameters::CREDIT_CARD_EXPIRATION_DATE] = '2015/01';
		$parameters[PayUParameters::PAYMENT_METHOD] = 'VISA';
					
		$parameters[PayUParameters::PAYER_NAME] = 'User Credit Card Test Name';
		$parameters[PayUParameters::PAYER_STREET] = 'CALLE 0 # 00-00';
		$parameters[PayUParameters::PAYER_CITY] = 'Leticia';
		$parameters[PayUParameters::PAYER_STATE] = 'Amazonas';
		$parameters[PayUParameters::PAYER_COUNTRY] = PayUCountries::CO;
		$parameters[PayUParameters::PAYER_POSTAL_CODE] = '12345';
		$parameters[PayUParameters::PAYER_PHONE] = '123456789';
		
		// Bank account parameters
		$parameters[PayUParameters::BANK_ACCOUNT_CUSTOMER_NAME] = "User Bank Account Test Name";
		$parameters[PayUParameters::ACCOUNT_ID] =  "3";
		$parameters[PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER] =  "78964874";
		$parameters[PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER_TYPE] = "CNPJ";
		$parameters[PayUParameters::BANK_ACCOUNT_BANK_NAME] =  "SANTANDER";
		$parameters[PayUParameters::BANK_ACCOUNT_TYPE] =  "CURRENT";
		$parameters[PayUParameters::BANK_ACCOUNT_NUMBER] =  "96325891";
		$parameters[PayUParameters::BANK_ACCOUNT_ACCOUNT_DIGIT] =  "2";
		$parameters[PayUParameters::BANK_ACCOUNT_AGENCY_DIGIT] =  "3";
		$parameters[PayUParameters::BANK_ACCOUNT_AGENCY_NUMBER] =  "4518";
		$parameters[PayUParameters::COUNTRY] =  "BR";
		$parameters[PayUParameters::TERMS_AND_CONDITIONS_ACEPTED] =  TRUE;
	
		$response = PayUSubscriptions::createSubscription($parameters);
	
		$this->assertNotNull($response->id);
		$this->assertEquals($parameters[PayUParameters::PLAN_CODE], $response->plan->planCode);
		$this->assertEquals($parameters[PayUParameters::PLAN_DESCRIPTION], $response->plan->description);
	
		$this->assertNotNull($response->customer->bankAccounts[0]->id);
	}	
	
	/**
	 * Test to update a subscription with other Credit Card.
	 * depends of testCreateSubscriptionNewPlanAndNewCustomerAndNewCreditCard() method 
	 */
	public function testUpdateSubscriptionCreditCard() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		
		//executes the creation Subscription
		$this->testCreateSubscriptionNewPlanAndNewCustomerAndNewCreditCard();
		
		//Update the subscription with the new bank Credit Card Info
		$parameters = array();
		$parameters[PayUParameters::SUBSCRIPTION_ID] = $this->subscription->id;
	
		//Credit Card parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		
		$parameters[PayUParameters::PAYMENT_METHOD] = 'CENCOSUD';
		$parameters[PayUParameters::CREDIT_CARD_NUMBER] = '6034931111111111';
		$parameters[PayUParameters::PAYER_CITY] = 'Meta';
		$parameters[PayUParameters::PAYER_STATE] = 'Villavicencio';
	
		$response = PayUSubscriptions::update($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->creditCardToken);
	}	
	
	/**
	 * Test to update a subscription with Other token
	 * depends of testCreateSubscriptionNewPlanAndNewCustomerAndNewCreditCard() method
	 */
	public function testUpdateSubscriptionWithOtherToken() {
	
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		//executes the creation Subscription. The method set the $this->subscription object
		$this->testCreateSubscriptionNewPlanAndNewCustomerAndNewCreditCard();
		
		$parameters = array();

		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		$parameters[PayUParameters::PAYMENT_METHOD] = 'CENCOSUD';
		$parameters[PayUParameters::CREDIT_CARD_NUMBER] = '6034931111111111';
		$parameters[PayUParameters::PAYER_CITY] = 'Meta';
		$parameters[PayUParameters::PAYER_STATE] = 'Villavicencio';
		//the same customer id of the subscription
		$parameters[PayUParameters::CUSTOMER_ID] = $this->subscription->customer->id;
		//Creates the token
		$creditCardToken = PayUCreditCards::create($parameters);

		//Update the subscription with the new bank Token
		$parameters = array();
		$parameters[PayUParameters::SUBSCRIPTION_ID] = $this->subscription->id;
		$parameters[PayUParameters::TOKEN_ID] = $creditCardToken->token;
		$response = PayUSubscriptions::update($parameters);
	
		$this->assertNotNull($response);
		$this->assertNotNull($response->creditCardToken);
		$this->assertEquals($creditCardToken->token, $response->creditCardToken);
	}	
	
/**
	 * test to update a subscription with other bank account
	 * depends of testCreateSubscriptionNewPlanAndNewCustomerAndNewBankAccount() method 
	 */
	public function testUpdateSubscriptionBankAccount(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		//executes the creation Subscription
		$this->testCreateSubscriptionNewPlanAndNewCustomerAndNewBankAccount();
				
		//Creates other Bank Account. The account is of Brazil, which is active when it is created 
		$parameters = PayUTestUtil::buildParametersBankAccountBrazil();
		
		//the same customer id of the subscription
		$parameters[PayUParameters::CUSTOMER_ID] = $this->subscription->customer->id;
		$newBankAccount = PayUBankAccounts::create($parameters);

// 		print_r(" newBankAccount=> id=". $newBankAccount->id . ", state=" . $newBankAccount->state);
		
		//Update the subscription with the new bank account
		$parameters = array();
		$parameters[PayUParameters::SUBSCRIPTION_ID] = $this->subscription->id;
		$parameters[PayUParameters::BANK_ACCOUNT_ID] = $newBankAccount-> id;
		
// 		print_r(" BankAccount Id Old: " . $this->subscription->customer->bankAccounts[0]->id);
// 		print_r(" BankAccount Account Id New: " . $newBankAccount-> id);
		
		$response = PayUSubscriptions::update($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->bankAccountId);
 		$this->assertEquals($newBankAccount-> id, $response->bankAccountId);
		$this->assertEquals("ACTIVE", $newBankAccount->state);
	}	

	/**
	 * test to update a subscription with other bank account
	 * depends of testCreateSubscriptionNewPlanAndNewCustomerAndNewBankAccount() method
	 * @expectedException  PayUException
	 */
	public function testUpdateSubscriptionBankAccountInactiveAutomaticDebit(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		//executes the creation Subscription
		$this->testCreateSubscriptionNewPlanAndNewCustomerAndNewBankAccount();
	
		//Creates other Bank Account. The account is of Colombia, which is not active when it is created
		$parameters = PayUTestUtil::buildParametersBankAccount();
	
		//the same customer id of the subscription
		$parameters[PayUParameters::CUSTOMER_ID] = $this->subscription->customer->id;
		$newBankAccount = PayUBankAccounts::create($parameters);
	
// 		print_r("newBankAccount=> id=". $newBankAccount->id . ", state=" . $newBankAccount->state);
		//Update the subscription with the new bank account
		$parameters = array();
		$parameters[PayUParameters::SUBSCRIPTION_ID] = $this->subscription->id;
		$parameters[PayUParameters::BANK_ACCOUNT_ID] = $newBankAccount-> id;
	
// 		print_r(" BankAccount Id Old: " . $this->subscription->customer->bankAccounts[0]->id);
// 		print_r(" BankAccount Account Id New: " . $newBankAccount-> id);
	
		$response = PayUSubscriptions::update($parameters);
	}		

	/**
	 * Test that it find the subscription given the id
	 *  depends of testCreateSubscriptionNewPlanAndNewCustomerAndNewBankAccount() method
	 */
	public function testGetSubscriptionById(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
		
		//executes the creation Subscription
		$this->testCreateSubscriptionNewPlanAndNewCustomerAndNewBankAccount();

		$parameters[PayUParameters::SUBSCRIPTION_ID] = $this->subscription->id;
		$response = PayUSubscriptions::find($parameters);
		
		$this->assertNotNull($response);
		$this->assertNotNull($response->id);
		$this->assertEquals($this->subscription->id, $response->id);
	}	
	
	/**
	 * Test that it finds the subscriptions given a plan id 
	 */
	public function testGetSubscriptionListByPlanId(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
	
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
	
		// Plan parameters
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan($parameters));
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		$response = PayUSubscriptions::createSubscription($parameters);
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
		$subscriptionId0 = $response->id;
		
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		$response = PayUSubscriptions::createSubscription($parameters);
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
		$subscriptionId1 = $response->id;
		
		$subscriptionIds = array($subscriptionId0, $subscriptionId1);
	
		$parameters = array(PayUParameters::PLAN_ID => $plan->id);
		$response = PayUSubscriptions::findSubscriptionsByPlanOrCustomerOrAccount($parameters);
	
		$this->assertNotNull($response);
		$this->assertEquals(2, count($response->subscriptionsList));
		$this->assertContains($response->subscriptionsList[0]->id, $subscriptionIds);
		$this->assertContains($response->subscriptionsList[1]->id, $subscriptionIds);
		
	}	
	
	/**
	 * Test that it finds the subscriptions given a plan code
	 */
	public function testGetSubscriptionListByPlanCode(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
	
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
	
		// Plan parameters
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan($parameters));
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		$response = PayUSubscriptions::createSubscription($parameters);
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
		$subscriptionId0 = $response->id;
	
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		$response = PayUSubscriptions::createSubscription($parameters);
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
		$subscriptionId1 = $response->id;
	
		$subscriptionIds = array($subscriptionId0, $subscriptionId1);
		
		$parameters = array(PayUParameters::PLAN_CODE => $plan->planCode);
		$response = PayUSubscriptions::findSubscriptionsByPlanOrCustomerOrAccount($parameters);
	
		$this->assertNotNull($response);
		$this->assertEquals(2, count($response->subscriptionsList));
		$this->assertContains($response->subscriptionsList[0]->id, $subscriptionIds);
		$this->assertContains($response->subscriptionsList[1]->id, $subscriptionIds);
	}	
	
	/**
	 * Test that it finds the subscriptions given a account state
	 */
	public function testGetSubscriptionListByAccountStateActive(){
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;
		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	
		// Subscription parameters
		$parameters = PayUTestUtil::buildSubscriptionParameters();
	
		// Customer parameters
		$parameters = PayUTestUtil::buildSubscriptionParametersCustomer($parameters);
	
		// Plan parameters
		$plan = PayUSubscriptionPlans::create(PayUTestUtil::buildSuccessParametersPlan($parameters));
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		$response = PayUSubscriptions::createSubscription($parameters);
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
		$subscriptionId0 = $response->id;
	
		$parameters[PayUParameters::PLAN_ID] = $plan->id;
		$parameters = PayUTestUtil::buildSubscriptionParametersCreditCard($parameters);
		$response = PayUSubscriptions::createSubscription($parameters);
		$this->assertNotNull($response->id);
		$this->assertNotNull($response->plan->id);
		$subscriptionId1 = $response->id;
	
		$subscriptionIds = array($subscriptionId0, $subscriptionId1);
		$limit = 10;
		$parameters = array();
		$parameters[PayUParameters::SUBSCRIPTION_STATE] = "ACTIVE";
		$parameters[PayUParameters::OFFSET] = 10;
		$parameters[PayUParameters::LIMIT] = $limit;
		
		$response = PayUSubscriptions::findSubscriptionsByPlanOrCustomerOrAccount($parameters);
	
		$this->assertNotNull($response);
		$this->assertNotNull($response->subscriptionsList);
		$this->assertGreaterThan(0, $response->subscriptionsList);
		$this->assertEquals($limit, count($response->subscriptionsList));

	}
	
}
