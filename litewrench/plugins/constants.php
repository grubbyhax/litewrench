<?php
//constants.php
//Monkeywrench constants file.
//*!*This is going to be a plugin file

///////////////////////////////////////////////////////////////////////////////
//                 U S E R   A C C E S S   C O N S T A N T S                 //
///////////////////////////////////////////////////////////////////////////////

	//These values are dynamic(after the first four) so should be identified in the config file or events file.
	//*!*I must install these into the cms,  the values will be the bitwise of the entry id.
	define('MW_CONST_BIT_ACCESS_PUBLIC',	 		1);
	define('MW_CONST_BIT_ACCESS_MEMBER',	 		2);
	define('MW_CONST_BIT_ACCESS_STAFF',		 		4);
	define('MW_CONST_BIT_ACCESS_ADMIN',				8);

	define('MW_CONST_STR_DB_ACECSS_INHERIT',		'x');
	define('MW_CONST_STR_DB_LIKE_MATCH_SINGLE',		'_'); //this shoulld be in the driver or somewhere else.



///////////////////////////////////////////////////////////////////////////////
//                U S E R   S E S S I O N   C O N S T A N T S                //
///////////////////////////////////////////////////////////////////////////////

	//Core session status variables.
	define('MW_STR_SESSION_USERLOGN',				'MW_USERLOGN');
	define('MW_STR_SESSION_USERIDEN',				'MW_USERIDEN');
	define('MW_STR_SESSION_USERNAME',				'MW_USERNAME');
	define('MW_STR_SESSION_USERMAIL',				'MW_USERMAIL');
	define('MW_STR_SESSION_USERSTAT',				'MW_USERSTAT');
	define('MW_STR_SESSION_USERVRSN',				'MW_USERVRSN');
	define('MW_STR_SESSION_USERZONE',				'MW_USERZONE');
	define('MW_STR_SESSION_USERLANG',				'MW_USERLANG');
	define('MW_STR_SESSION_USEREDIT',				'MW_USEREDIT');
	define('MW_STR_SESSION_USERVIEW',				'MW_USERVIEW');
	define('MW_STR_SESSION_USERLOCK',				'MW_USERLOCK');



///////////////////////////////////////////////////////////////////////////////
//                S Y S T E M   P A T H   C O N S T A N T S                  //
///////////////////////////////////////////////////////////////////////////////

	// *** DYNAMICALLY DEFINED CONSTANTS *** //
	//MW_CONST_STR_DIR_DOMAIN - Website domain url address.
	//MW_CONST_STR_DIR_INSTALL - Monkeywrench file directory.

	// *** SYSTEM LIBRARY FILES *** //
	define('MW_CONST_STR_FILE_DEBUGGER',			MW_CONST_STR_DIR_INSTALL.'system/debugger.php');


	// *** FRAMEWORK DIRECTORIES *** //
	define('MW_CONST_STR_DIR_PARCELS',				MW_CONST_STR_DIR_INSTALL.'parcels/');
	define('MW_CONST_STR_DIR_DRIVERS',				MW_CONST_STR_DIR_INSTALL.'drivers/');

	// *** LOG FILES *** //
	define('MW_CONST_STR_DIR_LOG',					MW_CONST_STR_DIR_INSTALL.'logfiles/');
	define('MW_CONST_STR_FILE_DEBUG_USER',			MW_CONST_STR_DIR_LOG.'debug_user.log');
	define('MW_CONST_STR_FILE_EXCEPT_CONF',			MW_CONST_STR_DIR_LOG.'except_conf.log');
	define('MW_CONST_STR_FILE_EXCEPT_DATA',			MW_CONST_STR_DIR_LOG.'except_data.log');
	define('MW_CONST_STR_FILE_EXCEPT_USER',			MW_CONST_STR_DIR_LOG.'except_user.log');

	// *** USER DEFINED FILES *** //
	/*
	define('MW_CONST_STR_DIR_USERDEFINED',			MW_CONST_STR_DIR_INSTALL.'udf/');
	define('MW_CONST_STR_FILE_UDF_CONSTANTS',		MW_CONST_STR_DIR_USERDEFINED.'ud_constants.php');
	define('MW_CONST_STR_FILE_UDF_EVENT',			MW_CONST_STR_DIR_USERDEFINED.'ud_event.php');
	define('MW_CONST_STR_FILE_UDF_EXCEPTION',		MW_CONST_STR_DIR_USERDEFINED.'ud_exception.php');
	*/
	// *** EXTENSION MODULES DIRECTORY *** //
	define('MW_CONST_STR_DIR_EXTENSION',			MW_CONST_STR_DIR_INSTALL.'modules/');



///////////////////////////////////////////////////////////////////////////////
//              E X C E P T I O N   T Y P E   C O N S T A N T S              //
///////////////////////////////////////////////////////////////////////////////

	// *** INTERNAL EXCEPTION TOKEN *** //
	define('MW_CONST_TOK_EXCEPTION_INTERNAL',		'MW');

	// *** EXCEPTION TYPES *** //
	define('MW_CONST_INT_EXCEPTION_TYPE_CONF',		1);
	define('MW_CONST_INT_EXCEPTION_TYPE_DATA',		2);
	define('MW_CONST_INT_EXCEPTION_TYPE_USER',		3);

	// *** EXCEPTION CODES *** //
	define('MW_CONST_INT_EXCEPTION_EXCEPTION',		001);
	define('MW_CONST_INT_EXCEPTION_CODEBASE',		002);
	define('MW_CONST_INT_EXCEPTION_PARCEL',			003);
	define('MW_CONST_INT_EXCEPTION_BUILDER',		004);
	define('MW_CONST_INT_EXCEPTION_PLUGIN',			005);

	// *** Default exception Code ** //
	define('MW_CONST_INT_EXCEPTION_DEFAULT',		1001);



///////////////////////////////////////////////////////////////////////////////
//                          W I D G E T   H O O K S                          //
///////////////////////////////////////////////////////////////////////////////

	define('MW_HOOK_CHAR_HOOKOPEN',		'{');
	define('MW_HOOK_CHAR_HOOKCLOSE',	'}');
	define('MW_HOOK_CHAR_SEPARATOR',	'|');

	define('MW_STR_FORMAT_DATETIME',	'Y-m-d H:i:s');
	define('MW_REG_SYSTEM_DATETIME',	'^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$');
	//NB: MW_REG_JQUERYUI_DATETIME cannot be wrapped in forward slashes as it will not escape properly.
	//define('MW_REG_JQUERYUI_DATETIME',	'([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2})');
	define('MW_REG_JQUERYUI_DATETIME',	'([0-9]{2})/([0-9]{2})/([0-9]{4})( ([0-9]{2}):([0-9]{2}))*'); //this should be optional once.


///////////////////////////////////////////////////////////////////////////////
//                 D A T A   F O R M A T   C O N S T A N T S                 //
///////////////////////////////////////////////////////////////////////////////

	//*!*Need to replace the URI regex with the web standard PERL URI regex.
	//*!*I might change the representation of these constants to MW_REG_MATCH_NAME
	define('MW_REG_URI',				'^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$');
	define('MW_REG_EMAIL',				"^(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$");


?>