<?php
//xtcpdf.php
//This is the PDF output plugin script.

require_once(MW_CONST_STR_DIR_INSTALL.'libraries/tcpdf/config/lang/eng.php');
require_once(MW_CONST_STR_DIR_INSTALL.'libraries/tcpdf/tcpdf.php');

//Set the PDF object locally.
$Arr_NewVars = array();
$Arr_NewVars['Obj_Doc'] = new XTCPDF();
$this->Arr_Vars = $Arr_NewVars;

class XTCPDF extends TCPDF
{

	public $Str_Header			= true;
	public $Str_Footer			= true;

	private $Int_ReferenceId	= null;


	//Sets object reference identifier in system execution.
	// - $Int_RefId:			Unique object identifier
	// * Return:				VOID
	public function set_ref($Int_RefId)
	{
		$this->Int_ReferenceId = $Int_RefId;
		return;
	}

	public function plan($Str_PluginName)
	{
		//this doesn't do anything.
	}


	public function setPrintHeader($Bol_Display=true)
	{
		if ($Bol_Display == false)
		{
			parent::setPrintHeader($Bol_Display);
		}

		$this->Str_Header = $Bol_Display;
		return;
	}

	public function setPrintFooter($Bol_Display=false)
	{
		if ($Bol_Display == false)
		{
			parent::setPrintFooter($Bol_Display);
		}

		$this->Str_Footer = $Bol_Display;
		return;
	}

	public function Header()
	{
		//Call parent method.
		if ($this->Str_Header === true)
		{
			parent::Header();
		}
		//Include the view file.
		elseif (is_string($this->Str_Header) && $this->Str_Header)
		{
			global $CMD;
			$Str_Theme = $CMD->helper('page')->has_asset('layouts/'.$this->Str_Header.'.html');
			include(MW_CONST_STR_DIR_DOMAIN.'themes/'.$Str_Theme.'/layouts/'.$this->Str_Header.'.html');
		}
	}

	public function Footer()
	{
		if ($this->Str_Header === true)
		{
			parent::Footer();
		}
		elseif (is_string($this->Str_Footer) && $this->Str_Footer)
		{
			global $CMD;
			$Str_Theme = $CMD->helper('page')->has_asset('layouts/'.$this->Str_Footer.'.html');
			include(MW_CONST_STR_DIR_DOMAIN.'themes/'.$Str_Theme.'/layouts/'.$this->Str_Footer.'.html');
		}
	}


}
?>