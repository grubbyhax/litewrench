<?php
//step_1.php
//Step 1 install model

class MW_Install_Model_Step_1 extends MW_Utility_Model
{
	protected $Arr_TableSchema	= array(
		'install'				=> array(
			'column'			=> 'install',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9_]*'),
					'error'		=> 'Install password must be letters, numbers and underscores only'))),
		'step'				=> array(
			'column'			=> 'step',
			'format'			=> 'integer',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[0-9]+'),
					'error'		=> 'Step must be numbers only'))));

}
?>