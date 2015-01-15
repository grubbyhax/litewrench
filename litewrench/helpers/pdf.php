<?php
//pdf.php
//PDF helper class file

class MW_Helper_Pdf extends MW_Utility_Helper
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	public $Obj_Doc		= false;


///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Class constructor
	// * Return:					VOID
	// * NB: This helper uses the TCPDF library
	public function __construct()
	{
		//Get TCPDF library files
		require_once(MW_CONST_STR_DIR_INSTALL.'libraries/tcpdf/config/lang/eng.php');
		require_once(MW_CONST_STR_DIR_INSTALL.'libraries/tcpdf/tcpdf.php');


		// create new PDF document
		global $CMD;
		$Obj_Plugin = $CMD->plugin('xtcpdf');
		$Obj_Plugin->build();
		$this->Obj_Doc = $Obj_Plugin->Arr_Vars['Obj_Doc'];

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//                       O U T P U T   M E T H O D S                         //
///////////////////////////////////////////////////////////////////////////////


	public function output($Str_FileName, $Str_OutputType='I')
	{
		//Test is a test for PDF
		if ($Str_OutputType == 'I')
		{
			header("Content-type: application/pdf");
		}

		$this->Obj_Doc->Output($Str_FileName, $Str_OutputType);

		if ($Str_OutputType == 'I')
		{
			exit;
		}

		return;
	}

	public function meta($Arr_MetaData=array())
	{
		if ($Arr_MetaData)
		{
			if (isset($Arr_MetaData['creator']))
			{
				$this->Obj_Doc->SetCreator($Arr_MetaData['creator']);
			}

			if (isset($Arr_MetaData['author']))
			{
				$this->Obj_Doc->SetAuthor($Arr_MetaData['author']);
			}

			if (isset($Arr_MetaData['title']))
			{
				$this->Obj_Doc->SetTitle($Arr_MetaData['title']);
			}

			if (isset($Arr_MetaData['subject']))
			{
				$this->Obj_Doc->SetSubject($Arr_MetaData['subject']);
			}

			if (isset($Arr_MetaData['keywords']))
			{
				$this->Obj_Doc->SetKeywords($Arr_MetaData['keywords']);
			}
		}

		return $this;
	}


	public function header($Str_Header=false)
	{
		$this->Obj_Doc->setPrintHeader($Str_Header);
		return $this;
	}

	public function footer($Str_Footer=false)
	{
		$this->Obj_Doc->setPrintFooter($Str_Footer);
		return $this;
	}

/*
	function header($Arr_Header=false)
    {
		if ($Arr_Header && isset($Arr_Header['text']))
		{
	        if (isset($Arr_Header['fillcolor']))
	        {
				list($r, $b, $g) = $Arr_Header['fillcolor'];
   		    	$this->Obj_Doc->SetFillColor($r, $b, $g);
			}
			else
			{
   		    	$this->Obj_Doc->SetFillColor(255, 255, 255);
			}

	        if (isset($Arr_Header['textcolor']))
	        {
				list($r, $b, $g) = $Arr_Header['textcolor'];
	    		$this->Obj_Doc->SetTextColor($r, $b, $g);
			}
			else
			{
	    		$this->Obj_Doc->SetTextColor(168, 168, 168);
			}

	        if (isset($Arr_Header['font']))
	        {
				$this->Obj_Doc->setHeaderFont(Array($Arr_Header['font']['family'], $Arr_Header['font']['style'], Arr_Header['font']['size']));
			}
			else
			{
				$this->Obj_Doc->setHeaderFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
			}

   	    	$this->Obj_Doc->setY(10);
    		$this->Obj_Doc->Cell(0, 20, '', 0,1, 'C', 1);
    		$this->Obj_Doc->Text(15, 26, $Arr_Header['text']);
		}

		return;
    }

    function footer($Arr_Footer=false)
    {
		if ($Arr_Footer)
		{
    		$year = date('Y');
    		$footertext = sprintf($this->xfootertext, $year);
   			$this->SetY(-20);
    		$this->SetTextColor(0, 0, 0);
    		$this->SetFont($this->xfooterfont,'',$this->xfooterfontsize);
    		$this->Cell(0,8, $footertext,'T',1,'C');
		}

		return;
    }
*/
}

?>