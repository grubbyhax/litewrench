<?php
//command.php
//Command class file.
//Design by David Thomson at Hundredth Codemonkey.


class MW_System_Command extends MW_System_Base
{
/* I should check these htaccess directives
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
RewriteRule ^.*$ index.php [NC,L]

Just some quick notes:
I'm thinking about using a hash() wrapper for md5 so I can change hashing algorithms universally, I must check to see if this is appropriate
I would like this wrapper to extend to the module class.

*/
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	// *** SYSTEM CONFIGURATION *** //
	private $Arr_SysConfig				= array();		//User-defined settings.
	private $Flt_SecurityCode			= '';			//Object security code.
	private $Bol_SelfDestruct			= false;		//Unique object insurance.

	// *** CORE ENGINE OBJECTS *** //
	private $Obj_Storage				= null;			//Data storage object.
	private $Obj_Exception				= null;			//User error handling object.
	private $Obj_Authorise				= null;			//User authentication object.
	private $Obj_Analytics				= null;			//Metrics and tracking object.
	private $Obj_Plugin					= null;			//
	private $Obj_Module					= null;



///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties	= array(
												// *** REQUESTED URI DATA *** //
		'Str_RequestedDomainName'	=> '',		//Domain name being accessed.
		'Str_RequestedViewPath'		=> '',		//Entire view path of request.
		'Str_RequestedFilePath'		=> '',		//File path of requested page.
		'Arr_RequestedPathVars'		=> array(),	//Additional view path variables.

												// *** REQUEST DATA HOOKS *** //
		'Arr_RequestedPageInfo'		=> array(), //Meta data for requested webpage.
		'Str_RequestedInterface'	=> false,	//Interface template of requested webpage.

												//*** DATABASE HOLDERS ***//
		'Arr_Athorisation'			=> array(),	//User view and edit access permissions.
		'Arr_QueryStack'			=> array(),	//Database XQL query stack.
		'Arr_QueryErrors'			=> array(),	//Incorrectly formatted queries.

												// *** COMPONENT INFO *** //
		'Arr_GetModel'				=> array(),	//Parcel type=>key being used.

												//*** OBJECT REGISTRAR ***//
		'Arr_Registrar'				=> array(),	//Set of files registered in request.
		'Int_Registrar'				=> 0,		//Number of unique objects in execution.
		'Arr_Locales'				=> array(),

		'Arr_BuildSections'			=> array());//Sections within the build string.




///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Constructor loads core view objects locally for server caching.
	// - $Flt_SecurityCode:		Unique object security code
	// - $Arr_ConfigSettings:	Configuration
	// * Return:					VOID
	public function __construct($Flt_SecurityCode, $Arr_ConfigSettings)
	{
		if (!defined('INITIALISE_COMMAND'))
		{
			define('INITIALISE_COMMAND', true); //NB: runkit must be turned off.
			$this->Flt_SecurityCode = $Flt_SecurityCode;
		}
		else
		{
			//Kill foreman.
			return;
		}

		//Set configuration settings.
		$this->Arr_SysConfig = $Arr_ConfigSettings;

		//Set up object registrar.
		$this->Arr_Registrar = array('system' => array(), 'utilities' => array(), 'models' => array(), 'modules' => array(), 'helpers' => array(), 'locales' => array(), 'plugins'=>array());

		//Include core constants file.
		$this->plugin('constants')->build();

		//Load core engine objects locally.
		$this->system('storage');
		$this->system('authorise');
		$this->system('analytics');

		//Debugging object.
		if ($Arr_ConfigSettings['debug'])
		{
			global $GLB_DEBUGGER;
			require_once(MW_CONST_STR_FILE_DEBUGGER);
			$GLB_DEBUGGER = new MW_System_Debugger();
		}

		return;
	}

	//*!*I don't think this has been implemented properly.
	// - $Flt_SecurityCode:		Destruct code
	public function __destruct()
	{
		//Push all new generated content to cache.

		//Log exceptions, just die once ffs.
		if ($this->Obj_Exception)
			$this->Obj_Exception->log_exceptions();

		//If the foreman is being killed prematurely.
		if (!$this->Bol_SelfDestruct)
		{
			//Handle exception.
			exit;
		}

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//       E N G I N E   I N I T I A L I S A T I O N   F U N C T I O N S       //
///////////////////////////////////////////////////////////////////////////////

	//Searches sitemap to find next webpage along view p ath starting at $Obj_ParentElement.
	// - $Obj_ParentElement:		Starting element from which view path is resolved
	// - $Str_FileName:				File name of webpage whose parent is $Obj_ParentElement
	// * Return:					Chile webpage in sitemap file of $Obj_ParentElement if it exists, otherwise VOID
	private function get_child_webpage_by_file_name($Obj_ParentElement, $Str_FileName)
	{
		$Arr_ChildElements = $Obj_ParentElement->childNodes;
		foreach ($Arr_ChildElements as $Obj_ChildNode)
		{
			if (($Obj_ChildNode->nodeType == XML_ELEMENT_NODE)
			&& ($Obj_ChildNode->nodeName == 'node')
			&& ($Obj_ChildNode->hasAttribute('key'))
			&& ($Obj_ChildNode->getAttribute('key') == $Str_FileName))
				return $Obj_ChildNode;
		}

		return;
	}

	//Replaces a sitemap node with its sitemap document object tree.
	// - $Obj_SitemapNode:			Sitemap node to replace with its sitemap tree
	// - $Obj_SitemapTree:			Sitemap document to replace sitemap node
	// * Return:					True if successfull, otherwise false
	private function replace_sitemap_node($Obj_SitemapNode, $Obj_SitemapTree)
	{
		$Bol_InsertSuccess = false;

		return $Bol_InsertSuccess;
	}

	//Sets page meta data to interface before sending request response.
	public function set_interface_attributes()
	{
		//Set attributes in head section.

		//Set page response headers.
	}

	//Gets the rtequested view path from a URI string.
	// - $Str_RequestUri:		URI string to find the view path variables for
	// * Return:				The view path of the requested URI as an array
	public function get_view_path($Str_RequestUri)
	{
		$Arr_ViewPath = array();

		//Get requested file path in url.
		$Int_RequestGetVarStart = strpos($Str_RequestUri, '?');
		$Str_RequestUri = ($Int_RequestGetVarStart !== false)? substr($Str_RequestUri, 0, $Int_RequestGetVarStart): $Str_RequestUri;
		$Arr_RequestValues = explode('/', $Str_RequestUri);

		//Get view path of requested webpage.
		foreach ($Arr_RequestValues as $Str_RequestValue)
		{
			if ($Str_RequestValue)
				$Arr_ViewPath[] = $Str_RequestValue;
		}

		return $Arr_ViewPath;
	}

	//Configures page request from http variables and locallises webpage interface to be built.
	//*!*There is a serious oversight with this function.
	//I need to be able to access get vars request string and full uri through the $config var
	//Should also extend that to the post and cookie vars, but not sure how to format them yet
	//This all ties into how the builder interprets the {module name.uri}  tag string
	// * Return:				VOID
	private function configure_request()
	{
		//Remember domain name.
		$this->Str_RequestedDomainName = $_SERVER['SERVER_NAME'];
		define('MW_CONST_STR_URL_DOMAIN', $this->Arr_SysConfig['root_url']);

		//Connect to database.
		$this->Obj_Storage->connect(array('database' => $this->Arr_SysConfig['x_database'],
									'username' => $this->Arr_SysConfig['x_username'],
									'password' => $this->Arr_SysConfig['x_password'],
									'location' => $this->Arr_SysConfig['x_location'],
									'driver' => $this->Arr_SysConfig['driver']));

		//Set versioning and user permissions.
		//*!*Thyere is no versioning in this framework, remove all references to it.
		$this->set_version($this->Arr_SysConfig['version']);
		$this->initialise_user();

		//Set default timezone.
		date_default_timezone_set($this->Arr_SysConfig['timezone']);

		//Get requested file path in url.
		$Arr_ViewPath = $this->get_view_path($_SERVER['REQUEST_URI']);
		$Str_ViewPath = '/'.implode('/', $Arr_ViewPath);

		//Set webpage meta data.
		//*!#Pull the meta data from somewhere else. - we might jsut be getting rid of everything else from here.
		//*!*Removing all from here out of the configure routine.
		//$this->Arr_RequestedPageInfo = array('meta' => $Arr_PathNodes[$Int_AccessDepth]);

		//Set request configuration loaclly.
		//*!*Need to deal with get vars?
		$this->Str_RequestedViewPath = $Str_ViewPath;
		//*!*Need to get these from the router
		//$this->Str_RequestedFilePath = $Str_FilePath;
		//$this->Arr_RequestedPathVars = $Arr_PathVars;

		//*!*This is a reworking to add these to the config to make them accessible.
		//Is this a good idea? I Ddon't know yet. It might be a better way than separating them.
		//I don't know if this is a potential securiy risk with user-generated modules.
		$this->Arr_SysConfig['full_path'] = $this->Arr_SysConfig['root_url'].$Str_ViewPath;
		$this->Arr_SysConfig['view_path'] = $Str_ViewPath;
		//$this->Arr_SysConfig['file_path'] = $Str_FilePath;
		//$this->Arr_SysConfig['path_vars'] = $Arr_PathVars;
		$this->Arr_SysConfig['path_vars'] = $Arr_ViewPath;

		//*!*Still unsure why this is useful.
		//Format GET varialbes in to a string
		//$Int_RequestGetVarStart = strpos($_SERVER['REQUEST_URI'], '?');
		//$this->Arr_SysConfig['get_vars'] = ($Int_RequestGetVarStart !== false)? substr($_SERVER['REQUEST_URI'], $Int_RequestGetVarStart, strlen($_SERVER['REQUEST_URI'])): '';

		//Route request.
		$Obj_Router = new MW_Router();
		$Obj_Router->routes();
		return $Obj_Router->build();
	}

	//Finalises request and prints requested webpage.
	// - $Str_RequestedWebpage:	Webpage built by builder
	// - $Flt_SecurityCode:		Destruct code
	// * Return:				VOID
	public function send_response($Str_RequestedWebpage, $Flt_SecurityCode)
	{
		//Test for improper use of method.
		if ($Flt_SecurityCode != $this->Flt_SecurityCode)
		{
			$this->handle_exception('Unathorised call of MW_Foreman->send_response()', 'MW:100');
			return;
		}

		//Set requested interface locally.
		$this->Str_RequestedInterface = $Str_RequestedWebpage;

		//Write and close the session.
		session_write_close();

		//Close all database connections.
		$this->Obj_Storage->disconnect();

		//Send page to client.
		echo $this->Str_RequestedInterface;
		MW_Events::post_send_response();
		$this->__destruct();

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//   S Y S T E M   O B J E C T   A P I   W R A P P E R   F U N C T I O N S   //
///////////////////////////////////////////////////////////////////////////////
//*!*Additional module APU function for authentication, this section of code needs a little reorganisation!

	//Gets the value of the session variable $Str_Session.
	// - $Str_Name:					Name of session variable to get value for
	// * Return:					Value of session variable, otherwise null
	public function get_session($Str_Name)
	{
		return $this->Obj_Authorise->get_session($Str_Name);
	}

	//Sets session variable $Str_Name to value $Mix_Value.
	// - $Str_Name:					Name of session variable to get or set
	// - $Mix_Value:				Value to set session variable, if null variable is unset
	// * Return:					True if variable was set, otherwise false
	public function set_session($Str_Name, $Mix_Value)
	{
		return $this->Obj_Authorise->set_session($Str_Name, $Mix_Value);
	}

	//Gets boolean if the user is logged in or not.
	// * Return:				true if user is logged in, otherwise false
	public function is_logged_in()
	{
		if ($this->Obj_Authorise->is_logged_in())
			return true;

		return false;
	}

	//Logs user in under the credentials supplied by user model $Obj_User.
	// - $Obj_User:				Reference of a user data model with minimum properties username and password set
	public function log_user_in(&$Obj_User)
	{
		$this->Obj_Authorise->log_user_in($Obj_User);

		return;
	}

	//Logs user out of their current session.
	// - $Bol_DestroySession		Destroys the session and cookie values, default = false(keep session data)
	// * Return:					VOID
	// * NB: This function does not end the session, only sets user as not logged in
	public function log_user_out($Bol_DestroySession=false)
	{
		$this->Obj_Authorise->log_user_out($Bol_DestroySession);
	}

	//Gets the session data for the user.
	// * Return:				Core user session values.
	public function get_user_session()
	{
		return $this->Obj_Authorise->get_user_session();
	}

	//Makes configuration value available for public use.
	// - $Str_ConfigKey:		Configuration setting key
	// * Return:				Non-sensitive configuration value, otherwise null
	//*!*IMPORTANT: I need to set internal defaults for all core values so that internal code integrity is maintained.
	public function config($Str_ConfigKey)
	{
		//If configuration setting is not sensitive return value.
		if (strpos($Str_ConfigKey, 'x_') === false)
		{
			return $this->Arr_SysConfig[$Str_ConfigKey];
		}
		//Else if the install file is present allow sensitive values.
		elseif (file_exists(MW_CONST_STR_DIR_INSTALL.'/install.php'))
		{
			return $this->Arr_SysConfig[$Str_ConfigKey];
		}
		//Otherwise throw a system access error.
		else
		{
			$this->handle_exception('Public attempt to access sensitve configuration value', 'MW:100');
		}

		return null;
	}

	//Sets version to authorise object and holds it in session.
	// - $Flt_Version:			Version of domain being accessed
	// * Return:				VOID - or true/false session set?
	public function set_version($Flt_Version)
	{
		$this->Obj_Authorise->set_version($Flt_Version);
		return;
	}

	//Gets version set to authorise object.
	// * Return:				Version held by the authorise object
	public function get_version()
	{
		return $this->Obj_Authorise->version;
	}

	//Starts/resumes user session and applies permissions.
	//*!*Is this the correct approach?
	// * Return:				True if session is resuming, false if session is started
	public function initialise_user()
	{
		$this->Obj_Authorise->start_session();
		$this->Obj_Authorise->set_user();
		return;
	}

//*!*I think from about here I can fuck off the rest of this section.
//Much of this has to removed or reworked in the existing code base,
//the above functions take care of most of the functionallity

	//Sets meta data to the webpage interface.
	// - $Str_Name:				Name of the meta data value to set
	// - $Str_Value:			Value of the meta data to set
	// * Return:				VOID
	public function set_interface_data($Str_Name, $Str_Value)
	{
		$Arr_NewRequestedPageInfo = $this->Arr_RequestedPageInfo;
		$Arr_NewRequestedPageInfo[$Str_Name] = $Str_Value;
		$this->Arr_RequestedPageInfo = $Arr_NewRequestedPageInfo;

		return;
	}

	//Gets meta data of the webpage interface.
	// - $Str_Name:				Name of the meta data value to retrieve
	// * Return:				Value fo the interface meta data named $Str_Name
	public function get_interface_data($Str_Name)
	{
		if (isset($this->Arr_RequestedPageInfo[$Str_Name]))
		{
			return $this->Arr_RequestedPageInfo[$Str_Name];
		}

		return array();
	}

	//Gets authorisation creditials of user
	public function authorisation()
	{
		$Str_Authorisation = '0x000000';
		return $Str_Authorisation;
	}
/*
	//Wrapper function for the authorisation object.
	// - $Bit_AccessLevel:			Access flag being tested to be contained with the flags
	// - $Bit_AccessFlags:			Access flags against which the access level is being sought
	// * Return:					TRUE if $Bit_AccessLevel value is within $Bit_AccessFlags, otherwise FALSE.
	// * NB: $Bit_AccessLevel can be a combnation of flags if desired.
	public function has_access($Bit_AccessLevel, $Bit_AccessFlags)
	{
		return $this->Obj_Authorise->has_access($Bit_AccessLevel, $Bit_AccessFlags);
	}
*/
	public function has_access($Arr_AccessRules, $Str_ModelStatus, $Int_ModelAccess, $Bol_HasAccess=false)
	{
		return $this->Obj_Authorise->has_access($Arr_AccessRules, $Str_ModelStatus, $Int_ModelAccess, $Bol_HasAccess);
	}

	//Determines whether user has view access.
	// - $Var_MixedSubject:		Parcel object or MW_CONST_BIT_ACCESS_VIEW_*
	// * Return:				True if user has view access, otherwise false
	// NB: This function has a variable number of parameters, all must test positive.
	//*!*These two functions need to be rebuilt and needs to distinguish between file and database access.
	public function has_view_access()
	{
		//Find out how many parameters have been supplied.
		$Int_NumOfArgs = func_num_args();
		$Arr_ArgsList = func_get_args();

		//Loop through each parameter.
		for ($i = 0; $i < $Int_NumOfArgs; $i++)
		{
			$Bit_ViewAccessLevel = 0;
			$Bol_ArgIsValid = false;

			//If the parameter is a parcel object get parcel view access.
			if ($this->is_parcel($Arr_ArgsList[$i]))
			{
				$Bit_ViewAccessLevel = $Arr_ArgsList[$i]->get_view_access();
				$Bol_ArgIsValid = true;
			}
			//Else if the parameter can undergo a bitwise operation let it.
			elseif ($this->is_bitwise($Arr_ArgsList[$i]))
			{
				$Bit_ViewAccessLevel = $Arr_ArgsList[$i];
				$Bol_ArgIsValid = true;
			}
			//Otherwise throw an error for incorrect parameter.
			else
			{
				//Do error here
			}

			//Test the access level against the authorisation object.
			if (!$Bol_ArgIsValid || !$this->Obj_Authorise->has_view_access($Bit_ViewAccessLevel))
				return false;
		}

		return true;
	}

	//Determines whether user has edit access.
	// - $Var_MixedSubject:		Parcel object or MW_CONST_BIT_ACCESS_EDIT_*
	// * Return:				True if user has edit access, otherwise false
	// NB: This function has a variable number of parameters, all must test positive.
	public function has_edit_access()
	{
		//Find out how many parameters have been supplied.
		$Int_NumOfArgs = func_num_args();
		$Arr_ArgsList = func_get_args();

		//Loop through each parameter.
		for ($i = 0; $i < $Int_NumOfArgs; $i++)
		{
			$Bit_EditAccessLevel = 0;
			$Bol_ArgIsValid = false;

			//If the parameter is a parcel object get parcel view access.
			if ($this->is_parcel($Arr_ArgsList[$i]))
			{
				$Bit_ViewAccessLevel = $Arr_ArgsList[$i]->get_view_access();
				$Bol_ArgIsValid = true;
			}
			//Else if the parameter can undergo a bitwise operation let it.
			elseif ($this->is_bitwise($Arr_ArgsList[$i]))
			{
				$Bit_ViewAccessLevel = $Arr_ArgsList[$i];
				$Bol_ArgIsValid = true;
			}
			//Otherwise throw an error for incorrect parameter.
			else
			{
				//Do error here
			}

			//Test the access level against the authorisation object.
			if (!$Bol_ArgIsValid || !$this->Obj_Authorise->has_edit_access($Bit_EditAccessLevel))
				return false;
		}

		return true;
	}

	//Debug variable function.
	//Needs to b expanded.
	public function get_status($Var_Data)
	{
		$this->Obj_Debugging->get_status($Var_Data);
	}


///////////////////////////////////////////////////////////////////////////////
//                   B U I L D E R   F U N C T I O N S                       //
///////////////////////////////////////////////////////////////////////////////

	//Creates a new builder object and sets template to build.
	// - $Str_Plan:				Build plan string to be used in the construction of the interface.
	// * Return:				Builder object
	// * NB: $Str_Plan has a special case of false if the page request is yet to be configured
	//*!* this stuff is going to be rearranged so that the plugin file is not calling a plan->bind->build statement.
	public function plan($Str_Plan)
	{
		//If request hasn't been configured setup interface.
		//*!* This stuff is coming out of here aned goining into the plugin and/or cinfigure routine.
		$Obj_Module = null;
		if (($Str_Plan === false) && ($this->Str_RequestedInterface === false))
		{
			MW_Events::pre_configure_request();
			$Obj_Module = $this->configure_request();
			MW_Events::post_configure_request();
			//$Str_Plan = $this->Str_RequestedInterface;
		}
		else
		{
			//*!*I don't know if routine is going to be called, may have to with layout inheritance.
			$Obj_Module = $this->utility('builder');
		}

		//Make builder object.
		$Obj_Module->bind(array('config' => $this->Arr_SysConfig,
						'user' => $this->get_user_session(),
						'page' => $this->helper('page')->set_page_data($this->get_interface_data('meta'))));

		return $Obj_Module;
	}

	//Sets and gets the section string for the current interface section stack.
	// - $Str_SectionName:			Name of the section to get and set string for
	// - $Str_SectionString:		Interface markup string of the section to set
	// * Return:					The set section string
	// * NB: This function currently is a stright replace of the first section string set.
	public function section($Str_SectionName, $Str_SectionString)
	{
		//Decouple section stack.
		$Arr_NewBuildSections = $this->Arr_BuildSections;

		//If the section is not set, set it.
		if (!array_key_exists($Str_SectionName, $Arr_NewBuildSections))
		{
			$Arr_NewBuildSections[$Str_SectionName] = $Str_SectionString;
		}
		//Otherwise return the section string.
		else
		{
			$Str_SectionString = $Arr_NewBuildSections[$Str_SectionName];
		}

		//Recouple section stack.
		$this->Arr_BuildSections = $Arr_NewBuildSections;

		return $Str_SectionString;
	}

	public function lang($Str_Constant, $Str_Language=false)
	{
		//If no lanuage is defined get the settings value.
		if (!$Str_Language)
		{
			if (!$Str_Language = $this->config('language'))
			{
				$this->handle_exception('No language defined in settings.', 'MW:101');
			}
		}
		
		//Get variables from language string.
		//*!*This is a bit hacky, need to make a more robust test with error handling in the future.
		//lang_const[var1][var2]
		//this is the % lnaguage %
		$Arr_Constant = explode('[', $Str_Constant);
		$Str_Constant = $Arr_Constant[0];
		$Arr_LangValues = array();
		if (count($Arr_Constant) > 1)
		{
			for ($i = 1; $i < count($Arr_Constant); $i++)
			{
				$Arr_LangValues[] = str_replace(']', '', $Arr_Constant[$i]);
			}
		}

		//If the local is not in the register set it.
		if (!isset($this->Arr_Locales[$Str_Language]))
		{
			$this->locale($Str_Language);
		}

		//Get the locale object constant value.
		if (!isset($this->Arr_Locales[$Str_Language]->$Str_Constant))
		{
			//$CMD->handle_exception('Constant '.$Str_Constant.' has not been set on '.$Str_Language.' locale object.', 'MW:101');
			return $Str_Constant;
		}

		$Str_Text = $this->Arr_Locales[$Str_Language]->$Str_Constant;

		//Swap in language values into language text.
		//*!*This replacement symbol needs to be defined as a constant
		if ($Arr_LangValues)
		{
			foreach ($Arr_LangValues as $Str_Value)
			{
				if ($Int_Pos = strpos($Str_Text, '%'))
				{
					$Str_Text = substr_replace($Str_Text, $Str_Value, $Int_Pos, 1);
				}
			}
		}

		return $Str_Text;
	}


///////////////////////////////////////////////////////////////////////////////
//              S Y S T E M   U T I L I T Y   F U N C T I O N S              //
///////////////////////////////////////////////////////////////////////////////

	//Converts module query string into a key/value array.
	// - $Str_ModuleQuery:			Module query string
	// * Return:					$Str_ModuleQuery as a key/value array
	// * NB: This is a convenience formatting function.
	private function get_query_string_as_array($Str_ModuleQuery)
	{
		//Declare return array.
		$Arr_ModuleQuery = array();

		if ($Str_ModuleQuery != '')
		{
			//Carve up query string and assemble return array.
			$Arr_QueryKeyValues = explode('&', $Str_ModuleQuery);
			foreach ($Arr_QueryKeyValues as $Str_KeyValuePair)
			{
				$Arr_KeyValuePair = explode('=', $Str_KeyValuePair);
				$Arr_ModuleQuery[$Arr_KeyValuePair[0]] = $Arr_KeyValuePair[1];
			}
		}
		else
		{
			$Arr_ModuleQuery[''] = '';
		}
		
		return $Arr_ModuleQuery;
	}

	//Tests whether $Var_Mixed is a monkeywrench parcel object.
	// - $Var_Mixed:			Variable to test wehther or not it is a parcel
	// * Return:				True if $Var_Mixed is a monkeywrench parcel, otherwise false
	//*!*This function calling the wrong parent name but it is breaking the setting of values to the module object in the builder
	//I cannot locate this funcion call to correct the error so the error, which makes it work remains.
	//I suspect the error could also lie in the use of has_view/edit_access() which is being used inmodule building,
	//or parcel collection querying. Might need to look up the query building chain to isolate this breaking function call
	public function is_parcel($Var_Mixed)
	{

		//if (is_object($Var_Mixed) && get_parent_class($Var_Mixed) == 'MW_Utility_Parcel')
		if (is_object($Var_Mixed) && get_parent_class($Var_Mixed) == 'MW_Parcel')
		{
			return true;
		}

		return false;
	}
	
	//Test whether $Var_Mixed is a variable capable of bitwise operation.
	// - $Var_Mixed:			Variable to test whether or not
	public function is_bitwise($Var_Mixed)
	{
		if (is_numeric($Var_Mixed) && !is_float($Var_Mixed) && $Var_Mixed >= 0)
			return true;
		else return false;
	}



///////////////////////////////////////////////////////////////////////////////
//          F I L E   M A N I P U L A T I O N   F U N C T I O N S            //
///////////////////////////////////////////////////////////////////////////////
//This tuff should be moved into the file hepler function maybe...
	//Gets parcel data on file of parcel type $Str_ParcelType with the key $Str_ParcelKey.
	// - $Str_FileName:			Name of file to be accessed as a string
	// - $Str_AccessMode:		File stream access mode, fopen()
	// * Return:				Parcel file string on success, otherwise false
	//*!*I'm now testing that this function has been moved to the helper
/*
	public function get_file_as_string($Str_FileName, $Str_AccessMode='r+')
	{
		$Str_FileData = '';

		//If file does not exist handle expection.
		if (!file_exists($Str_FileName))
		{
			$this->handle_exception('Could not read file. File '.$Str_FileName.' not found', 'MW:100');
			return false;
		}

		//If file is not readable handle exception.
		if (!is_readable($Str_FileName))
		{
			$this->handle_exception('Could not read file. File '.$Str_FileName.' could not be read', 'MW:100');
			return false;
		}

		$Res_FileHandle = fopen(path_request($Str_FileName), $Str_AccessMode);
		$Str_FileData = fread($Res_FileHandle, filesize($Str_FileName));
		fclose($Res_FileHandle);

		return $Str_FileData;
	}
*/
	//Formatts and saves parcel data $Mix_ParcelData to file.
	// - $Str_DataFile:			Data string to save to file
	// - $Str_FileName:			Name of file to be accessed to write data
	// - $Str_AccessMode:		File stream access mode, fopen()
	// - $Bol_VerifyFile:		Verifies that the file exists before writing
	// * Return true on success, otherwise false
	//*!*I'm now testing that this function has been moved to the helper
/*
	public function save_string_as_file($Str_DataFile, $Str_FileName, $Str_AccessMode='w', $Bol_VerifyFile=false)
	{
		//If the file is not writeable handle error.
		if ($Bol_VerifyFile && !file_exists($Str_FileName))
		{
			$this->handle_exception('Could not write to file. File '.$Str_FileName.' does not exist', 'MW:100');
			return false;
		}

		//If the file is not writeable handle error.
		if (!is_writable($Str_FileName))
		{
			$this->handle_exception('Could not write to file. File '.$Str_FileName.' is not writable', 'MW:100');
			return false;
		}

		$Res_FileHandle = fopen($Str_FileName, $Str_AccessMode);
		if (!fwrite($Res_FileHandle, $Str_FileData))
		{
			$this->handle_exception('Save file '.$Str_FileName.' failed.', 'MW:100');
		}

		fclose($Res_FileHandle);

		return true;
	}
*/


///////////////////////////////////////////////////////////////////////////////
//                  X Q L   P A R S I N G   F U N C T I O N S                //
///////////////////////////////////////////////////////////////////////////////
//*!*I was kinda in the middle of all this shit but it's gone onto the backcurner while more pressing stuff is being attented to.

	//Gets the tree within a DOM element as a string.
	// - $Obj_Element:			Element whose content is being retrieved
	// * Return:				Text content of $Obj_Element's full tree as a string
	// * NB: If $Obj_Element is a null object then an empty string isreturned.
	public function get_element_content_as_string($Obj_Element)
	{
		//If there is no element return an empty string.
		if (!$Obj_Element)
			return '';

		//Import element into new document.
		$Obj_HolderDocument = new DOMDocument();
		$Obj_HolderDocument->loadXML('<holder></holder>');
		$Obj_DocumentElement = $Obj_HolderDocument->documentElement;
		$Obj_ImportedElement = $Obj_HolderDocument->importNode($Obj_Element, true);
		$Obj_DocumentElement->appendChild($Obj_ImportedElement);

		//Convert document to string.
		$Str_HolderNode = $Obj_HolderDocument->saveXML($Obj_ImportedElement);

		//Cut content from element string.
		$Str_OpenTagLength = strpos($Str_HolderNode, '>') + 1;
		$Str_CloseTagLength = strrpos($Str_HolderNode, '<');
		$Str_DataLength = $Str_CloseTagLength - $Str_OpenTagLength;

		$Str_Element = substr($Str_HolderNode, $Str_OpenTagLength, $Str_DataLength);

		return $Str_Element;
	}

	//Parses XQL document string into a DOM Document object.
	// - $Str_XqlDocument:		XQL Document string
	// * Return:				XQL Document object on success, otherwise false
	// * NB: I may put DTD validation into this routine
	public function parse_xql_string($Str_XqlDocument)
	{
		$Obj_XqlDocument = new DOMDocument();
		if (@$Obj_XqlDocument->loadXML($Str_XqlDocument) === false)
			return false;

		return $Obj_XqlDocument;
	}

	//Converts and XQL Document string to the local query stack from processing by the driver.
	// - $Mix_XqlDocument:		XQL Document object or string to add to query stack.
	// * Return:				True on success, otherwise false
	public function add_xql_to_stack($Obj_XqlDocument)
	{
		//Decouple stack.
		$Arr_NewQueryStack = $this->Arr_QueryStack;
		
		//Check that the document string exists.
		if (!$Obj_XqlDocument)
		{
			$this->handle_exception('No value given as XQL Document', 'MW:100');
			return false;
		}

		//If the function parameter is a string load document.
		if (is_string($Obj_XqlDocument))
		{
			$Obj_XqlDocument = $this->parse_xql_string($Obj_XqlDocument);
			
			if ($Obj_XqlDocument === false)
			{
				$this->handle_exception('XQL Document is not valid XQL string', 'MW:100');
				return false;
			}
		}
		//If the object is not a DOMDocument handle exception.
		elseif(get_class($Obj_XqlDocument) != 'DOMDocument')
		{
			$this->handle_exception('No value given as XQL Document', 'MW:100');
			return false;
		}

		//Loop through each node.
		//*!*This first loop might be excessive so just ignore it for the time being.
		$Arr_Processes = $Obj_XqlDocument->getElementsByTagName('query');
		foreach ($Arr_Processes as $Obj_Process)
		{
			$Arr_Queries = $Obj_Process->childNodes;
			foreach ($Arr_Queries as $Obj_Query)
			{
				//If node is an element add it to the stack.
				if ($Obj_Query->nodeType == XML_ELEMENT_NODE)
				{
					//Parse query.
					$Str_QueryNode = '';

					//If the query is properly formatted add it to stack.
					if ($Obj_Query = $this->Obj_Storage->validate_query_element($Obj_Query))
					{
						//Add system configuration to queries.
						$Obj_Query->setAttribute('access', $this->athorisation());
						//*!* Driver switching later!
						$Obj_Query->setAttribute('driver', $this->config['driver']);
						//*!*This is to be done later for multiple db connections/types.
						$Obj_Query->setAttribute('connect', 'some-connection-key');

						//Validation for query elements.

						//Do batch branching.
						$this->Obj_Storage->add_query_to_batch($Obj_Query);

						//Harvest query metrics
						$this->Obj_Analytics->add_query_to_metrics($Obj_Query);

						$Arr_NewQueryStack[] = $Obj_Query;
					}
					//Otherwise handle exception.
					else
					{
						$this->handle_exception('XQL query element '.$this->get_element_content_as_string($Obj_Query).' not valid', 'MW:100');
						
						//Store bad query for analysis.
						$this->Arr_QueryErrors[] = $Obj_Query;
					}
				}
			}
		}

		//Recouple stack.
		$this->Arr_QueryStack = $Arr_NewQueryStack;

		return true;
	}
	
	//Executes XQL query stack, prepares batch processing and gathers metrics.
	// * Return:				True on successful execution, otherwise false
	public function execute_xql_stack()
	{
		//Validate data elements in stack?
		$Arr_NewQueryResults = $this->Arr_QueryResults;

		//Execute queries in stack.
		if ($this->Arr_QueryStack)
		{
			foreach ($this->Arr_QueryStack as $Obj_Query)
			{
				//Get query system level details.
				$Str_Driver = $Obj_Query->getAttribute('driver');
				$Str_Connect = $Obj_Query->getAttribute('connect');

				//If driver has not been loaded get it.
				if (!isset($this->Obj_Storage->Arr_Drivers[$Str_Driver]))
				{
					if (!$this->Obj_Storage->load_driver($Str_Driver))
					{
						//Handle driver load failure.
						$this->handle_exception('Failed to load database driver '.$Str_Driver, 'MW:100');
						continue;
					}
				}

				//If db connection doesn't exist create new one.
				//*!* Connections will be predefined on config but with resource to be loaded on demand.
				if (!isset($this->Obj_Storage->Arr_Connections[$Str_Connect]['resource']))
				{
					if (!$this->Obj_Storage->new_connection($Str_Connect, $Str_Driver))
					{
						//Handle connection failure.
						$this->handle_exception('Failed to make database connection '.$Str_Connect, 'MW:100');
						continue;
					}
				}

				//Make database query.
				$Arr_Results = $this->Obj_Storage->execute($Obj_Query, $Str_Driver, $Str_Connect);
				if ($Arr_Results === false)
					$this->handle_exception('Failed to make database query '.$this->get_element_content_as_string($Obj_Query), 'MW:100');
				else
					$Arr_NewQueryResults[] = $Arr_Results;
			}
		}

		//Empty query stack.
		$this->Arr_QueryStack = array();

		//Recouple results stack.
		$this->Arr_QueryResults = $Arr_NewQueryResults;

		return true;
	}



///////////////////////////////////////////////////////////////////////////////
//               S T O R A G E   A P I   F U N C T I O N S                   //
///////////////////////////////////////////////////////////////////////////////

	//*!*This is a hack just to do basic databasing for this project.
	//*!*NB: Queries will be done through the DB drivers probably using this as a wrapper
	//*!*Notice how I haven't done any query validation or cleaning here
	// - $Arr_Query:			Query property set.
	// * Returns  query results.
	public function query($Arr_Query)
	{
		$Arr_Results = false;

		//If the query array is empty return.
		if (!$Arr_Query) return;
		
		//Add system setting to the query properties.
		//*!*I'm really not sure I should be supplying these to each query, they are supplied on connection
		//and if that goes down then I'll rather fetch a new driver than try to reconnect, that would give me better
		//error handling and analysis of the database issue which fucked the connection up.
		$Arr_Query['database'] = $this->Arr_SysConfig['x_database'];

		$Arr_Results = $this->Obj_Storage->query($Arr_Query);

		return $Arr_Results;
	}

	//Saves data set to a data holding object.
	// - $Obj_Data:				Data holding object: parcel, package, form or collection
	// * Return:				True on success, otherwise false
	//*!*I'm prbably only going to make this for saving parcels and packages directly
	//Not sure what I am doing with forms and collections at this point in time.
	public function save($Obj_Data)
	{
		$Bol_Success = false;

		//*!*For the moment we are just calling a save on these objects until I can figure out wtf I am doing with this method.
		//I think I'm entering this save into a registrar to prevent saving at the parcel/model level.
		$Bol_Success = $Obj_Data->save();

		//*!*I'm not sure I'm returning a result, I think I'll just throw an exception instead.

		return $Bol_Success;
	}



///////////////////////////////////////////////////////////////////////////////
//            P A R C E L   B U I L D I N G   F U N C T I O N S              //
///////////////////////////////////////////////////////////////////////////////
//*!*I think these functions need to be removed when I clean up the parcels object properties.
//These are more really to be used with 'packages' but that api is yet to be written.

	//Loads parcel data which is most recent at or below version.
	// - $Obj_Parcel:			Parcel whose data is being loaded from the database
	// - $Flt_Version:			Version of parcel data which is being retrieved from the database, default = 0.0(Foreman version)
	// * Return:				True if parcel matching version $Flt_Version is found, otherwise false
	public function load_data_by_version($Obj_Parcel, $Flt_Version=0.0)
	{
		$Int_ParcelId = $Obj_Parcel->get_id();
		$Int_ParcelType = $Obj_Parcel->get_type();

		//Load parcel data corresponding to version.
		if (!$this->Obj_Storage->load_parcel_data_by_version($Int_ParcelId, $Int_ParcelType, $Flt_Version))
		{
			//Handle error if parcel data matching versioning is not found.
			$this->handle_exception('Parcel data of id '.$Int_ParcelId.' not found.', 'MW:100');
			return false;
			//*!*might do a different escape other than a null response here...
		}

		return true;
	}

	//Loads parcel data which is most recent at or below timestamp.
	// - $Obj_Parcel:			Parcel whose data is being loaded from the database
	// - $Int_Timestamp:		Timestamp of parcel data which is being retrieved from the database, default = 0(Foreman timestamp)
	// * Return:				True if parcel matching timestamp $Int_Timestamp is found, otherwise false
	public function load_data_by_timestamp($Obj_Parcel, $Int_Timestamp=0)
	{
		$Int_ParcelId = $Obj_Parcel->get_id();
		$Int_ParcelType = $Obj_Parcel->get_type();

		//Load parcel data corresponding to version.
		if (!$this->Obj_Storage->load_parcel_data_by_timestamp($Int_ParcelId, $Int_ParcelType, $Int_Timestamp))
		{
			//Handle error if parcel data matching versioning is not found.
			$this->handle_exception('Parcel data of id '.$Int_ParcelId.' not found.', 'MW:100');
			return false;
			//*!*might do a different escape other than a null response here...
		}

		return true;
	}

	//Gets parcel with meta data loaded only using it's id as the reference.
	// - $Int_ParcelType:		Type of parcel being created, MW_CONST_INT_PARCELTYPE_*
	// - $Int_ParcelId:			Unique id number of the parcel object
	// - $Int_Version:			Maximum  version number of parcel to load, default = 0(version being used globally)
	// * Return:				Parcel with data loaded, null if parcel with $Int_ParcelId id is not found
	public function get_parcel_by_id($Int_ParcelType, $Int_ParcelId, $Int_Version=0)
	{
		//If parcel id is empty handle error.
		if (!$Int_ParcelId)
			$Int_ParcelId = $this->handle_exception('Parcel id not supplied', 'MW:!00');

		//Get class file.
		$Obj_Parcel = $this->parcel($Int_ParcelType);
		$Obj_Parcel->set_id($Int_ParcelId);

		//Load parcel meta information.
		if (!$this->Obj_Storage->load_parcel_meta($Obj_Parcel))
		{
			//Handle error if parcel was not found.
			$this->handle_exception('Parcel by id '.$Int_ParcelId.' not found.', 'MW:100');
			return null;
			//*!*might do a different escape other than a null response here...
		}

		return $Obj_Parcel;
	}

	//Gets parcel with meta data loaded only using it's key as the reference.
	// - $Int_ParcelType:		Type of parcel being created, MW_CONST_INT_PARCELTYPE_*
	// - $Str_ParcelKey:		Unique key string of the parcel object
	// - $Int_Version:			Maximum  version number of parcel to load, default = 0(version being used globally)
	// * Return:				Parcel with data loaded, null if parcel with $Str_ParcelKey key is not found
	public function get_parcel_by_key($Int_ParcelType, $Str_ParcelKey, $Int_Version=0)
	{
		//If parcel key is empty handle error.
		if (!$Str_ParcelKey)
			$Str_ParcelKey = $this->handle_exception('Parcel key not supplied', 'MW:100');

		//Get class file.
		$Obj_Parcel = $this->parcel($Int_ParcelType);
		$Obj_Parcel->set_key($Str_ParcelKey);

		//Load parcel meta information.
		if (!$this->Obj_Storage->load_parcel_meta($Obj_Parcel))
		{
			//Handle error if parcel was not found.
			$this->handle_exception('Parcel by key '.$Str_ParcelKey.' not found.', 'MW:100');
			return null;
			//*!*might do a different escape other than a null response here...
		}

		return $Obj_Parcel;
	}



///////////////////////////////////////////////////////////////////////////////
//        O B J E C T   R E G I S T R A T I O N   F U N C T I O N S          //
///////////////////////////////////////////////////////////////////////////////
//I need to shuffle the ording of these functions so that they make mores sense when skimming.

	//Gets the name of a class from the file location and class type
	// - $Str_FileName:			Name of file to derive class name
	// - $Str_ClassType:		Class type to build name for
	public function get_class_name($Str_FileName, $Str_ClassType)
	{
		$Str_ClassName = 'MW_'.ucfirst($Str_ClassType);

		//Build folder appendages to the class name.
		$Arr_ClassName = explode('/', $Str_FileName);
		foreach ($Arr_ClassName as $Str_NamePart)
		{
			$Str_ClassName .= '_'.ucfirst($Str_NamePart);
		}

		return $Str_ClassName;
	}

	//Registers an object file as used in the request.
	// - $Str_Name:				Name of the object to register
	// - $Str_Type:				Type of object being registered
	// - $Str_Path:				Path of the object file to include
	// * Return:				True on success, otherwise an error string
	public function register($Str_Name, $Str_Type, $Str_Path)
	{
		//If the object is already registered confirm.
		if (in_array($Str_Name, $this->Arr_Registrar[$Str_Type]))
			return true;

		//Include the file of registered object.
		//I need to hide any error message in cse the parcel is not named properly of the file is not found
		//this should be done using the output buffer.
		try
		{
			include(MW_CONST_STR_DIR_INSTALL.$Str_Path.path_request($Str_Name).'.php');
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}

		//Add object to registrar.
		$Arr_Rgistration = $this->Arr_Registrar;
		$Arr_Rgistration[$Str_Type][] = $Str_Name;
		$this->Arr_Registrar = $Arr_Rgistration;

		return true;
	}


	//Gets an include file for use by other classes.
	// - $Str_Name:				Class name of the incliude file to get
	// * Return:				true on success, otherwise false
	public function utility($Str_Name)
	{
		//If there is an error in the object registration handle exception
		if (is_string($Str_Registrar = $this->register($Str_Name, 'utilities', 'utilities/')))
		{
			$this->handle_exception("Utility file $Str_Name not registered: $Str_Registrar", 'MW:100');
			return false;
		}

		return true;
	}


	//Creates a $Str_Name driver object and connects it to the database.
	// - $Str_Type:				Name of the driver to load
	// * Return:				VOID
	//*!*This function needs to be removed adn replaced b the storage connect routine directly.
	public function driver($Str_Name)
	{
		//Register the system parcel object.
		if (!$this->utility('driver'))
			return false;

		//If there is an error in the object registration handle exception
		if (is_string($Str_Registrar = $this->register($Str_Name, 'drivers', 'drivers/')))
		{
			$this->handle_exception("Driver $Str_Name not registered: $Str_Registrar", 'MW:100');
			return false;
		}

		//Create driver object.
		$Str_Driver = $this->get_class_name($Str_Name, 'driver');
		$Obj_Driver = new $Str_Driver();

		//Set object reference id.
		$this->Int_Registrar++;
		$Obj_Driver->set_ref($this->Int_Registrar);

		//Connect driver to database.
		$this->Obj_Storage->connect($Obj_Driver, $this->Arr_SysConfig);

		return;
	}

	//Creates a system object of the class name $Str_Name.
	// - $Str_Name:				Class name of the system object to get
	// * Return:				true on success, otherwise false
	public function system($Str_Name)
	{
		//If there is an error in the object registration handle exception
		if (is_string($Str_Registrar = $this->register($Str_Name, 'system', 'system/')))
		{
			$this->handle_exception("System obejct $Str_Name not registered: $Str_Registrar", 'MW:100');
			return false;
		}

		//Create module object.
		$Str_System = 'MW_System_'.ucfirst($Str_Name);
		$Str_ObjectName = 'Obj_'.ucfirst($Str_Name);
		$this->$Str_ObjectName = new $Str_System();

		//Set object reference id.
		$this->Int_Registrar++;
		$this->$Str_ObjectName->set_ref($this->Int_Registrar);

		return true;
	}

	//Creates a model object of the class name $Str_Name.
	// - $Str_Name:				Class name of the model object to get
	// - $Mix_Reference:		Parcel Id or Key unique reference
	// - $Mix_Storage:			Storage type to use for model, default = false(use settings value)
	// * Return:				Model object on success, otherwise false
	//*!*I'm going to come into an issue where I'm mixing the variable names parcel and model.
	public function model($Str_Name, $Mix_Reference='', $Mix_Storage=false)
	{
		//Debug parcel request.
		$this->Arr_GetModel = array($Str_Name, $Mix_Reference);

		//Register the parcel utility object.
		if (!$this->utility('model'))
			return false;

		//Remove any slash at the start and end of the parcel name.
		$Str_ParcelName = (strpos($Str_Name, '/') === 0)? substr($Str_Name, 1): $Str_Name;
		$Str_ParcelName = (strpos($Str_ParcelName, '/') === strlen($Str_ParcelName) - 1)? substr($Str_Name, 0, strlen($Str_ParcelName) - 1): $Str_ParcelName;

		//If the parcel name is not valid handle excpetion
		if (!$Str_ParcelName)
		{
			$this->handle_exception('Parcel name not valid: '.$Str_ParcelName, 'MW:100');
			return false;
		}

		//If there is an error in the object registration handle exception.
		//$Str_ParcelName = (strpos($Str_ParcelName, '/') === false)? 'core/'.$Str_ParcelName: $Str_ParcelName;
		if (is_string($Str_Registrar = $this->register($Str_ParcelName, 'models', 'models/')))
		{
			$this->handle_exception("Model $Str_Dir.$Str_File not registered: $Str_Registrar", 'MW:100');
			return false;
		}

		//Create module object.
		//*!*I need to hide this error message if no parcel has been returned.
		//Ideally i'd like to test for the model before executing the rest of this method
		$Str_Parcel = $this->get_class_name($Str_ParcelName, 'model');

		$Obj_Parcel = new $Str_Parcel();

		//Set object reference id.
		$this->Int_Registrar++;
		$Obj_Parcel->set_ref($this->Int_Registrar);

		if ($Mix_Reference)
		{
			//If the reference is an integer assign parcel id.
			if(is_int($Mix_Reference))
			{
				$Obj_Parcel->set_values(array('id' => $Mix_Reference));
			}
			//Else if the reference is a string assign parcel key.
			elseif (is_string($Mix_Reference))
			{
				if ($Str_ParcelKey = $Obj_Parcel->get_key_field())
				{
					$Obj_Parcel->set_values(array($Str_ParcelKey => $Mix_Reference));
				}
				else
				{
					$this->handle_exception('Parcel '.$Str_ParcelName.' does not have a valid key assigned, reference: '.$Mix_Reference, 'MW:101');
				}
			}
			//Otherwise handle exception.
			else
			{
				$this->handle_exception('Parcel reference data is not a string or integer', 'MW:100');
			}
		}

		//If the parcel has a reference load data.
		if ($Mix_Reference)
		{
			$Str_Storage = (!$Mix_Storage)? $this->Arr_SysConfig['storage']: $Mix_Storage;
			$Obj_Parcel->load($Str_Storage);
		}

		return $Obj_Parcel;
	}

	//Creates a collection object for data selection and updating.
	// - $Mix_Model:			Model to use in collection(EG; parcel, package), or data collection
	// * Return:				Collection object on success, otherwise false
	public function collection($Mix_Model)
	{
		//Register the collection utility object.
		if (!$this->utility('collection'))
			return false;

		//Test that $Obj_Model is a legitimate model object
		if (false)
		{
			$this->handle_exception("Model object for collection not valid: $Str_ParcelName", 'MW:100');
			return false;
		}

		$Obj_Collection = new MW_Utility_Collection();

		//If it is an array set as the collection data
		if (is_array($Mix_Model))
		{
			$Obj_Collection->Arr_CollectionData = $Mix_Model;
		}
		//Elseif the parameter is a model object call collection
		//elseif ($this->is_parcel($Mix_Model))
		elseif (is_object($Mix_Model) && get_parent_class($Mix_Model) == 'MW_Utility_Model')
		{
			$Obj_Collection->collect($Mix_Model);
		}

		return $Obj_Collection;
	}

	//Creates a module object of the class name $Str_Name.
	// - $Str_Name:				Class name of the module object to get
	// * Return:				Module object on success, otherwise false
	public function module($Str_Name)
	{
		//Register the system module object.
		if (!$this->utility('module'))
			return false;

		//If there is an error in the object registration handle exception
		if (is_string($Str_Registrar = $this->register($Str_Name, 'modules', 'modules/')))
		{
			$this->handle_exception("Module $Str_Name not registered: $Str_Registrar", 'MW:100');
			return false;
		}

		//Create module object.
		$Str_Module = $this->get_class_name($Str_Name, 'module');
		$Obj_Module = new $Str_Module();

		//Set object reference id.
		$this->Int_Registrar++;
		$Obj_Module->set_ref($this->Int_Registrar);
		
		//Add default variables.
		$Obj_Module->bind(array('config' => $this->Arr_SysConfig,
						'user' => $this->get_user_session(),
						'page' => $this->helper('page')->set_page_data($this->get_interface_data('meta'))));

		return $Obj_Module;
	}

	//Creates a helper object of the class name $Str_Name.
	// - $Str_Name:				Class name of the helper object to get
	// * Return:				Helper object on success, otherwise false
	public function helper($Str_Name)
	{
		//Register the utility helper object.
		if (!$this->utility('helper'))
			return false;

		//*!*Not sure I'm doing versioning for helper objects.
		//If there is an error in the object registration handle exception
		if (is_string($Str_Registrar = $this->register($Str_Name, 'helpers', 'helpers/')))
		{
			$this->handle_exception("Helper $Str_Name not registered: $Str_Registrar", 'MW:100');
			return false;
		}

		//Create helper object.
		$Str_Helper = $this->get_class_name($Str_Name, 'helper');
		$Obj_Helper = new $Str_Helper();

		//Set object reference id.
		$this->Int_Registrar++;
		$Obj_Helper->set_ref($this->Int_Registrar);

		return $Obj_Helper;
	}

	//Creates a locale object for the lanuage $Str_Lang
	// - $Str_Language:			Language and file name of the locale object to get
	// * Return:				Locale object on success, otherwise false
	public function locale($Str_Language)
	{
		//If there is an error in the object registration handle exception
		if (is_string($Str_Registrar = $this->register($Str_Language, 'locales', 'locales/')))
		{
			$this->handle_exception("Locale obejct $Str_Language not registered: $Str_Registrar", 'MW:100');
			return false;
		}

		//Create module object.
		$Str_Locale = 'MW_Locale_'.ucfirst($Str_Language);
		$Obj_Locale = new $Str_Locale();

		//Set object reference id.
		$this->Int_Registrar++;
		$Obj_Locale->set_ref($this->Int_Registrar);

		//Add to locale register.
		if (!isset($this->Arr_Locales[$Str_Language]))
		{
			$Arr_NewLocales = $this->Arr_Locales;
			$Arr_NewLocales[$Str_Language] = $Obj_Locale;
			$this->Arr_Locales = $Arr_NewLocales;
		}

		return $Obj_Locale;
	}

	//Includes a plugin file with the path name $Str_Name.
	// - $Str_Name:				Directory path of the helper plugin file to include
	// * Return:				True on success, otherwise false
	public function plugin($Str_Name)
	{
		//Register the utility plugin object.
		if (!$this->utility('plugin'))
			return false;

		//Create driver object.
		$c = (strpos($Str_Name, '/') === false)? $Str_Name: $Str_Name;
		$Str_Plugin = $this->get_class_name('plugin', 'utility');
		$Obj_Plugin = new $Str_Plugin();
		$Obj_Plugin->plan($Str_Name);

		//Set object reference id.
		$this->Int_Registrar++;
		$Obj_Plugin->set_ref($this->Int_Registrar);

		return $Obj_Plugin;
	}

///////////////////////////////////////////////////////////////////////////////
//             E R R O R   H A N D L I N G   F U N C T I O N S               //
///////////////////////////////////////////////////////////////////////////////

	//Throws and catches a system exception.
	// - $Str_Message:			Exception message string
	// - $Str_Token:			Exception token
	// - $Int_Type:				Exception type MW_CONST_INT_EXCEPTION_TYPE_*
	//							default = MW_CONST_INT_EXCEPTION_TYPE_USER(user defined)
	// * Return:				Variable to handle system exception
	public function handle_exception($Str_Message, $Str_Token='', $Int_Type=MW_CONST_INT_EXCEPTION_TYPE_USER)
	{
		//Get exception object.
		$this->system('exception');
		return $this->Obj_Exception->handle_exception($Str_Message, $Str_Token, $Int_Type);
	}


///////////////////////////////////////////////////////////////////////////////
//         S Y S T E M   I N S T A L L A T I O N   F U N C T I O N S         //
///////////////////////////////////////////////////////////////////////////////

	//System installation API function.
	// - $Flt_SecurityCode:		Destruct code
	// * Return:				VOID
	public function install($Flt_SecurityCode)
	{
		//Run installation routine if install file is in installation folder.
		if (file_exists(MW_CONST_STR_DIR_INSTALL.'/install.php'))
		{
			//Initialise build.
			//$this->driver($this->Arr_SysConfig['driver']);	- have I cleaned these out properly?
			$this->initialise_user();
			$this->set_version($this->Arr_SysConfig['version']);
			$this->utility('model');
			$this->utility('module');

			//Get installation object.
			require(MW_CONST_STR_DIR_INSTALL.'install.php');
			$Obj_Installer = new MW_Installer();
			$Obj_Installer->bind(array('config' => $this->Arr_SysConfig,
						'user' => $this->get_user_session(),
						'page' => $this->helper('page')->set_page_data($this->get_interface_data('meta'))));

			//Do installation process.
			$Str_InstallInterface = $Obj_Installer->install();

			//Build installation interface.
			$this->Str_RequestedInterface = '';
			$this->send_response($Str_InstallInterface , $Flt_SecurityCode);

			exit;
		}

		return;
	}


}

?>