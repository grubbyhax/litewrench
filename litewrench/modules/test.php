<?php
//user.php
//User extension module file.
//Design by David Thomson at Hundredth Codemonkey.


///////////////////////////////////////////////////////////////////////////////
//                     M O D U L E   C O N S T A N T S                       //
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
//                        M O D U L E   C L A S S                            //
///////////////////////////////////////////////////////////////////////////////



class MW_Module_Test extends MW_Utility_Module
{


///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Cleanup any module processes.
	private $Bol_Cleanup		= false;



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	public function __construct()
	{
		//Turn on debugging for the module.
		/*!*This should be in the module parent class as a method.
		global $GLB_DEBUGGER;
		if (LOGIN_CONST_INT_DEBUG_MODULE)
		{
			$GLB_DEBUGGER->add_dir(LOGIN_CONST_STR_DIR_ROOT);
		}
		*/
	}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////


	//Responds to a module request, the return value replaces a builder widget
	public function process_request()
	{
		//$Obj_Company = $this->model('company');
		$Arr_Range = array('new'=>'new', 'old'=>'old');
		$Obj_Form = $this->helper('form')->name('test')->method('post')->action($this->config('full_path'))
						->field('check', 'radio')->range($Arr_Range)->display('old')
						->field('text', 'text')->rule(array('match'=>array('required'), 'error'=>'error msg'))->opt(array('check', 'equal', 'new'));//

		$Str_Valid = 'invalid';
		if ($Obj_Form->post())
		{
			$Str_Valid = 'valid';
		}

		echo $this->plan('test')->bind(array('form'=>$Obj_Form, 'valid'=>$Str_Valid, 'range'=>$Arr_Range))->build();
		return;
	}

	//Gets the recovery value of the module exception with code $Int_Code
	// - $Int_Code:				Exception code integer
	public function handle_exception($Int_Code)
	{
		$Var_Recovery = null;

		//Get recovery value.
		switch ($Int_Code)
		{
			case LOGIN_CONST_INT_EXCEPTION_DEFAULT: $Var_Recovery = false; break;
			case LOGIN_CONST_INT_EXCEPTION_REQUEST: $Var_Recovery = 'Empty request!'; break;
			default: $Var_Recovery = '';
		}

		return $Var_Recovery;
	}

	public function define_caching(){}



///////////////////////////////////////////////////////////////////////////////
//                 U T I L I T Y   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

}

?>