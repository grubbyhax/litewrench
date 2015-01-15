<?php
//mw_debugging.php
//Monkeywrench debugging class

class MW_System_Debugger
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//System wide debugging flags.
	public $Hex_DebugFlags		= 0x000000;

	//Convert html special chars flag
	private $Bol_ConvertSpecialChars	= true; //*!* should start as false after testing.

	//Debugging output type.
	public $Int_DebugOutput		= 1;

	//Debugging information holder.
	private $Arr_DebugStack = array();

	//Master debug switch.
	private $Bol_DebugSwitch		= true;

	//Debugging configuration.
	//NB: If nothing set and debugging is turned on every object will be debugged.
	private $Arr_DebugFolders		= array();		//Folders to debug
	private $Arr_DebugFiles			= array();		//Files to debug
	private $Arr_DebugClasses		= array();		//Classes to debug
	private $Arr_DebugFunctions		= array();		//Functions to debug
	private $Arr_DebugProperties	= array();		//Properties to debug
	private $Arr_FilterFolders		= array();		//Folders not to debug
	private $Arr_FilterFiles		= array();		//Files not to debug
	private $Arr_FilterClasses		= array();		//Classes not to debug
	private $Arr_FilterFunctions	= array();		//Functions not to debug
	private $Arr_FilterProperties	= array();		//Properties not to debug



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	public function __construct()
	{
		//Set debugging user-defined flags.
		if (defined('UDF_CONST_INT_FLAG_CONVERTHTMLCHARS') && constant('UDF_CONST_INT_FLAG_CONVERTHTMLCHARS'))
			$this->Bol_ConvertSpecialChars = true;

		//Set debugging properties.
		//*!* Need this here? What is this?
	}



///////////////////////////////////////////////////////////////////////////////
//                  D E B U G G I N G   F U N C T I O N S                    //
///////////////////////////////////////////////////////////////////////////////

	//Sets master debugging switch.
	// - $Bol_SetSwitch:		Master switch setting
	// * Return:				SELF
	public function debug($Bol_SetSwitch=true)
	{
		$this->Bol_DebugSwitch = $Bol_SetSwitch;
		return $this;
	}

	//Sets an array of folders to be debugged or to not be debugged.
	// - $Arr_Folders:			Array of folders to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function debug_folders($Arr_Folders, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Folders))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each files passed.
		if ($Arr_Folders)
		{
			foreach ($Arr_Folders as $Str_Folder)
			{
				//If set to debug and the file is not in the debug set add it.
				if ($Bol_Debug && !in_array($Str_Folder, $this->Arr_DebugFolders))
				{
					$this->Arr_DebugFolders[] = $Str_Folder;
				}
				//Else if set to not debug and the file is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_Folder, $this->Arr_DebugFolders))
				{
					$Int_Key = array_search($Str_Folder, $this->Arr_DebugFolders);
					unset($this->Arr_DebugFolders[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of files to be debugged or to not be debugged.
	// - $Arr_Files:			Array of files to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function debug_files($Arr_Files, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Files))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each files passed.
		if ($Arr_Files)
		{
			foreach ($Arr_Files as $Str_File)
			{
				//If set to debug and the file is not in the debug set add it.
				if ($Bol_Debug && !in_array($Str_File, $this->Arr_DebugFiles))
				{
					$this->Arr_DebugFiles[] = $Str_File;
				}
				//Else if set to not debug and the file is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_File, $this->Arr_DebugFiles))
				{
					$Int_Key = array_search($Str_File, $this->Arr_DebugFiles);
					unset($this->Arr_DebugFiles[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of classes to be debugged or to not be debugged.
	// - $Arr_Classes:			Array of classes to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function debug_classes($Arr_Classes, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Classes))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each class passed.
		if ($Arr_Classes)
		{
			foreach ($Arr_Classes as $Str_Class)
			{
				//If set to debug and the class is not in the debug set add it.
				if ($Bol_Debug && !in_array($Str_Class, $this->Arr_DebugClasses))
				{
					$this->Arr_DebugClasses[] = $Str_Class;
				}
				//Else if set to not debug and the class is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_Class, $this->Arr_DebugClasses))
				{
					$Int_Key = array_search($Str_Class, $this->Arr_DebugClasses);
					unset($this->Arr_DebugClasses[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of functions to be debugged or to not be debugged.
	// - $Arr_Functions:		Array of functions to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function debug_functions($Arr_Functions, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Functions))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each function passed.
		if ($Arr_Functions)
		{
			foreach ($Arr_Functions as $Str_Function)
			{
				//If set to debug and the function is not in the debug set add it.
				if ($Bol_Debug && !in_array($Str_Function, $this->Arr_DebugFunctions))
				{
					$this->Arr_DebugFunctions[] = $Str_Function;
				}
				//Else if set to not debug and the function is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_Function, $this->Arr_DebugFunctions))
				{
					$Int_Key = array_search($Str_Function, $this->Arr_DebugFunctions);
					unset($this->Arr_DebugFunctions[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of properties to be debugged or to not be debugged.
	// - $Arr_Properties:		Array of properties to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function debug_properties($Arr_Properties, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Properties))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each property passed.
		if ($Arr_Properties)
		{
			foreach ($Arr_Properties as $Str_Property)
			{
				//If set to debug and the property is not in the debug set add it.
				if ($Bol_Debug && !in_array($Str_Property, $this->Arr_DebugProperties))
				{
					$this->Arr_DebugProperties[] = $Str_Property;
				}
				//Else if set to not debug and the property is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_Property, $this->Arr_DebugProperties))
				{
					$Int_Key = array_search($Str_Property, $this->Arr_DebugProperties);
					unset($this->Arr_DebugProperties[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of folders to be filtered or to not be filtered from debugging.
	// - $Arr_Folders:			Array of folders to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function filter_folders($Arr_Folders, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Folders))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each folders passed.
		if ($Arr_Folders)
		{
			foreach ($Arr_Folders as $Str_Folder)
			{
				//If set to filter and the folder is not in the filter set add it.
				if ($Bol_Debug && !in_array($Str_Folder, $this->Arr_FilterFolders))
				{
					$this->Arr_FilterFolders[] = $Str_Folder;
				}
				//Else if set to not filter and the folder is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_Folder, $this->Arr_FilterFolders))
				{
					$Int_Key = array_search($Str_Folder, $this->Arr_FilterFolders);
					unset($this->Arr_FilterFolders[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of files to be filtered or to not be filtered from debugging.
	// - $Arr_Files:			Array of files to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function filter_files($Arr_Files, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Files))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each files passed.
		if ($Arr_Files)
		{
			foreach ($Arr_Files as $Str_File)
			{
				//If set to filter and the file is not in the filter set add it.
				if ($Bol_Debug && !in_array($Str_File, $this->Arr_FilterFiles))
				{
					$this->Arr_FilterFiles[] = $Str_File;
				}
				//Else if set to not filter and the file is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_File, $this->Arr_FilterFiles))
				{
					$Int_Key = array_search($Str_File, $this->Arr_FilterFiles);
					unset($this->Arr_FilterFiles[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of classes to be filtered or to not be filtered from debugging.
	// - $Arr_Classes:			Array of classes to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function filter_classes($Arr_Classes, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Classes))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each class passed.
		if ($Arr_Classes)
		{
			foreach ($Arr_Classes as $Str_Class)
			{
				//If set to filter and the class is not in the filter set add it.
				if ($Bol_Debug && !in_array($Str_Class, $this->Arr_FilterClasses))
				{
					$this->Arr_FilterClasses[] = $Str_Class;
				}
				//Else if set to not filter and the class is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_Class, $this->Arr_FilterClasses))
				{
					$Int_Key = array_search($Str_Class, $this->Arr_FilterClasses);
					unset($this->Arr_FilterClasses[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of functions to be filtered or to not be filtered from debugging.
	// - $Arr_Functions:		Array of functions to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function filter_functions($Arr_Functions, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Functions))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each function passed.
		if ($Arr_Functions)
		{
			foreach ($Arr_Functions as $Str_Function)
			{
				//If set to filter and the function is not in the filter set add it.
				if ($Bol_Debug && !in_array($Str_Function, $this->Arr_FilterFunctions))
				{
					$this->Arr_FilterFunctions[] = $Str_Function;
				}
				//Else if set to not filter and the function is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_Function, $this->Arr_FilterFunctions))
				{
					$Int_Key = array_search($Str_Function, $this->Arr_FilterFunctions);
					unset($this->Arr_FilterFunctions[$Int_Key]);
				}
			}
		}

		return $this;
	}

	//Sets an array of properties to be filtered or to not be filtered from debugging.
	// - $Arr_Properties:		Array of properties to debug
	// - $Bol_Debug:			Set to debug toggle, default = true(set to debug)
	// * Return:				SELF
	public function filter_properties($Arr_Properties, $Bol_Debug=true)
	{
		//Handle exception on bad parameter.
		if (!is_array($Arr_Properties))
		{
			global $CMD;
			$CMD->handle_exception('First parameter is not an array', 'MW:101');
		}

		//Loop through each property passed.
		if ($Arr_Properties)
		{
			foreach ($Arr_Properties as $Str_Property)
			{
				//If set to filter and the property is not in the filter set add it.
				if ($Bol_Debug && !in_array($Str_Property, $this->Arr_FilterProperties))
				{
					$this->Arr_FilterProperties[] = $Str_Property;
				}
				//Else if set to not filter and the property is in the set remove it.
				elseif (!$Bol_Debug && in_array($Str_Property, $this->Arr_FilterProperties))
				{
					$Int_Key = array_search($Str_Property, $this->Arr_FilterProperties);
					unset($this->Arr_FilterProperties[$Int_Key]);
				}
			}
		}

		return $this;
	}

///////////////////////////////////////////////////////////////////////////////
//                    U T I L I T Y   F U N C T I O N S                      //
///////////////////////////////////////////////////////////////////////////////

	//Determines whether stack is debuggable or not
	// - $Arr_Backtrace:		Backtrace to test if it is debuggable
	// - Arr_Backtrace:			Position in backtrace to test if allowed to debug
	// * Return:				True if $Arr_Backtrace[$Int_Position] is debuggable, otherwise false
	public function is_debuggable_stack($Arr_Backtrace, $Int_Position)
	{
		$Bol_Debuggable = false;

		//Folders and files.
		if ($Arr_Backtrace[$Int_Position]['file'])
		{
			//No folders set.
			if (!$this->Arr_DebugFolders)
			{
				$Bol_Debuggable = true;
			}
			else
			{
				foreach ($this->Arr_DebugFolders as $Str_DebugPath)
				{
					if (strrpos(str_replace('\\', '/', $Arr_Backtrace[$Int_Position]['file']), $Str_DebugPath) !== false)
					{
						$Bol_Debuggable = true;
						break;
					}
				}
			}

			//Filtered folders.
			if ($this->Arr_FilterFolders)
			{
				foreach ($this->Arr_FilterFolders as $Str_FilterPath)
				{
					if (strpos(str_replace('\\', '/', $Arr_Backtrace[$Int_Position]['file']), $Str_FilterPath) !== false)
					{
						$Bol_Debuggable = false;
						break;
					}
				}
			}

			//If false exit.
			if (!$Bol_Debuggable)
				return $Bol_Debuggable;


			//No files set.
			if (!$this->Arr_DebugFiles)
			{
				$Bol_Debuggable = true;
			}
			//Search files set.
			else
			{
				foreach ($this->Arr_DebugFiles as $Str_DebugPath)
				{
					if (strrpos(str_replace('\\', '/', $Arr_Backtrace[$Int_Position]['file']), $Str_DebugPath.'.php') !== false)
					{
						$Bol_Debuggable = true;
						break;
					}
				}
			}

			//Filtered files.
			if ($this->Arr_FilterFiles)
			{
				foreach ($this->Arr_FilterFiles as $Str_FilterPath)
				{
					if (strpos(str_replace('\\', '/', $Arr_Backtrace[$Int_Position]['file']), $Str_FilterPath.'.php') !== false)
					{
						$Bol_Debuggable = false;
						break;
					}
				}
			}
		}

		//If false exit.
		if (!$Bol_Debuggable)
			return $Bol_Debuggable;

		//Classes.
		if (isset($Arr_Backtrace[$Int_Position + 1]['class']))
		{
			//Search classes set.
			if (($this->Arr_DebugClasses) && (!in_array($Arr_Backtrace[$Int_Position + 1]['class'], $this->Arr_DebugClasses)))
				$Bol_Debuggable = false;

			//Filtered classes
			if (in_array($Arr_Backtrace[$Int_Position + 1]['class'], $this->Arr_FilterClasses))
				$Bol_Debuggable = false;
		}

		//If false exit.
		if (!$Bol_Debuggable)
			return $Bol_Debuggable;

		//Functions.
		if (isset($Arr_Backtrace[$Int_Position + 1]['function']))
		{
			//Search functions set.
			if (($this->Arr_DebugFunctions) && (!in_array($Arr_Backtrace[$Int_Position + 1]['function'], $this->Arr_DebugFunctions)))
				$Bol_Debuggable = false;

			//Filtered functions.
			if (in_array($Arr_Backtrace[$Int_Position + 1]['function'],  $this->Arr_FilterFunctions))
				$Bol_Debuggable = false;
		}

		//If false exit.
		if (!$Bol_Debuggable)
			return $Bol_Debuggable;

		//Variables.
		if ($Arr_Backtrace[$Int_Position]['args'][0])
		{
			//Search functions set.
			if (($this->Arr_DebugProperties) && (!in_array($Arr_Backtrace[$Int_Position]['args'][0], $this->Arr_DebugProperties)))
				$Bol_Debuggable = false;

			//Filtered functions.
			if (in_array($Arr_Backtrace[$Int_Position]['args'][0],  $this->Arr_FilterProperties))
				$Bol_Debuggable = false;
		}

		return $Bol_Debuggable;
	}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

	//Gets formatted information of the global forman object.
	//*!*This is pretty much a summation of the object configuration and registration for the request(at the end)
	public function get_status($Var_Data)
	{
		//Start output buffering.

		//Get var_dump.

		//Format information.

		//End output buffering.

		echo '<div style="text-align:left"><xmp>';
		var_dump($Var_Data);
		echo '</xmp></div>';
	}

	//Adds debugging backtrace to debug stack.
	// - $Str_CLassName:		Name of base class calling the debugger, should be 'MW_Base'.
	public function set_backtrace($Str_ClassName)
	{
		//if the master switch is turned off do nothing.
		if (!$this->Bol_DebugSwitch)
			return;

		//Get debugging location in backtrace.
		$Arr_Backtrace = debug_backtrace();
		$i = 1; //NB: Only MW_Base calls debugger.

		//If allowed add debug backtrace to stack.
		if ($this->is_debuggable_stack($Arr_Backtrace, $i))
		{
			$Str_Trace = '';
			for ($j = count($Arr_Backtrace) - 1; $j > 1; $j--)
			{
				if (strpos(str_replace('\\', '/', $Arr_Backtrace[$j]['file']), MW_CONST_STR_DIR_INSTALL) !== false)
					$Str_File = substr($Arr_Backtrace[$j]['file'], strlen(MW_CONST_STR_DIR_INSTALL) - 1);
				else
					$Str_File = substr($Arr_Backtrace[$j]['file'], strlen(MW_CONST_STR_DIR_DOMAIN));

				$Str_Trace .= $Arr_Backtrace[$j]['line'].' - '.$Str_File."\n";
			}

			$this->Arr_DebugStack[] = array('time' => microtime(true) - MW_CONST_FLT_MICRO_EXECUTE,
									'file' => $Arr_Backtrace[$i]['file'],
									'line' => $Arr_Backtrace[$i]['line'],
									'class' => isset($Arr_Backtrace[$i + 1]['class'])? $Arr_Backtrace[$i + 1]['class']: '',
									'function' => isset($Arr_Backtrace[$i + 1]['function'])? $Arr_Backtrace[$i + 1]['function']: '',
									'type' => $Arr_Backtrace[$i]['type'],
									'name' => $Arr_Backtrace[$i]['args'][0],
									'value' => $Arr_Backtrace[$i]['args'][1],
									'trace' => $Str_Trace);
		}

		return;
	}

	//Formatts proerty value for feedback display
	// - $Str_PropertyValue:		Property value to display
	// - $Bol_Escape:				Flag to escape special characters
	// * Return:					Property value formatted for display
	public function display_property_value($Str_PropertyValue, $Bol_Escape=false)
	{
		$Str_DisplayValue = $Str_PropertyValue;

		if (is_array($Str_DisplayValue) || is_object($Str_DisplayValue))
		{
			ob_start();
			var_dump($Str_DisplayValue);
			$Str_DisplayValue = ob_get_clean();
		}

		if ($Bol_Escape && is_string($Str_DisplayValue))
			$Str_DisplayValue = htmlspecialchars($Str_DisplayValue);

		return $Str_DisplayValue;
	}

	//Writes debuggin to log file.
	// * Return:				VOID
	public function log_debugstack()
	{
		//Format debugging feed back.
		$Str_DebugFeedback = '';
		foreach ($this->Arr_DebugStack as $Int_StackPosition => $Arr_DebugDetails)
		{
			$Str_DebugFeedback .= '#'.$Int_StackPosition
								.' '.$Arr_DebugDetails['time']
								.' '.$Arr_DebugDetails['line']
								.' '.$Arr_DebugDetails['class']
								.' '.$Arr_DebugDetails['type']
								.' '.$Arr_DebugDetails['function']
								.' '.$Arr_DebugDetails['name']
								.' = '.$this->display_property_value($Arr_DebugDetails['value'])
								.' '.$Arr_DebugDetails['file']
								."\r\n";
		}

		//Write debugging feedback to file.
		$Str_LogFileName = MW_CONST_STR_FILE_DEBUG_USER;

		//Open debug log file.
		if (!is_writable($Str_LogFileName))
		{
			//Deal with error log issues.
			continue;
		}

		$Res_LogFile = null;
		if (!$Res_LogFile = fopen($Str_LogFileName, 'a'))
		{
			//Handle error.
			continue;
		}

		//Write exceptions to file.
		if (fwrite($Res_LogFile, $Str_DebugFeedback) === false)
		{
			//Handle error.
			continue;
		}

		fclose($Res_LogFile);
		return;
	}

	public function stack_to_html()
	{
		$Str_DebugFeedback = '
<style  type="text/css">
table pre{margin:0;padding:0;border:none;background-color:#f8f8ff;}
table#debug_output{width:100%;margin-top:10px;border-spacing:1px;background-color:#f8f8ff;font-family:verdana, monospace, "Courier New";}
table#debug_output, #debug_output th, #debug_output td{border:1px solid #ccc;}
#debug_output th, #debug_output td{padding:2px;font-size:10px;vertical-align:top;}
#debug_output pre{font-family:verdana, monospace, "Courier New";}
</style>
<table id="debug_output">
	<tr>
		<th>#</th><th>Time</th><th>File</th><th>Line</th><th>Class</th><th>Op</th><th>Function</th><th>Variable</th><th>Value</th><th>Trace</th>
	</tr>';

		foreach ($this->Arr_DebugStack as $Int_StackPosition => $Arr_DebugDetails)
		{
			//Get file being debugged.
			$Str_DebugFile = substr($Arr_DebugDetails['file'], strlen(MW_CONST_STR_DIR_INSTALL) - 1);

			$Str_DebugFeedback .= '
	<tr>
		<th>'.$Int_StackPosition.'</th>
		<td>'.round($Arr_DebugDetails['time'], 5).'</td>
		<td>'.$Str_DebugFile.'</td>
		<td>'.$Arr_DebugDetails['line'].'</td>
		<td>'.$Arr_DebugDetails['class'].'</td>
		<td>'.$Arr_DebugDetails['type'].'</td>
		<td>'.$Arr_DebugDetails['function'].'</td>
		<td>'.$Arr_DebugDetails['name'].'</td>
		<td><pre>'.$this->display_property_value($Arr_DebugDetails['value'], true).'</pre></td>
		<td><pre>'.$Arr_DebugDetails['trace'].'</pre></td>
	</tr>';
		}

		$Str_DebugFeedback .= '
</table>';
		
		return $Str_DebugFeedback;
	}

	//Converts debugging stack to an html output.
	// * Return:				VOID
	public function print_debugstack()
	{
		//If there are output sets in the debug stack print them.
		if ($this->Arr_DebugStack)
		{
			$Str_DebugFeedback = $this->stack_to_html();
			print $Str_DebugFeedback;
		}

		return;
	}

}

?>