<?php
//mw_exception.php
//Exception class file.
//Design by David Thomson at Hundredth Codemonkey.

class MW_System_Exception extends MW_System_Base
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	private $Arr_ExceptionStack			= array();	//Exceptions handled stack.
/*
	//Don't know if I need these, I can separate types by error code in write to log.
	private $Str_DefaultSystemMessages	= '';
	private $Str_CustomLevelMessages	= '';
	private $Str_UserDefinedMessages	= '';
*/

												// *** Token Regex *** //
	private $Reg_ExceptionGlue			= '';	//Exception token glue string
	private $Reg_ExceptionCode			= '';	//Exception code integer
	private $Reg_ExceptionModule		= '';	//Exception module string

///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties	= array(
												// *** Sensitive stack values *** //
		'Arr_SensitiveCodes'		=> array(),	//Codes excluded from default logging.
		'Arr_SensitiveFiles'		=> array(),	//Files excluded from default logging.
		'Arr_SensitiveClasses' 		=> array(),	//Classes excluded from default logging.
		'Arr_SensitiveFunctions'	=> array());//Functions excluded from default logging.


///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	public function __construct()
	{
		//Object regex
		$this->Reg_ExceptionGlue	= ':';
		$this->Reg_ExceptionCode	= '[0-9]+';
		$this->Reg_ExceptionModule	= '[\w]+';

		//Sensitive code.
		//*!*This should be done by the foreman configuration routine and udf.
		//$this->file_is_sensitive(MW_CONST_STR_DIR_LIBRARY);

		 return;
	}

	//*!*Not sure about these routines here, I've put them somewhere else
	public function __destruct()
	{
		//Organise and format exception messages.

		//If no error log files exist create them.

		//Write default system exceptions to file.

		//Write custom level messages to file.

		//Parse user defined messages to user defined function.
	}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////
/*
	//Evaluates whether or not a backtrace is system sensitive or not depending upon the error code supplied
	// - $Arr_TraceInfo:			Exception backtrace information array
	// - $Int_ExceptionCode:		Exception code supplied when thrown
	// * Return:					True if backtrace is system sensitive, otherwise false
	public function trace_is_sensitive($Arr_TraceInfo, $Int_ExceptionCode)
	{
		//*!*Must make file, class and function camparisons according to $Int_ExceptionCode
		if ((strpos(str_replace('\\', '/', $Arr_TraceInfo['file']), MW_CONST_STR_DIR_LIBRARY) !== false)
		&& (strpos(str_replace('\\', '/', $Arr_TraceInfo['function']), 'handle_exception') !== false))
		{
			return true;
		}
		else if (strpos(str_replace('\\', '/', $Arr_TraceInfo['file']), MW_CONST_STR_DIR_PARCELS) !== false)
		{
			return true;
		}

		return false;
	}
*/
	//Sets exception code as too sensitive to include in error messages.
	// - $Int_Code:				Exception code to exclude from error stack trace
	// * Return:				VOID
	public function code_is_sensitive($Int_Code)
	{
		if (!in_array($Int_Code, $this->Arr_SensitiveCodes))
		{
			$Arr_Codes = $this->Arr_SensitiveCodes;
			$Arr_Codes[] = $Int_Code;
			$this->Arr_SensitiveCodes = $Arr_Codes;
		}

		return;
	}

	//Sets file path as too sensitive to include in error messages.
	// - $Str_File:				File or path to exclude from error stack trace
	// * Return:				VOID
	public function file_is_sensitive($Str_File)
	{
		if (!in_array($Str_File, $this->Arr_SensitiveFiles))
		{
			$Arr_Files = $this->Arr_SensitiveFiles;
			$Arr_Files[] = str_replace('/', '\\', $Str_File);
			$this->Arr_SensitiveFiles = $Arr_Files;
		}

		return;
	}

	//Sets class as too sensitive to include in error messages.
	// - $Str_Class:			Name of class to exclude from error stack trace
	// * Return:				VOID
	public function class_is_sensitive($Str_Class)
	{
		if (!in_array($Str_Class, $this->Arr_SensitiveClasses))
		{
			$Arr_Classes = $this->Arr_SensitiveClasses;
			$Arr_Classes[] = $Str_Class;
			$this->Arr_SensitiveClasses = $Arr_Classes;
		}

		return;
	}

	//Sets function as too sensitive to include in error messages.
	// - $Str_Function:			Name of class to exclude from error stack trace
	// * Return:				VOID
	public function function_is_sensitive($Str_Function)
	{
		if (!in_array($Str_Function, $this->Arr_SensitiveFunctions))
		{
			$Arr_Function = $this->Arr_SensitiveFunctions;
			$Arr_Function[] = $Str_Function;
			$this->Arr_SensitiveFunctions = $Arr_Function;
		}

		return;
	}

	//Checks that error code $Int_Code is feedback sensitive
	// - $Int_Code:				Error code number to check for sensitibity
	// * Return:				True if $Int_Code is sensitive, otherwise false
	public function is_sensitive_code($Int_Code)
	{
		if ($Int_Code && isset($this->Arr_SensitiveCodes)
		&& in_array($Int_Code, $this->Arr_SensitiveCodes))
			return true;

		return false;
	}

	//Checks that file name $Str_File is feedback sensitive
	// - $Str_File:				Name of function to check for sensitibity
	// * Return:				True if $Str_File is sensitive, otherwise false
	public function is_sensitive_file($Str_File)
	{
		if ($Str_File && isset($this->Arr_SensitiveFiles))
		{
			foreach ($this->Arr_SensitiveFiles as $Str_Path)
			{
				if (strpos($Str_File, $Str_Path) !== false)
					return true;
			}
		}

		return false;
	}

	//Checks that class name $Str_Class is feedback sensitive
	// - $Str_Class:			Name of class to check for sensitibity
	// * Return:				True if $Str_Class is sensitive, otherwise false
	public function is_sensitive_class($Str_Class)
	{
		if ($Str_Class && isset($this->Arr_SensitiveClasses)
		&& in_array($Str_Class, $this->Arr_SensitiveClasses))
			return true;

		return false;
	}

	//Checks that function name $Str_Function is feedback sensitive
	// - $Str_Function:			Name of function to check for sensitibity
	// * Return:				True if $Str_Class is sensitive, otherwise false
	public function is_sensitive_function($Str_Function)
	{
		if ($Str_Function && isset($this->Arr_SensitiveFunctions)
		&& in_array($Str_Function, $this->Arr_SensitiveFunctions))
			return true;

		return false;
	}

	//Internal recovery for system specific exceptions.
	// - $Int_Code:				Exception code integer
	// - $Int_Type:				Exception type MW_CONST_INT_EXCEPTION_TYPE_*
	//*!*This is wrapperisation, fuck it off and stick this shit into the routine, dude!
	//Probaly do the same thing with the external recovery.
	private function internal_recovery($Int_Code)
	{
		$Var_ExceptionRecovery = null;

		//User defined behaviour.
		$Var_ExceptionRecovery = MW_Events::handle_exception($Int_Code);

		return $Var_ExceptionRecovery;
	}

	//External recovery for extensions exceptions.
	// - $Str_Module:			Module name(extension directory)
	// - $Int_Code:				Exception code integer
	// - $Int_Type:				Exception type MW_CONST_INT_EXCEPTION_TYPE_*
	//*!* This needs to be reorganised to a more user friendly structure, as per the one file/class rule for modules to plugin to system.
	private function external_recovery($Str_Module, $Int_Code)
	{
		$Var_ExceptionRecovery = '';

		//Execute module exception routine.
		//*!* We don't need this module exception file!...We jsut need to get objedt and handle_exception()
		//*!* Do I need to check registry to see if module is turned on or off, or, do this in the builder?
		//*!*Need to look for module version
		//So basically we're fucking off this umodule specific events and putting them straight into the module file.
		//So external recovery is going to go back to the module file and do a method call to recover a value.
		$Str_ModuleExceptionFile = MW_CONST_STR_DIR_EXTENSION.strtolower($Str_Module).'exception.php';
		if (file_exists($Str_ModuleExceptionFile))
		{
			require_once($Str_ModuleExceptionFile);
			$Str_ModulePrefix = constant(strtoupper($Str_Module).'_CONST_STR_CLASS_PREFIX');
			$Str_ModulePlugin = constant(strtoupper($Str_Module).'_CONST_STR_CLASS_PLUGIN');
			$Str_PluginClass = $Str_ModulePrefix.'_'.$Str_ModulePlugin;
			$Obj_Module = new $Str_PluginClass();
			$Var_ExceptionRecovery = $Obj_Module->handle_exception($Int_Code);
		}
		//Or, handle exception.
		else
		{
			$this->handle_exception('Extension module file not found',
			MW_CONST_TOK_EXCEPTION_INTERNAL.$this->Reg_ExceptionGlue.MW_CONST_INT_EXCEPTION_DEFAULT,
			MW_CONST_INT_EXCEPTION_TYPE_USER);
		}

		return $Var_ExceptionRecovery;
	}

	//Throws and catches a system excpetion, logging relevant information.
	// - $Str_Message:			Exception message string
	// - $Str_Token:			Exception token string
	// - $Int_Type:				Exception type MW_CONST_INT_EXCEPTION_TYPE_*(as str or int)
	// * Return:				Variable to handle system exception
	public function handle_exception($Str_Message, $Str_Token, $Int_Type)
	{
		//Exception handling parameters.
		$Str_ExceptMessage = $Str_Message;
		$Str_ExceptToken = $Str_Token;
		$Str_ExceptModule = MW_CONST_TOK_EXCEPTION_INTERNAL;
		$Var_Recovery = null;

		try
		{
			//Validate $Str_Message
			if (!is_string($Str_Message))
			{
				$Str_ExceptMessage = 'Exception error - Message not a string.';
			}
			else if ($Str_Message == '')
			{
				$Str_ExceptMessage = 'Exception error  - Message is empty.';
			}
			else
			{
				$Str_ExceptMessage = '"'.$Str_ExceptMessage.'"';
			}

			//Validate exception token string.
			if (!is_string($Str_Token))
			{
				$Str_ExceptMessage = $this->str_rsp($Str_ExceptMessage).'Exception error - Token not a string.';
				$Str_ExceptToken = MW_CONST_TOK_EXCEPTION_INTERNAL.$this->Reg_ExceptionGlue.MW_CONST_INT_EXCEPTION_DEFAULT;
			}
			else if ($Str_Token == '')
			{
				$Str_ExceptMessage = $this->str_rsp($Str_ExceptMessage).'Exception error - Token is empty.';
				$Str_ExceptToken = MW_CONST_TOK_EXCEPTION_INTERNAL.$this->Reg_ExceptionGlue.MW_CONST_INT_EXCEPTION_DEFAULT;
			}
			else if (strpos($Str_Token, $this->Reg_ExceptionGlue) == false)
			{
				$Str_ExceptMessage = $this->str_rsp($Str_ExceptMessage).'Exception error - No glue in token('.$Str_Token.').';
				$Str_ExceptToken = MW_CONST_TOK_EXCEPTION_INTERNAL.$this->Reg_ExceptionGlue.MW_CONST_INT_EXCEPTION_DEFAULT;
			}
			else if (!preg_match('/'.$this->Reg_ExceptionGlue.$this->Reg_ExceptionCode.'/', $Str_Token))
			{
				$Str_ExceptMessage = $this->str_rsp($Str_ExceptMessage).'Exception error - No code in token('.$Str_Token.').';
				$Str_ExceptToken = MW_CONST_TOK_EXCEPTION_INTERNAL.$this->Reg_ExceptionGlue.MW_CONST_INT_EXCEPTION_DEFAULT;
			}
			else if (!preg_match('/'.$this->Reg_ExceptionModule.$this->Reg_ExceptionGlue.'/', $Str_Token))
			{
				$Str_ExceptMessage = $this->str_rsp($Str_ExceptMessage).'Exception error - No module in token('.$Str_Token.').';
				$Str_ExceptToken = MW_CONST_TOK_EXCEPTION_INTERNAL.$this->Reg_ExceptionGlue.MW_CONST_INT_EXCEPTION_DEFAULT;
			}
			
			$Arr_Code = explode(':', $Str_ExceptToken);
			if (count($Arr_Code) > 2)
			{
				$Str_ExceptMessage = $this->str_rsp($Str_ExceptMessage).'Exception error - Too many separators in token('.$Str_ExceptToken.').';
				$Arr_Code[0] = MW_CONST_TOK_EXCEPTION_INTERNAL;
				$Arr_Code[1] = MW_CONST_INT_EXCEPTION_DEFAULT;
			}

			$Str_ExceptModule = $Arr_Code[0];
			$Int_ExceptCode = $Arr_Code[1];

			//Throw exception.
			throw new Exception($Str_ExceptMessage, $Int_ExceptCode);
		}
		catch (Exception $e)
		{
			//Get exception recovery value.
			if ($Str_ExceptModule != MW_CONST_TOK_EXCEPTION_INTERNAL)
				$Var_Recovery = $this->external_recovery($Str_ExceptModule, $Int_ExceptCode);
			else
				$Var_Recovery = $this->internal_recovery($Int_ExceptCode);

			//Build exception stack trace.
			if (!$this->is_sensitive_code($Int_ExceptCode))
			{
				$Arr_StackTrace = array();
				foreach ($e->getTrace() as $Int_TraceLevel => $Arr_TraceInfo)
				{
					$Arr_TraceDetails = array();
					$Arr_TraceDetails['stack'] = $Int_TraceLevel;
					$Arr_TraceDetails['line'] = isset($Arr_TraceInfo['line'])? $Arr_TraceInfo['line']: '';
					$Arr_TraceDetails['class'] = isset($Arr_TraceInfo['class'])? $Arr_TraceInfo['class']: '';
					$Arr_TraceDetails['function'] = isset($Arr_TraceInfo['function'])? $Arr_TraceInfo['function']: '';
					$Arr_TraceDetails['file'] = isset($Arr_TraceInfo['file'])? $Arr_TraceInfo['file']: '';

					if (($this->is_sensitive_file($Arr_TraceDetails['file']))
					|| ($this->is_sensitive_class($Arr_TraceDetails['class']))
					|| ($this->is_sensitive_function($Arr_TraceDetails['function'])))
					{
						$Arr_TraceDetails['line'] = '-';
						$Arr_TraceDetails['class'] = '';
						$Arr_TraceDetails['function'] = '';
						$Arr_TraceDetails['file'] = '';
					}

					$Arr_StackTrace[] = $Arr_TraceDetails;
				}

				$this->Arr_ExceptionStack[$Int_Type][] = array('type' => $Int_Type,
															'time' => MW_CONST_FLT_MICRO_EXECUTE,
															'code' => $Int_ExceptCode,
															'message' => $Str_ExceptMessage,
															'trace' => $Arr_StackTrace);

            }
		}

		return $Var_Recovery;
	}

	//Writes esceptions to error log.
	// - $Str_ExceptionType		Type of exception being logged
	// * Return:				VOID
	public function log_exceptions()
	{
		for ($i = 1; $i < 4; $i++)
		{
			//If there are no exceptions of type continue.
			if (!isset($this->Arr_ExceptionStack[$i]))
				continue;
			//Get exception log file.
			$Str_LogFileName = '';
			switch ($i)
			{
				case MW_CONST_INT_EXCEPTION_TYPE_CONF: $Str_LogFileName = MW_CONST_STR_FILE_EXCEPT_CONF; break;
				case MW_CONST_INT_EXCEPTION_TYPE_DATA: $Str_LogFileName = MW_CONST_STR_FILE_EXCEPT_DATA; break;
				default: $Str_LogFileName = MW_CONST_STR_FILE_EXCEPT_USER;
			}

			//Open exception log file.
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

			//Format exception messages for feedback.
			$Str_ErrorMessage = $this->format_excpetions($i);

			//Write exceptions to file.
			if (fwrite($Res_LogFile, $Str_ErrorMessage) === false)
			{
				//Handle error.
				continue;
			}

			fclose($Res_LogFile);
		}
	}



///////////////////////////////////////////////////////////////////////////////
//         O U T P U T   F O R M A T T I N G   F U N C T I O N S             //
///////////////////////////////////////////////////////////////////////////////

/* NB: These are temporary functions which will be handled by a builder object later.
 * The builder object may use build plans defined within this class or external plans
 */

	//Formats and exception type stack for output.
	// - $Int_Type:				Type of exception, MW_CONST_INT_EXCEPTION_TYPE_*
	// * Return:				Formatted eception output string
	public function format_excpetions($Int_Type)
	{
		$Str_ExceptionOutput = '';

		//Format exception messages for feerdback.
		foreach ($this->Arr_ExceptionStack[$Int_Type] as $Arr_StackDetail)
		{
    		//Get errors of log type.
			if ($Arr_StackDetail['type'] == $Int_Type)
			{
				$Str_ExceptionOutput .= date('[H:i:s][j-m-Y]', $Arr_StackDetail['time'])
								.' Code: '.$Arr_StackDetail['code']
    							.', Message: '.$Arr_StackDetail['message'];

				foreach ($Arr_StackDetail['trace'] as $Arr_StackTrace)
    			{
    				$Str_ExceptionOutput .= "\r\n".'#'.$Arr_StackTrace['stack'].' '.$Arr_StackTrace['line'].' ';

					if ($Arr_StackTrace['class'])
						$Str_ExceptionOutput .= $Arr_StackTrace['class'].'->';
					if ($Arr_StackTrace['function'])
						$Str_ExceptionOutput .= $Arr_StackTrace['function'].'()';

					$Str_ExceptionOutput .= ' '.$Arr_StackTrace['file'];
				}

				$Str_ExceptionOutput .= "\r\n";
			}
		}

		return $Str_ExceptionOutput;
	}



///////////////////////////////////////////////////////////////////////////////
//                     U T I L I T Y   F U N C T I O N S                     //
///////////////////////////////////////////////////////////////////////////////

	//Adds a space to the start of a string
	// - $Str_String:			String to add a space to
	// * Return:				$Str_String with a space added to the start
	public function str_lsp($Str_String)
	{
		if ((strlen($Str_String) > 1) && (substr($Str_String, 0, 1) != ' '))
			$Str_String = ' '.$Str_String;

		return $Str_String;
	}

	//Adds a space to the end of a string
	// - $Str_String:			String to add a space to
	// * Return:				$Str_String with a space added to the end
	private function str_rsp($Str_String)
	{
		if ((strlen($Str_String) > 1) && (substr($Str_String, -1, 1) != ' '))
			$Str_String .= ' ';

		return $Str_String;
	}


}

?>