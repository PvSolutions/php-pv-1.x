<?php
	
	if(! defined('COMMON_GD_CONTROLS_INCLUDED'))
	{
		define('COMMON_GD_CONTROLS_INCLUDED', 1) ;
	
		class CommonGDControl
		{
			public $Name ;
			public function CommonGDControl($Name="undef")
			{
				$this->Name = $Name ;
				$this->Init() ;
			}
			public function Init()
			{
			}
		}
		
		class CommonGDManipulator extends CommonGDControl
		{
			function Init()
			{
				parent::Init() ;
			}
			function getSub($Format)
			{
				global $GDFormats ;
				$Sub = '' ;
				$Format = strtolower($Format) ;
				if(isset($GDFormats[$Format]))
					$Sub = $GDFormats[$Format] ;
				return $Sub ;
			}
			function HasImageFormat($FilePath)
			{
				return ($this->getFileFormat($FilePath) != '') ;
			}
			function getFileExt($FilePath)
			{
				$FileExt = "" ;
				if(preg_match('/\.([a-zA-Z0-9]+)$/', $FilePath, $Ext))
				{
					$FileExt = $Ext[1] ;
				}
				return $FileExt ;
			}
			function getFileName($FilePath)
			{
				return basename($FilePath) ;
			}
			function getFileFormat($FilePath)
			{
				$FileExt = $this->getFileExt($FilePath) ;
				$Format = $this->getSub($FileExt) ;
				return $Format ;
			}
			function getDimensions($Handle)
			{
				return array(imagesx($Handle), imagesy($Handle)) ;
			}
			function getDimensionsFromFile($FilePath)
			{
				/*
				$Handle = $this->LoadHandleFromFile($FilePath) ;
				if(! $Handle)
				{
					return array(0, 0) ;
				}
				$Dimensions = $this->getDimensions($Handle) ;
				imagedestroy($Handle) ;
				*/
				if(! file_exists($FilePath))
				{
					return array(0, 0) ;
				}
				if($this->getFileFormat($FilePath) == '')
				{
					return array(0, 0) ;
				}
				// echo $FilePath.'<br />' ;
				$Dimensions = @getimagesize($FilePath) ;
				return $Dimensions ;
			}
			function getWidthFromFile($FilePath)
			{
				$Dims = $this->getDimensionsFromFile($FilePath) ;
				return $Dims[0] ;
			}
			function getHeightFromFile($FilePath)
			{
				$Dims = $this->getDimensionsFromFile($FilePath) ;
				return $Dims[1] ;
			}
			function getSizeFromFile($FilePath)
			{
				$Dims = $this->getDimensionsFromFile($FilePath) ;
				return $Dims[1]*$Dims[0] ;
			}
			function CallLoadFileSub($FilePath)
			{
				$Sub = "imagecreatefrom" ;
				$Format = $this->getFileFormat($FilePath) ;
				$Result = NULL ;
				if(! file_exists($FilePath))
				{
					return $Result ;
				}
				if(function_exists($Sub.$Format))
				{
					$Result = call_user_func($Sub.$Format, $FilePath) ;
				}
				return $Result ;
			}
			function CallSaveFileSub($Handle, $FilePath)
			{
				$Sub = "image" ;
				$Format = $this->getFileFormat($FilePath) ;
				$Result = NULL ;
				if(function_exists($Sub.$Format))
				{
					$Result = call_user_func($Sub.$Format, $Handle, $FilePath) ;
				}
				return $Result ;
			}
			function CallOutputSub($Handle, $Format)
			{
				$Sub = "image" ;
				$Result = NULL ;
				if(function_exists($Sub.$Format))
				{
					$Result = call_user_func($Sub.$Format, $Handle) ;
				}
				return $Result ;
			}
			function LoadHandleFromFile($FilePath)
			{
				return $this->CallLoadFileSub($FilePath) ;
			}
			function UnloadHandle($Handle)
			{
				if($Handle)
				{
					if(is_resource($Handle))
					{
						imagedestroy($Handle) ;
					}
				}
			}
			function SaveHandleToFile($Handle, $FilePath)
			{
				return $this->CallSaveFileSub($Handle, $FilePath) ;
			}
			function Rescale($Handle, $Scale, $BgColor=array(255, 255, 255))
			{
				list($Width, $Height) = $this->getDimensions($Handle) ;
				$NewWidth = $Width * $Scale ; $NewHeight = $Height * $Scale ;
				return $this->Resize($Handle, $NewWidth, $NewHeight, $BgColor) ;
			}
			function Resize($Handle, $Width, $Height, $BgColor=array(255, 255, 255))
			{
				list($OldWidth, $OldHeight) = $this->getDimensions($Handle) ;
				if($Width == 0)
				{
					$Width = $OldWidth ;
				}
				if($Height == 0)
				{
					$Height = $OldHeight ;
				}
				$NewHandle = imagecreatetruecolor($Width, $Height) ;
				$BgColor_h = imagecolorallocate($NewHandle, $BgColor[0], $BgColor[1], $BgColor[2]) ;
				imagefilledrectangle($NewHandle, 0, 0, $Width, $Height, $BgColor_h) ;
				imagecopyresampled($NewHandle, $Handle, 0, 0, 0, 0, $Width, $Height, $OldWidth, $OldHeight) ;
				return $NewHandle ;
			}
			function Crop($Handle, $Left=0, $Top=0, $Width=0, $Height=0, $BgColor=array(255, 255, 255))
			{
				list($OldWidth, $OldHeight) = $this->getDimensions($Handle) ;
				if($Width == 0)
				{
					$Width = $OldWidth ;
				}
				if($Height == 0)
				{
					$Height = $OldHeight ;
				}
				list($NewWidth, $NewHeight) = $this->getAdjustedDimensions($Handle, $Width, $Height) ;
				$NewHandle = imagecreatetruecolor($Width, $Height) ;
				$BgColor_h = imagecolorallocate($NewHandle, $BgColor[0], $BgColor[1], $BgColor[2]) ;
				imagefilledrectangle($NewHandle, 0, 0, $Width, $Height, $BgColor_h) ;
				imagecopyresampled($NewHandle, $Handle, $Left, $Top, 0, 0, $NewWidth, $NewHeight, $OldWidth, $OldHeight) ;
				return $NewHandle ;
			}
			function CopyFile($FilePathSource, $FilePathDest)
			{
				$Handle = $this->LoadHandleFromFile($FilePathSource) ;
				if(!$Handle)
					return ;
				$this->SaveHandleToFile($Handle, $FilePathDest) ;
				imagedestroy($Handle) ;
			}
			function CopyRescaledFile($FilePathSource, $FilePathDest, $Scale)
			{
				$Handle = $this->LoadHandleFromFile($FilePathSource) ;
				if(!$Handle)
					return ;
				$NewHandle = $this->Rescale($Handle, $Scale) ;
				$this->SaveHandleToFile($NewHandle, $FilePathDest) ;
				imagedestroy($Handle) ;
				imagedestroy($NewHandle) ;
			}
			function CopyResizedFile($FilePathSource, $FilePathDest, $Width, $Height)
			{
				$Handle = $this->LoadHandleFromFile($FilePathSource) ;
				if(!$Handle)
					return ;
				$NewHandle = $this->Resize($Handle, $Width, $Height) ;
				$this->SaveHandleToFile($NewHandle, $FilePathDest) ;
				imagedestroy($Handle) ;
				imagedestroy($NewHandle) ;
			}
			function CopyAdjustedFile($FilePathSource, $FilePathDest, $Width, $Height)
			{
				$Handle = $this->LoadHandleFromFile($FilePathSource) ;
				if(! $Handle)
					return ;
				list($NewWidth, $NewHeight) = $this->getAdjustedDimensions($Handle, $Width, $Height) ;
				$NewHandle = $this->Resize($Handle, $NewWidth, $NewHeight) ;
				$this->SaveHandleToFile($NewHandle, $FilePathDest) ;
				imagedestroy($Handle) ;
				imagedestroy($NewHandle) ;
			}
			function CopyWrappedFile($FilePathSource, $FilePathDest, $Width, $Height, $BgColor=array(255, 255, 255))
			{
				$Handle = $this->LoadHandleFromFile($FilePathSource) ;
				if(! $Handle)
					return ;
				list($NewWidth, $NewHeight) = $this->getAdjustedDimensions($Handle, $Width, $Height) ;
				$Left = 0 ;
				$Top = 0 ;
				if($NewWidth < $Width)
				{
					$Left = bcdiv($Width - $NewWidth, 2) ;
				}
				if($NewHeight < $Height)
				{
					$Top = bcdiv($Height - $NewHeight, 2) ;
				}
				$NewHandle = $this->Crop($Handle, $Left, $Top, $Width, $Height, $BgColor) ;
				$this->SaveHandleToFile($NewHandle, $FilePathDest) ;
				imagedestroy($Handle) ;
				imagedestroy($NewHandle) ;
			}
			function getAdjustedDimensions($Handle, $Width, $Height)
			{
				list($OldWidth, $OldHeight) = $this->getDimensions($Handle) ;
				if($OldWidth == 0 or $OldHeight == 0)
				{
					return array(0, 0) ;
				}
				if($Width == 0)
				{
					$Width = $OldWidth ;
				}
				if($Height == 0)
				{
					$Height = $OldHeight ;
				}
				$ScaleWidth = $OldWidth / $Width ;
				$ScaleHeight = $OldHeight / $Height ;
				$NewWidth = $Width ;
				$NewHeight = $Height ;
				if($OldHeight / $ScaleWidth <= $Height)
				{
					$NewHeight = $OldHeight / $ScaleWidth ;
					$NewWidth = $OldWidth / $ScaleWidth ;
				}
				elseif($OldWidth / $ScaleHeight <= $Width)
				{
					$NewWidth = $OldWidth / $ScaleHeight ;
					$NewHeight = $OldHeight / $ScaleHeight ;
				}
				return array($NewWidth, $NewHeight) ;
			}
		}
		
		class CommonGDColor
		{
			public $R ;
			public $G ;
			public $B ;
			function CommonGDColor($R=0, $G=0, $B=0)
			{
				$this->R = $R ;
				$this->G = $G ;
				$this->B = $B ;
			}
			function FromRVBHex($RVB)
			{
				$this->R = bcmod($RVB, 256) ;
				$this->G = bcmod($RVB, 256*256) ;
				$this->B = bcmod($RVB, 256*256*256) ;
			}
		}
		
		class CommonGDBackground extends CommonGDControl
		{
			public $Color ;
			public $_ColorHandle ;
			public $_Parent ;
			function & Create(& $Parent)
			{
				$bg = new CommonGDBackground(uniqid()) ;
				$bg->_Parent = & $Parent ;
				return $bg ;
			}
			function Init()
			{
				$this->Color = new CommonGDColor(255, 255, 255) ;
			}
			function Draw()
			{
				$this->_ColorHandle = imagecolorallocate($this->_Parent->_Handle, $this->Color->R, $this->Color->G, $this->Color->B) ;
				imagefilledrectangle($this->_Parent->_Handle, 0, 0, $this->_Parent->_Width, $this->_Parent->_Height, $this->_ColorHandle) ;
			}
		}
	
		class CommonGDImage extends CommonGDControl
		{
			public $_Handle ;
			public $_Width ;
			public $_Height ;
			public $_Left ;
			public $_Top ;
			public $Background ;
			public $_FilePath ;
			public static function & Create($Width=0, $Height=0)
			{
				$img = new CommonGDImage(uniqid()) ;
				$img->_Width = $Width ;
				$img->_Height = $Height ;
				$img->_FilePath = '' ;
				return $img ;
			}
			public function Init()
			{
				parent::Init() ;
				$this->_Top = 0 ;
				$this->_Left = 0 ;
			}
			public function Open()
			{
				$this->Background = CommonGDBackground::Create($this) ;
				$this->OpenHandle() ;
			}
			public function OpenHandle()
			{
				$this->_Handle = imagecreatetruecolor($this->_Width, $this->_Height) ;
			}
			public function OpenFromFile($FilePath)
			{
				$this->Background = new CommonGDBackground($this) ;
				$this->OpenHandleFromFile($FilePath) ;
			}
			public function OpenHandleFromFile($FilePath)
			{
				global $CommonGDManipulator ;
				$this->_FilePath = $FilePath ;
				$this->_Handle = $CommonGDManipulator->LoadHandleFromFile($this->_FilePath) ;
				$this->_Width = 0 ;
				$this->_Height = 0 ;
				$this->SetPropertiesFromHandle() ;
			}
			public function SetPropertiesFromHandle()
			{
				if($this->_Handle)
				{
					$Dims = $GLOBALS['CommonGDManipulator']->getDimensionsFromFile($this->_FilePath) ;
					$this->_Width = $Dims[0] ;
					$this->_Height = $Dims[1] ;
				}
			}
			public function SaveToFile($FilePath = '')
			{
				if($FilePath == '')
				{
					$FilePath = $this->_FilePath ;
				}
				$GLOBALS['CommonGDManipulator']->SaveHandleToFile($this->_Handle, $FilePath) ;
			}
			public function CloseHandle()
			{
				if(! $this->_Handle)
				{
					return ;
				}
				imagedestroy($this->_Handle) ;
			}
			public function Close()
			{
				$this->CloseHandle() ;
			}
			public function Draw()
			{
				$this->DrawBackground() ;
			}
			public function DrawBackground()
			{
				if(! $this->Background)
				{
					return ;
				}
				$this->Background->Draw() ;
			}
			public function Show($format="jpeg")
			{
				if($format == "jpg" or ! in_array($format, array('gif', 'png', 'jpeg', 'jpg', 'ico')))
				{
					$format = "jpeg" ;
				}
				header("Content-type:image/$format") ;
				$GLOBALS['CommonGDManipulator']->CallOutputSub($this->_Handle, $format) ;
			}
			public function getWidth()
			{
				return $this->_Width ;
			}
			public function getHeight()
			{
				return $this->_Height ;
			}
			public function Rescale($Scale)
			{
				if(! $this->_Handle)
				{
					return ;
				}
				$this->_Handle = $GLOBALS['CommonGDManipulator']->Rescale($this->_Handle, $Scale) ;
				$this->SetPropertiesFromHandle() ;
			}
			public function Resize($Width, $Height)
			{
				if(! $this->_Handle)
				{
					return ;
				}
				$this->_Handle = $GLOBALS['CommonGDManipulator']->Resize($this->_Handle, $Width, $Height) ;
				$this->SetPropertiesFromHandle() ;
			}
			public function Adjust($Width, $Height)
			{
				list($NewWidth, $NewHeight) = $GLOBALS["CommonGDManipulator"]->getAdjustedDimensions($this->_Handle, $Width, $Height) ;
				if(! $NewWidth or ! $NewHeight)
				{
					return ;
				}
				$this->_Handle = $GLOBALS["CommonGDManipulator"]->Resize($this->_Handle, $NewWidth, $NewHeight) ;
				$this->SetPropertiesFromHandle() ;
			}
			public function Wrap($Width, $Height, $BgColor=array(255, 255, 255))
			{
				if(! $this->_Handle)
					return ;
				list($NewWidth, $NewHeight) = $GLOBALS["CommonGDManipulator"]->getAdjustedDimensions($this->_Handle, $Width, $Height) ;
				$Left = 0 ;
				$Top = 0 ;
				if($NewWidth < $Width)
				{
					$Left = bcdiv($Width - $NewWidth, 2) ;
				}
				if($NewHeight < $Height)
				{
					$Top = bcdiv($Height - $NewHeight, 2) ;
				}
				$this->_Handle = $GLOBALS["CommonGDManipulator"]->Crop($this->_Handle, $Left, $Top, $Width, $Height, $BgColor) ;
				$this->SetPropertiesFromHandle() ;
			}
			public function TextOut($string, $x=0, $y=0, $BgColor=array(0, 0, 0), $font_index=8)
			{
				$color = imagecolorallocate($this->_Handle, $BgColor[0], $BgColor[1], $BgColor[2]) ;
				imagestring($this->_Handle, $font_index, $x, $y, $string, $color);
			}
			public function PasteImageFromPath($image_path, $x=0, $y=0)
			{
				global $CommonGDManipulator ;
				$Handle = $CommonGDManipulator->LoadHandleFromFile($image_path) ;
				if(! $Handle)
				{
					return ;
				}
				list($w, $h) = $CommonGDManipulator->getDimensions($Handle) ;
				imagecopymerge($this->_Handle, $Handle, $x, $y, 0, 0, $w, $h, 100) ;
				imagedestroy($Handle) ;
			}
			public function ApplyPatternFromPath($image_path)
			{
				global $CommonGDManipulator ;
				$Handle = $CommonGDManipulator->LoadHandleFromFile($image_path) ;
				if(! $Handle)
				{
					return ;
				}
				list($w, $h) = $CommonGDManipulator->getDimensions($Handle) ;
				for($i=0; $i < $this->getWidth(); $i += $w)
				{
					for($j=0; $j < $this->getHeight(); $j += $h)
					{
						$x = $i ;
						$y = $j ;
						imagecopymerge($this->_Handle, $Handle, $x, $y, 0, 0, $w, $h, 100) ;
					}
				}
				imagedestroy($Handle) ;
			}
			public function TextOutTTF($string, $x=0, $y=0, $size=18, $angle=0, $BgColor=array(0, 0, 0), $font_name="arial")
			{
				$color = imagecolorallocate($this->_Handle, $BgColor[0], $BgColor[1], $BgColor[2]) ;
				$font_path = $font_name.'.ttf' ;
				$font_file_name = $font_path ;
				if(file_exists($font_path))
				{
					putenv('GDFONTPATH=' . realpath('.'));
					$font_file_name = $font_name ;
				}
				elseif(is_dir($win_font_path = 'C:/WINDOWS/Fonts'))
				{
					$font_file_name = $win_font_path.'/'.$font_path ;
				}
				imagettftext($this->_Handle, $x, $y, $size, $angle, $color, $font_file_name, $string);
				// exit ;
			}
			public function setLeft($Left)
			{
				$this->_Left = $Left ;
			}
			public function setTop($Top)
			{
				$this->_Top = $Top ;
			}
			public function getLeft()
			{
				return $this->_Left ;
			}
			public function getTop()
			{
				return $this->_Top ;
			}
		}
		
		class CommonGDLayer extends CommonGDImage
		{
			public static function & Create(& $Parent)
			{
				$layer = new CommonGDLayer(uniqid()) ;
				$layer->_Parent = & $Parent ;
				return $layer ;
			}
			public function getParentWidth()
			{
				return $this->_Parent->getWidth() ;
			}
			public function getParentHeight()
			{
				return $this->_Parent->getHeight() ;
			}
		}
		
		class CommonGDCaptcha extends CommonGDLayer
		{
			public $CharCount = 6 ;
			public $Codes = "ressERDFtgt673TGgj5huyTHNJkMASs51uqz8921Oo5b6132kMPnVcxWw" ;
			protected $_Text ;
			public $MinFontSize = 24 ;
			public $MaxFontSize = 30 ;
			static $InstanceCount = 0 ;
			static $CaseInsensitive = 1 ;
			public $SessionName = "CaptchaText" ;
			public function Init()
			{
				CommonGDCaptcha::$InstanceCount++ ;
				parent::Init() ;
			}
			public static function & Create($Width=0, $Height=0)
			{
				$name = "CommonGDCaptcha_".CommonGDCaptcha::$InstanceCount ;
				$img = new CommonGDCaptcha($name) ;
				$img->_Width = $Width ;
				$img->_Height = $Height ;
				return $img ;
			}
			public function DrawSubmittedText()
			{
				$textColor = imagecolorallocate($this->_Handle, 0, 0, 255);
				$charLeft = 10 ;
				$charWidth = 15 ;
				$charTop = 8 ;
				$this->_Text = "" ;
				for($i=0; $i<$this->CharCount; $i++)
				{
					$charFont = rand($this->MinFontSize, $this->MaxFontSize) ;
					$charIndex = rand(0, strlen($this->Codes) - 1) ;
					$this->_Text .= $this->Codes[$charIndex] ;
					imagestring($this->_Handle, $charFont, $charLeft, $charTop, $this->Codes[$charIndex], $textColor) ;
					$charLeft += $charWidth ;
				}
				$this->Store() ;
			}
			public function Store()
			{
				$_SESSION[$this->Name.$this->SessionName] = $this->_Text ;
			}
			public function Draw()
			{
				$this->DrawBackground() ;
				$this->DrawSubmittedText() ;
				$this->Store() ;
			}
			public function ConfirmSubmittedText($text)
			{
				if(! isset($_SESSION[$this->Name.$this->SessionName]))
					return 0 ;
				$ok = 0 ;
				if($this->CaseInsensitive)
				{
					$ok = (strtolower($_SESSION[$this->Name.$this->SessionName]) == strtolower($text)) ;
				}
				else
				{
					$ok = ($_SESSION[$this->Name.$this->SessionName] == $text) ;
				}
				$this->ClearSubmittedText() ;
				return $ok ;
			}
			public function ClearSubmittedText()
			{
				unset($_SESSION[$this->Name.$this->SessionName]) ;
			}
		}
		
		$GLOBALS['CommonGDManipulator'] = new CommonGDManipulator("CommonGDManipulator") ;
	
	}
	
?>