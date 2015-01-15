<?php
//user.php
//User extension module file.
//Design by David Thomson at Hundredth Codemonkey.

/*
For this module to be extensible all forks in the logic on ->action(value) need to be wrapped into a function
This way I can just call a function of the module with the action parameters. Look at the store module for
the basic structure which will be followed as standard for developing modules.


Just a a side note which needs to be implemented on modules:
The redirect call which exists in the logic needs to be pulled out of the functions
and put as a callback for the module.
So setting a redirect doesn't actually do the redirect but sets the path, or part of the path
to which the redirect must be taken. Having a split URL building mechanism will allow moduls
calling other modules to contribute to the redirect call so modules are not 'stuck' in a static
position within the website hierarchy, which is the point of the framework in the first place.

I've decided to get rid of the redirect, or maybe set it's value as a variable
so that other modules can largely seize the control structure of the application from this module
*/


///////////////////////////////////////////////////////////////////////////////
//                     M O D U L E   C O N S T A N T S                       //
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
//                        M O D U L E   C L A S S                            //
///////////////////////////////////////////////////////////////////////////////



class MW_Module_User extends MW_Utility_Module
{


///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Cleanup any module processes.
	private $Bol_Cleanup		= false;



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	public function __construct()
	{
		//Turn on debugging for the module.
		/*!*This should be in the module parent class as a method.
		global $GLB_DEBUGGER;
		if (LOGIN_CONST_INT_DEBUG_MODULE)
		{
			$GLB_DEBUGGER->add_dir(LOGIN_CONST_STR_DIR_ROOT);
		}
		*/
	}



///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

/*
Just a quick note about the implementation.
I need to make sure I can achieve the following
1. the user can log in, then they get the option of seeing their account
2. the user gets the option of recovering/changing their password via email
3. the user gets the opportunity to signup to the website
	this signup process is goin to happen in a number of different locaions in the application control logic
4. the user gets the opportunity to  create a new account
	this accont is going to be linked to sopping carts and orders of the user

This means that the user module is going to be called in the store module so that I( need an API to
proces this functionallity. I'm going to have to make sure that funcion hooks re in place and that
he moidule can be treated properly as an obvject andtat the forms shich it creates are going to be able
to have a layer of logic which can be called.
Obvioulsy I don't wnt to abstarct this too highly as it would take away from use flexibility.

*/


	//Responds to a module request, the return value replaces a builder widget
	public function process_request()
	{
		//Get module call.
		switch ($this->value('control'))
		{
			case 'login':		echo $this->user_login(); break;
			case 'logout': 		echo $this->user_logout(); break;
			case 'display':		echo $this->user_display(); break;
			case 'navigate':	echo $this->user_navigate(); break;
			case 'register':	echo $this->user_register($this->value('type')); break;
			case 'edit':		echo $this->user_edit($this->value('value')); break;
			case 'recover':		echo $this->password_recover(); break;
			case 'reset':		echo $this->password_reset(); break;
		}

		return;
	}

	//Builds the user login form.
	// * NB: Also binds user login status to the builder object calling this module
	//*!*I should have a redirect value in this function that, if exists redirects to that url
	public function user_login()
	{
		//If the user is alreay lgged in do nothing.
		if ($this->user())
			return;

		$Obj_User = $this->parcel('user');

		//Build login form.
		$Obj_Form = $this->helper('form')->name('user_login')->action($this->config('full_path'))->method('post')
						->field('username', 'text')->bind($Obj_User, 'username')
						->field('password', 'password')->bind($Obj_User, 'password');

		$Bol_ShowAccount = false;
		if ($Obj_Form->post())
		{
			//Might add some login metrics here. Do later.
			//*!*Will need drop down boxes with links to contact form and password recovery script in this module.

			//If there is no username in the database create error.
			$Arr_MatchUsername = array('username', 'eq', $Obj_Form->text('username'));
			if (!$Arr_UserData = $this->collection($Obj_User)->where($Arr_MatchUsername)->limit(1)->fetch())
			{
				$Obj_Form->field('username')->set_error('Username not found');
			}
			//If the password is incorrect create error.
			elseif ($Arr_UserData[0]['password'] != md5($Obj_Form->text('password')))
			{
				$Obj_Form->field('password')->set_error('Password not valid');
			}
			//Otherwise log user in and take them to the account page.
			else
			{
				$this->login($Obj_User);
				$this->bind(array('user', 'true'));
				$Bol_ShowAccount = true;
			}
		}

		if ($this->user())
		{
			//If the user has just logged in take them to the account admin page.
			if ($Bol_ShowAccount)
			{
				//$this->redirect($this->config('root_url').'/account/');
				return;
			}
			//Else if the client has successfully logged in show their status.
			else
			{
				return 'Logged in as '.$this->user('name');
			}
		}
		//Otherwise display the login form.
		else
		{
			return $this->plan($this->helper('page')->layout('account/form_login'))
						->bind(array('form'=>$Obj_Form))->build();
		}
	}

	//Log user out of their account.
	// - Mix_Redirect:		If boolean redirect value, otherwise if array
	//find if the paths need to be redirected from
	public function user_logout()
	{
		//*!*Might need to remove other session values here.
		$this->logout();
		$this->session('access', '');

		//If the user is in an account management area send them to the homepage.
		//*!* Need to fix these file paths, might include an exclusion path list for this function
		if ($this->config('file_path') == '/account' || $this->config('file_path') == '/admin')
		{
			$this->redirect($this->config('root_url'));
		}
	}

	//Build global user status information.
	// NB: This is a convenience function for building application functionality.
	public function user_display()
	{
		return $this->plan($this->helper('page')->layout('account/show_user_display'))->build();
	}

	//Build global user navigation menu items.
	// NB: This is a convenience function for building application functionality.
	public function user_navigate()
	{
		return $this->plan($this->helper('page')->layout('account/show_user_navigate'))->build();
	}

	//Build user registration form.
	// - $Bol_LoginUser:		logs user in on successful registration, default = false(don't log user in)
	// * NB: $Bol_LoginUser is passed as a string.
	public function user_register($Bol_LoginUser='false')
	{
		$Obj_User = $this->parcel('user');

		//Build login form.
		$Obj_Form = $this->helper('form')->name('user_register')->action($this->config('full_path'))->method('post')
						->field('username', 'text')->bind($Obj_User, 'username')
						->field('email', 'text')->bind($Obj_User, 'email')
						->field('password', 'password')->bind($Obj_User, 'password');

		$Bol_RegisterSuccess = false;
		if ($Obj_Form->post())
		{
			//If there is a user of with the same account name show error.
			$Arr_MatchUsername = array('username', 'eq', $Obj_Form->text('username'));
			if ($Arr_UserData = $this->collection($Obj_User)->where($Arr_MatchUsername)->limit(1)->fetch())
			{
				$Obj_Form->field('username')->set_error('Username already taken');
			}
			else
			{
				//Send an email to user and administrator confirming new registration.
				//*!*I need separate email templates which can show password only to user
				$Str_Email = $this->parcel('template', 'email/user_registration')->data('file');
				$Arr_Email = array('user'=>$Obj_User->data());
				$Obj_Mailer = $this->helper('mailer')->subject('Website registration '.$this->config('root_url'))
					->message($this->plan($Str_Email)->bind($Arr_Email)->build());
				$Obj_Mailer->to($Obj_User->data('email'))->from($this->config('inbox'))->send();
				$Obj_Mailer->to($this->config('inbox'))->from($Obj_User->data('email'))->reply_to($Obj_User->data('email'))->send();

				//Redirect user to their account area.
				//*!*Not sure where I'm redirecting to at this point in time, not sure it's a good plan here anyway
				//do it in other application logic.
				//$this->redirect($this->config('root_url').'/account/');

				$Bol_RegisterSuccess = true;
			}
		}

		if ($Bol_RegisterSuccess)
		{
			//Save user.
			$Obj_Form->field('password')->filter('encrypt_md5');
			$this->save($Obj_User);

			//Log the user in.
			if ($Bol_LoginUser == 'true')
			{
				$this->login($Obj_User);
				$this->bind(array('user', 'true')); //*!* this call is incorrect
			}

			//I would like to add a module variable here to let the plugin know that the registration is successful.
			//$this->bind(array('registered'=>true));
			//*!*for now the api is not letting me access module variables, I will need to modify it so that
			//the convenience is pulled back a bit to allow more controll. Register succes can be determined
			//by a lack of module output.
			return;
		}
		//Otherwise display registration form.
		else
		{
			return $this->plan($this->helper('page')->layout('form/user_register'))
						->bind(array('form'=>$Obj_Form))->build();
		}
	}

	//Edits a user which has already been edited.
	// - $Int_UserId:			Id of the user to edit.
	// * NB: $Int_UserId must be validated elsewhere at ths point in time
	public function user_edit($Int_UserId)
	{
		$Obj_User = $this->parcel('user', (int) $Int_UserId);

		//Build attribute edit form.
		$Obj_Form = $this->helper('form')->name('edit_user')->action($this->config('full_path'))->method('post')
						->field('username', 'text')->bind($Obj_User, 'username')->display($Obj_User->data('username'))
						->field('email', 'text')->bind($Obj_User, 'email')->display($Obj_User->data('email'))
						->field('password', 'password')->bind($Obj_User, 'password');

		if ($Obj_Form->post())
		{
			//*!*I need to do similar checks here to the registration function
			//-test for same username, email but negate those values for this parcel on the test
			//Not doing it now, no time, even 5min!
			$this->save($Obj_User);
			$this->redirect($this->config('root_url').'/account/edit/');
		}
		else
		{
			return $this->plan($this->helper('page')->layout('account/form_user_edit'))
						->bind(array('form'=>$Obj_Form))->build();
		}
	}

	//Recovers user password with an email message to reset
	public function password_recover()
	{
		//*!*Need the session variable for the pasword reset. I should probably store this is the database.
		//I'm thinking of just using an encrpyt of the username and email address with salt in it.

		//Build login form.
		$Obj_Form = $this->helper('form')->name('user_recover')->action($this->config('full_path'))->method('post')
						->field('email', 'text')->bind($Obj_User, 'email');

		$Bol_RecoverSuccess = false;
		if ($Obj_Form->post())
		{
			//If there is a user of with the same account name show error.
			$Arr_MatchEmail = array('email', 'eq', $Obj_Form->text('email'));
			if (!$Arr_UserData = $this->collection($Obj_User)->where($Arr_MatchEmail)->limit(1)->fetch())
			{
				$Obj_Form->field('email')->set_error('Email address not found contact admin for help');
			}
			else
			{
				$Bol_RecoverSuccess = true;
			}
		}

		//If password recovery succeeed display confirmation and send email.
		//*!*not done yet
		if ($Bol_RecoverSuccess)
		{
			//send emil with recovery session id

			return $this->plan($this->helper('page')->layout('form/recover_confirm'))->bind()->build();
		}
		//Otherwise build recovery form.
		else
		{
			return $this->plan($this->helper('page')->layout('form/recover_password'))
						->bind(array('form'=>$Obj_Form))->build();
		}
	}

	//Resets the user password.
	//*!*I'm going to come back to this, this is low priority.
	public function password_reset()
	{
		//I need to match the session/recover id to the reset
		$Obj_User = $this->parcel('user');

		//Build login form.
		$Obj_Form = $this->helper('form')->name('user_reset')->action($this->config('full_path'))->method('post')
						->field('email', 'text')->bind($Obj_User, 'email')
						->field('password', 'text')->bind($Obj_User, 'password');

		$Bol_ResetSuccess = false;
		if ($Obj_Form->post())
		{
			//If there is a user of with the same account name show error.
			$Arr_MatchEmail = array('email', 'eq', $Obj_Form->text('email'));
			if (!$Arr_UserData = $this->collection($Obj_User)->where($Arr_MatchEmail)->limit(1)->fetch())
			{
				$Obj_Form->field('email')->set_error('Email address not found contact admin for help');
			}
			else
			{
				$Bol_ResetSuccess = true;
			}
		}

		if ($Bol_ResetSuccess)
		{
			//Need to load the user by username/this needs to be passed to and from the email as a get var.
			//Save and login user.
			$Obj_Form->field('password')->filter('encrypt_md5');
			$this->save($Obj_User);
			$this->login($Obj_User);
			$this->bind(array('user', 'true'));
		}
	}

	//Gets the recovery value of the module exception with code $Int_Code
	// - $Int_Code:				Exception code integer
	public function handle_exception($Int_Code)
	{
		$Var_Recovery = null;

		//Get recovery value.
		switch ($Int_Code)
		{
			case LOGIN_CONST_INT_EXCEPTION_DEFAULT: $Var_Recovery = false; break;
			case LOGIN_CONST_INT_EXCEPTION_REQUEST: $Var_Recovery = 'Empty request!'; break;
			default: $Var_Recovery = '';
		}

		return $Var_Recovery;
	}

	public function define_caching(){}



///////////////////////////////////////////////////////////////////////////////
//                 U T I L I T Y   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

}

?>