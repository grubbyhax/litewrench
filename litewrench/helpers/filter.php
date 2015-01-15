<?php
//filter.php
//Core filter class file

class MW_Helper_Filter extends MW_Utility_Helper
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Class constructor
	// * Return:					VOID
	public function __construct()
	{
		return;
	}


///////////////////////////////////////////////////////////////////////////////
//                       F I L T E R   M E T H O D S                         //
///////////////////////////////////////////////////////////////////////////////

	//Adds an html break tag to new lines in $Arr_Values(same as nl2br()).
	// - $Mix_Values:			Values to run filter on
	// * Return:				Filtered values
	public function newline_to_break($Mix_Values)
	{
		//$Bol_Success = true;

		//If the values are an array run filter on each value.
		if (is_array($Mix_Values))
		{
			foreach ($Mix_Values as $Mix_Key => $Str_Value)
			{
				$Mix_Values[$Mix_Key] = nl2br($Str_Value);
			}
		}
		//Otherwise if it is scalar run the filter on it.
		elseif (is_scalar($Mix_Values))
		{
			$Mix_Values = nl2br($Mix_Values);
		}
		//Otherwise handle exception.
		else
		{
			global $CMD;
			$CMD->handle_exception('Attempted to run newline_to_break() filter on an object.', 'MW:101');
		}

		return $Mix_Values;
	}
	
	//Replicates htmlentities PHP function.
	// - $Mix_Values:			Values to run filter on
	// - $Int_QuoteStyle:		Encoding quote style PHP constant, default = ENT_QUOTES
	// - $Str_Charset:			Characterset used in conversion, default = false(use config file)
	// - $Bol_DoubleEncode:		If true encode all existing entities, default = true
	// * Return:				Filtered values
	public function encode($Mix_Values, $Int_QuoteStyle = ENT_QUOTES, $Str_Charset=false, $Bol_DoubleEncode=true)
	{
		$Bol_Success = true;

		//If encoding characterset is not defined use settings default.
		if ($Str_Charset === false)
		{
			global $CMD;
			$Str_Charset = $CMD->config('encoding');
		}

		//If the values are an array run filter on each value.
		if (is_array($Mix_Values))
		{
			foreach ($Mix_Values as $Mix_Key => $Str_Value)
			{
				$Mix_Values[$Mix_Key] = htmlentities($Str_Value, $Int_QuoteStyle, $Str_Charset, $Bol_DoubleEncode);
			}
		}
		//Otherwise if it is scalar run the filter on it.
		elseif (is_scalar($Mix_Values))
		{
			$Mix_Values = htmlentities($Mix_Values, $Int_QuoteStyle, $Str_Charset, $Bol_DoubleEncode);
		}
		//Otherwise handle exception.
		else
		{
			global $CMD;
			$CMD->handle_exception('Attempted to run encode() filter on an object.', 'MW:101');
		}

		return $Mix_Values;
	}

	//Encrypts data $Arr_Values with the md5 hash alogorithm.
	// - $Mix_Values:			Values to run filter on
	// * Return:				Filtered values
	public function encrypt_md5($Mix_Values)
	{
		$Bol_Success = true;

		//If the values are an array run filter on each value.
		if (is_array($Mix_Values))
		{
			foreach ($Mix_Values as $Mix_Key => $Str_Value)
			{
				$Mix_Values[$Mix_Key] = (string) md5($Str_Value);
			}
		}
		//Otherwise if it is scalar run the filter on it.
		elseif (is_scalar($Mix_Values))
		{
			$Mix_Values = (string) md5($Mix_Values);
		}
		//Otherwise handle exception.
		else
		{
			global $CMD;
			$CMD->handle_exception('Attempted to run encrypt_md5() filter on an object.', 'MW:101');
		}

		return $Mix_Values;
	}
	
	//*!*Really need to handle arrays like with all filter functions.
	// - $Mix_EmailBody:		String of an email
	public function remove_email_headers($Mix_EmailBody)
	{
		$Arr_IllegalHeaders = array("/to\:/i", "/from\:/i", "/bcc\:/i", "/cc\:/i", "/Content\-Transfer\-Encoding\:/i", "/Content\-Type\:/i", "/Mime\-Version\:/i");
		$Mix_EmailBody = preg_replace($Arr_IllegalHeaders, '', $Mix_EmailBody);

		return $Mix_EmailBody;
	}

}

?>