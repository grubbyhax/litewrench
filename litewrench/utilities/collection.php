<?php
//collection.php
//Collection class file.
//Design by David Thomson at Hundredth Codemonkey.


/*
*!*Important, I might change this object to a query object.
I'm even thinking of stuffing these routines back into the parcel(+others) object, do an API like this
//This basicall creates a query building object. $this->collection is handled by the module object to initialise some of the paramaters of the query.
$Arr_Results = $this->collection($this->parcel('name'))->data(array('field', 'field'))->limit(1)->order('field')->where($Arr_Where)->fetch();


*!*Design note: I'm hacking out these functions straight to SQL without using a driver or the storage object for version one.
I just need to get this fucker up and running for now.

Collections can be called three ways, all these calls return a collection object..
1. directly from the foreman object:
$CMD->utility('collection')->type('modeltype', 'modelname');

2. Through the module object:
$this->collection('modeltype', 'modelname');

3. With a model object:
$Obj_Parcel->collection('modeltype', 'modelname');


*!*So, to figure out what type a field is I have to get a vanilla parcel and look at it's schema.


$Obj_Loader->collection(CollectionName)->fetch(ParcelType)->collect(true);
$this->collect('parcel', 'users', array('id', 'key', 'username'))->limit(1)->order('a')->fetch(true);


SELECT `$fields[name]` FROM `$table` WHERE `` = '' IN('');
UPDATE `$table` SET `$fields[name]` = '$fields[value]' WHERE `` = ''

//*!*These are the basic query structures as DOM objects.

<insert database="" table="" limit="">
	<field name=""></field>
	<field name=""></field>
</insert>

<delete database="" table="" limit="">
	<where>
		<eq name=""></eq>
	</where>
</delete>

<select database="" table="" limit="">
	<field name="">
	<field name="">
	<where>
		<or>
			<eq name=""></eq>
			<and>
				<gt name=""></gt>
				<gt name=""></gt>
			</and>
		</or>
		<between name="">
			<low></low>
			<high></high>
		</between>
	</where>
	<order name=""></order>
</select>

<update database="" table="" limit="">
	<field name=""></field>
	<where>
		<eq name=""></eq>
	</where>
</update>

//example of a where declaration.
$Obj_Collection = $this->collect('parcel', 'user', array('hello', 'world'))->where(
	'and' => array(
		array('test', 'eq', 'hey'),
		array('blah', 'gt', 'yo'),
		'or' => array(
			array('test', 'eq', 'hey'),
			array('blah', 'gt', 'yo'))
))->fetch();

(() && () && ( || ))

*/

class MW_Utility_Collection extends MW_System_Base
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////
/*
	//Model descriptor variables.
	public $Str_ModelType			= '';	//Name of parent.
	public $Str_ModelName			= '';	//Name of class.
*/
	//Collection query data.
	public $Arr_CollectionData		= false;

	public $Arr_LinkColumns			= array();
	public $Arr_LinkFormats			= array();

	public $Str_ExtractField		= false;


///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties	= array(
		'Arr_CollectQuery'		=> null,	//Collection query properties
		'Arr_DataSchema'		=> null);	//Data schema model object



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	public function __construct(){}


///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////


	//Initiallises a new collection to a specific parcel.
	// - $Obj_Model:			Instance of model to create a collection for
	// * Return:				SELF
	public function collect($Obj_Model)
	{
/*
		//Set model descriptors
		//*!*I don't if I actually need these.
		$this->Str_ModelType = $Obj_Model->Str_ModelType;
		$this->Str_ModelName = $Obj_Model->Str_ModelName;
*/
		//Set link table schema.
		$this->Arr_LinkColumns = $Obj_Model->Arr_LinkColumns;
		$this->Arr_LinkFormats = $Obj_Model->Arr_LinkFormats;

		//Decouple collection queries.
		$Arr_NewCollectQuery = $this->Arr_CollectQuery;

		//Set up the collection array default values.
		$Arr_NewCollectQuery = array(
			'type'		=> 'select',
			'node'		=> $Obj_Model->Str_ModelType,
			'name'		=> $Obj_Model->Str_ModelName,
			'fields'	=> false,
			'offset'	=> false,
			'range'		=> false,
			'order'		=> false,
			'where'		=> false,
			'column'	=> $Obj_Model->query_column(),
			'format'	=> $Obj_Model->query_format());

		//Get model object data schema.
		$this->Arr_DataSchema = $Obj_Model->get_data_schema();
//var_dump($Arr_NewCollectQuery);exit;
		//Recouple collection queries.
		$this->Arr_CollectQuery = $Arr_NewCollectQuery;

		return $this;
	}

	//Loads parcel data by id or key as set in __construct()
	// * Return:					SELF
	public function load()
	{
		global $CMD;
		$CMD->load($this);

		return $this;
	}

	//Sets parcel data fields to query in collection
	// - $Arr_Fields:			Fields to include in the collection, default=false(all fields)
	// * Return:				Data in database fields $Arr_Fields
	//*!*This needs a second paramter which is $this->Arr_DataSchema.
	public function data($Arr_Fields=false)
	{
		//If the fields are within the model schema add them.
		if ($Arr_Fields)
		{
			//Decouple collection query.
			$Arr_NewCollectQuery = $this->Arr_CollectQuery;

			$Arr_AddedFields = array();
			foreach ($this->Arr_DataSchema as $Arr_FieldName => $Arr_FieldProperty)
			{
				//If the field has not already been added do it.
				if (in_array($Arr_FieldName, $Arr_Fields))
				{
					$Arr_AddedFields[] = $Arr_FieldProperty['column'];
				}
			}

			//Set database field names to query.
			if ($Arr_AddedFields)
				$Arr_NewCollectQuery['fields'] = $Arr_AddedFields;
			else
				$Arr_NewCollectQuery['fields'] = false;

			//If their is a discrepancy between the added fields and those supplied handle exception.
			//*!*Might be more specific with the error messsage in the future.
			if (count($Arr_AddedFields) != count($Arr_Fields))
			{
				global $CMD;
				$CMD->handle_exception('Incorrect fields found supplied in collect', 'MW:101');
			}

			//Recouple collection query.
			$this->Arr_CollectQuery = $Arr_NewCollectQuery;
		}

		return $this;
	}

	//Makes a database inner join of parcels by type.
	// - $Mix_Storage:				Storage type to use for parcel, default = false(use settings value)
	// * Return:					$Arr_QueryResults
	//*!*Note: I will be moving the module->couple functionallity to this object which is switched on using a parameter
	//that will indicate that the returned results should be coupled with the data set supplied, if given on linked
	//Actually, thinking about it, doing this a number of times, stacking a dataset could get programatically unweildy
	//This might mean that this is not the best place to put the function. In fact it's pretty much a no go.
	//Better to make the function $collection($data)->couple($collection, $name);
	public function fetch($Mix_Storage=false)
	{
		global $CMD;
		$Arr_Query = $this->Arr_CollectQuery;
		$Str_Storage = (!$Mix_Storage)? $CMD->config('storage'): $Mix_Storage;
		$Arr_Results = array();

		//If the storage model is file then fetch data from files if possible.
		if ($Str_Storage == 'file' && !isset($Arr_Query['join']))
		{
			$Obj_Model = $CMD->$Arr_Query['node'](str_replace('_', '/', $Arr_Query['name']));

			//Get the model key field with conditions and load data for sorting.
			if ($Arr_Query['where'] && is_array($Arr_Query['where']))
			{
				foreach ($Arr_Query['where'] as $Arr_Where)
				{
					if ($Arr_Where[0] == $Obj_Model->get_key_field())
					{
						//*!*I need to add tests for all the different query operators(neq) here.
						//Get file matching key.
						if ($Arr_Where[1] == 'eq' && is_string($Arr_Where[2]))
						{
							$Obj_Model->set_values(array($Obj_Model->get_key_field() => $Arr_Where[2]));
							$Arr_Results[0] = $Obj_Model->load('file')->data();

							break;
						}
						//Get all files in matching key set.
						elseif ($Arr_Where[1] == 'in' && is_array($Arr_Where[2]))
						{
							foreach ($Arr_Where[2] as $Str_Key)
							{
								$Obj_Model->set_values(array());
								$Obj_Model->set_values(array($Obj_Model->get_key_field() => $Str_Key));
								$Arr_Results[] = $Obj_Model->load('file')->data();
							}

							break;
						}
					}
				}
			}

			//*!*Still need to sort through the data in the results. but this is functional for now.
			return $Arr_Results;
		}

		//Convert model property field names to database column names.
		//*!*Need to cycle through the model object table schema here.
		//*!*Maybe I should add the translation to another array like the format array.
		//I'm not sure why I have commented here, I thought this was taken care of with the driver or storage object

		//*!*Adding quick hack as we are only dealing with parcel data.
		$Arr_Query['aspect'] = 'data';

		//If extract field is set and
		if ($this->Str_ExtractField)
		{
			$Arr_Query['fields'][] = $this->Str_ExtractField;

			//Also, if the field is not the id then add the id as I'm returning a key/value pair with ids
			//*!*Will change this as packages are introduced
			if ($this->Str_ExtractField != 'id')
			{
				$Arr_Query['fields'][] = 'id';
			}
		}
		//If there are defined data fields to get add all default columns to query.
		//*!*We're only dealing with parcels for the moment.
		elseif ($Arr_Query['fields'])
		{
			$Arr_Query['fields'][] = 'id';
			$Arr_Query['fields'][] = 'key';
			$Arr_Query['fields'][] = 'view';
			$Arr_Query['fields'][] = 'edit';
			$Arr_Query['fields'][] = 'lock';
			$Arr_Query['fields'][] = 'status';
			$Arr_Query['fields'][] = 'created';
			$Arr_Query['fields'][] = 'modified';
		}

		//Execute collection query.
		$Arr_Results = $CMD->query($Arr_Query);

		//Set results locally for later use.
		$this->Arr_CollectionData = $Arr_Results;

		//If a field is to be extracted get it.
		if ($Arr_Results && $this->Str_ExtractField)
		{
			$Arr_ExtractedResults = array();
			foreach ($Arr_Results as $Arr_Result)
			{
				//*!*Will modify when packages are introduced
				//*!*I need a call here to reverse the extraction as the second paramater to the extract() method.
				//$Arr_ExtractedResults[$Arr_Result['id']] = $Arr_Result[$this->Str_ExtractField];
				$Arr_ExtractedResults[$Arr_Result[$this->Str_ExtractField]] = $Arr_Result['id'];

			}

			return $Arr_ExtractedResults;
		}


		return $Arr_Results;
	}

	//Set an access where clause on the collection query.
	// - $Bit_ViewAccess:			Level of access to match for
	public function access($Bit_ViewAccess)
	{
		//Just doing some dodgey whering for now.
		$Arr_Access = array(array('view', 'eq', $Bit_ViewAccess));

		return $this->where($Arr_Access);
	}

	//Sets a data version clause on the collection query.
	//*!*Um, their is no versioning with parcels so, we are coming back to this.
	public function version($Bit_DataVersion)
	{

		return $this;
	}
	
	//Adds a where conditional clause to the collection.
	// - $Arr_Conditions:			Conditiona to add to the WHERE collect statement
	// * Return:					SELF
	// * NB: If this function is used more than once then $Arr_Conditions will be added
	//to the previous conditions at the top level, which, is always an AND clause
	//*!*I need to change this levle on the where to apply to the cuurent join query.
	public function where($Arr_Conditions)
	{
		if (is_array($Arr_Conditions) && $Arr_Conditions)
		{
			//Decouple collection queries.
			$Arr_NewCollectQuery = $this->Arr_CollectQuery;

			//If there is an existing where clause add conditions to the top level
			if ($Arr_NewCollectQuery['where'])
			{
				$Arr_NewCollectQuery['where'][] = $Arr_Conditions;
			}
			//Otherwise set where conditions.
			else
			{
				//Ensure conditions are wrapped in an array.
				if (isset($Arr_Conditions[0]) && !is_array($Arr_Conditions[0]))
					$Arr_Conditions = array($Arr_Conditions);

				$Arr_NewCollectQuery['where'] = $Arr_Conditions;
			}

			//Recouple collection queries.
			$this->Arr_CollectQuery = $Arr_NewCollectQuery;
		}

		return $this;
	}

	//Adds list of linked model ids as a where statement to the collection.
	//*!*Adding a third parameter to this function to look for objects which aren't linked
	//To find the unlinked data I need the model object type, the id os parcels to find
	//I simply need to find a way of making acl
	// - $Obj_Model:				The storage model type that the collection is linked to
	// - $Arr_LinkIds:				Ids of models to link, default = false(use id of $Obj_Model if it exists)
	// * Return:					SELF
	//*!*This function is chain linking, so I'll need to debug the join functionality to make sure that
	//the query is being coprrectly formed
	//*!*Need to put a test in to see if Obj_Model is an object of the correct type, otherwise handle exception
	public function linked($Obj_Model, $Arr_LinkIds=false)
	{
		//Decouple collection queries.
		$Arr_NewCollectQuery = $this->Arr_CollectQuery;

		//Add parcel type links condition.
		$Arr_Conditions = array();
		$Arr_Conditions[] = array('model_type', 'eq', $Obj_Model->Str_ModelType);
		$Arr_Conditions[] = array('model_name', 'eq', $Obj_Model->Str_ModelName);

		//If there are link ids defined add them to.
		$Arr_ModelIds = array();
		if ($Arr_LinkIds)
		{
			foreach ($Arr_LinkIds as $Mix_Key => $Mix_Value)
			{
				//If the key and value are integers add value.
				if (is_int($Mix_Value) || ctype_digit($Mix_Value))
				{
					$Arr_ModelIds[] = $Mix_Value;
				}
				//Else if the value is an array and has a model id key add value.
				elseif (is_array($Mix_Value) && isset($Mix_Value[$Obj_Model->Str_ModelType.'_id']))
				{
					$Arr_ModelIds[] = $Mix_Value[$Obj_Model->Str_ModelType.'_id'];
				}
				//*!*Not sure if I'm error handling the else() or ignoring all other values.
			}

			$Arr_Conditions[] = array('model_id', 'in', $Arr_ModelIds);
		}
		//Else if there is an id on the model supplied add it as a link condition.
		elseif ($Obj_Model->data($Obj_Model->Str_ModelType.'_id'))
		{
			$Arr_Conditions[] = array('model_id', 'eq', $Obj_Model->data($Obj_Model->Str_ModelType.'_id'));
		}

		//Construct the query join.
		$Arr_Join = array(
			'type'		=> 'select',
			'node'		=> $this->Arr_CollectQuery['node'],
			'name'		=> $this->Arr_CollectQuery['name'],
			'aspect'	=> 'link',
			'on'		=> $Obj_Model->Str_ModelType.'_id',
			'fields'	=> false,
			'where'		=> $Arr_Conditions,
			'column'	=> $this->Arr_LinkColumns,
			'format'	=> $this->Arr_LinkFormats);

		//Add join to query.
		if (!isset($Arr_NewCollectQuery['join']))
			$Arr_NewCollectQuery['join'] = array();

		$Arr_NewCollectQuery['join'][] = $Arr_Join;

		//Recouple collection queries.
		$this->Arr_CollectQuery = $Arr_NewCollectQuery;

		return $this;
	}

	//Excludes fields from collection.
	// - $Arr_Fields:			Fields to exclude from collection
	// * Return:				SELF
	//*!*I may allowscalar values and variable number of params in the future.
	//*!*OK this is fucked I need to find the column name of the field property.
	//But I cbf doing this now, little assument for later on with cleanup patrol!
	// * NB: This is purely a memory optimisation function.
	//*!*I looks like the functions admit and omit are pretty useless, they can be handled by data()
	public function omit($Arr_Fields)
	{
		if (is_array($Arr_Fields) && $Arr_Fields)
		{
			//Decouple collection queries.
			$Arr_NewCollectQuery = $this->Arr_CollectQuery;

			//Rebuild field list.
			$Arr_IncludeFields = array();
			foreach ($Arr_NewCollectQuery['fields'] as $Str_IncludedField)
			{
				if (!in_array($Str_IncludedField, $Arr_Fields))
					$Arr_IncludeFields[] = $Str_IncludedField;
			}

			if ($Arr_IncludeFields !== false)
				$Arr_NewCollectQuery['fields'] = $Arr_IncludeFields;
			else
				$Arr_NewCollectQuery['fields'] = false;

			//Recouple collection queries.
			$this->Arr_CollectQuery = $Arr_NewCollectQuery;
		}
		else
		{
			global $CMD;
			$CMD->handle_exception('Excluded fields parameter is not an array', 'MW:101');
		}

		return $this;
	}

	//Only gets data for the specified model object fields.
	// - $Arr_Fields:			Fields to include in the collection
	// * Return:				SELF
	// * NB: This is purely a memory optimisation function.
	public function admit()
	{

	}

	//Sets results limit on a query.
	// - $Int_MaxResultRows:	Limit of affected rows in query
	// * Return:				SELF
	//*!*I need to be able to include the second parameter with this limit function.
	//I'm currently researching whether or not this is a good way to do the pagination 
	//as there may be perfomance issues with large datasets.
	//The tw params are now offset and range
	//public function limit($Int_MaxResultRows)
	public function limit($Int_Offset, $Int_Range=false)
	{
		if ($Int_Range === false)
		{
			$Int_Range = $Int_Offset;
			$Int_Offset = 0;
		}

		//Handle exception if limit is not a number.
		//*!*handle exception for both the offset and the range
		if (!is_int($Int_Offset) && (is_string($Int_Offset) && !ctype_digit($Int_Offset)))
		{
			global $CMD;
			$CMD->handle_exception('Query limit offset is not a number', 'MW:101');
		}

		//Decouple collection queries.
		$Arr_NewCollectQuery = $this->Arr_CollectQuery;

		//Add limit.
		$Arr_NewCollectQuery['offset'] = $Int_Offset;
		$Arr_NewCollectQuery['range'] = $Int_Range;

		//Recouple collection queries.
		$this->Arr_CollectQuery = $Arr_NewCollectQuery;

		return $this;
	}
	
	//adds where clauses to find parcels which are activated.
	// - $Bol_Activation:		If true looks for active parcel, otherwise looks for disabled parcel.
	public function active($Bol_Activation=true)
	{

	}

	//Set the order in which the results are returned
	// - $Str_Field:			Field name against which to order results
	// - $Str_Token:			Order token('a'=ascending, 'd'=descending), default = 'a'(ascending)
	// * Return:				SELF
	//*!*Note, I'll probably have to look the query type to see if this function is appropriate.
	public function order($Str_Field, $Str_Token='a')
	{
		//Decouple collection queries.
		$Arr_NewCollectQuery = $this->Arr_CollectQuery;

		//Add order.
		$Arr_NewCollectQuery['order'] = array($Str_Field, $Str_Token);

		//Recouple collection queries.
		$this->Arr_CollectQuery = $Arr_NewCollectQuery;

		return $this;
	}

	//Extracts a single field from the query results as an array.
	// - $Str_Field:			Name of model schema field to extract
	// - $Bol_Flip:				Flip the extracted array, default = false(Ids as keys)
	//*!*I'm not setting the flip directive for results which are yet to be gotten.
	public function extract($Str_Field, $Bol_Flip=false)
	{
		//*!*If the data schema is set and the field is not on the schema handle excpetion.
		$this->Str_ExtractField = $Str_Field;

		//If the data schema isn't set but the collection data has return an extracted set.
		if (is_array($this->Arr_CollectionData) && $this->Arr_CollectionData && !$this->Arr_DataSchema)
		{
			$Arr_ExtractedData = array();

			//Handle exception if the field name is not in the collection data.
			if (!isset($this->Arr_CollectionData[0][$Str_Field]))
			{
				global $CMD;
				$CMD->handle_exception('Field '.$Str_Field.' not found in Arr_CollectionData', 'MW:101');
			}

			//Extract data set.
			foreach ($this->Arr_CollectionData as $Arr_Data)
			{
				$Arr_ExtractedData[$Arr_Data['id']] = $Arr_Data[$Str_Field];
			}

			if ($Bol_Flip)
			{
				$Arr_ExtractedData = array_flip($Arr_ExtractedData);
			}

			return $Arr_ExtractedData;
		}

		return $this;
	}



///////////////////////////////////////////////////////////////////////////////
//                U P D A T I N G   F U N C T I O N S                        //
///////////////////////////////////////////////////////////////////////////////

	//Refines the collection once it has been retrieved.
	//NB: This is used before updating the database, in some instances.
	public function refine()
	{

	}


///////////////////////////////////////////////////////////////////////////////
//             D A T A   S O R T I N G   F U N C T I O N S                   //
///////////////////////////////////////////////////////////////////////////////

	//Gets all data in results set within a specified column
	// - $Str_FieldName:		Name of column to get all data for
	// * Return:				Results set if fields exists in collection data, otherwise false
	public function get_column_data($Str_FieldName)
	{
		$Arr_ColumnData = array();
		
		if ($this->Arr_CollectionData === false)
		{
			return false;
		}

		//If there are no result rows return false.
		if (($Int_ResultRows = count($this->Arr_CollectionData)) == 0)
		{
			return false;
		}
		//Else if there are no columns by column name handle exception.
		elseif (!isset($this->Arr_CollectionData[0][$Str_FieldName]))
		{
			global $CMD;
			$CMD->handle_exception('Collection has no data with field name '.$Str_FieldName, 'MW:101');

			return false;
		}
		//Otherwise get results data by column.
		else
		{
			for ($i = 0; $i < $Int_ResultRows; $i++)
			{
				//Add field value to the id on the model data.
				$Arr_ColumnData[$this->Arr_CollectionData[$i][$this->Str_ModelName.'_id']] = $this->Arr_CollectionData[$i][$Str_FieldName];
			}
		}

		return $Arr_ColumnData;
	}

///////////////////////////////////////////////////////////////////////////////
//                 U T I L I T Y   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

	//Gets the text node of a data field element.
	// - $Obj_Element:			DOM element being to get text node for
	// * Return:				The text node if it exists, otherwisre false
	public function get_text_node($Obj_Element)
	{
		//Set default return variable.
		$Obj_TextNode = false;

		//Get data field text node.
		$Arr_ChildNodes = $Obj_Element->childNodes;
		foreach ($Arr_ChildNodes as $Obj_Child)
		{
			if ($Obj_Child->nodeType == XML_TEXT_NODE)
			{
				$Obj_TextNode = $Obj_Child;
				break;
			}
		}

		return $Obj_TextNode;
	}


///////////////////////////////////////////////////////////////////////////////
//                   M O V E D     F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////
/*!*These functions have been moved, here's the scoop.
I'm not converting properties to QXL o convert to SQL
Instead I'll directly convert properties to SQL via the driver then, if batching needs to take place,
I'll convert the objects to XQL which will be turned back into objects when it is time to make the query.

So, I'll name these to xql_function_name unitl such a point when I properly sort out an API


*/

	//Just some simple debugging of a dom document
	public function xql_debug_query($Str_Query)
	{
		var_dump(htmlentities($this->Arr_CollectQueries[$Str_Query]->saveXML()));exit;
	}

	//Initiallises a new collection to a specific parcel.
	// - $Str_Type:				Type of model to create a collection for
	// - $Str_Name:				Name of the model that the collection is of
	// - $Arr_Fields:			Fieds to include in the collection, default=false(all fields)
	// * Return:				SELF
	//*!*I think some of this initialisation routine needs to be performed by the model object.
	public function xql_collect($Str_Type, $Str_Name, $Arr_Fields=false)
	{
		//Decouple collection queries.
		$Arr_NewCollectionQueries = $this->Arr_CollectQueries;

		//Get model object for property reference.
		global $CMD;
		$this->Obj_CollectModel = $CMD->$Str_Type($Str_Name);

		//Create selection DOM object.
		$Obj_SelectQuery = new DOMDocument('1.0', 'utf-8');
		$Obj_SelectRoot = $Obj_SelectQuery->createElement('select');
		$Obj_SelectQuery->appendChild($Obj_SelectRoot);
		//*!*This fucker needs to be added in by either the storage or the foreman object.
		$Obj_SelectRoot->setAttribute('database', 'monkeywrench'); //$CMD->config('x_database')
		$Obj_SelectRoot->setAttribute('table', 'parcel_'.$Str_Name.'_data');

		//Add fields to the collection.
		if (is_array($Arr_Fields) && $Arr_Fields)
		{
			$Arr_AddedFields = array();
			foreach ($Arr_Fields as $Str_Field)
			{
				//Avoid duplication of fields.
				if (!in_array($Str_Field, $Arr_AddedFields))
				{
					$Obj_Field = $Obj_SelectQuery->createElement('field');
					$Obj_SelectRoot->appendChild($Obj_Field);
					$Obj_Field->setAttribute('name', $Str_Field);
					$Arr_AddedFields[] = $Str_Field;
				}
			}
		}

		//*!*Quick hack save as the first collect query object.
		//Not sure what indexs I'm using at the moment, I might retain this.
		$Arr_NewCollectionQueries['data'] = $Obj_SelectQuery;

		//Recouple collection queries.
		$this->Arr_CollectQueries = $Arr_NewCollectionQueries;

		return $this;
	}

	//Sets results limit on a query.
	// - $Int_MaxResultRows:	Limit of affected rows in query
	// * Return:				SELF
	public function xql_limit($Int_Offset, $Int_Range=false)
	{
		if ($Int_Range === false)
		{
			$Int_Range = $Int_Offset;
			$Int_Offset = 0;
		}

		//Handle exception if limit is not a number.
		if (!is_int($Int_Offset) && (is_string($Int_Offset) && !ctype_digit($Int_Offset)))
		{
			global $CMD;
			$CMD->handle_exception('Query limit is not a number', 'MW:101');
		}

		//Decouple collection queries.
		$Arr_NewCollectionQueries = $this->Arr_CollectQueries;

		//Set limit.
		$Obj_SelectRoot = $Arr_NewCollectionQueries['data']->getElementsByTagName('select')->item(0);
		$Obj_SelectRoot->setAttribute('offset', $Int_Offset);
		$Obj_SelectRoot->setAttribute('range', $Int_Range);

		//Recouple collection queries.
		$this->Arr_CollectQueries = $Arr_NewCollectionQueries;

		return $this;
	}

	//Set the order in which the results are returned
	// - $Str_Field:			Field name against which to order results
	// - $Str_Token:			Order token('a'=ascending, 'd'=descending), default = 'a'(ascending)
	// * Return:				SELF
	//*!*Note, I'll probably have to look the query type to see if this function is appropriate.
	public function xql_order($Str_Field, $Str_Token='a')
	{
		//Decouple collection queries.
		$Arr_NewCollectionQueries = $this->Arr_CollectQueries;

		//Handle exception if limit is not a number.
		if ($Str_Token != 'a' || $Str_Token != 'd')
		{
			global $CMD;
			$CMD->handle_exception('Incorrent token supplied for order', 'MW:101');
		}
		
		//If we are not making a select statement handle exception.
		if (!$Obj_SelectRoot = $Arr_NewCollectionQueries['data']->getElementsByTagName('select')->item(0))
		{
			global $CMD;
			$CMD->handle_exception('Incorrent token supplied for order', 'MW:101');
		}
		//Otherwise set the results order.
		else
		{
			//If there isn't already an order element create a new one.
			if (!$Obj_OrderElement = $Arr_NewCollectionQueries['data']->getElementsByTagName('order')->item(0))
			{
				$Obj_OrderElement = $Arr_NewCollectionQueries['data']->createElement('order', $Str_Token);
				$Obj_SelectRoot->appendChild($Obj_OrderElement);
			}
			//Else if there is no text node add one.
			elseif (($Obj_OrderText = $this->get_text_node($Obj_OrderElement)) === false)
			{
				$Obj_OrderElement->appendChild($Arr_NewCollectionQueries['data']->createTextNode($Str_Token));
			}
			//Otherwise set the order element text value.
			else
			{
				$Obj_OrderText->nodeValue = $Str_Token;
			}

			$Obj_OrderElement->setAttribute('name', $Str_Field);
		}

		//Recouple collection queries.
		$this->Arr_CollectQueries = $Arr_NewCollectionQueries;

		return $this;
	}


}

?>