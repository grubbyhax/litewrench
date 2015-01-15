<?php
//mw_base.php
//Base class file.
//Design by David Thomson at Hundredth Codemonkey.
//*!*This really should be in the system directory


class MW_System_Base
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	// *** Nihlist Reference *** //
	private $Bol_Annilate		= false;

	//*** Object Reference Identifier ***//
	private $Int_ReferenceId	= null;



///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties	= array();



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//The base class is a security and development class
	//*!*Note here, the pattern applied to this object will not be extended inspfar as the parent method is not being called.
	// - $Bol_Annilate:				Flag to self destruct, default true
	// * NB: This is an implementation of the nihlist pattern.
	// This class should never be instantiated. All behaviour
	// must be extended using static methods and properties.
	public function __construct($Bol_SelfDestruct=true)
	{
		//Nihlist pattern.
		$this->Bol_Annilate = $Bol_SelfDestruct;
		$this->__destruct();
	}

	//Logs exception when class has been self destructed.
	public function __destruct()
	{
		//Take note of object's brief existance.
		if ($this->Bol_Annilate)
		{
			global $GLB_FOREMAN;
			if ($GLB_FOREMAN)
			{
				//Get debug trace.
				$GLB_FOREMAN->handle_exception('Attempt to create instance of mw_base class made.', 'MW:100',
				MW_CONST_INT_EXCEPTION_TYPE_CONF);
			}
		}
	}

	//Set object property with system debugging.
	// - $Var_Name:				Name of property to set locally
	// - $Var_Value:			Value of property to set locally
	// * Return:				VOID
	public function __set($Var_Name, $Var_Value)
	{
		if (array_key_exists($Var_Name, $this->Arr_Properties))
		{
			//Set property.
			$this->Arr_Properties[$Var_Name] = $Var_Value;

			//Do debugging.
			global $GLB_DEBUGGER;
			if ($GLB_DEBUGGER)
			{
				//Get debug trace.
				$GLB_DEBUGGER->set_backtrace(__CLASS__);
			}
		}

		return;
	}

	//Get object property.
	// - $Var_Name:				Name of local property to get
	// * Return:				Value of local property if set, otherwise null.
	public function __get($Var_Name)
	{
		if (array_key_exists($Var_Name, $this->Arr_Properties))
			return $this->Arr_Properties[$Var_Name];

		return null;
	}

	//Test if object property exists.
	// - $Var_Name:				Name of local property to test for
	// * Return:				True if property exists, otherwise false
	public function __isset($Var_Name)
	{
		return isset($this->Arr_Properties[$Var_Name]);
	}


	//Remove property from object
	// - $Var_Name:				Name of local property to remove
	// * Return:				VOID
	public function __unset($Var_Name)
	{
		unset($this->Arr_Properties[$Var_Name]);
		return;
	}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

	//Sets object reference identifier in system execution.
	// - $Int_RefId:			Unique object identifier
	// * Return:				VOID
	public function set_ref($Int_RefId)
	{
		$this->Int_ReferenceId = $Int_RefId;
		return;
	}

	//Gets object reference identifier in system execution.
	// * Return:				Unqiue object identifier
	public function get_ref()
	{
		return $this->Int_ReferenceId;
	}

}

?>