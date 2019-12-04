<?php

	if(! defined('HTML_TAG_INC'))
	{
		if(! class_exists('HtmlParser'))
		{
			include dirname(__FILE__).'/htmlparser.inc.php' ;
		}
		define('HTML_TAG_INC', 1) ;
		
		if(! class_exists('HTMLTagParameters'))
		{
			class HTMLTagParameters
			{
				public static function UnsafeTags()
				{
				}
				public static function UnclosedTags()
				{
					return array("br", "meta", "nobr", "img") ;
				}
				public static function ExternTags()
				{
					return array('object', 'script', 'style') ;
				}
			}
		}
		if(! class_exists('HTMLTag'))
		{
			class HTMLTag
			{
				var $TagName ;
				var $PreserveSpaces = false ;
				var $SafeMode = 0 ;
				var $Name ;
				var $Tags ;
				var $AsHTML ;
				var $Content ;
				var $Preview ;
				var $Text ;
				var $Value ;
				var $Attributes ;
				var $OnLoadText ;
				var $OnLoadFile ;
				public static function ExtractPreview($text)
				{
					$tag = new HTMLTag() ;
					$tag->LoadFromText($text) ;
					return $tag->Preview ;
				}
				public static function ExtractSafeContent($text)
				{
					$tag = new HTMLTag() ;
					$tag->LoadFromText($text) ;
					$tag->SafeMode = 1 ;
					return $tag->GetContent(true) ;
				}
				function __construct()
				{
					$this->Init() ;
				}
				function Init()
				{
					$this->Tags = array() ;
					$this->Attributes = array() ;
				}
				function LoadFromText($Text)
				{
					$this->AsHTML = $Text ;
					$parser = new HtmlParser($this->AsHTML);
					$current_owner = & $this ;
					$current_index = 0 ;
					$this->Tags = array() ;
					$this->Attributes = array() ;
					while ($parser->parse())
					{
						$parser->iNodeName = strtolower($parser->iNodeName) ;
						switch ($parser->iNodeType)
						{
							case 1 :
							{
								$current_owner->Tags[] = new HTMLTag() ;
								$current_index = count($current_owner->Tags) - 1 ;
								$current_owner->Tags[$current_index]->Owner = & $current_owner ;
								$current_owner->Tags[$current_index]->TagName = $parser->iNodeName ;
								$current_owner->Tags[$current_index]->Value = $parser->iNodeValue ;
								$current_owner->Tags[$current_index]->Attributes = $parser->iNodeAttributes ;
								if(! in_array($parser->iNodeName, HTMLTagParameters::UnclosedTags()))
									$current_owner = & $current_owner->Tags[$current_index]  ;
							}
							break ;
							case 3 :
							{
								if(strpos(strtolower($parser->iNodeValue), "\!doctype") !== false)
									continue 2 ;
								if(strpos(strtolower($parser->iNodeValue), "html public") !== false)
									continue 2 ;
								$text = $parser->iNodeValue ;
								if(! $this->PreserveSpaces)
									$text = preg_replace("/[[:space:]]{2,}/", " ", $text) ;
								if($text != "")
								{
									$current_owner->Tags[] = new HTMLTag() ;
									$current_index = count($current_owner->Tags) - 1 ;
									$current_owner->Tags[$current_index]->Owner = & $current_owner ;
									$current_owner->Tags[$current_index]->TagName = "__Text" ;
									$current_owner->Tags[$current_index]->Value = $text ;
									$current_owner->Tags[$current_index]->Text = $text ;
								}
							}
							break ;
							case 2 :
							{
								$current_owner = & $current_owner->Owner ;
							}
							break ;
						}
					}
					unset ($parser) ;
				}
				function LoadFromFile($FileName)
				{
					if(! file_exists($FileName))
						return ;
					$this->LoadFromText(file_get_contents($FileName)) ;
				}
				function LoadFromURL($URL)
				{
					$this->LoadFromText(file_get_contents($URL)) ;
				}
				function Build()
				{
					$Text = "" ;
					$Preview= "" ;
					if($this->TagName != "__Text" && $this->TagName != "")
					{
						$Text .= "<".$this->TagName ;
					}
					foreach($this->Attributes as $AttrName => $AttrVal)
					{
						$skipAttr = 0 ;
						if($this->SafeMode == 1 && ($AttrName == "href" || $AttrName == "src"))
						{
							if(preg_match('/^javascript/i', $AttrVal))
							{
								$skipAttr = 1 ;
							}
							else
							{
								$urlParts = @parse_url($AttrVal) ;
								if($urlParts !== false && isset($_SERVER["SERVER_NAME"]) && $urlParts["host"] != $_SERVER["SERVER_NAME"])
								{
									$skipAttr = 1 ;
								}
							}
							if($skipAttr == 1)
							{
								continue ;
							}
						}
						$Text .= " ".$AttrName."='".str_replace("'", "&quot;", $AttrVal)."'" ;
					}
					if($this->TagName != "__Text" && $this->TagName != "")
					{
						$Text .= ">" ;
					}
					$Text .= $this->Text ;
					$Preview .= $this->Text ;
					foreach($this->Tags as $i => $Tag)
					{
						if($this->SafeMode == 1 && in_array($Tag->TagName, array('script', 'object', 'style')))
						{
							continue ;
						}
						$Tag->Build() ;
						$Text .= $Tag->Content ;
						if(! in_array($Tag->TagName, HTMLTagParameters::UnclosedTags()) && ! in_array($Tag->TagName, HTMLTagParameters::ExternTags()))
						{
							$Preview .= " ".$Tag->Preview ;
						}
					}
					if($this->TagName != "__Text" && ! in_array($this->TagName, HTMLTagParameters::UnclosedTags()) && $this->TagName != "")
					{
						$Text .= "</".$this->TagName.">" ;
					}
					$this->Content = $Text ;
					$Preview = $this->RepairPreview($Preview) ;
					$this->Preview = $Preview ;
				}
				function GetContent($ForceBuild = true)
				{
					if($ForceBuild)
						$this->Build() ;
					return $this->Content ;
				}
				function RepairPreview($Preview)
				{
					$Result = $Preview ;
					$Result = strip_tags($Preview) ;
					$Result = str_replace(">", ' ', $Result) ;
					$Result = str_replace("<", ' ', $Result) ;
					$Result = preg_replace('/^ /', '', $Result) ;
					$Result = preg_replace("/[[:space:]]{2,}/", ' ', $Result) ;
					return $Result ;
				}
			}
		}
	}

?>