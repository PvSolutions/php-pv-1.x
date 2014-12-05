<?php

	if(! defined('PERS_ZIP'))
	{
		if(! class_exists('Zip'))
		{
			include dirname(__FILE__)."/Zip.php" ;
		}
		define('PERS_ZIP', 1) ;		
		
		class PersZip extends Zip
		{
			protected $dirHandle ;
			public function releaseDirHandle()
			{
				if(is_resource($this->dirHandle))
				{
					closedir($this->dirHandle) ;
				}
			}
			public function insertFile($filePath, $zipFilePath='')
			{
				if($zipFilePath == '')
					$zipFilePath = str_replace(array('../', '..\\', './', '.\\'), '', $filePath);
				return $this->addFile(file_get_contents($filePath), $zipFilePath, filectime($filePath)) ;
			}
			public function insertDir($dirpath, $allowedExts=array())
			{
				$allowedExts = array_map('strtolower', $allowedExts) ;
				if ($this->dirHandle = @opendir($dirpath))
				{
					while (false !== ($fileName = readdir($this->dirHandle))) {
						$filePath = $dirpath.PATH_SEPARATOR.$fileName ;
						if ($fileName != '.' &&  $fileName != '..' && ! is_dir($filePath)) {
							$pathData = pathinfo($filePath);
							if(in_array(strtolower($pathData['extension']), $allowedExts))
							{
								$this->insertFile($filePath) ;
							}
						}
					}
					$this->releaseDirHandle() ;
				}
			}
		}
	}

?>