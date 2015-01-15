<?php
//time.php
//Time class file.
//Design by David Thomson at Hundredth Codemonkey.

///////////////////////////////////////////////////////////////////////////////
//                        M O D U L E   C L A S S                            //
///////////////////////////////////////////////////////////////////////////////

class MW_Helper_Time extends MW_Utility_Helper
{


///////////////////////////////////////////////////////////////////////////////
//                      C L A S S   P R O P E R T I E S                      //
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////


	public function __construct()
	{
	}





///////////////////////////////////////////////////////////////////////////////
//              T I M E   C O N S T A N T S   F U N C T I O N S              //
///////////////////////////////////////////////////////////////////////////////

	//Gets the days of the week.
	//*!*Will expand this for starting day/timestamp, and formatt
	public function days_of_week()
	{
		return array('Sunday'=>1, 'Monday'=>2, 'Tuesday'=>3, 'Wednesday'=>4, 'Thursday'=>5, 'Friday'=>6, 'Saturday'=>7);
	}

	//Gets the days of the week.
	//*!*Will expand this for starting day/timestamp, and formatt
	public function months_of_year()
	{
		return array('January'=>1, 'Feburary'=>2, 'March'=>3, 'April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12);
	}

	//Gets all continent timezones in alphbetical order.
	// * Return:				Timezone of each continent in alphabetical order.
	//*!*I might expand this function to get timezones by region and maybe country.
	public function timezones()
	{
		return array('Africa/Abidjan' => 'Africa/Abidjan', 'Africa/Accra' => 'Africa/Accra', 'Africa/Addis_Ababa' => 'Africa/Addis_Ababa', 'Africa/Algiers' => 'Africa/Algiers', 'Africa/Asmera' => 'Africa/Asmera', 'Africa/Bamako' => 'Africa/Bamako', 'Africa/Bangui' => 'Africa/Bangui', 'Africa/Banjul' => 'Africa/Banjul', 'Africa/Bissau' => 'Africa/Bissau', 'Africa/Blantyre' => 'Africa/Blantyre', 'Africa/Brazzaville' => 'Africa/Brazzaville', 'Africa/Bujumbura' => 'Africa/Bujumbura', 'Africa/Cairo' => 'Africa/Cairo', 'Africa/Casablanca' => 'Africa/Casablanca', 'Africa/Ceuta' => 'Africa/Ceuta', 'Africa/Conakry' => 'Africa/Conakry', 'Africa/Dakar' => 'Africa/Dakar', 'Africa/Dar_es_Salaam' => 'Africa/Dar_es_Salaam', 'Africa/Djibouti' => 'Africa/Djibouti', 'Africa/Douala' => 'Africa/Douala', 'Africa/El_Aaiun' => 'Africa/El_Aaiun', 'Africa/Freetown' => 'Africa/Freetown', 'Africa/Gaborone' => 'Africa/Gaborone', 'Africa/Harare' => 'Africa/Harare', 'Africa/Johannesburg' => 'Africa/Johannesburg', 'Africa/Kampala' => 'Africa/Kampala', 'Africa/Khartoum' => 'Africa/Khartoum', 'Africa/Kigali' => 'Africa/Kigali', 'Africa/Kinshasa' => 'Africa/Kinshasa', 'Africa/Lagos' => 'Africa/Lagos', 'Africa/Libreville' => 'Africa/Libreville', 'Africa/Lome' => 'Africa/Lome', 'Africa/Luanda' => 'Africa/Luanda', 'Africa/Lubumbashi' => 'Africa/Lubumbashi', 'Africa/Lusaka' => 'Africa/Lusaka', 'Africa/Malabo' => 'Africa/Malabo', 'Africa/Maputo' => 'Africa/Maputo', 'Africa/Maseru' => 'Africa/Maseru', 'Africa/Mbabane' => 'Africa/Mbabane', 'Africa/Mogadishu' => 'Africa/Mogadishu', 'Africa/Monrovia' => 'Africa/Monrovia', 'Africa/Nairobi' => 'Africa/Nairobi', 'Africa/Ndjamena' => 'Africa/Ndjamena', 'Africa/Niamey' => 'Africa/Niamey', 'Africa/Nouakchott' => 'Africa/Nouakchott', 'Africa/Ouagadougou' => 'Africa/Ouagadougou', 'Africa/Porto-Novo' => 'Africa/Porto-Novo', 'Africa/Sao_Tome' => 'Africa/Sao_Tome', 'Africa/Timbuktu' => 'Africa/Timbuktu', 'Africa/Tripoli' => 'Africa/Tripoli', 'Africa/Tunis' => 'Africa/Tunis', 'Africa/Windhoek' => 'Africa/Windhoek', 'America/Adak' => 'America/Adak', 'America/Anchorage' => 'America/Anchorage', 'America/Anguilla' => 'America/Anguilla', 'America/Antigua' => 'America/Antigua', 'America/Araguaina' => 'America/Araguaina', 'America/Argentina/Buenos_Aires' => 'America/Argentina/Buenos_Aires', 'America/Argentina/Catamarca' => 'America/Argentina/Catamarca', 'America/Argentina/ComodRivadavia' => 'America/Argentina/ComodRivadavia', 'America/Argentina/Cordoba' => 'America/Argentina/Cordoba', 'America/Argentina/Jujuy' => 'America/Argentina/Jujuy', 'America/Argentina/La_Rioja' => 'America/Argentina/La_Rioja', 'America/Argentina/Mendoza' => 'America/Argentina/Mendoza', 'America/Argentina/Rio_Gallegos' => 'America/Argentina/Rio_Gallegos', 'America/Argentina/San_Juan' => 'America/Argentina/San_Juan', 'America/Argentina/Tucuman' => 'America/Argentina/Tucuman', 'America/Argentina/Ushuaia' => 'America/Argentina/Ushuaia', 'America/Aruba' => 'America/Aruba', 'America/Asuncion' => 'America/Asuncion', 'America/Atikokan' => 'America/Atikokan', 'America/Atka' => 'America/Atka', 'America/Bahia' => 'America/Bahia', 'America/Barbados' => 'America/Barbados', 'America/Belem' => 'America/Belem', 'America/Belize' => 'America/Belize', 'America/Blanc-Sablon' => 'America/Blanc-Sablon', 'America/Boa_Vista' => 'America/Boa_Vista', 'America/Bogota' => 'America/Bogota', 'America/Boise' => 'America/Boise', 'America/Buenos_Aires' => 'America/Buenos_Aires', 'America/Cambridge_Bay' => 'America/Cambridge_Bay', 'America/Campo_Grande' => 'America/Campo_Grande', 'America/Cancun' => 'America/Cancun', 'America/Caracas' => 'America/Caracas', 'America/Catamarca' => 'America/Catamarca', 'America/Cayenne' => 'America/Cayenne', 'America/Cayman' => 'America/Cayman', 'America/Chicago' => 'America/Chicago', 'America/Chihuahua' => 'America/Chihuahua', 'America/Coral_Harbour' => 'America/Coral_Harbour', 'America/Cordoba' => 'America/Cordoba', 'America/Costa_Rica' => 'America/Costa_Rica', 'America/Cuiaba' => 'America/Cuiaba', 'America/Curacao' => 'America/Curacao', 'America/Danmarkshavn' => 'America/Danmarkshavn', 'America/Dawson' => 'America/Dawson', 'America/Dawson_Creek' => 'America/Dawson_Creek', 'America/Denver' => 'America/Denver', 'America/Detroit' => 'America/Detroit', 'America/Dominica' => 'America/Dominica', 'America/Edmonton' => 'America/Edmonton', 'America/Eirunepe' => 'America/Eirunepe', 'America/El_Salvador' => 'America/El_Salvador', 'America/Ensenada' => 'America/Ensenada', 'America/Fort_Wayne' => 'America/Fort_Wayne', 'America/Fortaleza' => 'America/Fortaleza', 'America/Glace_Bay' => 'America/Glace_Bay', 'America/Godthab' => 'America/Godthab', 'America/Goose_Bay' => 'America/Goose_Bay', 'America/Grand_Turk' => 'America/Grand_Turk', 'America/Grenada' => 'America/Grenada', 'America/Guadeloupe' => 'America/Guadeloupe', 'America/Guatemala' => 'America/Guatemala', 'America/Guayaquil' => 'America/Guayaquil', 'America/Guyana' => 'America/Guyana', 'America/Halifax' => 'America/Halifax', 'America/Havana' => 'America/Havana', 'America/Hermosillo' => 'America/Hermosillo', 'America/Indiana/Indianapolis' => 'America/Indiana/Indianapolis', 'America/Indiana/Knox' => 'America/Indiana/Knox', 'America/Indiana/Marengo' => 'America/Indiana/Marengo', 'America/Indiana/Petersburg' => 'America/Indiana/Petersburg', 'America/Indiana/Vevay' => 'America/Indiana/Vevay', 'America/Indiana/Vincennes' => 'America/Indiana/Vincennes', 'America/Indianapolis' => 'America/Indianapolis', 'America/Inuvik' => 'America/Inuvik', 'America/Iqaluit' => 'America/Iqaluit', 'America/Jamaica' => 'America/Jamaica', 'America/Jujuy' => 'America/Jujuy', 'America/Juneau' => 'America/Juneau', 'America/Kentucky/Louisville' => 'America/Kentucky/Louisville', 'America/Kentucky/Monticello' => 'America/Kentucky/Monticello', 'America/Knox_IN' => 'America/Knox_IN', 'America/La_Paz' => 'America/La_Paz', 'America/Lima' => 'America/Lima', 'America/Los_Angeles' => 'America/Los_Angeles', 'America/Louisville' => 'America/Louisville', 'America/Maceio' => 'America/Maceio', 'America/Managua' => 'America/Managua', 'America/Manaus' => 'America/Manaus', 'America/Martinique' => 'America/Martinique', 'America/Mazatlan' => 'America/Mazatlan', 'America/Mendoza' => 'America/Mendoza', 'America/Menominee' => 'America/Menominee', 'America/Merida' => 'America/Merida', 'America/Mexico_City' => 'America/Mexico_City', 'America/Miquelon' => 'America/Miquelon', 'America/Moncton' => 'America/Moncton', 'America/Monterrey' => 'America/Monterrey', 'America/Montevideo' => 'America/Montevideo', 'America/Montreal' => 'America/Montreal', 'America/Montserrat' => 'America/Montserrat', 'America/Nassau' => 'America/Nassau', 'America/New_York' => 'America/New_York', 'America/Nipigon' => 'America/Nipigon', 'America/Nome' => 'America/Nome', 'America/Noronha' => 'America/Noronha', 'America/North_Dakota/Center' => 'America/North_Dakota/Center', 'America/North_Dakota/New_Salem' => 'America/North_Dakota/New_Salem', 'America/Panama' => 'America/Panama', 'America/Pangnirtung' => 'America/Pangnirtung', 'America/Paramaribo' => 'America/Paramaribo', 'America/Phoenix' => 'America/Phoenix', 'America/Port-au-Prince' => 'America/Port-au-Prince', 'America/Port_of_Spain' => 'America/Port_of_Spain', 'America/Porto_Acre' => 'America/Porto_Acre', 'America/Porto_Velho' => 'America/Porto_Velho', 'America/Puerto_Rico' => 'America/Puerto_Rico', 'America/Rainy_River' => 'America/Rainy_River', 'America/Rankin_Inlet' => 'America/Rankin_Inlet', 'America/Recife' => 'America/Recife', 'America/Regina' => 'America/Regina', 'America/Rio_Branco' => 'America/Rio_Branco', 'America/Rosario' => 'America/Rosario', 'America/Santiago' => 'America/Santiago', 'America/Santo_Domingo' => 'America/Santo_Domingo', 'America/Sao_Paulo' => 'America/Sao_Paulo', 'America/Scoresbysund' => 'America/Scoresbysund', 'America/Shiprock' => 'America/Shiprock', 'America/St_Johns' => 'America/St_Johns', 'America/St_Kitts' => 'America/St_Kitts', 'America/St_Lucia' => 'America/St_Lucia', 'America/St_Thomas' => 'America/St_Thomas', 'America/St_Vincent' => 'America/St_Vincent', 'America/Swift_Current' => 'America/Swift_Current', 'America/Tegucigalpa' => 'America/Tegucigalpa', 'America/Thule' => 'America/Thule', 'America/Thunder_Bay' => 'America/Thunder_Bay', 'America/Tijuana' => 'America/Tijuana', 'America/Toronto' => 'America/Toronto', 'America/Tortola' => 'America/Tortola', 'America/Vancouver' => 'America/Vancouver', 'America/Virgin' => 'America/Virgin', 'America/Whitehorse' => 'America/Whitehorse', 'America/Winnipeg' => 'America/Winnipeg', 'America/Yakutat' => 'America/Yakutat', 'America/Yellowknife' => 'America/Yellowknife', 'Antarctica/Casey' => 'Antarctica/Casey', 'Antarctica/Davis' => 'Antarctica/Davis', 'Antarctica/DumontDUrville' => 'Antarctica/DumontDUrville', 'Antarctica/Mawson' => 'Antarctica/Mawson', 'Antarctica/McMurdo' => 'Antarctica/McMurdo', 'Antarctica/Palmer' => 'Antarctica/Palmer', 'Antarctica/Rothera' => 'Antarctica/Rothera', 'Antarctica/South_Pole' => 'Antarctica/South_Pole', 'Antarctica/Syowa' => 'Antarctica/Syowa', 'Antarctica/Vostok' => 'Antarctica/Vostok', 'Arctic/Longyearbyen' => 'Arctic/Longyearbyen', 'Asia/Aden' => 'Asia/Aden', 'Asia/Almaty' => 'Asia/Almaty', 'Asia/Amman' => 'Asia/Amman', 'Asia/Anadyr' => 'Asia/Anadyr', 'Asia/Aqtau' => 'Asia/Aqtau', 'Asia/Aqtobe' => 'Asia/Aqtobe', 'Asia/Ashgabat' => 'Asia/Ashgabat', 'Asia/Ashkhabad' => 'Asia/Ashkhabad', 'Asia/Baghdad' => 'Asia/Baghdad', 'Asia/Bahrain' => 'Asia/Bahrain', 'Asia/Baku' => 'Asia/Baku', 'Asia/Bangkok' => 'Asia/Bangkok', 'Asia/Beirut' => 'Asia/Beirut', 'Asia/Bishkek' => 'Asia/Bishkek', 'Asia/Brunei' => 'Asia/Brunei', 'Asia/Calcutta' => 'Asia/Calcutta', 'Asia/Choibalsan' => 'Asia/Choibalsan', 'Asia/Chongqing' => 'Asia/Chongqing', 'Asia/Chungking' => 'Asia/Chungking', 'Asia/Colombo' => 'Asia/Colombo', 'Asia/Dacca' => 'Asia/Dacca', 'Asia/Damascus' => 'Asia/Damascus', 'Asia/Dhaka' => 'Asia/Dhaka', 'Asia/Dili' => 'Asia/Dili', 'Asia/Dubai' => 'Asia/Dubai', 'Asia/Dushanbe' => 'Asia/Dushanbe', 'Asia/Gaza' => 'Asia/Gaza', 'Asia/Harbin' => 'Asia/Harbin', 'Asia/Hong_Kong' => 'Asia/Hong_Kong', 'Asia/Hovd' => 'Asia/Hovd', 'Asia/Irkutsk' => 'Asia/Irkutsk', 'Asia/Istanbul' => 'Asia/Istanbul', 'Asia/Jakarta' => 'Asia/Jakarta', 'Asia/Jayapura' => 'Asia/Jayapura', 'Asia/Jerusalem' => 'Asia/Jerusalem', 'Asia/Kabul' => 'Asia/Kabul', 'Asia/Kamchatka' => 'Asia/Kamchatka', 'Asia/Karachi' => 'Asia/Karachi', 'Asia/Kashgar' => 'Asia/Kashgar', 'Asia/Katmandu' => 'Asia/Katmandu', 'Asia/Krasnoyarsk' => 'Asia/Krasnoyarsk', 'Asia/Kuching' => 'Asia/Kuching', 'Asia/Kuwait' => 'Asia/Kuwait', 'Asia/Macao' => 'Asia/Macao', 'Asia/Macau' => 'Asia/Macau', 'Asia/Magadan' => 'Asia/Magadan', 'Asia/Makassar' => 'Asia/Makassar', 'Asia/Manila' => 'Asia/Manila', 'Asia/Muscat' => 'Asia/Muscat', 'Asia/Nicosia' => 'Asia/Nicosia', 'Asia/Novosibirsk' => 'Asia/Novosibirsk', 'Asia/Omsk' => 'Asia/Omsk', 'Asia/Oral' => 'Asia/Oral', 'Asia/Phnom_Penh' => 'Asia/Phnom_Penh', 'Asia/Pontianak' => 'Asia/Pontianak', 'Asia/Pyongyang' => 'Asia/Pyongyang', 'Asia/Qatar' => 'Asia/Qatar', 'Asia/Qyzylorda' => 'Asia/Qyzylorda', 'Asia/Rangoon' => 'Asia/Rangoon', 'Asia/Riyadh' => 'Asia/Riyadh', 'Asia/Saigon' => 'Asia/Saigon', 'Asia/Sakhalin' => 'Asia/Sakhalin', 'Asia/Samarkand' => 'Asia/Samarkand', 'Asia/Seoul' => 'Asia/Seoul', 'Asia/Shanghai' => 'Asia/Shanghai', 'Asia/Singapore' => 'Asia/Singapore', 'Asia/Taipei' => 'Asia/Taipei', 'Asia/Tashkent' => 'Asia/Tashkent', 'Asia/Tbilisi' => 'Asia/Tbilisi', 'Asia/Tehran' => 'Asia/Tehran', 'Asia/Tel_Aviv' => 'Asia/Tel_Aviv', 'Asia/Thimbu' => 'Asia/Thimbu', 'Asia/Thimphu' => 'Asia/Thimphu', 'Asia/Tokyo' => 'Asia/Tokyo', 'Asia/Ujung_Pandang' => 'Asia/Ujung_Pandang', 'Asia/Ulaanbaatar' => 'Asia/Ulaanbaatar', 'Asia/Ulan_Bator' => 'Asia/Ulan_Bator', 'Asia/Urumqi' => 'Asia/Urumqi', 'Asia/Vientiane' => 'Asia/Vientiane', 'Asia/Vladivostok' => 'Asia/Vladivostok', 'Asia/Yakutsk' => 'Asia/Yakutsk', 'Asia/Yekaterinburg' => 'Asia/Yekaterinburg', 'Asia/Yerevan' => 'Asia/Yerevan', 'Atlantic/Azores' => 'Atlantic/Azores', 'Atlantic/Bermuda' => 'Atlantic/Bermuda', 'Atlantic/Canary' => 'Atlantic/Canary', 'Atlantic/Cape_Verde' => 'Atlantic/Cape_Verde', 'Atlantic/Faeroe' => 'Atlantic/Faeroe', 'Atlantic/Jan_Mayen' => 'Atlantic/Jan_Mayen', 'Atlantic/Madeira' => 'Atlantic/Madeira', 'Atlantic/Reykjavik' => 'Atlantic/Reykjavik', 'Atlantic/South_Georgia' => 'Atlantic/South_Georgia', ' Atlantic/St_Helena ' => ' Atlantic/St_Helena ', 'Atlantic/Stanley' => 'Atlantic/Stanley', 'Australia/ACT' => 'Australia/ACT', 'Australia/Adelaide' => 'Australia/Adelaide', 'Australia/Brisbane' => 'Australia/Brisbane', 'Australia/Broken_Hill' => 'Australia/Broken_Hill', 'Australia/Canberra' => 'Australia/Canberra', 'Australia/Currie' => 'Australia/Currie', 'Australia/Darwin' => 'Australia/Darwin', 'Australia/Hobart' => 'Australia/Hobart', 'Australia/LHI' => 'Australia/LHI', 'Australia/Lindeman' => 'Australia/Lindeman', 'Australia/Lord_Howe' => 'Australia/Lord_Howe', 'Australia/Melbourne' => 'Australia/Melbourne', 'Australia/North' => 'Australia/North', 'Australia/NSW' => 'Australia/NSW', 'Australia/Perth' => 'Australia/Perth', 'Australia/Queensland' => 'Australia/Queensland', 'Australia/South' => 'Australia/South', 'Australia/Sydney' => 'Australia/Sydney', 'Australia/Tasmania' => 'Australia/Tasmania', 'Australia/Victoria' => 'Australia/Victoria', 'Australia/West' => 'Australia/West', 'Australia/Yancowinna' => 'Australia/Yancowinna', 'Europe/Amsterdam' => 'Europe/Amsterdam', 'Europe/Andorra' => 'Europe/Andorra', 'Europe/Athens' => 'Europe/Athens', 'Europe/Belfast' => 'Europe/Belfast', 'Europe/Belgrade' => 'Europe/Belgrade', 'Europe/Berlin' => 'Europe/Berlin', 'Europe/Bratislava' => 'Europe/Bratislava', 'Europe/Brussels' => 'Europe/Brussels', 'Europe/Bucharest' => 'Europe/Bucharest', 'Europe/Budapest' => 'Europe/Budapest', 'Europe/Chisinau' => 'Europe/Chisinau', 'Europe/Copenhagen' => 'Europe/Copenhagen', 'Europe/Dublin' => 'Europe/Dublin', 'Europe/Gibraltar' => 'Europe/Gibraltar', 'Europe/Guernsey' => 'Europe/Guernsey', 'Europe/Helsinki' => 'Europe/Helsinki', 'Europe/Isle_of_Man' => 'Europe/Isle_of_Man', 'Europe/Istanbul' => 'Europe/Istanbul', 'Europe/Jersey' => 'Europe/Jersey', 'Europe/Kaliningrad' => 'Europe/Kaliningrad', 'Europe/Kiev' => 'Europe/Kiev', 'Europe/Lisbon' => 'Europe/Lisbon', 'Europe/Ljubljana' => 'Europe/Ljubljana', 'Europe/London' => 'Europe/London', 'Europe/Luxembourg' => 'Europe/Luxembourg', 'Europe/Madrid' => 'Europe/Madrid', 'Europe/Malta' => 'Europe/Malta', 'Europe/Mariehamn' => 'Europe/Mariehamn', 'Europe/Minsk' => 'Europe/Minsk', 'Europe/Monaco' => 'Europe/Monaco', 'Europe/Moscow' => 'Europe/Moscow', 'Europe/Nicosia' => 'Europe/Nicosia', 'Europe/Oslo' => 'Europe/Oslo', 'Europe/Paris' => 'Europe/Paris', 'Europe/Prague' => 'Europe/Prague', 'Europe/Riga' => 'Europe/Riga', 'Europe/Rome' => 'Europe/Rome', 'Europe/Samara' => 'Europe/Samara', 'Europe/San_Marino' => 'Europe/San_Marino', 'Europe/Sarajevo' => 'Europe/Sarajevo', 'Europe/Simferopol' => 'Europe/Simferopol', 'Europe/Skopje' => 'Europe/Skopje', 'Europe/Sofia' => 'Europe/Sofia', 'Europe/Stockholm' => 'Europe/Stockholm', 'Europe/Tallinn' => 'Europe/Tallinn', 'Europe/Tirane' => 'Europe/Tirane', 'Europe/Tiraspol' => 'Europe/Tiraspol', 'Europe/Uzhgorod' => 'Europe/Uzhgorod', 'Europe/Vaduz' => 'Europe/Vaduz', 'Europe/Vatican' => 'Europe/Vatican', 'Europe/Vienna' => 'Europe/Vienna', 'Europe/Vilnius' => 'Europe/Vilnius', 'Europe/Volgograd' => 'Europe/Volgograd', 'Europe/Warsaw' => 'Europe/Warsaw', 'Europe/Zagreb' => 'Europe/Zagreb', 'Europe/Zaporozhye' => 'Europe/Zaporozhye', 'Europe/Zurich' => 'Europe/Zurich', 'Indian/Antananarivo' => 'Indian/Antananarivo', 'Indian/Chagos' => 'Indian/Chagos', 'Indian/Christmas' => 'Indian/Christmas', 'Indian/Cocos' => 'Indian/Cocos', 'Indian/Comoro' => 'Indian/Comoro', 'Indian/Kerguelen' => 'Indian/Kerguelen', 'Indian/Mahe' => 'Indian/Mahe', 'Indian/Maldives' => 'Indian/Maldives', 'Indian/Mauritius' => 'Indian/Mauritius', 'Indian/Mayotte' => 'Indian/Mayotte', 'Indian/Reunion' => 'Indian/Reunion', 'Pacific/Apia' => 'Pacific/Apia', 'Pacific/Auckland' => 'Pacific/Auckland', 'Pacific/Chatham' => 'Pacific/Chatham', 'Pacific/Easter' => 'Pacific/Easter', 'Pacific/Efate' => 'Pacific/Efate', 'Pacific/Enderbury' => 'Pacific/Enderbury', 'Pacific/Fakaofo' => 'Pacific/Fakaofo', 'Pacific/Fiji' => 'Pacific/Fiji', 'Pacific/Funafuti' => 'Pacific/Funafuti', 'Pacific/Galapagos' => 'Pacific/Galapagos', 'Pacific/Gambier' => 'Pacific/Gambier', 'Pacific/Guadalcanal' => 'Pacific/Guadalcanal', 'Pacific/Guam' => 'Pacific/Guam', 'Pacific/Honolulu' => 'Pacific/Honolulu', 'Pacific/Johnston' => 'Pacific/Johnston', 'Pacific/Kiritimati' => 'Pacific/Kiritimati', 'Pacific/Kosrae' => 'Pacific/Kosrae', 'Pacific/Kwajalein' => 'Pacific/Kwajalein', 'Pacific/Majuro' => 'Pacific/Majuro', 'Pacific/Marquesas' => 'Pacific/Marquesas', 'Pacific/Midway' => 'Pacific/Midway', 'Pacific/Nauru' => 'Pacific/Nauru', 'Pacific/Niue' => 'Pacific/Niue', 'Pacific/Norfolk' => 'Pacific/Norfolk', 'Pacific/Noumea' => 'Pacific/Noumea', 'Pacific/Pago_Pago' => 'Pacific/Pago_Pago', 'Pacific/Palau' => 'Pacific/Palau', 'Pacific/Pitcairn' => 'Pacific/Pitcairn', 'Pacific/Ponape' => 'Pacific/Ponape', 'Pacific/Port_Moresby' => 'Pacific/Port_Moresby', 'Pacific/Rarotonga' => 'Pacific/Rarotonga', 'Pacific/Saipan' => 'Pacific/Saipan', 'Pacific/Samoa' => 'Pacific/Samoa', 'Pacific/Tahiti' => 'Pacific/Tahiti', 'Pacific/Tarawa' => 'Pacific/Tarawa', 'Pacific/Tongatapu' => 'Pacific/Tongatapu', 'Pacific/Truk' => 'Pacific/Truk', 'Pacific/Wake' => 'Pacific/Wake', 'Pacific/Wallis' => 'Pacific/Wallis', 'Pacific/Yap' => 'Pacific/Yap');
	}

	public function countries()
	{
		 return array('Afghanistan' => 'Afghanistan', 'Albania' => 'Albania', 'Algeria' => 'Algeria', 'Andorra' => 'Andorra', 'Angola' => 'Angola', 'Antigua and Barbuda' => 'Antigua and Barbuda', 'Argentina' => 'Argentina', 'Armenia' => 'Armenia', 'Australia' => 'Australia', 'Austria' => 'Austria', 'Azerbaijan' => 'Azerbaijan', 'Bahamas' => 'Bahamas', 'Bahrain' => 'Bahrain', 'Bangladesh' => 'Bangladesh', 'Barbados' => 'Barbados', 'Belarus' => 'Belarus', 'Belgium' => 'Belgium', 'Belize' => 'Belize', 'Benin' => 'Benin', 'Bhutan' => 'Bhutan', 'Bolivia' => 'Bolivia', 'Bosnia and Herzegovina' => 'Bosnia and Herzegovina', 'Botswana' => 'Botswana', 'Brazil' => 'Brazil', 'Brunei' => 'Brunei', 'Bulgaria' => 'Bulgaria', 'Burkina Faso' => 'Burkina Faso', 'Burundi' => 'Burundi', 'Cambodia' => 'Cambodia', 'Cameroon' => 'Cameroon', 'Canada' => 'Canada', 'Cape Verde' => 'Cape Verde', 'Central African Republic' => 'Central African Republic', 'Chad' => 'Chad', 'Chile' => 'Chile', 'China' => 'China', 'Colombi' => 'Colombi', 'Comoros' => 'Comoros', 'Congo (Brazzaville)' => 'Congo (Brazzaville)', 'Congo' => 'Congo', 'Costa Rica' => 'Costa Rica', 'Cote d\'Ivoire' => 'Cote d\'Ivoire', 'Croatia' => 'Croatia', 'Cuba' => 'Cuba', 'Cyprus' => 'Cyprus', 'Czech Republic' => 'Czech Republic', 'Denmark' => 'Denmark', 'Djibouti' => 'Djibouti', 'Dominica' => 'Dominica', 'Dominican Republic' => 'Dominican Republic', 'East Timor (Timor Timur)' => 'East Timor (Timor Timur)', 'Ecuador' => 'Ecuador', 'Egypt' => 'Egypt', 'El Salvador' => 'El Salvador', 'Equatorial Guinea' => 'Equatorial Guinea', 'Eritrea' => 'Eritrea', 'Estonia' => 'Estonia', 'Ethiopia' => 'Ethiopia', 'Fiji' => 'Fiji', 'Finland' => 'Finland', 'France' => 'France', 'Gabon' => 'Gabon', 'Gambia, The' => 'Gambia, The', 'Georgia' => 'Georgia', 'Germany' => 'Germany', 'Ghana' => 'Ghana', 'Greece' => 'Greece', 'Grenada' => 'Grenada', 'Guatemala' => 'Guatemala', 'Guinea' => 'Guinea', 'Guinea-Bissau' => 'Guinea-Bissau', 'Guyana' => 'Guyana', 'Haiti' => 'Haiti', 'Honduras' => 'Honduras', 'Hungary' => 'Hungary', 'Iceland' => 'Iceland', 'India' => 'India', 'Indonesia' => 'Indonesia', 'Iran' => 'Iran', 'Iraq' => 'Iraq', 'Ireland' => 'Ireland', 'Israel' => 'Israel', 'Italy' => 'Italy', 'Jamaica' => 'Jamaica', 'Japan' => 'Japan', 'Jordan' => 'Jordan', 'Kazakhstan' => 'Kazakhstan', 'Kenya' => 'Kenya', 'Kiribati' => 'Kiribati', 'Korea, North' => 'Korea, North', 'Korea, South' => 'Korea, South', 'Kuwait' => 'Kuwait', 'Kyrgyzstan' => 'Kyrgyzstan', 'Laos' => 'Laos', 'Latvia' => 'Latvia', 'Lebanon' => 'Lebanon', 'Lesotho' => 'Lesotho', 'Liberia' => 'Liberia', 'Libya' => 'Libya', 'Liechtenstein' => 'Liechtenstein', 'Lithuania' => 'Lithuania', 'Luxembourg' => 'Luxembourg', 'Macedonia' => 'Macedonia', 'Madagascar' => 'Madagascar', 'Malawi' => 'Malawi', 'Malaysia' => 'Malaysia', 'Maldives' => 'Maldives', 'Mali' => 'Mali', 'Malta' => 'Malta', 'Marshall Islands' => 'Marshall Islands', 'Mauritania' => 'Mauritania', 'Mauritius' => 'Mauritius', 'Mexico' => 'Mexico', 'Micronesia' => 'Micronesia', 'Moldova' => 'Moldova', 'Monaco' => 'Monaco', 'Mongolia' => 'Mongolia', 'Morocco' => 'Morocco', 'Mozambique' => 'Mozambique', 'Myanmar' => 'Myanmar', 'Namibia' => 'Namibia', 'Nauru' => 'Nauru', 'Nepa' => 'Nepa', 'Netherlands' => 'Netherlands', 'New Zealand' => 'New Zealand', 'Nicaragua' => 'Nicaragua', 'Niger' => 'Niger', 'Nigeria' => 'Nigeria', 'Norway' => 'Norway', 'Oman' => 'Oman', 'Pakistan' => 'Pakistan', 'Palau' => 'Palau', 'Panama' => 'Panama', 'Papua New Guinea' => 'Papua New Guinea', 'Paraguay' => 'Paraguay', 'Peru' => 'Peru', 'Philippines' => 'Philippines', 'Poland' => 'Poland', 'Portugal' => 'Portugal', 'Qatar' => 'Qatar', 'Romania' => 'Romania', 'Russia' => 'Russia', 'Rwanda' => 'Rwanda', 'Saint Kitts and Nevis' => 'Saint Kitts and Nevis', 'Saint Lucia' => 'Saint Lucia', 'Saint Vincent' => 'Saint Vincent', 'Samoa' => 'Samoa', 'San Marino' => 'San Marino', 'Sao Tome and Principe' => 'Sao Tome and Principe', 'Saudi Arabia' => 'Saudi Arabia', 'Senegal' => 'Senegal', 'Serbia and Montenegro' => 'Serbia and Montenegro', 'Seychelles' => 'Seychelles', 'Sierra Leone' => 'Sierra Leone', 'Singapore' => 'Singapore', 'Slovakia' => 'Slovakia', 'Slovenia' => 'Slovenia', 'Solomon Islands' => 'Solomon Islands', 'Somalia' => 'Somalia', 'South Africa' => 'South Africa', 'Spain' => 'Spain', 'Sri Lanka' => 'Sri Lanka', 'Sudan' => 'Sudan', 'Suriname' => 'Suriname', 'Swaziland' => 'Swaziland', 'Sweden' => 'Sweden', 'Switzerland' => 'Switzerland', 'Syria' => 'Syria', 'Taiwan' => 'Taiwan', 'Tajikistan' => 'Tajikistan', 'Tanzania' => 'Tanzania', 'Thailand' => 'Thailand', 'Togo' => 'Togo', 'Tonga' => 'Tonga', 'Trinidad and Tobago' => 'Trinidad and Tobago', 'Tunisia' => 'Tunisia', 'Turkey' => 'Turkey', 'Turkmenistan' => 'Turkmenistan', 'Tuvalu' => 'Tuvalu', 'Uganda' => 'Uganda', 'Ukraine' => 'Ukraine', 'United Arab Emirates' => 'United Arab Emirates', 'United Kingdom' => 'United Kingdom', 'United States' => 'United States', 'Uruguay' => 'Uruguay', 'Uzbekistan' => 'Uzbekistan', 'Vanuatu' => 'Vanuatu', 'Vatican City' => 'Vatican City', 'Venezuela' => 'Venezuela', 'Vietnam' => 'Vietnam', 'Yemen' => 'Yemen', 'Zambia' => 'Zambia', 'Zimbabwe' => 'Zimbabwe');
	}



///////////////////////////////////////////////////////////////////////////////
//            T I M E   F O R M A T T I N G   F U N C T I O N S              //
///////////////////////////////////////////////////////////////////////////////

	//Converts unix timestamp to date time database string format.
	// - $Int_Timestamp:		Timestamp to convert to datetime string, default = false($_SERVER['REQUEST_TIME'])
	// * Return:				Date time string for insertion into database
	// * NB: String format will optionally be user defined,
	//this function will look for that first before using the default constant.
	public function timestamp_to_datetime($Int_Timestamp=false)
	{
		if ($Int_Timestamp === false)
		{
			$Int_Timestamp = $_SERVER['REQUEST_TIME'];
		}

		$Str_Timestamp = date(MW_STR_FORMAT_DATETIME, $Int_Timestamp);
		return $Str_Timestamp;
	}

	//Converts a datetime string to UNIX timestamp.
	// - $Str_Datetime:			Datetime string to convert to timestamp
	// * Return:				Timestamp integer of $Str_Datetime string
	// * NB: The format regex will be optionally user defined in the future.
	public function datetime_to_timestamp($Str_Datetime)
	{
		if ($Str_Datetime === false)
		{
			return $_SERVER['REQUEST_TIME'];
		}

		//If the datetime format is incorrect handle exception.
		if (preg_match('/'.MW_REG_SYSTEM_DATETIME.'/', $Str_Datetime, $Arr_Datetime) === false)
		{
			global $CMD;
			handle_exception('Datetime could not be converted to timestamp, format incorrect: '.$Str_Datetime, 'MW:101');
		}

		return mktime($Arr_Datetime[4], $Arr_Datetime[5], $Arr_Datetime[6], $Arr_Datetime[2], $Arr_Datetime[3], $Arr_Datetime[1]);
	}

	//Tests if $Int_Timestamp is a timestamp.
	// - $Mix_Timestamp:		Integer or string to test as timestamp
	// * Return:				True if $Mix_Timestamp is a correct timestamp value, other false
	//*!*This functin will be slightly rearranged in the future to include a reex test against possible timestamp strings
	//ATM, this will placehold untill performance is tested.
	public function is_timestamp($Mix_Timestamp)
	{
		return is_int($Mix_Timestamp);
	}



///////////////////////////////////////////////////////////////////////////////
//          T I M E   M O D I F I C A T I O N   F U N C T I O N S            //
///////////////////////////////////////////////////////////////////////////////

	//Adds number of years from time
	// - $Int_Years:			Number of years to ad to datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function add_years($Int_Years, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('+'.$Int_Years.' year');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('+'.$Int_Years.' year', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Add year datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Adds number of months from time
	// - $Int_Months:			Number of months to add to datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function add_months($Int_Months, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('+'.$Int_Months.' month');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('+'.$Int_Months.' month', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Add month datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Adds number of days from time
	// - $Int_Days:				Number of days to add to datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function add_days($Int_Days, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('+'.$Int_Days.' day');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('+'.$Int_Days.' day', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Add day datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Adds number of hours from time
	// - $Int_Hours:			Number of hours to add to datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function add_hours($Int_Hours, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('+'.$Int_Hours.' hour');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('+'.$Int_Hours.' hour', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Add hour datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Adds number of minutes from time
	// - $Int_Minutes:			Number of minutes to add to datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function add_minutes($Int_Minutes, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('+'.$Int_Minutes.' minute');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('+'.$Int_Minutes.' minute', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Add minute datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Adds number of seconds from time
	// - $Int_Seconds:			Number of seconds to add to datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function add_seconds($Int_Seconds, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('+'.$Int_Seconds.' second');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('+'.$Int_Seconds.' second', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Add second datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}


	//Subtracts number of years from time
	// - $Int_Years:			Number of years to subtract from datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function sub_years($Int_Years, $Mix_Datetime=false)
	{

		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('-'.$Int_Years.' year');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('-'.$Int_Years.' year', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Subtract year datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Subtracts number of months from time
	// - $Int_Months:			Number of months to subtract from datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function sub_months($Int_Months, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;
		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('-'.$Int_Months.' month');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('-'.$Int_Months.' month', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Subtract month datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Subtracts number of days from time
	// - $Int_Days:				Number of days to subtract from datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function sub_days($Int_Days, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('-'.$Int_Days.' day');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('-'.$Int_Days.' day', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Subtract day datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Subtracts number of hours from time
	// - $Int_Hours:			Number of hours to subtract from datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function sub_hours($Int_Hours, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('-'.$Int_Hours.' hour');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('-'.$Int_Hours.' hour', $this->datetime_to_timestamp($Mix_Datetime)));
		}
		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Subtract hour datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Subtracts number of minutes from time
	// - $Int_Minutes:			Number of minutes to subtract from datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function sub_minutes($Int_Minutes, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('-'.$Int_Minutes.' minute');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('-'.$Int_Minutes.' minute', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Subtract minute datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Subtracts number of seconds from time
	// - $Int_Seconds:			Number of seconds to subtract from datetime
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function sub_seconds($Int_Seconds, $Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if ($this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = strtotime('-'.$Int_Seconds.' second');
		}
		else
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime(strtotime('-'.$Int_Seconds.' second', $this->datetime_to_timestamp($Mix_Datetime)));
		}

		//Handle formating exception.
		if ($Mix_ModifiedDatetime === false)
		{
			global $CMD;
			$CMD->handle_exception('Subtract second datetime format exception.', 'MW:101');
		}

		return $Mix_ModifiedDatetime;
	}

	//Sets datetime value to the beginning of the year.
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function start_of_year($Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->datetime_to_timestamp($Mix_Datetime);
		}

		$Arr_Date = getdate($Mix_ModifiedDatetime);
		$Mix_ModifiedDatetime = mktime(0, 0, 0, 1, 1, $Arr_Date['year']);

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime($Mix_ModifiedDatetime);
		}

		return $Mix_ModifiedDatetime;
	}

	//Sets datetime value to the beginning of the month.
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function start_of_month($Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;
		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->datetime_to_timestamp($Mix_Datetime);
		}

		$Arr_Date = getdate($Mix_ModifiedDatetime);
		$Mix_ModifiedDatetime = mktime(0, 0, 0, $Arr_Date['mon'], 1, $Arr_Date['year']);

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime($Mix_ModifiedDatetime);
		}

		return $Mix_ModifiedDatetime;
	}

	//Sets datetime value to the beginning of the week.
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function start_of_week($Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->datetime_to_timestamp($Mix_Datetime);
		}

		$Arr_Date = getdate($Mix_ModifiedDatetime);
		$Int_Day = $Arr_Date['mday'] - $Arr_Date['wday'];
		$Mix_ModifiedDatetime = mktime(0, 0, 0, $Arr_Date['mon'], $Int_Day, $Arr_Date['year']);

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime($Mix_ModifiedDatetime);
		}

		return $Mix_ModifiedDatetime;
	}

	//Sets datetime value to the beginning of the day.
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function start_of_day($Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->datetime_to_timestamp($Mix_Datetime);
		}

		$Arr_Date = getdate($Mix_ModifiedDatetime);
		$Mix_ModifiedDatetime = mktime(0, 0, 0, $Arr_Date['mon'], $Arr_Date['mday'], $Arr_Date['year']);

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime($Mix_ModifiedDatetime);
		}

		return $Mix_ModifiedDatetime;
	}

	//Sets datetime value to the beginning of the hour.
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function start_of_hour($Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->datetime_to_timestamp($Mix_Datetime);
		}

		$Arr_Date = getdate($Mix_ModifiedDatetime);
		$Mix_ModifiedDatetime = mktime($Arr_Date['hours'], 0, 0, $Arr_Date['mon'], $Arr_Date['mday'], $Arr_Date['year']);

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime($Mix_ModifiedDatetime);
		}

		return $Mix_ModifiedDatetime;
	}

	//Sets datetime value to the beginning of the minute.
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function start_of_minute($Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->datetime_to_timestamp($Mix_Datetime);
		}

		$Arr_Date = getdate($Mix_ModifiedDatetime);
		$Mix_ModifiedDatetime = mktime($Arr_Date['hours'], $Arr_Date['minutes'], 0, $Arr_Date['mon'], $Arr_Date['mday'], $Arr_Date['year']);

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime($Mix_ModifiedDatetime);
		}

		return $Mix_ModifiedDatetime;
	}

	//Sets datetime value to the beginning of the second.
	// - $Str_Datetime:			Datetime value, default = false(now)
	// - $Str_Return:			Return datetime value format
	public function start_of_second($Mix_Datetime=false)
	{
		$Mix_ModifiedDatetime = $Mix_Datetime;

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->datetime_to_timestamp($Mix_Datetime);
		}

		$Arr_Date = getdate($Mix_ModifiedDatetime);
		$Mix_ModifiedDatetime = mktime($Arr_Date['hours'], $Arr_Date['minutes'], $Arr_Date['seconds'], $Arr_Date['mon'], $Arr_Date['mday'], $Arr_Date['year']);

		if (!$this->is_timestamp($Mix_Datetime))
		{
			$Mix_ModifiedDatetime = $this->timestamp_to_datetime($Mix_ModifiedDatetime);
		}

		return $Mix_ModifiedDatetime;
	}



///////////////////////////////////////////////////////////////////////////////
//       F R O N T - E N D   C O N V E R S I O N   F U N C T I O N S         //
///////////////////////////////////////////////////////////////////////////////

	//Converts the JQuery UI datetime string value to database datetime format
	// - $Str_JqueryUIDateTime:	Datetime strin created by JQueryUI datetime pluin
	// * Return:				Datetime strin formatted for database insetion
	public function jqueryui_to_datetime($Str_JqueryUIDateTime)
	{
		//If there is no datetime supplied return empty string.
		if (!$Str_JqueryUIDateTime)
		{
			return '';
		}

		$Str_FormattedDatetime = '';

		//*!*I'm in the process of modifying this constant name, which needs to be tested.
		preg_match('@'.MW_REG_JQUERYUI_DATETIME.'@', $Str_JqueryUIDateTime, $Arr_Datetime);

		//If there is no time supplied with thye date default to zero.
		if (!isset($Arr_Datetime[4]))
		{
			$Arr_Datetime[4] = '';
			$Arr_Datetime[5] = '00';
			$Arr_Datetime[6] = '00';
		}

		$Int_Datetime = mktime($Arr_Datetime[5], $Arr_Datetime[6], 0, $Arr_Datetime[1], $Arr_Datetime[2], $Arr_Datetime[3]);
		$Str_FormattedDatetime = $this->timestamp_to_datetime($Int_Datetime);

		return $Str_FormattedDatetime;
	}

	public function datetime_to_jqueryui($Str_SystemDateTime, $Bol_Time=true)
	{
		//If there is no datetime supplied return empty string.
		if (!$Str_SystemDateTime)
		{
			return '';
		}

		$Str_FormattedDatetime = '';

		preg_match('@'.MW_REG_SYSTEM_DATETIME.'@', $Str_SystemDateTime, $Arr_Datetime);

		$Int_Datetime = mktime($Arr_Datetime[4], $Arr_Datetime[5], 0, $Arr_Datetime[2], $Arr_Datetime[3], $Arr_Datetime[1]);
		$Str_FormattedDatetime = $this->timestamp_to_datetime($Int_Datetime);
		$Str_FormattedDatetime = ($Bol_Time)? date('m/d/Y H:i', $Int_Datetime): date('m/d/Y', $Int_Datetime);

		return $Str_FormattedDatetime;
	}

}