<?php

	if(! defined('XML_UTILS_INCLUDED'))
	{
		define('XML_UTILS_INCLUDED', 1) ;

/**
 * Project:     XMLParser: A library for parsing XML feeds
 * File:        XMLParser.class.php
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @link http://www.phpinsider.com/php/code/XMLParser/
 * @copyright 2004-2005 New Digital Group, Inc.
 * @author Monte Ohrt <monte at newdigitalgroup dot com>
 * @package XMLParser
 * @version 1.0-dev
 */
		
		if(! class_exists('XMLParser'))
		{
			class XMLParser
			{
			   /**
				* holds the expat object
				*
				* @var obj
				*/
			   var $xml_obj = null;
			   /**
				* holds the output array
				*
				* @var array
				*/
			   var $output = array();
			   /**
				* the XML file character set
				*
				* @var array
				*/
			   var $char_set = 'ISO-8859-1';
			   public $EncodeUtf8 = 0 ;
			   var $parse_error_string = "" ;
			   var $check_file_path = true ;
			   public $CaseInsensitive = 1 ;
			   public $SkipWhiteSpaces = 1 ;
			   public $AttrsCaseSensitive = 0 ;
			   public $NameCaseSensitive = 1 ;
				/**#@-*/
				/**
				 * The class constructor.
				 */
			   function XMLParser(){ }
				/**
				 * parse the XML file (or URL)
				 *
				 * @param string $path the XML file path, or URL
				 */
				function init_output()
				{
					$this->output = array();
					$this->parse_error_string = "" ;
					$this->init_parser() ;
				}
				function init_parser()
				{
				   $this->xml_obj = xml_parser_create($this->char_set);
				   xml_parser_set_option($this->xml_obj, XML_OPTION_SKIP_WHITE, $this->SkipWhiteSpaces) ;
				   xml_parser_set_option($this->xml_obj, XML_OPTION_CASE_FOLDING, $this->CaseInsensitive) ;
				   xml_parser_set_option($this->xml_obj, XML_OPTION_TARGET_ENCODING, $this->char_set) ;
				   xml_set_object($this->xml_obj, $this);
				   xml_set_character_data_handler($this->xml_obj, 'dataHandler');   
				   xml_set_element_handler($this->xml_obj, "startHandler", "endHandler");
				}
			   function parse($path)
			   {
					$this->init_output() ;
					try
					{
						if($this->check_file_path && ! file_exists($path)) {
							$this->parse_error_string = "Le fichier $path n'existe pas" ;
							return false ;
						}
						if (!($fp = fopen($path, "r"))) {
							$this->parse_error_string = "Cannot open XML data file: $path" ;
							return false;
						}
						while (($data = fread($fp, 4096)) != false) {	
							if($this->EncodeUtf8)
								$data = utf8_encode($data) ;
							$this->parse_data($data, feof($fp)) ;
						}
					}
					catch(Exception $ex)
					{
						$this->parse_error_string = $ex->getMessage() ;
					}
					return $this->output;
			   }
			   function & parse_error()
			   {
					return $this->parse_error_string ;
			   }
			   function parse_data($data, $eof=true)
			   {
					// echo $data ;
					if(! $this->xml_obj)
					{
						$this->init_parser() ;
					}
					if(! xml_parse($this->xml_obj, $data, $eof)) {
						$this->parse_error_string = sprintf("XML error: %s at line %d",
							xml_error_string(xml_get_error_code($this->xml_obj)),
							xml_get_current_line_number($this->xml_obj)
						) ;
						xml_parser_free($this->xml_obj);
						$this->xml_obj = false ;
					}
			   }
			   function parse_ctn($ctn)
			   {
					$this->init_output() ;
					$this->parse_data($ctn, true) ;
				   return $this->output;
			   }
				function DecodeAttrs($attrString, $encoding='ISO-8859-1')
				{
					$xmlData = '<?xml version="1.0" encoding="'.$encoding.'"><element '.$attrString.' />' ;
					$node = $this->parse_ctn($xmlData) ;
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
				/**
				 * define the start tag handler
				 *
				 * @param obj $parser the expat parser object
				 * @param string $name the XML tag name
				 * @param array $attribs the XML tag attributes
				 */
				function startHandler($parser, $name, $attribs){
				   $_content = array('name' => $name);
				   $_content['attrs'] = array() ;
				   foreach($attribs as $n => $v)
				   {
						$key = ($this->AttrsCaseSensitive) ? $n : strtoupper($n) ;
						$_content['attrs'][$key] = $v ;
				   }
				   array_push($this->output, $_content);
				}
				/**
				 * define the tag data handler
				 *
				 * @param obj $parser the expat parser object
				 * @param string $data the XML data
				 */
				function dataHandler($parser, $data){
				   if(! empty($data) || $data === 0 || $data === "") {
					   $_output_idx = count($this->output) - 1;
					   if(!isset($this->output[$_output_idx]['content']))
						 $this->output[$_output_idx]['content'] = trim($data);             
					   else
						 $this->output[$_output_idx]['content'] .= $data;
				   }
				}
				/**
				 * define the end tag handler
				 *
				 * @param obj $parser the expat parser object
				 * @param string $name the XML tag name
				 */
				function endHandler($parser, $name){
				   if(count($this->output) > 1)
				   {
					   $_data = array_pop($this->output);
					   $_output_idx = count($this->output) - 1;
					   $this->output[$_output_idx]['child'][] = $_data;
				   }
				}
			}
		}
		
		if(! function_exists('array_to_xml'))
		{
			function array_to_xml($data, $rootNodeName = 'data')
			{
			$ctn = '' ;
			// turn off compatibility mode as simple xml throws a wobbly if you don't.
			if (ini_get('zend.ze1_compatibility_mode') == 1)
			{
				ini_set ('zend.ze1_compatibility_mode', 0);
			}
			if($rootNodeName != '')
			{
				$ctn .= '<?xml version="1.0"?>' ;
				$ctn .= '<'.$rootNodeName.'>' ;
			}
			if(is_array($data))
			{
				// loop through the data passed in.
				foreach($data as $key => $value)
				{
					// no numeric keys in our xml please!
					if (is_numeric($key))
					{
						// make string key...
						$key = "unknownNode_". (string) $key;
					}
				 
					// replace anything not alpha numeric
					$key = preg_replace('/[^a-z]/i', '', $key);
					 
					// if there is another array found recrusively call this function
					if (is_array($value))
					{
						// recrusive call.
						$ctn .= array_to_xml($value, '');
					}
					else
					{
						// add single node.
						$value = htmlentities($value);
						$ctn .= '<'.$key.'>'.$value.'</'.$key.'>' ;
					}
				}
			}
			if($rootNodeName != '')
			{
				$ctn .= '</'.$rootNodeName.'>' ;
			}
			// pass back as string. or simple xml object if you want!
			return $ctn ;
		}
		}
		
		// Racourcis pour parser
		// Parser du contenu XML
		function xmldata_parse_content($ctn)
		{
			$xml = new XMLParser() ;
			$result = $xml->parse_ctn($ctn) ;
			return $result ;
		}
		// Parser un fichier XML
		function xmldata_parse_file($filepath)
		{
			$xml = new XMLParser() ;
			$result = $xml->parse($filepath) ;
			return $result ;
		}
		function xmldata_parse($filepath)
		{
			return xmldata_parse_file($filepath) ;
		}
		
		// Localisation de noeuds XML
		if(! function_exists('xmldata_get_node_by_name'))
		{
			function & xmldata_get_node_by_name(& $node, $node_name="")
			{
				if($node_name == "")
				{
					return null ;
				}
				$node_name = strtoupper($node_name) ;
				$current_nodes = array() ;
				if(isset($node["child"]))
				{
					foreach($node["child"] as $i => $node_temp)
					{
						if($node_temp["name"] == $node_name)
						{
							$current_nodes[] = $node_temp ;
						}
					}
				}
				return $current_nodes ;
			}
			function & xmldata_get_node_by_attr(& $node, $attrs=array())
			{
				if(empty($attrs) || empty($node))
				{
					$res = null ;
					return $res ;
				}
				$current_nodes = array() ;
				if(isset($node["child"]))
				{
					foreach($node["child"] as $i => $node_temp)
					{
						if(isset($node_temp["attrs"]))
						{
							$match = 1 ;
							foreach($attrs as $n => $v)
							{
								$n = strtoupper($n) ;
								if(! isset($node_temp["attrs"][$n]))
								{
									$match = 0 ;
									break ;
								}
								if($node_temp["attrs"][$n] != $v)
								{
									$match = 0 ;
									break ;
								}
							}
							if($match)
							{
								$current_nodes[] = $node_temp ;
							}
						}
					}
				}
				// print_r($current_nodes) ;
				return $current_nodes ;
			}
			function xmldata_get_node_by_attr_name(& $node, $attr_name="")
			{
				return xmldata_get_node_by_attr($node, array('NAME' => $attr_name)) ;
			}
			function xmldata_get_first_node_by_attr_name(& $node, $attr_name="")
			{
				return xmldata_get_first_node_by_attr($node, array('NAME' => $attr_name)) ;
			}
			function & xmldata_get_first_node_by_attr(& $node, $attr=array())
			{
				$current_nodes = xmldata_get_node_by_attr($node, $attr) ;
				$current_node = null ;
				if($current_nodes)
				{
					if(count($current_nodes))
					{
						$current_node = $current_nodes[0] ;
					}
				}
				return $current_node ;
			}
			function & xmldata_get_first_node_by_name(& $node, $node_name="")
			{
				$current_nodes = xmldata_get_node_by_name($node, $node_name) ;
				$current_node = null ;
				if($current_nodes)
				{
					if(count($current_nodes))
					{
						$current_node = $current_nodes[0] ;
					}
				}
				return $current_node ;
			}
			function & xmldata_reach_first_node_by_name(& $node, $node_path=array())
			{
				$node_temp = $node ;
				$result = null ;
				for($i=0; $i<count($node_path); $i++)
				{
					$node_temp = xmldata_get_first_node_by_name($node_temp, $node_path[$i]) ;
					//print $node_temp["name"]."<br>" ;
					if($node_temp === null)
					{
						break ;
					}
					else
					{
						if($i == count($node_path) - 1)
						{
							$result = $node_temp ;
						}
					}
				}
				return $result ;
			}
			
			// Convertion de noeuds XML en tableau
			/****
			Conversion en Params Array :
			---------------------------------------
			<node>
				<param name="data1" value="val1"></param>
				<param name="data2" value="val2"></param>
			</node>
			***/
			function & xmldata_node_to_param_array($node)
			{
				if(empty($node))
				{
					$res = null ;
					return $res ;
				}
				$result = array() ;
				if(isset($node["child"]))
				{
					foreach($node["child"] as $i => $node_temp)
					{
						if(isset($node_temp["attrs"]))
						{
							if(isset($node_temp["attrs"]["NAME"]))
							{
								$value = (isset($node_temp["attrs"]["VALUE"])) ? $node_temp["attrs"]["VALUE"] : "" ;
								$result[$node_temp["attrs"]["NAME"]] = $value ;
							}
						}
					}
				}
				return $result ;
			}
			function & xmldata_node_to_defs($node, $child_node_name="")
			{
				if(empty($node))
				{
					return null ;
				}
				$result= array() ;
				if(isset($node["child"]))
				{
					foreach($node["child"] as $i => $node_temp)
					{
						if(isset($node_temp["attrs"]))
						{
							if(isset($node_temp["attrs"]["NAME"]))
							{
								if($child_node_name == "" || strtoupper($child_node_name) == strtoupper($node["name"]))
								{
									$result[strtoupper($node_temp["attrs"]["NAME"])] = $node_temp["attrs"] ;
								}
							}
						}
					}
				}
				return $result ;
			}
			function & xmldata_node_to_array($node)
			{
				if(empty($node))
				{
					$result = null ;
					return $result ;
				}
				$result = array() ;
				if(isset($node["child"]))
				{
					foreach($node["child"] as $i => $node_temp)
					{
						$content = (isset($node_temp["content"])) ? $node_temp["content"] : "" ;
						$result[$node_temp["name"]] = $content ;
					}
				}
				return $result ;
			}
			function & xmldata_node_to_list($node)
			{
				if(empty($node))
				{
					$result = null ;
					return $result ;
				}
				$result = array() ;
				if(isset($node["child"]))
				{
					foreach($node["child"] as $i => $node_temp)
					{
						$content = (isset($node_temp["content"])) ? $node_temp["content"] : "" ;
						$result[] = $content ;
					}
				}
				return $result ;
			}
			function & xmldata_node_to_array_of_attrs($node)
			{
				if(empty($node))
				{
					$result = null ;
					return $result ;
				}
				$result = array() ;
				if(isset($node["child"]))
				{
					foreach($node["child"] as $i => $node_temp)
					{
						$attrs = (isset($node_temp["attrs"])) ? $node_temp["attrs"] : "" ;
						$result[$node_temp["name"]] = $attrs ;
					}
				}
				return $result ;
			}
			function & xmldata_node_to_child_param_array($node)
			{
				if(empty($node))
				{
					$result = null ;
					return $result ;
				}
				$result = array() ;
				if(isset($node["child"]))
				{
					foreach($node["child"] as $i => $node_temp)
					{
						if(isset($node_temp["attrs"]))
						{
							if(isset($node_temp["attrs"]["NAME"]))
							{
								$result[$node_temp["attrs"]["NAME"]] = $node_temp["attrs"] ;
								$result[$node_temp["attrs"]["NODETYPE"]] = $node_temp["name"] ;
								$result[$node_temp["attrs"]["NAME"]]["child"] = (isset($node_temp["child"])) ? $node_temp["_child"] : array() ;
								$result[$node_temp["attrs"]["NAME"]]["content"] = (isset($node_temp["content"])) ? $node_temp["content"] : array() ;
							}
						}
					}
				}
				return $result ;
			}
			
			// Conversion et formattage XML <-> Chaine de caractère
			function xmldata_format_attr_value($value)
			{
				return htmlentities($value) ;
			}
			function xmldata_format_attr_name($name)
			{
				$name = preg_replace('/[^a-zA-Z0-9_\:\-]/', '', $name) ;
				return $name ;
			}
			function xmldata_format_content($ctn)
			{
				return '<![CDATA['.$node["content"].']]>' ;
			}
			function xmldata_node_to_string(& $node)
			{
				$ctn = '' ;
				if($node)
				{
					$node_name = (isset($node["name"])) ? $node["name"] : "UNKNOWN_NODE" ;
					$ctn .= '<'.$node_name ;
					if(isset($node["attrs"]))
					{
						foreach($node["attrs"] as $key => $val)
						{
							$ctn .= ' '.xmldata_format_attr_name($key).'="'.xmldata_format_attr_value($val).'"' ;
						}
					}
					$ctn .= '>' ;
					if(isset($node["child"]))
					{
						foreach($node["child"] as $i => $node_temp)
						{
							$ctn .= xmldata_node_to_string($node_temp) ;
						}
					}
					if(isset($node["content"]))
					{
						if($node["content"] != "")
						{
							$ctn .= xmldata_format_content($node["content"]) ;
						}
					}
					$ctn .= '</'.$node_name.'>' ;
				}
				return $ctn ;
			}
			function xmldata_param_array_to_string(& $data)
			{
			$ctn = '' ;
			if(is_array($data))
			{
				foreach($data as $key => $val)
				{
					$ctn .= '<param name="'.xmldata_format_attr_value($key).'" value="'.xmldata_format_attr_value($value).'" />'."\r\n" ;
				}
			}
			return $ctn ;
		}
		}
		
		if(! function_exists('xmlconfig_load_array'))
		{
			// Configuration
			function xmlconfig_load_array($file_path)
			{
				$CONFIG_NODE = xmldata_parse_file($file_path) ;
				$CONFIG_DATA = array() ;
				if(isset($CONFIG_NODE[0]))
				{
					$CONFIG_DATA = xmldata_node_to_array($CONFIG_NODE[0]) ;
				}
				else
				{
					$CONFIG_DATA = null ;
				}
				return $CONFIG_DATA ;
			}
			function xmlconfig_load_array_data($data)
			{
				$CONFIG_NODE = xmldata_parse($data) ;
				$CONFIG_DATA = array() ;
				if(isset($CONFIG_NODE[0]))
				{
					$CONFIG_DATA = xmldata_node_to_array($CONFIG_NODE[0]) ;
				}
				else
				{
					$CONFIG_DATA = null ;
				}
				return $CONFIG_DATA ;
			}
		}
	}
	
?>