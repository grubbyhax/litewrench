<?php
//step_4.php
//Step 4 install model

class MW_Install_Model_Step_4 extends MW_Utility_Model
{
	protected $Arr_TableSchema	= array(
		'database'				=> array(
			'column'			=> 'database',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9_]+'),
					'error'		=> 'Username must be letters, numbers and underscores only'))),
		'username'				=> array(
			'column'			=> 'username',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9]+'),
					'error'		=> 'Username must be letters and numbers only'))),
		'password'				=> array(
			'column'			=> 'password',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9]*'),
					'error'		=> 'Password must be letters and numbers only'))),
		'location'				=> array(
			'column'			=> 'password',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9]+'),
					'error'		=> 'Password must be letters and numbers only'))),
		'step'				=> array(
			'column'			=> 'password',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[0-9]+'),
					'error'		=> 'Step must be numbers only'))));
}

?>