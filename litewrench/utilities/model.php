<?php
//mw_parcel.php
//Parcel class file, defines basic parcel behaviour.
//Design by David Thomson at Hundredth Codemonkey.

/* DATA TYPES
string
integer
float
boolean
text
binary
datetime

*/



//Looking to add a couple of function for the retrieval of parcels.
// ->locked()	puts a query condition in for the lock level compared to the user requesting the data
// ->status()	puts in a query condition to match the status against the level of the user requesting the parcel.

//The current TODO list on this parcel.
//I'm just doing to archiving now and need to be able to identify the parcels which are to be archived.
//This routine must look up the archive variable locally on the parcel to indicate that is should be archived.


//Must also add a dfault document type to save data on the webpage for.


class MW_Utility_Model extends MW_System_Base
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Parcel meta data values.
	//*!*This is all going on th efollowing property $this->Arr_ParcelMetaInfo
	protected $Str_ParcelType 		= '';
	protected $Int_ParcelId			= '1';
	protected $Str_ParcelKey		= 'some-key';
	protected $Int_ParcelStatus		= 'open';
	protected $Hex_ParcelView		= 1;
	protected $Hex_ParcelEdit		= 1;

/* PARCEL FORM/FIELD PROPERTIES


I will create a bundle class which is the current parcel class.
The bundle does full ORM as per the initial framework spec.
The parcel class will be a single table class for lightweight application/applet/widget deployment.

NB: I'm confusing $this->Arr_TableSchema with properties labelled Arr_DataSchema elsewhere
the source of this is the function get_data_schema so I'm going to have to change one for the
sake of consistancy across the entire codebase

This file needs to be restructured a bit. function placement is not exactly representative of what they do.
Also, I think I'm going to fuck off those excessive API functions down the bottom.


//need to add a new function ->archive() which can go before/after save()
//and takes the data saved to the parcel and moves it to the archive.
//This needs to go into the module API nd hyandled in this class like the save().
//WIll also need to add the archive query in the right format to the driver, so those routines need to be modified.

//NB: there are three values n the table schema which affect how the installer indexes data on the table
// lookup is the schema property and the values can be one of the following:
//	key - unique index. This value can only exist once in the table schema
/	unique - another unique index but this can exist in more thna one field in the schema
//	index - this is an indexing of the field for table optimisation and can exist in more than one field
// *** this lookup property must be one of the three for each field, no field can have more than one lookup property.
// These notes will be moved to the system documentation but are residing here for now.

*/

	//Database table schema.
	protected $Arr_TableSchema		= array();	//Data schema for parcels.

	//Data archive setting.
	protected $Bol_ArchiveData		= false;	//Creates archive table on installation.

	//File storge format: dom(XML record document), xml(XML file), img(image file), txt(text string), bin(binary string).
	//*!*I don't think this txt here is needed, this has all become confused, must straighten out.
	protected $Str_FileFormat		= 'dom';	//File format for data storage.

	//Model descriptor variables.
	public $Str_ModelType			= '';	//Name of parent.
	public $Str_ModelName			= '';	//Name of class.

	//Link table column names.
	public $Arr_LinkColumns			= array(
		'link_id'					=> 'link_id',
		'link_type'					=> 'link_type',
		'link_time'					=> 'link_time',
		'parcel_id'					=> 'parcel_id',
		'model_type'				=> 'model_type',
		'model_name'				=> 'model_name',
		'model_id'					=> 'model_id');

	//Link table column formatts.
	public $Arr_LinkFormats			= array(
		'link_id'					=> 'integer',
		'link_type'					=> 'string',
		'link_time'					=> 'datetime',
		'parcel_id'					=> 'integer',
		'model_type'				=> 'string',
		'model_name'				=> 'string',
		'model_id'					=> 'integer');



///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties		= array(
		'Arr_ParcelQuery'			=> null,	//Parcel query properties
		'Arr_ParcelData'			=> array(),	//Parcel field data
		'Str_OperationsFlag'		=> '');		//Flag to tell the foreman object what it is saving/manipulating
		//*!*Don't think I need $this->Str_OperationsFlag



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Constructor defines basic properties.
	// * Return:					VOID
	public function __construct()
	{
		//Set model node and name.
		$this->Str_ModelType = strtolower(str_replace('MW_Utility_', '', get_parent_class($this)));
		$this->Str_ModelName = strtolower(str_replace('MW_Model_', '', get_class($this)));

		$Arr_ModelSchema = array(
			'id'				=> array(
				'column'		=> 'id',
				'format'		=> 'integer'),
			'view'				=> array(
				'column'		=> 'view',
				'format'		=> 'string'),
			'edit'				=> array(
				'column'		=> 'edit',
				'format'		=> 'string'),
			'lock'				=> array(
				'column'		=> 'lock',
				'format'		=> 'string'),
			'status'			=> array(
				'column'		=> 'status',
				'format'		=> 'string'),
			'enable'			=> array(
				'column'		=> 'enable',
				'format'		=> 'datetime'),
			'disable'			=> array(
				'column'		=> 'disable',
				'format'		=> 'datetime'),
			'created'			=> array(
				'column'		=> 'created',
				'format'		=> 'datetime'),
			'modified'			=> array(
				'column'		=> 'modified',
				'format'		=> 'datetime'));

		//Incorporate base parcel schema.
		foreach ($Arr_ModelSchema as $Str_FieldName => $Arr_MetaField)
		{
			$this->Arr_TableSchema[$Str_FieldName] = $Arr_MetaField;
		}

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//              F O R M   H A N D L I N G   F U N C T I O N S                //
///////////////////////////////////////////////////////////////////////////////

	//Sets field data values locally.
	// - $Arr_FieldValues:			Key/pair array of field values to set
	// * Return:					True if all values were in the table schema, otherwise false
	public function set_values($Arr_FieldValues)
	{
		$Bol_ValidFields = true;

		//Decouple local data values
		$Arr_NewParcelData = $this->Arr_ParcelData;

		//Get table schema fields
		$Arr_FieldNames = array_keys($this->Arr_TableSchema);

		//Add data values.
		foreach ($Arr_FieldValues as $Str_Field => $Str_Value)
		{
			//If the array key is invalid set return error.
			if (is_int($Str_Field) || (!in_array($Str_Field, $Arr_FieldNames)))
			{
				$Bol_ValidFields = false;
				continue;
			}

			$Arr_NewParcelData[$Str_Field] = $Str_Value;
		}

		//Recouple local data values
		$this->Arr_ParcelData = $Arr_NewParcelData;

		return $Bol_ValidFields;
	}

	//Unsets local field data values.
	// - $Arr_Fields:				Array of field names to unset values for
	// * Return:					True if all values were in the table schema, otherwise false
	public function unset_values($Arr_Fields)
	{
		$Bol_ValidFields = true;

		//Decouple local data values
		$Arr_NewParcelData = $this->Arr_ParcelData;

		//Add data values.
		foreach ($Arr_Fields as $Str_Field)
		{
			//If the array key is invalid set return error.
			if (!array_key_exists($Str_Field, $Arr_NewParcelData))
			{
				$Bol_ValidFields = false;
				continue;
			}

			$Arr_NewParcelData[$Str_Field] = null;
		}

		//Recouple local data values
		$this->Arr_ParcelData = $Arr_NewParcelData;

		return $Bol_ValidFields;
	}

	//Gets parcel data of the fieldnames $Mix_FieldNames.
	// - $Mix_FieldNames:		Single field name or an array of fieldnames, default = ''(all fields)
	// - $Bol_InSchema:			Tests to see if value is in the table schema, deafult = false(tests in parcel data)
	// * Return:				Data in database fields Mix_FieldNames
	// * NB: If a field name requested does not exist function will return false as a proxy has_field_names()
	public function data($Mix_FieldNames='', $Bol_InSchema=false)
	{
		if ($Mix_FieldNames)
		{
			//If the field names are an array get the values.
			if (is_array($Mix_FieldNames))
			{
				$Arr_FieldsParcelData = array();
				foreach ($Mix_FieldNames as $Str_FieldName)
				{
					//this needs to be tested in the parcel schema
					if (isset($this->Arr_ParcelData[$Str_FieldName]))
					{
						$Arr_FieldsParcelData[$Str_FieldName] = $this->Arr_ParcelData[$Str_FieldName];
					}
					else
					{
						if ($Bol_InSchema && isset($this->Arr_TableSchema[$Str_FieldName]))
						{
							$Arr_FieldsParcelData[$Str_FieldName] = '';
						}
						else
						{
							return false;
						}
					}
				}

				return $Arr_FieldsParcelData;
			}
			//Otherwise if the field is a string get the signle value.
			elseif (is_string($Mix_FieldNames))
			{
				if (isset($this->Arr_ParcelData[$Mix_FieldNames]))
				{
					return $this->Arr_ParcelData[$Mix_FieldNames];
				}
				else
				{
					if ($Bol_InSchema && isset($this->Arr_TableSchema[$Str_FieldName]))
					{
						return '';
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				global $CMD;
				$CMD->handle_exception('Model data field names not a string or array', 'MW:101');
			}
		}
		//Otherwise get all set values.
		else
		{
			if ($Bol_InSchema)
			{
				$Arr_FieldsParcelData = array();
				//*!*This should be getting array_keys.
				foreach ($this->Arr_TableSchema as $Str_FieldName => $Arr_SchemaFields)
				{
					//If the field exists get the value.
					if (isset($this->Arr_ParcelData[$Str_FieldName]))
					{
						$Arr_FieldsParcelData[$Str_FieldName] = $this->Arr_ParcelData[$Str_FieldName];
					}
					//Otherwise set value as false.
					else
					{
						$Arr_FieldsParcelData[$Str_FieldName] = false;
					}
				}

				return $Arr_FieldsParcelData;
			}
			else
			{
				return $this->Arr_ParcelData;
			}
		}

		return false;
	}


///////////////////////////////////////////////////////////////////////////////
//          P A R C E L   C O L L E C T I O N   F U N C T I O N S            //
///////////////////////////////////////////////////////////////////////////////

	//Gets parcel data table schema
	// - $Str_PropertyName: 	Schema field name, dafault = 0(all fields)
	// * Return:				Array of field $Str_FieldName data schema, default all field schemas.
	//*!*Might shorten the name of this function to data_schema()
	public function get_data_schema($Str_PropertyName=false)
	{
		if ($Str_PropertyName)
		{
			if (!isset($this->Arr_TableSchema[$Str_PropertyName]))
			{
				global $CMD;
				$CMD->handle_exception('Field '.$Str_PropertyName.' does not exist in model object', 'MW:101');
				return array();
			}
			else
			{
				return $this->Arr_TableSchema[$Str_PropertyName];
			}
		}

		return $this->Arr_TableSchema;
	}
	
	//Gets the field/column pairing for database queries.
	// - $Str_PropertyName: 	Schema field name, dafault = 0(all fields)
	// * Return:				Field/column pairing of table schema
	public function query_column($Str_PropertyName=false)
	{
		$Arr_SchemaColumn = array();

		foreach ($this->Arr_TableSchema as $Str_FieldName => $Arr_FieldProperty)
		{
			if ($Str_PropertyName === false || $Str_PropertyName == $Str_FieldName)
				$Arr_SchemaColumn[$Str_FieldName] = $Arr_FieldProperty['column'];
		}

		return $Arr_SchemaColumn;
	}

	//Gets field key/value format array for database queries.
	// - $Str_PropertyName: 	Schema field name, dafault = 0(all fields)
	// * Return:				Key/value array of parcel field names and column properties
	public function query_format($Str_PropertyName=false)
	{
		$Arr_SchemaFormat = array();

		foreach ($this->Arr_TableSchema as $Str_FieldName => $Arr_FieldProperty)
		{
			if ($Str_PropertyName === false || $Str_PropertyName == $Str_FieldName)
				$Arr_SchemaFormat[$Str_FieldName] = $Arr_FieldProperty['format'];
		}

		return $Arr_SchemaFormat;
	}

	//Formats a parcel data query results set into a field properties set.
	// - $Arr_QueryResults:			Query results to convert column keys to field name keys
	//*!*This is somewhat fucked for multiple field columns, will need to fix in the future.
	public function column_to_field_keys($Arr_QueryResults)
	{
		$Arr_FieldValues = array();

		if ($Arr_QueryResults)
		{
			foreach ($this->Arr_TableSchema as $Str_FieldName => $Arr_FieldProperty)
			{
				if (array_key_exists($Arr_FieldProperty['column'], $Arr_QueryResults))
					$Arr_FieldValues[$Str_FieldName] = $Arr_QueryResults[$Arr_FieldProperty['column']];
			}
		}

		return $Arr_FieldValues;
	}

/*
	//Loads storage lite data.
	// * Return:					False if not implementing storage lite for this parcel, otherwise parcel data
	// * NB: This function is to be overriden in individual parcel implementations, which should return true if db querying is not desired.
	// *!* There must be a way to refactor the majority of the code in the individule implementation
	//I'm getting stuck on PHP's class implementation not allowing me to call the child class name, will need to revisit...
	//Basically I need to pass the class name from the foreman into the load() method and then onto here.
	//Then I need to look up a property on the parcel implementation to find out the format of the file(EG: .html, .xml, etc)
	//*!*This is all pretty straight forward and I can come back to this very soon.
	protected function file_data()
	{
		return false;
	}
*/
	//Loads parcel data by id or key as set in __construct()
	// - $Str_Storage:				Storage type to use for parcel
	// * Return:					SELF
	public function load($Str_Storage)
	{
		global $CMD;

		//Additional load parameters.
		if (method_exists($this, 'load_prior'))
			$this->load_prior();

		//If the file storage configuration is set get file handling utility.
		if ($Str_Storage == 'file' && $this->file_data())
		{
			return $this;
		}

		//Get parcel reference condition.
		$Str_KeyField = $this->get_key_field();
		$Str_Reference = '';
		$Arr_Where = array();
		if (isset($this->Arr_ParcelData[$Str_KeyField]) && $this->Arr_ParcelData[$Str_KeyField])
		{
			$Arr_Where[] = array($Str_KeyField, 'eq', $this->Arr_ParcelData[$Str_KeyField]);
			$Str_Reference = $this->Arr_ParcelData[$Str_KeyField];
		}
		elseif (isset($this->Arr_ParcelData['id']) && $this->Arr_ParcelData['id'])
		{
			$Arr_Where[] = array('id', 'eq', $this->Arr_ParcelData['id']);
			$Str_Reference = $this->Arr_ParcelData['id'];
		}
		else
		{
			$Arr_Where[] = $CMD->handle_exception('No parcel KEY or ID supplied for load()', 'MW:101');
		}

		//Get parcel data from storage.
		$Arr_Query = array();
		$Arr_Query = array(
			'type' => 'select',
			'node' => $this->Str_ModelType,
			'name' => $this->Str_ModelName,
			'aspect' => 'data',
			'fields' => false,
			'offset' => 0,
			'range' => 1,
			'where' => $Arr_Where,
			'column' => $this->query_column(),
			'format' => $this->query_format());

		//If the parcel was not found handle excpetion.
		if (!$Arr_ParcelData = $CMD->query($Arr_Query))
		{
			$CMD->handle_exception('Parcel '.$this->Str_ModelName.' by reference '.$Str_Reference.' not found', 'MW:101');
			$Arr_ParcelData = array(array());
		}

		//*!*I'll need to run filters here.
		//Run data filters.

		//Set parcel data locally.
		$Arr_PropertyData = $this->column_to_field_keys($Arr_ParcelData[0]);
		$this->set_values($Arr_PropertyData);

		return $this;
	}

	//Saves parcel data.
	//  Return:					True on succes, otherwise false
	//*!*I think I'm trying to save parcels which are empty, no values, must check this, not seeing an error in log.
	//I believe this is the stuff I need to validate in the foreman object, which is why the API should be used
	//If the parcel doesn't have an id I need to set the created date.
	public function save()
	{
		$Bol_ParcelSaved = false;
		global $CMD;

		$Arr_ColumnData = array();
		$Arr_SaveCallbacks = array();

		//Set boolean values.
		//*!*Formatting parcel data completely or just doing booleans?
		//*!*I really should bash this block into it's own function for multi-page data transformations.
		$Arr_FormattedData = $this->Arr_ParcelData;
		foreach ($this->Arr_ParcelData as $Str_FieldName => $Mix_FieldValue)
		{
			//If the field is boolean and its value is an array evaluated it.
			if (($this->Arr_TableSchema[$Str_FieldName]['format'] == 'boolean') && (is_array($Mix_FieldValue)))
			{
				foreach ($Mix_FieldValue as $Mix_ValuePosition)
				{
					//If the value evaluates as true set field data as 1(boolean true)
					if ($Mix_ValuePosition)
					{
						$Arr_FormattedData[$Str_FieldName] = 1;
						break;
					}
				}
			}
		}

		//Add the created time.
		if (!isset($Arr_FormattedData['id']))
		{
			$Arr_FormattedData['created'] = date(MW_STR_FORMAT_DATETIME, $_SERVER['REQUEST_TIME']);
		}

		//Add save time.
		$Arr_FormattedData['modified'] = date(MW_STR_FORMAT_DATETIME, $_SERVER['REQUEST_TIME']);

		$Arr_Query = array(
			'type' => 'update',
			'node' => $this->Str_ModelType,
			'name' => $this->Str_ModelName,
			'aspect' => 'data',
			'fields' => $Arr_FormattedData,
			'column' => $this->query_column(),
			'format' => $this->query_format());

		//If the parcel has an id assigned update record.
		if (isset($Arr_FormattedData['id']) &&  $Arr_FormattedData['id'])
		{
			$Arr_Query['type'] = 'update';
			$Arr_Query['where'] = array(array($this->Arr_TableSchema['id']['column'], 'eq', $Arr_FormattedData['id']));
			$Arr_Query['offset'] = 1;
		}
		//Otherwise create a new database entry.
		else
		{
			$Arr_Query['type'] = 'insert';
		}
//var_dump($Arr_Query);exit;
		//Do pre-save handler.
		if (method_exists($this, 'save_prior'))
			$this->save_prior($Arr_Query);

		//Make data saving query.
		$Arr_ParcelData = $CMD->query($Arr_Query);

		//If there is no parcel id get it.
		if (!isset($Arr_FormattedData['id']) || !$Arr_FormattedData['id'])
		{
			$Arr_IdQuery = array(
				'type'		=> 'select',
				'node'		=> $this->Str_ModelType,
				'name'		=> $this->Str_ModelName,
				'aspect'	=> 'data',
				'fields'	=> array('id'),
				'where'		=> array(array('modified', 'eq', $Arr_FormattedData['modified'])),
				'order'		=> array('id', 'd'),
				'offset' 	=> 0,
				'range' 	=> 1,
				'column' => $this->query_column(),
				'format' => $this->query_format());

			//Set parcel data locally.
			$Arr_ParcelId = $CMD->query($Arr_IdQuery);

			if ($Arr_ParcelId)
			{
				$Arr_PropertyData = $this->column_to_field_keys($Arr_ParcelId[0]);
				$this->set_values($Arr_PropertyData);
			}
		}

		//Do post-save handler.
		if (method_exists($this, 'save_after'))
			$this->save_after($Arr_Query);

		return $Bol_ParcelSaved;
	}

	//Links parcel to model object by type, name and id.
	// - $Obj_Model:			Model object to create a link to
	// - $Int_ModelId:			Id of model to create a link to
	// * Return:				True on success, otherwise false
	public function link($Obj_Model, $Int_ModelId)
	{
		//Assemble link field data.
		$Arr_Fields = array('link_time'=>date(MW_STR_FORMAT_DATETIME, $_SERVER['REQUEST_TIME']),
						'parcel_id'=>$Int_ModelId,
						'model_type'=>$this->Str_ModelType,
						'model_name'=>$this->Str_ModelName,
						'model_id'=>$this->data('id'));

		//Construct link query.
		$Arr_Query = array(
			'type'		=> 'insert',
			'node'		=> $Obj_Model->Str_ModelType,
			'name'		=> $Obj_Model->Str_ModelName,
			'aspect'	=> 'link',
			'fields'	=> $Arr_Fields,
			'column'	=> $Obj_Model->Arr_LinkColumns,
			'format'	=> $Obj_Model->Arr_LinkFormats);

		//If the parcel was not linked handle excpetion.
		global $CMD;
		$this->Arr_ParcelQuery = $Arr_Query;
		if (!$Arr_ParcelData = $CMD->query($Arr_Query))
		{
			$CMD->handle_exception('Model '.$Obj_Model->Str_ModelType.' '.$Obj_Model->Str_ModelName.' with id '.$Int_ModelId.' could not be linked', 'MW:101');
			return false;
		}

		return true;
	}

	//Links the parcel to the model $Obj_Model with the ID $Int_ModelId
	// - $Obj_Model:			Model object to create a link to
	// - $Int_ModelId:			Id model to create a link to
	// * Return:				True on success, otherwise false
	public function unlink()
	{

	}




///////////////////////////////////////////////////////////////////////////////
//                     U T I L I T Y   F U N C T I O N S                     //
///////////////////////////////////////////////////////////////////////////////




///////////////////////////////////////////////////////////////////////////////
//         D A T A   T R A N S F O R M A T I O N   F U N C T I O N S         //
///////////////////////////////////////////////////////////////////////////////

	//Sets file data from upload field to corresponding data fields and parcel query.
	// - $Arr_Query:				Reference to the query array used to save parcel
	// - $Str_UploadField:			Name of data field to get upload inofrmation from
	// - $Str_UploadName:			name of field to store uploaded file name
	// - $Str_UploadType:			name of field to store uploaded file type
	// - $Str_UploadSize:			name of field to store uploaded file size
	// * Return:					VOID
	public function set_upload_data(&$Arr_Query, $Str_UploadField, $Str_UploadName, $Str_UploadType, $Str_UploadSize)
	{
		//If the file field data is an array of uploaded file information.
		if (isset($this->Arr_ParcelData[$Str_UploadField]) && is_array($this->Arr_ParcelData[$Str_UploadField]) && $this->Arr_ParcelData[$Str_UploadField])
		{
			//If the data file has the correct data format add additional file information.
			if (isset($this->Arr_ParcelData['file']['name']) && isset($this->Arr_ParcelData['file']['type']) && isset($this->Arr_ParcelData['file']['size']))
			{
				//Decouple parcel data.
				$Arr_NewParcelData = $this->Arr_ParcelData;

				$Arr_NewParcelData[$Str_UploadName] = $this->Arr_ParcelData['file']['name'];
				$Arr_NewParcelData[$Str_UploadType] = $this->Arr_ParcelData['file']['type'];
				$Arr_NewParcelData[$Str_UploadSize] = $this->Arr_ParcelData['file']['size'];

				//Recouple parcel data.
				$this->Arr_ParcelData = $Arr_NewParcelData;

				//Add fields to query.
				$Arr_Query['fields']['name'] = $this->Arr_ParcelData['file']['name'];
				$Arr_Query['fields']['type'] = $this->Arr_ParcelData['file']['type'];
				$Arr_Query['fields']['size'] = $this->Arr_ParcelData['file']['size'];
			}
			//Otherwise handle exception.
			else
			{
				global $CMD;
				$CMD->handle_exception('Saving file without correct upload properties', 'MW:101');
			}
		}

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////
//*!These aren't really core functions, I'm yet to come back to these and figure out what's happening here exactly.

	//Gets the name of the field indexed as the parcel's unique key.
	// * Return:				Parcel key field if set, otherwise false
	public function get_key_field()
	{
		//Find find the field with it's index defined as key.
		$Str_Key = false;
		foreach ($this->Arr_TableSchema as $Str_FieldName => $Arr_FieldProperties)
		{
			if (isset($Arr_FieldProperties['lookup']) && $Arr_FieldProperties['lookup'] == 'key')
			{
				//If the field is not a string handle exception.
				if ($Arr_FieldProperties['format'] != 'string')
				{
					global $CMD;
					$CMD->handle_exception('Parcel key field '.$Str_FieldName.' is not in string format', 'MW:101');
				}
				else
				{
					$Str_Key = $Str_FieldName;
				}

				break;
			}
		}

		return $Str_Key;
	}

	//Gets the name of the field holding binary data
	// * Return:				Parcel file field if it exists, otherwise false
	// * NB: If there are more than one binary fields the function will return false
	public function get_file_field()
	{
		//Find find the field with it's index defined as key.
		$Str_FileField = false;
		foreach ($this->Arr_TableSchema as $Str_FieldName => $Arr_FieldProperties)
		{
			//If the field is not a string handle exception.
			if ($Arr_FieldProperties['format'] == 'binary' && $Str_FileField != false)
			{
				global $CMD;
				$CMD->handle_exception('Parcel cannot have file field '.$Str_FieldName.', two or more binary fields exist', 'MW:101');
				return false;
			}
			elseif ($Arr_FieldProperties['format'] == 'binary')
			{
				$Str_FileField = $Str_FieldName;
			}
		}

		return $Str_FileField;
	}

	//Sets missing core parcel data fields to default values.
	// - $Arr_Data:				Parcel data to normalise system values into.
	// * Return:				Parcel data set with system default values added if not present
	public function normalise_data($Arr_Data)
	{
		global $CMD;

		//Set default access levels.
		$Arr_Data['view'] = (isset($Arr_Data['view']))? $Arr_Data['view']: $CMD->config('view');
		$Arr_Data['edit'] = (isset($Arr_Data['edit']))? $Arr_Data['edit']: $CMD->config('edit');
		$Arr_Data['lock'] = (isset($Arr_Data['lock']))? $Arr_Data['lock']: $CMD->config('lock');
		$Arr_Data['status'] = (isset($Arr_Data['status']))? $Arr_Data['status']: $CMD->config('status');

		//Set create and mondify times.
		$Obj_Time = $CMD->helper('time');
		$Arr_Data['created'] = (isset($Arr_Data['created']))? $Arr_Data['created']: $Obj_Time->timestamp_to_datetime();
		$Arr_Data['modified'] = (isset($Arr_Data['modified']))? $Arr_Data['modified']: $Obj_Time->timestamp_to_datetime();

		//Set enable and disable values.
		$Arr_Data['enable'] = (isset($Arr_Data['enable']))? $Arr_Data['enable']: NULL;
		$Arr_Data['disable'] = (isset($Arr_Data['disable']))? $Arr_Data['disable']: NULL;

		return $Arr_Data;
	}

/*!*Not sure about these guys
	public function set_id($Int_Id)
	{
		$this->Int_ParcelId = $Int_Id;
	}

	public function set_key($Str_Key)
	{
		$this->Str_ParcelKey = $Str_Key;
	}

	public function get_id()
	{

	}
*/
	//*!*Do I need this?
	public function get_meta_data($Obj_Database, $Str_ReferenceType, $Var_ParcelReference)
	{
		$Bol_ParcelFound = false;

		$Str_ParcelRequest = 'SELECT FROM '.MW_CONST_STR_PROPERTY_SET_PARCEL.' WHERE '.$Var_ReferenceType.' = '.$Var_ParcelReference.' LIMIT = 1;';
		$Arr_MetaData = $Obj_Database->parcel_request($Str_ParcelRequest);
/*
		//Set meta data locally.
		for ()
		{

		}
*/

		return $Bol_ParcelFound;
	}

	//*!*The view property will probably eventually be in an array,
	// so change routine on all following wrapper functions <--
	//Sets system status of the parcel.
	// - $Int_Status:			Parcel status constant MW_CONST_INT_PARCELSTATUS_*
	// * Return:				VOID
	public function set_status()
	{
		return;
	}

	public function set_version()
	{

	}

	//Gets the view access property of the parcel.
	// * Return:				View access permission of the parcel
	public function get_view_access()
	{
		return $this->Hex_ParcelView;
	}

	//Sets the view access of the parcel to $Hex_ViewAccess.
	// $Hex_ViewAccess:			View access value of the parcel
	// * Return:				VOID
	public function set_view_access()
	{
		return;
	}

	//Adds view access value $Hex_ViewAccess to parcel.
	// - $Hex_ViewAccess:		View access value to add to parcel
	// * Return:				VOID
	public function add_view_access()
	{
		return;
	}

	//Gets the edit access property of the parcel.
	// * Return:				Edit access permission of the parcel
	public function get_edit_access()
	{
		return $this->Hex_ParcelEdit;
	}

	//Sets the view access of the parcel to $Hex_ViewAccess.
	// $Hex_ViewAccess:			View access value of the parcel
	// * Return:				VOID
	public function set_edit_access()
	{
		return;
	}

	//Adds edit access value $Hex_EditAccess to parcel.
	// - $Hex_EditAccess:		Edit access value to add to parcel
	// * Return:				VOID
	public function add_edit_access()
	{
		return;
	}


///////////////////////////////////////////////////////////////////////////////
//                       F I L E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

	//This method gets the folder location of the parcel raw data file.
	// - $Str_ClassName:			Name of the parcel class to get it's associated file from
	// * Return:					Directory path of parcel file containing data
	public function file_path($Str_ClassName)
	{
		return MW_CONST_STR_DIR_INSTALL.'data/parcels/'.strtolower(str_replace('_', '/', str_replace('MW_Parcel_', '', $Str_ClassName))).'/';
	}

	//Sets the parcel record data from XML data file.
	// * Return:				True on success otherwise false
	// * NB: Return behaviour must be identical on child classes.
	//*!*I'll consolidate the comments of the previous function to this.
	//If the user wants to customsied the functionality they should implement parent::file_data()
	//checking the return value prior to the custom implementation, in most cases.
	//If a user wants to negate this function with optimisation they can simply implement to return false like old version
	public function file_data()
	{
		$Str_Sitemap = '';
		global $CMD;

		//Decouple parcel data.
		$Arr_NewParcelData = $this->Arr_ParcelData;

		//Set file data to parcel
		$Obj_File = $CMD->helper('file');
		$Obj_File->load_data_document($this->file_path(get_called_class()).$Arr_NewParcelData[$this->get_key_field()].'.xml', 'r');
		$Arr_NewParcelData = $Obj_File->get_data_record_from_file($this->get_key_field(), $Arr_NewParcelData[$this->get_key_field()]);

		//NB: in an xml document this get_data_record_from_file() function is not going to return the correct result
		//because the data will be a dom element. I should put file data as a comment not a content element

		//If the binary data field is empty check for a file.
		if ($this->get_file_field() && $this->Str_FileFormat && $this->Str_FileFormat != 'dom'
		&& (!isset($Arr_NewParcelData[$this->get_file_field()]) || !$Arr_NewParcelData[$this->get_file_field()]))
		{
			$Arr_NewParcelData[$this->get_file_field()] = $Obj_File->get_file_as_string($this->file_path(get_called_class()).'_bin/'.$Arr_NewParcelData[$this->get_key_field()].'.'.$this->Str_FileFormat, 'r');
		}

		if ($Arr_NewParcelData)
		{
			//Recouple parcel data.
			$Arr_NewParcelData = $this->normalise_data($Arr_NewParcelData);
			$this->Arr_ParcelData = $Arr_NewParcelData;
			return true;
		}

		return false;
	}

}

?>