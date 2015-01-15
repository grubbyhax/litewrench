<?php
//api.php
//This is the API transaction script.

$cooApi = new Coo_Api();

/* transact 1 */
$response = $cooApi->authenticate('admin', '21232f297a57a5a743894a0e4a801fc3')
		->updateProfile(array(
			'exporter'				=> array(
				'title'					=>'Mr',
				'firstname'				=>'thisisaname',
				'lastname'				=>'thisname',
				'position'				=>'thisposition',
				'phone'					=>'98723457'),
			'company'				=> array(
				'name' 					=> 'tradename',
				'address_1' 			=> 'this address line here',
				'address_2' 			=> 'this address line here',
				'city' 					=> 'this cityname',
				'state' 				=> 'thisstatename',
				'country' 				=> 'Australia',
				'postcode' 				=> '2000')))
		->fetch();
/**/

/* transact 2 */
$response = $cooApi->authenticate('admin', '21232f297a57a5a743894a0e4a801fc3')
		->updateSignatory(array(
			'key'					=>'wencsdsdc0923n23lnsdkcn',
			'name'					=>'sgiie',
			'title'					=>'sometitle',
			'position'				=>'thisposition',
			'phone'					=>'98723457',
			'company'				=> array(
				'key' 					=> '098asc08a0vc8vd09v8s',
				'name' 					=> 'tradename',
				'address_1' 			=> 'this address line here',
				'address_2' 			=> 'this address line here',
				'city' 					=> 'this cityname',
				'state' 				=> 'thisstatename',
				'country' 				=> 'Australia',
				'postcode' 				=> '2000')))
		->fetch();
/**/

/* transact 3 */
$response = $cooApi->authenticate('admin', '21232f297a57a5a743894a0e4a801fc3')
		->updateConsignee(array(
			'key'					=>'asjxmcdas0u8c0aucamco',
			'name'					=>'somename',
			'type'					=>'consginee',
			'company'				=> array(
				'key' 					=> 'mnb56b54m7nb5mn7u',
				'name' 					=> 'tradename',
				'address_1' 			=> 'this address line here',
				'address_2' 			=> 'this address line here',
				'city' 					=> 'this cityname',
				'state' 				=> 'thisstatename',
				'country' 				=> 'Australia',
				'postcode' 				=> '2000')))
		->fetch();
/**/

/* transact 4 */
$response = $cooApi->authenticate('admin', '21232f297a57a5a743894a0e4a801fc3')
		->updateGoods(array(
			'key'					=>'a98cjdw8us0cjdae4wqe5c',
			'name'					=>'somename',
			'origin'				=>'theorigin',
			'hs_code'				=>'goods hs code',
			'description'			=>'Some decript here'))
		->fetch();
/**/


/*
$response = $cooApi->authenticate('admin', '21232f297a57a5a743894a0e4a801fc3')
		->updateProfile(array(
			'exporter'				=> array(
				'title'					=>'Mr',
				'firstname'				=>'thisisaname',
				'lastname'				=>'thisname',
				'position'				=>'thisposition',
				'phone'					=>'98723457'),
			'company'				=> array(
				'name' 					=> 'tradename',
				'address_1' 			=> 'this address line here',
				'address_2' 			=> 'this address line here',
				'city' 					=> 'this cityname',
				'state' 				=> 'thisstatename',
				'country' 				=> 'Australia',
				'postcode' 				=> '2000')))
		->updateSignatory(array(
			'key'					=>'wencsdsdc0923n23lnsdkcn',
			'name'					=>'sgiie',
			'title'					=>'sometitle',
			'position'				=>'thisposition',
			'phone'					=>'98723457',
			'company'				=> array(
				'key' 					=> '098asc08a0vc8vd09v8s',
				'name' 					=> 'tradename',
				'address_1' 			=> 'this address line here',
				'address_2' 			=> 'this address line here',
				'city' 					=> 'this cityname',
				'state' 				=> 'thisstatename',
				'country' 				=> 'Australia',
				'postcode' 				=> '2000')))
		->updateConsignee(array(
			'key'					=>'asjxmcdas0u8c0aucamco',
			'name'					=>'somename',
			'type'					=>'consginee',
			'company'				=> array(
				'key' 					=> 'mnb56b54m7nb5mn7u',
				'name' 					=> 'tradename',
				'address_1' 			=> 'this address line here',
				'address_2' 			=> 'this address line here',
				'city' 					=> 'this cityname',
				'state' 				=> 'thisstatename',
				'country' 				=> 'Australia',
				'postcode' 				=> '2000')))
		->updateGoods(array(
			'key'					=>'a98cjdw8us0cjdae4wqe5c',
			'name'					=>'somename',
			'origin'				=>'theorigin',
			'hs_code'				=>'goods hs code',
			'description'			=>'Some decript here'))
		->updateCertificate(array(
			'key'					=>'8as98sfdsf976df98s76',
			'importing_country'		=>'China',
			'certificate_type'		=>'TAFTA',
			'products'				=>'abriefproductsoverviewtext',
			'source'				=>'someproductsource',
			'signatory'				=> array(
				'key' 					=> 'wencsdsdc0923n23lnsdkcn'),
			'consignee'				=> array(
				'key' 					=> 'asjxmcdas0u8c0aucamco'),
			'shipping'				=> array(
				'port_of_loading' 		=> 'someportname',
				'port_of_discharge' 	=> 'anotherportname',
				'final_destination' 	=> 'somedestination',
				'vessel_name' 			=> 'vesselcode',
				'shippment_date' 		=> 'someformateddate'),
			'coo_item'				=> array(
				'key' 					=> 'a9c87sd53cvx2km345mk',
				'marks' 				=> 'somemarks',
				'quantity' 				=> 'somequantity',
				'invoicing' 			=> 'invoicingnotes',
				'numbers_kind' 			=> 'somemorenumbers',
				'description' 			=> 'coo item description',
				'goods' 				=> array(
					'key'					=> 'a98cjdw8us0cjdae4wqe5c'))))
		->fetch();
*/

//Handle returned errors.
if (!$response)
{
	//Get errors and do something with them.
	$response = $cooApi->getErrors();
}
else
{
	$response = $cooApi->requestDocument->saveXML();
}

echo $response;
exit;

class Coo_Api
{
	//Current authentication settings.
	public $email = ''; //Currently not used
	protected $username = '';
	protected $password = '';

	//Local request and response DOM documents.
	public $requestDocument = null;
	public $responseDocument = null;

	//Object constructor.
	// - $language:					Language settings to use, mostly for error return strings
	// * Return:					SELF
	public function __construct($language='en')
	{
		//Create document wrapper elements.
		$this->requestDocument = new DOMDocument('1.0', 'utf-8');
		$requestElement = $this->requestDocument->createElement('request');
		$requestElement->setAttribute('language', $language);
		$this->requestDocument->appendChild($requestElement);

		return $this;
	}

	//Sets the current authentication setting in the request builder.
	// - $username:					Profile login user name
	// - $password:					Profile login password
	// * Return:					SELF
	public function authenticate($username, $password)
	{
		$this->username = $username;
		$this->password = $password;

		return $this;
	}

	//This does the transaction and returns to response document.
	// * Return:					True if response indicated complete and successful transaction
	//*!*Needs more work to handle connection errors
	public function fetch()
	{
		//Attach error handling if there is no request document.
		if (!$this->requestDocument)
		{
			exit;
		}

		//Convert request document to a string.
		$request = $this->requestDocument->saveXML();

		$url = 'http://servername/api/';
		$curlHandler = curl_init();

		$curlPost = array('api' => urlencode($request));
		$curlPostSerialised = '';
		foreach($curlPost as $key => $value)
		{
			$curlPostSerialised .= $key.'='.$value.'&';
		}
		rtrim($curlPostSerialised, '&');

		curl_setopt($curlHandler, CURLOPT_HEADER, 0);
		curl_setopt($curlHandler, CURLOPT_URL, $url);
		curl_setopt($curlHandler, CURLOPT_POST, count($curlPost));
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $curlPostSerialised);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);

		//Make request.
		$response = curl_exec($curlHandler) or die(curl_error());
		curl_close($curlHandler);

		//Convert response string to document.
		$this->responseDocument = new DOMDocument('1.0', 'utf-8');
		$this->responseDocument->loadXML($response);

		//Find errors in return documnt.
		$elements = $this->responseDocument->getElementsByTagName('*');
		foreach ($elements as $element)
		{
			if ($element->hasAttribute('result') && $element->getAttribute('result') == 'failure')
			{
				return false;
			}
		}

		return true;
	}

	//Returns the request document authentication element with the current user settings
	// * NB: If element is not found this function will create and append  new authenticate element.
	public function getAuthenticateElement()
	{
		//If the authenticate element has been created return it.
		$authenticateElements = $this->requestDocument->getElementsByTagName('authenticate');
		foreach ($authenticateElements as $authenticateElement)
		{
			if ($authenticateElement->hasAttribute('username')
			&& $authenticateElement->hasAttribute('password')
			&& $authenticateElement->getAttribute('username') == $this->username
			&& $authenticateElement->getAttribute('password') == $this->password)
			{
				return $authenticateElement;
			}
		}

		//Otherwise create the authentication element.
		$authenticateElement = $this->requestDocument->createElement('authenticate');
		$authenticateElement->setAttribute('username', $this->username);
		$authenticateElement->setAttribute('password', $this->password);
		$this->requestDocument->documentElement->appendChild($authenticateElement);

		return $authenticateElement;
	}

	//Recursive function to build a transaction data element from any nested array of values.
	// - $elementData:			Array of data to add to setElement
	// - $setElement:			Element to set elementData as dom elements to
	// * Return:				$setElement with data attached as dom elements
	public function createDataElements($elementData, $setElement)
	{
		if ($elementData && is_array($elementData))
		{
			foreach ($elementData as $dataKey => $dataValue)
			{
				$dataElement = null;

				if (is_array($dataValue))
				{
					$dataElement = $this->requestDocument->createElement($dataKey);
					$dataElement = $this->createDataElements($dataValue, $dataElement);
				}
				else
				{
					//Set the key as an attribute.
					if ($dataKey == 'key')
					{
						$setElement->setAttribute('key', $dataValue);
					}
					//Set value as an element.
					else
					{
						$dataElement = $this->requestDocument->createElement($dataKey, $dataValue);
					}
				}

				if ($dataElement)
				{
					$setElement->appendChild($dataElement);
				}
			}
		}

		return $setElement;
	}

	//Adds request data to the current authenticate element.
	// - $type:						Type of data to create a transaction for(profile, signatory, consignee, goods, certificate)
	// - $action:					Transaction action to be made(create, update)
	// - $data:						Array of data to add to the request document or a prebuilt DOMElement.
	// * Return:					True of the data supplied is valid, otherwise false
	public function addRequestData($type, $action, $data)
	{
		$success = true;
		$authenticateElement = $this->getAuthenticateElement();

		if (is_array($data))
		{
			//Create profile element.
			$dataElement = $this->requestDocument->createElement($type);
			$dataElement->setAttribute('action', $action);

			//If there are exporter details add them.
			if ($data)
			{
				$dataElement = $this->createDataElements($data, $dataElement);
			}

			//Set profile element to request authentication.
			$authenticateElement->appendChild($dataElement);
		}
		elseif (is_object($data) && get_class($data) == 'DOMElement')
		{
			//Set profile element to request authentication.
			$authenticateElement->appendChild($data);
		}
		else
		{
			//Do exception handling here.
			$success = false;
		}

		return $success;
	}

	//Udpates the user's profile
	// - $profileDetails:			Key/Value array of the new deatils to update the usr profile.
	// - $create:					Creates a new profile for the user.
	// * Return:					SELF
	public function updateProfile($profileDetails, $create=false)
	{
		$action = ($create)? 'create': 'update';
		if (!$this->addRequestData('profile', $action, $profileDetails))
		{
			//Do error message here.
		}

		return $this;
	}

	//Udpates the a signatory attached to the user's profile.
	// - $signatoryDetails:			Key/Value array of the new deatils to update.
	// - $create:					Creates a new signaotry for the user.
	// * Return:					SELF
	public function updateSignatory($signatoryDetails, $create=false)
	{
		$action = ($create)? 'create': 'update';
		if (!$this->addRequestData('signatory', $action, $signatoryDetails))
		{
			//Do error message here.
		}

		return $this;
	}

	//Udpates the a consignee attached to the user's profile.
	// - $consigneeDetails:			Key/Value array of the new deatils to update.
	// - $create:					Creates a new consignee for the user.
	// * Return:					SELF
	public function updateConsignee($consigneeDetails, $create=false)
	{
		$action = ($create)? 'create': 'update';
		if (!$this->addRequestData('consignee', $action, $consigneeDetails))
		{
			//Do error message here.
		}

		return $this;
	}

	//Udpates the consignment goods attached to the user's profile.
	// - $goodsDetails:				Key/Value array of the new deatils to update.
	// - $create:					Creates a new consignment goods for the user.
	// * Return:					SELF
	public function updateGoods($goodsDetails, $create=false)
	{
		$action = ($create)? 'create': 'update';
		if (!$this->addRequestData('goods', $action, $goodsDetails))
		{
			//Do error message here.
		}

		return $this;
	}

	//Udpates the a certificate request attached to the user's profile.
	// - $certificateDetails:		Key/Value array of the new deatils to update.
	// - $create:					Creates a new certificate request for the user.
	// * Return:					SELF
	public function updateCertificate($certificateDetails, $create=false)
	{
		$action = ($create)? 'create': 'update';
		if (!$this->addRequestData('certificate', 'update', $certificateDetails))
		{
			//Do error message here.
		}

		return $this;
	}
	
	//Recursively builds an xpath string to the transaction element.
	// - $errorElement:				Element to build xpath for
	// * Return:					Xpath string to transaction element
	public function transactXpath($errorElement)
	{
		$Xpath = '/'.$errorElement->nodeName;

		if ($errorElement->parentNode->nodeName != 'authenticate')
		{
			$Xpath = $this->transactXpath($errorElement->parentNode).$Xpath;
		}

		return $Xpath;
	}

	//Gets a set of errors from a transaction response document.
	// - $response:				Response document elemnt or string, default false(locally set response document)
	// * Return:				Array of response errors with their xpaths for reference(xpath, error, value)
	public function getErrors($response=false)
	{
		//If no external document is used get locally set response.
		if (!$response)
		{
			$response = $this->responseDocument;
		}
		//Otherwise ensure response is a document
		elseif (is_string($response))
		{
			$responseString = $response;
			$response = new DOMDocument('1.0', 'utf-8');
			$response->loadXML($responseString);
		}
		else
		{
			if (is_object($response) && get_class($response) != 'DOMDocument')
			{
				//Do error for response not beign a dom document.
				return false;
			}
			else
			{
				//Otherwise do error for invalid data type.
				return false;
			}
		}

		//Get elements with error messages on them.
		$elements = $response->getElementsByTagName('*');
		$errorElements = array();
		foreach ($elements as $element)
		{
			if ($element->hasAttribute('error') && $element->getAttribute('error'))
			{
				$errorElements[] = array('xpath' => $this->transactXpath($element),
										'error' => $element->getAttribute('error'),
										'value' => $element->nodeValue);
			}
		}

		return $errorElements;
	}

}

?>