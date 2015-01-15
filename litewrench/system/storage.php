<?php

class MW_System_Storage extends MW_System_Base
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Database connection.
	protected $Res_Connection		= null;

	//Query reference.
	//*!*Don't think I'll be using this!
	protected $Arr_QueryIndexed			= array();



///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties	= array(
		'Arr_Settings'			=> array(),	//Database connection settings.
		'Arr_ConnectConfig'		=> array(),	//Database connection settings.
		'Arr_SchemaIndex'		=> array(),	//Index of table data schema.
		'Str_DBQuery'			=> '');		//Driver query made in query().



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Sets system datbase variables locally.
	public function __construct()
	{
		//Get database values from system config file.

		//If database variables are invalid handle error.

		//Set the host, user name and password.

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

	//Makes a database query and returns a response to the query.
	// - $Arr_Query:			Query properties array
	// * Return:				Results set if a SELECT query, otherwise rue or false to query success.
	public function query($Arr_Query)
	{
		//I need to check that the query has been correctly defined.

		//Insert files from file data. NB: This treats uploaded files in a posted form only.
		//*!*Gut this file stuff.
		if (isset($Arr_Query['format']) && $Arr_Query['format']
		&& isset($Arr_Query['fields']) && $Arr_Query['fields'])
		{
			$Arr_OldQuery = $Arr_Query;
			foreach ($Arr_OldQuery['format'] as $Str_Column => $Str_Format)
			{
				if ($Str_Format == 'binary'
				&& isset($Arr_OldQuery['fields'][$Str_Column])
				&& is_array($Arr_OldQuery['fields'][$Str_Column]))
				{
					$Arr_Query['fields'][$Str_Column] = $Arr_OldQuery['fields'][$Str_Column]['name'];
					//$Res_File = fopen($Arr_OldQuery['fields'][$Str_Column]['tmp_name'], 'r');
					//$Str_File = fread($Res_File, $Arr_OldQuery['fields'][$Str_Column]['size']);
					//$Arr_Query['fields'][$Str_Column] = $Str_File;
				}
			}
		}

		//If pre-query formatting is successfull make query.
		if ($Mix_Results = $this->prequery($Arr_Query))
			$Mix_Results = $this->do_query($Arr_Query);

		return $Mix_Results;
	}



///////////////////////////////////////////////////////////////////////////////
//                    D R I V E R   F U N C T I O N S                        //
///////////////////////////////////////////////////////////////////////////////

	//Formatts where conditionsm by inserting index placeholders for column names.
	// - $Arr_WhereSet:			Set of query conditions
	// * Return:				Conditions set with index placeholders inserted
	public function formatt_conditions($Arr_WhereSet, $Arr_TableIndex)
	{
		//Loop through where set and formatt condition group.
		$Arr_ConditionKeys = array_keys($Arr_WhereSet);
		$Int_Length = count($Arr_ConditionKeys);
		for ($i = 0; $i < $Int_Length; $i++)
		{
			if (is_int($Arr_ConditionKeys[$i]) && count($Arr_WhereSet[$Arr_ConditionKeys[$i]]) > 1)
			{
				//If the column is in the index replace it with the index.
				$Bol_IndexFound = false;
				foreach ($Arr_TableIndex as $Str_Index => $Arr_Index)
				{
					if ($Arr_WhereSet[$Arr_ConditionKeys[$i]][0] == $Arr_Index['column'])
					{
						$Arr_WhereSet[$Arr_ConditionKeys[$i]][0] = $Str_Index;
						$Bol_IndexFound = true;
					}
				}

				//If the index has not been found handle exception.
				if (!$Bol_IndexFound)
				{
					global $CMD;
					$CMD->handle_exception('Index '.$Arr_ConditionKeys[$i][0].' not found', 'MW:101');
				}
			}
			else
			{
				$Arr_WhereSet[$Arr_ConditionKeys[$i]] = $this->formatt_conditions($Arr_WhereSet[$Arr_ConditionKeys[$i]], $Arr_TableIndex);
			}
		}

		return $Arr_WhereSet;
	}

	//Adds table schema index to query array.
	// - $Arr_Query:			Query properties array
	// - $Int_Indexes:			Number of indexes already created for query
	// * Return:				Query table schema index
	public function index_query($Arr_Query, $Int_Indexes)
	{
		//Create and insert table schema indexes.
		$Arr_TableIndex = array();
		$Int_ColumnNumb = count($Arr_Query['column']);
		$Arr_ColumnKeys = array_keys($Arr_Query['column']);
		for ($i = 0; $i < $Int_ColumnNumb; $i++)
		{
			$Int_Indexes++;
			$Str_Index = 'index_'.$Int_Indexes;
			$Arr_TableIndex[$Str_Index] = array();
			//$Arr_TableIndex[$Str_Index]['table'] = $Arr_Query['node'].'_'.$Arr_Query['name'].'_'.$Arr_Query['aspect'];
			//Removed for lite version
			$Arr_TableIndex[$Str_Index]['table'] = $Arr_Query['name'];
			$Arr_TableIndex[$Str_Index]['column'] = $Arr_Query['column'][$Arr_ColumnKeys[$i]];
			$Arr_TableIndex[$Str_Index]['format'] = $Arr_Query['format'][$Arr_ColumnKeys[$i]];

			//If there is a field key/value set replace key with an index.
			if (is_array($Arr_Query['fields']) && isset($Arr_Query['fields'][$Arr_ColumnKeys[$i]]))
			{
				$Arr_Query['fields'][$Str_Index] = $Arr_Query['fields'][$Arr_ColumnKeys[$i]];
				unset($Arr_Query['fields'][$Arr_ColumnKeys[$i]]);
			}
			//Else if the there is a key in a list of fields replace it with an index.
			elseif (is_array($Arr_Query['fields']) && ($Arr_FieldNames = array_keys($Arr_Query['fields'], $Arr_ColumnKeys[$i]))
			&& is_int($Arr_FieldNames[0]) && isset($Arr_Query['fields'][$Arr_FieldNames[0]]))
			{
				$Arr_Query['fields'][$Arr_FieldNames[0]] = $Str_Index;
			}
			//Else if the key/value set has a null value taken from the database remove the field.
			elseif (is_array($Arr_Query['fields'])
			&& array_key_exists($Arr_ColumnKeys[$i], $Arr_Query['fields'])
			&& $Arr_Query['fields'][$Arr_ColumnKeys[$i]] === null)
			{
				unset($Arr_Query['fields'][$Arr_ColumnKeys[$i]]);
			}

			//If there is a results order definition replace it with an index.
			if (isset($Arr_Query['order'][0]) && $Arr_Query['order'][0] == $Arr_ColumnKeys[$i])
			{
				$Arr_Query['order'][0] = $Str_Index;
			}
		}

		//Include all fields if there are none defined on a select query.
		if ((!isset($Arr_Query['type']) || ($Arr_Query['type'] == 'select'))
		&& (!isset($Arr_Query['fields']) || (!$Arr_Query['fields'] && $Arr_Query['fields'] !== false)))
		{
			$Arr_Indexes = array_keys($Arr_TableIndex);
			$Int_Indexs = count($Arr_Indexes);
			for ($i = 0; $i < $Int_Indexs; $i++)
			{
				$Arr_Query['fields'][] = $Arr_Indexes[$i];
			}
		}

		//Add indexes to where conditions.
		if (isset($Arr_Query['where']) && $Arr_Query['where'])
			$Arr_Query['where'] = $this->formatt_conditions($Arr_Query['where'], $Arr_TableIndex);

		//Merge local table schema index.
		$this->Arr_SchemaIndex = array_merge($this->Arr_SchemaIndex, $Arr_TableIndex);

		return $Arr_Query;
	}

	//Prepares query array to build a query string.
	// - $Arr_Query:			Reference to query properties array
	//*!*I should add this locally for easy access and debugging.
	// * Return:				True on success, otherwiwse false
	public function prequery(&$Arr_Query)
	{
		//If there is no query type defined handle exception.
		if (!isset($Arr_Query['type']) || !$Arr_Query['type'])
		{
			global $CMD;
			$CMD->handle_exception('No query type defined', 'MW:101');
			return false;
		}

		//Index root query table schema.
		$Arr_Query = $this->index_query($Arr_Query, 0);

		//Free up formatting data on query.
		//unset($Arr_Query['column']);
		//unset($Arr_Query['format']);

		//If there are joins add tables to index.
		if (isset($Arr_Query['join']) && is_array($Arr_Query['join']) && $Arr_Query['join'])
		{
			$Int_IndexNumb = count($this->Arr_SchemaIndex);
			foreach ($Arr_Query['join'] as $Arr_Join)
			{
				//Index join query table schema.
				$Arr_Join = $this->index_query($Arr_Join, $Int_IndexNumb);

				//Add indexes to where conditions.
				if (isset($Arr_Join['where']) && $Arr_Join['where'])
				{
					if (isset($Arr_Query['where']) && $Arr_Query['where'])
					{
						foreach ($Arr_Join['where'] as $Arr_Condition)
						{
							$Arr_Query['where'][] = $Arr_Condition;
						}
					}
					else
					{
						$Arr_Query['where'] = $Arr_Join['where'];
					}
				}

				//Add join fields to query.
				if (isset($Arr_Join['fields']) && is_array($Arr_Join['fields']) && $Arr_Join['fields'])
				{
					foreach ($Arr_Join['fields'] as $Str_Field)
					{
						$Arr_Query['fields'][] = $Str_Field;
					}
				}

				//Add order to query.
				//*!*Not sure I'll ever have an order directive on a join

				$Int_IndexNumb = count($this->Arr_SchemaIndex);
			}
		}

		return true;
	}



///////////////////////////////////////////////////////////////////////////////
//                   R E S O U R C E   F U N C T I O N S                     //
///////////////////////////////////////////////////////////////////////////////

	//Connect to the database.
	// - $Arr_DatabaseSettings:		Settings for database connection
	// * Return:					True if a connection was successfully established, otherwise false
	public function connect($Arr_DatabaseSettings)
	{
		//If there is no connection create it.
		if (!$this->Res_Connection)
		{
			$this->Arr_ConnectConfig = $Arr_DatabaseSettings;

			if (isset($Arr_DatabaseSettings['driver']) && $Arr_DatabaseSettings['driver'] == 'mysqli')
			{
				$this->Res_Connection = mysqli_connect($Arr_DatabaseSettings['location'],
													$Arr_DatabaseSettings['username'],
													$Arr_DatabaseSettings['password'],
													$Arr_DatabaseSettings['database']);

				//If not connection was made handle exception.
				if (!$this->Res_Connection)
				{
					global $CMD;
					$CMD->handle_exception('Database could not make connection: '.mysqli_connect_error(), 'MW:101');

					return false;
				}
			}
			else
			{
				$this->Res_Connection = mysql_connect($Arr_DatabaseSettings['location'],
													$Arr_DatabaseSettings['username'],
													$Arr_DatabaseSettings['password']);

				//If not connection was made handle exception.
				if (mysql_error($this->Res_Connection))
				{
					global $CMD;
					$CMD->handle_exception('Database could not make connection: '.mysql_error($this->Res_Connection), 'MW:101');
					$this->Res_Connection = null;

					return false;
				}
			}
		}

		return true;
	}

	//Disconnects driver from the database.
	// * Return:				VOID
	public function disconnect()
	{
		if ($this->Res_Connection)
		{
			if ($this->Arr_ConnectConfig['driver'] == 'mysqli')
			{
				mysqli_close($this->Res_Connection);
			}
			else
			{
				mysql_close($this->Res_Connection);
			}

			$this->Res_Connection = null;
		}

		return;
	}

	//Performs a query with the database connection.
	// - $Arr_Query:			Database query formatted as an array
	//  Return:					Query result(array or true) if successfull, otherwise false
	public function do_query($Arr_Query)
	{
		$Mix_Results = array();

		//If there is no connection establish one.
		if (!$this->Res_Connection)
		{
			//If a connection could not be established return.
			//*!*Need to check whether or not the database settings are already set, otherwise I need to use those supplied.
			if (!$this->connect($this->Arr_ConnectConfig))
			{
				return;
			}
		}

		//Get query as a statement string.
		$Str_Query = $this->build_query($Arr_Query);

		//If the query produces a result get its value.
		$Obj_Result = ($this->Arr_ConnectConfig['driver'] == 'mysqli')? mysqli_query($this->Res_Connection, $Str_Query): mysql_query($Str_Query, $this->Res_Connection);
		if ($Obj_Result)
		{
			//Get select query result as an array.
			if ($Arr_Query['type'] == 'select')
			{
				if ($this->Arr_ConnectConfig['driver'] == 'mysqli')
				{
					while ($Arr_Row = mysqli_fetch_assoc($Obj_Result))
					{
						$Mix_Results[] = $Arr_Row;
					}

					mysqli_free_result($Obj_Result);
				}
				else
				{
					while ($Arr_Row = mysql_fetch_assoc($Obj_Result))
					{
						$Mix_Results[] = $Arr_Row;
					}

					mysql_free_result($Obj_Result);
				}
			}
			else
			{
				$Mix_Results = true;
			}
		}
		//Otherwise handle exception and assign a false value.
		else
		{
			global $CMD;
			$Str_Error = ($this->Arr_ConnectConfig['driver'] == 'mysqli')? mysqli_error($this->Res_Connection): mysql_error($this->Res_Connection);
			$CMD->handle_exception('MySQLi query error: '.$Str_Error, 'MW:101');

			return false;
		}

		return $Mix_Results;
	}

	//Converts query array into a query statement string.
	// - $Arr_Query:			Database query formatted as an array
	// * Return:				Query array as a SQL statement string
	// * NB: This function does test whether $Arr_Query has been correctly defined
	public function build_query($Arr_Query)
	{
		//If there is no query type defined handle exception.
		//*!*This is to be done in the storage object.
		if (!isset($Arr_Query['type']) || !$Arr_Query['type'])
		{
			global $CMD;
			$CMD->handle_exception('No query type defined', 'MW:101');
		}

		//Format the query data.
		$this->format_data($Arr_Query);

		//Build query type snippet.
		switch ($Arr_Query['type'])
		{
			//Basic select formatting.
			case 'select':
				//*!*This is stupid I need to define fields if none as part of preprocessing.
				//I think I've actually done this and the * is a placeholder
				$Str_Fields = '*';
				$Str_Join = '';

				//Build query field list.
				if (isset($Arr_Query['fields']) && $Arr_Query['fields'])
				{
					$Str_Fields = '';
					$Arr_FormattedFields = array();
					foreach ($Arr_Query['fields'] as $Str_Index)
					{
						$Arr_FormattedFields[] = '`'.$Arr_Query['database'].'`.`'.$this->Arr_SchemaIndex[$Str_Index]['table'].'`.`'.$this->Arr_SchemaIndex[$Str_Index]['column'].'`';
					}

					$Str_Fields = implode(', ', $Arr_FormattedFields);
				}

				//If there is a query join add it.
				//*!*This is a the basic method for getting linked model data
				//	I need to check here for  a normalised database table if the databasing caching is turned on.
				if (isset($Arr_Query['join'][0]) && $Arr_Query['join'][0])
				{
					$Str_JoinTable = '`'.$Arr_Query['database'].'`.`'.$Arr_Query['join'][0]['node'].'_'.$Arr_Query['join'][0]['name'].'_'.$Arr_Query['join'][0]['aspect'].'`';
					$Str_Join .= ' INNER JOIN '.$Str_JoinTable;
					$Str_Join .= ' ON `'.$Arr_Query['database'].'`.`'.$Arr_Query['node'].'_'.$Arr_Query['name'].'_'.$Arr_Query['aspect'].'`.`'.$Arr_Query['join'][0]['on'].'` = '.$Str_JoinTable.'.`'.$Arr_Query['join'][0]['on'].'`';
				}

				//$Str_Query = 'SELECT '.$Str_Fields.' FROM `'.$Arr_Query['database'].'`.`'.$Arr_Query['node'].'_'.$Arr_Query['name'].'_'.$Arr_Query['aspect'].'`'.$Str_Join;
				//removed for lite version
				$Str_Query = 'SELECT '.$Str_Fields.' FROM `'.$Arr_Query['database'].'`.`'.$Arr_Query['name'].'`'.$Str_Join;
				break;

			//Basic update formatting.
			case 'update':
				$Arr_FormattedFields = array();
				foreach ($Arr_Query['fields'] as $Str_Index => $Str_Value)
				{
					$Arr_FormattedFields[] = '`'.$this->Arr_SchemaIndex[$Str_Index]['table'].'`.`'.$this->Arr_SchemaIndex[$Str_Index]['column'].'` = '.$Str_Value;
				}

				$Str_Fields = implode(', ', $Arr_FormattedFields);
				//$Str_Query = 'UPDATE `'.$Arr_Query['database'].'`.`'.$Arr_Query['node'].'_'.$Arr_Query['name'].'_'.$Arr_Query['aspect'].'` SET '.$Str_Fields.'';
				//removed for lite version
				$Str_Query = 'UPDATE `'.$Arr_Query['database'].'`.`'.$Arr_Query['name'].'` SET '.$Str_Fields.'';
				break;

			//Basic insert formatting.
			case 'insert':
				$Arr_FormattedFields = array();
				$Arr_FormattedData = array();
				foreach ($Arr_Query['fields'] as $Str_Index => $Str_Value)
				{
					$Arr_FormattedFields[] = '`'.$this->Arr_SchemaIndex[$Str_Index]['table'].'`.`'.$this->Arr_SchemaIndex[$Str_Index]['column'].'`';
					$Arr_FormattedData[] = $Str_Value;
				}

				$Str_Fields = '('.implode(', ', $Arr_FormattedFields).')';
				$Str_Data = '('.implode(', ', $Arr_FormattedData).')';
				//$Str_Query = 'INSERT INTO `'.$Arr_Query['database'].'`.`'.$Arr_Query['node'].'_'.$Arr_Query['name'].'_'.$Arr_Query['aspect'].'` '.$Str_Fields.' VALUES '.$Str_Data;
				//removed for lite version
				$Str_Query = 'INSERT INTO `'.$Arr_Query['database'].'`.`'.$Arr_Query['name'].'` '.$Str_Fields.' VALUES '.$Str_Data;
				break;

			//Basic delete formatting.
			case 'delete':
				$Str_Query = '';
				break;
		}

		//Add where clauses.
		if (isset($Arr_Query['where']) && $Arr_Query['where'])
			$Str_Query .= ' WHERE'.$this->build_where($Arr_Query['where']);

		//Add query order.
		if (isset($Arr_Query['order']) && $Arr_Query['order'])
		{
			$Str_Order = ($Arr_Query['order'][1] == 'a')? 'ASC': 'DESC';
			$Str_Query .= ' ORDER BY `'.$this->Arr_SchemaIndex[$Arr_Query['order'][0]]['table'].'`.`'.$this->Arr_SchemaIndex[$Arr_Query['order'][0]]['column'].'` '.$Str_Order;
		}

		//Add query limit.
		//*!*this limit statement is going to be broken up into two parts, start results, and range.
		//$Arr_Query['offset'] && $Arr_Query['range']
		//*!*Must run three test here for the combinations of offset and range value/false
/*
		if (isset($Arr_Query['limit']) && $Arr_Query['limit'] && $Arr_Query['type'] != 'insert')
		{
			$Str_Query .= ' LIMIT '.$Arr_Query['limit'];
		}
*/
		if ($Arr_Query['type'] != 'insert')
		{
			if (isset($Arr_Query['offset']) && $Arr_Query['offset']	&& isset($Arr_Query['range']) && $Arr_Query['range'])
			{
				$Str_Query .= ' LIMIT '.$Arr_Query['offset'].', '.$Arr_Query['range'];
			}
			elseif (isset($Arr_Query['range']) && $Arr_Query['range'])
			{
				$Str_Query .= ' LIMIT '.$Arr_Query['range'];
			}
		}

		//Finish query statement.
		$Str_Query .= ';';
		$this->Str_DBQuery = $Str_Query;
//var_dump($Str_Query);
		return $Str_Query;
	}

	//Builds where clause in query statement.
	// - $Arr_WhereSet:			Set of query conditions
	// - $Str_Group:			Type of boolean comparisson with query conditions
	// * Return:				Query conditional statement
	//*!*This basic shell routine could even go into the parent object with modifications to the syntax...
	public function build_where($Arr_WhereSet, $Str_Group='and')
	{
		$Str_Condition = '';
		$Arr_Conditions = array();

		//*!*I'll nee to error check to make sure developer didn't fuck up the array structure.
		//*!*This check should also bee done in the drive utility

		//Loop through where set and build condition group.
		$Arr_ConditionKeys = array_keys($Arr_WhereSet);
		$Int_Length = count($Arr_ConditionKeys);
		for ($i = 0; $i < $Int_Length; $i++)
		{
			//If the first position is not a group marker build set.
			if ((is_int($Arr_ConditionKeys[$i])) && count($Arr_WhereSet[$Arr_ConditionKeys[$i]]) > 1)
			{
//var_dump($Arr_ConditionKeys[$i]);
//var_dump($this->Arr_SchemaIndex);
				//*!*I ned to test that these array keys exist and if they don't handle the excpetion
				//Column name.
				$Str_Statement = ' `'.$this->Arr_SchemaIndex[$Arr_WhereSet[$Arr_ConditionKeys[$i]][0]]['table'].'`'
								.'.`'.$this->Arr_SchemaIndex[$Arr_WhereSet[$Arr_ConditionKeys[$i]][0]]['column'].'` ';

				//Condition type.
				switch ($Arr_WhereSet[$Arr_ConditionKeys[$i]][1])
				{
					case 'gt': $Str_Statement .= '>'; break;
					case 'lt': $Str_Statement .= '<'; break;
					case 'gte': $Str_Statement .= '>='; break;
					case 'lte': $Str_Statement .= '<='; break;
					case 'in': $Str_Statement .= 'IN'; break;
					default: $Str_Statement .= '=';
				}

				//If building an IN comparison assemble matching values.
				if ($Arr_WhereSet[$Arr_ConditionKeys[$i]][1] == 'in')
				{
					//If the IN values are an array build value set.
					if (is_array($Arr_WhereSet[$Arr_ConditionKeys[$i]][2]))
					{
						$Str_Statement .= " (";
						$Arr_InsKeys = array_keys($Arr_WhereSet[$Arr_ConditionKeys[$i]][2]);
						$Int_InsNumb = count($Arr_InsKeys);
						for($j = 0; $j < $Int_InsNumb; $j++)
						{
							//If the condition is set then make database statement.
							if (isset($Arr_WhereSet[$Arr_ConditionKeys[$i]][2][$Arr_InsKeys[$j]]))
							{

								//If the condition parameter is not a string or an integer handle excpetion.
								if ((!is_string($Arr_WhereSet[$Arr_ConditionKeys[$i]][2][$Arr_InsKeys[$j]]))
								&& (!is_int($Arr_WhereSet[$Arr_ConditionKeys[$i]][2][$Arr_InsKeys[$j]])))
								{
									global $CMD;
									$CMD->handle_exception('Where condition '.$Arr_WhereSet[$Arr_ConditionKeys[$i]][2][$Arr_InsKeys[$j]].' not a string', 'MW:101');
								}
								else
								{
									if ($this->Arr_ConnectConfig['driver'] == 'mysqli')
									{
										$Str_Statement .= "'".mysqli_real_escape_string($this->Res_Connection, $Arr_WhereSet[$Arr_ConditionKeys[$i]][2][$Arr_InsKeys[$j]])."'";
									}
									else
									{
										$Str_Statement .= "'".mysql_real_escape_string($Arr_WhereSet[$Arr_ConditionKeys[$i]][2][$Arr_InsKeys[$j]], $this->Res_Connection)."'";
									}

									$Str_Statement .= ($j < $Int_InsNumb - 1)? ", ": "";
								}
							}
							//Otherwise handle excpetion.
							else
							{
								global $CMD;
								$CMD->handle_excpetion('Where condition '.$Arr_InsKeys[$j].' not set', 'MW:101');
							}
						}

						$Str_Statement .= ")";
					}
					//Otherwise handle exception.
					else
					{
						global $CMD;
						$CMD->handle_exception('Where condition IN values are not an array', 'MW:101');
					}
				}
				//Otherwise build standard comparison statement.
				else
				{
				//*!*I ned to test that these array keys exist and if they don't handle the excpetion
					if ($this->Arr_SchemaIndex[$Arr_WhereSet[$Arr_ConditionKeys[$i]][0]]['format'] == 'string'
					|| $this->Arr_SchemaIndex[$Arr_WhereSet[$Arr_ConditionKeys[$i]][0]]['format'] == 'text'
					|| $this->Arr_SchemaIndex[$Arr_WhereSet[$Arr_ConditionKeys[$i]][0]]['format'] == 'binary'
					|| $this->Arr_SchemaIndex[$Arr_WhereSet[$Arr_ConditionKeys[$i]][0]]['format'] == 'integer'
					|| $this->Arr_SchemaIndex[$Arr_WhereSet[$Arr_ConditionKeys[$i]][0]]['format'] == 'float'
					|| $this->Arr_SchemaIndex[$Arr_WhereSet[$Arr_ConditionKeys[$i]][0]]['format'] == 'datetime')
					{
						if ($this->Arr_ConnectConfig['driver'] == 'mysqli')
						{
							$Str_Statement .= " '".mysqli_real_escape_string($this->Res_Connection, $Arr_WhereSet[$Arr_ConditionKeys[$i]][2])."' ";
						}
						else
						{
							$Str_Statement .= " '".mysql_real_escape_string($Arr_WhereSet[$Arr_ConditionKeys[$i]][2], $this->Res_Connection)."' ";
						}
					}
					else
					{
						$Str_Statement .= ' '.$Arr_WhereSet[$Arr_ConditionKeys[$i]][2];
					}
				}

				$Arr_Conditions[] = $Str_Statement;
			}
			//Otherwise build group.
			else
			{
				$Arr_Conditions[] = $this->build_where($Arr_WhereSet[$Arr_ConditionKeys[$i]], $Arr_ConditionKeys[$i]);
			}
		}

		//Assemble each condition statment into a group.
		if (count($Arr_Conditions) > 1)
		{
			$Str_Condition .= '('.implode(' '.strtoupper($Str_Group).' ', $Arr_Conditions).')';
		}
		else
		{
			//*!*This can create an empty condition and should be validated when the where is set to the query
			//I would say that it is the job of the driver to make this test and modify the array structure accordingly
			//I believe this is being done for AND conditions but not the OR condition.
			if (isset($Arr_Conditions[0]) && $Arr_Conditions[0])
			{
				$Str_Condition = $Arr_Conditions[0];
			}
		}

		return $Str_Condition;
	}

	//Formats query data, adding parenthises to string representations.
	// - $Arr_Query:				Reference to the query array
	// * Return:					VOID
	//*!*I need to test for other things like NULL and CURRENT_TIMESTAMP on this function I believe. I've noted this elsewhere, no longer useing timestamps btw.
	public function format_data(&$Arr_Query)
	{
		if ($Arr_Query['fields'] && $Arr_Query['type'] != 'select')
		{
			foreach ($Arr_Query['fields'] as $Str_Index => $Str_Value)
			{
				//*!*Do I have to format binary like string and text?
				if (isset($this->Arr_SchemaIndex[$Str_Index]['format'])
				&& ($this->Arr_SchemaIndex[$Str_Index]['format'] == 'string'
				|| $this->Arr_SchemaIndex[$Str_Index]['format'] == 'text'
				|| $this->Arr_SchemaIndex[$Str_Index]['format'] == 'binary'
				|| $this->Arr_SchemaIndex[$Str_Index]['format'] == 'integer'
				|| $this->Arr_SchemaIndex[$Str_Index]['format'] == 'float'
				|| $this->Arr_SchemaIndex[$Str_Index]['format'] == 'datetime'))
				{
					//If the string is empty assign it a null value.
					//*!*I really do this in the driver parent as part of the pre-query routine, if possible.
					if ($Str_Value === ''
					&& ($this->Arr_SchemaIndex[$Str_Index]['format'] == 'string'
					|| $this->Arr_SchemaIndex[$Str_Index]['format'] == 'text'
					|| $this->Arr_SchemaIndex[$Str_Index]['format'] == 'binary'))
					{
						$Arr_Query['fields'][$Str_Index] = "NULL";
					}
					else
					{
						if ($this->Arr_ConnectConfig['driver'] == 'mysqli')
						{
							$Arr_Query['fields'][$Str_Index] = "'".mysqli_real_escape_string($this->Res_Connection, $Str_Value)."'";
						}
						else
						{
							$Arr_Query['fields'][$Str_Index] = "'".mysql_real_escape_string($Str_Value, $this->Res_Connection)."'";
						}
					}
				}
			}
		}

		return;
	}




}

?>