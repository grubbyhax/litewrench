<?php
//step_5.php
//Step 5 install model

class MW_Install_Model_Step_5 extends MW_Utility_Model
{
	protected $Arr_TableSchema	= array(
		'cms'				=> array(
			'column'			=> 'cms',
			'format'			=> 'boolean',
			'default'			=> '0',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[a-zA-Z0-9_]+'),
					'error'		=> 'CMS option must be boolean'))),
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