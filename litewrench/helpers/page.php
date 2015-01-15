<?php
//page.php
//Core filter class file

/*
Asset inclusion usage(this will be commented in the API file):

-calling script groups in wrapper
{$page.scripts(lib)}
{$page.scripts(gui)}

-adding script in template
{$page.add(scripts).lib(scripts/jquery-1.6.2.min.js)

-calling meta data
{$page.meta(description)}

-calling linked document
{$page.document()}
-calling doucment by key
{$page.document(documentKey)}

-setting document description and keywords
***NOT IMPLEMENTED YET
{$page.document().append(description).replace(keywords)}

*/

class MW_Helper_Page extends MW_Utility_Helper
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////


	protected $Arr_Properties	= array(
		'Arr_PageData'			=> array(),	//Data for page parcel object.
		'Str_PageTheme'			=> array(),	//Current theme set forthis page.
		'Arr_Callbacks'			=> array(),	//List of callback methods.
		'Arr_Active'			=> array());//Active properties within the page.



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////

	//Class constructor.
	// * Return:					VOID
	public function __construct()
	{
		$this->Arr_Active = array('asset'=>'', 'action'=>'');

		return;
	}



///////////////////////////////////////////////////////////////////////////////
//                  B U I L D   A P I   F U N C T I O N S                    //
///////////////////////////////////////////////////////////////////////////////

	//Gets the layout for the web page by name.
	// - $Str_Name:				Name of the layout to get
	// * Return:				Layout file as a string
	public function layout($Str_Name, $Str_Theme='')
	{
		$Str_Layout = '';
		global $CMD;

		if (($Str_Theme = $this->has_asset('layouts/'.$Str_Name.'.html', $Str_Theme)) !== false)
		{
			$Str_Layout = $CMD->helper('file')->get_file_as_string(MW_CONST_STR_DIR_DOMAIN.'themes/'.$Str_Theme.'/layouts/'.$Str_Name.'.html');
		}

		return $Str_Layout;
	}

	//Sets the page parcel object's data locally.
	// - $Arr_ParcelData:		Data for the page parcel object
	// * Return:				VOID
	public function set_page_data($Arr_ParcelData)
	{
		$this->Arr_PageData = $Arr_ParcelData;
		return $this;
	}

	//Handles miscellaneous functions applied to the form object.
	//*!* This callback functionbality needs to be rewritten.
	//need to replicate the template functions from the old template system
	//and incorporate them into the new system with that functionality extened so plugins can work to the same effect.
	// - $Str_Function:			Name of the function
	// - $Str_Paramters:		Build paramter string
	// * Return:				VOID
	// * NB: This method must return VOID
	public function call($Str_Function, $Str_Paramters)
	{
		global $CMD;

		$Arr_Paramters = explode(MW_HOOK_CHAR_SEPARATOR, $Str_Paramters);
		$Arr_NewActive = $this->Arr_Active;

		//I'm also adding t these function calls the append, prepend and replace for both description and keywords.

		//Make build function call.
		switch ($Str_Function)
		{
			case 'asset':
				$this->add_callback('get_asset_url', $Str_Paramters);
				break;
			case 'document':
				$this->add_callback('get_document_string', $Str_Paramters);
				break;
			case 'styles':
				$this->add_callback('get_styles_string', $Str_Paramters);
				$Arr_NewActive['asset'] = 'styles';
				break;
			case 'scripts':
				$this->add_callback('get_scripts_string', $Str_Paramters);
				$Arr_NewActive['asset'] = 'scripts';
				break;
			case 'meta':
				$this->add_callback('get_metadata_string', $Str_Paramters);
				break;
			case 'add':
				$Arr_NewActive['action'] = 'add';
				$Arr_NewActive['asset'] = $Arr_Paramters[0];
				break;
			case 'remove':
				$Arr_NewActive['action'] = 'remove';
				$Arr_NewActive['asset'] = $Arr_Paramters[0];
				break;
			default:
				if ($Arr_NewActive['asset'] == 'styles' || $Arr_NewActive['asset'] == 'scripts')
				{
					$Str_Theme = (isset($Arr_Paramters[1]))? $Arr_Paramters[1]: '';
					$this->add_callback($Arr_NewActive['action'].'_'.$Arr_NewActive['asset'], $Str_Function.MW_HOOK_CHAR_SEPARATOR.$Arr_Paramters[0].MW_HOOK_CHAR_SEPARATOR.$Str_Theme);
				}
		}

		$this->Arr_Active = $Arr_NewActive;

		return;
	}

	//Gets the URL of a layout theme asset.
	// - $Arr_Parameters:			Assets parameters as an array($Str_AssetFile, $Str_Theme='', $Str_ExternalUrl)
	// * Return:					Theme asset file location as a URL
	public function get_asset_url($Arr_Parameters)
	{
		$Str_Location = '';
		global $CMD;

		//If there is a documet paramter then get the parcel named.
		if (isset($Arr_Parameters[0]) && $Arr_Parameters[0])
		{
			//If there is an external url get the location
			//*!* I need to think about setting up CDN plugin and look for some values in that for external locations.
			//if (isset($Arr_Parameters[2]) && $Arr_Parameters[2])

			//Get the theme for the asset
			//*!*Will probably come back here at a later point in time to add the default value if theme cannot be found.
			$Str_Theme = (isset($Arr_Parameters[1]) && $Arr_Parameters[1])? $Arr_Parameters[1]: $CMD->config('theme');
			$Str_Location = MW_CONST_STR_URL_DOMAIN.'/themes/'.$Str_Theme.'/'.$Arr_Parameters[0];
		}

		return $Str_Location;
	}

	//Gets a document parcel binary data as a string.
	// - $Arr_Parameters:		Document paramters as an array(Str_Reference, $Str_Group)
	// * Return:				Document binary text as a string for template output.
	public function get_document_string($Arr_Parameters)
	{
		$Str_Document = '';
		global $CMD;

		//If there is a documet paramter then get the parcel named.
		if (isset($Arr_Parameters[0]) && $Arr_Parameters[0])
		{
			$Obj_Document = $CMD->parcel('document', $Arr_Parameters[0]);
			if (!$Str_Document = $Obj_Document->data($Obj_Document->get_file_field()))
			{
				$CMD->handle_exception('Empty document returned matching supplied key', 'MW:101');
			}
		}
		//Otherwise get the document parcel referenced by the current page.
		else
		{
			//Get the document linked to the set page.
			if (!$this->Arr_PageData)
			{
				$CMD->handle_exception('No parcel data attached to page', 'MW:101');
			}
			else
			{
				$Arr_Documents = $CMD->collection($CMD->parcel('document'))
											->linked($CMD->parcel('page'), array($this->Arr_PageData['parcl_id']))
											->fetch();

				//If no document was linked handle exception.
				if (!$Arr_Documents)
				{
					$CMD->handle_exception('No document linked to page was found', 'MW:101');
				}
				//Otherwise get the first document found.
				else
				{
					$Str_Document = $Arr_Documents[0]['file'];
				}
			}
		}

		return $Str_Document;
	}

	//Finds if the asset exists within the theme hierarchy of the page.
	// * Return:				Name of theme asset is in, otherwise false
	public function has_asset($Str_Asset, $Str_Theme='')
	{
		global $CMD;

		if ($Str_Theme)
		{
			if (!file_exists(MW_CONST_STR_DIR_DOMAIN.'themes/'.$Str_Theme.'/'.$Str_Asset))
			{
				if (file_exists(MW_CONST_STR_DIR_DOMAIN.'themes/'.$CMD->get_interface_data('theme').'/'.$Str_Asset))
				{
					$Str_Theme = $CMD->get_interface_data('theme');
				}
				elseif (file_exists(MW_CONST_STR_DIR_DOMAIN.'themes/'.$CMD->config('theme').'/'.$Str_Asset))
				{
					$Str_Theme = $CMD->config('theme');
				}
			}
		}
		elseif (file_exists(MW_CONST_STR_DIR_DOMAIN.'themes/default/'.$Str_Asset))
		{
			$Str_Theme = 'default';
		}

		if (!$Str_Theme)
		{
			$CMD->handle_exception('Page asset '.$Str_Asset. ' does not exist', 'MW:101');
			$Str_Theme = false;
		}

		return $Str_Theme;
	}

	//Adds imported asset to interface asset list.
	// - $Arr_Assets:			Asset list to add placeholder reference to
	// - $Str_Reference:		Placeholder value to add to asset list
	// - $Str_Group:			Location group of the asset to add, default = false(no asset grouping)
	// * Return:				Asset list with asset reference added to it
	// * NB: This function will remove all other references of the asset in list unless it is already placed as defined
	public function add_asset($Arr_Assets, $Str_Reference, $Str_Group=false)
	{
		//If the group exists check for the value.
		if (array_key_exists($Str_Group, $Arr_Assets))
		{
			foreach($Arr_Assets as $Str_GroupName => $Str_GroupValues)
			{
				//Remove all references in other groups.
				if ($Str_GroupName != $Str_Group)
				{
					for ($i = 0; $i < count($Str_GroupValues); $i++)
					{
						if ($Str_GroupValues[$i] == $Str_Reference)
						{
							unset($Arr_Assets[$Str_GroupName][$i]);
						}
					}
				}
				//Add value to group if it doesn't already exist.
				elseif ($Str_GroupName == $Str_Group && !in_array($Str_Reference, $Str_GroupValues))
				{
					$Arr_Assets[$Str_GroupName][] = $Str_Reference;
				}
			}
		}
		//Otherwise create new group with reference.
		else
		{
			$Arr_Assets[$Str_Group] = array($Str_Reference);
		}

		return $Arr_Assets;
	}

	//Removes imported asset to interface asset list.
	// - $Arr_Assets:			Asset list to remove placeholder reference from
	// - $Str_Reference:		Placeholder value to remove from asset list
	// - $Str_Group:			Location group of the asset to remove, default = false(no asset grouping)
	// * Return:				Asset list with asset reference removed from it
	public function remove_asset($Arr_Assets, $Str_Reference, $Str_Group=false)
	{
		if (isset($Arr_Assets[$Str_Group]) && $Arr_Assets[$Str_Group])
		{
			for ($i = 0; $i < count($Arr_Assets[$Str_Group]); $i++)
			{
				if ($Arr_Assets[$Str_Group][$i] == $Str_Reference)
				{
					unset($Arr_Assets[$Str_Group][$i]);
					break;
				}
			}
		}
	}

	//Builds a stylesheet file import reference for an HTML head section.
	// - $Arr_Parameters:		Import paramters as an array(Str_Reference, $Str_Group, $Str_Theme)
	// * Return:				VOID
	public function add_styles($Arr_Parameters)
	{
		global $CMD;

		//If the asset exists within the theme hierarchy add it.
		$Str_Theme = (isset($Arr_Parameters[2]))? $Arr_Parameters[2]: '';
		if ($Str_Theme = $this->has_asset($Arr_Parameters[1].'.css', $Str_Theme))
		{
			$Arr_Styles = $CMD->get_interface_data('styles');
			$Arr_Styles = $this->add_asset($Arr_Styles, $Str_Theme.'/'.$Arr_Parameters[1], $Arr_Parameters[0]);
			$CMD->set_interface_data('styles', $Arr_Styles);
		}

		return;
	}

	//Builds a javascript file import reference for an HTML head section.
	// - $Arr_Parameters:		Import paramters as an array(Str_Reference, $Str_Group, $Str_Theme)
	// * Return:				VOID
	public function add_scripts($Arr_Parameters)
	{
		global $CMD;

		//If the asset exists within the theme hierarchy add it.
		$Str_Theme = (isset($Arr_Parameters[2]))? $Arr_Parameters[2]: '';
		if ($Str_Theme = $this->has_asset($Arr_Parameters[1].'.js', $Str_Theme))
		{
			$Arr_Scripts = $CMD->get_interface_data('scripts');
			$Arr_Scripts = $this->add_asset($Arr_Scripts, $Str_Theme.'/'.$Arr_Parameters[1], $Arr_Parameters[0]);
			$CMD->set_interface_data('scripts', $Arr_Scripts);
		}

		return;
	}

	//Removes from a stylesheet file import reference for an HTML head section.
	// - $Arr_Parameters:		Import paramters as an array(Str_Reference, $Str_Group)
	// * Return:				HTML stylesheet asset import reference as a string
	public function remove_styles($Arr_Parameters)
	{
		global $CMD;
		$Arr_Styles = $CMD->get_interface_data('styles');
		$Arr_Styles = $this->remove_asset($Arr_Styles, $Arr_Parameters[1], $Arr_Parameters[0]);
		$CMD->set_interface_data('styles', $Arr_Styles);
	}

	//Removes from a javascript file import reference for an HTML head section.
	// - $Arr_Parameters:		Import paramters as an array(Str_Reference, $Str_Group)
	// * Return:				Void
	public function remove_scripts($Arr_Parameters)
	{
		global $CMD;
		$Arr_Scripts = $CMD->get_interface_data('scripts');
		$Arr_Scripts = $this->add_asset($Arr_Scripts, $Arr_Parameters[1], $Arr_Parameters[0]);
		$CMD->set_interface_data('scripts', $Arr_Scripts);
	}

	//Gets the stylesheet import group as a markup string.
	// - $Arr_Parameters:		Callback function parameter array($Str_Group)
	// * Return:				Stylesheet import group as a string
	public function get_styles_string($Arr_Parameters)
	{
		$Str_Styles = '';

		global $CMD;
		$Arr_Styles = $CMD->get_interface_data('styles');

		if (isset($Arr_Styles[$Arr_Parameters[0]]))
		{
			foreach ($Arr_Styles[$Arr_Parameters[0]] as $Str_Style)
			{
				$Str_Styles .= '<link rel="stylesheet" href="'.$CMD->config('root_url').'/themes/'.$Str_Style.'.css" type="text/css" media="screen, projection, print" />
';
			}
		}

		return $Str_Styles;

	}

	//Gets the javascript import group as a markup string.
	// - $Arr_Parameters:		Callback function parameter array($Str_Group)
	// * Return:				Javascript import group as a string
	public function get_scripts_string($Arr_Parameters)
	{
		$Str_Scripts = '';

		global $CMD;
		$Arr_Scripts = $CMD->get_interface_data('scripts');

		if (isset($Arr_Scripts[$Arr_Parameters[0]]))
		{
			foreach ($Arr_Scripts[$Arr_Parameters[0]] as $Str_Script)
			{
				$Str_Scripts .= '<script src="'.$CMD->config('root_url').'/themes/'.$Str_Script.'.js" type="text/javascript"></script>
';
			}
		}

		return $Str_Scripts;
	}

	//Gets the page meta data tag content as a string.
	// - $Arr_Parameters:		Callback function parameter array($Str_TagType)
	// * Return:				Page meta data tag content as a string
	public function get_metadata_string($Arr_Parameters)
	{
		global $CMD;
		$Arr_MetaData = $CMD->get_interface_data('meta');

		//If the webpage meta data is not set handle error.
		if (!isset($Arr_MetaData[$Arr_Parameters[0]]))
		{
			$CMD->handle_exception('Webpage meta data '.$Arr_Parameters[0].' not set', 'MW:101');
			return '';
		}

		return $Arr_MetaData[$Arr_Parameters[0]];
	}



///////////////////////////////////////////////////////////////////////////////
//                 P L U G I N   A P I   F U N C T I O N S                   //
///////////////////////////////////////////////////////////////////////////////

	//Initiates the addition of an asset by type through object chaining.
	// - $Str_AssetType:		Type of asset to add: styles, scripts, meta
	// * Return:				SELF
	// * NB: The duplicates the builder callback functionality
	public function add($Str_AssetType)
	{
		$Arr_NewActive = $this->Arr_Active;

		$Arr_NewActive['action'] = 'add';
		$Arr_NewActive['asset'] = $Str_AssetType;

		$this->Arr_Active = $Arr_NewActive;

		return $this;
	}

	//Initiates the removal of an asset by type through object chaining.
	// - $Str_AssetType:		Type of asset to remove: styles, scripts, metadata
	// * Return:				SELF
	// * NB: The duplicates the builder callback functionality
	public function remove($Str_AssetType)
	{
		$Arr_NewActive = $this->Arr_Active;

		$Arr_NewActive['action'] = 'remove';
		$Arr_NewActive['asset'] = $Str_AssetType;

		$this->Arr_Active = $Arr_NewActive;

		return $this;
	}

	//Assigns an interface asset to a group with the optional theme parameter.
	// - $Str_Group:				Group name to which the asset is assigned
	// - $Str_Asset:				Asset identifier which is being assigned to a group
	// - $Str_Theme:				Theme which the asset is within, default false(use config theme)
	// * Return:					SELF
	public function group($Str_Group, $Str_Asset, $Str_Theme=false)
	{
		$Arr_Parameters = array($Str_Group, $Str_Asset);
		if ($Str_Theme)
		{
			$Arr_Parameters[] = $Str_Theme;
		}

		$Str_Method = $this->Arr_Active['action'].'_'.$this->Arr_Active['asset'];
		$this->$Str_Method($Arr_Parameters);

		return $this;
	}

	//Gets the import markup string for a group of style assets.
	// - $Str_Group:				Group name to get the style markup for
	// * Return:					SELF
	// * NB: This is a wrapper function for get_styles_string()
	public function styles($Str_Group)
	{
		echo $this->get_styles_string(array($Str_Group));

		return $this;
	}

	//Gets the import markup string for a group of script assets.
	// - $Str_Group:				Group name to get the script markup for
	// * Return:					SELF
	// * NB: This is a wrapper function for get_scripts_string()
	public function scripts($Str_Group)
	{
		echo $this->get_scripts_string(array($Str_Group));

		return $this;
	}

	//Gets the full file path as a string for a page asset.
	// - $Str_Asset:			Asset file name to retrieve
	// - $Str_Theme:			Theme in which the asset will be retrieved, default = false(config defined theme)
	// * Return:				Full file path of page asset.
	// * NB: This is a wrapper function for get_asset_url();
	public function asset($Str_Asset, $Str_Theme=false)
	{
		$Arr_Paramaters = array($Str_Asset);

		if ($Str_Theme)
		{
			$Arr_Paramaters[] = $Str_Theme;
		}

		return $this->get_asset_url($Arr_Paramaters);
	}




///////////////////////////////////////////////////////////////////////////////
//                 P A G I N A T I O N   F U N C T I O N S                   //
///////////////////////////////////////////////////////////////////////////////

	//Builds a pagination structure for pagination display processing within a template.
	// - $Obj_Collection:		Collection object holding the search parameters
	// - $Int_Range:			Number of results to display per page
	// - $Int_Page:				Current set of results in the collection search
	// - $Int_Links:			Number of links to display either side of current results
	// * Return:				Array of pagination values for display links
	// * NB: $Obj_Collection can be a collection array for mixed searches, will do later.
	public function pagination($Obj_Collection, $Int_Range, $Int_Page, $Int_Links, $Arr_Options=false)
	{
/*
		//These can all be set to false if not needed.
		$Arr_Options['first'] = '|<';
		//$Arr_Options['last'] = '>|'; - I should not a have a last indicator.
		$Arr_Options['next'] = '>>';
		$Arr_Options['prev'] = '<<';
		$Arr_Options['nav'] = 'url/get';
		$Arr_Options['link'] = 'http://www/foo?bar=#';
		$Arr_Options['separator'] = '|'; //turn off = false.

		$Arr_Pagination[]['type'] = 'link/separator/holder';
		$Arr_Pagination[]['text'] = 'first/last/#/next/prev';
		$Arr_Pagination[]['url'] = 'first/last/#/next/prev';
*/
		$Arr_Pagination = array();
		$Int_Pagination = 0;
		$Bol_Pagination = false;

		//Get pagination menu items.
		$Int_Start = ($Int_Page < $Int_Links + 1)? 1: $Int_Page;
		$Int_Finish = $Int_Page + $Int_Links;

		for ($i = $Int_Start; $i <= $Int_Finish; $i++)
		{
			if ($i == $Int_Page)
			{
				$Arr_Pagination[] = array('type'=>'holder', 'text'=>(string) $i);
				$Int_Pagination++;
			}
			elseif ($x = $Obj_Collection->limit($Int_Range * $i - $Int_Range + 1, 1)->fetch())
			{
				$Arr_Pagination[] = array('type'=>'link', 'text'=>(string) $i);
				$Bol_Pagination = true;
				$Int_Pagination++;
			}
		}

		if (!$Bol_Pagination)
		{
			return false;
		}

		//Look for additional results.
		if ($Obj_Collection->limit($Int_Range * $Int_Page * $Int_Links + 1, 1)->fetch())
		{
			$Arr_Pagination[] = array('type'=>'holder', 'text'=>'&#133;');
			$Bol_MoreResults = true;
		}

		return $Arr_Pagination;
	}

	//Outputs the pagination value
	// - $Mix_Value:		page, link(url), title
	// - $Str_BaseUrl:		Url to build links for, default = false(full path with get vars)
	public function paginate($Arr_Pagination, $Mix_Value='page', $Str_BaseUrl=false)
	{

	}

}

?>