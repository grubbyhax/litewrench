<?php
//mw_xmlquery.php
//Developed by David Thomson & Daniel Thomson
//This file supplies basic SQL functionallity in XML.

//NB: I want to remove the read and save routines to the storage object.
//Also I want to set this object up with base object properties


//*!*Need to add exception handling into all these imported xml functions.

//*!*I have changed the structure of the XML record nodes to include the id and keys in the record and not as attributes
//of the record node. Consequently I must upade the eite and create function to reflect this change.

class MW_Helper_File extends MW_Utility_Helper
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	public $Obj_LoadedDocument	= NULL;
	public $Str_LoadedDocument	= '';
	public $Str_DocumentPath	= '';



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////
/*
These two functions need to be redone as this class is now a helper not a utility.
The __set needs to be moved over to a new function and any setting/getting in the functions of this  class need to be re-referenced.
The function in this class are yet to be fully redacted from it's initial use many years ago with xml databasing.
	//Constructor.
	// * Return:					VOID
	public function __construct()
	{
		//*!* This needs to be moved.
		$this->Str_DocumentPath = MW_CONST_STR_DIR_INSTALL.'data/';
		//$this->load_dom_xml_file($Str_DataFileName);
	}

	//Sets variable locally by outside object.
	// * Return:					VOID
	public function __set($Var_Name, $Var_Value)
	{
		$this->$Var_Name = $Var_Value;
	}
*/


///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////


// MOVED FROM THE FREMAN
//I'm going to need to reorganise this functionally... Not sure.
	//Gets parcel data on file of parcel type $Str_ParcelType with the key $Str_ParcelKey.
	// - $Str_FileName:			Name of file to be accessed as a string
	// - $Str_AccessMode:		File stream access mode, fopen()
	// * Return:				Parcel file string on success, otherwise false
	//*!*I'm now testing that this function has been moved to the helper
	public function get_file_as_string($Str_FileName, $Str_AccessMode='r+')
	{
		$Str_FileData = '';

		//If file does not exist handle expection.
		if (!file_exists($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception('Could not read file. File '.$Str_FileName.' not found', 'MW:100');
			return false;
		}

		//If file is not readable handle exception.
		if (!is_readable($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception('Could not read file. File '.$Str_FileName.' could not be read', 'MW:100');
			return false;
		}

		$Res_FileHandle = fopen(path_request($Str_FileName), $Str_AccessMode);
		$Str_FileData = fread($Res_FileHandle, filesize($Str_FileName));
		fclose($Res_FileHandle);

		return $Str_FileData;
	}

	//Formatts and saves parcel data $Mix_ParcelData to file.
	// - $Str_DataFile:			Data string to save to file
	// - $Str_FileName:			Name of file to be accessed to write data
	// - $Str_AccessMode:		File stream access mode, fopen()
	// - $Bol_VerifyFile:		Verifies that the file exists before writing
	// * Return true on success, otherwise false
	//*!*I'm now testing that this function has been moved to the helper
	public function save_string_as_file($Str_DataFile, $Str_FileName, $Str_AccessMode='w', $Bol_VerifyFile=false)
	{
		//If the file is not writeable handle error.
		if ($Bol_VerifyFile && !file_exists($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception('Could not write to file. File '.$Str_FileName.' does not exist', 'MW:100');
			return false;
		}

		//If the file is not writeable handle error.
		if (!is_writable($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception('Could not write to file. File '.$Str_FileName.' is not writable', 'MW:100');
			return false;
		}

		$Res_FileHandle = fopen($Str_FileName, $Str_AccessMode);
		if (!fwrite($Res_FileHandle, $Str_FileData))
		{
			$this->handle_exception('Save file '.$Str_FileName.' failed.', 'MW:100');
		}

		fclose($Res_FileHandle);

		return true;
	}

	//Gets all immediate files in a directory.
	// - $Str_DirPath:				Directory path to retrieve file names from
	// * Return:					All child file names within $Str_DirPath as an array
	public function get_file_names_in_dir($Str_DirPath)
	{
		$Arr_FileNames = array();

		//Open directory.
		if ($Obj_DirHandle = opendir($Str_DirPath))
		{
			//Get each folder.
			while (false !== ($Str_File = readdir($Obj_DirHandle)))
			{
				if (!is_dir($Str_DirPath.$Str_File) && ($Str_File != 'Thumbs.db'))
					$Arr_FileNames[] = $Str_File;
			}

			//Close directory.
			closedir($Obj_DirHandle);
		}

		return $Arr_FileNames;
	}

//*!*I need to move some of the routines over from the foreman
//These routines in the forem are currrently being extensively used thorughout the codeabse
//so care should be taken to make sure that those issues are squashed







	//Gets xml file and creates dom document for future processing.
	// - $Str_FileName:			Name of the file to open as a DOM Document
	// * Return:				VOID, document is stored locally
	public function load_data_document($Str_FileName)
	{
		$this->Str_LoadedDocument = $Str_FileName;
		$this->Obj_LoadedDocument = $this->load_dom_xml_file($this->Str_DocumentPath.$Str_FileName);
	}

	//*!*This function depreciates the two below it.
	public function get_record_element_by_value($Str_Field, $Str_Value)
	{
		//Declare return variable.
		$Obj_RecordNode = NULL;

		//Get each record node.
		$Arr_FieldElements = $this->Obj_LoadedDocument->getElementsByTagName($Str_Field);

		foreach ($Arr_FieldElements as $Obj_FieldElement)
		{
			//Look for an id match.
			if ($this->get_field_text_node($Obj_FieldElement)->nodeValue == $Str_Value)
			{
				$Obj_RecordNode = $Obj_FieldElement->parentNode;
				break;
			}
		}

		return $Obj_RecordNode;
	}

	//Gets record object from loaded document by its id attribute.
	// - $Int_RecordId:			Id of record to acquire
	// * Return:				Document record element
	public function get_record_element_by_id($Int_RecordId)
	{
		//Declare return variable.
		$Obj_RecordNode = NULL;

		//Get each record node.
		$Arr_DataRecords = $this->Obj_LoadedDocument->getElementsByTagName('record');
		foreach ($Arr_DataRecords as $Obj_DataRecord)
		{
			//Look for an id match.
			if ($Obj_DataRecord->getAttribute('id') == $Int_RecordId)
			{
				$Obj_RecordNode = $Obj_DataRecord;
				break;
			}
		}

		return $Obj_RecordNode;
	}

	//Gets record object from loaded document by its key attribute.
	// - $Str_RecordKey:		Key of record to acquire
	// * Return:				Document record element
	public function get_record_element_by_key($Str_RecordKey)
	{
		//Declare return variable.
		$Obj_RecordNode = NULL;

		//Get each record node.
		$Arr_DataRecords = $this->Obj_LoadedDocument->getElementsByTagName('record');
		foreach ($Arr_DataRecords as $Obj_DataRecord)
		{
			//Look for an id match.
			if ($Obj_DataRecord->getAttribute('key') == $Str_RecordKey)
			{
				$Obj_RecordNode = $Obj_DataRecord;
				break;
			}
		}

		return $Obj_RecordNode;
	}

	//Gets the tree within a DOM element as a string.
	// - $Obj_Element:			Element whose content is being retrieved
	// * Return:				Text content of $Obj_Element's full tree as a string
	// * NB: If $Obj_Element is a null object then an empty string isreturned.
	public function get_element_content_as_string($Obj_Element)
	{
		//If there is no element return an empty string.
		if (!$Obj_Element)
			return '';

		//Import element into new document.
		$Obj_HolderDocument = new DOMDocument();
		$Obj_HolderDocument->loadXML('<holder></holder>');
		$Obj_DocumentElement = $Obj_HolderDocument->documentElement;
		$Obj_ImportedElement = $Obj_HolderDocument->importNode($Obj_Element, true);
		$Obj_DocumentElement->appendChild($Obj_ImportedElement);

		//Convert document to string.
		$Str_HolderNode = $Obj_HolderDocument->saveXML($Obj_ImportedElement);

		//Cut content from element string.
		$Str_OpenTagLength = strpos($Str_HolderNode, '>') + 1;
		$Str_CloseTagLength = strrpos($Str_HolderNode, '<');
		$Str_DataLength = $Str_CloseTagLength - $Str_OpenTagLength;

		$Str_Element = substr($Str_HolderNode, $Str_OpenTagLength, $Str_DataLength);

		return $Str_Element;
	}

	//Gets the text node of a data field element.
	// - $Obj_DataField:		Data field element being queried
	// * Return:				The text node object of $Obj_DataField
	public function get_field_text_node($Obj_DataField)
	{
		//Declare return variable.
		$Obj_TextNode = NULL;
		
		//Get data field text node.
		$Arr_DataFieldChildren = $Obj_DataField->childNodes;
		foreach ($Arr_DataFieldChildren as $Obj_DataFieldChild)
		{
			if ($Obj_DataFieldChild->nodeType == XML_TEXT_NODE)
			{
				$Obj_TextNode = $Obj_DataFieldChild;
				break;
			}
		}

		return $Obj_TextNode;
	}


///////////////////////////////////////////////////////////////////////////////
//      X M L   D A T A   M A N I P U L A T I O N   F U N C T I O N S        //
///////////////////////////////////////////////////////////////////////////////

	//Gets all data records from a file and returns them as an id indexed array.
	// * Return:				Multidimensional array of data fields indexed to record id
	public function get_all_records_from_file()
	{
		//Declare return variable.
		$Arr_DataTableRecords = array();

		//Get data element nodes.
		$Arr_DataRecords = $this->Obj_LoadedDocument->documentElement->childNodes;
		foreach ($Arr_DataRecords as $Obj_DataRecord)
		{
			//If the child is a record node get its field values.
			if (($Obj_DataRecord->nodeType == XML_ELEMENT_NODE) && ($Obj_DataRecord->nodeName == 'record'))
			{
				//Get record id.
				$Int_RecordId = $Obj_DataRecord->getAttribute('id');

				//Get record field values.
				$Arr_DataRecordChildren = $Obj_DataRecord->childNodes;
				foreach ($Arr_DataRecordChildren as $Obj_DataRecordChild)
				{
					if ($Obj_DataRecordChild->nodeType == XML_ELEMENT_NODE)
					{
						$Str_FieldName = $Obj_DataRecordChild->nodeName;

						//Get data text node.
						$Str_FieldNode  = $this->get_field_text_node($Obj_DataRecordChild);
						$Str_FieldValue = $this->get_element_content_as_string($Obj_DataRecordChild);

						$Arr_DataTableRecords[$Int_RecordId][$Str_FieldName] = $Str_FieldValue;
					}
				}
			}
		}

		return $Arr_DataTableRecords;
	}

	//Gets every value of a data field from file as an array indexed to each record's id.
	// - $Str_FieldName			Name of field data is being collected from
	// * Retrun:				Array of feild values indexed to each field's id
	public function get_data_field_set_from_file($Str_FieldName)
	{
		//Declare return variable.
		$Arr_DataFeildSet = array();

		//Get each data field node.
		$Arr_DataFieldNodes = $this->Obj_LoadedDocument->getElementsByTagName($Str_FieldName);
		foreach ($Arr_DataFieldNodes as $Obj_DataFieldNode)
		{
			//If record has an id attribute add it to the list.
			if ($Obj_DataFieldNode->parentNode->hasAttribute('id'))
			{
				$Str_RecordId = $Obj_DataFieldNode->parentNode->getAttribute('id');
				$Arr_DataFeildSet[$Str_RecordId] = $this->get_element_content_as_string($Obj_DataFieldNode);
			}
		}

		return $Arr_DataFeildSet;
	}

	//Gets a data row in a file as an array indexed by field name.
	// - $Mix_Reference:		Unique identifier of the record being sort.
	// * Return:				Array of values indexed by field names.
	public function get_data_record_from_file($Str_IdentifyingField, $Mix_Reference)
	{
		//Declare return variable.
		$Arr_DataRecordSet = array();

/*
		//Get record element by id.
		if (is_int($Mix_Reference))
		{
			$Obj_DataRecord = $this->get_record_element_by_id($Mix_Reference);
		}
		//Get record element by key.
		elseif (is_string($Mix_Reference))
		{
			$Obj_DataRecord = $this->get_record_element_by_key($Mix_Reference);
		}
		//Handle incorrect parameter exception.
		else
		{
			global $CMD;
			$CMD->handle_exception('Record id or key supplied is not an integer or string', 'MW:101');
		}
*/

		$Obj_DataRecord = $this->get_record_element_by_value($Str_IdentifyingField, $Mix_Reference);

		//Add reference values.
		//$Arr_DataRecordSet['id'] = $Obj_DataRecord->getAttribute('id');

		//Get data element nodes.
		$Arr_DataRecordChildren = $Obj_DataRecord->childNodes;
		foreach ($Arr_DataRecordChildren as $Obj_DataRecordChild)
		{
			//If this child is an element mine its value.
			if ($Obj_DataRecordChild->nodeType == XML_ELEMENT_NODE)
			{
				$Str_FieldName = $Obj_DataRecordChild->nodeName;

				//Get data text node.
				$Obj_DataNode = $this->get_field_text_node($Obj_DataRecordChild);
				$Arr_DataRecordSet[$Str_FieldName] = $this->get_element_content_as_string($Obj_DataRecordChild);
			}
		}

		return $Arr_DataRecordSet;
	}

	//Gets data value from a record field in a file
	// - $Int_RecordId:				Record identifier data is being retrieved from
	// - $Str_FieldName:			Name of data field value is being acquired from
	// * Return:					Data as a text node value
	public function get_data_value_from_file($Int_RecordId, $Str_FieldName)
	{
		//Declae return variable.
		$Str_DataValue = '';

		//Get record element.
		$Obj_DataRecord = $this->get_record_element_by_id($Int_RecordId);

		//Get data field element.
		$Obj_DataField = $Obj_DataRecord->getElementsByTagName($Str_FieldName)->item(0);
		$Str_DataValue = $this->get_element_content_as_string($Obj_DataField);

		return $Str_DataValue;
	}

	//Edits a data value of field $Str_DataField in record $Int_RecordId in the current loaded document.
	// - $Int_RecordId:			Identifier of the record being edited
	// - $Str_FieldName:		Data field of the record being edited
	// - $Str_EditValue:		New data value of the edited field
	// * Return:				VOID
	public function edit_data_value_in_file($Int_RecordId, $Str_FieldName, $Str_EditValue)
	{
		//Get record element.
		$Obj_DataRecord = $this->get_record_element_by_id($Int_RecordId);

		//Get data field element.
		$Obj_DataField = $Obj_DataRecord->getElementsByTagName($Str_FieldName)->item(0);

		//Get data text node.
		$Obj_DataNode = $this->get_field_text_node($Obj_DataField);
		
		//Replace current data node with new value.
		$Obj_NewDataNode = $this->Obj_LoadedDocument->createTextNode($Str_EditValue);
		$Obj_DataNode->parentNode->replaceChild($Obj_NewDataNode, $Obj_DataNode);
		
		//Save document.
		$this->Obj_LoadedDocument->save($this->Str_DocumentPath.$this->Str_LoadedDocument.'.xml');

		return;
	}

	//*!*This function is pretty useless!
	//Removes $Obj_Element node from its DOM tree.
	// - $Obj_Element:			Element to remove from parent element
	// * Return:				VOID
	public function remove_element_node($Obj_Element)
	{
		$Obj_Parent = $Obj_Element->parentNode;
		$Obj_Parent->removeChild($Obj_Element);
		
		return;
	}

	//Replaces a data record with the values of $Arr_DataRecord.
	// - $Arr_DataRecord:			Array of record values to store
	// * Return:					VOID
	// * NB: $Arr_DataRecord requires an 'id' value to work.
	public function replace_data_record_on_file($Arr_DataRecord)
	{
		//Get record element.
		$Obj_DataRecord = $this->get_record_element_by_id($Arr_DataRecord['id']);

		//Remove existing fields.
		$Arr_RecordFields = $Obj_DataRecord->getElementsByTagName("*");
		$Int_ElementNodes = true;
		while ($Int_ElementNodes)
		{
			foreach ($Arr_RecordFields as $Obj_RecordField)
			{
				$this->remove_element_node($Obj_RecordField);
			}

			if (!$Obj_DataRecord->getElementsByTagName("*")->item(0))
				$Int_ElementNodes = false;
		}

		//Add field values.
		foreach ($Arr_DataRecord as $Str_Field => $Str_Value)
		{
			if ($Str_Field != 'id')
			{
				//Add description field.
				if ($Str_Field == 'description')
				{
					$Obj_DescriptionDoc = new DOMDocument();
					$Obj_DescriptionDoc->loadXML('<description>'.$Str_Value.'</description>');
					$Obj_DescriptionRoot = $Obj_DescriptionDoc->documentElement;

					$Obj_ImportedDescription = $this->Obj_LoadedDocument->importNode($Obj_DescriptionRoot, true);
					$Obj_DataRecord->appendChild($Obj_ImportedDescription);
				}
				//Add other fields.
				else
				{
					$Obj_NewDataField = $this->Obj_LoadedDocument->createElement($Str_Field, $Str_Value);
					$Obj_DataRecord->appendChild($Obj_NewDataField);
				}
			}
		}

		//Save document.
		$this->Obj_LoadedDocument->save($this->Str_DocumentPath.$this->Str_LoadedDocument.'.xml');
	}

	//Adds a record to the data file
	// - $Arr_DataRecord:			Key/value array of record field values.
	public function add_data_record_to_file($Arr_DataRecord)
	{
		//Get last record key.
		$Arr_RecordEntries = $this->Obj_LoadedDocument->getElementsByTagName('record');

		$Int_LastRecord = 0;
		foreach ($Arr_RecordEntries as $Obj_RecordEntry)
		{
			$Int_CheckedRecordId;
			if ($Obj_RecordEntry->hasAttribute('id'))
			{
				$Int_CheckedRecordId = $Obj_RecordEntry->getAttribute('id');
				$Int_LastRecord = ($Int_CheckedRecordId > $Int_LastRecord)? $Int_CheckedRecordId: $Int_LastRecord;
			}
		}

		//Add record element.
		$Obj_NewDataRecord = $this->Obj_LoadedDocument->createElement('record');
		$Obj_NewDataRecord->setAttribute('id', $Int_LastRecord + 1);
		$Obj_DocumentElement = $this->Obj_LoadedDocument->documentElement;
		$Obj_DocumentElement->appendChild($Obj_NewDataRecord);

		//Add field values.
		foreach ($Arr_DataRecord as $Str_Field => $Str_Value)
		{
			//Add description field.
			if ($Str_Field == 'description')
			{
				$Obj_DescriptionDoc = new DOMDocument();
				$Obj_DescriptionDoc->loadXML('<description>'.$Str_Value.'</description>');
				$Obj_DescriptionRoot = $Obj_DescriptionDoc->documentElement;
				
				$Obj_ImportedDescription = $this->Obj_LoadedDocument->importNode($Obj_DescriptionRoot, true);
				$Obj_NewDataRecord->appendChild($Obj_ImportedDescription);
			}
			//Add other fields.
			else
			{
				$Obj_NewDataField = $this->Obj_LoadedDocument->createElement($Str_Field, $Str_Value);
				$Obj_NewDataRecord->appendChild($Obj_NewDataField);
			}
		}
		
		//Save document.
		$this->Obj_LoadedDocument->save($this->Str_DocumentPath.$this->Str_LoadedDocument.'.xml');

		return;
	}

	//Removes a data record from document currently loaded locally.
	// - $Int_RecordId:			Unique identifier of record being removed.
	// * Return:				VOID
	//*!* I'm getting a nullvalue from the data management area, need to traceand test.
	public function remove_data_record_from_file($Int_RecordId)
	{
		//Get record element.
		$Obj_DataRecord = $this->get_record_element_by_id($Int_RecordId);

		//Remove record.
		$Obj_DocumentElement = $this->Obj_LoadedDocument->documentElement;
		$Obj_DocumentElement->removeChild($Obj_DataRecord);

		//Save document.
		$this->Obj_LoadedDocument->save($this->Str_DocumentPath.$this->Str_LoadedDocument.'.xml');

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//          S T O R A G E   L I T E   F I L E   F U N C T I O N S            //
///////////////////////////////////////////////////////////////////////////////







///////////////////////////////////////////////////////////////////////////////
//             I M A G E   H A N D L I N G   F U N C T I O N S               //
///////////////////////////////////////////////////////////////////////////////

	//Opens image resource of any type from file $Str_FileName.
	// - $Str_FileName:				File name of the image to open
	// * Return:					Image resource handle and properties as an array if valid, otherwise false
	public function open_image($Str_FileName)
	{
		$Arr_Image = false;

	    //If GD extension is not loaded handle exception
	    if (!extension_loaded('gd') && !extension_loaded('gd2'))
	    {
			global $CMD;
			$CMD->handle_exception('Could not open image, GD is not loaded', 'MW:101');
        	return false;
	    }

		if (($Arr_Properties = getimagesize($Str_FileName)) !== false)
		{
			$Arr_Image = array('width' => $Arr_Properties[0],
								'height' => $Arr_Properties[1],
								'mime' => $Arr_Properties['mime']);

			switch ($Arr_Image['mime'])
			{
				//case 'image/jpg':
					//$Arr_PathInfo = pathinfo($Str_FileName);
					//$Str_FileName = path_request($Arr_PathInfo['dirname'].'/'.$Arr_PathInfo['filename'].'.jpeg');
					//$Arr_Image['handle'] = imagecreatefromjpeg($Str_FileName); break;
				case 'image/jpeg': $Arr_Image['handle'] = imagecreatefromjpeg($Str_FileName); break;
				case 'image/png': $Arr_Image['handle'] = imagecreatefrompng($Str_FileName); break;
				case 'image/gif': $Arr_Image['handle'] = imagecreatefromgif($Str_FileName); break;
				default: global $CMD;
					$CMD->handle_exception('Image file does not have a valid mime type.', 'MW:101');
			}
		}

		return $Arr_Image;
	}

	//Resizes an image file for display in the gallery.
	// - $Str_Image:				Image resource path to be resized
	// - $Int_MaxWidth:				Maximum width to resize the image, default = false(no max width)
	// - $Int_MaxHeight:			Maximum height to resize the image, default = false(no max height)
	// * Return:					Height and width dimensions of the resized image, false on fail
	//This should not return values for w/h it should put properties onto the the model obejct
	public function resize_image($Str_Image, $Int_MaxWidth=false, $Int_MaxHeight=false)
	{
		//If the file cannont be opened
		if (($Arr_Image = $this->open_image($Str_Image)) === false)
		{
			return false;
		}

		$Res_ResizedImage = null;
		$Int_ResizeWidth = $Arr_Image['width'];
		$Int_ResizeHeight = $Arr_Image['height'];

		//Resize to max height.
		if (($Int_MaxHeight) && ($Int_ResizeHeight > $Int_MaxHeight))
		{
			$Int_ResizeWidth = round($Int_ResizeWidth * ($Int_MaxHeight / $Int_ResizeHeight));
			$Int_ResizeHeight = round($Int_ResizeHeight * ($Int_MaxHeight / $Int_ResizeHeight));
		}

		//Resize to max width.
		if (($Int_MaxWidth) && ($Int_ResizeWidth > $Int_MaxWidth))
		{
			$Int_ResizeHeight = round($Int_ResizeHeight * ($Int_MaxWidth / $Int_ResizeWidth));
			$Int_ResizeWidth = round($Int_ResizeWidth * ($Int_MaxWidth / $Int_ResizeWidth));
		}

		//Get new height.
		if ($Arr_Image['width'] > $Int_MaxWidth)
			$Int_ResizeHeight = round($Arr_Image['height'] * ($Int_ResizeWidth / $Arr_Image['width']));

		$Res_ResizedImage = imagecreatetruecolor($Int_ResizeWidth, $Int_ResizeHeight);

	    //Set image transparency.
    	if (($Arr_Image['mime'] == 'image/png') || ($Arr_Image['mime'] == 'image/gif'))
		{
        	imagealphablending($Res_ResizedImage, false);
        	imagesavealpha($Res_ResizedImage, true);
        	$Int_Transparency = imagecolorallocatealpha($Res_ResizedImage, 255, 255, 255, 127);
        	imagefilledrectangle($Res_ResizedImage, 0, 0, $Int_ResizeWidth, $Int_ResizeHeight, $Int_Transparency);
		}

		//Create resized image.
		imagecopyresampled($Res_ResizedImage, $Arr_Image['handle'], 0, 0, 0, 0, $Int_ResizeWidth, $Int_ResizeHeight, $Arr_Image['width'], $Arr_Image['height']);

		//Save by file type.
		$Bol_ImageSaved = false;
		switch ($Arr_Image['mime'])
		{
			//case 'image/jpeg': $Arr_PathInfo = pathinfo($Str_Image);
				//$Str_Image = path_request($Arr_PathInfo['dirname'].'/'.$Arr_PathInfo['filename'].'.jpeg');
				//$Bol_ImageSaved = imagejpeg($Res_ResizedImage, $Str_Image); break;
			case 'image/jpeg': $Bol_ImageSaved = imagejpeg($Res_ResizedImage, $Str_Image); break;
			case 'image/png': $Bol_ImageSaved = imagepng($Res_ResizedImage, $Str_Image); break;
			case 'image/gif': $Bol_ImageSaved = imagegif($Res_ResizedImage, $Str_Image); break;
			default: global $CMD;
				$CMD->handle_exception('Image file '.$Str_Image.' could not be saved after resize', 'MW:101');

		}

		//If image was saved return the new dimensions.
		if ($Bol_ImageSaved)
		{
			$Arr_ResizeDimensions = array('width'=>$Int_ResizeWidth, 'height'=>$Int_ResizeHeight);

			return $Arr_ResizeDimensions;
		}

		return false;
	}

/*
This is hee just for reference it needs to be gotten rid of when routines are in palce.
//Uploads image file to specified folder.
// - $Str_FileName:				Name of file with extension
// - $Str_DirPath:				Directory path to upload file to
// - $Str_FileVar:				Name of file form input
// * Return:					Name of uploaded file if successful, otherwise boolean false
function UploadPhotoFile($Str_DirPath, $Str_FileVar)
{
	$Str_DestinationDir = CONST_STR_DIR_IMAGES.$Str_DirPath;

	//Exit if file is invalid.
	if ($_FILES[$Str_FileVar]['name'] == '')
		return '';

	//Get file type.
	$Arr_FileInfo = explode('.', basename($_FILES[$Str_FileVar]['name']));

	//Get new name for file.
	$Str_UploadedFileName = '';
	$Arr_UploadedPhotos = $this->get_files_in_dir($Str_DestinationDir);
	$Int_UploadedPhotoKey = 1000;
	foreach ($Arr_UploadedPhotos as $Str_UploadedPhoto)
	{
		$Int_UploadedPhotoKey = ($Str_UploadedPhoto > $Int_UploadedPhotoKey)? $Str_UploadedPhoto: $Int_UploadedPhotoKey;
	}

	$Int_UploadedFileName = $Int_UploadedPhotoKey + 1;
	$Str_UploadedFileName = strtolower($Int_UploadedFileName.'.'.$Arr_FileInfo[1]);

	//Move file to folder.
	if (move_uploaded_file($_FILES[$Str_FileVar]['tmp_name'], $Str_DestinationDir.$Str_UploadedFileName))
	{
		//Resize image once uploaded.
		ResizeImage($Str_DestinationDir.$Str_UploadedFileName);

		return $Str_UploadedFileName;
	}
	else return '';
}
*/

}

?>