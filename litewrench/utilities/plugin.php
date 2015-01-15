<?php
//plugin.php
//Plugin class file.
//Design by David Thomson at Hundredth Codemonkey.

class MW_Utility_Plugin extends MW_System_Base
{
///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties	= array(
		'Str_File'				=> '',			//Name of the plugin file
		'Arr_Vars'				=> array());	//Key/value array of variables

///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////


	public function __construct(){}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

	//Executes the plugin script with variables set locally
	// * Return:				The result of the plugin script.
	public function build()
	{
		//include file
		$Str_Execute = '';

		//Put local vars into the local namespace.
		if ($Arr_Vars = $this->Arr_Vars)
		{
			extract($Arr_Vars);
		}

		//Get execution result.
		ob_start();
		include(path_request(MW_CONST_STR_DIR_INSTALL.'plugins/'.$this->Str_File.'.php'));
		$Str_Execute = ob_get_contents();
		ob_end_clean();
		
		//**!*Need to remove the vars from the local namespace.
		//test the scoping on these vars, they might actually remain within this function's scope.

		return $Str_Execute;
	}

	//Set the plugin execute file.
	// - $Str_File:				File for the plugin to execute with the build call
	// * Return:					SELF
	public function plan($Str_File)
	{
		$Arr_NewProperties = $this->Arr_Properties;
		$Arr_NewProperties['Str_File'] = $Str_File;
		$this->Arr_Properties = $Arr_NewProperties;

		return $this;
	}

	//Binds the variables for use in the plugin file.
	// - $Arr_Variables:			Variables as key/value array to be used in the plugin script
	// * Return:					SELF
	public function bind($Arr_Variables)
	{
		if ($Arr_Variables !== false)
		{
			$Arr_NewProperties = $this->Arr_Properties;
			$Arr_NewProperties['Arr_Vars'] = $Arr_Variables;
			$this->Arr_Properties = $Arr_NewProperties;
		}

		return $this;
	}

///////////////////////////////////////////////////////////////////////////////
//                    U T I L I T Y   F U N C T I O N S                      //
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
//                      B U I L D   F U N C T I O N S                        //
///////////////////////////////////////////////////////////////////////////////



}

?>