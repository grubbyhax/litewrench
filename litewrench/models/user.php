<?php
//user.php
//User model class file

class MW_Model_User extends MW_Utility_Model
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Document data.
	private $Str_Person			= '';

	//Parcel database data table schema.
	protected $Arr_TableSchema	= array(
		'access'			=> array(
			'column'			=> 'access',
			'format'			=> 'string',
			'default'			=> '',
			'lookup'			=> 'index',
			'validate'			=> array(
				array(
					'match'		=> array('required'),
					'error'		=> 'user_access_req'),
				array(
					'match'		=> array('regex', '^[a-zA-Z0-9_-]+$'),
					'error'		=> 'user_access_regex'))),
		'username'			=> array(
			'column'			=> 'username',
			'format'			=> 'string',
			'lookup'			=> 'key',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('required'),
					'error'		=> 'user_username_req'),
				array(
					'match'		=> array('regex', '^[a-zA-Z0-9_-]+$'),
					'error'		=> 'user_username_regex'))),
		'password'			=> array(
			'column'			=> 'password',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('required'),
					'error'		=> 'user_password_req'),
				array(
					'match'		=> array('regex', '^[\S]+$'),
					'error'		=> 'user_password_regex'))),
		'email'				=> array(
			'column'			=> 'email',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('required'),
					'error'		=> 'user_email_req'),
				array(
					'match'		=> array('regex', MW_REG_EMAIL),
					'error'		=> 'user_email_regex'))),
		'recovery'			=> array(
			'column'			=> 'recovery',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('required'),
					'error'		=> 'user_password_req'))));



///////////////////////////////////////////////////////////////////////////////
//            P A R C E L   H A N D L I N G   F U N C T I O N S              //
///////////////////////////////////////////////////////////////////////////////


}

?>