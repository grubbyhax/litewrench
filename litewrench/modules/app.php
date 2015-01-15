<?php
//app.php
//App extension module file.
//Design by David Thomson at Hundredth Codemonkey.

//This is just some app code from a website


///////////////////////////////////////////////////////////////////////////////
//                     M O D U L E   C O N S T A N T S                       //
///////////////////////////////////////////////////////////////////////////////

//Module identifiers.
define('EXAMPLE_CONST_STR_CLASS_PREFIX',		'EX');
define('EXAMPLE_CONST_STR_CLASS_PLUGIN',		'Example');

//Module root.
define('EXAMPLE_CONST_STR_DIR_ROOT',			dirname(__FILE__));

//Exception codes.
define('EXAMPLE_CONST_INT_EXCEPTION_DEFAULT',	101);
define('EXAMPLE_CONST_INT_EXCEPTION_REQUEST',	201);

//Module switches.
define('EXAMPLE_CONST_INT_DEBUG_MODULE',		1);



///////////////////////////////////////////////////////////////////////////////
//                        M O D U L E   C L A S S                            //
///////////////////////////////////////////////////////////////////////////////

class MW_Module_App extends MW_Utility_Module
{

///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Cleanup any module processes.
	private $Bol_Cleanup			= false;


///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	public function __construct()
	{
		//Turn on debugging for the module.
		/*!*This should be in the module parent class as a method.
		global $GLB_DEBUGGER;
		if (EXAMPLE_CONST_INT_DEBUG_MODULE)
		{
			$GLB_DEBUGGER->add_dir(EXAMPLE_CONST_STR_DIR_ROOT);
		}
		*/
	}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////


	//Removes excess special characters from $Str_TextInput before converting to a well formed string.
	// - $Str_TextInput:			Form input text to be formatted
	// * Return:					String with excess special characters removed, ready for xml format
	public function FormatTextInput($Str_TextInput)
	{
		//Declare return string.
		$Str_FormattedString = $Str_TextInput;

		//Remove excess carriage returns.
		$Int_DblReturn = TRUE;
		while ($Int_DblReturn !== FALSE)
		{
			$Str_FormattedString = str_replace("\r\r", "\r", $Str_FormattedString);
			$Int_DblReturn = strpos($Str_FormattedString, "\r\r");
		}

		//Remove excess spaces.
		$Int_DblSpace = TRUE;
		while ($Int_DblSpace !== FALSE)
		{
			$Str_FormattedString = str_replace('  ', ' ', $Str_FormattedString);
			$Int_DblSpace = strpos($Str_FormattedString, '  ');
		}

		//Cleanup special characters.
		$Str_FormattedString = str_replace("\t", "", $Str_FormattedString);
		$Str_FormattedString = str_replace("\n", "", $Str_FormattedString);
		$Str_FormattedString = str_replace("\f", "", $Str_FormattedString);
		$Str_FormattedString = str_replace("\v", "", $Str_FormattedString);
		$Str_FormattedString = str_replace('\"', '"', $Str_FormattedString);
		$Str_FormattedString = str_replace("\'", "'", $Str_FormattedString);

		return $Str_FormattedString;
	}

	//Adds paragraphs tags to each item in a string array.
	// - $Arr_InputTextParagraphs:	Array of strings each item representing one paragraph
	// * Return:					Single string as a sequence of markup paragraphs
	public function GenerateTextOutput($Str_TextToOutput)
	{
		//Declare return string.
		$Str_FormattedOutput = '';

		$Arr_InputTextParagraphs = explode("\n", $Str_TextToOutput);

		//Add each paragraph.
		foreach ($Arr_InputTextParagraphs as $Str_Paragraph)
		{
			if ($Str_Paragraph != '')
			$Str_FormattedOutput .= $Str_Paragraph.'<br />';
		}

		return $Str_FormattedOutput;
	}


	//Responds to a module request, the return value replaces a builder widget
	public function process_request()
	{
		$Obj_Form = $this->helper('form')->name('contact')->action($this->config('full_path'))->method('post')
						->field('name', 'text')
						->field('email', 'text')
						->field('message', 'textarea');

		//Do contact form.
		if ($this->value('control') == 'api')
		{
			if (isset($_POST['inputs']) && isset($_POST['type']) && ($_POST['type'] == 'contact'))
			{
				$Str_Name = isset($_POST['inputs']['name'])? $_POST['inputs']['name']: '';
				$Str_Email = isset($_POST['inputs']['email'])? $_POST['inputs']['email']: '';
				$Str_Message = isset($_POST['inputs']['message'])? $_POST['inputs']['message']: '';

				$Arr_Response = $_POST;
				$Arr_Response['errors'] = array();
				if ($Obj_Form->process($_POST['inputs'], true))
				{
					$Arr_Email = array('name'=>$Str_Name, 'email'=>$Str_Email, 'message'=>$Str_Message);
					$Obj_Mailer = $this->helper('mailer')->subject('Website inquiry '.$this->config('root_url'))
						->message($this->plan('contact')->bind($Arr_Email)->build());
					$Obj_Mailer->to($this->config('inbox'))->from($Str_Email)->reply_to($Str_Email)->send();

					return json_encode($Arr_Response);
				}
				else
				{
					$Arr_Response['errors'] = $Obj_Form->errors();
				}

				return $Arr_Response;
			}
		}

		return $this->plan('public')->bind(array('form'=>$Obj_Form))->build();
	}

	//Gets the recovery value of the module exception with code $Int_Code
	// - $Int_Code:				Exception code integer
	public function handle_exception($Int_Code)
	{
		$Var_Recovery = null;

		//Get recovery value.
		switch ($Int_Code)
		{
			case EXAMPLE_CONST_INT_EXCEPTION_DEFAULT: $Var_Recovery = false; break;
			case EXAMPLE_CONST_INT_EXCEPTION_REQUEST: $Var_Recovery = 'Empty request!'; break;
			default: $Var_Recovery = '';
		}

		return $Var_Recovery;
	}

	public function define_caching(){}

}

?>