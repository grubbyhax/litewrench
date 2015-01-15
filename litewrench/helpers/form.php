<?php
//mw_form.php
//Form class file.
//Design by David Thomson at Hundredth Codemonkey.

///////////////////////////////////////////////////////////////////////////////
//                        M O D U L E   C L A S S                            //
///////////////////////////////////////////////////////////////////////////////

class MW_Helper_Form extends MW_Utility_Helper
{
	//*!*We need to test whether or not these are parcels or packages.
	//I don't like using parcels globally as I'm going to use different layers of model objects.
	//So this var will probably end up being defined as $Arr_Models or some such shit.
	private $Arr_Parcels			= array();	//Parcels with fields in form.

	private $Reg_FieldNameVar		= '/\$([a-zA-Z0-9]+)(\[([a-zA-Z0-9,]*)\])*/';
	
	//Indicate3s whether or not the form hasbeen processed.
	private $Bol_Processed			= false;

///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties	= array(
		'Arr_Validators'		=> array(),	//Data validator helper objects.*!*I think these need to be like the parcels
		'Arr_Filters'			=> array(), //Data filter helper object. *!*Same note as above
		'Arr_Callbacks'			=> array(),	//List of callback methods.
		'Arr_Submit'			=> array(),	//Properties and status of form.
		'Arr_Fields'			=> array(),	//Field names and parcel properties.
		'Arr_Active'			=> array());//Active elements within the form.


/* PARCEL FORM/FIELD PROPERTIES - these become fields
NB: Same of these have been modified or removed...
//Also need to add a parcel reference when loading these properties.
	'parcel'	=> 'reference_id';

$Arr_DataProperties = array('propertyname' => array(
	'name'		=> 'form field name'
	'id'		=> 'form field id'
	'field'		=> 'database field name',
	'value'		=> 'form (user)field value, can be array for multis',
	'insert'	=> 'database insert values',
	'format'	=> 'array of formatting instrauctions for inerstion in db'
	'display'	=> '(validated)form display value',
	'range'		=> 'rules for value range - array()', - this should be part of the validator.
	'valid'		=> 'whetter the form field value is valid',
	'error'		=> 'Error message',
	'default'	=> 'default values',
	'filters'	=> 'array of regex filters with error messages',
	'validators	=> 'array of value validators w/ error messages(Eg '> 5') - they are similar to filters but more user friendly and can use objects'
	'required'	=> 'required message'
	'type'		=> 'field type, can be dynamic')
);
*/

//*!*Just a quicky, I need to look through all of this object's methods and determine which ones are public, protected and private. :)
//I need to stay true to my API concepts.

//TODO:
//Fix attributes code, it's a bit fucked. I need to be able to set classes to labels and then inputs and not have them share them.
//I need to go throught the code and test in API functions that a field has been activated otherwise I need to throw an exception

//Quick note about filters. I'm leaning towards having all filters being manually applied.
//It doesn't seem like there would be any control if filters were run from a schema property.

///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////


	public function __construct()
	{
		//Initialise form submission values.
		$this->Arr_Submit = array('valid' => false, 'data' => '', 'action' => '', 'method' => 'post', 'encoding'=>'');

		//Create default fields.
		$this->field('submit', 'submit')->display('Submit')->field('reset', 'reset')->display('Reset');
		$this->Arr_Active = array('field'=>'', 'element'=>'', 'value'=>'');

		//Add the core validator.
		global $CMD;
		$this->validator($CMD->helper('validate'));
		$this->filtration($CMD->helper('filter'));

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//                 M O D U L E   A P I   F U N C T I O N S                   //
///////////////////////////////////////////////////////////////////////////////

	//Creates an field for form display and saving.
	// - $Str_Name:				Field's form value name
	// - $Str_Type:				Name of field to set as a confirmation to, default = false(not a confirmation field)
	// * Return:				SELF
	public function field($Str_Name, $Str_Type=false)
	{
		//If the field name is invalid handle exception.
		//*!*Need a better test, ie for string and length
		if (!is_string($Str_Name) || !$Str_Name)
		{
			global $CMD;
			$CMD->handle_exception('Field name is not a valid string', 'MW:101');
		}

		//Decouple active values.
		$Arr_NewActive = $this->Arr_Active;

		//If there is no field type defined set field as active.
		if ($Str_Type === false)
		{
			if (isset($this->Arr_Fields[$Str_Name]))
			{
				if ($Arr_NewActive['field'] != $Str_Name)
					$Arr_NewActive['value'] = '';

				$Arr_NewActive['field'] = $Str_Name;
			}
			//Otherwise handle exception.
			else
			{
				global $CMD;
				$CMD->handle_exception('Field '.$Str_Name.' does not exist on form object', 'MW:101');
			}
		}
		//Otherwise create field.
		else
		{
			//If the field name $Str_Name is already in use handle exception.
			if (isset($this->Arr_Fields[$Str_Name]))
			{
				global $CMD;
				$CMD->handle_exception('Field '.$Str_Name.' already exists on form object', 'MW:101');
				//*!*This should be in the exception handler.
				//*!*And alot more elgent than this too!
				//I need to check for other inputs that have replicated this one and put the right suffix on it.
				$Str_Name .= '_1';
			}

			//Initialise field properties.
			$Arr_FieldProperties = array();
			$Arr_FieldProperties['name'] = $Str_Name;
			$Arr_FieldProperties['type'] = $Str_Type;
			$Arr_FieldProperties['id'] = count($this->Arr_Fields) + 1;

			//Set field as active.
			$Arr_NewActive['field'] = $Arr_FieldProperties['name'];

			//Set field properties locally.
			$Arr_NewFields = $this->Arr_Fields;
			$Arr_NewFields[$Arr_FieldProperties['name']] = $Arr_FieldProperties;
			$this->Arr_Fields = $Arr_NewFields;

			//Add encoding.
			if ($Str_Type == 'file')
				$this->encoding('multipart/form-data');
		}

		//Recouple active values.
		$this->Arr_Active = $Arr_NewActive;

		return $this;
	}

	//Binds a model object field to active form field.
	// - $Obj_Model:			Model object to bind to active field
	// - $Str_Property:			Data schema property in $Obj_Model->Arr_TableSchema
	// - $Str_Aspect:			Binds only a part of $Str_Field to the active field, default=false(undefined)
	// * Return:				SELF
	//*!*Need to add new logic for creating vanilla fields with no binding $this->field('foo', 'checkbox')->bind(false)
	//Do I even need to call the bind method for a vanilla field?
	public function bind(&$Obj_Model, $Str_Property, $Str_Aspect=false)
	{
		//If the model table schema field exists set it to the form.
		if ($Arr_FieldDataSchema = $Obj_Model->get_data_schema($Str_Property))
		{
			//If the parcel is not registered set it locally.
			if (!array_key_exists($Obj_Model->get_ref(), $this->Arr_Parcels))
			{
				$Arr_NewParcels = $this->Arr_Parcels;
				$Arr_NewParcels[$Obj_Model->get_ref()] = $Obj_Model;
				$this->Arr_Parcels = $Arr_NewParcels;
			}

			//Decouple fields.
			$Arr_NewFields = $this->Arr_Fields;

			//Add model reference.
			if (!isset($Arr_NewFields[$this->Arr_Active['field']]['ref']))
				$Arr_NewFields[$this->Arr_Active['field']]['ref'] = $Obj_Model->get_ref();
			//*!*It seems like this test is catching out users who don't define a new field with the second paramter.
			//Not thinking clearly right now, but, this would be useful to execute some exception handling to inform the user
			//what they have done wrong.
			elseif (!in_array($Obj_Model->get_ref(), $Arr_NewFields[$this->Arr_Active['field']]['ref']))
				$Arr_NewFields[$this->Arr_Active['field']]['ref'] = $Obj_Model->get_ref();

			//Set model property name to field.
			$Arr_NewFields[$this->Arr_Active['field']]['property'] = $Str_Property;

			//If the field has a model aspect set it.
			if ($Str_Aspect !== false)
				$Arr_NewFields[$this->Arr_Active['field']]['aspect'] = $Str_Aspect;

			//Add model field validators to the active field.
			if (isset($Arr_FieldDataSchema['validate']) && $Arr_FieldDataSchema['validate'])
			{
				foreach ($Arr_FieldDataSchema['validate'] as $Arr_Validator)
				{
					//If the there is no aspect defined or the aspects equal set validator.
					if (!isset($Arr_Validator['aspect']) || $Arr_Validator['aspect'] == $Str_Aspect)
					{
						$Arr_Validator['ref'] = $Obj_Model->get_ref();
						$Arr_NewFields[$this->Arr_Active['field']]['validate'][] = $Arr_Validator;
					}
				}
			}

			//Add model field validation optionals to active field.
			/*!*I may ditch this so as to have no preset optionals.
			if (isset($Arr_FieldDataSchema['optional']) && $Arr_FieldDataSchema['optional'])
			{
				foreach ($Arr_FieldDataSchema['optional'] as $Arr_Optional)
				{
					//If the there is no aspect defined or the aspects equal set validator.
					//*!*I'm going to have to deal with this later, I don;t think this test is ROBUST!
					if (!isset($Arr_Optional['aspect']) || $Arr_Optional['aspect'] == $Str_Aspect)
					{
						$Arr_NewFields[$this->Arr_Active['field']]['optional'][] = $Arr_Optional;
					}
				}
			}
*/
			//Loop through all fields and convert property name and aspects to form fields.
			foreach ($Arr_NewFields as $Str_FieldName => $Arr_FieldProperties)
			{
				//If there are validate optionals update field references.
				//If there are validators set update field refernces.
				if (isset($Arr_FieldProperties['validate']) && is_array($Arr_FieldProperties['validate']))
				{
					foreach ($Arr_FieldProperties['validate'] as $Int_Validator => $Arr_Validator)
					{
/*-------------------------------------------------------------------------------------*/
//This ia all to be refactored so I can do optionals as well, actually I don't need to do this, or maybe I should...
						//Asses each validate match parameter.
						$Int_Paramters = count($Arr_Validator['match']);
						for ($i = 1; $i < $Int_Paramters; $i++)
						{
							//If there is a reference match replace it with the field name.
							$Arr_RegexMatches = array();
							$Str_FieldRegex = '/\&([a-zA-Z0-9]+)(\(([a-zA-Z0-9]+)\))?(\[([a-zA-Z0-9,]*)\])*/';
							if (preg_match($Str_FieldRegex, $Arr_Validator['match'][$i], $Arr_RegexMatches))
							{
								$Str_ReferenceArray = (isset($Arr_RegexMatches[4]))? $Arr_RegexMatches[4]: '';

								//Loop through each form field again and find a field with a matching name and model object.
								foreach ($Arr_NewFields as $Str_ReferenceName => $Arr_ReferenceProperties)
								{
									if (isset($Arr_ReferenceProperties['property'])
									&& $Arr_ReferenceProperties['property'] == $Arr_RegexMatches[1]
									&& $Arr_ReferenceProperties['ref'] == $Arr_Validator['ref'])
									{
										//If there is an aspect to property match get it.
										if (isset($Arr_RegexMatches[3]) && $Arr_RegexMatches[3])
										{
											if (isset($Arr_ReferenceProperties['aspect']) &&  isset($Arr_RegexMatches[3])
											&& $Arr_ReferenceProperties['aspect'] == $Arr_RegexMatches[3])
												$Arr_NewFields[$Str_FieldName]['validate'][$Int_Validator]['match'][$i] = '$'.$Str_ReferenceName.$Str_ReferenceArray;
										}
										//Otherwise keep column and aspect intact.
										else
										{
											$Arr_NewFields[$Str_FieldName]['validate'][$Int_Validator]['match'][$i] = '$'.$Str_ReferenceName.$Str_ReferenceArray;
										}
									}
								}
							}
						}
					}
/*-------------------------------------------------------------------------------------*/

				}
			}

			//Recouple fields.
			$this->Arr_Fields = $Arr_NewFields;
		}

		return $this;
	}

	//Adds a validator helper object.
	// - $Obj_Validator:		Validator hepler object to add locally
	// * Return:				SELF
	public function validator($Obj_Validator)
	{
		//If the validator does not already exist locally on the form add it.
		if (!array_key_exists(get_class($Obj_Validator), $this->Arr_Validators))
		{
			$Arr_NewValidators = $this->Arr_Validators;
			$Arr_NewValidators[get_class($Obj_Validator)] = $Obj_Validator;
			$this->Arr_Validators = $Arr_NewValidators;
		}

		return $this;
	}

	//Adds a filter helper object.
	// - $Obj_Filter:			Filter hepler object to add locally
	// * Return:				SELF
	public function filtration($Obj_Filter)
	{
		//If the filter does not already exist locally on the form add it.
		if (!array_key_exists(get_class($Obj_Filter), $this->Arr_Filters))
		{
			$Arr_NewFilters = $this->Arr_Filters;
			$Arr_NewFilters[get_class($Obj_Filter)] = $Obj_Filter;
			$this->Arr_Filters = $Arr_NewFilters;
		}

		return $this;
	}

	//Processes form post data against pre-defined fields.
	// - $Bol_ValidateData:		Directive to validate data, default=true(validate data)
	// * Return:				VOID
	public function post($Bol_ValidateData=true)
	{
		//Set form method to post.
		$Arr_NewSubmit = $this->Arr_Submit;
		//*!*Should I remove this? Or, should I check this value and throw an exception if the method is get?
		$Arr_NewSubmit['method'] = 'post';
		$this->Arr_Submit = $Arr_NewSubmit;

		//*!*Moved from $this->process()
		//If the form has not been submitted return as invalid.
		if (!array_key_exists('submit', $_POST)
		|| !array_key_exists('form', $_POST)
		|| $_POST['form'] != $this->Arr_Submit['name'])
		{
			return false;
		}

		//Add files to post data.
		//*!*Note: currently chosen not to handle files uploaded as an input array.
		$Arr_Post = $_POST;
		if (isset($_FILES) && $_FILES)
		{
			foreach ($_FILES as $Str_FieldName => $Arr_FileData)
			{
				$Arr_Post[$Str_FieldName] = $Arr_FileData;
			}
		}

		return $this->process($Arr_Post, $Bol_ValidateData);
	}

	//Processes form get data against pre-defined fields.
	// - $Bol_ValidateData:		Directive to validate data, default=true(validate data)
	// * Return:				VOID
	public function get($Bol_ValidateData=true)
	{
		//Set form method to get.
		$Arr_NewSubmit = $this->Arr_Submit;
		//Should I remove this? Or, should I check this value and throw an exception if the method is post?
		$Arr_NewSubmit['method'] = 'get';
		$this->Arr_Submit = $Arr_NewSubmit;

		return $this->process($_GET, $Bol_ValidateData);
	}

	//Indicates whether the form has been processed or not.
	// * Return:				True if the form has been processed, otherwise false
	public function processed()
	{
		return $this->Bol_Processed;
	}

	//Processes data set against pre-defined fields, usually get and post variables.
	// - $Arr_DataSet:			Array of variables to process against pre-defined fields
	// - $Bol_ValidateData:		Directive to validate data, default=true(validate data)
	// * Return:				True if data was validated and valid, otherwise false
	// * NB: This function can be extended to any dataset if desired.
	public function process($Arr_DataSet, $Bol_ValidateData=true)
	{
		$Bol_FormValid = true;

		//If magic quotes are turned on strip slashes from variables
		//*!*There is a problem with NULL values in post/get/cookies with magic_quotes_gpc turned on. I may need to rectifiy this.
		if (!get_magic_quotes_gpc())
		{
			$Arr_DataSetKeys = array_keys($Arr_DataSet);
			$Int_DataSetKeys = count($Arr_DataSetKeys);
			for ($i = 0; $i < $Int_DataSetKeys; $i++)
			{
				//If the data field is an array stripslashes from each value.
				if (is_array($Arr_DataSet[$Arr_DataSetKeys[$i]]))
				{
					$Arr_DataKeys = array_keys($Arr_DataSet[$Arr_DataSetKeys[$i]]);
					$Int_DataKeys = count($Arr_DataKeys);
					for ($j = 0; $j < $Int_DataKeys; $j++)
					{
						if ($Arr_DataKeys[$j] != 'tmp_name')
						{
							$Arr_DataSet[$Arr_DataSetKeys[$i]][$Arr_DataKeys[$j]] = stripslashes($Arr_DataSet[$Arr_DataSetKeys[$i]][$Arr_DataKeys[$j]]);
						}
					}
				}
				//Otherwise strip slashes from the scalar value.
				else
				{
					$Arr_DataSet[$Arr_DataSetKeys[$i]] = stripslashes($Arr_DataSet[$Arr_DataSetKeys[$i]]);
				}
			}
		}

		//Decouple field(and parcel) information.
		$Arr_NewFields = $this->Arr_Fields;

		//Loop dataset and assign values to both the fields and parcels.
		$Arr_ValidateFields = array();
		foreach ($Arr_DataSet as $Str_Name => $Mix_Data)
		{
			$Arr_ValidateFields[] = $Str_Name;

			//*!*Is this to ensure no duplication? I can't remember, reanalysis at system testing and comment. I think I've
			if (isset($Arr_NewFields[$Str_Name]))
			{
				if (is_array($Arr_DataSet[$Str_Name]))
				{
					$Arr_NewFields[$Str_Name]['value'] = $Mix_Data;
				}
				else
				{
					$Arr_NewFields[$Str_Name]['value'] = array($Mix_Data);
				}

				//Set data value to parcel.
				//*!*why is this blanked out, i think this is why data is going missing on my parcels set to the form object
				//if (isset($Arr_NewFields[$Str_Name]['ref']))
				//	$this->Arr_Parcels[$Arr_NewFields[$Str_Name]['ref']]->set_values(array($Str_Name => $Mix_Data));
			}
			else
			{
				$Arr_NewFields[$Str_Name]['value'] = array(false);
			}
		}

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		//Validate form fields.
		$Bol_FormValid = $this->validate($Arr_ValidateFields, false, true, $Arr_SetFields);

		//Set fields values to model.
		//*!*Not sure if I should put filtering in here. Best to leave it to db gets.
		foreach ($Arr_SetFields as $Str_Name)
		{
			if (isset($Arr_NewFields[$Str_Name]['ref']) && isset($Arr_DataSet[$Str_Name]))
			{
//*!*this is the block of code which i have editied so that the differnet naming convention will work across a multi-parcel form object
//I believe that there is a sister routine higher up which is hn=handling the referenced object but I am not sure without studying it atm
				$this->Arr_Parcels[$Arr_NewFields[$Str_Name]['ref']]->set_values(array($Arr_NewFields[$Str_Name]['property'] => $Arr_DataSet[$Str_Name]));
			}
		}

		//Flag form as being processed.
		$this->Bol_Processed = true;

		return $Bol_FormValid;
	}

	//Constructs a set of values if the validation rule position is a reference to a field name.
	// - $Str_RulePosition:			Validation rule array position
	// * Return:					values of field if a reference is given, otherwise the rule position
	public function construct_values($Str_RulePosition)
	{
		$Mix_Values = $Str_RulePosition;

		//If there is a match to a field value token get it's value.
		if (preg_match($this->Reg_FieldNameVar, $Str_RulePosition, $Arr_ValueMatches))
		{
			//If the variable has an array marker with no defined values get all values.
			if (isset($Arr_ValueMatches[2]) && $Arr_ValueMatches[2] == '')
			{
				$Mix_Values = $this->Arr_Fields[$Arr_ValueMatches[1]]['value'];
			}
			//Else if the variable has values defined get them.
			else if (isset($Arr_ValueMatches[3]) && $Arr_ValueMatches[3])
			{
				$Mix_Values = array();
				$Arr_ValidateValues = explode(',', $Arr_ValueMatches[3]);
				foreach ($this->Arr_Fields[$Arr_ValueMatches[1]]['value'] as $Mix_Key => $Var_Value)
				{
					if (in_array($Var_Value, $Arr_ValidateValues))
						$Mix_Values[$Mix_Key] = $Var_Value;
				}
			}
			//Else if the value is set get the first value.
			elseif (isset($this->Arr_Fields[$Arr_ValueMatches[1]]['value'][0]))
			{
				$Mix_Values = $this->Arr_Fields[$Arr_ValueMatches[1]]['value'][0];
			}
			//Otherwise set the non-existing value to false.
			else
			{
				$Mix_Values = false;
			}
		}

		return $Mix_Values;
	}

	//Gets field values of the field references within a validation rule, while setting validation ethod by reference.
	// - $Str_FieldName:		Name of field with validation rule being constructed
	// - $Arr_ValidationRule:	Validation rule set to the field being evaluated.
	// - $Str_ValidateMethod:	Reference to the validation method, set within this function
	// * Return:				Set or paramters to be supplied to validation method, false if method is not a string
	// NB: This is refactored out of validate() for flexibility which, means it's not a simple in/out job.
	//*!*I might change the variable handling when making a validation method call to make this funciton a little siimpler to follow.
	//I think have to define which set of values I need to pull off as optionals don't need the current field value to make evaluation.
	//But it might be cool if they could do that...
	public function construct_validation($Str_FieldName, $Arr_ValidationRule, &$Str_ValidateMethod)
	{
		$Str_ValidateMethod = '';
		$Arr_ValidateParamters = array();

		//Interpret all parameter values.
		$Int_MatchParameters = count($Arr_ValidationRule);
		for ($i = 0; $i < $Int_MatchParameters; $i++)
		{
			//Get validation rule values.
			$Mix_Values = $this->construct_values($Arr_ValidationRule[$i]);

			//Set method and field values as first parameter.
			if ($i == 0)
			{
				$Str_ValidateMethod = $Mix_Values;

				//If there are value set to the field add it as the first parameter.
				if (isset($this->Arr_Fields[$Str_FieldName]['value']))
					$Arr_ValidateParamters[] = $this->Arr_Fields[$Str_FieldName]['value'];
				//Otherwise set the first paramter to false.
				else
					$Arr_ValidateParamters[] = false;
			}
			//Construct additional validation method call parameters.
			else
			{
				$Arr_ValidateParamters[] = $Mix_Values;
			}
		}

		//If the validation method is not a string handle exception and return false.
		if (!is_string($Str_ValidateMethod))
		{
			global $CMD;
			$CMD->handle_exception('Validation method is not a string', 'MW:101');
			$Arr_ValidateParamters = false;
		}

		return $Arr_ValidateParamters;
	}

	//This is like the above function but makes a parameter set out of the referenced field values.
	//This means that I have to ensure that the reference points to an actual field, if not then I'll throw a false value back
	public function construct_option($Str_FieldName, $Arr_ValidationRule, &$Str_ValidateMethod)
	{
		$Str_ValidateMethod = '';
		$Arr_ValidateParamters = array();
		$Str_ValidateField = '';

		//Interpret all parameter values.
		$Int_MatchParameters = count($Arr_ValidationRule);
		for ($i = 0; $i < $Int_MatchParameters; $i++)
		{
			//Get validation rule values.
			$Mix_Values = $this->construct_values($Arr_ValidationRule[$i]);

			//If we are processing the first position set the name of the field to gets values for.
			if ($i == 0)
			{
				//If there are value set to the field add it as the first parameter.
				if (isset($this->Arr_Fields[$Arr_ValidationRule[$i]]['value']))
					$Arr_ValidateParamters[] = $this->Arr_Fields[$Arr_ValidationRule[$i]]['value'];
				//Otherwise set the first paramter to false.
				else
					$Arr_ValidateParamters[] = false;
			}
			//Else if we are processing the second position get the rule set validation method.
			elseif ($i == 1)
			{
				$Str_ValidateMethod = $Arr_ValidationRule[$i];
			}
			//Construct additional validation method call parameters.
			else
			{
				$Arr_ValidateParamters[] = $Mix_Values;
			}
		}

		//If the method is not a string handle exception.
		if (!is_string($Str_ValidateMethod))
		{
			global $CMD;
			$CMD->handle_exception('Validate option method not a string', 'MW:101');
			$Arr_ValidateParamters = false;
		}

		return $Arr_ValidateParamters;

	}
	
	//Returns a validation result against validaotrs loaded locally.
	// - $Str_Method:			Validation method to find and execute
	// - $Arr_Paramters:		Validation parameters to supply test method
	// * Return:				True if the rule is valid, otherwise false
	public function get_validation_result($Str_Method, $Arr_Paramters)
	{
		$Bol_ValidateResult = false;

		//Ensure parameters are an array.
		if (!is_array($Arr_Paramters))
			$Arr_Paramters = array();

		//Loop through all validation helpers set to form.
		$Bol_RuleTested = false;
		foreach ($this->Arr_Validators as $Obj_Validator)
		{
			//If helper has the validation method get call result.
			if (method_exists($Obj_Validator, $Str_Method))
			{
				$Bol_RuleTested = true;
				$Bol_ValidateResult = call_user_func_array(array($Obj_Validator, $Str_Method), $Arr_Paramters);

				break;
			}
		}

		//If the validation rule remains untested handle excepption.
		if (!$Bol_RuleTested)
		{
			global $CMD;
			$CMD->handle_exception('Validation rule '.$Str_Method.' not found', 'MW:101');
		}

		return $Bol_ValidateResult;
	}

	//Compiles valiator code swapping out field references with their values.
	// - $Mix_FieldNames:		Name of form fields to validate
	// - $Int_Validator:		Validation rule to test, default = false(all)
	// - $Bol_SetErrrors:		If true, sets errors locally, default = true(set errors)
	// - $Arr_SetFields:		Reference to array of fields to set values for
	// * Return:				True if tested validation rules are all valid, other false
	// NB: If $Int_Validator is not set each field validation test will exit of first error.
	public function validate($Mix_FieldNames, $Int_Validator=false, $Bol_SetErrrors=true, &$Arr_SetFields=false)
	{
		$Bol_Valid = true;

		//Get field setting array.
		if (!is_array($Arr_SetFields))
			$Arr_SetFields = array();

		//If only one field name is supplied convert it into an array.
		$Arr_FieldNames = array();
		if (is_string($Mix_FieldNames))
		{
			$Arr_FieldNames[] = $Mix_FieldNames;
		}
		//Else if it is an array set names to test array.
		elseif (is_array($Mix_FieldNames))
		{
			$Arr_FieldNames = $Mix_FieldNames;
		}
		//Otherwise handle exception.
		else
		{
			global $CMD;
			$CMD->handle_exception('Validate method requires paramter $Mix_FieldNames to be a string or an array', 'MW:101');
			//*!*This needs to be supplied as an exception value because it might be subject to change.
			return false;
		}

		//Decouple field information.
		$Arr_NewFields = $this->Arr_Fields;

		//Loop hrough each field set to the form and validate them.
		foreach ($Arr_NewFields as $Str_Name => $Arr_Field)
		{
			$Bol_OptionalValidate = true;

			//If the field is not required and no value is set remoe errors.
			//*!*This might need some tweaking depending upon what data structures exist in the value set
			if ((isset($Arr_NewFields[$Str_Name]['required']))
			&& ($Arr_NewFields[$Str_Name]['required'] == false))
			{
				if ((($Arr_NewFields[$Str_Name]['type'] == 'file')
				&& (!$Arr_NewFields[$Str_Name]['value']['size']))
				|| (!$Arr_NewFields[$Str_Name]['value']))
				{
					continue;
				}
			}

			//If the field is optional to validate get requirement.
			if (isset($Arr_NewFields[$Str_Name]['optional']))
			{
				foreach ($Arr_NewFields[$Str_Name]['optional'] as $Int_Optional => $Arr_OptionalRule)
				{
					if (!$Bol_OptionalValidate) break;

					//Build and test validation condition.
					//*!*Need to test that the validation parameters are supplied as an array and not a strigng.
					if (($Arr_ValidateParamters = $this->construct_option($Str_Name, $Arr_OptionalRule, $Str_ValidateMethod)) !== false)
						$Bol_OptionalValidate = $this->get_validation_result($Str_ValidateMethod, $Arr_ValidateParamters);
				}
			}

			//If the field is not optional include it to be set it to parcel data.
			if ($Bol_OptionalValidate)
				$Arr_SetFields[] = $Str_Name;

			//If the field is not optional and has validators test for valid data values.
			if ($Bol_OptionalValidate && isset($Arr_NewFields[$Str_Name]['validate']))
			{
				$Bol_FieldValid = true;
				foreach ($Arr_NewFields[$Str_Name]['validate'] as $Int_Validator => $Arr_ValidationRule)
				{
					//If the field has validation rules and is still valid.
					if (isset($Arr_ValidationRule['match']) && $Bol_FieldValid)
					{
						//If the validation rule cannot be properly constructed set field as invalid.
						if (($Arr_ValidateParamters = $this->construct_validation($Str_Name, $Arr_ValidationRule['match'], $Str_ValidateMethod)) === false)
						{
							$Bol_FieldValid = false;
						}
						//Otherwise get result of validation method call.
						else
						{
							$Bol_FieldValid = $this->get_validation_result($Str_ValidateMethod, $Arr_ValidateParamters);
						}

						//If the result is false set validation error message.
						if (!$Bol_FieldValid && $Bol_SetErrrors)
						{
							if (isset($Arr_NewFields[$Str_Name]['validate'][$Int_Validator]['error'])
							&& $Arr_NewFields[$Str_Name]['validate'][$Int_Validator]['error'])
								$Arr_NewFields[$Str_Name]['error'] = $Arr_NewFields[$Str_Name]['validate'][$Int_Validator]['error'];
							else
								$Arr_NewFields[$Str_Name]['error'] = 'Input value is invalid';
						}

						//If the field is invlaid stop validating field.
						if (!$Bol_FieldValid)
						{
							$Bol_Valid = false;
							break;
						}
					}
				}
			}

		}

		//Recouple field information.
		$this->Arr_Fields = $Arr_NewFields;

		return $Bol_Valid;
	}

	//Set an error string to the active field field.
	public function set_error($Str_ErrorMessage)
	{
		//Decouple field information.
		$Arr_NewFields = $this->Arr_Fields;

		//*!*This needs more work to check for the filed and maybe set form as invalid, this is hacky for now, no time!
		$Arr_NewFields[$this->Arr_Active['field']]['error'] = $Str_ErrorMessage;

		//Recouple field information.
		$this->Arr_Fields = $Arr_NewFields;
	}
	


///////////////////////////////////////////////////////////////////////////////
//            F O R M   P R O P E R T Y   F U N C T I O N S                  //
///////////////////////////////////////////////////////////////////////////////

	//Sets or gets the name of the form.
	// - $Str_FormName:			Name to set the form, default = ''(get form name)
	// * Return:				SELF if setting form name, otherwise form name value
	public function name($Str_FormName='')
	{
		if ($Str_FormName)
		{
			$Arr_NewSubmit = $this->Arr_Submit;
			$Arr_NewSubmit['name'] = $Str_FormName;
			$this->Arr_Submit = $Arr_NewSubmit;
			return $this;
		}
		else
		{
			return $this->Arr_Submit['name'];
		}
	}

	//Sets ir gets the submit action of the form.
	// - $Str_FormAction:		Submit action to set to the form, default = ''(get form action)
	// * Return:				SELF if setting form action, otherwsise form action value
	public function action($Str_FormAction='')
	{
		if ($Str_FormAction)
		{
			$Arr_NewSubmit = $this->Arr_Submit;
			$Arr_NewSubmit['action'] = $Str_FormAction;
			$this->Arr_Submit = $Arr_NewSubmit;
			return $this;
		}
		else
		{
			return $this->Arr_Submit['action'];
		}
	}

	//Sets the submit method of the form.
	// - $Str_FormMethod:		Submit method to set to the form, default = ''(get form method)
	// * Return:				SELF if setting submit method, otherwise method value
	public function method($Str_FormMethod='')
	{
		if ($Str_FormMethod)
		{
			$Arr_NewSubmit = $this->Arr_Submit;
			$Arr_NewSubmit['method'] = $Str_FormMethod;
			$this->Arr_Submit = $Arr_NewSubmit;
			return $this;
		}
		else
		{
			return $this->Arr_Submit['method'];
		}
	}

	//Sets the data encoding type for the form.
	// - $Str_FormEncoding:		Data encoding type to set to the form, default = ''(get form encoding)
	// * Return:				SELF if setting encoding type, otherwise encoding value
	//*!*Not sure how I'll deal with AJAX yet.
	public function encoding($Str_FormEncoding='')
	{
		if ($Str_FormEncoding)
		{
			$Arr_NewSubmit = $this->Arr_Submit;
			$Arr_NewSubmit['encoding'] = $Str_FormEncoding;
			$this->Arr_Submit = $Arr_NewSubmit;
			return $this;
		}
		else
		{
			return $this->Arr_Submit['encoding'];
		}
	}


///////////////////////////////////////////////////////////////////////////////
//           I N P U T   P R O P E R T Y   F U N C T I O N S                 //
///////////////////////////////////////////////////////////////////////////////

	//Sets type property on active field.
	// - $Str_InputType:		Type to set field as
	// * Return:				SELF
	public function type($Str_InputType)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;

		$Arr_NewFields[$this->Arr_Active['field']]['type'] = $Str_InputType;

		//If the type is file set encoding to multipart
		if ($Str_InputType == 'file')
			$this->encoding('multipart/form-data');

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//Sets range of values for a multiple value field.
	// - $Arr_FieldValues:		Key array of values to assign to active field
	// - $Bol_AddValues:		If true values will be added to range, default = false(values are overwritten)
	// - $Bol_ResetValues:		Removes all the values which have been added to the range before adding the new values
	// * Return:				SELF
	public function range($Arr_FieldValues, $Bol_AddValues=false, $Bol_ResetValues=false)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;
		
		//Clear values.
		if ($Bol_ResetValues)
		{
			$Arr_NewFields[$this->Arr_Active['field']]['range'] = array();
		}

		//If we are adding values do so.
		if ($Bol_AddValues)
		{
			foreach($Arr_FieldValues as $Str_Key => $Str_Value)
			{
				$Arr_NewFields[$this->Arr_Active['field']]['range'][$Str_Key] = $Str_Value;
			}
		}
		//Otherwise replace range with new values.
		else
		{
			$Arr_NewFields[$this->Arr_Active['field']]['range'] = $Arr_FieldValues;
		}

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//Sets the form display value.
	// - $Mix_DisplayValue:		Scalar value or value array to set to the active input.
	// * Return:				SELF
	// NB: Best used for unsubmitted forms where default(initial()) is not appropriate
	//*!* I need to go back and figure out what the difference between this funciton and initial() is.
	public function display($Mix_DisplayValue)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;

		if (is_scalar($Mix_DisplayValue))
		{
			$Arr_NewFields[$this->Arr_Active['field']]['value'][0] = $Mix_DisplayValue;
		}
		elseif (is_array($Mix_DisplayValue))
		{
			$Arr_NewFields[$this->Arr_Active['field']]['value'] = $Mix_DisplayValue;
		}

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//Sets initial default value on active field.
	// * Return:				SELF
	public function initial($Mix_DefaultValues)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;

		//If the initial value is a scalar value set it to first position.
		if (is_scalar($Mix_DefaultValues))
		{
			$Arr_NewFields[$this->Arr_Active['field']]['initial'][0] = $Mix_DefaultValues;
		}
		//Else if it is an array add those values.
		elseif(is_array($Mix_DefaultValues))
		{
			//Loop through each value an replace any found or, add values not found.
			//*!*This could produce problems with type mixing, I'll need to keep my eye on this function and seee what comes of it.
			foreach ($Mix_DefaultValues as $Var_Value)
			{
				if (($Str_ValueKey = array_search($Var_Value, $Arr_NewFields[$this->Arr_Active['field']]['initial'])) !== false)
				{
					$Arr_NewFields[$this->Arr_Active['field']]['initial'][$Str_ValueKey] = $var_Value;
				}
				else
				{
					$Int_Values = count($Arr_NewFields[$this->Arr_Active['field']]['initial']);
					$Arr_NewFields[$this->Arr_Active['field']]['initial'][$Int_Values] = $Var_Value;
				}
			}
		}
		//Else if the value isn't null handle exception.
		elseif (!is_null($Mix_DefaultValues))
		{
			global $CMD;
			$CMD->handle_exception('Form field '.$this->Arr_Active['field'].' not set to be matched', 'MW:101');
		}

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//Assigns the active field to be required to match the field $Str_MatchedField on validation
	// - $Str_MatchedField:		Name of field the active input is required to match
	// * Return:				SELF
	//*!*I think this is moreor less depriciated with references in validation
	public function match($Str_MatchedField)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;

		//If the matching field does not exist handle exception.
		if (!isset($Arr_NewFields[$Str_MatchedField]))
		{
			global $CMD;
			$CMD->handle_exception('Form field '.$Str_MatchedField.' not set to be matched', 'MW:101');
		}

		//Set matching field.
		$Arr_NewFields[$this->Arr_Active['field']]['match'] = $Str_MatchedField;

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//Sets field requirement.
	// - $Bol_Required			Sets whether the field is required, default = true
	// * Return:				SELF
	public function required($Bol_Required=true)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;

		if ($Bol_Required)
		{
			$Arr_NewFields[$this->Arr_Active['field']]['required'] = true;
		}
		else
		{
			$Arr_NewFields[$this->Arr_Active['field']]['required'] = false;
		}

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//*!*Need to be able to add validation rules to field, this is what this function does.
	public function rule($Arr_Conditions)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;

		//Ensure that the validation rule is properly wrapped.
		if (!isset($Arr_Conditions[0]) || !is_array($Arr_Conditions[0]))
			$Arr_Conditions = array($Arr_Conditions);

		//Get any existing validation rules.
		$Arr_NewConditions = array();
		if (isset($Arr_NewFields[$this->Arr_Active['field']]['validate']))
			$Arr_NewConditions = $Arr_NewFields[$this->Arr_Active['field']]['validate'];

		//Loop through all rules and add them to the active field.
		foreach ($Arr_Conditions as $Arr_Condition)
		{
			//*!*Need to test that the condition is valid.

			$Arr_NewConditions[] = $Arr_Condition;
		}

		$Arr_NewFields[$this->Arr_Active['field']]['validate'] = $Arr_NewConditions;

//var_dump($Arr_NewFields);exit;
		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//Sets active field conditions for optional validation.
	// - $Arr_Optionals:			Condition optionals for validation
	// * Return:					SELF
	// * NB: Field will not run validation tests untill optional conditions are met.
	public function opt($Arr_Optionals)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;

		//Ensure that the conditions are properly wrapped.
		if (!isset($Arr_Optionals[0]) || !is_array($Arr_Optionals[0]))
			$Arr_Optionals = array($Arr_Optionals);

		//Get any existing validation options.
		$Arr_NewOptionals = array();
		if (isset($Arr_NewFields[$this->Arr_Active['field']]['optional']))
			$Arr_NewOptionals = $Arr_NewFields[$this->Arr_Active['field']]['optional'];

		//Loop through all conditions and add them to the active field.
		foreach ($Arr_Optionals as $Arr_Option)
		{
			//*!*Need to test that the option is valid.

			$Arr_NewOptionals[] = $Arr_Option;
		}

		$Arr_NewFields[$this->Arr_Active['field']]['optional'] = $Arr_NewOptionals;

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//Adds a filter to the active field.
	// - $Arr_Filters:			Filter to add to the active field
	// * Return:				SELF
	// * NB: This function will replace an existing filter with the same key.
	public function filters($Arr_Filters)
	{
		//Decouple field values.
		$Arr_NewFields = $this->Arr_Fields;

		//If the filter exists.
		foreach ($Arr_Filters as $Str_FilterRule => $Str_ErrorMessage)
		{
			$Arr_NewFields[$this->Arr_Active['field']]['filter'][$Str_FilterRule] = $Str_ErrorMessage;
		}

		//Recouple field values.
		$this->Arr_Fields = $Arr_NewFields;

		return $this;
	}

	//Wrapper of $this->set_field_attributes(name, array(Str_AttributeName=>$Str_AttributeValue))
	// * NB: This function will handle any additional attributes the above functions don't handle.
	//*!*This function will overrides all existing values, and needs to offer the nesting as a parameter, eg neest1|nest2
	// * NB: if Str_AttributeValue is false then remove this attribute.
	public function attribute($Str_AttributeName, $Str_AttributeValue)
	{
		//If attribute value is not scalar handle exception.
		if (!is_scalar($Str_AttributeValue))
		{
			global $CMD;
			$CMD->handle_exception('Form attribute value is not scalar', 'MW:101');
			$Str_AttributeValue = '';
		}

		//If the attribute value is false then remove it from the form element.
		if ($Str_AttributeValue === false)
		{
			$this->set_field_attribute($this->Arr_Active['field'], $Str_AttributeName, false);
		}
		//Otherwise set the attribute to the element.
		else
		{
			$this->set_field_attribute($this->Arr_Active['field'], $Str_AttributeName, strval($Str_AttributeValue));
		}

		return $this;
	}



///////////////////////////////////////////////////////////////////////////////
//                  B U I L D   A P I   F U N C T I O N S                    //
///////////////////////////////////////////////////////////////////////////////

	//Builds input markup {$form.input(name).class(text)}
	// - $Arr_ArgList[0]:		Name of field to retrieve string for.
	// * Return:				Input markup as a string
	public function get_input_string($Arr_ArgList)
	{
		$Str_Input = '';

		//If field type hasn't been set handle error.
		if (!isset($this->Arr_Fields[$Arr_ArgList[0]]['type']) || !$this->Arr_Fields[$Arr_ArgList[0]]['type'])
		{
			global $CMD;
			$CMD->handle_exception('Field '.$Arr_ArgList[0].' has no type defined', 'MW:100');

			return $Str_Input;
		}

		//Build input by type.
		switch ($this->Arr_Fields[$Arr_ArgList[0]]['type'])
		{
			case 'text': $Str_Input = $this->input_text($Arr_ArgList[0]); break;
			case 'textarea': $Str_Input = $this->input_textarea($Arr_ArgList[0]); break;
			case 'password': $Str_Input = $this->input_password($Arr_ArgList[0]); break;
			case 'checkbox': $Str_Input = $this->input_checkbox($Arr_ArgList[0]); break;
			case 'radio': $Str_Input = $this->input_radio($Arr_ArgList[0]); break;
			case 'button': $Str_Input = $this->input_button($Arr_ArgList[0]); break;
			case 'image': $Str_Input = $this->input_image($Arr_ArgList[0]); break;
			case 'select': $Str_Input = $this->input_select($Arr_ArgList[0]); break;
			case 'hidden': $Str_Input = $this->input_hidden($Arr_ArgList[0]); break;
			case 'file': $Str_Input = $this->input_file($Arr_ArgList[0]); break;
			case 'reset': $Str_Input = $this->get_reset_string(); break;
			case 'submit': $Str_Input = $this->get_submit_string(); break;
			default: global $CMD; $CMD->handle_exception('Input '.$Arr_ArgList[0].' type is not valid', 'MW:100');
		}

		return $Str_Input;
	}

	//Builds field value as a text string. {$form.text(name|element)}
	// - $Arr_ArgList[0]:		Name of field to retrieve value for
	// * Return:				Field value as a markup string
	//*!*I'm going to come back to this to put the wrapper in.
	//I'm also going to have to deal with fields that have more than one value.
	//So atm this is just a quick hack to get this functioning :)
	//Second argument might need to be a filter to convert input to a markup string.
	//I also need to add the sisther API function for this $this->text('name');
	public function get_text_string($Arr_ArgList)
	{
		$Str_ValueText = '';

		$Str_ValueText = $this->Arr_Fields[$Arr_ArgList[0]]['value'][0];//HAHA!

		return $Str_ValueText;
	}

	//Gets list of range values for field.
	// - $Arr_ArgList[0]:		Name of field to retrieve values for
	// * Return:				Key/value array of $Arr_ArgList[0] field's range
	public function get_field_range($Arr_ArgList)
	{
		$Arr_Values = array();

		//If the field has a range get it.
		if (isset($this->Arr_Fields[$Arr_ArgList[0]]['range']) && $this->Arr_Fields[$Arr_ArgList[0]]['range'])
		{
			foreach($this->Arr_Fields[$Arr_ArgList[0]]['range'] as $Str_Value => $Str_Display)
			{
				$Arr_Values[] = $Str_Value;
			}
		}

		return $Arr_Values;
	}

	//Builds a list of form field names. {$form.names()}
	// NB: Useful for building a form inside a loop
	/* EG
	<ul>
	{each $form.names() as $name}
	<li>{$form.label($name)}{$form.error($name)}{$form.input($name)}</li>
	{/each}
	</ul>
	*/
	//Need a parameter set to filter hidden(and maybe other properties) inputs
	// i: - include only
	// e: - exclude all
	// h - hidden
	// d - disabled
	public function get_field_names($Arr_ArgList)
	{
		return $Arr_FieldNames;
	}

	//API wrapper function for the callback helper method.
	// * Return:				Output of currently set callbacks
	public function build()
	{
		return $this->callback();
	}

	public function input($Str_FieldName)
	{
		$this->call('input', $Str_FieldName);
		return $this;
	}

	public function label($Str_FieldName, $Str_LabelText=false)
	{
		$Str_Parameters = ($Str_LabelText !== false)? $Str_FieldName.'|'.$Str_LabelText: $Str_FieldName;
		$this->call('label', $Str_Parameters);
		return $this;
	}

	public function error($Str_FieldName, $Str_WrapperElement=false)
	{
		$Str_Parameters = ($Str_WrapperElement !== false)? $Str_FieldName.'|'.$Str_WrapperElement: $Str_FieldName;
		$this->call('error', $Str_Parameters);
		return $this;
	}

	public function attr($Str_AttributeName, $Str_AttributeValue)
	{
		$this->call($Str_AttributeName, $Str_AttributeValue);
		return $this;
	}

	public function submit($Str_InputValue)
	{
		$this->call('submit', $Str_InputValue);
		return $this;
	}

	public function reset($Str_InputValue)
	{
		$this->call('submit', $Str_InputValue);
		return $this;
	}

	public function open()
	{
		$this->call('open', '');
		return $this;
	}

	public function close()
	{
		$this->call('close', '');
		return $this;
	}
	
	//Returns the names of fields that have an error.
	//*!*This will be used to do error display control logic
	// - $Str_FieldName:		Name of field to find error for
	// * NB: If no field is defiened return a list of error fields otherwise give back a bool answer on field
	public function errors($Str_FieldName=false)
	{
		//Loop through all the fields and find ones with errors in them.
		$Arr_Errors = array();
		foreach ($this->Arr_Fields as $Arr_Field)
		{
			if (isset($Arr_Field['error']) && $Arr_Field['error'])
			{
				$Arr_Errors[$Arr_Field['name']] = $Arr_Field['error'];
			}
		}

		return $Arr_Errors;
	}

	//Handles miscellaneous functions applied to the form object.
	// - $Str_Function:			Name of the function
	// - $Str_Paramters:		Build paramter string
	// * Return:				VOID
	// * NB: This method must return VOID
	public function call($Str_Function, $Str_Paramters)
	{
		global $CMD;

		//*!*I might define this separator as a constant which means I'll have to change it in all helpers plus the builder.
		$Arr_Paramters = explode(MW_HOOK_CHAR_SEPARATOR, $Str_Paramters);

		//Decouple active values.
		$Arr_NewActive = $this->Arr_Active;

		//Make build function call.
		switch ($Str_Function)
		{
			case 'open':
				$Arr_NewActive['field'] = 'form';
				$this->add_callback('get_open_string', $Str_Paramters);
				break;

			case 'close':
				$Arr_NewActive['field'] = '';
				$this->add_callback('get_close_string', $Str_Paramters);
				break;

			case 'submit':
				$Arr_NewActive['field'] = 'submit';
				$this->add_callback('get_submit_string', $Str_Paramters);
				break;

			case 'reset';
				$Arr_NewActive['field'] = 'reset';
				$this->add_callback('get_reset_string', $Str_Paramters);
				break;

			case 'input':
				if (!isset($Arr_Paramters[0]) || !$Arr_Paramters[0])
					$CMD->handle_exception('Input call argument not valid, field name required', 'MW:100');
				elseif (!isset($this->Arr_Fields[$Arr_Paramters[0]]) || !$this->Arr_Fields[$Arr_Paramters[0]])
					$CMD->handle_exception('Input '.$Arr_Paramters[0].' not set to form object', 'MW:100');

				$Arr_NewActive['field'] = $Arr_Paramters[0];
				$Arr_NewActive['element'] = 'input';
				$this->add_callback('get_input_string', $Str_Paramters);
				break;

			case 'label':
				if (!isset($Arr_Paramters[0]) || !$Arr_Paramters[0])
					$CMD->handle_exception('Label call argument not valid, field name required', 'MW:100');
				elseif (!isset($this->Arr_Fields[$Arr_Paramters[0]]) || !$this->Arr_Fields[$Arr_Paramters[0]])
					$CMD->handle_exception('Label '.$Arr_Paramters[0].' not set to form object', 'MW:100');

				if ($Arr_NewActive['field'] != $Arr_Paramters[0])
					$Arr_NewActive['value'] = '';

				$Arr_NewActive['field'] = $Arr_Paramters[0];
				$Arr_NewActive['element'] = 'label';
				$this->add_callback('get_label_string', $Str_Paramters);
				break;

			case 'error':
				if (!isset($Arr_Paramters[0]) || !$Arr_Paramters[0])
					$CMD->handle_exception('Error call argument not valid, field name required', 'MW:100');
				elseif (!isset($this->Arr_Fields[$Arr_Paramters[0]]) || !$this->Arr_Fields[$Arr_Paramters[0]])
					$CMD->handle_exception('Error '.$Arr_Paramters[0].' not set to form object', 'MW:100');

				if ($Arr_NewActive['field'] != $Arr_Paramters[0])
					$Arr_NewActive['value'] = '';

				$Arr_NewActive['field'] = $Arr_Paramters[0];
				$Arr_NewActive['element'] = 'error';
				$this->add_callback('get_error_string', $Str_Paramters);
				break;

			case 'value':
				if (!isset($Arr_Paramters[0]) || !$Arr_Paramters[0])
					$CMD->handle_exception('Value call argument not valid, field value required', 'MW:100');

				$Arr_NewActive['value'] = $Arr_Paramters[0];
				break;

			case 'text';
				if (!isset($Arr_Paramters[0]) || !$Arr_Paramters[0])
					$CMD->handle_exception('Text call argument not valid, field value required', 'MW:100');

				if ($Arr_NewActive['field'] != $Arr_Paramters[0])
					$Arr_NewActive['value'] = '';

				$this->add_callback('get_text_string', $Str_Paramters);
				break;

			case 'filter':
				if (!isset($Arr_Paramters[0]) || !$Arr_Paramters[0])
					$CMD->handle_exception('Filter call argument not valid, filter method required', 'MW:100');

				//Not sure that this is going here, though it would be handy to use filters inside of a template.

			case 'field':
				if (!isset($Arr_Paramters[0]) || !$Arr_Paramters[0])
					$CMD->handle_exception('Field call argument not valid, field value required', 'MW:100');
				elseif (!isset($this->Arr_Fields[$Arr_Paramters[0]]) || !$this->Arr_Fields[$Arr_Paramters[0]])
					$CMD->handle_exception('Field '.$Arr_Paramters[0].' not set to form object', 'MW:100');

				if ($Arr_NewActive['field'] != $Arr_Paramters[0])
					$Arr_NewActive['value'] = '';

				$Arr_NewActive['field'] = $Arr_Paramters[0];
				break;

			case 'range':
				if (!isset($Arr_Paramters[0]) || !$Arr_Paramters[0])
					$CMD->handle_exception('Range call argument not valid, field name required', 'MW:100');
				elseif (!isset($this->Arr_Fields[$Arr_Paramters[0]]) || !$this->Arr_Fields[$Arr_Paramters[0]])
					$CMD->handle_exception('Range '.$Arr_Paramters[0].' not set to form object', 'MW:100');

				$Arr_NewActive['field'] = $Arr_Paramters[0];
				$this->add_callback('get_field_range', $Str_Paramters);
				break;

			case 'names':
				$this->add_callback('get_field_names', $Str_Paramters);
				break;

			default:
				//*!*What I might do here is add default builder string functions which would be called as a helper function.
				$Arr_NewFieldAttributes = $this->Arr_Fields;
				$Arr_NewFieldAttributes[$this->Arr_Active['field']]['attributes'][$Str_Function] = $Str_Paramters;
				$this->Arr_Fields = $Arr_NewFieldAttributes;
		}

		//Recouple active values.
		$this->Arr_Active = $Arr_NewActive;

		return;
	}

	//Sets active value $Str_Value to active field.
	// - $Str_Value:				Value to set as active
	// * Return:					SELF
	public function value($Str_Value)
	{
		$Arr_NewActive = $this->Arr_Active;
		$Arr_NewActive['value'] = $Str_Value;
		$this->Arr_Active = $Arr_NewActive;

		return $this;
	}

	//*!*This needs the same work as get_text_string() does.
	//*!*I really don't like this name, I'd rather use something along the lines of value, might have to do some juggling.
	public function text($Str_FieldName)
	{
		$Str_ValueText = '';

		if (isset($this->Arr_Fields[$Str_FieldName]['value'][0]))
			$Str_ValueText = $this->Arr_Fields[$Str_FieldName]['value'][0];

		return $Str_ValueText;

	}

	//*!*This is like text() but gets all values not just the first value. Not suree about the method name too similar to value()
	// - $Str_FieldName:		Name of field to get vlaues for.
	//*!*I also need a function to get all field values as an array so I can stack them into a display template more easily, this might be the no-paramewter option
	public function values($Str_FieldName)
	{
		$Arr_ValueSet = array();

		if (isset($this->Arr_Fields[$Str_FieldName]['value']))
			$Arr_ValueSet = $this->Arr_Fields[$Str_FieldName]['value'];

		return $Arr_ValueSet;
	}

	//Runs a flter on the active value.
	// - $Str_FitlerMethod:			Filter method to run from local filters.
	// * Return:					VOID
	//*!*Just a note, I'm not saving this filter result to the data array, should put in a param to do this?
	//*!*I also need a function which runs a 'bulk' filter on all field values, so I don;t have to do them individualy.
	public function filter($Str_FilterMethod)
	{
		//Decouple active values.
		$Arr_NewFields = $this->Arr_Fields;

		//Look thorough each filter object set locally to find the filter method.
		$Bol_FilterRun = false;
		foreach ($this->Arr_Filters as $Obj_Filter)
		{
			//If helper has the filter method call it.
			if (method_exists($Obj_Filter, $Str_FilterMethod))
			{
				$Bol_FilterRun = true;

				//Get filtered value.
				//*!*I might want to add more paramters here... Not sure now simple is best.
				$Arr_MethodParamters = array(&$Arr_NewFields[$this->Arr_Active['field']]['value']);
				$Mix_FilteredValue = call_user_func_array(array($Obj_Filter, $Str_FilterMethod), $Arr_MethodParamters);

				//Set filtered data value to field.
				$Arr_NewFields[$this->Arr_Active['field']]['value'] = $Mix_FilteredValue;

				//Set data value to parcel.
				if (isset($Arr_NewFields[$this->Arr_Active['field']]['ref']))
					//*!* $Mix_FilteredValue[0] is a quick hack. I need to deal with the different formats of the two arrays differently.
					$this->Arr_Parcels[$Arr_NewFields[$this->Arr_Active['field']]['ref']]->set_values(array($this->Arr_Active['field'] => $Mix_FilteredValue[0]));

				break;
			}
		}

		//If the filter was not run handle exception.
		if (!$Bol_FilterRun)
		{
			global $CMD;
			$CMD->handle_exception('Filter '.$Str_FilterMethod.' not run, method not found on form filters', 'MW:101');
		}

		//Recouple active values.
		$this->Arr_Fields = $Arr_NewFields;

		return;
	}


///////////////////////////////////////////////////////////////////////////////
//                     M A R K U P   F U N C T I O N S                       //
///////////////////////////////////////////////////////////////////////////////

	//Builds field label markup.
	// - $Arr_ArgList[0]:		Name of field to retrieve string for.
	// - $Arr_ArgList[1]:		Label display string('' = no text)
	//  Return:					Label markup as a string
	//*!*I want arg no 3 to be the language.
	public function get_label_string($Arr_ArgList)
	{
		global $CMD;
		$Str_FieldName = $Arr_ArgList[0];
		$Str_LabelText = (isset($Arr_ArgList[1]))? $Arr_ArgList[1]: '';

		//Get multiple select value.
		$Str_FieldValue = '';
		if ($this->Arr_Active['value'])
			$Str_FieldValue = '-'.$this->Arr_Active['value'];
		elseif ($this->Arr_Active['value'] == '0')
			$Str_FieldValue = '-0';

		$Str_FieldLabel = '<label for="'.$Str_FieldName.$Str_FieldValue.'"'.$this->get_field_attribute_as_string($Str_FieldName, 'class').'>'.$CMD->lang($Str_LabelText).'</label>';

		return $Str_FieldLabel;
	}

	//Builds field error message markup.
	// - $Arr_ArgList[0]:		Name of field to get error string for
	// - $Arr_ArgList[1]:		Wrapper element to place around error message, default=''(no wrapper)
	// - $Arr_ArgList[2]:		Language to display error message, default=false(use config language)
	// * Return:				Label markup as a string
	public function get_error_string($Arr_ArgList)
	{
		global $CMD;

		//If the argument list is not valid handle exception
		if (!isset($Arr_ArgList[0]) || !$Arr_ArgList[0])
		{
			$CMD->handle_exception('Label callback argument not valid, field name required', 'MW:100');

			return '';
		}

		$Str_FieldName = $Arr_ArgList[0];
		$Str_ErrorWrapper = (isset($Arr_ArgList[1]))? $Arr_ArgList[1]: '';
		$Str_Error = '';

		//If field by name hasn't been set handle error.
		if (!isset($this->Arr_Fields[$Str_FieldName]) || !$this->Arr_Fields[$Str_FieldName])
		{
			$CMD->handle_exception('Input '.$Str_FieldName.' not set to form object', 'MW:100');
		}
		//Else if there is an error message get it.
		elseif (isset($this->Arr_Fields[$Str_FieldName]['error']) && $this->Arr_Fields[$Str_FieldName]['error'])
		{
			//Get the wrapper.
			$Str_WrapperOpen = ($Str_ErrorWrapper)? '<'.strtolower($Str_ErrorWrapper).$this->get_all_field_attributes_as_string($Str_FieldName).'>':'';
			$Str_WrapperClose = ($Str_ErrorWrapper)? '</'.strtolower($Str_ErrorWrapper).'>':'';

			//Get error locallity.
			$Str_Language = (isset($Arr_ArgList[2]) && $Arr_ArgList[2])? $Arr_ArgList[2]: false;
			$Str_Error = $CMD->lang($this->Arr_Fields[$Str_FieldName]['error'], $Str_Language);

			$Str_Error = $Str_WrapperOpen.$Str_Error.$Str_WrapperClose;
		}

		return $Str_Error;
	}

	//Gets the form submit button markup.
	// - $Arr_ArgList:			Builder argument string, default=''(keep display value)
	// * Return:				Submit button markup string
	public function get_submit_string($Arr_ArgList='')
	{
		global $CMD;

		//If there is a paramter set it as the field's display value.
		if (isset($Arr_ArgList[0]) && $Arr_ArgList[0] && is_string($Arr_ArgList[0]))
			$this->display($Arr_ArgList[0]);

		//Get submit display value.
		$Str_InputValue = 'Submit';
		if (isset($this->Arr_Fields['submit']['value'])
		&& $this->Arr_Fields['submit']['value'][0])
		{
			$Str_InputValue = $this->Arr_Fields['submit']['value'][0];
		}

		$Str_FormSubmit = '<input type="submit" name="submit" id="submit"'.$this->get_all_field_attributes_as_string('submit').' value="'.$CMD->lang($Str_InputValue).'" />';

		return $Str_FormSubmit;
	}

	//Gets the form reset button markup.
	// - $Arr_ArgList:			Builder argument string, default=''(keep display value)
	// * Return:				Reset button markup string
	public function get_reset_string($Arr_ArgList='')
	{
		global $CMD;

		//If there is a paramter set it as the field's display value.
		if (isset($Arr_ArgList[0]) && $Arr_ArgList[0] && is_string($Arr_ArgList[0]))
			$this->display($Arr_ArgList[0]);

		//Get submit display value.
		$Str_InputValue = 'Reset';
		if (isset($this->Arr_Fields['reset']['value'])
		&& $this->Arr_Fields['reset']['value'][0])
		{
			$Str_InputValue = $this->Arr_Fields['reset']['value'][0];
		}

		$Str_FormReset = '<input type="reset" name="reset" id="submit"'.$this->get_all_field_attributes_as_string('reset').' value="'.$CMD->lang($Str_InputValue).'" />';

		return $Str_FormReset;
	}

	//Gets the form's opening markup string.
	// * Return:				Markup string of form's opening
	//*!*This function needs to test for non-required attributes and throw exceptions if required attributes are fucked.
	public function get_open_string()
	{
		//*!*Same old thing required, I need to test parameters and supply them to the open fucntion
		//After we do want forms to have additional attributes.

		//If there are files, add upload input.
		$Str_MaxFileSize = '';
		if ($this->Arr_Submit['encoding'] == 'multipart/form-data')
		{
			//This needs to come from the config file.
			$Int_MaxFileSize = 1000000;
			$Str_MaxFileSize = '
<input type="hidden" name="MAX_FILE_SIZE" value="'.$Int_MaxFileSize.'" />';
		}

		$Str_FormOpen = '<form name="'.$this->Arr_Submit['name'].'" id="'.$this->Arr_Submit['name'].'" action="'.$this->Arr_Submit['action'].'" method="'.$this->Arr_Submit['method'].'" enctype="'.$this->Arr_Submit['encoding'].'"'.$this->get_all_field_attributes_as_string('form').'>
<input type="hidden" name="form" id="form" value="'.$this->Arr_Submit['name'].'" />'.$Str_MaxFileSize;

		return $Str_FormOpen;
	}

	//Gets the form's opening markup string.
	// * Return:				Markup string of form's opening
	public function get_close_string()
	{
		$Str_FormClose = '</form>';

		return $Str_FormClose;
	}

	//Builds a text input.
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	public function input_text($Str_FieldName)
	{
		$Str_TextInput = '';

		//Get field value.
		$Str_InputValue = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& $this->Arr_Fields[$Str_FieldName]['value'][0])
		{
			$Str_InputValue = $this->Arr_Fields[$Str_FieldName]['value'][0];
		}

		$Str_TextInput = '<input name="'.$Str_FieldName.'" id="'.$Str_FieldName.'" type="text"'.$this->get_all_field_attributes_as_string($Str_FieldName).' value="'.$Str_InputValue.'" />';

		return $Str_TextInput;
	}

	//Builds a select options input.
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	//*!*I need to be able to assign optgroup tags to this routine.
	public function input_select($Str_FieldName)
	{
		$Str_SelectInput = '';

		//If there is no defined range handle exception.
		if (!isset($this->Arr_Fields[$Str_FieldName]['range'])
		|| !is_array($this->Arr_Fields[$Str_FieldName]['range']))
		{
			global $CMD;
			$CMD->handle_exception('Form select input '.$Str_FieldName.' has no options assigned.', 'MW:100');
		}

		//Build input tag.
		$Str_SelectInput .= '<select name="'.$Str_FieldName.'" id="'.$Str_FieldName.'"'.$this->get_all_field_attributes_as_string($Str_FieldName, 0).'>';

		//Build select options set.
		$Str_SelectedClasses = $this->get_field_attribute_as_string($Str_FieldName, 'class', 1);
		foreach ($this->Arr_Fields[$Str_FieldName]['range'] as $Mix_FieldDisplay => $Mix_FieldValue)
		{
			$Str_Selected = '';
			$Str_OptionClass = '';
			if (isset($this->Arr_Fields[$Str_FieldName]['value'])
			&& in_array($Mix_FieldValue, $this->Arr_Fields[$Str_FieldName]['value']))
			{
				$Str_Selected = ' selected="selected"';

				if ($Str_SelectedClasses)
					$Str_OptionClass = $Str_SelectedClasses;
			}

			$Str_SelectInput .= '
<option value="'.$Mix_FieldValue.'"'.$Str_Selected.$Str_OptionClass.'>'.$Mix_FieldDisplay.'</option>';
		}

		$Str_SelectInput .= '
</select>';

		//Remove classes.
		$this->set_field_attribute($Str_FieldName, 'class', '' -1);

		return $Str_SelectInput;
	}

	//Builds a radio input
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	public function input_radio($Str_FieldName)
	{
		$Str_InputSuffix = '-'.$this->Arr_Active['value'];

		//If there is no field value assigned default to boolean.
		if ($this->Arr_Active['value'] === '')
		{
			$Arr_NewActive = $this->Arr_Active;
			$Arr_NewActive['value'] = 1;
			$this->Arr_Active = $Arr_NewActive;
			$Str_InputSuffix = '';
		}
/*
//*!*In this function I need to make split between a radio with no range and a radio with a range
//The radio without a range is treate similar to a text input, just follow and test the logic which is already here
//The radio with a range is handled much like a select option with the following excpetions
	I need to be able to add decorations to each input element,
		I'm thinking decorators should be a function
	I also need to be able to display partial sets of the radio input.
		This should be a universal function to be able to this on all inputs(grouping input sets for display.)
*/

		//Get checked field value.
		$Str_InputChecked = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& in_array($this->Arr_Active['value'], $this->Arr_Fields[$Str_FieldName]['value']))
		{
			$Str_InputChecked = ' checked="checked"';
		}

		//If there are more than two radio values handle exception.
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& count($this->Arr_Fields[$Str_FieldName]['value']) > 1)
		{
			global $CMD;
			$CMD->handle_exception('Radio input '.$Str_FieldName.' has'.count($this->Arr_Fields[$Str_FieldName]['value']).' values', 'MW:100');
		}

		$Str_RadioInput = '<input name="'.$Str_FieldName.'[]" id="'.$Str_FieldName.$Str_InputSuffix.'" type="radio"'.$Str_InputChecked.$this->get_all_field_attributes_as_string($Str_FieldName).' value="'.$this->Arr_Active['value'].'" />';

		return $Str_RadioInput;
	}

	//Builds a checkbox input.
	// - $Str_FieldName:		Name of field to build
	// - $Str_InputValue:		Value of the field to build
	// * Return:				Input markup as a string
	//*!*I need to test whether or not the field is boolean, IE whether it requires a value() call.
	//If the field is boolean then it's probably best that the input value is not treated as an array.
	public function input_checkbox($Str_FieldName)
	{
		$Str_InputSuffix = '-'.$this->Arr_Active['value'];

		//If there is no field value assigned default to boolean.
		if ($this->Arr_Active['value'] === '')
		{
			$Arr_NewActive = $this->Arr_Active;
			$Arr_NewActive['value'] = 1;
			$this->Arr_Active = $Arr_NewActive;
			$Str_InputSuffix = '';
		}

		//Get checked field value.
		$Str_InputChecked = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& in_array($this->Arr_Active['value'], $this->Arr_Fields[$Str_FieldName]['value']))
		{
			$Str_InputChecked = ' checked="checked"';
		}

		$Str_CheckboxInput = '<input name="'.$Str_FieldName.'[]" id="'.$Str_FieldName.$Str_InputSuffix.'" type="checkbox"'.$Str_InputChecked.$this->get_all_field_attributes_as_string($Str_FieldName).' value="'.$this->Arr_Active['value'].'" />';

		return $Str_CheckboxInput;
	}

	//Builds a button input.
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	// NB: This function builds an input tag of type button, not a button tag
	public function input_button($Str_FieldName)
	{
		//Get field value.
		$Str_InputValue = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& $this->Arr_Fields[$Str_FieldName]['value'][0])
		{
			$Str_InputValue = $this->Arr_Fields[$Str_FieldName]['value'][0];
		}

		$Str_ButtonInput = '<input name="'.$Str_FieldName.'" id="'.$Str_FieldName.'" type="button"'.$this->get_all_field_attributes_as_string($Str_FieldName).' value="'.$Str_InputValue.'" />';

		return $Str_ButtonInput;
	}

	//Builds a textarea input.
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	public function input_textarea($Str_FieldName)
	{
		//Get field value.
		$Str_InputValue = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& $this->Arr_Fields[$Str_FieldName]['value'][0])
		{
			$Str_InputValue = $this->Arr_Fields[$Str_FieldName]['value'][0];
		}

		$Str_TextareaInput = '<textarea name="'.$Str_FieldName.'" id="'.$Str_FieldName.'"'.$this->get_all_field_attributes_as_string($Str_FieldName).'>'.$Str_InputValue.'</textarea>';

		return $Str_TextareaInput;
	}

	//Builds a password input.
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	public function input_password($Str_FieldName)
	{
		//Get field value.
		$Str_InputValue = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& $this->Arr_Fields[$Str_FieldName]['value'][0])
		{
			$Str_InputValue = $this->Arr_Fields[$Str_FieldName]['value'][0];
		}

		$Str_TextInput = '<input name="'.$Str_FieldName.'" id="'.$Str_FieldName.'" type="password" '.$this->get_all_field_attributes_as_string($Str_FieldName).' value="'.$Str_InputValue.'" />';

		return $Str_TextInput;
	}

	//Builds a file input.
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	public function input_file($Str_FieldName)
	{
		//Get field value.
		$Str_InputValue = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& $this->Arr_Fields[$Str_FieldName]['value'])
		{
			$Str_InputValue = $this->Arr_Fields[$Str_FieldName]['value'];
		}

		$Str_FileInput = '<input name="'.$Str_FieldName.'" id="'.$Str_FieldName.'" type="file" '.$this->get_all_field_attributes_as_string($Str_FieldName).' value="'.$Str_InputValue.'" />';

		return $Str_FileInput;
	}

	//Builds a hidden input.
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	public function input_hidden($Str_FieldName)
	{
		//Get field value.
		$Str_InputValue = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& $this->Arr_Fields[$Str_FieldName]['value'][0])
		{
			$Str_InputValue = $this->Arr_Fields[$Str_FieldName]['value'][0];
		}

		$Str_HiddenInput = '<input name="'.$Str_FieldName.'" id="'.$Str_FieldName.'" type="hidden" '.$this->get_all_field_attributes_as_string($Str_FieldName).' value="'.$Str_InputValue.'" />';

		return $Str_HiddenInput;
	}

	//Builds an image input.
	// - $Str_FieldName:		Name of field to build
	// * Return:				Input markup as a string
	public function input_image($Str_FieldName)
	{
		//Get field value.
		$Str_InputValue = '';
		if (isset($this->Arr_Fields[$Str_FieldName]['value'])
		&& $this->Arr_Fields[$Str_FieldName]['value'][0])
		{
			$Str_InputValue = $this->Arr_Fields[$Str_FieldName]['value'][0];
		}

		$Str_ImageInput = '<input name="'.$Str_FieldName.'" id="'.$Str_FieldName.'" type="image"'.$this->get_all_field_attributes_as_string($Str_FieldName).' value="'.$Str_InputValue.'" />';


		return $Str_ImageInput;
	}



///////////////////////////////////////////////////////////////////////////////
//                    U T I L I T Y   F U N C T I O N S                      //
///////////////////////////////////////////////////////////////////////////////

	//Gets attribute $Str_AttributeName of field $Str_FieldName.
	// - $Str_FieldName:		Name of the field to retrieve attributes
	// = $Str_AttributeName:	Name of attribute to get values for
	// - $Int_NestingLevel:		Nesting level of attributes to retrieve, default = 0
	// - $Bol_RemoveAttribute:	If true the attribute will be removed from field
	// * Return:				Field attribute as a string.
	public function get_field_attribute($Str_FieldName, $Str_AttributeName, $Int_NestingLevel=0, $Bol_RemoveAttribute=false)
	{
		$Str_FieldAttribute = '';

		//If the field has class attributes assigned get them.
		if (isset($this->Arr_Fields[$Str_FieldName]['attributes'][$Str_AttributeName])
		&& $this->Arr_Fields[$Str_FieldName]['attributes'][$Str_AttributeName])
		{
			$Arr_FieldAttribute = explode(MW_HOOK_CHAR_SEPARATOR, $this->Arr_Fields[$Str_FieldName]['attributes'][$Str_AttributeName]);

			//If the attribute nesting level has a value get it.
			if (isset($Arr_FieldAttribute[$Int_NestingLevel]))
			{
				$Str_FieldAttribute = $Arr_FieldAttribute[$Int_NestingLevel];

				//Remove value from attribute.
				if ($Bol_RemoveAttribute)
				{
					$Arr_FieldAttribute[$Int_NestingLevel] = '';
					$this->Arr_Fields[$Str_FieldName]['attributes'][$Str_AttributeName] = implode(MW_HOOK_CHAR_SEPARATOR, $Arr_FieldAttribute);
				}
			}
		}

		return $Str_FieldAttribute;
	}

	//Gets field Str_FieldName attribute $Str_AttributeName as a markup string.
	// - $Str_FieldName:		Name of the field to retrieve attributes
	// = $Str_AttributeName:	Name of attribute to get values for
	// - $Int_NestingLevel:		Nesting level of attributes to retrieve, default = 0
	// - $Bol_RemoveAttribute: If true the attribute will be removed from field
	// * Return:				All attribute
	public function get_field_attribute_as_string($Str_FieldName, $Str_AttributeName, $Int_NestingLevel=0, $Bol_RemoveAttribute=false)
	{
		$Str_FieldAttribute = '';

		if ($Str_AttributeValue = $this->get_field_attribute($Str_FieldName, $Str_AttributeName, $Int_NestingLevel, $Bol_RemoveAttribute))
			$Str_FieldAttribute = ' '.$Str_AttributeName.'="'.$Str_AttributeValue.'"';

		return $Str_FieldAttribute;
	}

	//Gets field Str_FieldName attribute $Str_AttributeName as a markup string.
	// - $Str_FieldName:		Name of the field to retrieve attributes
	// = $Str_AttributeName:	Name of attribute to get values for
	// - $Int_NestingLevel:		Nesting level of attributes to retrieve, default = 0
	// - $Bol_RemoveAttribute:	If true the attribute will be removed from field
	// * Return:				All attribute
	public function get_all_field_attributes_as_string($Str_FieldName, $Int_NestingLevel=0, $Bol_RemoveAttribute=false)
	{
		$Str_FieldAttributes = '';

		//Get all field attributes as a string.
		if (isset($this->Arr_Fields[$Str_FieldName]['attributes']))
		{
			foreach($this->Arr_Fields[$Str_FieldName]['attributes'] as $Str_AttributeName => $Str_AttributeValue)
			{
				$Str_FieldAttributes .= $this->get_field_attribute_as_string($Str_FieldName, $Str_AttributeName, $Int_NestingLevel, $Bol_RemoveAttribute);
			}
		}

		return $Str_FieldAttributes;
	}

	//Sets field attribute $Str_AttributeName to $Str_AttributeValue at nesting level $Int_NestingLevel.
	// - $Str_FieldName:		Name of the field to set attruibute value
	// - $Str_AttributeName:	Name of attributes to set to field
	// - $Int_NestingLevel:		Nesting level to set value, default = 0(top level)
	// - $Bol_OverrideValues:	If false keeps existing values if they are set, default = true(override existing values)
	// * NB: Use $Int_NestingLevel=-1 and $Bol_OverrideValues=true to override all attributes
	//This function needs to be set_field_attribute, with another function to handle set_field_attributes() using a default values of this function.
	public function set_field_attribute($Str_FieldName, $Str_AttributeName, $Str_AttributeValue, $Int_NestingLevel=0, $Bol_OverrideValues=true)
	{
		$Arr_NewAttributes = $this->Arr_Fields;
		$Arr_Attribute = array();

		//*!*This is not paying attention to the nesting levels, it is removing the attribute of the fielod\
		//This shouold be fixed to look at the nestin levels in the future.
		if ($Str_AttributeValue === false)
		{
			unset($Arr_NewAttributes[$Str_FieldName]['attributes'][$Str_AttributeName]);
		}

		//If there are attribute values get nested values as an array.
		if (isset($Arr_NewAttributes[$Str_FieldName]['attributes'])
		&& array_key_exists($Str_AttributeName, $Arr_NewAttributes[$Str_FieldName]['attributes']))
			$Arr_Attribute = explode(MW_HOOK_CHAR_SEPARATOR, $this->Arr_Fields[$Str_FieldName]['attributes'][$Str_AttributeName]);

		//Loop through attribute nesting levels and set values.
		$Int_AttributeNestingLevels = ($Int_NestingLevel > count($Arr_Attribute))? $Int_NestingLevel: count($Arr_Attribute);
		for ($i = 0, $j = $Int_AttributeNestingLevels + 1; $i < $j; $i++)
		{
			if (($i == $Int_NestingLevel) && ($Bol_OverrideValues))
			{
				$Arr_Attribute[$i] = $Str_AttributeValue;
			}

			if (!isset($Arr_Attribute[$i]) || !$Arr_Attribute[$i])
				$Arr_Attribute[$i] = '';
		}

		//Add attribute value.
		$Str_Attribute = (($Int_NestingLevel < 0) && ($Bol_OverrideValues))? '': implode($Arr_Attribute, MW_HOOK_CHAR_SEPARATOR);

		//Recombine nested values into an attribute string.
		$Arr_NewAttributes[$Str_FieldName]['attributes'][$Str_AttributeName] = $Str_Attribute;
		$this->Arr_Fields = $Arr_NewAttributes;

		return;
	}

	//Sets field attribute $Str_AttributeName to $Str_AttributeValue at nesting level $Int_NestingLevel.
	// - $Str_FieldName:		Name of the field to set attruibute value
	// - $Arr_FieldAttributes:	Key/value array of attributes to set to field
	// - $Int_NestingLevel:		Nesting level to set value, default = 0(top level)
	// - $Bol_OverrideValues:	If false keeps existing values if they are set, default = true(override existing values)
	// * NB: Use $Int_NestingLevel=-1 and $Bol_OverrideValues=true to override all attributes
	//*!*Might have to use an asterix as a place holder for keeping existing nested level ie $Arr_FieldAttributes = array(*|nest2);
	public function set_field_attributes($Str_FieldName, $Arr_FieldAttributes, $Int_NestingLevel=0, $Bol_OverrideValues=true)
	{
		//Assess each field attribute passed.
		if ($Arr_FieldAttributes)
		{
			foreach($Arr_FieldAttributes as $Str_AttributeName => $Str_AttributeValue)
			{
				$Arr_Attribute = array();

				//If there are attribute values get nested values as an array.
				if ($this->Arr_Fields[$Str_FieldName]['attributes'][$Str_AttributeName])
					$Arr_Attribute = explode(MW_HOOK_CHAR_SEPARATOR, $this->Arr_Fields[$Str_FieldName]['attributes'][$Str_AttributeName]);

				//If there is an override directive replace value.
				//if ($Bol_OverrideValues)

				//Recombine nested values into an attribute string.

			}
		}
	}

}

?>