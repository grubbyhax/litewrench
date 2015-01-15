<?php
//step_3.php
//Step 3 install model

class MW_Install_Model_Step_3 extends MW_Utility_Model
{
	protected $Arr_TableSchema	= array(
		'domain'				=> array(
			'column'			=> 'username',
			'format'			=> 'shorttext',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9_\/:]+'),
					'error'		=> 'Domain name must be a valid URL'))),
		'version'				=> array(
			'column'			=> 'username',
			'format'			=> 'shorttext',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[0-9.]+'),
					'error'		=> 'version must be a decimal number'))),
		'timezone'				=> array(
			'column'			=> 'password',
			'format'			=> 'shorttext',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9\/\-]+'),
					'error'		=> 'Timezone invalid'))),
		'organisation'				=> array(
			'column'			=> 'password',
			'format'			=> 'shorttext',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9]+'),
					'error'		=> 'Organisation must be letters and numbers only'))),
		'email'				=> array(
			'column'			=> 'password',
			'format'			=> 'integer',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9]+'),
					'error'		=> 'Must be a valid email address'))),
		'step'				=> array(
			'column'			=> 'password',
			'format'			=> 'integer',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[0-9]+'),
					'error'		=> 'Step must be numbers only'))));
}

?>