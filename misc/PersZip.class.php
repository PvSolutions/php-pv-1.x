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
			public $isUTF8 = true ;
			public function releaseDirHandle()
			{
				if(is_resource($this->dirHandle))
				{
					closedir($this->dirHandle) ;
				}
			}
		   private function buildZipEntry($filePath, $fileComment, $gpFlags, $gzType, $timestamp, $fileCRC32, $gzLength, $dataLength, $extFileAttr) {
				$filePath = str_replace("\\", "/", $filePath);
				$fileCommentLength = (empty($fileComment) ? 0 : strlen($fileComment));
				$timestamp = (int)$timestamp;
				$timestamp = ($timestamp == 0 ? time() : $timestamp);

				$dosTime = $this->getDosTime($timestamp);
				$tsPack = pack("V", $timestamp);

				if (!isset($gpFlags) || strlen($gpFlags) != 2) {
					$gpFlags = "\x00\x00";
				}

				$isFileUTF8 = $this->isUTF8;
				$isCommentUTF8 = !empty($fileComment) && $this->isUTF8;
				
				$localExtraField = "";
				$centralExtraField = "";
				
				if ($this->addExtraField) {
					$localExtraField .= "\x55\x54\x09\x00\x03" . $tsPack . $tsPack . Zip::EXTRA_FIELD_NEW_UNIX_GUID;
					$centralExtraField .= "\x55\x54\x05\x00\x03" . $tsPack . Zip::EXTRA_FIELD_NEW_UNIX_GUID;
				}
				
				if ($isFileUTF8 || $isCommentUTF8) {
					$flag = 0;
					$gpFlagsV = unpack("vflags", $gpFlags);
					if (isset($gpFlagsV['flags'])) {
						$flag = $gpFlagsV['flags'];
					}
					$gpFlags = pack("v", $flag | (1 << 11));
					
					if ($isFileUTF8) {
						$utfPathExtraField = "\x75\x70"
							. pack ("v", (5 + strlen($filePath)))
							. "\x01" 
							.  pack("V", crc32($filePath))
							. $filePath;

						$localExtraField .= $utfPathExtraField;
						$centralExtraField .= $utfPathExtraField;
					}
					if ($isCommentUTF8) {
						$centralExtraField .= "\x75\x63" // utf8 encoded file comment extra field
							. pack ("v", (5 + strlen($fileComment)))
							. "\x01"
							. pack("V", crc32($fileComment))
							. $fileComment;
					}
				}

				$header = $gpFlags . $gzType . $dosTime. $fileCRC32
					. pack("VVv", $gzLength, $dataLength, strlen($filePath)); // File name length

				$zipEntry  = self::ZIP_LOCAL_FILE_HEADER
					. self::ATTR_VERSION_TO_EXTRACT
					. $header
					. pack("v", strlen($localExtraField)) // Extra field length
					. $filePath // FileName
					. $localExtraField; // Extra fields

				$this->zipwrite($zipEntry);

				$cdEntry  = self::ZIP_CENTRAL_FILE_HEADER
					. self::ATTR_MADE_BY_VERSION
					. ($dataLength === 0 ? "\x0A\x00" : self::ATTR_VERSION_TO_EXTRACT)
					. $header
					. pack("v", strlen($centralExtraField)) // Extra field length
					. pack("v", $fileCommentLength) // File comment length
					. "\x00\x00" // Disk number start
					. "\x00\x00" // internal file attributes
					. pack("V", $extFileAttr) // External file attributes
					. pack("V", $this->offset) // Relative offset of local header
					. $filePath // FileName
					. $centralExtraField; // Extra fields

				if (!empty($fileComment)) {
					$cdEntry .= $fileComment; // Comment
				}

				$this->cdRec[] = $cdEntry;
				$this->offset += strlen($zipEntry) + $gzLength;
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