<?php
//step_2.php
//Step 2 install model
class MW_Install_Model_Step_2 extends MW_Utility_Model
{
	protected $Arr_TableSchema	= array(
		'username'				=> array(
			'column'			=> 'username',
			'format'			=> 'shorttext',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9]+'),
					'error'		=> 'Username must be letters and numbers only'))),
		'password'				=> array(
			'column'			=> 'password',
			'format'			=> 'shorttext',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9]+'),
					'error'		=> 'Password must be letters and numbers only'))),
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