<?php
//access.php
//Access model class file
//*!*this is to be removed, must test before removing.
//This is basically the user groups but they can also be attached to any type of object to cntrol what that
//object has access to in the database.
//for the cms I will be ading permission objects against the users and the uploads folders.
//This creates both user groups and folder permisions for viewing and editing.
//Important to note that any model can be given and access entry, this is usually for workfolw groups assigned
//to users to override their direct 'access' values.

//Not sure if the credential key is needed here.

//Access values are tested as bitwise operations against the view and edit

class MW_Model_Access extends MW_Utility_Model
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Parcel database data table schema.
	protected $Arr_TableSchema	= array(
		'model_id'			=> array(
			'column'			=> 'model_id',
			'format'			=> 'integer',
			'lookup'			=> 'key',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '^[\w]+$'),
					'error'		=> 'Permission key must be a word'))),
		'type'				=> array(
			'column'			=> 'type',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '^[\w]+$'),
					'error'		=> 'Permission data type must be a word'))),
		'name'				=> array(
			'column'			=> 'name',
			'format'			=> 'string',
			'default'			=> '',
			'validate'			=> array(
				array(
					'match'		=> array('regex', '^[\w]+$'),
					'error'		=> 'Permission data name must be a word'))),
		'view_flags'		=> array(
			'column'			=> 'view_flags',
			'format'			=> 'integer',
			'default'			=> 0,
			'validate'			=> array(
				array(
					'match'		=> array('regex', '^[0-9]+$'),
					'error'		=> 'View permssion must be an integer'))),
		'edit_flags'		=> array(
			'column'			=> 'edit_flags',
			'format'			=> 'integer',
			'default'			=> 0,
			'validate'			=> array(
				array(
					'match'		=> array('regex', '^[0-9]+$'),
					'error'		=> 'Edit permssion must be an integer'))));



///////////////////////////////////////////////////////////////////////////////
//            P A R C E L   H A N D L I N G   F U N C T I O N S              //
///////////////////////////////////////////////////////////////////////////////


}

?>