<?php

	if(! defined('EXPAT_XML_INCLUDED'))
	{
		define('EXPAT_XML_INCLUDED', 1) ;
		
		class ExpatXmlParser
		{
			public $XmlObject = null;
			public $Output = array();
			public $Charset = 'ISO-8859-1';
			public $EncodeUtf8 = 0 ;
			public $ErrorString = "" ;
			public $CheckFilePath = true ;
			public $CaseInsensitive = 1 ;
			public $SkipWhiteSpaces = 1 ;
			public $AttrsCaseSensitive = 0 ;
			public $NameCaseSensitive = 1 ;
			protected function InitOutput($path="")
			{
				$this->Output = new ExpatXmlDocument();
				$this->Output->Path = $path ;
				$this->ErrorString = "" ;
				$this->init_Parser() ;
			}
			protected function init_Parser()
			{
				$this->XmlObject = xml_Parser_create($this->Charset);
				xml_Parser_set_option($this->XmlObject, XML_OPTION_SKIP_WHITE, $this->SkipWhiteSpaces) ;
				xml_Parser_set_option($this->XmlObject, XML_OPTION_CASE_FOLDING, $this->CaseInsensitive) ;
				xml_Parser_set_option($this->XmlObject, XML_OPTION_TARGET_ENCODING, $this->Charset) ;
				xml_set_object($this->XmlObject, $this);
				xml_set_character_data_handler($this->XmlObject, 'DataHandler');   
				xml_set_element_handler($this->XmlObject, "StartHandler", "EndHandler");
			}
			public function ParseFile($path)
			{
				$this->InitOutput($path) ;
				try
				{
					if($this->CheckFilePath && ! file_exists($path)) {
						$this->ErrorString = "The Path $path doesn't exists on this server" ;
						return false ;
					}
					if (!($fp = fopen($path, "r"))) {
						$this->ErrorString = "Cannot open XML data file: $path" ;
						return false;
					}
					while (($data = fread($fp, 4096)) != false) {	
						if($this->EncodeUtf8)
							$data = utf8_encode($data) ;
						$this->ParseData($data, feof($fp)) ;
					}
				}
				catch(Exception $ex)
				{
					$this->ErrorString = $ex->getMessage() ;
				}
				return $this->Output;
			}
			public function ParseContent($ctn)
			{
				$this->InitOutput() ;
				$this->ParseData($ctn, true) ;
				return $this->Output;
			}
			public function ParseError()
			{
				return $this->ErrorString ;
			}
			protected function ParseData($data, $eof=true)
			{
				// echo $data ;
				if(! $this->XmlObject)
				{
					$this->init_Parser() ;
				}
				if(! xml_Parse($this->XmlObject, $data, $eof))
				{
					$this->ErrorString = sprintf(
						"XML error: %s at line %d",
						xml_error_string(xml_get_error_code($this->XmlObject)),
						xml_get_current_line_number($this->XmlObject)
					) ;
					xml_Parser_free($this->XmlObject);
					$this->XmlObject = false ;
				}
			}
			public function DecodeAttrs($attrString, $encoding='ISO-8859-1')
			{
				$xmlData = '<?xml version="1.0" encoding="'.$encoding.'"><element '.$attrString.' />' ;
				$node = $this->ParseContent($xmlData) ;
				$attrs = null ;
				if(isset($node[0]))
				{
					$attrs = array() ;
					if(isset($node[0]['attrs']))
					{
						$attrs = $node[0]['attrs'] ;
					}
				}
				return $attrs ;
			}
			public function EncodeAttrs($attrs)
			{
				$ctn = '' ;
				$i = 0 ;
				foreach($attrs as $name => $value)
				{
					if($i > 0)
						$ctn .= ' ' ;
					$ctn .= htmlentities($name).'="'.htmlentities($value).'"' ;
					$i++ ;
				}
				return $ctn ;
			}
			public function StartHandler($Parser, $name, $attribs)
			{
				$element = $this->Output->CreateNode() ;
				$element->Name = $name ;
				if($this->AttrsCaseSensitive)
					$attribs = array_map("strtoupper", $attribs) ;
				$element->AddAttributes($attribs) ;
				$this->Output->AddNode($element);
			}
			public function DataHandler($Parser, $data)
			{
				if(! empty($data) || trim($data) === 0 || trim($data) === "") {
					$_output_idx = count($this->Output->ChildNodes) - 1;
					$this->Output->ChildNodes[$_output_idx]->Content .= $data;
				}
			}
			public function EndHandler($Parser, $name)
			{
				if(count($this->Output->ChildNodes) > 1)
				{
					$_data = array_pop($this->Output->ChildNodes);
					$_output_idx = count($this->Output->ChildNodes) - 1;
					$this->Output->ChildNodes[$_output_idx]->AddNode($_data);
				}
			}
		}
		
		class ExpatXmlElement
		{
			public $Name = "" ;
			public $ElementName = "" ;
			public $ElementType = "" ;
			public $ElementValue = "" ;
			public $Elements = array() ;
			public $RegisterElements = 1 ;
			public function AddElement($type, $name, $value)
			{
				if(! $this->RegisterElements)
					return ;
				$element = new ExpatXmlElement() ;
				$element->ElementType = $type ;
				$element->ElementName = $name ;
				$element->ElementValue = $value ;
				$this->Elements[] = $element ;
			}
			public function GetElementsByName($name)
			{
				if(! $this->RegisterElements)
					return ;
				$elements = array() ;
				for($i=0; $i<count($this->Elements); $i++)
				{
					if($this->Elements[$i]->ElementName == $name)
					{
						$elements[] = $this->Elements[$i] ;
					}
				}
				return $elements ;
			}
			public function GetElementsByType($type)
			{
				if(! $this->RegisterElements)
					return ;
				$elements = array() ;
				for($i=0; $i<count($this->Elements); $i++)
				{
					if($this->Elements[$i]->ElementType == $type)
					{
						$elements[] = $this->Elements[$i] ;
					}
				}
				return $elements ;
			}
			public function GetElementsByValue($value)
			{
				if(! $this->RegisterElements)
					return ;
				$elements = array() ;
				for($i=0; $i<count($this->Elements); $i++)
				{
					if($this->Elements[$i]->ElementValue == $value)
					{
						$elements[] = $this->Elements[$i] ;
					}
				}
				return $elements ;
			}
		}
		class ExpatXmlNode extends ExpatXmlElement
		{
			public $RegisterChildNodes = 1 ;
			public $RegisterAttributes = 1 ;
			public $RegisterContents = 1 ;
			public $ElementID = "" ;
			public $Attributes = array() ;
			public $ChildNodes = array() ;
			public $Content = "" ;
			public function AddContent($content)
			{
				if($this->RegisterContents)
					$this->Content .= $content ;
				$this->AddElement('content', 'content', $content) ;
			}
			public function AddNode($node)
			{
				if($this->RegisterChildNodes)
					$this->ChildNodes[] = $node ;
				$this->AddElement('node', $node->Name, $node) ;
			}
			public function AddChildNode($node)
			{
				$this->AddNode($node) ;
			}
			public function AddAttributes($attributes=array())
			{
				if($this->RegisterAttributes)
					$this->Attributes = array_merge($this->Attributes, $attributes) ;
				foreach($attributes as $name => $value)
				{
					$this->AddElement('attribute', $name, $value) ;
				}
			}
			public function GetElementById($id)
			{
				$node = false ;
				for($i=0; $i<count($this->ChildNodes); $i++)
				{
					if(isset($this->ChildNodes[$i]->Attributes["ID"]) && $this->ChildNodes[$i]->Attributes["ID"] == $id)
					{
						$node = $this->ChildNodes[$i] ;
					}
					if($node == false)
					{
						$node = $this->ChildNodes[$i]->GetElementById($id) ;
					}
					if($node != false)
					{
						break ;
					}
				}
				return $node ;
			}
			public function ChildNodeCount()
			{
				return count($this->ChildNodes) ;
			}
			public function GetChildNodesByName($name)
			{
			}
		}
		
		class ExpatXmlDocument extends ExpatXmlNode
		{
			public $Path = "" ;
			public function RootNode()
			{
				if(count($this->ChildNodes) < 1)
					return false ;
				return $this->ChildNodes[0] ;
			}
			public function CreateNode()
			{
				return new ExpatXmlNode() ;
			}
		}
	}
	
?>