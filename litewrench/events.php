<?php
//ud_event.php
//User-defined event class file.
//Design by David Thomson at Hundredth Codemonkey.


class MW_Events
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////




///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Initialises builder parsing patterns.
	// * Return:				VOID
	public function __construct()
	{

	}



///////////////////////////////////////////////////////////////////////////////
//                    U T I L I T Y   F U N C T I O N S                      //
///////////////////////////////////////////////////////////////////////////////

	//Custom exception handler recovers a default value for user defined exceptions.
	// - $Int_Code:				Exception code integer
	public static function handle_exception($Int_Code)
	{
		$Var_ExceptionRecovery = '';

		return $Var_ExceptionRecovery;
	}


///////////////////////////////////////////////////////////////////////////////
//                   C O R E   S Y S T E M   E V E N T S                     //
///////////////////////////////////////////////////////////////////////////////

	//User defined behaviour in MW_Foreman->__construct() before request is configured.
	//  Return:				VOID
	public static function pre_configure_request()
	{
		//Default debugging behaviour.
		global $GLB_DEBUGGER;
		if ($GLB_DEBUGGER)
		{
			$Arr_DebugFolders = array();
			$Arr_DebugFiles = array();
			$Arr_DebugClasses = array();
			$Arr_DebugFunctions = array();
			$Arr_DebugProperties = array();
			$Arr_FilterFolders = array();
			$Arr_FilterFiles = array();
			$Arr_FilterClasses = array();
			$Arr_FilterFunctions = array();
			$Arr_FilterProperties = array();
			$GLB_DEBUGGER->debug_folders($Arr_DebugFolders)->debug_files($Arr_DebugFiles)->debug_classes($Arr_DebugClasses)->debug_functions($Arr_DebugFunctions)->debug_properties($Arr_DebugProperties)
						->filter_folders($Arr_FilterFolders)->filter_files($Arr_FilterFiles)->filter_classes($Arr_FilterClasses)->filter_functions($Arr_FilterFunctions)->filter_properties($Arr_FilterProperties);
		}

		return;
	}

	//User defined behaviour in MW_Foreman->__construct() after request is configured.
	//  Return:				VOID
	public static function post_configure_request()
	{
		//Define website specific constants.
		define ('EBW_PRODUCT_DISCOUNT',				0.2);

		return;
	}

	//User defined behaviour in MW_Foreman->send_response() after response has been sent.
	// * Return:				VOID
	public static function post_send_response()
	{
		global $GLB_DEBUGGER;
		if ($GLB_DEBUGGER)
			$GLB_DEBUGGER->print_debugstack();

		return;
	}

}

?>