<?php
//mw_module.php
//Module interface class file.
//Design by David Thomson at Hundredth Codemonkey.

///////////////////////////////////////////////////////////////////////////////
//                S Y S T E M   P A T H   C O N S T A N T S                  //
///////////////////////////////////////////////////////////////////////////////
/* Required constants
define('(strtoupper(module name))_CONST_STR_MODULE_PREFIX',		'(module prefix)');
define('(strtoupper(module name))_CONST_STR_MODULE_PLUGIN',		'(module plugin)');
*/

/*I'm conplemtating having this a a parent class and doing basic functions that call to the
Forame here so when you use a module you would do:
$this->somemethod();
instead of this:
global $GLB_FORMAN; $GLB_FORMAN->somemethod();

Need to figure out what routines should be put into modules and how they will be linked to the foreman
Also want to register userdefined library sets to the module via the foreman with a method call
These user defined libraies will have to be created as an interface so I an register them properly.
//Need to register modules to the foreman like every other file/class that is called up.
Also need to supply and interface to call up parcels/bundles

Example module form handling:

//Simple form building and saving, no data retrieval by default.

//Parcel objects are instaniated and localised, note we are not retrieving any data.
$parcel1 = $this->parcel(type);
$parcel2 = $this->parcel(type);
//pacel field data is bound to the form object.
//formname is the hook which we access all the inputs holding parcel objects in the bind()
//And we are custom defining which fields are being used on the form and saved, must define form before inputs.
//inputname becomes a shorthand reference to our parcel object data (parcel1[fieldname]) which gets bound to
//the template when we bind the form with additional properties which can be added through the form->input api
//On first bound input that parcel needs to be loaded onto the form object registry
$form = $this->form(formname)->input(inputname, parcel1[fieldname])
				->input(inputname, parcel1[fieldname])
				->input(inputname, parcel2[fieldname])
				->input(inputname, parcel2[fieldname]);
//Validate submitted form, if valid save it.
if ($this->post($form)->valid())
	$this->save($form)->plan($this->parcel(key, template)->data(template))->bind($form)->build();
//Otherwise build with or without any submitted data.
else
	$this->post($form)->plan($this->parcel(key, template)->bind($form)->build();



//So we can add additional properties to the input object like this
$form->input(inputname)->type(text)
					->length(20); //reember we can add label and classes in template
QUICK API REF

$parcel = $this->parcel(type) //returns a parcel
$form = $this->form(formname) //returns $form
$form->input //returns $form
$this->post($form) //returns self so, $this->post($form1)->post($form2)->valid() theoretically could validate both form objects.
$form->valid() //returns bool
$this->plan() //returns builder for usual bind,build calls
//NB I'm going to have to change $CMD->plan() so it doesn't return itself

//How about this? I think that those guys could be a reference but you havt  build the form manually each time anyway.
//Btter for these to be variables completely separate from form building logic.
$form = $this->fieldset(setname $parcel1)
			->fielset(setname $parcel2)

//Linking Parcels
$Obj_Parcel1 = $this->parcel(Name, Key);
$Obj_Parcel2 = $this->parcel(Name, Key);
$Obj_Parcel3 = $this->parcel(Name, Key);
$this->link($Obj_Parcel1, $Obj_Parcel2)->link($Obj_Parcel2, $Obj_Parcel3)
//NB: saving parcels can be done directly or in the form.
$this->save($Obj_Parcel1, $Obj_Parcel2, $Obj_Parcel3);
$this->save($Obj_Form);
//Best to do it in the form object I think.

//Creating a loader
$Obj_Parcel = $this->parcel(Name, Key);
$Obj_Loader = $this->loader($Obj_Parcel);

//Doing collections
$Obj_Loader->collection(CollectionName)->fetch(ParcelType)->collect(true); //true loads data, default is false - only get parcels' meta data
$Arr_Collection = $Obj_Loader->data(CollectionName);


One more note: I think I should get rid of constants at the top of the module file, they don't seem to make sense now.
I'll have to look through some of the core routines to see if they were ever used, I suspect not.


I need to fix up the redirect function to remove CRLF injection code: %0d%0a
	- this will prevent the CRLF injection.

*/
///////////////////////////////////////////////////////////////////////////////
//                        M O D U L E   C L A S S                            //
///////////////////////////////////////////////////////////////////////////////

class MW_Utility_Module extends MW_System_Base
{

///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//This variable indicates whether to add module response to the build stack and process it.
	//*!*I'm a little fuzzy as to whether this is the absolute best approach toimplement this concept, I do need a controlling variable though.
	public $Bol_BuildResponse		= false;



///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	//*!*A quick note about these variables. I'm going to have to assign them to the
	//Properties array via the constructor. I'm not even sure these should be properties or variables.
	protected $Arr_Properties	= array(
		'Arr_OriginalVariables'	=> array(),	//Pre-defined module tag values.
		'Arr_ModifiedVariables'	=> array(),	//Modified module variables.
		'Arr_BuildValues'		=> array(),	//Build variables bound to layouts.
		'Arr_BuildStack'		=> array(),	//Output buffer build stack.
		'Str_BuildLayout'		=> '',		//Identifier of the layout file.
		'Str_BuildSection'		=> false,	//Current section being built.
		'Str_BuildInherit'		=> false,	//Identifier of the layout file to inherit.
		'Arr_BuilderBindValues'	=> array(),	//Values to bind to builder. - depreciated from other framework.
		'Arr_AccessValues'		=> array(),	//Placeholder values for access rules.
		'Arr_ViewAccess'		=> array(),	//Data view access rules.
		'Arr_EditAccess'		=> array());//Data edit access rules.



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//*!*I need to put in a (post)construct event handler.
	//In fact I need to do this with all user objects so as not to
	//override the base class behaviours.
	public function __construct(){}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////


	//Responds to a module request, the return value replaces a builder widget
	// * Return:				Output string of requested module
	public function process_request(){}

	//Gets the recovery value of the module exception with code $Int_Code
	// - $Int_Code:				Exception code integer
	// * Return:				Exception recovery value
	public function handle_exception($Int_Code){}

	//Defines the scope of module caching behaviour
	// * Return:				VOID
	public function define_caching(){}



///////////////////////////////////////////////////////////////////////////////
//           B U I L D E R   I N T E R F A C E   F U N C T I O N S           //
///////////////////////////////////////////////////////////////////////////////

	//Configures module request and initialises local variables.
	// - $Arr_OriginalValues:	Pre-defined(default) module variables defined in build tag
	// - $Arr_ModifiedValues:	Module variables modified by combining path and get values
	// * Return:				VOID
	final public function configure_request($Arr_OriginalValues, $Arr_ModifiedValues)
	{
		$this->Arr_OriginalVariables = $Arr_OriginalValues;
		$this->Arr_ModifiedVariables = $Arr_ModifiedValues;

		return;
	}

	//Caches request string.
	//*!*Should this be a module function or should this be passed to the Foreman to handle?
	final public function cache_request($Str_ModuleResponse)
	{

	}

	public function build_response()
	{
		$this->Bol_BuildResponse = true;
	}


///////////////////////////////////////////////////////////////////////////////
//                        A P I   F U N C T I O N S                          //
///////////////////////////////////////////////////////////////////////////////

	/*
		As a security measure I need to add the the foreman a routine to check what class
		and methods are calling a function so as to prevent any unauthorised access to the internal
		API layer.
		Might do:
		private $Arr_ClassInfo = array('class' => (parent::?)__class__, 'method' => __function__);
		Then pass this array to the foreman as a parameter an it can look up its allowable
		class/function registry.
		Test if I need to use parent:: for child class.
	*/

	//Saves module response locally.
	// - $Str_ModuleResponse:	Module response string to hold locally
	// * Return:				VOID
	//*!*This is fucked. Remember, the reason why I eched the response inside the module is so
	//*!*This is so retarded!
	//That fuckwits that was in;line scripting for their module code can call the fuck
	//I'm a dumbass programer routine. So fix this shit back to how it was!!!!
	public function response($Str_Response)
	{
		$this->Str_ModuleString = $Str_Response;
		return;
	}

	//Prints module response, for retrieval with output buffering.
	// * Return:				VOID
	public function respond()
	{
		echo $this->Str_ModuleString;
		return;
	}

	//Redirects to a new URL.
	// - $Str_Location:			Location to redirect to
	// * Return:				VOID, this script exits execution
	public function redirect($Str_Location)
	{
		header('Location: '.$Str_Location);
		exit;
	}

	//Formats the get variale string
	// - $Arr_GetVars:				Array of variable strings to add to the request string.
	// * NB: This function does not yet deal with arrays in the get string, assumes all values are strings.
	//*!*This function does not deal with any GET variables supplied in the header, not sure if I should do this.
	public function get_path($Arr_GetVars=false)
	{
		//Swap in any variables supplied which already exist.
		$Str_FormattedRequest = '';
		if (($Int_RequestGetVarStart = strpos($_SERVER['REQUEST_URI'], '?')) !== false)
		{
			$Str_RequestVars = substr($_SERVER['REQUEST_URI'], $Int_RequestGetVarStart + 1, strlen($_SERVER['REQUEST_URI']));
			$Arr_RequestVars = explode('&', $Str_RequestVars);

			foreach ($Arr_RequestVars as $Arr_RequestVar)
			{
				$Arr_KeyValue = explode('=', $Arr_RequestVar);
				if ($Arr_GetVars && array_key_exists($Arr_KeyValue[0], $Arr_GetVars))
				{
					$Str_FormattedRequest .= '&'.$Arr_KeyValue[0].'='.$Arr_GetVars[$Arr_KeyValue[0]];
					unset($Arr_GetVars[$Arr_KeyValue[0]]);
				}
				else
				{
					$Str_FormattedRequest .= '&'.$Arr_KeyValue[0].'='.$Arr_KeyValue[1];
				}
			}
		}

		//Swap in any additional variables supplied which do not already exist.
		if ($Arr_GetVars)
		{
			foreach ($Arr_GetVars as $Str_Key => $Str_Value)
			{
				$Str_FormattedRequest .= '&'.$Str_Key.'='.$Str_Value;
			}
		}

		//Swap out the first ampersand for a question mark.
		if ($Str_FormattedRequest)
		{
			$Str_FormattedRequest = '?'.substr($Str_FormattedRequest, 1, strlen($Str_FormattedRequest) -1);
		}

		return $Str_FormattedRequest;
	}

	//Removes get variables from the request path.
	//*!*Later add a thrid parameter to keep the variables instead of removing those supplied.
	public function clean_path($Str_GetPath, $Arr_GetVars)
	{
		$Str_FormattedRequest = '';
		if (($Int_RequestGetVarStart = strpos($Str_GetPath, '?')) !== false)
		{
			$Str_RequestVars = substr($Str_GetPath, $Int_RequestGetVarStart + 1, strlen($Str_GetPath));

			$Arr_RequestVars = explode('&', $Str_RequestVars);
			foreach ($Arr_RequestVars as $Arr_RequestVar)
			{
				$Arr_KeyValue = explode('=', $Arr_RequestVar);
				if (!in_array($Arr_KeyValue[0], $Arr_GetVars))
				{
					$Str_FormattedRequest .= '&'.$Arr_KeyValue[0].'='.$Arr_KeyValue[1];
				}
			}
		}

		//Swap out the first ampersand for a question mark.
		if ($Str_FormattedRequest)
		{
			$Str_FormattedRequest = '?'.substr($Str_FormattedRequest, 1, strlen($Str_FormattedRequest) -1);
		}

		return $Str_FormattedRequest;
	}


///////////////////////////////////////////////////////////////////////////////

/*
	//Adds values to be bound to the builder processing this module.
	// - $Arr_BindValues:		Key/value array of values to bind to builder
	// - $Bol_Overwrite:		Flag to overwrite existing holder key values, default = true(overwrite existing values)
	// * Return:				VOID
	//*!* I need to update this routine to bind values to existing arrarys(EG config).
	public function bind($Arr_BindValues, $Bol_Overwrite=true)
	{
		//If the parameter is not an arrayhandle exception.
		if (!is_array($Arr_BindValues))
		{
			global $CMD;
			$CMD->handle_exception('Attempt to bind as non-array to builder object through module', 'MW:101');

			return;
		}

		//If there are values to bind add them to the module holder for binding.
		if ($Arr_BindValues)
		{
			//Decouple builder binders.
			$Arr_NewBuilderBindValues = $this->Arr_BuilderBindValues;

			foreach ($Arr_BindValues as $Str_Name => $Mix_Value)
			{
				//Add value if we are overwriting or it does not exist yet.
				if ($Bol_Overwrite || !isset($this->Arr_ModuleValues[$Str_Name]))
					$Arr_NewBuilderBindValues[$Str_Name] = $Mix_Value;
			}

			//Recouple builder binders.
			$this->Arr_BuilderBindValues = $Arr_NewBuilderBindValues;
		}

		return;
	}
*/
	//Gets value bound to the module to be binded to the builder.
	// - $Str_Name:				Name of bind value, default = false(get all values)
	// * Return:				Value to be bound to the builder, if no name is supplied all values are returned.
	public function bound($Str_Name=false)
	{
		if ($Str_Name === false)
			return $this->Arr_BuilderBindValues;
		elseif (isset($this->Arr_BuilderBindValues[$Str_Name]))
			return $this->Arr_BuilderBindValues[$Str_Name];

		return;
	}

	//Gets value of the modified module variables set on configuration.
	//*!*I'd really like to change the name of this function and do a sister function for default variables.
	//Would like to keep them one word for the sake of simplicity of the function calls
	//Or, I could just use tag_value()
	// - $Str_Variable:			Variable to get modified value of
	// * Return:				Variable value if it is set, otherwise false
	public function value($Str_Variable)
	{
		//If the variable is set return it.
		if (isset($this->Arr_ModifiedVariables[$Str_Variable]))
			return $this->Arr_ModifiedVariables[$Str_Variable];
		else
			return false;
	}

	//Gets all module values
	public function values()
	{
		return $this->Arr_ModifiedVariables;
	}

	//Gets value of the original module variables set on the build plan tag.
	// - $Str_Variable:			Variable to get original value of
	// * Return:				Variable value if it is set, otherwise false
	public function tag_value($Str_Variable)
	{
		//If the variable is set return it.
		if (isset($this->Arr_OriginalVariables[$Str_Variable]))
			return $this->Arr_OriginalVariables[$Str_Variable];
		else
			return false;
	}


	//Gets the system configuration settings.
	// - $Str_ConfigKey:		Configuration setting key
	// * Return:				Configuration value if access is granted to the key
	//*!*This routine will probably need more work to get a default if $CMD->config($Str_ConfigKey) returns null
	public function config($Str_ConfigKey)
	{
		global $CMD;
		$Str_ConfigValue = $CMD->config($Str_ConfigKey);

		return $Str_ConfigValue;
	}

	//Creates a model object with input and display of data.
	// - $Str_Type:				Type of parcel to create
	// - $Str_Key:				Parcel key of parcel to load
	// - $Mix_Storage:			Storage type to use for parcel, default = false(use settings value)
	// * Return:				Parcel object, false if parcel $Str_Key is not found
	// * NB: If second paramter is defined parcel will be loaded from database
	// * NB: To build a new parcel the key should be an empty string or false.
	public function model($Str_Type, $Str_Key='', $Mix_Storage=false)
	{
		global $CMD;
		$Obj_Parcel = $CMD->model($Str_Type, $Str_Key, $Mix_Storage);

		return $Obj_Parcel;
	}

	//Gets or sets the module access rule placeholder values.
	// - $Str_AccessName:			Placeholder name of the access value
	// - $Mix_AccessValue:			Value for the access ruleplaceholder reference
	// * Return:					Access value if not supplied, otherwise SELF.
	public function access($Str_AccessName, $Mix_AccessValue=null)
	{
		//If the access value is not supplied get it.
		if ($Mix_AccessValue === null)
		{
			if (isset($Arr_NewAccessValues[$Str_AccessName]))
			{
				return $Arr_NewAccessValues[$Str_AccessName];
			}
		}
		//Otherwise set the value.
		else
		{
			$Arr_NewAccessValues = $this->Arr_AccessValues;
			$Arr_NewAccessValues[$Str_AccessName] = $Mix_AccessValue;
			$this->Arr_AccessValues = $Arr_NewAccessValues;
		}

		return $this;
	}
/*
	public function evaluate_access($Arr_AccessRules, $Str_ModelStatus, $Int_ModelAccess, $Bol_HasAccess=false)
	{
		global $CMD;

		//Get the ruleset for the model status.
		foreach ($Arr_AccessRules as $Str_Status => $Arr_StatusRule)
		{
			if ($Str_ModelStatus == $Str_Status)
			{
				//Evaluate each access rule.
				foreach ($Arr_StatusRule as $Int_Access => $Arr_AccessRule)
				{
					//This wrong i need to find the user's access.
					if ($CMD->has_access($Int_ModelAccess, $Int_Access)
					{
						if (is_boolean($Arr_AccessRule))
						{
							$Bol_HasAccess = $Arr_AccessRule;
						}
						elseif (is_array($Arr_AccessRule))
						{
							if (count($Arr_AccessRule) != 3)
							{
								$CMD->handle_exception('Access rule array does not contain three value', 'MW:101');
								continue;
							}
							else
							{
								//Replace any placeholders in the evaluation array.
								$Arr_AccessRule[0] = (in_array($Arr_AccessRule[0], $this->Arr_AccessValues))? $this->Arr_AccessValues[$Arr_AccessRule[0]]: $Arr_AccessRule[0];
								$Arr_AccessRule[1] = (in_array($Arr_AccessRule[1], $this->Arr_AccessValues))? $this->Arr_AccessValues[$Arr_AccessRule[1]]: $Arr_AccessRule[1];
								$Arr_AccessRule[2] = (in_array($Arr_AccessRule[2], $this->Arr_AccessValues))? $this->Arr_AccessValues[$Arr_AccessRule[2]]: $Arr_AccessRule[2];

								//Do evaluation of access rule.
								switch ($Arr_AccessRule[1])
								{
									case 'eq': $Bol_HasAccess = ($Arr_AccessRule[0] = $Arr_AccessRule[2])? true: false; break;
									case 'gt': $Bol_HasAccess = ($Arr_AccessRule[0] > $Arr_AccessRule[2])? true: false; break;
									case 'lt': $Bol_HasAccess = ($Arr_AccessRule[0] < $Arr_AccessRule[2])? true: false; break;
									case 'gte': $Bol_HasAccess = ($Arr_AccessRule[0] >= $Arr_AccessRule[2])? true: false; break;
									case 'lte': $Bol_HasAccess = ($Arr_AccessRule[0] <= $Arr_AccessRule[2])? true: false; break;
									default: $CMD->handle_exception('Access rule array operator not valid', 'MW:101');
								}
							}
						}
						else
						{
							$CMD->handle_exception('Access rule is not a boolean value or an array', 'MW:101');
							continue;
						}
					}
				}
			}
		}

		return $Bol_HasAccess;
	}
*/
	public function access_values($Arr_AccessRules, $Obj_Model)
	{
		foreach ($Arr_AccessRules as &$Arr_StatusRule)
		{
			foreach ($Arr_StatusRule as &$Arr_AccessRule)
			{
				if (is_array($Arr_AccessRule))
				{
					if (isset($Arr_AccessRule[0]) && is_string($Arr_AccessRule[0]))
					{
						$Arr_AccessRule[0] = (array_key_exists($Arr_AccessRule[0], $this->Arr_AccessValues))? $this->Arr_AccessValues[$Arr_AccessRule[0]]: $Arr_AccessRule[0];
						$Arr_AccessRule[0] = $Obj_Model->data($Arr_AccessRule[0]);
					}
					if (isset($Arr_AccessRule[1]) && is_string($Arr_AccessRule[1]))
					{
						$Arr_AccessRule[1] = (array_key_exists($Arr_AccessRule[1], $this->Arr_AccessValues))? $this->Arr_AccessValues[$Arr_AccessRule[1]]: $Arr_AccessRule[1];
					}
					if (isset($Arr_AccessRule[2]) && is_string($Arr_AccessRule[2]))
					{
						$Arr_AccessRule[2] = (array_key_exists($Arr_AccessRule[2], $this->Arr_AccessValues))? $this->Arr_AccessValues[$Arr_AccessRule[2]]: $Arr_AccessRule[2];
					}
				}
			}
		}

		return $Arr_AccessRules;
	}

	public function has_view_access($Obj_Model, $Str_Access, $Arr_Rules=false)
	{
		global $CMD;
		$Bol_HasAccess = false;

		//If the model has a status then determine if the user has access.
		if ($Obj_Model->data('status'))
		{
			//Do tests for default access rules.
			if (isset($this->Arr_ViewAccess) && $this->Arr_ViewAccess)
			{
				if (!is_array($this->Arr_ViewAccess))
				{
					$CMD->handle_exception('Default view access rules not an array', 'MW:101');
				}
				else
				{
					$Bol_HasAccess = $CMD->has_access($this->Arr_ViewAccess, $Obj_Model->data('status'), $Str_Access, $Bol_HasAccess);
				}
			}

			//Do tests for additional access rules.
			if ($Arr_Rules)
			{
				if (!is_array($Arr_Rules))
				{
					$CMD->handle_exception('Default view access rules not an array', 'MW:101');
				}
				else
				{
					//Replace any placeholders in the rules array.
					foreach ($Arr_Rules as $Arr_RuleSet)
					{
						$Arr_RuleSet = $this->access_values($Arr_RuleSet, $Obj_Model);
						$Bol_HasAccess = $CMD->has_access($Arr_RuleSet, $Obj_Model->data('status'), $Str_Access, $Bol_HasAccess);
					}
				}
			}
		}

		return $Bol_HasAccess;
	}

	public function has_edit_access($Obj_Model, $Str_Access, $Arr_Rules=false)
	{
		global $CMD;
		$Bol_HasAccess = true;

		//If the model has a status then determine if the user has access.
		if ($Obj_Model->data('status'))
		{
			//Do tests for default access rules.
			if (isset($this->Arr_EditAccess) && $this->Arr_EditAccess)
			{
				if (!is_array($this->Arr_EditAccess))
				{
					$CMD->handle_exception('Default edit access rules not an array', 'MW:101');
				}
				else
				{
					$Bol_HasAccess = $CMD->has_access($this->Arr_EditAccess, $Obj_Model->data('status'), $Str_Access, $Bol_HasAccess);
				}
			}

			//Do tests for additional access rules.
			if ($Arr_Rules)
			{
				if (!is_array($Arr_Rules))
				{
					$CMD->handle_exception('Default edit access rules not an array', 'MW:101');
				}
				else
				{
					//Replace any placeholders in the rules array.
					foreach ($Arr_Rules as $Arr_RuleSet)
					{
						$Arr_RuleSet = $this->access_values($Arr_RuleSet, $Obj_Model);
						$Bol_HasAccess = $CMD->has_access($Arr_RuleSet, $Obj_Model->data('status'), $Str_Access, $Bol_HasAccess);
					}
				}
			}
		}

		return $Bol_HasAccess;
	}

	//Creates a collection object for data selection and updating.
	// - $Mix_Model:			Model object or data to use in collection(EG; 'parcel'), or data collection
	// * Return:				Collection utility object.
	public function collection($Mix_Model)
	{
		global $CMD;
		$Obj_Collection = $CMD->collection($Mix_Model);

		return $Obj_Collection;
	}

	//*!*This function needs to be rewritten to work in this lite version of the framework.
	//*!*This will become like a SQL join statement.
	//Couples linked collection data to a collection data set for display
	// - $Arr_Parent:			Collection data set to add linked data to
	// - $Arr_Children:			Collection data set linked to $Arr_Parent
	// - $Str_Placeholder:		Placeholder to put collection data, default = false('linked_model')
	// * Return:				$Arr_Parent data with linked children added for display
	//*!*This function needs to be moved into the collection object $collection($Arr_Parent)->couple($Arr_Children, $Str_Placeholder)
	//So this will become a depreciated function, not that I have used it much.
	//*!*What I should do is add a prameter on the collection->fetch() method to coupl results with data set if supplied
	public function couple($Arr_Parent, $Arr_Children, $Str_Placeholder=false)
	{
		for ($i = 0; $i < count($Arr_Parent); $i++)
		{
			//If the child is linked to the parent then couple it.
			foreach ($Arr_Children as $Arr_Child)
			{
				if ($Arr_Parent[$i]['id'] == $Arr_Child['model_id'])
				{
					if (($Str_Placeholder !== false) && (is_string($Str_Placeholder)))
					{
						$Arr_Parent[$i][$Str_Placeholder][] = $Arr_Child;
					}
					else
					{
						$Arr_Parent[$i]['linked_model'][] = $Arr_Child;
					}
				}
			}
		}

		return $Arr_Parent;
	}

	//Determines whether the collection $Arr_LinkNeedles are within the collection $Arr_LinkHeystack.
	// - $Arr_LinkNeedles:		Extracted or unextracted collection set to examine
	// - $Arr_LinkHeystack:		Extracted or unextracted collection set to examine
	// - $Bol_Match:			Test switch for existing links, default true(check links exist)
	// * Return:				Set of model ids matching the link condition(actually this returns what is put in)
	// * NB: If $Bol_Match is false a set is returned in the collection which don't have links
	//*!*I need a function which is going to add linked objects to the dataset of the linked item
	//so that I can display the data which is linked to the model in the template without having
	//to do this manually every time I want to display a hierarchy of objects.
	//An exampole the basic thing that needs to be done, but for multiple levels, is linking the shopping cart
	//categories and products for display in the public area.
	public function linkage($Arr_LinkNeedles, $Arr_LinkHeystack, $Bol_Match=true)
	{
		$Arr_Linkage = array();
		$Arr_LinkCollection = array();
		$Arr_LinkNeedlesIds = array();
		$Arr_LinkHeystackIds = array();
		$Bol_NeedleData = false;

		//Get data ids of collection one.
		if ($Arr_LinkNeedles)
		{
			//If the collection has not been extracted then get row ids.
			if (isset($Arr_LinkNeedles[0]) && is_array($Arr_LinkNeedles[0]))
			{
				$Bol_NeedleData = true;
				foreach ($Arr_LinkNeedles as $Arr_LinkNeedlesData)
				{
					$Arr_LinkNeedlesIds[] = $Arr_LinkNeedlesData['id'];
				}
			}
			//Otherwise get model ids from extracted data keys.
			else
			{
				$Arr_LinkNeedlesIds = array_keys($Arr_LinkNeedles);
			}
		}

		//Get data ids of collection two.
		if ($Arr_LinkHeystack)
		{
			//If the collection has not been extracted then get row ids.
			if (isset($Arr_LinkHeystack[0]) && is_array($Arr_LinkHeystack[0]))
			{
				foreach ($Arr_LinkHeystack as $Arr_LinkHeystackData)
				{
					$Arr_LinkHeystackIds[] = $Arr_LinkHeystackData['id'];
				}
			}
			//Otherwise get model ids from extracted data keys.
			else
			{
				$Arr_LinkHeystackIds = array_keys($Arr_LinkHeystack);
			}
		}

		//Get linkage between collections.
		//*!*The postive test is falling down on this function, need to test.
		//Make sure that the ids are being passed, not sure if they were thoroughtly tested on implementation
		foreach ($Arr_LinkNeedlesIds as $Int_LinkNeedlesId)
		{
			if (($Bol_Match && in_array($Int_LinkNeedlesId, $Arr_LinkHeystackIds))
			|| (!$Bol_Match && !in_array($Int_LinkNeedlesId, $Arr_LinkHeystackIds)))
			{
				$Arr_Linkage[] = $Int_LinkNeedlesId;
			}
		}

		//If the haystack data is unextracted rebuild it.
		if ($Bol_NeedleData)
		{
			for ($i = 0; $i < count($Arr_LinkNeedles); $i++)
			{
				if (in_array($Arr_LinkNeedles[$i]['id'], $Arr_Linkage))
				{
					$Arr_LinkCollection[] = $Arr_LinkNeedles[$i];
				}
			}
		}
		//Otherwise put the key/value pairs back together.
		else
		{
			foreach ($Arr_LinkNeedles as $Arr_LinkNeedleKey => $Arr_LinkNeedleData)
			{
				if (in_array($Arr_LinkNeedleKey, $Arr_Linkage))
				{
					$Arr_LinkCollection[$Arr_LinkNeedleKey] = $Arr_LinkNeedleData;
				}
			}
		}

		return $Arr_LinkCollection;
	}


	//Saves parcels attached to passed data object to database.
	// - $Obj_Data:				Data holding object: parcel, package, form or collection
	// * NB: Form should be validated prior to calling this method
	//*!*I do believe this is going to be removed. !!!Not to be removed, wtf?
	public function save($Obj_Data)
	{
		global $CMD;
		$CMD->save($Obj_Data);

		return $this;
	}

	//Links two or more model objects in an array
	//*!*I'm moving the query building to here from the parcel object. Might be best, not sure.
	//Though, I don't like queries being at this level of the system.
	// - $Obj_Linker:			Model object or collection to link to
	// - $Obj_Linkee:			Model object or collection to be linked
	// - $Bol_LinkType:			Type of link being created, default true(brother link, both ways)
	// * Return:				SELF
	// * NB:
	//*!*I'm going to re-explore the link type at a later date. For the moment the link type true
	//indicates link is going both ways. The plan is to use two linktype params and if the second one
	//is true use the same link type both ways, and if it is false do not link in the other direction.
	//otherwise just use the link specified in the param for the link in the other direction.
	//*!*So yeah, if the link type param is tru or false then the link entry into the db will be null.
	public function link($Obj_Linker, $Obj_Linkee, $Bol_LinkType=true)
	{
		//If the object is a parcel call a parcel link.
		if (is_subclass_of($Obj_Linkee, 'MW_Utility_Parcel'))
		{
			//Check to see if the objects are linked before making the link.
			//*!*This is a non-optimised approachbut I can't think of a better way to do this.
			//*!*I will also need to look at thei reciplrication relationship if the link is
			//created differently in either direction
			global $CMD;
			if (!$CMD->collection($Obj_Linker)->linked($Obj_Linkee)->where(array('id', 'eq', $Obj_Linker->data('id')))->fetch())
			{
				$Obj_Linkee->link($Obj_Linker, $Obj_Linker->data('id'));
			}

			if (!$CMD->collection($Obj_Linkee)->linked($Obj_Linker)->where(array('id', 'eq', $Obj_Linkee->data('id')))->fetch())
			{
				$Obj_Linker->link($Obj_Linkee, $Obj_Linkee->data('id'));
			}
		}
		//Otherwise if the object is a collection link each collected model by id.
		//*!*I have changed the parcel link logic to fetch a parcel coolection before linking it
		//This type of logic needs to be replicated here to ensure that parcels within a collection are
		//not linked to twice. Not sure if the best way is to get a collection and loop through the collection to
		//make sure that the id is not in the cololection. If the id is in the collection then I will have to remove it
		//*!*I do know that that return value that the object is linked should only be used to handle exceptions that
		//arise out of database conbnecdtivity issues, nopt to do with the consistancy of the code itself.
		elseif (get_class($Obj_Linkee) == 'MW_Utility_Collection')
		{
			//*!* This is to be done at a later point in time.
			//*!*I need to have the linkee object local to the collection for reference.
			foreach ($Arr_CollectionIds as $Arr_CollectionId)
			{
				//*!*Obj_CollectModel is the stored model that builds a collection.
				$Bol_Linked = $Obj_Linker->link($Obj_Linkee->Obj_CollectModel, $Arr_CollectionId);

				if ($Bol_Linked && $Bol_LinkType)
					$Obj_Linkee->Obj_CollectModel->link($Obj_Linker, $Obj_Linker->data($Obj_Linker->Str_ModelType.'_id'));
			}
		}
		else
		{
			//*!*Will need to test for packages and other classes later.
		}

		return $this;
	}

	//Unlinks two parcels by removing link table rows on each parcel.
	// - $Obj_Linker:			Model object or collection to link to
	// - $Obj_Linkee:			Model object or collection to be linked
	// - $Bol_LinkType:			Type of link being created, default true(brother link, both ways)
	// * Return:				SELF
	//*!*The package links will never remove rows, only change the status of the link.
	//This means that packages will also have a status field, though packages will have
	//a new link row each time the status changes.
	public function unlink($Obj_Linker, $Obj_Linkee, $Bol_LinkType=true)
	{

	}

	//Creates a helper object to execute miscellaneous functions.
	// - $Str_Name:				Name of helper class object to create
	// * Return:				New helper object
	public function helper($Str_Name)
	{
		global $CMD;
		$Obj_Helper = $CMD->helper($Str_Name);

		return $Obj_Helper;
	}

	//Gets a localised string value froma predefined canstant value
	// - $Str_Constant:			Language variable to retrieve text value
	// - $Str_Language:			Language identifier, default = false(use configured language)
	public function lang($Str_Constant, $Str_Language=false)
	{
		global $CMD;
		$Str_Text = $CMD->lang($Str_Constant, $Str_Language);

		return $Str_Text;
	}

	//Creates form plugin object with the path $Str_Name using the variables $Arr_Vars
	// - $Str_Name:				Name of form controlling module data
	// - $Arr_Vars:				Array of variables to assign to the plugin
	// * Return:				Result string of the plugin execution
	public function plugin($Str_Name, $Arr_Vars=false)
	{
		$Str_Plugin = '';

		global $CMD;
		$Str_Plugin = $CMD->plugin($Str_Name)->bind($Arr_Vars)->build();

		return $Str_Plugin;
	}

	//Makes a module request to compact the functionality of two modules.
	// - $Str_Module:			Name of module to request
	// - $Arr_Values:			Module request values
	// - $Bol_Buffer:			Add output buffering, default(true)
	//*!*I need to add combine functionallity onto this call
	//*!*I'm only using internal modules for now
	//NB: This comes form a regex bug which is calling an exception on the ob string which I can't get any info on
	//I cannot find a way to debug this as there is some serious funky redirection happening with the ob stream which
	//is outside of php execution. The script executes perfect but my exception log is picking up funky module tag strings
	//which should actually never be formed in the first place. I will need to make a unit test case of this to sort it out
	//In the mean time, this function should be used when calling a module within a module insteaded of using the builder
	//*!*Need to be able to call up the version of the module as well.
	public function compact($Str_Module, $Arr_Values=false, $Bol_Buffer=true)
	{
		$Mix_ModuleOutput = false;
		global $CMD;
		$Obj_Module = $CMD->module($Str_Module);

		//Configure the module variables.
		if ($Arr_Values === false)
		{
			$Obj_Module->configure_request($this->Arr_OriginalVariables, $this->Arr_ModifiedVariables);
		}
		else
		{
			$Obj_Module->configure_request($this->Arr_OriginalVariables, $Arr_Values);
		}

		//Process module request.
		if ($Bol_Buffer)
		{
			ob_start();
			$Obj_Module->process_request();
			$Mix_ModuleOutput = ob_get_contents();
			ob_end_clean();

			return $Mix_ModuleOutput;
		}

		$Mix_ModuleOutput = $Obj_Module->process_request();

		return $Mix_ModuleOutput;
	}

	//Gets an instance of the module named $Str_Module.
	// - $Str_Module:			Name of the module to create
	// * Return:				Module object if found, otherwise false.
	//*!*Need to add an API set of functions to set variables and process request like the full framework.
	public function module($Str_Module)
	{
		global $CMD;
		return $CMD->module($Str_Module);
	}

	//Gets the meta data of the page
	public function page($Str_PageName=false)
	{
		$Arr_MetaData = array();

		if ($Str_PageName === false)
		{
			global $CMD;
			$Arr_MetaData = $CMD->get_interface_data('meta');
		}

		return $Arr_MetaData;
	}

	//Gets or sets session values.
	// - $Mix_Index:				Index of session variable to get or set
	// - $Mix_Value:				Value to set session variable, default=false(get session variable)
	// * Return:					Session variable if getting variable, otherwise boolean success operation
	// * NB: If $Mix_Value is null then the session variable is unset.
	// * NB: If $Str_Name is null then all session variables are unset.
	public function session($Mix_Index, $Mix_Value=false)
	{
		global $CMD;
		$Mix_Request = false;

		//If clearing the session unset all values.
		if ($Mix_Index === null)
		{
			if ($Arr_Session = $CMD->get_session())
			{
				foreach ($Arr_Session as $Str_Session)
				{
					$CMD->set_session($Str_Session, false);
				}
			}
		}
		//Else if the value is null unset it
		elseif ($Mix_Value === null)
		{
			$CMD->set_session($Mix_Index, null);
		}
		//If the value is not supplied get it.
		elseif ($Mix_Value === false)
		{
			if (is_string($Mix_Index))
			{
				$Mix_Request = $CMD->get_session($Mix_Index);
			}
			elseif (is_array($Mix_Index) && $Mix_Index)
			{
				//Build index location.
				$Arr_Index = $CMD->get_session($Mix_Index[0]);
				for ($i = 1; $i < count($Mix_Index); $i++)
				{
					if (isset($Arr_Index[$Mix_Index[$i]]))
					{
						$Arr_Index = $Arr_Index[$Mix_Index[$i]];
					}
					else
					{
						return false;
					}
				}

				$Mix_Request = $Arr_Index;
			}
			else
			{
				$CMD->handle_exception('Session index supplied is of invalid type', 'MW:101');
				return false;
			}
		}
		//Otherwise set session value.
		else
		{
			$Mix_Request = $CMD->set_session($Mix_Index, $Mix_Value);
		}

		return $Mix_Request;
	}

	//Gets property data about the user's current session.
	// - $Str_Property:			User session property to get value for, default false(get boolean logged in)
	// * Return:				Value of session property named $Str_Property
	// * NB: If no session property is named only a true or false of whether the user is logged in or not is returned
	// This function when first called will start the session, good idea?
	//*!*Might add another parameter to add variables.
	//*!*I might push this abstraction into the foreman to de clutter it...
	public function user($Str_Property=false)
	{
		$Mix_UserProperty = null;
		global $CMD;

		if ($Str_Property !== false)
		{
			$Str_Variable = '';
			switch ($Str_Property)
			{
				//*!*These login values should be constants.
				case 'login': $Str_Variable = MW_STR_SESSION_USERLOGN; break;
				case 'name': $Str_Variable = MW_STR_SESSION_USERNAME; break;
				case 'status': $Str_Variable = MW_STR_SESSION_USERSTAT; break;
				case 'version': $Str_Variable = MW_STR_SESSION_USERVRSN; break;
				case 'lang': $Str_Variable = MW_STR_SESSION_USERLANG; break;
				case 'view': $Str_Variable = MW_STR_SESSION_USEREDIT; break;
				case 'edit': $Str_Variable = MW_STR_SESSION_USERVIEW; break;
				case 'lock': $Str_Variable = MW_STR_SESSION_USERLOCK; break;
				default: $CMD->handle_exception('User property '.$Str_Property.' invalid', 'MW:101');
			}

			$Mix_UserProperty = $CMD->get_session($Str_Variable);
		}
		else
		{
			$Mix_UserProperty = $CMD->is_logged_in();
		}

		return $Mix_UserProperty;
	}

	//Logs user in with the credentials held by $Obj_User model
	// - $Obj_User:				Reference to user model object validated with form or loaded from database
	// * NB: If $Obj_User has no username and password attached this function fails
	// * NB: $Obj_User is passed as a reference to pull additional information from the database onto the session.
	//*!*The updated parcel data test has not been tested.
	public function login(&$Obj_User)
	{
		global $CMD;

		//If the model is not a user or has no data attached handle error.
		if ((!isset($Obj_User->Str_ModelType)) || ($Obj_User->Str_ModelType != 'model')
		|| (!isset($Obj_User->Str_ModelName)) || ($Obj_User->Str_ModelName != 'user'))
		{
			$CMD->handle_exception('Object used for login not a user model', 'MW:101');
		}
		//Else if the user has no username handle exception.
		elseif (!$Obj_User->data('username'))
		{
			$CMD->handle_exception('Username not set to user on login', 'MW:101');
		}
		//Else if the user has no password handle exception.
		elseif (!$Obj_User->data('password'))
		{
			$CMD->handle_exception('Password not set to user on login', 'MW:101');
		}
		//Otherwise log user in.
		else
		{
			//*!*This function should be called with param, $Arr_Credentials = array('username'=>'foo', 'version'=>'foo',  'language'='foo');
			$CMD->log_user_in($Obj_User);
		}

		return;
	}

	//Logs user out, unsets all session information
	// - $Bol_DestroySession		Destroys the session and cookie values, default = false(keep session data)
	// * Return:				True if user  was logged in, other false
	public function logout($Bol_DestroySession=false)
	{
		global $CMD;

		//If the user is logged in log them out.
		if ($CMD->is_logged_in())
		{
			$CMD->log_user_out($Bol_DestroySession);
			//This should only be bound if the seesssion is not destroyed.
			$this->bind(array('user'=>$CMD->get_user_session()));
			return true;
		}
		//Otherwise do nothing.
		else
		{
			return false;
		}
	}


	//*!*will add ability to define theme of layout here.
	// - $Str_Plan:				Name of the resource to set as build plan
	// - $Bol_Plugin:			Set the plan resource as a plugin, default - false(use a layout)
	public function plan($Str_Plan, $Bol_Plugin=false)
	{
		$this->Str_BuildLayout = $Str_Plan;
		return $this;
	}

	//*!* this is where i'm putting the builder function
	//Need to pull this shit out of the plugin and then add the stack building routines
	//with thier associated locall variables.
	//Sets build variable values locally for processing of variable hooks.
	// - $Arr_BuildValues:		Key/value array to bind as $this->Arr_BuildValues
	// - $Bol_Overwite:			Overwrite existing values arlready set, default TRUE
	// * Return:				TRUE if successfull, FALSE if values cannot be set
	//*!*This is fucked, needs a review ASAP. And I need feedback as to what goes wrong in this function plus exception handling
	public function bind($Arr_BuildValues, $Bol_Overwite=true)
	{
		$Arr_NewBuildValues = $this->Arr_BuildValues;

		//Set each build value locally.
		if (count($Arr_BuildValues) > 0)
		{
			foreach ($Arr_BuildValues as $Str_Name => $Var_Value)
			{
				//*!*I'll need to test that the $config variables aren't being messed with.
				//Still undecided about $status variables....
				if (($Bol_Overwite) || (!isset($Arr_NewBuildValues[$Str_Name])))
					$Arr_NewBuildValues[$Str_Name] = $Var_Value;
			}
		}

		$this->Arr_BuildValues = $Arr_NewBuildValues;

		return $this;
	}

	//Builds the set plan and bound variables to construct an interface string
	// - $Str_OverrideFile:		Full path of execute override file, default = false(use define layout)
	// * Return:				Result of the executed file as a string
	public function build($Str_OverrideFile=false)
	{
		global $CMD;

		//Reset build stack.
		$this->Arr_BuildStack = array();

		//Get build values
		$Arr_ExtractedBuildValues = $this->Arr_BuildValues;
		extract($Arr_ExtractedBuildValues);
		ob_start();

		//If there is an override file defined execute that.
		if ($Str_OverrideFile)
		{
			include($Str_OverrideFile);
		}
		//Oytherwise execute the defined plan.
		else
		{
			$Str_Theme = $CMD->helper('page')->has_asset('layouts/'.$this->Str_BuildLayout.'.html');
			include(MW_CONST_STR_DIR_DOMAIN.'themes/'.$Str_Theme.'/layouts/'.$this->Str_BuildLayout.'.html');
		}

		//Decouple build stack.
		$Arr_NewBuildStack = $this->Arr_BuildStack;

		//Fold buffer into stack.
		$Arr_NewBuildStack[] = ob_get_contents();
		ob_end_clean();

		//Recouple build stack.
		$this->Arr_BuildStack = $Arr_NewBuildStack;

		//If there is an inheritance execute it.
		//*!*This needs to be a recursive function. of doa while statement.
		if ($this->Str_BuildInherit)
		{
			ob_start();

			//Reset build stack.
			$this->Arr_BuildStack = array();

			$Str_Theme = $CMD->helper('page')->has_asset('layouts/'.$this->Str_BuildInherit.'.html');
			include(MW_CONST_STR_DIR_DOMAIN.'themes/'.$Str_Theme.'/layouts/'.$this->Str_BuildInherit.'.html');

			//Decouple build stack.
			$Arr_NewBuildStack = $this->Arr_BuildStack;

			//Fold buffer into stack.
			$Arr_NewBuildStack[] = ob_get_contents();
			ob_end_clean();

			//Recouple build stack.
			$this->Arr_BuildStack = $Arr_NewBuildStack;
		}

		//Recouple build stack.
		$this->Arr_BuildStack = $Arr_NewBuildStack;

		return implode('', $Arr_NewBuildStack);
	}

	//this and the prepend, replace and override functions will replicate the 
	//full version of this framework's templating functionality.
	public function append()
	{

	}

	//*!*themes not implemented yet
	public function inherit($Str_InheritLayout, $Str_InheritTheme=false)
	{
		//Check for the inheritance file.

		//Set the inheritance.
		$this->Str_BuildInherit = $Str_InheritLayout;
	}

	//Sets execued section string to the command object for placement in inheritance layout.
	// - $Str_SectionName:		Identifying name of the section within the layout heirarchy
	// * Return:				SELF
	public function section($Str_SectionName=false)
	{
		//If opening a section start a new buffer.
		if ($Str_SectionName)
		{
			//Set the section name.
			if ($this->Str_BuildSection)
			{
				global $CMD;
				$CMD->handle_exception('Section is already being built' ,'MW:101');
			}
			else
			{
				//need to check that there is not already a section already on the cmd
				$this->Str_BuildSection = $Str_SectionName;

				//Decouple build stack.
				$Arr_NewBuildStack = $this->Arr_BuildStack;

				//Capture the output of current buffer and add to stack.
				$Arr_NewBuildStack[] = ob_get_contents();
				ob_end_clean();

				//Recouple build stack.
				$this->Arr_BuildStack = $Arr_NewBuildStack;

				ob_start();
			}
		}
		//Otherwise close the section and save the stack block.
		else
		{
			global $CMD;

			//Set the section name.
			if (!$this->Str_BuildSection)
			{
				$CMD->handle_exception('No section is currently being built on close' ,'MW:101');
			}
			else
			{
				//Decouple build stack.
				$Arr_NewBuildStack = $this->Arr_BuildStack;

				//Set section to command object.
				$Arr_NewBuildStack[] = $CMD->section($this->Str_BuildSection, ob_get_contents());
				ob_end_clean();

				//Recouple build stack.
				$this->Arr_BuildStack = $Arr_NewBuildStack;

				//Clear the section name.
				$this->Str_BuildSection = false;

				ob_start();
			}
		}

		return $this;
	}

	//Inserts a child layout no the current executed layout with bound variables.
	// - $Str_Layout:			Layout file to include in the current layout execution
	// - $Str_Theme:			Theme of the layout file to insert into execution
	// * Return:				SELF
	//*!*I will also need to define the theme as well.
	public function layout($Str_Layout, $Str_Theme=false)
	{
		global $CMD;
		$Str_Theme = $CMD->helper('page')->has_asset('layouts/'.$Str_Layout.'.html');
		include(MW_CONST_STR_DIR_DOMAIN.'themes/'.$Str_Theme.'/layouts/'.$Str_Layout.'.html');

		return $this;
	}


	//*!*this function is going to do a straight up query on the database using an SQL statement
	public function query($Str_SqlStatement)
	{
		global $CMD;

		if (!$Str_SqlStatement || !is_string($Str_SqlStatement))
		{
			$CMD->handle_exception('SQL statement for query not a valid string', 'MW:101');
			return false;
		}
		
		//Do query here.
	}

}

?>