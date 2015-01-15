<?php
//mw_helper.php
//Helper base class file.
//Design by David Thomson at Hundredth Codemonkey.

///////////////////////////////////////////////////////////////////////////////
//                        M O D U L E   C L A S S                            //
///////////////////////////////////////////////////////////////////////////////

class MW_Utility_Helper extends MW_System_Base
{

///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	// NB: These properties must be included into child classes to retain base functionality.
	protected $Arr_Properties	= array(
		'Arr_Callbacks'			=> array());//List of callback methods.

	//private $Arr_Callbacks			= array();	//List of callback functions.

///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////


	public function __construct(){}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
//                    U T I L I T Y   F U N C T I O N S                      //
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
//                      B U I L D   F U N C T I O N S                        //
///////////////////////////////////////////////////////////////////////////////

	//Adds a method to the callback registry.
	// - $Str_CallbackMethod:	Name of class method to trigger as a callback
	// - $Str_CallbackArgs:		Callback method arguments as a string separated by a pipe(|) character, default = ''(no arguments)
	// - $Bol_MoveCallback:		Removes all instances of the same callback method in callback stack, default = false(keep callbacks)
	// * Return:				VOID
	// * NB: The callbacks are triggered after a build event on a helper object.
	//*!*Will probably put callback parameters into this feature by extending the callback values to be arrays.
	public function add_callback($Str_CallbackMethod, $Str_CallbackArguments='', $Bol_MoveCallback=false)
	{
		//Decouple stack.
		$Arr_NewCallbacks = $this->Arr_Callbacks;
		
		//If callback is to be moved remove existing callbacks from stack.
		if ($Bol_MoveCallback && $Arr_NewCallbacks)
		{
			$Arr_ShuffledCallbacks = array();
			foreach ($Arr_NewCallbacks as $Int_ArrayKey => $Arr_Callback)
			{
				if (!array_key_exists($Str_CallbackMethod, $Arr_Callback))
					$Arr_ShuffledCallbacks[] = $Arr_Callback;
			}

			$Arr_NewCallbacks = $Arr_ShuffledCallbacks;
		}

		//Set callback locally.
		$Arr_CallbackArguments = explode('|', $Str_CallbackArguments);
		$Arr_NewCallbacks[] = array($Str_CallbackMethod => $Arr_CallbackArguments);
		
		//Recouple stack.
		$this->Arr_Callbacks = $Arr_NewCallbacks;

		return;
	}
	
	//Handles build functions applied to the helper object.
	// * Return:				VOID
	// * NB: This method must always return VOID
	public function call()
	{
		return;
	}

	//Executes callback functions set locally to $this->Arr_Callbacks.
	// * Return:				Callback result string.
	public function callback()
	{
		$Str_CallbackResult = '';

		//If there are callbacks execute them.
		if ($this->Arr_Callbacks && is_array($this->Arr_Callbacks))
		{
			foreach($this->Arr_Callbacks as $Arr_Callback)
			{
				if (is_array($Arr_Callback))
				{
					foreach($Arr_Callback as $Str_CallbackMethod => $Arr_CallbackArgs)
					{
						$Str_CallbackResult .= $this->$Str_CallbackMethod($Arr_CallbackArgs);
					}
				}
			}
		}
		
		//Clear callback stack.
		$this->Arr_Callbacks = array();

		return $Str_CallbackResult;
	}



///////////////////////////////////////////////////////////////////////////////
//        F I L E   A N D   D I R E C T O R Y   F U N C T I O N S            //
///////////////////////////////////////////////////////////////////////////////

	//Gets all immediate files in a directory.
	// - $Str_DirPath:				Directory path to retrieve file names from
	// * Return:					All child file names within $Str_DirPath as an array
	public function get_files_in_dir($Str_DirPath)
	{
		$Arr_FileNames = array();

		//Open directory.
		if ($Obj_DirHandle = opendir($Str_DirPath))
		{
			//Get each folder.
			while (false !== ($Str_File = readdir($Obj_DirHandle)))
			{
				if (!is_dir($Str_DirPath.$Str_File) && ($Str_File != 'Thumbs.db')
				&& ($Str_File != '.') && ($Str_File != '..'))
					$Arr_FileNames[] = $Str_File;
			}

			//Close directory.
			closedir($Obj_DirHandle);
		}

		return $Arr_FileNames;
	}

	//Gets all sub directories within a directory
	// - $Str_DirPath:				Directory path to retrieve folder names from
	// * Return:					All child folders names as an array
	public function get_folders_in_dir($Str_DirPath)
	{
		$Arr_FolderNames = array();

		//Open directory.
		if ($Obj_DirHandle = opendir($Str_DirPath))
		{
			//Get each folder.
			while (false !== ($Str_Folder = readdir($Obj_DirHandle)))
			{
				if (is_dir($Str_DirPath.$Str_Folder) && ($Str_Folder != '.') && ($Str_Folder != '..'))
					$Arr_FolderNames[] = $Str_Folder;
			}

			//Close directory.
			closedir($Obj_DirHandle);
		}

		return $Arr_FolderNames;
	}

	//Gets data fron a file as a string.
	// - $Str_FileName:			Name of file to be accessed as a string
	// - $Str_AccessMode:		File stream access mode, fopen()
	// * Return:				Parcel file string on success, otherwise false
	public function get_file_as_string($Str_FileName, $Str_AccessMode='r+')
	{
		$Str_FileData = '';

		//If file does not exist handle expection.
		if (!file_exists($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception("Could not read file. File $Str_FileName not found", 'MW:100');
			return false;
		}

		//If file is not readable handle exception.
		if (!is_readable($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception("Could not read file. File $Str_FileName could not be read", 'MW:100');
			return false;
		}

		$Res_FileHandle = fopen(str_replace('/', '\\', $Str_FileName), $Str_AccessMode);
		$Str_FileData = fread($Res_FileHandle, filesize($Str_FileName));
		fclose($Res_FileHandle);

		return $Str_FileData;
	}

	//Writes data to a file.
	// - $Str_FileData:			Data string to save to file
	// - $Str_FileName:			Name of file to be accessed to write data
	// - $Str_AccessMode:		File stream access mode, fopen()
	// - $Bol_VerifyFile:		Verifies that the file exists before writing
	// * Return:				True on success, otherwise false
	public function save_string_as_file($Str_FileData, $Str_FileName, $Str_AccessMode='w', $Bol_VerifyFile=false)
	{
		//If the file is not writeable handle error.
		if ($Bol_VerifyFile && !file_exists($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception("Could not write to file. File $Str_FileName does not exist", 'MW:100');
			return false;
		}

		//If the file is not writeable handle error.
		if (!is_writable($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception("Could not write to file. File $Str_FileName is not writable", 'MW:100');
			return false;
		}

		$Res_FileHandle = fopen($Str_FileName, $Str_AccessMode);
		if (!fwrite($Res_FileHandle, $Str_FileData))
		{
			global $CMD;
			$CMD->handle_exception("Save file $Str_FileName failed, could not write to file.", 'MW:100');
			return false;
		}

		fclose($Res_FileHandle);

		return true;
	}



///////////////////////////////////////////////////////////////////////////////
//                X M L   P A R S I N G   F U N C T I O N S                  //
///////////////////////////////////////////////////////////////////////////////

	//Wrapper function for DOMDocument->load().
	// - $Str_FileName:				XML file to retrieve as a DOMDocument object
	// - $Str_Encode:				Encoding for XML document parsing, default = 'utf-8'
	// - $Str_Version:				Version for XML document parsing, default = '1.0'
	// * Return:					DOMDocument object of the XML file or false on fail.
	public function load_dom_xml_file($Str_FileName, $Str_Encode='utf-8', $Str_Version='1.0')
	{
		//Check for the XML file extension.
		//$Bol_IsXmlFile = false;

		//Make sure XML file exists.
		if (!file_exists($Str_FileName))
		{
			global $CMD;
			$CMD->handle_exception("Could not find XML file: $Str_FileName! Check name and location of file.", 'MW:101');
			return false;
		}

		//Create and validate new dom object.
		$Obj_DOMXMLFile = new DOMDocument($Str_Version, $Str_Encode);
		$Obj_DOMXMLFile->validateOnParse = true;

		//If the file is an XML document load it directly from file.
		if (pathinfo($Str_FileName, PATHINFO_EXTENSION) == 'xml')
		{
			if ($Obj_DOMXMLFile->load($Str_FileName) === false)
			{
				global $CMD;
				$CMD->handle_exception("Error reading XML file: $Str_FileName! Check file uses valid XML.", 'MW:101');
				return false;
			}
		}
		//Otherwise get the file contents and load xml from a string.
		else
		{
			if ($Obj_DOMXMLFile->loadXML($this->get_file_as_string($Str_FileName)) === false)
			{
				global $CMD;
				$CMD->handle_exception("Error reading XML file: $Str_FileName! Check file uses valid XML.", 'MW:101');
				return false;
			}
		}

		return $Obj_DOMXMLFile;
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
	
	//Gets the text node of a DOm element.
	// - $Obj_Element:			DOM element being queried
	// * Return:				The text node object of $Obj_Element if ti exists, otherwise false
	public function get_field_text_node($Obj_Element)
	{
		$Obj_TextNode = false;

		$Arr_ElementChildren = $Obj_Element->childNodes;
		foreach ($Arr_ElementChildren as $Obj_ElementChild)
		{
			if ($Obj_ElementChild->nodeType == XML_TEXT_NODE)
			{
				$Obj_TextNode = $Obj_ElementChild;
				break;
			}
		}

		return $Obj_TextNode;
	}

	//Removes all child elements and text nodes from an XML element
	// - $Obj_Element:				DOM Element to remove child elements and textNodes
	// * Return:					DOM Element with child elements and text nodes removed
	//*!*I need to run some testing to see if other node types should be removed
	public function remove_children($Obj_Element)
	{
		//Need to do a for loop.
		$Arr_ElementChildren = $Obj_Element->childNodes;
		foreach ($Arr_ElementChildren as $Obj_ElementChild)
		{
			if (($Obj_ElementChild->nodeType == XML_ELEMENT_NODE)
			|| ($Obj_ElementChild->nodeType == XML_TEXT_NODE))
			{
				$Obj_Element->removeChild($Obj_ElementChild);
			}
		}

		return $Obj_Element;
	}

///////////////////////////////////////////////////////////////////////////////
//                      B U I L D   F U N C T I O N S                        //
///////////////////////////////////////////////////////////////////////////////


}

?>