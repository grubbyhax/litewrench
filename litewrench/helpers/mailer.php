<?php
//mailer.php
//Mailer helper class file

class MW_Helper_Mailer extends MW_Utility_Helper
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	//Header regex matches.
	private $Reg_HeaderTo				= '';
	private $Reg_HeaderFrom				= '';
	private $Reg_HeaderCarbonCopy		= '';
	private $Reg_HeaderBlindCarbonCopy	= '';
	private $Reg_HeaderReplyTo			= '';
	private $Reg_HeaderReturnPath		= '';

///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////

//additional properties
//sent - can be used to debug the properties set
//I need to have the option of saving the email if it doesn't get sent, it can't be a default option for secutrity reasons.
//*!*I think I need callback functionallity(IE the property)

	protected $Arr_Properties	= array(
		'Arr_EmailTo'			=> array(),
		'Str_EmailFrom'			=> '',
		'Str_EmailMessage'		=> '',
		'Str_EmailSubject'		=> '',
		'Arr_EmailHeaders'		=> array(),
		'Arr_EmailAttachments'	=> array());

//With receivers I'll make the decision to smtp if the list is more than 1
//I will kepp the option of alocating the smtp option for a siingle email if the user desires it.

///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Class constructor
	// * Return:					SELF
	public function __construct()
	{
		//Set up class regex variables.
		$this->Reg_HeaderTo					= '/'.MW_REG_EMAIL.'/';
		$this->Reg_HeaderFrom				= '/'.MW_REG_EMAIL.'/';
		$this->Reg_HeaderCarbonCopy			= '/[\w\W]/';
		$this->Reg_HeaderBlindCarbonCopy	= '/[\w\W]/';
		$this->Reg_HeaderReplyTo			= '/'.MW_REG_EMAIL.'/';
		$this->Reg_HeaderReturnPath			= '/[\w\W]/';

		//Set email defaults.
		$this->reset();

		return $this;
	}

	public function reset()
	{
		return $this;
	}


///////////////////////////////////////////////////////////////////////////////
//                       M A I L E R   M E T H O D S                         //
///////////////////////////////////////////////////////////////////////////////

	//Sends email/s to all addresses on the recievers
	// * Return:				True if email was sent, otherwise false
	// NB: If emails are turned off this function will do nothing and return true
	public function send()
	{
		//First i need to check that the email properties are valid.
		//If they are not valid try to correct some of the obvious ommissions.
		//*!*I'll also need to clean up the headers to remove the whitespace and other unwanted characters.
		//*!*Here's a quick hacky thang.
		//*!*I'm looking to de/recouple the entire properties set for debugging here.
		$Arr_NewHeaders = $this->Arr_EmailHeaders;

		if (!isset($Arr_NewHeaders['Reply-To']))
			$Arr_NewHeaders['Reply-To'] = $this->Str_EmailFrom;

		$this->Arr_EmailHeaders = $Arr_NewHeaders;


		//*!*Just do a fucking send already and clean this shit up later.
		//This is just fucking quick and nasty, ouch
		//*!*I also need to apply htmlspecialchars() according to mime version. Will need a parameter to control it's use.
		//Build email headers
		$Str_Headers	= 'MIME-Version: 1.0'."\r\n"
.'Content-type: text/html; charset=iso-8859-1'."\r\n"
.'From: '.$this->Str_EmailFrom."\r\n"
.'Reply-To: '.$this->Arr_EmailHeaders['Reply-To']."\r\n"
.'X-Mailer: PHP/'.phpversion();

		//*!*Maybe I should capture the error and insert it into the exception handling... or, it could be pulled out of the error logs.
		$Str_EmailReceiver = $this->Arr_EmailTo[0];

		//If the email in not turned off and failed to be sent handle exception.
		//*!* I wilol need to do some error suppression here.
  		global $CMD;
		if (($CMD->config('email') != false)
  		&& (!mail($Str_EmailReceiver, $this->Str_EmailSubject, $this->Str_EmailMessage, $Str_Headers)))
		{
			$CMD->handle_exception('Email failed to be sent to user '.$Str_EmailReceiver, 'MW:101');
			return false;
		}

		return true;
	}

///////////////////////////////////////////////////////////////////////////////
//          P R O P E R T Y   S E T T I N G   F U N C T I O N S              //
///////////////////////////////////////////////////////////////////////////////

	//Sets the email subject line property.
	// - $Str_Subject:			Email subject property to set
	// * Return:				SELF
	//*!*Do I need to run a filter of headers from the subject line?
	public function subject($Str_Subject)
	{
		//If the subject is not a string handle exception.
		if (!is_string($Str_Subject))
		{
			global $CMD;
			$CMD->handle_exception('Email subject is not a string', 'MW:1010');
			return $this;
		}

		//Set message.
		$this->Str_EmailSubject = $Str_Subject;

		return $this;
	}

	//Sets the email message property.
	// - $Str_Message:			Email message property to set
	// * Return:				SELF
	public function message($Str_Message)
	{
		//If the message is not a string handle exception.
		if (!is_string($Str_Message))
		{
			global $CMD;
			$CMD->handle_exception('Email message is not a string', 'MW:1010');
			return $this;
		}
		
		//Filter potential headers from message.
		//*!*I need to go back to these regular expressions, they look a little weak case-wise to me.
		$Arr_IllegalHeaders = array("/to\:/i", "/from\:/i", "/bcc\:/i", "/cc\:/i", "/Content\-Transfer\-Encoding\:/i", "/Content\-Type\:/i", "/Mime\-Version\:/i");
		$Str_Message = preg_replace($Arr_IllegalHeaders, '', $Str_Message);

		//Set message.
		$this->Str_EmailMessage = $Str_Message;

		return $this;
	}

	//Sets the To header property.
	// - $Str_To:				Valid string for a To email header
	// - $Bol_Override:			Override the current recipients
	// * Return:				SELF
	public function to($Str_To, $Bol_Override=true)
	{
		//If the To header is not a string handle exception
		if (!is_string($Str_To))
		{
			global $CMD;
			$CMD->handle_exception('Email To header is not a string', 'MW:1010');
			return $this;
		}

		//If the To header in invalid handle exception.
		if (preg_match($this->Reg_HeaderTo, $Str_To, $Arr_Matches) && $Arr_Matches[0] != $Str_To)
		{
			global $CMD;
			$CMD->handle_exception('Email To header string invalid: '.$Str_To, 'MW:1010');
			return $this;
		}

		//Decouple header properties.
		$Arr_NewEmailTo = $this->Arr_EmailTo;

		//Add header property.
		if ($Bol_Override)
			$Arr_NewEmailTo = array($Str_To);
		else
			$Arr_NewEmailTo[] = $Str_To;

		//Recouple header properties.
		$this->Arr_EmailTo = $Arr_NewEmailTo;

		return $this;
	}

	//Sets the From header property.
	// - $Str_From:				Valid string for a From email header
	// * Return:				SELF
	public function from($Str_From)
	{
		//If the From header is not a string handle exception
		if (!is_string($Str_From))
		{
			global $CMD;
			$CMD->handle_exception('Email From header is not a string', 'MW:1010');
			return $this;
		}

		//If the From header in invalid handle exception.
		if (preg_match($this->Reg_HeaderFrom, $Str_From, $Arr_Matches) && $Arr_Matches[0] != $Str_From)
		{
			global $CMD;
			$CMD->handle_exception('Email From header string invalid: '.$Str_From, 'MW:1010');
			return $this;
		}

		//Add header property.
		$this->Str_EmailFrom = $Str_From;

		return $this;
	}

	//Sets the Carbon Copy header property.
	// - $Str_CarbonCopy:		Valid string for a Cc email header
	// * Return:				SELF
	public function cc($Str_CarbonCopy)
	{
		//If the Cc header is not a string handle exception
		if (!is_string($Str_CarbonCopy))
		{
			global $CMD;
			$CMD->handle_exception('Email Cc header is not a string', 'MW:1010');
			return $this;
		}

		//If the Cc header in invalid handle exception.
		if (preg_match($this->Reg_HeaderCarbonCopy, $Str_CarbonCopy, $Arr_Matches) && $Arr_Matches[0] != $Str_CarbonCopy)
		{
			global $CMD;
			$CMD->handle_exception('Email Cc header string invalid: '.$Str_CarbonCopy, 'MW:1010');
			return $this;
		}

		//Decouple header properties.
		$Arr_NewHeaders = $this->Arr_EmailHeaders;

		//Add header property.
		$Arr_NewHeaders['Cc'] = $Str_CarbonCopy;

		//Recouple header properties.
		$this->Arr_EmailHeaders = $Arr_NewHeaders;

		return $this;
	}

	//Sets the Blind Carbon Copy header property.
	// - $Str_BlindCarbonCopy:	Valid string for a Bcc email header
	// * Return:				SELF
	public function bcc($Str_BlindCarbonCopy)
	{
		//If the Bcc header is not a string handle exception
		if (!is_string($Str_BlindCarbonCopy))
		{
			global $CMD;
			$CMD->handle_exception('Email Bcc header is not a string', 'MW:1010');
			return $this;
		}

		//If the Bcc header in invalid handle exception.
		if (preg_match($this->Reg_HeaderBlindCarbonCopy, $Str_BlindCarbonCopy, $Arr_Matches) && $Arr_Matches[0] != $Str_BlindCarbonCopy)
		{
			global $CMD;
			$CMD->handle_exception('Email Bcc header string invalid: '.$Str_BlindCarbonCopy, 'MW:1010');
			return $this;
		}

		//Decouple header properties.
		$Arr_NewHeaders = $this->Arr_EmailHeaders;

		//Add header property.
		$Arr_NewHeaders['Bcc'] = $Str_BlindCarbonCopy;

		//Recouple header properties.
		$this->Arr_EmailHeaders = $Arr_NewHeaders;

		return $this;
	}

	//Sets the Reply-To header property.
	// - $Str_ReplyTo:			Valid string for a Reply-To email header
	// * Return:				SELF
	public function reply_to($Str_ReplyTo)
	{
		//If the Repl-To header is not a string handle exception
		if (!is_string($Str_ReplyTo))
		{
			global $CMD;
			$CMD->handle_exception('Email Reply-To header is not a string', 'MW:1010');
			return $this;
		}

		//If the Reply-To header in invalid handle exception.
		if (preg_match($this->Reg_HeaderReplyTo, $Str_ReplyTo, $Arr_Matches) && $Arr_Matches[0] != $Str_ReplyTo)
		{
			global $CMD;
			$CMD->handle_exception('Email Reply-To header string invalid: '.$Str_ReplyTo, 'MW:1010');
			return $this;
		}

		//Decouple header properties.
		$Arr_NewHeaders = $this->Arr_EmailHeaders;

		//Add header property.
		$Arr_NewHeaders['Reply-To'] = $Str_ReplyTo;

		//Recouple header properties.
		$this->Arr_EmailHeaders = $Arr_NewHeaders;

		return $this;
	}

	//Sets the Return-Path header property.
	// - $Str_ReturnPath:		Valid string for a Return-Path email header
	// * Return:				SELF
	public function return_path($Str_ReturnPath)
	{
		//If the Return-Path header is not a string handle exception
		if (!is_string($Str_ReturnPath))
		{
			global $CMD;
			$CMD->handle_exception('Email Return-Path header is not a string', 'MW:1010');
			return $this;
		}

		//If the cc header in invalid handle exception.
		if (preg_match($this->Reg_HeaderReturnPath, $Str_ReturnPath, $Arr_Matches) && $Arr_Matches[0] != $Str_ReturnPath)
		{
			global $CMD;
			$CMD->handle_exception('Email Return-Path header string invalid: '.$Str_ReturnPath, 'MW:1010');
			return $this;
		}

		//Decouple header properties.
		$Arr_NewHeaders = $this->Arr_EmailHeaders;

		//Add header property.
		$Arr_NewHeaders['Return-Path'] = $Str_ReturnPath;

		//Recouple header properties.
		$this->Arr_EmailHeaders = $Arr_NewHeaders;

		return $this;
	}

	public function charset()
	{

		return $this;
	}

	public function date()
	{

		return $this;
	}

	public function xmailer()
	{

		return $this;
	}

	//*!*This function needs to be built to accommodate the pdf mailout functions.
	public function attachments()
	{

		return $this;
	}

	public function smtp()
	{

		return $this;
	}

}

?>