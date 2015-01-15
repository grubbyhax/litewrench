<?php
//validator.php
//Core validator class file

class MW_Helper_Validate extends MW_Utility_Helper
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
//                   V A L I D A T I O N   M E T H O D S                     //
///////////////////////////////////////////////////////////////////////////////

	//Matches an array of string values to regular expression.
	// - $Arr_Values:			Array of values to match against regex
	// - $Str_Regex:			Regular express to match against
	// - $Bol_MatchAll:			Requires all values to match regex, default=true(match all values)
	// * Return:				True if values are valid, otherwise false
	// NB: If $Bol_MatchAll is false only one match will produce a valid result.
	//*!*I should change $Bol_MatchAll to a number to test the number of values testing valid.
	public function regex($Arr_Values, $Str_Regex, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Str_Value)
			{
				//If there is no regex match invalid values.
				//*!*Will also need to test that $Str_Value is a string for script integrity...
				//Will also need to do a quality check on the regex string as it can be a variable variable
				if (!preg_match('/'.$Str_Regex.'/', $Str_Value))
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}

		return $Bol_Valid;
	}

	//Tests for numbers within an array to be less than a value.
	// - $Arr_Values:			Array of values to less than $Mix_Number
	// - $Mix_Number:			Float or interger to test against
	// - $Bol_MatchAll:			Requires all values to test true, default=true(match all values)
	// NB: If $Bol_MatchAll is false only one value less than will produce a valid result.
	public function less_than($Arr_Values, $Mix_Number, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Mix_Value)
			{
				//If there is no regex match invalid values.
				//Will also need to do a quality check on $Mix_Number is numeric as it can be a variable variable
				if (!is_numeric($Mix_Value) || $Mix_Value > $Mix_Number)
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}

		return $Bol_Valid;
	}

	//Tests for numbers within an array to be greater than a value.
	// - $Arr_Values:			Array of values to be greater than $Mix_Number
	// - $Mix_Number:			Float or interger to test against
	// - $Bol_MatchAll:			Requires all values to test true, default=true(match all values)
	// NB: If $Bol_MatchAll is false only one value greater than will produce a valid result.
	public function greater_than($Arr_Values, $Mix_Number, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Mix_Value)
			{
				//If there is no regex match invalid values.
				//Will also need to do a quality check on $Mix_Number is numeric as it can be a variable variable
				if (!is_numeric($Mix_Value) || $Mix_Value < $Mix_Number)
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}

		return $Bol_Valid;
	}

	//Is numeric and is betweeen two values
	// - $Arr_Values:			Array of values to be tested within a range
	public function between($Arr_Values, $Mix_LowNumber, $Mix_HighNumber, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Mix_Value)
			{
				//If there is no regex match invalid values.
				//Will also need to do a quality check on $Mix_Number is numeric as it can be a variable variable
				if (!is_numeric($Mix_Value) || ($Mix_Value < $Mix_LowNumber && $Mix_Value > $Mix_HighNumber))
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}

		return $Bol_Valid;
	}

	//Is equal to a value, not strict
	// - $Arr_Values:			Array of values to be equal to
	public function equal($Arr_Values, $Mix_Equal, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Mix_Value)
			{
				//If there is no regex match invalid values.
				//Will also need to do a quality check on $Mix_Number is numeric as it can be a variable variable
				if ($Mix_Value != $Mix_Equal)
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}

		return $Bol_Valid;
	}

	//Is not equal to a value, not strict
	// - $Arr_Values:			Array of values to be equal to
	public function unequal($Arr_Values, $Mix_Unequal, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Mix_Value)
			{
				//If there is no regex match invalid values.
				//Will also need to do a quality check on $Mix_Number is numeric as it can be a variable variable
				if ($Mix_Value == $Mix_Unequal)
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}

		return $Bol_Valid;
	}

	//Is equal to a value, strict
	// - $Arr_Values:			Array of values to be identical to
	public function identical($Arr_Values, $Mix_Identical, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Mix_Value)
			{
				//If there is no regex match invalid values.
				//Will also need to do a quality check on $Mix_Number is numeric as it can be a variable variable
				if ($Mix_Value !== $Mix_Identical)
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}

		return $Bol_Valid;
	}

	//Value is within an array
	// - $Arr_Values:			Array of values to be within an array
	public function within($Arr_Values, $Arr_Array, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Mix_Value)
			{
				//If there is no regex match invalid values.
				//Will also need to do a quality check on $Mix_Number is numeric as it can be a variable variable
				if (!in_array($Mix_Value, $Arr_Array))
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}

		return $Bol_Valid;
	}

	//Is a boolean value.
	// - $Arr_Values:			Array of values to be a boolean value
	public function boolean($Arr_Values, $Mix_Boolean, $Bol_MatchAll=true)
	{
		//Set test default value.
		$Bol_Valid = ($Bol_MatchAll)? true: false;

		//If the values are not set to false, evaluate each value.
		if ($Arr_Values !== false)
		{
			//Loop through each value.
			foreach ($Arr_Values as $Mix_Value)
			{
				//If there is no regex match invalid values.
				//Will also need to do a quality check on $Mix_Number is numeric as it can be a variable variable
				if ($Mix_Value && !$Mix_Boolean || !$Mix_Value && $Mix_Boolean)
				{
					$Bol_Valid = false;
					break;
				}
				//Else if we don't need to match all values validate values.
				elseif (!$Bol_MatchAll)
				{
					$Bol_Valid = true;
					break;
				}
			}
		}
		//Otherwise if the blooean value evalutes as true invalidate test.
		elseif ($Mix_Boolean)
		{
			$Bol_Valid = false;
		}

		return $Bol_Valid;
	}

	//has a file extension.
	// - $Arr_Values:			Array of string values to test for a file extension
	public function extension($Arr_Values)
	{

	}

	//String length is greater than a number.
	// - $Arr_Values:			Array of string values to test for a minimum length
	public function min_length($Arr_Values)
	{

	}

	//String length is less than a number
	// - $Arr_Values:			Array of string values to test for a maximum length
	public function max_length($Arr_Values)
	{

	}

	//is numeric
	// - $Arr_Values:			Array of string values to test to be numeric
	public function numeric($Arr_Values)
	{

	}

	//is numeric and is a decimal numer
	// - $Arr_Values:			Array of string values to test to bo a decimal number
	public function decimal($Arr_Values)
	{

	}
	
	//Test that an uploaded file has no errors.
	// - $Arr_Values:			Array of file values to test upload succeeded
	// * Return:				True if file was successfully uploaded, otherwise false
	public function upload_file($Arr_Values)
	{
		$Bol_Valid = false;

		if (isset($Arr_Values['name']) && !$Arr_Values['name'])
		{
			$Bol_Valid = true;
		}
		elseif (isset($Arr_Values['error']) && !$Arr_Values['error'])
		{
			$Bol_Valid = true;
		}

		return $Bol_Valid;
	}

	//tests the size of the uploaded file.
	// - $Arr_Values:			Array of file values to test upload file size
	// - $Int_MaxSize:			maximum allowable size of uploaded file
	// * Return:				True if file is less than or equal to $Int_MaxSize, other false
	public function upload_size($Arr_Values, $Int_MaxSize)
	{
		$Bol_Valid = false;

		if (isset($Arr_Values['size']) && $Arr_Values['size'] < $Int_MaxSize)
			$Bol_Valid = true;

		return $Bol_Valid;
	}

	//Test that an uploaded file has no errors.
	// - $Arr_Values:			Array of file values to test upload succeeded
	// * Return:				True if file was successfully uploaded, otherwise false
	public function upload_image($Arr_Values)
	{
		$Bol_Valid = false;

		if (isset($Arr_Values['name']) && $Arr_Values['name'])
		{
			//Get extension.
			$Arr_PathInfo = pathinfo($Arr_Values['name']);
			switch(strtolower($Arr_PathInfo['extension']))
			{
				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'gif': $Bol_Valid = true;
			}

		}

		return $Bol_Valid;
	}

	//*!*This really belongs in another class for optionals alone.
	//*!* Will fix parameters later, I've left this as an optional extra which might be used.
	//Tests to see if $Mix_Boolean has a value.
	// - $Mix_Boolean:			Input variables to search for a value
	// * Return:				True if $Mix_Boolean has a value, otherwise false
	public function required($Mix_Boolean, $Arr_Paramters='')
	{
		if (is_array($Mix_Boolean))
		{
			foreach ($Mix_Boolean as $Str_Value)
			{
				if ($Str_Value)
				{
					return true;
				}
			}
		}
		elseif ($Mix_Boolean)
		{
			return true;
		}
		
		return false;
	}


}

?>