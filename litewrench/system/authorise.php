<?php
/*
	// *** SESSION DATA STATES *** //
	//Session flag and constants.
	//What is this?
	private $Bit_Access_Flag			= 0;


Session system variable names
MW_USERLOGN		(Bol) User log in status
MW_USERNAME		(Str) Username logged in as
MW_USERVRSN		(Flt) Version number for user
MW_USERLANG		(Str) Language for user
MW_USEREDIT		(Hex) Edit access of user
MW_USERVIEW		(Hex) View access of user

*/

class MW_System_Authorise extends MW_System_Base
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Instance variable used for singleton pattern. Stores a single instance
	private static $instance;

	//The session ID assigned by PHP (usually a 32 character alpha-numeric
	public static $session_id;

	//View and edit version of domain.
	public $version	= 0.0;
	
	//Access variables
	private $Bit_ViewAccess			= 1;
	private $Bit_EditAccess			= 1;



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	public function __destruct(){}

/*
	//Starts the session and sets the session_id for the class.
	public function __construct()
	{
	    //Define custom session callbacks.
		session_set_save_handler(array(&$this,'open_session'),
								array(&$this,'close_session'),
								array(&$this,'read_session'),
								array(&$this,'write_session'),
								array(&$this,'destroy_session'),
								array(&$this,'gc_session'));
		//Start the session.
		@session_start();
		self::$session_id = session_id();
	}

	//Implementation of the singleton pattern.
	public static function singleton()
	{
		if (!isset(self::$instance))
		{
        	$className = __CLASS__;
            self::$instance = new $className;
        }

        return self::$instance;
    }

    public function destroy()
    {
        foreach ($_SESSION as $var => $val)
		{
        	$_SESSION[$var] = null;
        }

        session_destroy();
	}
      
	// Disable PHP5's cloning method for session so people can't make copies of the session instance.
    public function __clone()
    {
        trigger_error('Clone is not allowed for '.__CLASS__,E_USER_ERROR);
    }

    //Returns the requested session variable.
    public function __get($var)
    {
        if (isset($_SESSION[$var]))
			return $_SESSION[$var];
		else return '';
    }

	//Sets and returns session variable.
    public function __set($var, $val)
    {
        return ($_SESSION[$var] = $val);
    }

    //Writes the current session.
    public function __destruct()
    {
        session_write_close();
    }
*/


///////////////////////////////////////////////////////////////////////////////
//           S E S S I O N   H A N D L I N G   F U N C T I O N S             //
///////////////////////////////////////////////////////////////////////////////

/*
    //Session callback functions.
    
    public function open_session()
    {
		//Open db connection here.
		//*!*Need to figure out how session id is assigned and how to tie that into a database reference
		//*!*I will be using two reference points in the database, session id and md5(username)

		//Pull user session information from database.

		//Extract user session variables.

		return true;
	}

	public function close_session()
	{
		//Close db connection here.

		return true;
	}
	
	public function read_session()
	{
		//If we can find the session variable get it.
		if (true)
		{
			
		}
		//Otherwies return nothing.
		else
		{
			return '';
		}
	}
	
	public function write_session()
	{

	}
	
	public function destroy_session()
	{
		
	}
	
	public function gc_session()
	{

	}

*/


///////////////////////////////////////////////////////////////////////////////
//              S E S S I O N   S T A T U S   F U N C T I O N S              //
///////////////////////////////////////////////////////////////////////////////

	//Gets boolean if the user is logged in or not.
	// * Return:				true if user is logged in, otherwise false
	// * NB: null is not logged in, true is no expiry, otherwise set as expiry
	public function is_logged_in()
	{
		if (isset($_SESSION[MW_STR_SESSION_USERLOGN]))
		{
			if ($_SESSION[MW_STR_SESSION_USERLOGN] === null)
			{
				return false;
			}
			elseif ($_SESSION[MW_STR_SESSION_USERLOGN] === true)
			{
				return true;
			}
			elseif ($_SESSION[MW_STR_SESSION_USERLOGN] > $_SERVER['REQUEST_TIME'])
			{
				return true;
			}
		}

		return false;
	}

	//Gets the value of session variable $Str_SessionVar
	// - $Str_Name:					Name of session variable to get valuefor
	// * Return:					Value of session variable, otherwise null
	// * NB: Session values are never null, they are unset if attempting to set as null
	public function get_session($Str_Name)
	{
		//If the user is logged in get their session value.
		if ($this->is_logged_in())
		{
			if (isset($_SESSION[$Str_Name]))
			{
				return $_SESSION[$Str_Name];
			}
		}
		//Otherwise get public defaults.
		else
		{
			global $CMD;

			$Str_SessionValue = null;
			switch ($Str_Name)
			{
				case MW_STR_SESSION_USERLOGN: $Str_SessionValue = false; break;
				case MW_STR_SESSION_USERNAME: $Str_SessionValue = 'guest'; break;
				case MW_STR_SESSION_USERMAIL: $Str_SessionValue = false; break;
				case MW_STR_SESSION_USERSTAT: $Str_SessionValue = $CMD->config('status'); break;
				case MW_STR_SESSION_USERVRSN: $Str_SessionValue = $CMD->config('version'); break;
				case MW_STR_SESSION_USERZONE: $Str_SessionValue = $CMD->config('timezone'); break;
				case MW_STR_SESSION_USERLANG: $Str_SessionValue = $CMD->config('language'); break;
				case MW_STR_SESSION_USERLOCK: $Str_SessionValue = $CMD->config('lock'); break;
				case MW_STR_SESSION_USEREDIT: $Str_SessionValue = $CMD->config('edit'); break;
				case MW_STR_SESSION_USERVIEW: $Str_SessionValue = $CMD->config('view'); break;
			}

			return $Str_SessionValue;
		}

		return null;
	}

	//Gets the session data for the user.
	// * Return:				Core user session values.
	public function get_user_session()
	{
		return array(
			'login' =>		$this->get_session(MW_STR_SESSION_USERLOGN),
			'id' =>			$this->get_session(MW_STR_SESSION_USERIDEN),
			'name' =>		$this->get_session(MW_STR_SESSION_USERNAME),
			'email' =>		$this->get_session(MW_STR_SESSION_USERMAIL),
			'status' =>		$this->get_session(MW_STR_SESSION_USERSTAT),
			'version' =>	$this->get_session(MW_STR_SESSION_USERVRSN),
			'timezone' =>	$this->get_session(MW_STR_SESSION_USERZONE),
			'language' =>	$this->get_session(MW_STR_SESSION_USERLANG),
			'view' =>		$this->get_session(MW_STR_SESSION_USEREDIT),
			'edit' =>		$this->get_session(MW_STR_SESSION_USERVIEW),
			'lock' =>		$this->get_session(MW_STR_SESSION_USERLOCK));
	}

	//Sets session variable $Mix_Index to value $Mix_Value.
	// - $Mix_Index:				Index of session variable to get or set
	// - $Mix_Value:				Value to set session variable, if null variable is unset
	// * Return:					True if variable was set, otherwise false
	public function set_session($Mix_Index, $Mix_Value)
	{
		$Bol_Success = false;

		//If value is null unset variable.
		if ($Mix_Value === null)
		{
			if (is_string($Mix_Index))
			{
				unset($_SESSION[$Mix_Index]);
				$Bol_Success = true;
			}
			elseif (is_array($Mix_Index))
			{
				$Arr_Session = $_SESSION;
				for ($i = 0; $i < count($Mix_Index); $i++)
				{
					if (isset($Arr_Session[$Mix_Index[$i]]))
					{
						//var_dump($Arr_Session[$Mix_Index[$i]]);
						$Arr_Session = $Arr_Session[$Mix_Index[$i]];
					}
					else
					{
						global $CMD;
						$CMD->handle_exception('Unsetting SESSION index not found.', 'MW:101');
						return false;
					}
				}

				//This is hacky but, there is no other way known to do this is PHP.
				//The question is, why would anyone use more than 9 dimensions in the session var?
				//The user can always get a branch higher up and swap in new values if desired.
				//*!*These comment are all wrong, I can terate through the array dimensions using references in a while loop.
				//*!*SOLUTION: I can replace this code with references to the array index!
				//Get array keys, loop through the array keys setting reference, if set then unset otherwise do exception.
				switch(count($Mix_Index))
				{
					case 1: unset($_SESSION[$Mix_Index[0]]); break;
					case 2: unset($_SESSION[$Mix_Index[0]][$Mix_Index[1]]); break;
					case 3: unset($_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]]); break;
					case 4: unset($_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]]); break;
					case 5: unset($_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]]); break;
					case 6: unset($_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]][$Mix_Index[5]]); break;
					case 7: unset($_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]][$Mix_Index[5]][$Mix_Index[6]]); break;
					case 8: unset($_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]][$Mix_Index[5]][$Mix_Index[6]][$Mix_Index[7]]); break;
					case 9: unset($_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]][$Mix_Index[5]][$Mix_Index[6]][$Mix_Index[7]][$Mix_Index[8]]); break;
					default:
						global $CMD;
						$CMD->handle_exception('Unsetting SESSION index too deep.', 'MW:101');
				}

				$Bol_Success = true;
			}
			else
			{
				global $CMD;
				$CMD->handle_exception('Session index supplied is of invalid type', 'MW:101');
			}
		}
		//Otherwise set value.
		else
		{
			if (is_string($Mix_Index))
			{
				$_SESSION[$Mix_Index] = $Mix_Value;
				$Bol_Success = true;
			}
			elseif (is_array($Mix_Index))
			{
				//See above notes about index depth.
				switch(count($Mix_Index))
				{
					case 1: $_SESSION[$Mix_Index[0]] = $Mix_Value; break;
					case 2: $_SESSION[$Mix_Index[0]][$Mix_Index[1]] = $Mix_Value; break;
					case 3: $_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]] = $Mix_Value; break;
					case 4: $_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]] = $Mix_Value; break;
					case 5: $_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]] = $Mix_Value; break;
					case 6: $_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]][$Mix_Index[5]] = $Mix_Value; break;
					case 7: $_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]][$Mix_Index[5]][$Mix_Index[6]] = $Mix_Value; break;
					case 8: $_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]][$Mix_Index[5]][$Mix_Index[6]][$Mix_Index[7]] = $Mix_Value; break;
					case 9: $_SESSION[$Mix_Index[0]][$Mix_Index[1]][$Mix_Index[2]][$Mix_Index[3]][$Mix_Index[4]][$Mix_Index[5]][$Mix_Index[6]][$Mix_Index[7]][$Mix_Index[8]] = $Mix_Value; break;
					default:
						global $CMD;
						$CMD->handle_exception('Unsetting SESSION index too deep.', 'MW:101');
						return false;
				}
					$Bol_Success = true;
			}
			else
			{
				global $CMD;
				$CMD->handle_exception('Session index supplied is of invalid type', 'MW:101');
			}
		}

		return $Bol_Success;
	}

	//Logs user in under the credentials supplied by user model $Obj_User.
	// - $Obj_User:				Reference of a user data model with minimum properties username and password set
	//*!*Might return a success or failure value.
	public function log_user_in(&$Obj_User)
	{
		global $CMD;

		//*!*I need to test here for a session id.
		//There is a need to optimise my session setting/getting using isset($_SESSION['SESSIONID'])
		//I'm not sure if his is best done here of in the session object itself
		//It might need to be done in initialise_user() metyhod instead, I'll come back to this


		//*!*These checks are yet to be done!
		//Check that object supplied is of correct type.
		//Check user credentials against database entries.

		//Get user from the database.
		if (!$Obj_User->data('id'))
		{
			$Obj_User = $CMD->model('user', $Obj_User->data('username'));
		}

		//Set user sesion values.
		$Int_Timeout = ($Obj_User->data('disable'))? $Obj_User->data('disable'): true;
		$this->set_session(MW_STR_SESSION_USERLOGN, $Int_Timeout);
		$this->set_session(MW_STR_SESSION_USERIDEN, $Obj_User->data('id'));
		$this->set_session(MW_STR_SESSION_USERNAME, $Obj_User->data('username'));
		$this->set_session(MW_STR_SESSION_USERMAIL, $Obj_User->data('email'));
		$this->set_session(MW_STR_SESSION_USERSTAT, $Obj_User->data('status'));
		$this->set_session(MW_STR_SESSION_USERLOCK, $Obj_User->data('lock'));
		$this->set_session(MW_STR_SESSION_USERVIEW, $Obj_User->data('access'));
		$this->set_session(MW_STR_SESSION_USEREDIT, $Obj_User->data('access'));
		//*!*These are the defaults but can be changed.
		$this->set_session(MW_STR_SESSION_USERVRSN, $CMD->config('version'));
		$this->set_session(MW_STR_SESSION_USERZONE, $CMD->config('timezone'));
		$this->set_session(MW_STR_SESSION_USERLANG, $CMD->config('language'));


		return;
	}

	//Logs user out of their current session.
	// - $Bol_DestroySession		Destroys the session and cookie values, default = false(keep session data)
	// * Return:					VOID
	// * NB: This function does not end the session, only sets user as not logged in
	//*!*This function needs to be thoroughly tested.
	public function log_user_out($Bol_DestroySession=false)
	{
		//Destroy user session completely.
		if ($Bol_DestroySession)
		{
			$_SESSION = array();

			if (isset($_COOKIE[session_name()]))
			{
				setcookie(session_name(), '', time() - 42000, '/');
			}

			session_destroy();
		}
		//Unset user session values.
		else
		{
			$this->set_session(MW_STR_SESSION_USERLOGN, null);
			$this->set_session(MW_STR_SESSION_USERIDEN, null);
			$this->set_session(MW_STR_SESSION_USERNAME, null);
			$this->set_session(MW_STR_SESSION_USERMAIL, null);
			$this->set_session(MW_STR_SESSION_USERSTAT, null);
			$this->set_session(MW_STR_SESSION_USERVRSN, null);
			$this->set_session(MW_STR_SESSION_USERZONE, null);
			$this->set_session(MW_STR_SESSION_USERLANG, null);
			$this->set_session(MW_STR_SESSION_USEREDIT, null);
			$this->set_session(MW_STR_SESSION_USERVIEW, null);
			$this->set_session(MW_STR_SESSION_USERLOCK, null);
		}

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//        U S E R   A U T H E N T I C A T I O N   F U N C T I O N S          //
///////////////////////////////////////////////////////////////////////////////

	//Sets user information to object from seeeion
	// * NB: If there is no session one is started.
	public function set_user()
	{

	}

	//Set the version number of website locally to $this->version.
	// - $Flt_Version:			Supplied default version of website
	// * Return:				True if version is held in session, otherwise false
	// * NB: $Flt_Version will generally come from settings.ini file
	//*!* this will probably need to be reworked into multiple set/get/override functions
	public function set_version($Flt_Version)
	{
		//If version number is held in session set it.
		if (false)
		{
			return true;
		}
		//Otherwise use supplied configuration version.
		else
		{
			$this->version = $Flt_Version;
			return false;
		}
	}

	//Gets the cascaded access values contained in the array $Arr_AccessValues.
	// - $Arr_AccessValues:			Array of access value strings
	// * Return:					Sum of all cascaded access value strings as a string
	// * NB: This fuction will build access values to the length specified by the configuration.
	//If this length is changed it will database access data will be updated to reflect
	//new length on each transaction.
	//*!*Need to use local vars for debuggin in this function
	//I should also flip the array before starting and work from the back to the front.
	//*!*This functionalty is currently being developed in the full version where the main notes are documented.
	public function build_access_string($Arr_AccessValues, $Int_AccessValues)
	{
		global $GLB_FOREMAN;
/*
		$Str_AccessValues = '1111-1111-1101-1001-1111-1111-1111-1111-1111-1111'; //parcel
		$Str_AccessValues = '1xxx-11xx-xx0x-xxxx-xxxx-xxxx-xxxx-xxxx-xxxx-xxxx'; //user
		$Str_AccessValues = '1___-11__-__0_-____-____-____-____-____-____-____'; //sql

		$Str_AccessValues = '1111-1111-1101-1001';
		$Str_AccessValues = '11xx-1111-xxxx-xxxx'; //user 1
		$Str_AccessValues = '11xx-1100-xxxx-xxxx'; //user 2

*/

		//Gt the size of the access string to build.
		$Int_AccessString = $Int_AccessValues + floor($Int_AccessValues / 4) - 1;

		$Int_Spacers = 0;
		$Str_AccessValues = '';
		for ($i = 0; $i < $Int_AccessString; $i++)
		{
			$Str_Value = MW_CONST_STR_DB_ACECSS_INHERIT;

			//If the string value is not a spacer get value.
			if (($i + 1) % 5)
			{
				foreach ($Arr_AccessValues as $Str_AccessValue)
				{
					if (!is_string($Str_AccessValue))
					{
						//*!*should add a callback holder to do eception here at the end of the funcition
						//this would make the error handling more graceful.
						$GLB_FOREMAN->handle_exception('Access value not a string', 'MW:101');
						return false;
					}

					//If the access value is defined as on or off set it.
					if (isset($Str_AccessValue[$i]) && $Str_AccessValue[$i] != MW_CONST_STR_DB_ACECSS_INHERIT)
					{
						$Str_Value = $Str_AccessValue[$i];
					}
				}
			}
			//Otherwise add spacer.
			else
			{
				$Str_Value = '-';
			}

			$Str_AccessValues .= $Str_Value;
		}

		return $Str_AccessValues;
	}

	//Buids the access interger value from a strin formqatted for database storage.
	// - $Str_AccessString:			Access string formatted for the database
	// * Return:					Access string as an integer ready for bitwise comparrison
	//*!*This function is using a hacky approach which needs a more robust routine to check for user errors.
	//This is just being punched out for functionallity so, a rewrite will be in order at a later point in time.
	//I need to keep track of the position of the character and the values that it must be w/ exception handling.
	public function build_access_bitwise($Str_AccessString)
	{
		$Int_AccessValue = 0;

		$Arr_AccessString = str_split($Str_AccessString);
		$Int_Count = 0;
		foreach ($Arr_AccessString as $Str_AccessChar)
		{
			if ($Str_AccessChar != '-')
			{
				$Int_Count++;
				if ($Str_AccessChar == '1')
				{
					$Int_AccessValue = $Int_AccessValue + pow(2, $Int_Count);
				}
			}
		}

		return $Int_AccessValue;
	}

	//*!*Document this up properly.
	// - $Arr_AccessRules:			Array of access rules governing this test.
	// - $Str_ModelStatus:			Status of the model which access is being attempted
	// - $Bit_AccessValue:			Access flag value against which the rules are being tested
	// - $Bol_HasAccess:			Boolean of current access test state
	// * Return:					TRUE if access tests were all passed, otherwise false.
	public function has_access($Arr_AccessRules, $Str_ModelStatus, $Bit_AccessValue, $Bol_HasAccess=false)
	{
		global $CMD;

		//*!*This should always be a string.
		if (is_string($Bit_AccessValue))
		{
			$Bit_AccessValue = $this->build_access_bitwise($Bit_AccessValue);
		}

		//Get the ruleset for the model status.
		foreach ($Arr_AccessRules as $Str_Status => $Arr_StatusRule)
		{
			if ($Str_ModelStatus == $Str_Status)
			{
				//Evaluate each access rule.
				foreach ($Arr_StatusRule as $Int_Access => $Arr_AccessRule)
				{
					$Int_Access = $this->build_access_bitwise($Int_Access);

					//If the access bitwise flag test succeeds evqaluate the access value.
					if ($Int_Access == ($Bit_AccessValue & $Int_Access))
					{
						if (is_bool($Arr_AccessRule))
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
								//Do evaluation of access rule.
								switch ($Arr_AccessRule[1])
								{
									case 'eq': $Bol_HasAccess = ($Arr_AccessRule[0] == $Arr_AccessRule[2])? true: false; break;
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
/*
	//Test to see if the access level being tested exists as in access flags.
	// - $Bit_AccessLevel:			Access flag being tested to be contained with the flags
	// - $Bit_AccessFlags:			Access flags against which the access level is being sought
	// * Return:					TRUE if $Bit_AccessLevel value is within $Bit_AccessFlags, otherwise FALSE.
	// * NB: $Bit_AccessLevel can be a combnation of flags if desired.
	public function has_access($Bit_AccessLevel, $Bit_AccessFlags)
	{
		//Ready access values for bitwise comparrison.
		//*!*This needs some more robust testing to ensure the values supplied are not ints as strings
		//or any other bad value.
		$Bit_AccessLevel = (is_string($Bit_AccessLevel))? $this->build_access_bitwise($Bit_AccessLevel): $Bit_AccessLevel;
		$Bit_AccessLevel = (is_string($Bit_AccessLevel))? $this->build_access_bitwise($Bit_AccessLevel): $Bit_AccessLevel;

		//Do bitwise comparrison of access level with flags.
		if ($Bit_AccessFlags & $Bit_AccessLevel == $Bit_AccessLevel)
		{
			return true;
		}

		return false;
	}
*/
	//Determines whether user has view access.
	// - $Bit_AccessLevel:		View access constant MW_CONST_BIT_ACCESS_VIEW_*
	// * Return:				True if user has view access, otherwise false
	public function has_view_access($Bit_AccessLevel)
	{
		if ($this->Bit_ViewAccess & $Bit_AccessLevel == $Bit_AccessLevel)
			return true;
		else return false;
	}

	//Determines whether user has edit access.
	// - $Bit_AccessLevel:		Edit access constant MW_CONST_BIT_ACCESS_EDIT_*
	// * Return:				True if user has edit access, otherwise false
	public function has_edit_access($Bit_AccessLevel)
	{
		if ($this->Bit_EditAccess & $Bit_AccessLevel == $Bit_AccessLevel)
			return true;
		else return false;
	}

///////////////////////////////////////////////////////////////////////////////


	//*!*is this going to be a wrapper for open_session?
	public function start_session()
	{
		//Get session id.
		session_start();

	}


}

?>