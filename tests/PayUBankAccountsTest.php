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
 * Test cases to bank accounts api
 * @author PayU Latam
 * @since 1.0.0
 * @version 1.0
 *
 */
class PayUBankAccountsTest extends PHPUnit_Framework_TestCase
{
	/**Customer Id */
	private static $customerId;

	/**
	 * the constructor class
	 */
	public function __construct()
	{
		PayU::$apiLogin = PayUTestUtil::API_LOGIN;
		PayU::$apiKey = PayUTestUtil::API_KEY;

		Environment::setSubscriptionsCustomUrl(PayUTestUtil::SUBSCRIPTION_CUSTOM_URL);
	}

	/**
	 * test to get a bank account
	 */
	public function testGetBankAccount(){

		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		self::$customerId = $customer->id;
		$parameters = PayUTestUtil::buildParametersBankAccount();
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;

 		$bankAccount = PayUBankAccounts::create($parameters);
 		$accountId = $bankAccount->id;
 		$parameters = array(PayUParameters::BANK_ACCOUNT_ID => $bankAccount->id);

		$response = PayUBankAccounts::find($parameters);
		$this->assertNotNull($response);

		$this->assertEquals($response->id, $accountId);
		$this->assertNotNull($response->accountNumber);
 		$this->assertEquals($response->customerId, self::$customerId);
	}

	/**
	 * test to get a bank account with invalid account Id
	 * @expectedException PayUException
	 */
	public function testGetBankAccountWithInvalidAccountId(){

		$accountId = 'account_XXXX';
		$parameters = array(PayUParameters::BANK_ACCOUNT_ID => $accountId);

		$response = PayUBankAccounts::find($parameters);
	}

	/**
	 * test to get a list of bank account
	 */
	public function testfindListBankAccountByCustomer(){
		$request = new stdClass();
// 		self::$customerId = '5761dx4euxp4';

		$parameters = array(PayUParameters::CUSTOMER_ID => self::$customerId);

		$response = PayUBankAccounts::findListByCustomer($parameters);
		$this->assertNotNull($response);
		$this->assertNotEmpty($response->bankAccountList);

		$firstAccountBank = $response->bankAccountList[0];

		$this->assertNotNull($firstAccountBank->id);
		$this->assertNotNull($firstAccountBank->customerId);
		$this->assertNotNull($firstAccountBank->name);
		$this->assertNotNull($firstAccountBank->accountNumber);
	}


	/**
	 * test to create a bank account
	 */
	public function testCreateBankAccount(){
		$parameters = PayUTestUtil::buildParametersBankAccount();
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$response = PayUBankAccounts::create($parameters);
		$this->assertNotNull($response);
		$this->assertNotNull($response->id);
		$this->assertEquals("CREATED",$response->state);
	}

	/**
	 * test to create a bank account brazil
	 */
	public function testCreateBankAccountBrazil(){
		$parameters = PayUTestUtil::buildParametersBankAccountBrazil();
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;

		$response = PayUBankAccounts::create($parameters);
		$this->assertNotNull($response);
		$this->assertNotNull($response->id);
		$this->assertEquals("ACTIVE",$response->state);
	}

	/**
	 * test create invalid bank account brazil
	 * bankaccount type SAVING is invalid to brazil
	 * @expectedException PayUException
	 */
	public function testCreateBankAccountTypeInvalidBrazil(){
		$parameters = PayUTestUtil::buildParametersBankAccountBrazil();
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;
		$parameters[PayUParameters::BANK_ACCOUNT_TYPE] =  'SAVING';

		$response = PayUBankAccounts::create($parameters);
	}

	/**
	 * test update bank account
	 */
	public function testUpdateBankAccount(){

		$parameters = PayUTestUtil::buildParametersBankAccount();
		$customer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$this->assertNotNull($customer);
		$this->assertNotNull($customer->id);

		$parameters[PayUParameters::CUSTOMER_ID] = $customer->id;

		$createdAccount = PayUBankAccounts::create($parameters);
		$this->assertNotNull($createdAccount);
		$this->assertNotNull($createdAccount->id);

		$parameters[PayUParameters::BANK_ACCOUNT_ID] = $createdAccount->id;
		$foundAccount = PayUBankAccounts::find($parameters);

		//Update bank account data
		$parametersToEdit = array(
				PayUParameters::BANK_ACCOUNT_CUSTOMER_NAME => 'Test Johan Navarrete',
				PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER => '99999999',
				PayUParameters::BANK_ACCOUNT_DOCUMENT_NUMBER_TYPE => 'TI',
				PayUParameters::BANK_ACCOUNT_BANK_NAME => 'CITIBANK',
				PayUParameters::BANK_ACCOUNT_NUMBER => '7777777777',
				PayUParameters::BANK_ACCOUNT_ID => $foundAccount->id
		);

		$editedAccount = PayUBankAccounts::update($parametersToEdit);

		$this->assertNotNull($editedAccount);
		$this->assertEquals($parametersToEdit->bankAccountId,$editedAccount->bankAccountId);
		$this->assertEquals($parametersToEdit->name,$editedAccount->name);
		$this->assertEquals($parametersToEdit->documentNumber,$editedAccount->documentNumber);
		$this->assertEquals($parametersToEdit->documentNumberType,$editedAccount->documentNumberType);
		$this->assertEquals($parametersToEdit->bank,$editedAccount->bank);
	}

	/**
	 * test update bank account with invalid
	 * @expectedException PayUException
	 */
	public function testUpdateBankAccountWithInvalidId(){

		$parameters = PayUTestUtil::buildParametersBankAccount();

		//Update bank account data
		$parametersToEdit = array(
				PayUParameters::BANK_ACCOUNT_ID => "INVALID_ID"
		);

		$editedAccount = PayUBankAccounts::update($parametersToEdit);
	}

	/**
	 * test update bank account with invalid
	 * @expectedException InvalidArgumentException
	 */
	public function testUpdateBankAccountWithoutId(){

		$parameters = PayUTestUtil::buildParametersBankAccount();

		//Update bank account data
		$parametersToEdit = array(
				PayUParameters::OFFSET => "INCORRECT_PARAMETER"
		);

		$editedAccount = PayUBankAccounts::update($parametersToEdit);
	}

	/**
	 * test delete bank account
	 */
	public function testDeleteBankAccount(){

		//Create Customer
		$parameters = PayUTestUtil::buildParametersBankAccount();
		$createdCustomer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$this->assertNotNull($createdCustomer);
		$this->assertNotNull($createdCustomer->id);

		//Create bank account to customer
		$parameters[PayUParameters::CUSTOMER_ID] = $createdCustomer->id;
		$createdBankAccount = PayUBankAccounts::create($parameters);
		$this->assertNotNull($createdBankAccount);
		$this->assertNotNull($createdBankAccount->id);
		$this->assertEquals("CREATED",$createdBankAccount->state);

		//Delete bank account
		$parameters[PayUParameters::BANK_ACCOUNT_ID] = $createdBankAccount->id;
		$response = PayUBankAccounts::delete($parameters);
		$this->assertNotNull($response);
		$this->assertNotNull($response->description);
	}

	/**
	 * test delete bank account whit invalid or not existent bank account id
	 * @expectedException PayUException
	 */
	public function testDeleteBankAccountWithInvalidBankAccountId(){

		//Create Customer
		$parameters = PayUTestUtil::buildParametersBankAccount();
		$createdCustomer = PayUCustomers::create(PayUTestUtil::buildSubscriptionParametersCustomer());
		$this->assertNotNull($createdCustomer);
		$this->assertNotNull($createdCustomer->id);

		//Delete bank account
		$parameters[PayUParameters::CUSTOMER_ID] = $createdCustomer->id;
		$parameters[PayUParameters::BANK_ACCOUNT_ID] = "INVALID_BANK_ACCOUNT_ID";
		$response = PayUBankAccounts::delete($parameters);
	}

	/**
	 * test delete bank account whitout customer id
	 * @expectedException InvalidArgumentException
	 */
	public function testDeleteBankAccountWithoutCustomerId(){
		$parameters[PayUParameters::BANK_ACCOUNT_ID] = "ID_TESTING";
		$response = PayUBankAccounts::delete($parameters);
	}

	/**
	 * test delete bank account whitout customer id or bank account id
	 * @expectedException InvalidArgumentException
	 */
	public function testDeleteBankAccountWithoutParameters(){
		$response = PayUBankAccounts::delete(array());
	}
}
