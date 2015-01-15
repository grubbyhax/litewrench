<?php
//xls.php
//Excel helper class file

class MW_Helper_Xls extends MW_Utility_Helper
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	public $Obj_Doc		= false;
	public $Arr_Data	= false;
	public $Arr_Labels	= false;


///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Class constructor
	// * Return:					VOID
	// * NB: This helper uses the TCPDF library
	public function __construct()
	{
		//Get PHPExcel library files
		require_once(MW_CONST_STR_DIR_INSTALL.'libraries/phpexcel/classes/PHPExcel.php');

		// create new CSV document
		global $CMD;

		$this->Obj_Doc = new PHPExcel();
		$this->Obj_Doc->setActiveSheetIndex(0);
		$this->Obj_Doc->getActiveSheet()->getDefaultStyle()->getFont()->setName('Tahoma');

		return;
	}

	public function __destroy()
	{
		//Free up phpExcel memory allocation.
		$this->Obj_Doc->disconnectWorksheets();
		unset($this->Obj_Doc);
		$this->Obj_Doc = false;
	}

/*
	public function __call($Str_Method, $Arr_Arguments)
	{
		if (method_exists($this->Obj_Doc, $Str_Method))
		{
			return $this->Obj_Doc->$Str_Method(implode(',', $Arr_Arguments));
		}

	}
*/


///////////////////////////////////////////////////////////////////////////////
//                       O U T P U T   M E T H O D S                         //
///////////////////////////////////////////////////////////////////////////////


	public function sheet($Int_Index, $Str_Title=false)
	{
		if ($Int_Index > 0)
		{
			$Obj_Worksheet = $this->Obj_Doc->createSheet($Int_Index);
			$this->Obj_Doc->setActiveSheetIndex($Int_Index);
		}
		else
		{
			//*!*This should be get sheet by index and set to active. ATM quick hack.
			$Obj_Worksheet = $this->Obj_Doc->getActiveSheet();
		}

		if ($Str_Title)
		{
			$Obj_Worksheet->setTitle($Str_Title);
		}

		return $this;
	}

	public function output($Str_FileName, $Str_OutputType='xls')
	{
		//Get output by type.
		if ($Str_OutputType == 'xlsx')
		{
			//excel/PHPExcel/Writer/Excel2007.php
		    header('Content-Type: application/vnd.openXMLformats-officedocument.spreadsheetml.sheet');
		    header('Content-Disposition: attachment;filename="'.$Str_FileName.'.xlsx"');
		    header('Cache-Control: max-age=0');
		    $Obj_Writer = new PHPExcel_Writer_Excel2007($this->Obj_Doc);
		    $Obj_Writer->save('php://output');
		}
		else
		{
			//excel/PHPExcel/Writer/Excel5.php
	        header("Content-type: application/vnd.ms-excel");
	        header('Content-Disposition: attachment;filename="'.$Str_FileName.'.xls"');
	        header('Cache-Control: max-age=0');
	        $Obj_Writer = new PHPExcel_Writer_Excel5($this->Obj_Doc);
	        //$Obj_Writer->setTempDir(TMP); //*!*Do I need this in the other branch?
	        $Obj_Writer->save('php://output');
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
				$this->Obj_Doc->getProperties()->setCreator($Arr_MetaData['author']);
				$this->Obj_Doc->getProperties()->setLastModifiedBy($Arr_MetaData['author']);
			}

			if (isset($Arr_MetaData['title']))
			{
				$this->Obj_Doc->getProperties()->setTitle($Arr_MetaData['title']);
			}

			if (isset($Arr_MetaData['subject']))
			{
				$this->Obj_Doc->getProperties()->setSubject($Arr_MetaData['subject']);
			}

			if (isset($Arr_MetaData['keywords']))
			{
				$this->Obj_Doc->getProperties()->setKeywords($Arr_MetaData['keywords']);
			}

			if (isset($Arr_MetaData['description']))
			{
				$this->Obj_Doc->getProperties()->setDescription($Arr_MetaData['desciption']);
			}

			if (isset($Arr_MetaData['category']))
			{
				$this->Obj_Doc->getProperties()->setCategory("Test result file");
			}

		}

		return $this;
	}

	//*!*I don't really need this, but it is useful for doing more advanced work.
	public function document()
	{
		return $this->Obj_Doc;
	}

	// $Arr_Labels:				Replacement labels for the field names.
	public function header($Arr_Labels=false)
	{
		$this->Arr_Labels = $Arr_Labels;

        $Int_Column = 0;
        if ($Arr_Labels)
        {
	        foreach ($Arr_Labels as $Str_Field => $Str_Label)
			{
                $Str_Column = $Str_Label;
                $this->Obj_Doc->getActiveSheet()->setCellValueByColumnAndRow($Int_Column, 1, $Str_Column);
				$Int_Column++;
	        }
		}
		elseif (isset($this->Arr_Data[0]))
		{
	        foreach ($this->Arr_Data[0] as $Str_Field => $Str_Label)
			{
				$Int_Column++;
                $Str_Column = Inflector::humanize($Str_Field);
                $this->Obj_Doc->getActiveSheet()->setCellValueByColumnAndRow($Int_Column, 1, $Str_Column);
	        }
		}

		//Set the worksheet styles.
        $this->Obj_Doc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        //$this->Obj_Doc->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        //$this->Obj_Doc->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setRGB('ffffff');
        $this->Obj_Doc->getActiveSheet()->duplicateStyle( $this->Obj_Doc->getActiveSheet()->getStyle('A1'), 'B1:'.$this->Obj_Doc->getActiveSheet()->getHighestColumn().'1');

		for ($i = 1; $i <= $Int_Column; $i++)
		{
            $this->Obj_Doc->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
        }

		return $this;
	}

	//*!*This is function but the code should be cleaned up.
	public function data($Arr_Data)
	{
		//*!*Need to test if headers have been made.
        $i=2;
        foreach ($Arr_Data as $Arr_DataRow)
		{
            $j=0;
            foreach ($this->Arr_Labels as $Str_Field => $Str_Label)
			{
				$this->Obj_Doc->getActiveSheet()->setCellValueByColumnAndRow($j++, $i, $Arr_DataRow[$Str_Field]);
            }
            $i++;
        }

        return $this;
	}

	//*!*Tis is the poption to repeat the header or some oter info at th ebottom.
	public function footer($Bol_Labels=false)
	{
		return $this;
	}

}

?>