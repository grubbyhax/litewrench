<?php
//ud_event.php
//User-defined event class file.
//Design by David Thomson at Hundredth Codemonkey.


class MW_Router
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	private $Arr_Routes		= array();
	private $Str_Route		= '';



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
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

	public function build()
	{
		//Loop through each route o find the first matching entry.
		$Str_LongestIndex = '';
		$Int_LongestIndex = 0;

		foreach ($this->Arr_Routes as $Reg_Route => $Arr_Route)
		{
			if (preg_match('@\A'.$Reg_Route.'+@', $_SERVER['REQUEST_URI'], $Arr_Matches))
			{
				//Find the longest matching expression.
				//*!*This piece of logic is flawed and needs to be modified to match the
				//longest string with the shortest regex, not sure if this can actually be done
				//but will have to remove the * before the length test.
				//The other way is to run a split and count done th 'folders' on the path
				//to the page on the matched regex.
				if (strlen($Arr_Matches[0]) > $Int_LongestIndex)
				{
					$Str_LongestIndex = $Reg_Route;
					$Int_LongestIndex = strlen($Arr_Matches[0]);
				}
			}
		}

		if ($Str_LongestIndex)
		{
			global $CMD;
			$Obj_Module = $CMD->module($this->Arr_Routes[$Str_LongestIndex]['module']);

			//Get the combined variables. - big chunky piece of logic here.
			$Arr_ModuleVars = array();
			$Arr_ModuleVars = explode('&', $this->Arr_Routes[$Str_LongestIndex]['values']);
			$Arr_PathVars = $CMD->config('path_vars');
			$Arr_CombineVars = $this->Arr_Routes[$Str_LongestIndex]['combine'];

			$Int_ModuleVars = count($Arr_ModuleVars);

			$Int_ReplaceStart = 0;
			$Int_ReplaceFininsh = $Int_ModuleVars;
			if ($Arr_CombineVars[0] > 0)
			{
				$Int_ReplaceStart = $Arr_CombineVars[0];
			}
			elseif ($Arr_CombineVars[0] < 0)
			{
				$Int_ReplaceFininsh = $Int_ModuleVars + $Arr_CombineVars[0];
			}

			for ($i = 0; $i < $Int_ModuleVars; $i++)
			{
				$Arr_ModuleVariable = explode('=', $Arr_ModuleVars[$i]);
				$Arr_ModifiedModuleVars[$Arr_ModuleVariable[0]] = $Arr_ModuleVariable[1];
				$Arr_DefaultModuleVars[$Arr_ModuleVariable[0]] = $Arr_ModuleVariable[1];

				//If the variable is within the replacment range then swap the modified value in.
				if (($Arr_CombineVars[0] !== false) && ($i >= $Int_ReplaceStart) && ($i < $Int_ReplaceFininsh))
				{
					//If there is a path variable and a combine directive add path variable to module request.
					//*!*This test below is fucked because of the age old problem of allowing for spaces in the tags. Fix  regex l8er.
					if (isset($Arr_PathVars[$i - $Int_ReplaceStart]) && isset($Arr_CombineVars[1]) && $Arr_CombineVars[1] !== '')
					{
						//If there is a zero combine directive, or a macthing positive or negative directive swap in path variable.
						if ((!$Arr_CombineVars[1])
						|| (($Arr_CombineVars[1] > 0) && ($i - $Int_ReplaceStart + 1 > $Arr_CombineVars[1]))
						//*!*Needs to be redone/
						|| (($Arr_CombineVars[1] < 0) && ($i < $Int_ModuleVars + $Arr_CombineVars[1] + 1)))
						{
							//*!*I'm looking for this string key when I should be looking for the position index, must change.
							$Arr_ModifiedModuleVars[$Arr_ModuleVariable[0]] = $Arr_PathVars[$i - $Int_ReplaceStart];
					}
					}
					//If there is a matching get variable and a combine directive add get variable to module request.
					//*!*Needs to be redone
					if (isset($_GET[$Arr_ModuleVariable[0]]) && isset($Arr_CombineVars[2])  && $Arr_CombineVars[2] !== '')
					{
						//If there is a zero combine directive, or a macthing positive or negative directive swap in get variable.
						if ((!$Arr_CombineVars[2])
						|| (($Arr_CombineVars[2] > 0) && ($i > $Arr_CombineVars[2] - 1))
						|| (($Arr_CombineVars[2] < 0) && ($i < $Int_ModuleVars + $Arr_CombineVars[2] + 1)))
							$Arr_ModifiedModuleVars[$Arr_ModuleVariable[0]] = $_GET[$Arr_ModuleVariable[0]];
					}
				}
			}

			//Recombine modified module variables.
			//*!*I'm not sure where this goes?
			foreach ($Arr_ModifiedModuleVars as $Str_Key => $Str_Value)
			{
				$Arr_RecombinedModuleVars[] = $Str_Key.'='.$Str_Value;
			}

			$Str_RecombinedModuleVars = '?'.implode('&', $Arr_RecombinedModuleVars);
			$Obj_Module->configure_request($Arr_DefaultModuleVars, $Arr_ModifiedModuleVars);

			return $Obj_Module;
		}
	}

	public function route($Str_UriPath)
	{
		//Set the routing base uri and default combine values.
		$this->Str_Route = $Str_UriPath;
		$this->Arr_Routes[$Str_UriPath]['combine'] = array(false, false, false);

		return $this;
	}

	public function module($Str_Module, $Str_Values=false)
	{
		//Set module to current declared path with values.
		$this->Arr_Routes[$this->Str_Route]['module'] = $Str_Module;
		$this->Arr_Routes[$this->Str_Route]['values'] = $Str_Values;

		return $this;
	}

	public function combine($Int_VarPosition=false, $Int_UriPosition=false, $Int_GetPosition=false)
	{
		//Set combine directives to current declared path.
		$this->Arr_Routes[$this->Str_Route]['combine'] = array($Int_VarPosition, $Int_UriPosition, $Int_GetPosition);

		return $this;
	}

	//User defined system routing for calling the primary module to fulfill request logic.
	//  Return:				VOID
	//need to trim out the traling slash on the uri so shit doesn't break.
	public function routes()
	{
		$this->route('/')->module('app', 'control=page&action=default&state=default&option=default');
		$this->route('/api')->module('app', 'control=api&action=default&state=default&option=default')->combine(1);
	}

}

?>