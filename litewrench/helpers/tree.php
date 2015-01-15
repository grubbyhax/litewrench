<?php
//tree.php
//Developed by David Thomson
//This file supplies basic SQL functionallity in XML.


/*
This first thing that should be noted is that I've found this helper hard to conceptualise
I need to define exactly what I want to do with this object, possibly make use cases


*/

class MW_Helper_Tree extends MW_Utility_Helper
{
///////////////////////////////////////////////////////////////////////////////
//                       C L A S S   V A R I A B L E S                       //
///////////////////////////////////////////////////////////////////////////////

	protected $Arr_Properties	= array(
		'Arr_Callbacks'			=> array(),	//List of callback methods.
		'Arr_PathNodes'			=> array(),	//Path node details in the URL.
		'Arr_BuildPath'			=> array(), //Path on which to build a branch(Str_ViewPath).
		'Arr_BuiltPath'			=> array(),	//Path on which the branch was built(Str_FilePath).
		'Obj_DOMTree'			=> null);	//The DOM Document representing the tree.



///////////////////////////////////////////////////////////////////////////////
//                        M A G I C   M E T H O D S                          //
///////////////////////////////////////////////////////////////////////////////
/*
These two functions need to be redone as this class is now a helper not a utility.
The __set needs to be moved over to a new function and any setting/getting in the functions of this  class need to be re-referenced.
The function in this class are yet to be fully redacted from it's initial use many years ago with xml databasing.
	//Constructor.
	// * Return:					VOID
	public function __construct()
	{
		//*!* This needs to be moved.
		$this->Str_DocumentPath = MW_CONST_STR_DIR_INSTALL.'data/';
		//$this->load_dom_xml_file($Str_DataFileName);
	}

	//Sets variable locally by outside object.
	// * Return:					VOID
	public function __set($Var_Name, $Var_Value)
	{
		$this->$Var_Name = $Var_Value;
	}
*/


///////////////////////////////////////////////////////////////////////////////
//                       C O R E   F U N C T I O N S                         //
///////////////////////////////////////////////////////////////////////////////

	//Gets the node elements of a sitemap tree corresponding to a url path
	// - $Str_Url:				URL path to build sitemap path
	// - $Obj_RootNode:			XML Element to asses, default = false(fetch element from database)
	//*!*This is either supplying the dom element to start the lookup from or the lookup defatult to the root sitemap
	//of the request.
	//Root node needs to be an identifier.
	//*!*This function has simply become a placeholder as the kogic really needs to be split into a number of separate routines.
	public function url_path_nodes($Str_Url, $Obj_RootNode=false)
	{
		$Arr_PathElements = array();

/* Here's the structure of the returned array
$Arr_PathNodes = array(
	array(
		'url_path'		=> 'url/path',
		'sitemap_key'	=> 'sitemap_key',
		'sitemap_id'	=> 'sitemap_id',
		'webpage_key'	=> 'webpage_key',
		'webpage_id'	=> 'webpage_id',
		'view_access'	=> 'access_str',
		'edit_access'	=> 'access_str',
		'xml'			=> $xmlElement(w/children)));

//I'll need to assemble all the sitemap chunks one by one, by following the path.
//Then go back and do a collect on the webpages and add their data to the array.

What I want to do is collect these elements with the immediate child elements only so, I'll have to cycle through
all grandchild elements and remove them before creating this structure.

The other way I might do it is just to create a modified XML file with only the path elements with their siblings
Though, this is not such a good strategy in terms of performance, best to have an array?

I should also pass the parcel type to use as a parameter


HERES SOME NOTES ABOUT WHAT I WANT TO ACHIVE WITH THIS
***Looks like I'm going to have to design the authorisation object first and,
this particular function becomes somewhat of a wrapper for handling the authorisation object.
- the upath needs to rebuild parts of configure_request method
- All possible page ids of the path need to be gotten to make a single db call to check authorisation
- the authorisation of each page needs to be assessed progressively through the tree to find permissions
	-senario variations:
		-do url lookup, basic page request
		-assess page permission against user permission
		-get page request variables(filepath, viewpath, pathvars)
	-as an aside, I'll need to be able to run multiple permissions to be able to display for an admin what others can see,
	which means that, i'll even have to assess if the admin has the permission to see what other can see. This will also mean that
	I'll need a 'you cannot see this' holder in the templating of the menu
-also need to be able to build breadcrumbs and navigation templates, this needs that permission lookup.
-must be able to work backwards up a tree, sideways, etc
-I'll have pay attention to version of tree which will need to be set temporarily on the helper of something,
	maybe this is going on the parcel/package, which seems to make sense
-I also need to be able to access the permission variables which can't be global
	as this function is goin to be used to check internal processes and maintainance tasks.


*/

/*---------------------------------------------------------------------------*/


/*---------------------------------------------------------------------------*/

		return $Arr_PathElements;
	}

	//Returns the path nodes which have been set locally.
	// * Return:				Path nodes which have been built
	public function path_nodes()
	{
		//If there is no path nodes handle exception.
		if (!$this->Arr_PathNodes)
		{
			global $GLB_FOREMAN;
			$GLB_FOREMAN->handle_exception('No path nodes are attached to the Tree', 'MW:101');

			return false;
		}
		
		return $this->Arr_PathNodes;
	}

	//Gets all child nodes of an element by node type.
	// - $Obj_Element:			Element to retrieve all child nodes of
	// - $Int_NodeType:			Type of node type to get(XML_xxx_NODE)
	// - $Str_NodeName:			Name of the node, default = false(get nodes of all names)
	// - $Str_NodeValue:		Value of the node, default = false(get nodes of all values)
	public function get_child_nodes($Obj_Element, $Int_NodeType, $Str_NodeName=false, $Str_NodeValue=false)
	{
		$Arr_Children = array();

		if ($Obj_Element->nodeType != XML_ELEMENT_NODE)
		{
			global $CMD;
			$CMD->handle_exception('Object not a DOM element', 'MW:101');
			return false;
		}

		if ($Int_NodeType == XML_ATTRIBUTE_NODE)
		{
			$Arr_Attributes = $Obj_Element->attributes;
			foreach ($Arr_Attributes as $Obj_Attribute)
			{
				$Bol_Name = false;
				$Bol_Value = false;

				if ($Str_NodeName)
				{
					$Bol_Name = ($Str_NodeName == $Obj_Attribute->nodeName)? true: false;
				}
				else
				{
					$Bol_Name = true;
				}

				if ($Str_NodeValue)
				{
					$Bol_Value = ($Str_NodeValue == $Obj_Attribute->nodeValue)? true: false;
				}
				else
				{
					$Bol_Value = true;
				}

				if ($Bol_Name && $Bol_Value)
				{
					$Arr_Children[$Obj_Attribute->nodeName] = $Obj_Attribute->nodeValue;
				}
			}
		}
		else
		{
			$Arr_ChildNodes = $Obj_Element->childNodes;
			foreach ($Arr_ChildNodes as $Obj_ChildNode)
			{
				if ($Obj_ChildNode->nodeType == $Int_NodeType)
				{
					$Bol_Name = false;
					$Bol_Value = false;

					if ($Str_NodeName)
					{
						$Bol_Name = ($Str_NodeName == $Obj_ChildNode->nodeName)? true: false;
					}
					else
					{
						$Bol_Name = true;
					}

					if ($Str_NodeValue)
					{
						$Bol_Value = ($Str_NodeValue == $Obj_ChildNode->nodeValue)? true: false;
					}
					else
					{
						$Bol_Value = true;
					}

					if ($Bol_Name && $Bol_Value)
					{
						$Arr_Children[] = $Obj_ChildNode;
					}
				}
			}
		}

		return $Arr_Children;
	}

	//Gets all child nodes of an element by node type.
	// - $Obj_Element:			Element to retrieve all child nodes of
	// - $Int_NodeType:			Type of node type to get(XML_xxx_NODE)
	//*!*This function is depreciated.
	public function get_child_nodes_by_type($Obj_Element, $Int_NodeType)
	{
		$Arr_Children = array();

		if ($Obj_Element->nodeType != XML_ELEMENT_NODE)
		{
			global $CMD;
			$CMD->handle_exception('Object not a DOM element', 'MW:101');
			return false;
		}

		if ($Int_NodeType == XML_ATTRIBUTE_NODE)
		{
			$Arr_Attributes = $Obj_Element->attributes;
			foreach ($Arr_Attributes as $Obj_Attribute)
			{
				$Arr_Children[$Obj_Attribute->nodeName] = $Obj_Attribute->nodeValue;
			}
		}
		else
		{
			$Arr_ChildNodes = $Obj_Element->childNodes;
			foreach ($Arr_ChildNodes as $Obj_ChildNode)
			{
				if ($Obj_ChildNode->nodeType == $Int_NodeType)
				{
					$Arr_Children[] = $Obj_ChildNode;
				}
			}
		}

		return $Arr_Children;
	}

	//Recursive function to attach tree branches to the branch element along the tree path supplied.
	// - $Obj_BranchElement:		DOM Element on which to attach bracnh elements
	// - $Arr_BranchPath:			Array of branch pathways to add branch elements
	// * Return:					Branch element with branches appended along the branch path
	public function attach_branch($Obj_BranchElement, $Arr_BranchPath)
	{
		global $GLB_FOREMAN;

		//Build along the view path.
		$Arr_ChildElements = $this->get_child_nodes_by_type($Obj_BranchElement, XML_ELEMENT_NODE);

		//If there is a branch path import additional tree branches from in the children.
		//*!*I might put in the ability to define Arr_BranchPath as a string as well as an array for extensible use.
		if (is_array($Arr_BranchPath) && isset($Arr_BranchPath[0]) && $Arr_BranchPath[0])
		{
			foreach ($Arr_ChildElements as $Obj_Child)
			{
				if (!$Obj_Child->hasAttribute('key'))
				{
					$GLB_FOREMAN->handle_exception('Tree node has no key defined', 'MW:101');
					continue;
				}

				if (!$Obj_Child->hasAttribute('type'))
				{
					$GLB_FOREMAN->handle_exception('Tree node has no type defined', 'MW:101');
					continue;
				}

				//If the noe is on the branch path attach node.
				if ($Arr_BranchPath[0] == $Obj_Child->getAttribute('name'))
				{
					//If the element is a join type attach the referenced sitemap.
					if ($Obj_Child->getAttribute('type') == 'join')
					{
						//Get the tree dom document and attach it to the element
						$Obj_ReplacmentChild = new DOMDocument();
						$Obj_ReplacmentChild->loadXML('<root>'.$this->parcel('tree', $this->Arr_SysConfig['sitemap'])->data('tree').'</root>');
						$Obj_ReplacmentChild = $Obj_ReplacmentChild->getElementsByTagName('node')->item(0);
						if (!count($Obj_ReplacmentChild))
						{
							//Add path value to built branch.
							$Arr_NewBuiltPath = $this->Arr_BuiltPath;
							$Arr_NewBuiltPath[] = $Arr_BranchPath[0];
							$this->Arr_BuiltPath = $Arr_NewBuiltPath;

							//Investigate down the branch path
							$Arr_NewBranchPath = array_slice($Arr_BranchPath, 1, count($Arr_BranchPath) -1);
							$Obj_ReplacmentChild = $this->attach_branch($Obj_ReplacmentChild, $Arr_NewBranchPath);

							//Attach replacement to the document.
							$this->Obj_DOMTree->importNode($Obj_ReplacmentChild, true);
							$Obj_BranchElement->replaceChild($Obj_ReplacmentChild, $Obj_Child);
						}
					}
					//If the child is a data element then add it.
					elseif ($Obj_Child->getAttribute('type') == 'data')
					{
						//Add path value to built branch.
						$Arr_NewBuiltPath = $this->Arr_BuiltPath;
						$Arr_NewBuiltPath[] = $Arr_BranchPath[0];
						$this->Arr_BuiltPath = $Arr_NewBuiltPath;

						//Investigate down the branch path
						$Arr_NewBranchPath = array_slice($Arr_BranchPath, 1, count($Arr_BranchPath) -1);
						$Obj_ReplacmentChild = $this->attach_branch($Obj_Child, $Arr_NewBranchPath);

						//Attach replacement to the document.
						$Obj_BranchElement->replaceChild($Obj_ReplacmentChild, $Obj_Child);
					}
					//Otherwise remove the element from the document.
					else
					{
						$Obj_BranchElement->removeChild($Obj_Child);
					}
				}
				//Otherwise remove the element from the document.
				else
				{
					$Obj_BranchElement->removeChild($Obj_Child);
				}
			}
		}
		//Otherwise remove all the child elements.
		elseif ($Arr_ChildElements = $this->get_child_nodes_by_type($Obj_BranchElement, XML_ELEMENT_NODE))
		{
			for ($i = 0; $i < count($Arr_ChildElements); $i++)
			{
				$Obj_BranchElement->removeChild($Arr_ChildElements[$i]);
			}
		}

		return $Obj_BranchElement;
	}

	//Builds a branchless DOM Document tree along the path specified.
	// - $Mix_Tree:				The document tree as a string or a DOM DOcument
	// - $Arr_Path:				The path node keys to build along as an array
	// * Return:				VOID
	// * NB: This function assigns the build DOM Document tree locally for later use.
	public function build_branch_nodes($Mix_Tree, $Arr_Path=false)
	{
		global $GLB_FOREMAN;

		//If the build path is not supplied use existing build path.
		if ($Arr_Path === false)
		{
			if (!$this->Arr_BuildPath)
			{
				$GLB_FOREMAN->handle_exception('No build path exists to construct tree.', 'MW:101');
				return $this;
			}
		}
		//Otherwise use the supplied build path.
		else
		{
			$this->Arr_BuildPath = $Arr_Path;
		}

		//If the tree supplied is a string create a DOM Document.
		if (is_string($Mix_Tree))
		{
			$this->Obj_DOMTree = new DOMDocument();
			$this->Obj_DOMTree->loadXML('<root>'.$Mix_Tree.'</root>');
		}
		else
		{
			//*!*I should test that this is a dom document here.
			$this->Obj_DOMTree = $Mix_Tree;
		}

		//Get first node in sitemap.
		$Obj_RootDataNode = $this->Obj_DOMTree->getElementsByTagName('node')->item(0);
		if (!count($Obj_RootDataNode))
		{
			$GLB_FOREMAN->handle_exception('No webpages found in sitemap tree file.', 'MW:100');
			$Obj_DataNode = $this->Obj_DOMTree->createElement('node');
			$Obj_DataNode->setAttribute('type', 'data');
			$Obj_DataNode->setAttribute('name', '');
			$this->Obj_DOMTree->appendChild($Obj_DataNode);
		}
		else
		{
			//Get any child branches of the tree.
			$Obj_ReplacementElement = $this->attach_branch($Obj_RootDataNode, $Arr_Path);

			//I need to import the return element onto the existing document and do a replace.
			$this->Obj_DOMTree->documentElement->replaceChild($Obj_ReplacementElement, $Obj_RootDataNode);
		}

		return $this;
	}

	//Loads node data onto tree nodes by parcel type.
	// - $Str_ParcelType:		Type of parcel to load node data onto tree
	// * Return:				VOID
	public function load_node_data($Str_ParcelType)
	{
		//Decouple path nodes.
		$Arr_NewPathNodes = $this->Arr_PathNodes;

		//Get all the ids of the parcels.
		$Arr_DataNodes = $this->Obj_DOMTree->documentElement->getElementsByTagName('node');

		$Arr_ParcelKeys = array();
		foreach ($Arr_DataNodes as $Obj_DataNode)
		{
			$Arr_ParcelKeys[] = $Obj_DataNode->getAttribute('key');
		}
		global $GLB_FOREMAN;
		$Obj_Parcel = $GLB_FOREMAN->parcel($Str_ParcelType);
		$Arr_NodeData = $GLB_FOREMAN->collection($Obj_Parcel)->where(array($Obj_Parcel->get_key_field(), 'in', $Arr_ParcelKeys))->fetch();

		//Attach the node data to the local DOM document.
		for ($i = 0; $i < $Arr_DataNodes->length; $i++)
		{
			for ($j = 0; $j < count($Arr_NodeData); $j++)
			{
				if ($Arr_DataNodes->item($i)->getAttribute('key') == $Arr_NodeData[$j][$Obj_Parcel->get_key_field()])
				{
					$Arr_PathNodeValues = array();
					foreach ($Arr_NodeData[$j] as $Str_NodeKey => $Str_NodeData)
					{
						//Retain the node key from the hierarchy branch.
						if ($Str_NodeKey != $Obj_Parcel->get_key_field())
						{
							$Arr_PathNodeValues[$Str_NodeKey] = $Str_NodeData;
							$Arr_DataNodes->item($i)->setAttribute($Str_NodeKey, $Str_NodeData);
						}
						else
						{
							$Arr_PathNodeValues[$Str_NodeKey] = $Arr_DataNodes->item($i)->getAttribute('name');
						}
					}

					$Arr_NewPathNodes[] = $Arr_PathNodeValues;
				}
			}
		}

		//Recouple path nodes.
		$this->Arr_PathNodes = $Arr_NewPathNodes;

		return $this;
	}

	//Builds a menu list.
	public function build_menu()
	{

	}

	//Sets the DOM tree from a binary string for building navigation.
	// - $Str_Tree:				String of DOM Tree to build navigation elements
	public function binary($Str_Tree)
	{
		//If the tree is an empty string handle excpetion
		if (!$Str_Tree)
		{
			global $GLB_FOREMAN;
			$GLB_FOREMAN->handle_exception('Tree string empty', 'MW:101');
			return $this;
		}

		//Create DOM document from tree string.
		//Not sure if I should do error suppression here.
		$Obj_Tree = new DOMDocument();
		if (!$Obj_Tree->loadXML('<root>'.$Str_Tree.'</root>'))
		{
			global $GLB_FOREMAN;
			$GLB_FOREMAN->handle_exception('Tree string not valid', 'MW:101');
			return $this;
		}

		//Add tree document locally.
		$Arr_NewProperties = $this->Arr_Properties;
		$Arr_NewProperties['Arr_DOMTree'] = $Obj_Tree;
		$this->Arr_Properties = $Arr_NewProperties;

		return $this;
	}



///////////////////////////////////////////////////////////////////////////////
//                    U T I L I T Y   F U N C T I O N S                      //
///////////////////////////////////////////////////////////////////////////////

	//Searches tree element to find the child element with matching alias attribute.
	// - $Obj_Element:				Element to search for matching alias
	// - $Str_Alias:				Alias attribute value to find on child elements
	// * Return:					Child element with matching alias attribute if it exists, otherwise VOID
	private function get_child_by_alias($Obj_Element, $Str_Alias)
	{
		$Arr_Children = $Obj_Element->childNodes;
		foreach ($Arr_Children as $Obj_Node)
		{
			if (($Obj_Node->nodeType == XML_ELEMENT_NODE)
			&& ($Obj_Node->nodeName == 'node')
			&& ($Obj_Node->hasAttribute('alias'))
			&& ($Obj_Node->getAttribute('alias') == $Str_Alias))
				return $Obj_Node;
		}

		return;
	}

	//this kinda makes to function below a bit redundant...
	//need to know where I'm getting these tress from...
	//Need to keep track of the ids and keys to make sure that no infinate loops are being called here by evil users
	//also what needs to be parsed are the data types, field names and version info
	private function grow_tree(&$Obj_Tree)
	{
		$Bol_NodeAdded = false;
		global $GLB_FOREMAN;

		//For each join node get their child tree
		$Arr_Nodes = $Obj_Tree->getElementsByTagName('node');
		$Int_Nodes = count($Arr_Nodes);
		for ($i = 0; $i < $Int_Nodes; $i++)
		{
			$Obj_Node = $Obj_Tree->getElementsByTagName('node')->item($i);
			if ($Obj_Node->getAttribute('type') == 'join')
			{
				//Get new tree as an element.
				//*!*This needs to be fixed up
				$Obj_TreeData = $GLB_FOREMAN->parcel('tree', (int) $Obj_Node->getAttribute('id'));
				//I really should break up this function it will be hell to debug here.
				//in fact I need some alternate property setting debug here.
				$this->binary($Obj_TreeData->data($Obj_Sitemap->get_file_field()));
				$Obj_Branch = $this->Arr_Properties['Arr_DOMTree'];

				//Set alias of join element.
				$Obj_Branch->setAttribute('alias', $Obj_Node->getAttribute('alias'));

				//Replace join with new tree.
				$Obj_ImportedNode = $Obj_Tree->importNode($Obj_Branch);
				$Obj_Node->parentNode->replaceChild($Obj_ImportedNode, $Obj_Node);

				$Bol_NodeAdded = true;
			}
		}

		if ($Bol_NodeAdded)
		{
			$this->grow_tree($Obj_Tree);
		}

		return;
	}

	//Not sure is this is core or not
	//should this be private?
	//Builds tree document from the root data $Str_Key
	// - $Str_Key:				Data key of the root node to build the tree from
	//*Will probably have to parse the version here, I'm not parsing on the parcel here, will fix later
	//*!*I need to remember all the trees that get loaded so they can be cached
	//I don't think I need this function... I can just call the recursive
	private function build_tree($Str_Key, $Flt_Version, $Mix_Storage=false)
	{
		//Get the root from the database/file
		//global $GLB_FOREMAN;
		//$Obj_Sitemap = $GLB_FOREMAN->parcel('sitemap', $Str_Key, $Mix_Storage);

		$Obj_Tree;

		//Put the tree together.
		$this->grow_tree($Obj_Tree);

		//Set the grown tree locally.
		$Arr_NewProperties = $this->Arr_Properties;
		$Arr_NewProperties['Arr_DOMTree'] = $Obj_Tree;
		$this->Arr_Properties = $Arr_NewProperties;


	}

}

?>