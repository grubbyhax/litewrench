<?php
//step_6.php
//Step 6 install model

class MW_Install_Model_Step_6 extends MW_Utility_Model
{
	protected $Arr_TableSchema	= array(
		'step'				=> array(
			'column'			=> 'step',
			'format'			=> 'boolean',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '[0-9]+'),
					'error'		=> 'Step must be numbers only'))));
}

?>