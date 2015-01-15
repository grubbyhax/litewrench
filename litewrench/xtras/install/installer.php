<?php
//installer.php
//Installer file for MonkeyWrench.

class MW_Installer extends MW_Utility_Module
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   C O N S T A N T S                       //
///////////////////////////////////////////////////////////////////////////////

	//These are my MySQL hacks prior to setting up XQL.
	const CONST_DB_CREATE_TEXT		= ' `%s` TEXT NULL DEFAULT NULL';
	//const CONST_DB_CREATE_BINARY	= ' `%s` MEDIUMBLOB NULL DEFAULT NULL';
	const CONST_DB_CREATE_BINARY	= ' `%s` VARCHAR( 255 ) NULL DEFAULT NULL';
	const CONST_DB_CREATE_STRING	= ' `%s` VARCHAR( 255 ) NULL DEFAULT NULL';
	const CONST_DB_CREATE_INTEGER	= ' `%s` INT( 11 ) NULL DEFAULT NULL';
	const CONST_DB_CREATE_FLOAT		= ' `%s` FLOAT NULL DEFAULT NULL';
	const CONST_DB_CREATE_BOOLEAN	= ' `%s` TINYINT( 1 ) NULL DEFAULT NULL';
	const CONST_DB_CREATE_DATETIME	= ' `%s` DATETIME NULL DEFAULT NULL';



///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Installation folders.
	public $Str_TemplatesDir		= '';
	public $Str_ModelsDir			= '';

	private $Int_InstallStep	= 1;


///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

/*
	protected $Arr_Properties = array(
		'Int_InstallStep'		=> 1);
*/

///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	public function __construct()
	{
		$this->Str_TemplatesDir = MW_CONST_STR_DIR_INSTALL.'xtras/install/templates/';
		$this->Str_ModelsDir = MW_CONST_STR_DIR_INSTALL.'xtras/install/models/';
	}



///////////////////////////////////////////////////////////////////////////////
//                      C O R E   F U N C T I O N S                          //
///////////////////////////////////////////////////////////////////////////////

	//Gets a model object for the installation process.
	// * Return:				Model object for installation step form building
	public function get_install_model()
	{
		if ($this->Int_InstallStep == 7)
			return null;

		//Get the installation step model object.
		require($this->Str_ModelsDir.'step_'.$this->Int_InstallStep.'.php');
		$Str_Model = 'MW_Install_Model_Step_'.$this->Int_InstallStep;
		$Obj_Model = new $Str_Model();
		$Obj_Model->set_ref(99); //Fudge factor LOL

		return $Obj_Model;
	}

	//Gets a form object for the installation process.
	// - $Obj_Model:			Model to be used to bind fields to form
	// * Return:				Form object with fields bound to it
	public function get_install_form($Obj_Model)
	{
		//Get form helper.
		global $CMD;
		$Obj_Form = $CMD->helper('form')->name('install')->action($CMD->config('root_url'))->method('post');

		//Build form for current installation step.
		switch ($this->Int_InstallStep)
		{
			case 1:
				//Build default form.
				$Obj_Form->field('install', 'password')->bind($Obj_Model, 'install')
						->field('step', 'hidden')->bind($Obj_Model, 'step')->display(1);
				break;

			case 2:
				//Build default form.
				$Obj_Form->field('username', 'text')->bind($Obj_Model, 'username')
						->field('password', 'password')->bind($Obj_Model, 'password')
						->field('confirm', 'password')->bind($Obj_Model, 'password')
						->field('step', 'hidden')->bind($Obj_Model, 'step')->display(2);
				break;

			case 3:
				//Build default form.
				$Obj_Form->field('domain', 'text')->bind($Obj_Model, 'domain')->display('http://')
						->field('version', 'text')->bind($Obj_Model, 'version')->display('1.0')
						->field('timezone', 'select')->bind($Obj_Model, 'timezone')->range($CMD->helper('time')->timezones())
						->field('organisation', 'text')->bind($Obj_Model, 'organisation')
						->field('email', 'text')->bind($Obj_Model, 'email')
						->field('step', 'hidden')->bind($Obj_Model, 'step')->display(3);

				//If these values already exist in the configuration file assign them.
				if ($CMD->config('root_url'))
					$Obj_Form->field('domain')->display($CMD->config('root_url'));
				if ($CMD->config('version'))
					$Obj_Form->field('version')->display($CMD->config('version'));
				if ($CMD->config('timezone'))
					$Obj_Form->field('timezone')->display($CMD->config('timezone'));
				if ($CMD->config('organisation'))
					$Obj_Form->field('organisation')->display($CMD->config('organisation'));
				if ($CMD->config('inbox'))
					$Obj_Form->field('email')->display($CMD->config('inbox'));
				break;

			case 4:
				//Build default form.
				$Obj_Form->field('database', 'text')->bind($Obj_Model, 'database')
						->field('username', 'text')->bind($Obj_Model, 'username')->display('root')
						->field('password', 'password')->bind($Obj_Model, 'password')
						->field('location', 'text')->bind($Obj_Model, 'location')->display('localhost')
						->field('step', 'hidden')->bind($Obj_Model, 'step')->display(4);

				//If these values already exist in the configuration file assign them.
				if ($Arr_Databse = $CMD->config('x_database'))
					$Obj_Form->field('database')->display($CMD->config('x_database'));
				if ($CMD->config('x_username'))
					$Obj_Form->field('username')->display($CMD->config('x_username'));
				if ($CMD->config('x_password'))
					$Obj_Form->field('password')->display($CMD->config('x_password'));
				if ($CMD->config('x_location'))
					$Obj_Form->field('location')->display($CMD->config('x_location'));
				break;
			case 5:
				//Build default form.
				$Obj_Form->field('cms', 'checkbox')->bind($Obj_Model, 'cms')
						->field('step', 'hidden')->bind($Obj_Model, 'step')->display(5);
				break;
			case 6:
				//Build default form.
				$Obj_Form->field('step', 'hidden')->bind($Obj_Model, 'step')->display(6);
		}

		return $Obj_Form;
	}

	//Builds installation step interfaces to install system.
	// * Return:				Installation interface.
	public function install()
	{
		global $CMD;

		//Evaluate installation step.
		if (isset($_POST['step']) && $_POST['step'])
			$this->Int_InstallStep = $_POST['step'];

		//If the install password is not set or incorrect force step 1.
		if (!isset($_SESSION['install_password']) || ($this->Int_InstallStep > 1 && $_SESSION['install_password'] != $CMD->config('x_install')))
			$this->Int_InstallStep = 1;

		//If user is still going through the installation process get the current form.
		$Obj_Form = null;
		$Arr_Extras = array();

		//Get installation form.
		$Obj_Model = $this->get_install_model();
		$Obj_Form = $this->get_install_form($Obj_Model);

		//Evaluate form move forward a step if valid.
		if ($Obj_Form->post())
		{
			$Bol_Proceed = true;

			//Do post validation form data checks.
			switch ($this->Int_InstallStep)
			{
				case 1:
					//If the install password deos not match the configuration file create error.
					if ($Obj_Form->text('install') != $CMD->config('x_install'))
					{
						$Obj_Form->field('install')->set_error('Install password incorrect');
						$Bol_Proceed = false;
					}
					//Otherwise session up the password.
					else
					{
						$_SESSION['install_password'] = $Obj_Form->text('install');
					}
					break;

				case 2:
					//*!*I have to test aginst the master user in the database which can only be done at later point in time.
					//So, I'm going to leave this unchecked for now but must return to it when databasing is developed.
					//This is more relevent to when users are doing an update installation of new models(model objects)
					//*!*Remember to add a test to match the password confirmation in the form building.
					$_SESSION['master_username'] = $Obj_Form->text('username');
					$_SESSION['master_password'] = $Obj_Form->text('password');
					break;

				case 3:
					$_SESSION['owner_organisation'] = $Obj_Form->text('organisation');
					$_SESSION['owner_domain'] = $Obj_Form->text('domain');
					$_SESSION['owner_email'] = $Obj_Form->text('email');
					$_SESSION['owner_version'] = $Obj_Form->text('version');
					$_SESSION['owner_timezone'] = $Obj_Form->text('timezone');
					break;

				case 4;
					//Make a connection to the database to see if these settings are right.
					//18 also need to check the db user has create privillages. connecting is not enough.
					if ($CMD->config('driver') == 'mysqli')
					{
						$Res_DbConnect = @mysqli_connect($Obj_Form->text('location'), $Obj_Form->text('username'), $Obj_Form->text('password'), $Obj_Form->text('database'));
					}
					else
					{
						$Res_DbConnect = @mysql_connect($Obj_Form->text('location'), $Obj_Form->text('username'), $Obj_Form->text('password'), $Obj_Form->text('database'));
					}

					if ($Res_DbConnect)
					{
						$_SESSION['database_database'] = $Obj_Form->text('database');
						$_SESSION['database_username'] = $Obj_Form->text('username');
						$_SESSION['database_password'] = $Obj_Form->text('password');
						$_SESSION['database_location'] = $Obj_Form->text('location');
					}
					else
					{
						$Str_Error = ($CMD->config('driver') == 'mysqli')? $Str_Error = mysqli_connect_error(): mysql_error();
						$Arr_Extras['error'] = 'Failed to connect to database, check settings. Error message: <br />"'.$Str_Error.'"';
						$Bol_Proceed = false;
					}
					break;

				case 5:
					//Not sure I need to do anything with the checksum, will probably have to do something at a later date.
					//For example I'll have to check out some of the libraries that are installed and throw up a road block if they are not installed.
					//Check that config file is writeable, do this at the final step.
					$Arr_Extras['error'] = '';
					break;

				case 6:
					//If there are problems with the checksum, build a report,
					//*!*This is a little tricky because we are adding a step(interface) if the checksum has problems with it.
					//So need to modify the final form(maybe through the interface template) to run checksum again IE ->display(5)
					//So this final 'install' form is open to alot of variablitiy...
					if (false)
					{
						$Obj_Form->field('step')->display(5);
						$Arr_Extras['submit'] = 'Run Check sum'.
						//Will have to throw in more vars to alter second last template for a resubmit.
						$Bol_Proceed = false;
					}
					//Otherwise skip ahead to the installation step.
					else
					{
						//Some of these need to be selected for installation in the interface.
						$this->install_database();
					}
					//Otherwise build final confirmation of installation.
					//*!*Maybe with a redirect if the user has installed the CMS

					//Do installation routine.
					//If the installation failed we'll have to send the user back a step or, maybe back to the beginning.

					$Arr_Extras['results'] = 'Here I\'ll display the results of the system install, IE; what was installed/changed';

					//If installation is a success.
					if (true)
					{
						//Remove session.
						//unset($_SESSION);
						session_destroy();
						//*!*I will need to keep the user's login details for the cms but
						//I must remove all the variables which are used to build this installer.
					}
					//Otyherwise return them to the last step with errors.
					else
					{
						$this->Int_InstallStep = 6;
						$Obj_Form = $this->get_install_form($this->get_install_model());
						$Arr_Extras['error'] = 'Any errors we picked up';
					}
					break;
			}

			//If the installation is still in process build form for next step.
			if ($Bol_Proceed && $this->Int_InstallStep < 7)
			{
				$this->Int_InstallStep++;
				$Obj_Form = $this->get_install_form($this->get_install_model());
			}
			//Else if the installation is complete no more forms are needed.
			elseif($Bol_Proceed && $this->Int_InstallStep == 7)
			{
				$Obj_Form = null;
			}
		}

		//Build interface with values.
		$Str_InterfaceFile = $this->Str_TemplatesDir.'interface_'.$this->Int_InstallStep.'.html';
		$Str_InstallInterface = fread(fopen($Str_InterfaceFile, 'r'), filesize($Str_InterfaceFile));
		$Arr_InterfaceValues = array('form'=>$Obj_Form, 'extras'=>$Arr_Extras);

		//Not sure what this buildlayout variable is doing, should checkj and get rid of it.
		$this->Str_BuildLayout = $Str_InstallInterface;
		$Str_InstallInterface = $this->bind($Arr_InterfaceValues)->build($Str_InterfaceFile);

		//Add interface wrapper, ie hack inherit, more 'hiding the decline' in installer 'robustness' LMAO!
		$Str_WrapperFile = $this->Str_TemplatesDir.'wrapper.html';
		$Str_InstallInterface = $this->build($Str_WrapperFile);

		return $Str_InstallInterface;
	}


///////////////////////////////////////////////////////////////////////////////
//              D A T A   S A V I N G   F U N C T I O N S                    //
///////////////////////////////////////////////////////////////////////////////

	//Installs the monkeywrench database onto the pre-setup database.
	//*!*I'll need to have logic here to allow users to run the install to add new components(models) to the database
	// * Return:				True if the database was sucessfully setup, otherwise false
	public function install_database()
	{
		$Bol_QuerySuccess = false;
		global $CMD;

		//Assemble model names.
		$Arr_ModelFileNames = array();
		$Obj_File = $CMD->helper('file');
		$Str_DirModels = MW_CONST_STR_DIR_INSTALL.'models/';

		//Get all the models in the model directory.
		//*!*Make sure that the models are named correctly and they are bein formatted using the revised modeel object.
		$Arr_ModelNames = $Obj_File->get_files_in_dir($Str_DirModels);
		foreach ($Arr_ModelNames as $Str_FileName)
		{
			$Arr_ModelName = explode('.', $Str_FileName);
			$Arr_ModelFileNames[] = $Arr_ModelName[0];
		}

		//For each model file create a new model build create query from data schema.
		$Arr_CreateTable = array();
		foreach ($Arr_ModelFileNames as $Str_FileName)
		{
			$Obj_Model = $CMD->model($Str_FileName);
			$Arr_DataSchema = $Obj_Model->get_data_schema();

			//If there is no data schem handle exception.
			if (!$Arr_DataSchema)
			{
				//*!*Turn this exception one after all models have been properly developed.
				//$CMD->handle_exception('Model '.$Str_FileName.' does not have a valid data schema', 'MW:101');
				continue;
			}

			//Build create table query.
			//*!*This is where my MySQL fudge factor comes in.
			//I'll probably need to add the key index before fixing this routine, though I didn't have it in initially...
			$Str_CreateTable = 'CREATE TABLE `'.$_SESSION['database_database'].'`.`'.str_replace('/', '_', $Str_FileName).'` (
			`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`view` VARCHAR( 255 ) NOT NULL DEFAULT \'0\' ,
			`edit` VARCHAR( 255 ) NOT NULL DEFAULT \'0\' ,
			`lock` VARCHAR( 255 ) NOT NULL DEFAULT \'0\' ,
			`status` VARCHAR( 255 ) NOT NULL DEFAULT \'0\' ,
			`enable` DATETIME NULL DEFAULT NULL ,
			`disable` DATETIME NULL DEFAULT NULL ,
			`created` DATETIME NULL DEFAULT NULL ,
			`modified` DATETIME NULL DEFAULT NULL ,';

			//Add a field for each entry in the the data schema.
			$Int_Fields = 9;
			foreach ($Arr_DataSchema as $Str_FieldName => $Arr_FieldSchema)
			{
				if ($Str_FieldName == 'id'
				|| $Str_FieldName == 'view'
				|| $Str_FieldName == 'edit'
				|| $Str_FieldName == 'lock'
				|| $Str_FieldName == 'status'
				|| $Str_FieldName == 'enable'
				|| $Str_FieldName == 'disable'
				|| $Str_FieldName == 'created'
				|| $Str_FieldName == 'modified') continue;

				switch ($Arr_FieldSchema['format'])
				{
					case 'string': $Str_CreateTable .= sprintf(self::CONST_DB_CREATE_STRING, $Arr_FieldSchema['column']); break;
					case 'integer': $Str_CreateTable .= sprintf(self::CONST_DB_CREATE_INTEGER, $Arr_FieldSchema['column']); break;
					case 'float': $Str_CreateTable .= sprintf(self::CONST_DB_CREATE_FLOAT, $Arr_FieldSchema['column']); break;
					case 'boolean': $Str_CreateTable .= sprintf(self::CONST_DB_CREATE_BOOLEAN, $Arr_FieldSchema['column']); break;
					case 'text': $Str_CreateTable .= sprintf(self::CONST_DB_CREATE_TEXT, $Arr_FieldSchema['column']); break;
					case 'binary': $Str_CreateTable .= sprintf(self::CONST_DB_CREATE_BINARY, $Arr_FieldSchema['column']); break;
					case 'datetime': $Str_CreateTable .= sprintf(self::CONST_DB_CREATE_DATETIME, $Arr_FieldSchema['column']); break;
				}

				//Add comma to query.
				$Int_Fields++;
				if ($Int_Fields < count($Arr_DataSchema))
					$Str_CreateTable .= ' ,';

			}

			if ($Str_KeyField = $Obj_Model->get_key_field())
				$Str_CreateTable .= ', UNIQUE ( `'.$Str_KeyField.'` ) ';

			$Str_CreateTable .= ' ) ENGINE = MYISAM ;';
			$Arr_CreateTable[] = $Str_CreateTable;
		}

		//Connect to database.
		$Obj_Database = null;
		if ($CMD->config('driver') == 'mysqli')
		{
			$Obj_Database = new mysqli($_SESSION['database_location'], $_SESSION['database_username'], $_SESSION['database_password'], $_SESSION['database_database']);
		}
		else
		{
			$Obj_Database = mysql_connect($_SESSION['database_location'], $_SESSION['database_username'], $_SESSION['database_password']);
		}

		//Execute database queries.
		//*!*Obviously I need to keep track of both successes and errors.
		foreach ($Arr_CreateTable as $Str_CreateTableQuery)
		{
			$Bol_QuerySuccess;

			if ($CMD->config('driver') == 'mysqli')
			{
				if ($Obj_Database->query($Str_CreateTableQuery) === true)
				{
					$Bol_QuerySuccess = true;
				}
			}
			else
			{
				if (mysql_query($Str_CreateTableQuery, $Obj_Database) == true)
				{
					$Bol_QuerySuccess = true;
				}
			}
		}

		//Close database connection.
		if ($CMD->config('driver') == 'mysqli')
		{
			$Obj_Database->close();
		}
		else
		{
			mysql_close($Obj_Database);
		}

		return $Bol_QuerySuccess;
	}

}

?>