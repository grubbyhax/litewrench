<?php
//plugin.php
//Plugin file for MonkeyWrench.
//Copyright - David Thomson 2008.
//Support - david@hundredthcodemonkey.net
//Plugin file use: process_request(configure_request());

//Formats directory and file strings.
// - $Str_Path:					File path to format
// * Return:					Formatted file path
// * NB: This is a wrapper that deals with Windows/Linux diretory convetions
function path_request($Str_Path)
{
	$Str_Path = str_replace('\\', '/', $Str_Path);

	return $Str_Path;
}

//Not sure what this is doing here...
//Formats directory and file strings.
// - $Str_Path:					File path to format
// * Return:					Formatted file path
// * NB: This is a wrapper that deals with Windows/Linux diretory convetions
function file_request($Str_Path)
{
	$Str_Path = str_replace('/', '\\', $Str_Path);

	return $Str_Path;
}


//Gets and sets requestion configuration.
// * Arglist:					Configuration files to load in cascading order
// * Return:					Request configuration variables
function configure_request()
{
	$config = null;
	$Arr_ConfigurationFiles = array();

	//Set the default configuration.
	if (file_exists(path_request(dirname(__FILE__).'/config/default.php')))
	{
		include('config/default.php');
		$Arr_ConfigurationFiles[] = 'default';
	}

	//Loop through each function paramter and set the configuration files.
	$Int_Arguments = func_num_args();
	$Arr_Aruments = func_get_args();
	for ($i = 0; $i < $Int_Arguments; $i++)
	{
		if (!in_array($Arr_ConfigurationFiles[$Arr_Aruments[$i]])
		&& file_exists('config/'.$Arr_Aruments[$i].'.php'))
		{
			include('config/'.$Arr_Aruments[$i].'.php');
			$Arr_ConfigurationFiles[] = $Arr_Aruments[$i];
		}
	}

	$Arr_ConfigSettings = $config;
	$config = null;

	return $Arr_ConfigSettings;
}

//Gets components for requested webpage and builds it.
// - $Arr_ConfigSettings:		Request configuraation variables
// * Return:					VOID
function process_request($Arr_ConfigSettings)
{
	//Start of metrics.
	error_reporting(E_ALL);
	define('MW_CONST_FLT_MICRO_EXECUTE', microtime(true));

	//Define system root directory and base object.
	$Str_SystemRoot = path_request(dirname(__FILE__));
	define('MW_CONST_STR_DIR_INSTALL', $Str_SystemRoot.'/');
	require(MW_CONST_STR_DIR_INSTALL.'system/base.php');
	require('events.php');
	require('router.php');

	//Foreman object(global API).
	global $CMD;
	require(MW_CONST_STR_DIR_INSTALL.'system/command.php');
	$Flt_SecurityCode = microtime(true);
	$CMD = new MW_System_Command($Flt_SecurityCode, $Arr_ConfigSettings);

	//Do installation(requires install.php to be in MW_CONST_STR_DIR_INSTALL)
	$CMD->install($Flt_SecurityCode);

	//Build requested webpage.
	$Obj_Module = $CMD->plan(false);

	//Handle empty build string exception.
	if (!$Str_Response = $Obj_Module->process_request())
		$Obj_Module->append($CMD->handle_exception('Empty webpage request.', 'MW:100'));

	//Send requested webpage to client.
	$CMD->send_response($Str_Response, $Flt_SecurityCode);
	return;
}

?>