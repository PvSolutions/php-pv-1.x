<?php
	
	if(! defined('UTILS_INCLUDED'))
	{
		define("UTILS_INCLUDED", 1) ;
		
		if(! isset($AUTO_REGISTER_SESSION))
		{
			$AUTO_REGISTER_SESSION = (php_sapi_name() == "cli") ? 0 : 1 ;
		}
		
		if($AUTO_REGISTER_SESSION)
		{
			@session_start() ;
		}
		
		if(! function_exists("mb_check_encoding"))
		{
			function mb_check_encoding($var, $encoding=null)
			{
				return false ;
			}
		}
		
		if(! function_exists('get_interpreter_path'))
		{
			$GLOBALS['USER_INTERPRETER_PATH'] = '' ;
			function get_interpreter_path()
			{
				if($GLOBALS['USER_INTERPRETER_PATH'] != '')
				{
					return $GLOBALS['USER_INTERPRETER_PATH'] ;
				}
				$osType = 'LINUX' ;
				if(PHP_OS == "WINNT" || PHP_OS == "WIN32")
				{
					$osType = 'WINDOWS' ;
				}
				$phpbin = preg_replace("@/lib(64)?/.*$@", "/bin/php", ini_get("extension_dir"));
				$execPath = dirname($phpbin)."/php" ;
				if($osType == "WINDOWS")
					$execPath .= ".exe" ;
				return $execPath ;
			}
		}
		if(! function_exists('get_current_script'))
		{
			function get_current_script()
			{
				$scriptPath = $_SERVER["argv"][0] ;
				for($i=1; $i<count($_SERVER["argv"]); $i++)
				{
					$scriptPath .= ' '.escapeshellarg($_SERVER["argv"][$i]) ;
				}
				$cmd = '' ;
				if(PHP_OS == "WINNT" || PHP_OS == "WIN32")
				{
					$cmd = 'start /b '.get_interpreter_path().' '.$scriptPath ;
				}
				else
				{
					$cmd = get_interpreter_path().' '.$scriptPath.' >/dev/null 2>&1 &' ;
				}
				// popen(pclose($cmd)) ;
				return $cmd ;
			}
		}
		if(! function_exists('launch_current_script'))
		{
			function launch_current_script()
			{
				pclose(popen(get_current_script(), "r")) ;
			}
		}
		
		/*
		if(isset($_SESSION['posted_param']))
		{
			$_POST = $_SESSION['posted_param'] ;
			unset($_SESSION['posted_param']) ;
		}
		*/
		
		function try_session_start()
		{
			if (! isset ($_COOKIE[ini_get('session.name')]))
			{
				session_start();
			}
		}
		
		function encode_html_symbols($texte)
		{
			return str_replace(
				array('`', '¡', '¢', '£', '¤', '¥', '¦', '§', '¨', '©', 'ª', '«', '¬', '­', '®', '¯', '°', '±', '²', '³', '´', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼', '½', '¾', '¿', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', '÷', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ'),
				array('&#96;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;'),
				$texte
			) ;
		}
		
		function array_join_values_of_key($sep, $data=array(), $key='')
		{
			$result = '' ;
			foreach($data as $i => $row)
			{
				$val = (isset($row[$key])) ? $row[$key] : '' ;
				if($i > 0)
					$result .= $sep ;
				$result .= $val ;
			}
			return $result ;
		}
		$word_separator = array('&','~','"','\'','\\?',' ','\\:','\\!','/','\\(','\\)','\\|',' ') ;
		
		if(! function_exists('import_fields_to_form_from_POST'))
		{
			function import_fields_to_form_from_POST()
			{
				import_fields_to_form_from_value($_POST) ;
			}
			function import_fields_to_form_from_GET()
			{
				import_fields_to_form_from_value($_GET) ;
			}
			function import_fields_to_form_from_value($values=array())
			{
				foreach($values as $n => $v)
				{
					echo '<input type="hidden" name="'.$n.'" value="'.htmlentities($v).'" />'."\n" ;
				}
			}
			
			function in_list()
			{
				$args = func_get_args() ;
				$str = $args[0] ;
				array_shift($args) ;
				return (in_array($str, $args)) ;
			}
			
			function redirect_to($url)
			{
				header('location:'.$url."") ;
				exit ;
			}
			function js_redirect_to($url)
			{
				echo '<script type="text/javascript">
			window.location = "'.$url.'" ;
		</script>' ;
			}
			function extract_words($text, $min_length=1)
			{
				global $word_separator ;
				$word_sep = join('|', $word_separator) ;
				$res = split($word_sep, $text) ;
				$words = array() ;
				foreach($res as $i => $word_res)
				{
					if($word_res == '')
						continue ;
					if(strlen($word_res) < $min_length)
					{
						continue ;
					}
					$words[] = $word_res  ;
				}
				$words = array_unique($words) ;
				return $words ;
			}
			function extract_exprs($text, $expr_tag=array('"', '\''), $expr_separator=array(',',' '), $expr_escapes=array('\\'))
			{
				$exprs = array() ;
				$begin_expr = 0 ;
				$end_expr = 0 ;
				$begin_expr_char = '' ;
				$end_expr_char = '' ;
				$current_expr = '' ;
				for($i=0; $i<strlen($text); $i++)
				{
					if(! $begin_expr)
					{
						if(in_array($text[$i], $expr_tag))
						{
							$begin_expr = 1 ;
							$end_expr = 0 ;
							$begin_expr_char = $expr_tag[array_search($text[$i], $expr_tag)] ;
							if($current_expr != "")
							{
								$exprs[] = $current_expr ;
							}
							$current_expr = '' ;
						}
						elseif(in_array($text[$i], $expr_separator))
						{
							if($current_expr != "")
							{
								$exprs[] = $current_expr ;
								$current_expr = '' ;
							}
						}
						else
						{
							$current_expr .= $text[$i] ;
						}
					}
					else
					{
						if(in_array($text[$i], $expr_escapes))
						{
							if(isset($text[$i + 1]))
							{
								if($text[$i + 1] == $begin_expr_char)
								{
									continue ;
								}
							}
							$current_expr .= $text[$i] ;
						}
						elseif($text[$i] == $begin_expr_char)
						{
							if(isset($text[$i - 1]))
							{
								if(in_array($text[$i - 1], $expr_escapes))
								{
									$current_expr .= $text[$i] ;
									continue ;
								}
							}
							$begin_expr = 0 ;
							$end_expr = 1 ;
							$exprs[] = $current_expr ;
							$current_expr = '' ;
							// print $text[$i].'<br />' ;
						}
						else
						{
							$current_expr .= $text[$i] ;
						}
					}
				}
				if($current_expr != '')
				{
					$exprs[] = $current_expr ;
				}
				return $exprs ;
			}
			
			function concat_arrays()
			{
				$Arrays = func_get_args() ;
				$Results = array() ;
				foreach($Arrays as $i => $Array)
				{
					if(! is_array($Array))
						$Results[] = $Array ;
					else
					{
						foreach($Array as $Key => $Value)
						{
							$Results[] = $Value ;
						}
					}
				}
				return $Results ;
			}
			function array_contains_keys($array, $keys)
			{
				$ok = 1 ;
				foreach($keys as $i => $key)
				{
					if(! isset($array[$key]))
					{
						$ok = 0 ;
						break ;
					}
				}
				return $ok ;
			}
			function is_type_of($value, $type)
			{
				$ok = 1 ;
				if($type == '__variant')
				{
					return $ok ;
				}
				if($type == '__scalar')
				{
					return is_scalar($value) ;
				}
				if(($type_value = gettype($value)) != $type)
				{
					if($type_value == 'object')
					{
						if(get_class($array[$key]) != $type)
						{
							$ok =0 ;
						}
					}
					else
					{
						$ok = 0 ;
					}
				}
				return $ok ;
			}
			function array_contains_keys_of_type($array, $keys)
			{
				$ok = 1 ;
				foreach($keys as $key => $type)
				{
					if(! isset($array[$key]))
					{
						$ok =0 ;
					}
					else
					{
						$ok = is_type_of($array[$key], $type) ;
					}
					if(! $ok)
					{
						break ;
					}
				}
				return $ok ;
			}
			function array_contains_key($array, $key)
			{
				return array_contains_keys($array, array($key)) ;
			}
			function array_contains_values($array, $values)
			{
				$ok = 1 ;
				foreach($values as $i => $value)
				{
					$found = 0 ;
					foreach($array as $j => $v)
					{
						if($value == $v)
						{
							$found = 1 ;
							break ;
						}
					}
					if(! $found)
					{
						$ok = 0 ;
						break ;
					}
				}
				return $ok ;
			}
			function array_contains_value($array, $value)
			{
				return array_contains_values($array, array($value)) ;
			}
			function array_extract_value_for_key_str($haystack, $key_str, $key_sep=",")
			{
				$keys = explode($key_sep, $key_str) ;
				return array_extract_value_for_keys($haystack, $keys) ;
			}
			function array_rename_key($haystack, $key, $new_key)
			{
				return array_change_value($haystack, $key, $new_key, NULL) ;
			}
			function array_change_value($haystack, $key, $new_key="", $value = NULL)
			{
				$result = array() ;
				$keys = array_keys($haystack) ;
				foreach($keys as $i => $cur_key)
				{
					if($cur_key != $key)
					{
						$result[$cur_key] = $haystack[$cur_key] ;
					}
					else
					{
						if($new_key == "")
						{
							$new_key = $key ;
						}
						if($value == NULL)
						{
							$value = $haystack[$cur_key] ;
						}
						$result[$new_key] = $value ;
					}
				}
				return $result ;
			}
			function array_diff_value($src, $dest)
			{
				$result = array() ;
				foreach($src as $n => $v)
				{
					if(! isset($dest[$n]))
					{
						$result[$n] = $v ;
						continue ;
					}
					if($v != $dest[$n])
					{
						$result[$n] = $v ;
					}
				}
				foreach($dest as $n => $v)
				{
					if(! isset($src[$n]))
					{
						$result[$n] = $v ;
					}
				}
				return $result ;
			}
				
			function common_db_escape_string($text)
			{
				$result = $text ;
				if(! get_magic_quotes_gpc())
				{
					$result = addslashes($result) ;
				}
				return $result ;
			}
			function html_escape_attr_value($text)
			{
				$result = $text ;
				$result = str_replace('"', '&quot;', $result) ;
				return $result ;		
			}
			function clean_special_chars($text)
			{
				$result = $text ;
				$result = str_replace("\n", " ", $result) ;
				$result = str_replace("\t", " ", $result) ;
				$result = str_replace("&agrave;", "a", $result) ;
				$result = str_replace("&acirc;", "a", $result) ;
				$result = str_replace("à", "a", $result) ;
				$result = str_replace("â", "a", $result) ;
				$result = str_replace("ä", "a", $result) ;
				$result = str_replace("&eacute;", "e", $result) ;
				$result = str_replace("&egrave;", "e", $result) ;
				$result = str_replace("&ecirc;", "e", $result) ;
				$result = str_replace("&euml;", "e", $result) ;
				$result = str_replace("é", "e", $result) ;
				$result = str_replace("è", "e", $result) ;
				$result = str_replace("ê", "e", $result) ;
				$result = str_replace("ë", "e", $result) ;
				$result = str_replace("&igrave;", "i", $result) ;
				$result = str_replace("&icirc;", "i", $result) ;
				$result = str_replace("ì", "i", $result) ;
				$result = str_replace("î", "i", $result) ;
				$result = str_replace("ï", "i", $result) ;
				$result = str_replace("&ograve;", "o", $result) ;
				$result = str_replace("&ocirc;", "o", $result) ;
				$result = str_replace("ò", "o", $result) ;
				$result = str_replace("ô", "o", $result) ;
				$result = str_replace("ö", "o", $result) ;
				$result = str_replace("&ugrave;", "u", $result) ;
				$result = str_replace("&ucirc;", "u", $result) ;
				$result = str_replace("ù", "u", $result) ;
				$result = str_replace("û", "u", $result) ;
				$result = str_replace("ü", "u", $result) ;
				$result = str_replace("&ccedil;", "c", $result) ;
				$result = str_replace("ç", "c", $result) ;
				$result = str_replace("’", "'", $result) ;
				$result = str_replace("`", "'", $result) ;
				$result = str_replace("µ", "u", $result) ;
				$result = str_replace("£", "E", $result) ;
				$result = str_replace("$", "", $result) ;
				$result = str_replace("¤", "o", $result) ;
				$result = str_replace("°", "o", $result) ;
				$result = str_replace("@", "a", $result) ;
				$result = str_replace("^", " ", $result) ;
				$result = str_replace("¨", " ", $result) ;
				$result = str_replace("&quot;", "'", $result) ;
				$result = str_replace("&160#;", " ", $result) ;
				$result = preg_replace("/&[A-Z0-9#]+;/i", "", $result) ;
				$result = str_replace('\\\\', "", $result) ;
				$result = preg_replace("/[[:space:]]{2,}/", " ", $result) ;
				$result = preg_replace("/^[[:space:]]+/", "", $result) ;
				//$result = ereg_replace("\s\s+", "", $result) ;
				return $result ;
			}
			
			function date_fr($Date)
			{
				$DateAttr = explode("-", $Date) ;
				if(count($DateAttr) != 3)
				{
					return $Date ;
				}
				return $DateAttr[2]."/".$DateAttr[1].'/'.$DateAttr[0] ;
			}
			function date_time_fr($Date)
			{
				$dateParts = explode(" ", $Date) ;
				if(count($dateParts) != 2)
					return $Date ;
				$DateAttr = explode("-", $dateParts[0]) ;
				if(count($DateAttr) != 3)
				{
					return $Date ;
				}
				return $DateAttr[2]."/".$DateAttr[1].'/'.$DateAttr[0].' '.$dateParts[1] ;
			}
			function hour($Time)
			{
				$TimeAttr = explode(":", $Time) ;
				if(count($TimeAttr) != 3)
				{
					return $Time ;
				}
				if($TimeAttr[0] == '00' && $TimeAttr[1] == '00')
				{
					return "" ;
				}
				return $TimeAttr[0].":".$TimeAttr[1] ;
			}
			function get_age($date)
			{
				$birthDate = explode("-", $date) ;
				$age = (date("md", strtotime($date)) > date("md")
    ? ((date("Y") - $birthDate[0]) - 1)
    : (date("Y") - $birthDate[0]));
				return $age ;
			}
			
			function send_html_mail($to, $subject, $text, $from='', $cc='', $bcc='')
			{
				if($from == "")
				{
					$from = ini_get('sendmail_from') ;
				}
				// Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				// En-têtes additionnels
				// $headers .= 'To: '.$to. "\r\n";
				$headers .= 'From: '.$from. "\r\n";
				if($cc != '')
				{
					$headers .= 'Cc: '. $cc . "\r\n";
				}
				if($bcc != '')
				{
					$headers .= 'Bcc: '.$bcc . "\r\n";			
				}
				// Envoi
				return mail($to, $subject, $text, $headers);
			}
			function send_plain_mail($to, $subject, $text, $from='', $cc='', $bcc='')
			{
				if($to == "")
				{
					$to = ini_get('sendmail_from') ;
				}
				if($from == "")
				{
					$from = ini_get('sendmail_from') ;
				}
				// Pour envoyer un mail , l'en-tête Content-type doit être défini
				$headers = 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
				// En-têtes additionnels
				// $headers .= 'To: '.$to. "\r\n";
				$headers .= 'From: '.$from. "\r\n";
				if($cc != '')
				{
					$headers .= 'Cc: '. $cc . "\r\n";
				}
				if($bcc != '')
				{
					$headers .= 'Bcc: '.$bcc . "\r\n";			
				}
				// Envoi
				return mail($to, $subject, $text, $headers);
			}
			function send_mail_with_attachments($to, $subject, $text, $files=array(), $from='', $cc='', $bcc='')
			{
				if($to == "")
				{
					$to = ini_get('sendmail_from') ;
				}
				if($from == "")
				{
					$from = ini_get('sendmail_from') ;
				}
				// En-têtes additionnels
				$headers .= '';
				$headers .= 'To: '.$to. "\r\n";
				$headers .= 'From: '.$from. "\r\n";
				if($cc != '')
				{
					$headers .= 'Cc: '. $cc . "\r\n";
				}
				if($bcc != '')
				{
					$headers .= 'Bcc: '.$bcc . "\r\n";			
				}
				$mime_boundary="==Multipart_Boundary_x".md5(mt_rand())."x";
				// Boundary
		  $headers .= "Content-Type: multipart/mixed;\r\n" .
					" boundary=\"{$mime_boundary}\"" ;
				// Text
		  $message = "...\n\n".
			 "--{$mime_boundary}\n".
			 "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
			 "Content-Transfer-Encoding: 7bit\n\n" .
			 $text . "\n\n";
				// Files
				foreach($files as $i => $file_path)
				{
					if(! file_exists($file_path))
					{
						continue ;
					}
					$file_type = mime_content_type($file_path) ;
					$file_name = basename($file_path) ;
					// open the file for a binary read
					$file = fopen($file_path,'rb');
					// read the file content into a variable
					$file_data = fread($file,filesize($file_path));
					// close the file
					fclose($file);
					// now we encode it and split it into acceptable length lines
					$file_data = chunk_split(base64_encode($file_data));
					// Attach the file
					$message .= "--{$mime_boundary}\n" .
					"Content-Type: {$file_type};\n" .
					" name=\"{$file_name}\"\n" .
					//"Content-Disposition: attachment;\n" .
					//" filename=\"{$fileatt_name}\"\n" .
					"Content-Transfer-Encoding: base64\n\n" .
					$file_data . "\n\n" .
					"--{$mime_boundary}--\n";
				}
				// Envoi
				return mail($to, $subject, $message, $headers);
			}
			
			function _content_of($text, $field_name='', $type='text')
			{
				$result = $text ;
				if(function_exists('format_value_of_'.$field_name))
				{
					eval('$result = format_value_of_'.$field_name.'($result) ;'."\n") ;
					return $result ;
				}
				if($type == 'text' || $type == 'char' || $type == 'varchar' || $type == 'varchar2')
				{
					$result = format_text($result) ;
				}
				elseif($type == 'int' || $type == 'long' || $type == 'shortint' || $type == 'bigint' || $type == 'tinyint' || $type == 'mediumint' || $type == 'smallint')
				{
					$result = format_integer($result) ;
				}
				elseif($type == 'float' || $type == 'double' || $type == 'real' || $type == 'decimal')
				{
					$result = format_decimal($result) ;
				}
				elseif($type == 'date')
				{
					$result = format_date($result) ;
				}
				elseif($type == 'datetime')
				{
					$result = format_datetime($result) ;
				}
				elseif($type == 'time')
				{
					$result = ($result) ;
				}
				elseif($type == 'timestamp')
				{
					$result = format_timestamp($result) ;
				}
				elseif($type == 'year')
				{
					$result = format_year($result) ;
				}
				elseif($type == 'blob' || $type == 'tinyblob' || $type == 'mediumblob' || $type == 'longblob')
				{
					$result = format_blob($result) ;
				}
				elseif($type == 'binary' || $type == 'varbinary')
				{
					$result = format_binary($result) ;
				}
				elseif($type == 'binary' || $type == 'varbinary')
				{
					$result = format_binary($result) ;
				}
				elseif($type == 'enum')
				{
					$result = format_enum($result) ;
				}
				elseif($type == 'set')
				{
					$result = format_set($result) ;
				}
				return $result ;
			}
			function format_text($text)
			{
				$result = $text ;
				return $result ;
			}
			function format_blob($text)
			{
				$result = $text ;
				return $result ;
			}
			function format_binary($text)
			{
				$result = $text ;
				return $result ;
			}
			function format_enum($text)
			{
				$result = $text ;
				return $result ;
			}
			function format_set($text)
			{
				$result = $text ;
				return $result ;
			}
			function format_integer($number)
			{
				$result = format_money($number, 0) ;
				return $result ;
			}
			function format_decimal($number)
			{
				$result = format_money($number, 2) ;
				return $result ;
			}
			function format_money($number, $decimal_count=2, $max_length=5)
			{
				if($number == "")
					$number = 0 ;
				$result = number_format($number, $decimal_count, ',', ' ');
				if($decimal_count)
				{
					if(preg_match('/,0{'.$decimal_count.'}$/', $result))
					{
						$result = preg_replace('/,0{'.$decimal_count.'}$/', '&nbsp;&nbsp;&nbsp;', $result) ;
					}
				}
				$current_length = strlen($result) ;
				for($i=0; $i<$max_length - $current_length; $i++)
				{
					$result = '&nbsp;'.$result ;
				}
				return $result ;
			}
			function format_date($date)
			{
				$result = $date ;
				$attrs = explode('-', $result) ;
				$result = $attrs[2].'/'.$attrs[1].'/'.$attrs[0] ;
				return $result ;
			}
			function format_time($time)
			{
				$result = $time ;
				return $result ;
			}
			function format_datetime($datetime)
			{
				$attrs = explode(' ', $datetime) ;
				$date = $attrs[0] ;
				$time = '00:00:00' ;
				if(isset($attrs[1]))
				{
					$time = $attrs[1] ;
				}
				$result = format_date($date).' '.format_time($time) ;
				return $result ;
			}
			function format_timestamp($timestamp)
			{
				$result = $timestamp ;
				$result = format_datetime($timestamp) ;
				return $result ;
			}
			function format_year($text)
			{
				$result = format_text($text, 0) ;
				return $result ;
			}
			
			function build_detail_query($queryData)
			{
				if(! is_array($queryData))
					return '' ;
				$ctn = '' ;
				foreach($queryData as $name => $v)
				{
					if(! is_string($v))
					{
						continue ;
					}
					$ctn .= $name.' : '.$v."\r\n" ;
				}
				return $ctn ;
			}
			
			function parse_str_def($text, $def=array())
			{
				$result = $def ;
				try
				{
					parse_str($text, $data) ;
					if($data)
					{
						$result = $data ;
					}
				}
				catch(Exception $ex)
				{
				}
				return $result ;
			}
			
			function & _value_def($haystack, $param_name, $default='', $as='')
			{
				if(is_string($param_name) || is_numeric($param_name))
				{
					// print print_r($haystack, true).' & '.$param_name ;
					if(is_object($haystack))
					{
						$haystack = get_object_vars($haystack) ;
					}
					$value = (isset($haystack[$param_name])) ? _cast_value($haystack[$param_name], $as) : $default ;
					return $value ;
				}
				$value = $default ;
				if(is_array($param_name))
				{
					$haystack_temp = $haystack ;
					foreach($param_name as $n => $v)
					{
						// print $v.'<br />' ;
						// On a passé n'importe quoi sauf du texte, aie
						if(! is_string($v) && ! is_numeric($v))
						{
							$haystack_temp = null ;
							break ;
						}
						$cond = '' ;
						if(strpos($v, ':') !== false)
						{
							$attr = substr($v, 0, strpos($v, ':')) ;
							$cond = substr($v, strpos($v, ':') + 1) ;
						}
						else
						{
							$attr = $v ;
						}
						$haystack_temp = ((isset($haystack_temp[$attr])) ? $haystack_temp[$attr] : null) ;
						if($haystack_temp === null)
							break ;
						if($cond != '')
						{
							$filter = preg_replace('/\$([a-z0-9\_]+)/i', '$sub_haystack["\1"]', $cond) ;
							if(is_array($haystack_temp))
							{
								$OK = 0 ;
								foreach($haystack_temp as $i => $sub_haystack)
								{
									eval('$OK = ('.$filter.') ;') ;
									if($OK)
									{
										$haystack_temp = $sub_haystack ;
										break ;
									}
								}
								if(! $OK)
								{
									$haystack_temp = null ;
									break ;
								}
							}
						}
					}
					if($haystack_temp !== false && $haystack_temp !== null)
					{
						$value = $haystack_temp ;
					}
				}
				return $value ;
			}
			function _GET_def($param_name, $default='', $as='')
			{
				return _value_def($_GET, $param_name, $default, $as) ;
			}
			function _SESSION_def($param_name, $default='', $as='')
			{
				return _value_def($_SESSION, $param_name, $default, $as) ;
			}
			function _POST_def($param_name, $default='', $as='')
			{
				return _value_def($_POST, $param_name, $default, $as) ;
			}
			function _REQUEST_def($param_name, $default='', $as='')
			{
				return _value_def($_REQUEST, $param_name, $default, $as) ;
			}
			
			function array_has_empty_value($haystack, $keys=array(), $empty_value="")
			{
				$ok = 0 ;
				foreach($keys as $i => $key)
				{
					if(! isset($haystack[$key]))
					{
						$ok = 1 ;
					}
					elseif($haystack[$key] == $empty_value)
					{
						$ok = 1 ;
					}
					if($ok)
					{
						break ;
					}
				}
				return $ok ;
			}
			function array_assign_value($haystack, $keys=array(), $default_value="")
			{
				$result = $haystack ;
				while(list($i, $key) = each($keys))
				{
					if(! isset($result[$key]))
					{
						$result[$key] = $default_value ;
					}
				}
				return $result ;
			}
			function array_find_empty_values($haystack, $keys=array(), $empty_value="", $first_only=0)
			{
				$ok = 0 ;
				$empty_keys = array() ;
				foreach($keys as $i => $key)
				{
					if(! isset($haystack[$key]))
					{
						$ok = 1 ;
					}
					elseif($haystack[$key] == $empty_value)
					{
						$ok = 1 ;
					}
					if($ok)
					{
						$empty_keys[] = $key ;
						if($first_only)
						{
							break ;
						}
					}
				}
				return $empty_keys ;
			}
			function array_find_empty_value($haystack, $keys=array(), $empty_value="")
			{
				$key = array_find_empty_values($haystack, $keys, $empty_value) ;
				$result = ((isset($key[0]))) ? $key[0] : '' ;
				return $result ;
			}
			function array_remove_empty_values($haystack=array(), $keep_keys=0, $empty_values=array(""))
			{
				$result = array() ;
				foreach($haystack as $i => $v)
				{
					if(! in_array($v, $empty_values))
					{
						if($keep_keys)
						{
							$result[$i] = $v ;
						}
						else
						{
							$result[] = $v ;
						}
					}
				}
				return $result ;
			}
			function array_remove_empty_value($haystack=array(), $keep_keys=0, $empty_value="")
			{
				return array_remove_empty_values($haystack, $keep_keys, array($empty_value)) ;
			}
			
			// Cast function by Reference, including field formats
			function _cast_ref(& $var, $type='string', $default_value=null)
			{
				$var = _cast_value($var, $type, $default_value) ;
			}
			function _cast_value($val, $type='string', $default_value=null)
			{
				if($type == '') { return $val ; }
				$var = $val ;
				if(is_array($type))
				{
					if(count($type))
					{
						settype($var, 'string') ;
						(in_array($var, $type)) ? '' : $var = $type[0] ;
						return $var ;
					}
					else
					{
						$type = 'string' ;
					}
				}
				$type = ($type == 'char' or $type == 'text' or $type == 'varchar') ? 'string' : strtolower($type) ;
				$type = ($type == 'timestamp') ? 'int' : $type ;
				if(in_array($type, array('int', 'integer', 'double', 'float', 'number', 'numeric')))
				{
					$var = str_replace('[[:space:]]', '', $var) ;
					$var = str_replace(',|\:|;', '.', $var) ;
				}
				$php_types = array("array", "bool", "boolean", "float", "int", "integer", "null", "object", "string") ;
				if(in_array($type, $php_types))
				{
					settype($var, $type) ;
					return $var ;
				}
				$match_pos = strpos($type, '/') ;
				$i1_pos = strpos($type, '[') ;
				$i2_pos = strpos($type, ']') ;
				if($match_pos !== false && $match_pos == 0)
				{
					$var = (preg_match($type, $var, $match)) ? $match[0] : $default_value ;
				}
				elseif(($i1_pos !== false && $i1_pos == 0) || ($i2_pos !== false && $i2_pos == 0))
				{
					$left_tag = $type[0] ;
					$right_tag = $type[strlen($type) - 1] ;
					$nb_str = substr($type, 1, strlen($type) - 2) ;
					$nbs = explode(',', $nb_str) ;
					$cond = '1' ;
					(! isset($nbs[1])) ? $nbs[1] = '..' : 1 ;
					if($nbs[0] != '..' or $nbs[0] != '')
					{
						$sign = ($left_tag == '[') ? '>=' : '>' ;
						$cond .= ' and intval($var)'.$sign.intval($nbs[0]) ;
					}
					if($nbs[1] != '..' or $nbs[1] != '')
					{
						$sign = ($right_tag == ']') ? '<=' : '<' ;
						$cond .= ' and intval($var)'.$sign.intval($nbs[1]) ;
					}
					eval('if(!('.$cond.')) { $var = $default_value ; }') ;
				}
				elseif($type == 'url' || $type == 'same_domain')
				{
					$var = match_url($var) ;
					if(! $var)
						$var = $default_value ;
					($var && $type == 'same_domain') ? ($var = (has_domain($var, get_current_url(0))) ? $var : $default_value) : 1 ;
				}
				else
				{
					trim($var) ;
					rtrim($var) ;
					settype($var, 'string') ;
					switch($type)
					{
						case 'date' :
							$default_value = (! $default_value) ? date('Y-m-d') : $default_value ;
							$var = (preg_match('/^(\d\d\d\d-\d\d?-\d\d?)/', $var, $match)) ? $match[1] : $default_value ;
							if($var != $default_value) {
								(strtotime($var) < strtotime('1970-01-01')) ? $var = $default_value : 1 ;
							}
							break ;
						case 'time' :
							$default_value = (! $default_value) ? date('H:i:s') : $default_value ;
							$var = (preg_match('/^(\d\d?:\d\d?:\d\d?)/', $var, $match)) ? $match[1] : $default_value ;
							break ;
						case 'datetime' :
							$default_value = (! $default_value) ? date('Y-m-d H:i:s') : $default_value ;
							if(preg_match('/^(\d\d\d\d-\d\d?-\d\d? \d\d?:\d\d?:\d\d?)/', $var, $match))
								$var = $match[1] ;
							elseif(preg_match('/^(\d\d\d\d-\d\d?-\d\d? \d\d?:\d\d?)/', $var, $match)) 
								$var = $match[1].':00' ;
							elseif(preg_match('/^(\d\d\d\d-\d\d?-\d\d? \d\d?)/', $var, $match))
								$var = $match[1].':00:00' ;
							elseif(preg_match('/^(\d\d\d\d-\d\d?-\d\d?/', $var, $match)) 
								$var = $match[1].' 00:00:00' ;
							else
								$default_value ;
							if($var != $default_value) {
								(strtotime($var) < strtotime('1970-01-01')) ? $var = $default_value : 1 ;
							}
							break ;
						case 'person_name' :
							$var = preg_replace('/[[:space:]][[:space:]]+/', ' ', $var) ;
							$var = (preg_match('/^([a-z][a-z0-9&;éèçàêîâûïüôöü \']+)$/i', $var, $match)) ? $match[1] : $default_value ;
							break ;
						case 'user_name' :
							$var = (validate_name_user_format($var)) ? $var : $default_value ;
							break ;
						case 'password' :
							$var = (validate_password_format($var)) ? $var : $default_value ;
							break ;
						case 'action_name' :
							$var = (preg_match('/^([a-z0-9_\-]+)$/i', $var, $match)) ? $match[1] : $default_value ;
							break ;
						case 'relative_path' :
							$var = (preg_match('/^([^<>\*\+\?\^\:\|"]+)/i', $var, $match)) ? $match[1] : $default_value ;
							break ;
						case 'path' :
							$var = (preg_match('/^([^<>\*\^\|"\']+)/i', $var, $match)) ? $match[1] : $default_value ;
							break ;
						case 'normal_text' :
							$var = strip_tags($var) ;
							break ;
						case 'password' :
							$var = strip_tags($var) ;
							break ;
						case 'html_content' :
							$var = remove_suspicious_html($var) ;
							break ;
						case 'abs_url' :
							$var = (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $var, $match)) ? $match[0] : $var ;
							break ;
						case 'email' :
							$var = (validate_email_format($var)) ? $var : $default_value ;
							break ;
						case 'intbool' :
							$var = (intval($var)) ? 1 : 0 ;
							break ;
					}
				}
				return $var ;
			}
			function _cast_array_ref(& $data, $defs=array())
			{
				$key_names = array_keys($defs) ;
				$data = array_assign_value($data, $key_names, '') ;
				$ok = 1 ;
				while(list($i, $key_name) = each($key_names))
				{
					$data[$key_name] = _cast_value($data[$key_name], $defs[$key_name], null) ;
					if($ok)
					{
						(! $data[$key_name]) ? $ok = 0 : 1 ;
					}
				}
				return $ok ;
			}
			function _declare_array(& $data, $defs=array())
			{
				return _cast_array_ref($data, $defs) ;
			}
			
			function _html_debug($var, $exit_after=false)
			{
				print '<pre>'.print_r($var, true).'</pre>' ;
				if($exit_after)
					exit ;
			}
			
			function validate_name_user_format($name_user)
			{
				$ok = 1 ;
				if($name_user == '')
				{
					return 0 ;
				}
				if(! preg_match('/^[a-zA-Z0-9\_\.]{4,}$/', $name_user))
				{
					$ok = 0 ;
				}
				return $ok ;
			}
			function validate_password_format($password)
			{
				$ok = 1 ;
				if($password == '')
				{
					return 0 ;
				}
				if(! preg_match('/^[a-zA-Z0-9\_\/@:\^\\#\|\-]{4,}$/', $password))
				{
					$ok = 0 ;
				}
				return $ok ;
			}
			function validate_email_format($email)
			{
				$ok = 1 ;
				if($email == '')
				{
					return 0 ;
				}
				if(! preg_match('/^[a-z0-9\_\.]{4,}@[a-z0-9_\.\-]{2,}$/i', $email))
				{
					$ok = 0 ;
				}
				return $ok ;
			}
			function validate_url_format($url)
			{
				$ok = 0 ;
				if(preg_match('@^[a-z]+[:\|]+[\\/]+(.+)$@i', $url))
				{
					$ok = 1 ;
				}
				return $ok ;
			}
			function validate_action_name_format($text)
			{
				$OK = 1 ;
				if(preg_match('/[^a-z0-9_]/', $text))
				{
					$OK = 0 ;
				}
				return $OK ;
			}
			function validate_file_path_format($path)
			{
				$ok = 0 ;
				if(preg_match('@^[a-z]{2,}(:|\|){1}(\\|/)+(.)+$@i', $path))
				{
					$ok = 1 ;
				}
				return $ok ;
			}
			
			// Remove HTML contents
			function remove_html($text)
			{
				return strip_tags($text) ;
			}
			function remove_invisible_html($text)
			{
				$result = $text ;
				$var = preg_replace('@<\?php[^>]*?>.*?</script>@si', '', $var) ;
				$var = preg_replace('@<\!--[^>]*?>.*?-->@si', '', $var) ;
				$result = preg_replace('@<script[^>]*?>.*?</script>@si', '', $result) ;
				$result = preg_replace('@<style[^>]*?>.*?</style>@si', '', $result) ;
				$result = preg_replace('@<object[^>]*?>.*?</object>@si', '', $result) ;
				$result = preg_replace('@<embed[^>]*?>.*?</embed>@si', '', $result) ;
				$result = preg_replace('@<applet[^>]*?>.*?</applet>@si', '', $result) ;
				$result = preg_replace('@<noframes[^>]*?>.*?</noframes>@si', '', $result) ;
				$result = preg_replace('@<noscript[^>]*?>.*?</noscript>@si', '', $result) ;
				$result = preg_replace('@<noembed[^>]*?>.*?</noembed>@si', '', $result) ;
				$result = preg_replace('@<iframe[^>]*?>.*?</iframe>@si', '', $result) ;
				$result = preg_replace('@<frame[^>]*?>.*?</frame>@si', '', $result) ;
				$result = preg_replace('@<frameset[^>]*?>.*?</script>@si', '', $result) ;
				return $result ;
			}
			function remove_suspicious_html($text)
			{
				$result = $text ;
				$result = remove_invisible_html($result) ;
				$result = str_ireplace('javascript:', '', $result) ;
				return $result ;
			}
			
			// URLs
			if(! isset($HOST_ALIAS))
			{
				$HOST_ALIAS = array() ;
			}
			if(! function_exists('get_current_url'))
			{
				function apply_host_alias($url)
				{
					global $HOST_ALIAS ;
					$result = $url ;
					foreach($HOST_ALIAS as $search => $alias)
					{
						$result = str_replace($search, $alias, $result) ;
					}
					return $result ;
				}
				function get_current_url()
				{
					if(! isset($_SERVER["SERVER_NAME"]))
					{
						return "" ;
					}
					$protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "") ? "https" : "http" ;
					$url = $protocol."://".$_SERVER['SERVER_NAME'] ;
					if($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443')
					{
						$url .= ':'.$_SERVER['SERVER_PORT'] ;
					}
					$url .= $_SERVER['REQUEST_URI'] ;
					return $url ;
					// return apply_host_alias($url) ;
				}
				function get_current_url_dir()
				{
					return get_url_dir(get_current_url()) ;
				}
				function get_url_dir($current_url)
				{
					$current_url_dir = '' ;
					$php_ext_pattern = '(.html)|(.php)|(.php3)|(.php4)|(.php5)|(.phtml)|(.inc)$' ;
					$current_url = preg_replace('/\?[^\?]+$/', '', $current_url) ;
					if(preg_match('@\/$@', $current_url))
					{
						$current_url_dir = preg_replace('@\/$@', '', $current_url) ;
					}
					elseif(preg_match('/'.$php_ext_pattern.'/', $current_url))
					{
						$current_url_dir = dirname($current_url) ;
					}
					else
					{
						$current_url_dir = $current_url ;
					}
					return $current_url_dir ;
				}
				function update_current_url_params($params=array(), $encodeValues=1, $forceDecodeParams=0)
				{
					return update_url_params(get_current_url(), $params, $encodeValues, $forceDecodeParams) ;
				}
				function update_url_params($url, $params=array(), $encodeValues=1, $forceDecodeParams=0)
				{
					$url_attrs = explode('?', $url, 2) ;
					$url_params = array() ;
					if(isset($url_attrs[1]))
					{
						if($url_attrs[1] != "")
						{
							if($forceDecodeParams)
							{
								parse_str($url_attrs[1], $url_params) ;
							}
							else
							{
								$url_params_data = explode('&', $url_attrs[1]) ;
								foreach($url_params_data as $i => $data_temp)
								{
									$attrs_temp = explode('=', $data_temp) ;
									$url_params[$attrs_temp[0]] = (isset($attrs_temp[1])) ? $attrs_temp[1] : '' ;
								}
							}
						}
					}
					$native_keys = array_keys($url_params) ;
					foreach($params as $n => $v)
					{
						if($v === null)
						{
							if(isset($url_params[$n]))
								unset($url_params[$n]) ;
						}
						else
						{
							$url_params[$n] = $v ;
						}
					}
					$url_res = $url_attrs[0] ;
					if(count($url_params))
					{
						$url_res .= '?' ;
						$i = 0 ;
						foreach($url_params as $n => $v)
						{
							if($i)
								$url_res .= '&' ;
							$paramValue = ($encodeValues && (! in_array($n, $native_keys) && ! $forceDecodeParams)) ? urlencode($v) : $v ;
							$url_res .= urlencode($n).'='.$paramValue ;
							$i++ ;
						}
					}
					return $url_res ;
				}
				function update_url_param($ParamName, $ParamValue, $URL)
				{
					return update_url_params($URL, array($ParamName => $ParamValue)) ;
				}
				function send_data_to_url($url, $data=array())
				{
					if(! isset($data['get']))
					{
						$data['get'] = array() ;
					}
					if(! isset($data['post']))
					{
						$data['post'] = array() ;
					}
					if(! isset($data['file']))
					{
						$data['file'] = array() ;
					}
					// Update the URL
					if(! preg_match('@^(http://)|(https://)|(ssl://)|(ftp://)|(file:///)|(sftp://)@', $url))
					{
						if($url == '')
						{
							$url = ereg_replace(dirname($_SERVER['SCRIPT_NAME']).'/', '', $_SERVER['SCRIPT_NAME']) ;
						}
						if(ereg('^\?', $url))
						{
							$url = $_SERVER['REQUEST_URI'].$url ;
						}
						if(! ereg('/', $url))
						{
							$url = dirname($_SERVER['REQUEST_URI']).'/'.$url ;
						}
						if(ereg('^/', $url))
						{
							$url = $_SERVER['SERVER_NAME'].$url ;
						}
						else
						{
							$url = $_SERVER['SERVER_NAME'].'/'.$url ;
						}
						$url = 'http://'.$url ;
					}
					$get_list = '' ;
					if(preg_match('/\?(.+)$/', $url, $match))
					{
						$get_list = $match[1] ;
						$url = str_replace('\?'.$match[0], '', $url) ;
					}
					// Update the server
					$server = '' ;
					$process_url = preg_replace('@^[A-Za-z0-9]+:[/]+@', '', $url) ;
					$url_attrs = split('/', $process_url) ;
					if(count($url_attrs))
					{
						$server = $url_attrs[0] ;
					}
					//print $server.' '.$url ;
					foreach($data['get'] as $n => $v)
					{
						if($get_list != '')
						{
							$get_list .= '&' ;
						}
						$get_list .= $n.'='.urlencode($v) ;
					}
					$header = '' ;
					srand((double)microtime()*1000000);
					$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
					$header = "POST $url?$get_list HTTP/1.0\r\n";
					$header .= "Host: $server\r\n";
					$header .= "Content-type: multipart/form-data, boundary=$boundary\r\n";
					// attach post vars
					$data_list = '' ;
					foreach($data['post'] AS $n => $v){
						$data_list .="--$boundary\r\n";
						$data_list .= "Content-Disposition: form-data; name=\"".$n."\"\r\n";
						$data_list .= "\r\n".$v."\r\n";
						$data_list .="--$boundary\r\n";
					}
					// and attach the files
					$data_list .= "--$boundary\r\n";
					foreach($data['file'] as $n => $file_name)
					{
						$content_file = join("", file($file_name));
						$content_type = mime_content_type($file_name) ;
						$data_list .="Content-Disposition: form-data; name=\"$n\"; filename=\"$file_name\"\r\n";
						$data_list .= "Content-Type: $content_type\r\n\r\n";
						$data_list .= "".$content_file."\r\n";
						$data_list .="--$boundary--\r\n";
					}
					$header .= "Content-length: " . strlen($data_list) . "\r\n\r\n";
					//print $header ;
					//print $get_list ;
					// Send to the URL/
					$fp = fsockopen($server, 80);
					fputs($fp, $header.$data_list);
					$response = '' ;
					while (!feof($fp))
						$response.=fgets($fp, 8192);
					// Cleanning now
					$response=split("\r\n\r\n",$response);
					$header=$response[0];
					$responsecontent=$response[1];
					if(!(strpos($header,"Transfer-Encoding: chunked")===false))
					{
						$aux=split("\r\n",$responsecontent);
						for($i=0;$i<count($aux);$i++)
						if($i==0 || ($i%2==0))
						$aux[$i]="";
						$responsecontent=implode("",$aux);
					}//if
					return chop($responsecontent);
				}
			}
			function remove_last_trailing_slash($url)
			{
				$result = $url ;
				// $result = preg_replace('@/|\\$@', '', $result) ;
				return $result ;
			}
			function make_abs_url($url, $base, $relative_to='.')
			{
				$result = $url ;
				$base = remove_last_trailing_slash($base) ;
				$relative_to = remove_last_trailing_slash($relative_to) ;
				if(is_abs_url($result))
				{
					return $result ;
				}
				if(! file_exists($result))
				{
					return '' ;
				}
				$result = remove_last_trailing_slash($result) ;
				if(is_abs_url($base))
				{
					$result = $base.'/'.$result ;
				}
				else
				{
					if($relative_to == '')
					{
						$result = $base.'/'.$result ;
					}
					else
					{
						$result = $base.'/'.$relative_to.'/'.$result ;
					}
				}
				return $result ;
			}
			function is_abs_url($url)
			{
				$ok = 0 ;
				if(preg_match('/^[a-z]+\:/i', $url))
				{
					$ok = 1 ;
				}		
				return $ok ;
			}
			if(! function_exists('is_same_url'))
			{
				function is_same_url($urlLeft, $urlRight)
				{
					if($urlLeft == $urlRight)
						return true ;
					$urlPartsLeft = @parse_url($urlLeft) ;
					$urlPartsRight = @parse_url($urlRight) ;
					if($urlPartsLeft == false or $urlPartsRight == false)
					{
						return false ;
					}
					if(_value_def($urlPartsLeft, 'scheme') != _value_def($urlPartsRight, 'scheme'))
						return false ;
					if(_value_def($urlPartsLeft, 'host') != _value_def($urlPartsRight, 'host'))
						return false ;
					if(_value_def($urlPartsLeft, 'port') != _value_def($urlPartsRight, 'port'))
						return false ;
					if(_value_def($urlPartsLeft, 'user') != _value_def($urlPartsRight, 'user'))
						return false ;
					if(_value_def($urlPartsLeft, 'pass') != _value_def($urlPartsRight, 'pass'))
						return false ;
					if(_value_def($urlPartsLeft, 'path') != _value_def($urlPartsRight, 'path'))
						return false ;
					$queryLeftVal = _value_def($urlPartsLeft, 'query') ;
					$queryRightVal = _value_def($urlPartsRight, 'query') ;
					if($queryLeftVal == $queryRightVal)
						return true ;
					@parse_str($queryLeftVal, $queryLeft) ;
					@parse_str($queryRightVal, $queryRight) ;
					if(! is_array($queryLeft) || ! is_array($queryRight))
						return false ;
					// print_r($queryLeft) ;
					return (count(array_diff($queryLeft, $queryRight)) == 0) ;
				}
			}
			if(! function_exists('loadURLData'))
			{	
				function loadURLData($url,$options=array()) {
					$default_options = array(
						'method'        => 'get',
						'post_data'     => false,
						'return_info'   => false,
						'return_body'   => true,
						'cache'         => false,
						'referer'       => '',
						'headers'      	=> array(),
						'session'       => false,
						'session_close' => false,
						'connect_timeout' => 10,
						'read_timeout' => 10,
					);
					// Sets the default options.
					foreach($default_options as $opt=>$value) {
						if(!isset($options[$opt])) $options[$opt] = $value;
					}
					$url_parts = parse_url($url);
					$query_string = ((isset($url_parts["query"]))) ? $url_parts["query"] : "" ;
					$ch = false;
					$info = array(//Currently only supported by curl.
						'http_code'    => 200
					);
					$response = '';

					$send_header = array(
						'Accept' => 'text/*',
						'User-Agent' => 'BinGet/1.00.A (http://www.bin-co.com/php/scripts/load/)'
					) + $options['headers']; // Add custom headers provided by the user.

					if($options['cache']) {
						$cache_folder = joinPath(sys_get_temp_dir(), 'php-load-function');
						if(isset($options['cache_folder'])) $cache_folder = $options['cache_folder'];
						if(!file_exists($cache_folder)) {
							$old_umask = umask(0); // Or the folder will not get write permission for everybody.
							mkdir($cache_folder, 0777);
							umask($old_umask);
						}
						
						$cache_file_name = md5($url) . '.cache';
						$cache_file = joinPath($cache_folder, $cache_file_name); //Don't change the variable name - used at the end of the function.
						
						if(file_exists($cache_file)) { // Cached file exists - return that.
							$response = file_get_contents($cache_file);
							
							//Seperate header and content
							$separator_position = strpos($response,"\r\n\r\n");
							$header_text = substr($response,0,$separator_position);
							$body = substr($response,$separator_position+4);
							
							foreach(explode("\n",$header_text) as $line) {
								$parts = explode(": ",$line);
								if(count($parts) == 2) $headers[$parts[0]] = chop($parts[1]);
							}
							$headers['cached'] = true;
							
							if(!$options['return_info']) return $body;
							else return array('headers' => $headers, 'body' => $body, 'info' => array('cached'=>true));
						}
					}

					if(isset($options['post_data'])) { //There is an option to specify some data to be posted.
						$options['method'] = 'post';
						
						if(is_array($options['post_data'])) { //The data is in array format.
							$post_data = array();
							foreach($options['post_data'] as $key=>$value) {
								$post_data[] = "$key=" . urlencode($value);
							}
							$url_parts['query'] = implode('&', $post_data);
						} else { //Its a string
							$url_parts['query'] = $options['post_data'];
						}
					} elseif(isset($options['multipart_data'])) { //There is an option to specify some data to be posted.
						$options['method'] = 'post';
						$url_parts['query'] = $options['multipart_data'];
						/*
							This array consists of a name-indexed set of options.
							For example,
							'name' => array('option' => value)
							Available options are:
							filename: the name to report when uploading a file.
							type: the mime type of the file being uploaded (not used with curl).
							binary: a flag to tell the other end that the file is being uploaded in binary mode (not used with curl).
							contents: the file contents. More efficient for fsockopen if you already have the file contents.
							fromfile: the file to upload. More efficient for curl if you don't have the file contents.

							Note the name of the file specified with fromfile overrides filename when using curl.
						 */
					}

					///////////////////////////// Curl /////////////////////////////////////
					//If curl is available, use curl to get the data.
					if(function_exists("curl_init") and (! (isset($options['use']) and $options['use'] == 'fsocketopen'))) { //Don't use curl if it is specifically stated to use fsocketopen in the options
						
						if(isset($options['post_data'])) { //There is an option to specify some data to be posted.
							$page = $url;
							$options['method'] = 'post';
							
							if(is_array($options['post_data'])) { //The data is in array format.
								$post_data = array();
								foreach($options['post_data'] as $key=>$value) {
									$post_data[] = "$key=" . urlencode($value);
								}
								$url_parts['query'] = implode('&', $post_data);
							
							} else { //Its a string
								$url_parts['query'] = $options['post_data'];
							}
						} else {
							if(isset($options['method']) and $options['method'] == 'post') {
								$page = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
							} else {
								$page = $url;
							}
						}

						if($options['session'] and isset($GLOBALS['_binget_curl_session'])) $ch = $GLOBALS['_binget_curl_session']; //Session is stored in a global variable
						else $ch = curl_init($url_parts['host']);
						
						curl_setopt($ch, CURLOPT_URL, $page) or die("Invalid cURL Handle Resouce");
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Just return the data - not print the whole thing.
						curl_setopt($ch, CURLOPT_HEADER, true); //We need the headers
						curl_setopt($ch, CURLOPT_NOBODY, !($options['return_body'])); //The content - if true, will not download the contents. There is a ! operation - don't remove it.
						$tmpdir = NULL; //This acts as a flag for us to clean up temp files
						if(isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query'])) {
							curl_setopt($ch, CURLOPT_POST, true);
							if(is_array($url_parts['query'])) {
								//multipart form data (eg. file upload)
								$postdata = array();
								foreach ($url_parts['query'] as $name => $data) {
									if (isset($data['contents']) && isset($data['filename'])) {
										if (!isset($tmpdir)) { //If the temporary folder is not specifed - and we want to upload a file, create a temp folder.
											//  :TODO:
											$dir = sys_get_temp_dir();
											$prefix = 'load';
											
											if (substr($dir, -1) != '/') $dir .= '/';
											do {
												$path = $dir . $prefix . mt_rand(0, 9999999);
											} while (!mkdir($path, $mode));
										
											$tmpdir = $path;
										}
										$tmpfile = $tmpdir.'/'.$data['filename'];
										file_put_contents($tmpfile, $data['contents']);
										$data['fromfile'] = $tmpfile;
									}
									if (isset($data['fromfile'])) {
										// Not sure how to pass mime type and/or the 'use binary' flag
										$postdata[$name] = '@'.$data['fromfile'];
									} elseif (isset($data['contents'])) {
										$postdata[$name] = $data['contents'];
									} else {
										$postdata[$name] = '';
									}
								}
								curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
							} else {
								curl_setopt($ch, CURLOPT_POSTFIELDS, $url_parts['query']);
							}
						}

						//Set the headers our spiders sends
						curl_setopt($ch, CURLOPT_USERAGENT, $send_header['User-Agent']); //The Name of the UserAgent we will be using ;)
						$custom_headers = array("Accept: " . $send_header['Accept'] );
						if(isset($options['modified_since']))
							array_push($custom_headers,"If-Modified-Since: ".gmdate('D, d M Y H:i:s \G\M\T',strtotime($options['modified_since'])));
						curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);
						if($options['referer']) curl_setopt($ch, CURLOPT_REFERER, $options['referer']);

						curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/binget-cookie.txt"); //If ever needed...
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

						$custom_headers = array();
						unset($send_header['User-Agent']); // Already done (above)
						foreach ($send_header as $name => $value) {
							if (is_array($value)) {
								foreach ($value as $item) {
									$custom_headers[] = "$name: $item";
								}
							} else {
								$custom_headers[] = "$name: $value";
							}
						}
						if(isset($url_parts['user']) and isset($url_parts['pass'])) {
							$custom_headers[] = "Authorization: Basic ".base64_encode($url_parts['user'].':'.$url_parts['pass']);
						}
						curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);

						$response = curl_exec($ch);

						if(isset($tmpdir)) {
							//rmdirr($tmpdir); //Cleanup any temporary files :TODO:
						}

						$info = curl_getinfo($ch); //Some information on the fetch
						
						if($options['session'] and !$options['session_close']) $GLOBALS['_binget_curl_session'] = $ch; //Dont close the curl session. We may need it later - save it to a global variable
						else curl_close($ch);  //If the session option is not set, close the session.

					//////////////////////////////////////////// FSockOpen //////////////////////////////
					} else { //If there is no curl, use fsocketopen - but keep in mind that most advanced features will be lost with this approch.

						if(!isset($url_parts['query']) || (isset($options['method']) and $options['method'] == 'post'))
							$page = $url_parts['path'] . '?' . $query_string ;
						else
							$page = $url_parts['path'] . '?' . $url_parts['query'];
						
						if(!isset($url_parts['port'])) $url_parts['port'] = ($url_parts['scheme'] == 'https' ? 443 : 80);
						$host = ($url_parts['scheme'] == 'https' ? 'ssl://' : '').$url_parts['host'];
						$fp = @fsockopen($host, $url_parts['port'], $errno, $errstr, $options["connect_timeout"]);
						if ($fp) {
							$out = '';
							if(isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query'])) {
								$out .= "POST $page HTTP/1.1\r\n";
							} else {
								$out .= "GET $page HTTP/1.0\r\n"; //HTTP/1.0 is much easier to handle than HTTP/1.1
							}
							$out .= "Host: $url_parts[host]\r\n";
						foreach ($send_header as $name => $value) {
						if (is_array($value)) {
							foreach ($value as $item) {
							$out .= "$name: $item\r\n";
							}
						} else {
							$out .= "$name: $value\r\n";
						}
						}
							$out .= "Connection: Close\r\n";
							
							//HTTP Basic Authorization support
							if(isset($url_parts['user']) and isset($url_parts['pass'])) {
								$out .= "Authorization: Basic ".base64_encode($url_parts['user'].':'.$url_parts['pass']) . "\r\n";
							}

							//If the request is post - pass the data in a special way.
							if(isset($options['method']) and $options['method'] == 'post') {
								if(is_array($url_parts['query'])) {
									//multipart form data (eg. file upload)

									// Make a random (hopefully unique) identifier for the boundary
									srand((double)microtime()*1000000);
									$boundary = "---------------------------".substr(md5(rand(0,32000)),0,10);

									$postdata = array();
									$postdata[] = '--'.$boundary;
									foreach ($url_parts['query'] as $name => $data) {
										$disposition = 'Content-Disposition: form-data; name="'.$name.'"';
										if (isset($data['filename'])) {
											$disposition .= '; filename="'.$data['filename'].'"';
										}
										$postdata[] = $disposition;
										if (isset($data['type'])) {
											$postdata[] = 'Content-Type: '.$data['type'];
										}
										if (isset($data['binary']) && $data['binary']) {
											$postdata[] = 'Content-Transfer-Encoding: binary';
										} else {
											$postdata[] = '';
										}
										if (isset($data['fromfile'])) {
											$data['contents'] = file_get_contents($data['fromfile']);
										}
										if (isset($data['contents'])) {
											$postdata[] = $data['contents'];
										} else {
											$postdata[] = '';
										}
										$postdata[] = '--'.$boundary;
									}
									$postdata = implode("\r\n", $postdata)."\r\n";
									$length = strlen($postdata);
									$postdata = 'Content-Type: multipart/form-data; boundary='.$boundary."\r\n".
												'Content-Length: '.$length."\r\n".
												"\r\n".
												$postdata;

									$out .= $postdata;
								} else {
									if(! isset($options['headers']['Content-Type']))
										$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
									if(! isset($options['headers']['Content-Length']))
										$out .= 'Content-Length: ' . strlen($url_parts['query']) . "\r\n";
									$out .= "\r\n" . $url_parts['query'];
								}
							}
							$out .= "\r\n";
							fwrite($fp, $out);
							while (!feof($fp)) {
								stream_set_timeout($fp, $options["read_timeout"]) ;
								$response .= @fgets($fp, 1024);
							}
							fclose($fp);
						}
					}

					//Get the headers in an associative array
					$headers = array();

					if($info['http_code'] == 404) {
						$body = "";
						$headers['Status'] = 404;
					} else {
						//Seperate header and content
						if(! isset($info['header_size']))
						{
							$info['header_size'] = strpos($response, "\r\n\r\n") ;
						}
						$header_text = substr($response, 0, $info['header_size']);
						$body = substr($response, $info['header_size']);
						
						foreach(explode("\n",$header_text) as $line) {
							$parts = explode(": ",$line);
							if(count($parts) == 2) {
								if (isset($headers[$parts[0]])) {
									if (is_array($headers[$parts[0]])) $headers[$parts[0]][] = chop($parts[1]);
									else $headers[$parts[0]] = array($headers[$parts[0]], chop($parts[1]));
								} else {
									$headers[$parts[0]] = chop($parts[1]);
								}
							}
						}

					}

					if(isset($cache_file)) { //Should we cache the URL?
						file_put_contents($cache_file, $response);
					}

					if($options['return_info']) return array('headers' => $headers, 'body' => $body, 'info' => $info, 'curl_handle'=>$ch);
					return $body;
				}
			}
			
			// get remote file last modification date (returns unix timestamp)
			function GetRemoteLastModified( $uri )
			{
				return 0 ;
					// default
					$unixtime = 0;
					
					$fp = @fopen( $uri, "r" );
					if( !$fp ) {return 0;}
					
					$MetaData = stream_get_meta_data( $fp );
							
					foreach( $MetaData['wrapper_data'] as $response )
					{
							// case: redirection
							if( substr( strtolower($response), 0, 10 ) == 'location: ' )
							{
									$newUri = substr( $response, 10 );
									fclose( $fp );
									return GetRemoteLastModified( $newUri );
							}
							// case: last-modified
							elseif( substr( strtolower($response), 0, 15 ) == 'last-modified: ' )
							{
									$unixtime = strtotime( substr($response, 15) );
									break;
							}
					}
					fclose( $fp );
					return $unixtime;
			}
			function force_array(& $data)
			{
				if(! is_array($data))
				{
					$temp = $data ;
					if(is_object($temp))
					{
						$data = get_object_vars($temp) ;
					}
					elseif($temp === null)
					{
						$data = array() ;
					}
					else
					{
						$data = array($temp) ;
					}
				}
			}
			function force_array_rec(& $data)
			{
				force_array($data) ;
				foreach($data as $n => &$val)
				{
					if(is_object($val) || is_array($val))
					{
						force_array_rec($val) ;
					}
				}
			}
			function force_object(& $data)
			{
				$result = new StdClass ;
				if(is_array($result))
				{
					$result = conv_array_to_object($data) ;
				}
				return $result ;
			}
			function & conv_array_to_object(& $data)
			{
				$result = new StdClass ;
				if(! is_array($data))
				{
					return $result ;
				}
				foreach($data as $n => $v)
				{
					$result->$n = $v ;
				}
				return $result ;
			}
			function check_url($url)
			{
				$url = parse_url($url);
				if ( ! $url) {
					return false;
				}
				$url = array_map('trim', $url);
				$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
				$path = (isset($url['path'])) ? $url['path'] : '';
				if ($path == '')
				{
					$path = '/';
				}
				$path .= ( isset ( $url['query'] ) ) ? "?$url[query]" : '';
				if ( isset ( $url['host'] ) )
				{
					// _d_step($url, __LINE__, __FILE__) ;
					if ( PHP_VERSION >= 5 )
					{
						$headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
					}
					else
					{
						$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
						if ( ! $fp )
						{
							return false;
						}
						fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
						$headers = fread ( $fp, 128 );
						fclose ( $fp );
					}
					$headers = ( is_array ( $headers ) ) ? implode ( "\n", $headers ) : $headers;
					return ( bool ) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers );
				}
				return true;
			}
			
			function extract_element_value_from_source_code($name, $html, $default_value=null)
			{
				$value = null ;
				while(eregi('<input[^>]+name=("|\')'.$name.'("|\')[^>]*>', $html, $match) or eregi('<input[^>]+name=("|\')'.$name.'\[\]("|\')[^>]*>', $html, $match))
				{
					$element = $match[0] ;
					$current_value = null ;
					if(preg_match('/value="([^"]+)"/i', $element, $sub_match))
					{
						$current_value = $sub_match[1] ;
					}
					elseif(eregi('value=\'([^\']+)\'', $element, $sub_match))
					{
						$current_value = $sub_match[1] ;
					}
					if(eregi('type=("|\')checkbox|radio("|\')', $element))
					{
						if(! ereg('["\'[:space:]]checked', $element))
						{
							$current_value = null ;
						}
					}
					if($current_value)
					{
						if(! is_string($value))
						{
							if(! is_array($value))
							{
								$value = $current_value ;
							}
							else
							{
								$value[] = $current_value ;
							}
						}
						else
						{
							$value = array($value) ;
							$value[] = $current_value ;
						}
					}
					$html = str_replace($match[0], '', $html) ;
				}
				if(eregi('<select[^>]+name=["\']'.$name.'["\'][^>]*>((.|\n)*)</select>', $html, $match))
				{
					$options_str = ereg_replace('</select>(.+|\n)*$', '</select>', $match[1]) ;
					$i = 0 ;
					while(eregi('<option[^>+]value="([^"]+)"[^>]*>', $options_str, $sub_match))
					{
						$options_str = str_replace($sub_match[0], '', $options_str) ;
						if(eregi('["\'[:space:]]selected["\'[:space:]>]', $sub_match[0]) or $i == 0)
						{
							$value = $sub_match[1] ;
						}
						$i++ ;
					}
					while(eregi('<option[^>+]value=\'([^\']+)\'[^>]*>', $options_str, $sub_match))
					{
						$options_str = str_replace($sub_match[0], '', $options_str) ;
						if(eregi('["\'[:space:]]selected["\'[:space:]>]', $sub_match[0]) or $i == 0)
						{
							$value = $sub_match[1] ;
						}
						$i++ ;
					}
					while(eregi('<option[^>+]value=([^\']+)[[:space:]]?[^>]*>', $options_str, $sub_match))
					{
						$options_str = str_replace($sub_match[0], '', $options_str) ;
						if(eregi('["\'[:space:]]selected["\'[:space:]>]', $sub_match[0]) or $i == 0)
						{
							$value = $sub_match[1] ;
						}
						$i++ ;
					}
					// $value = $options_str ;
				}
				if($value == null)
				{
					$value = $default_value ;
				}
				return $value ;
			}
			function extract_div_content_from_source_code($id, $html, $default_value=null)
			{
				$value = $default_value ;
				if(ereg("<div id='".$id."'>([^>]+)</div>", $html, $match))
				{
					$value = $match[0] ;
				}
				return $value ;
			}
			function extract_links_from_content($ctn)
			{
				$result = $ctn ;
				$links = array() ;
				$attrs = array('href', 'src') ;
				$escs = array('"', "'") ;
				foreach($attrs as $i => $attr)
				{
					foreach($escs as $j => $esc)
					{
						while(eregi(''.$attr.'='.$esc.'([^'.$esc.']+)'.$esc.'', $result, $matches))
						{
							if(! in_array($matches[1], $links))
							{
								$links[] = $matches[1] ;
							}
							$result = str_replace($matches[0], '', $result) ;
						}
					}
				}
				return $links ;
			}
			function _html_value($text)
			{
				$result = protect_quote($text) ;
				$result = str_replace("'", "&#39;", $result) ;
				$result = str_replace("é", "&eacute;", $result) ;
				return $result ;
			}
			function protect_quote($text)
			{
				return str_replace("\"", "&quot;", $text) ;
			}
			
			if(! function_exists('var_to_file'))
			{
				// Var to file Functions
				function var_to_file($var_value, $file_path, $var_name='data')
				{
					if(file_exists($file_path))
					{
						if(is_writable($file_path))
						{
							unlink($file_path) ;
						}
					}
					$fh = fopen($file_path, 'w') ;
					fputs($fh, '<?php'."\r\n") ;
					fputs($fh, '$'.$var_name.' = '.var_export($var_value, true).' ;'."\r\n") ;
					fputs($fh, '?>') ;
					fclose($fh) ;
					chmod($file_path, 0777) ;
				}
				function file_to_var($file_path, $var_name='data', $default_value=null)
				{
					if(! file_exists($file_path))
					{
						return $default_value ;
					}
					include $file_path ;
					$value = $default_value ;
					eval('if(isset($'.$var_name.')){ $value = $'.$var_name.' ; }') ;
					return $value ;
				}
				// GZ Var to file Functions
				function gzvar_to_file($var_value, $file_path, $var_name='data', $compress="9")
				{
					if(file_exists($file_path))
					{
						if(is_writable($file_path))
						{
							unlink($file_path) ;
						}
					}
					$fh = gzopen($file_path, 'w'.$compress) ;
					gzputs($fh, '<?php'."\r\n") ;
					gzputs($fh, '$'.$var_name.' = '.var_export($var_value, true).' ;'."\r\n") ;
					gzputs($fh, '?>') ;
					gzclose($fh) ;
					chmod($file_path, 0777) ;
				}
				function gzfile_to_var($file_path, $var_name='data', $default_value=null)
				{
					if(! file_exists($file_path))
					{
						return $default_value ;
					}
					$fh = gzopen($file_path, "r") ;
					$content = "" ;
					while (! gzeof($fh))
					{
						$content .= gzgets($fh, 4096);
					}
					gzclose($fh);
					eval('?>'.$content.'<?php'."\n") ;
					$value = $default_value ;
					eval('if(isset($'.$var_name.')){ $value = $'.$var_name.' ; }') ;
					return $value ;
				}
			}
			if(! function_exists('array_fill_keys'))
			{
				function array_fill_keys($target, $value = '')
				{
					if(is_array($target)) {
						foreach($target as $key => $val) {
							$filledArray[$val] = is_array($value) ? $value[$key] : $value;
						}
					}
					return $filledArray;
				}
			}
			
			/* Graph Functions */
			function createimagegd(& $image, $width, $height)
			{
				if(! $image)
				{
					$image = imagecreatetruecolor($width, $height) ;
				}
				return $image ;
			}
			function show_pie_graph_3d($values, $titles, $colours=array(), $settings=array())
			{
				$image = draw_pie_graph_3d($image=null, $values, $titles, $colours, $settings) ;
				// Affichage de l'image
				header('Content-type: image/png');
				imagepng($image);
				imagedestroy($image);
			}
			function draw_pie_graph_3d(& $image, $values, $titles, $settings=array(), $colours=array())
			{
				if(! function_exists('gd_info'))
				{
					return ;
				}
				// Création de l'image
				$width = (isset($settings['width'])) ? $settings['width'] : 450 ;
				$height = (isset($settings['height'])) ? $settings['height'] : 450 ;
				createimagegd($image, $width, $height) ;
				// Allocation de quelques couleurs
				$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
				$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
				// Empty color
				$empty_color_clean = imagecolorallocate($image, 0xCC, 0xCC, 0xCC);
				$empty_color_dark = imagecolorallocate($image, 0xDC, 0xDC, 0xDC);		
				// Arrière plan
				imagefilledrectangle($image, 0, 0, $width, $height, $white) ;
				if(! count($colours))
				{
					$colours = array(
						array(238, 140, 81),
						array(0x00, 0x00, 0x80),
						array(0xC0, 0xC0, 0xC0),
						array(0xFF, 0x55, 0x00),
					) ;
				}
				$colour_index = array() ;			
				// Création de l'effet 3D
				$vertical_delim = intval($height / 16) ;
				$top_delim = $height * 3 / 8 ;
				$left_delim = $width / 2 ;
				$bottom_egde = $top_delim + $vertical_delim ;
				$top_egde = $top_delim - $vertical_delim ;
				$left_egde = $left_delim ;
				$arc_width = $width ;
				$arc_height = $height * 4 / 8 ;
				// Ecrivons la légende
				$top_space = 20 ;
				$left_space = 20 ;
				$row_end_height = 6 ;
				$col_end_width = 6 ;
				$font_index = 5 ;		
				foreach($colours as $i => $colour)
				{
					eval('$color_index[$i]["clean"] = imagecolorallocate($image, '.$colour[0].', '.$colour[1].', '.$colour[2].') ;'."\r\n") ;
					eval('$color_index[$i]["dark"] = imagecolorallocate($image, '.$colour[0].' + 0x11, '.$colour[1].' + 0x11, '.$colour[2].' + 0x11) ;'."\r\n") ;
				}
				$total = 0 ;
				foreach($values as $i => $value)
				{
					if(! isset($titles[$i]))
					{
						$titles[$i] = $i + 1 ;
					}
					$total += $value ;
				}
				// Calculate the angles
				$angles = array() ;
				$last_angle = 0 ;
				
				if($total)
				{
					foreach($values as $i => $value)
					{
						$angles[$i][0] = ($value * 360 / $total) ;
						$angles[$i][1] = $last_angle ;
						$angles[$i][2] = $last_angle + $angles[$i][0] ;
						$last_angle += $angles[$i][0] ;
					}			
					// Beginning
					for ($i = $bottom_egde; $i > $top_egde; $i--) {
						foreach($angles as $j => $angle)
						{
							eval('imagefilledarc($image, $left_egde, $i, $arc_width, $arc_height, $angles[$j][1], $angles[$j][2],  $color_index[$j]["dark"], IMG_ARC_PIE) ;'."\n");
						}
					}
					foreach($angles as $j => $angle)
					{
						eval('imagefilledarc($image, $left_egde, $top_egde, $arc_width, $arc_height, $angles[$j][1], $angles[$j][2], $color_index[$j]["clean"], IMG_ARC_PIE) ;'."\n");
					}
					imagefilledrectangle($image, 0, $height - (count($values)) * ($top_space + $row_end_height), $width, $height, $white) ;
					foreach($titles as $j => $title)
					{
						$x = $width / 6 ;
						$y = $height - ($j + 2) * ($top_space + $row_end_height) ;
						eval('imagefilledrectangle($image, $x, $y, $x + $left_space, $y + $top_space, $color_index[$j]["clean"]) ;'."\n");
						eval('imagestring($image, $font_index, $x + $left_space + $col_end_width, $y, $titles[$j], $color_index[$j]["dark"]) ;'."\n") ;
					}
				}
				else
				{
					for ($i = $bottom_egde; $i > $top_egde; $i--)
					{
						eval('imagefilledarc($image, $left_egde, $i, $arc_width, $arc_height, 0, 360,  $empty_color_clean, IMG_ARC_PIE) ;'."\n");
					}
					eval('imagefilledarc($image, $left_egde, $top_egde, $arc_width, $arc_height, 0, 360, $empty_color_dark, IMG_ARC_PIE) ;'."\n");
				}
				return $image ;
			}
			// Draw the 2 axis references
			function draw_graph_2axis_reference(& $image, $values=array(), $title_values=array(), $settings=array())
			{
				// Set the settings
				$text_width = (isset($settings['text_width'])) ? $settings['text_width'] : 40 ;
				$text_height = (isset($settings['text_height'])) ? $settings['text_height'] : 20 ;
				$text_font_index = (isset($settings['text_font_index'])) ? $settings['text_font_index'] : 4 ;
				$real_width = (isset($settings['width'])) ? $settings['width'] : 450 ;
				$real_height = (isset($settings['height'])) ? $settings['height'] : 300 ;
				$width = $real_width - $text_width * 2 ;
				$height = $real_height - $text_height * 2 ;
				$x_range = (isset($settings['x_range'])) ? $settings['x_range'] : array(0, $width) ;
				$y_range = (isset($settings['y_range'])) ? $settings['y_range'] : array(0, $height) ;
				$x_range_title = (isset($settings['x_range_title'])) ? $settings['x_range_title'] : $x_range ;
				$y_range_title = (isset($settings['y_range_title'])) ? $settings['y_range_title'] : $y_range ;
				$x_range_count = (isset($settings['range_count'])) ? $settings['range_count'][0] : 10 ;
				$y_range_count = (isset($settings['range_count'])) ? $settings['range_count'][1] : 10 ;
				$draw_titles = (isset($settings['draw_titles'])) ? $settings['draw_titles'] : 1 ;
				$draw_axis_points = (isset($settings['draw_axis_points'])) ? $settings['draw_axis_points'] : 1 ;
				$draw_axis_point_titles = (isset($settings['draw_axis_point_titles'])) ? $settings['draw_axis_point_titles'] : 1 ;
				$draw_axis_values = (isset($settings['draw_axis_values'])) ? $settings['draw_axis_values'] : 1 ;
				$draw_axis_value_titles = (isset($settings['draw_axis_value_titles'])) ? $settings['draw_axis_value_titles'] : 0 ;
				// Set the X units
				$x_part_separator_title = ($x_range_title[1] - $x_range_title[0]) / $x_range_count ;
				$x_indent = ($x_range[1] - $x_range[0]) ;
				$x_part_separator = $x_indent / $x_range_count ;
				if($x_part_separator < 5)
				{
					$x_range_count = 10 ;
					$x_part_separator = $x_indent / $x_range_count ;
				}
				// Set the Y units
				$y_part_separator_title = ($y_range_title[1] - $y_range_title[0]) / $y_range_count ;
				$y_indent = ($y_range[1] - $y_range[0]) ;
				$y_part_separator = $y_indent / $y_range_count ;
				if($y_part_separator < 5)
				{
					$y_range_count = 10 ;
					$y_part_separator = $y_indent / $y_range_count ;
				}
				// Create the image if necessary
				createimagegd($image, $real_width, $real_height) ;
				// Allocation de quelques couleurs
				$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
				$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
				$grey = imagecolorallocate($image, 0xC5, 0xC5, 0xC5);
				$axis_color = imagecolorallocate($image, 0x00, 0x00, 0x00);
				$value_color = imagecolorallocate($image, 0xFF, 0x00, 0x99);
				$bg_color = $white ;
				// Background of the image
				imagefilledrectangle($image, 0, 0, $real_width, $real_height, $bg_color) ;
				// Horizontal Axis 
				imagefilledrectangle($image, $x_range[0] + $text_width, $y_range[1] - 1, $x_range[1] + $text_width, $y_range[1], $axis_color) ;
				// vertical Axis
				imagefilledrectangle($image, $x_range[0] + $text_width - 1, $y_range[0], $x_range[0] + $text_width, $y_range[1], $axis_color) ;
				// Draw the X axis
				for($i = 0; $i <= $x_range_count; $i ++)
				{
					if(! isset($title_values[$i]))
					{
						$title_values[$i] = $x_range_title[0] + ($i * $x_part_separator_title) ;
					}
					$x = $x_range[0] + $text_width + ($x_part_separator * $i) ;
					$y = $y_range[1] ;
					$text = $title_values[$i] ;
					if($draw_axis_points)
					{
						imagefilledrectangle($image, $x - 2, $y - 2, $x + 2, $y + 2, $axis_color) ;
					}
					if($draw_axis_point_titles)
					{
						imagestring($image, $text_font_index, $x, $y + 5, "$text", $axis_color) ;
					}
				}
				// Draw the Y units
				$max_length = strlen(strval($y_range_title[1])) + 1 ;
				for($i = $y_range_count; $i >= 0; $i --)
				{
					$x = $x_range[0] ;
					$y = $y_range[1] - ($y_part_separator * $i) ;
					$text = ($y_range_title[0] + ($i * $y_part_separator_title)) ;
					if($draw_axis_points)
					{
						imagefilledrectangle($image, $x - 2 + $text_width, $y - 2, $x + 2 + $text_width, $y + 2, $axis_color) ;
					}
					if($draw_axis_point_titles)
					{
						$text_length = strlen(strval($text)) ;
						$text = str_repeat(" ", $max_length - $text_length).$text ;
						imagestring($image, $text_font_index, $x + 5, $y - 2, "$text", $axis_color) ;
					}
				}
				imagecolordeallocate($image, $white);
				imagecolordeallocate($image, $black);
				imagecolordeallocate($image, $grey);
				imagecolordeallocate($image, $axis_color);
				imagecolordeallocate($image, $value_color);
			}
			// Draw a Curve Graph
			function draw_graph_curve(& $image, $values=array(), $title_values=array(), $settings=array())
			{
				// Set the settings
				$text_width = (isset($settings['text_width'])) ? $settings['text_width'] : 40 ;
				$text_height = (isset($settings['text_height'])) ? $settings['text_height'] : 20 ;
				$text_font_index = (isset($settings['text_font_index'])) ? $settings['text_font_index'] : 4 ;
				$real_width = (isset($settings['width'])) ? $settings['width'] : 450 ;
				$real_height = (isset($settings['height'])) ? $settings['height'] : 300 ;
				$width = $real_width - $text_width * 2 ;
				$height = $real_height - $text_height * 2 ;
				$x_range = (isset($settings['x_range'])) ? $settings['x_range'] : array(0, $width) ;
				$y_range = (isset($settings['y_range'])) ? $settings['y_range'] : array(0, $height) ;
				$x_range_title = (isset($settings['x_range_title'])) ? $settings['x_range_title'] : $x_range ;
				$y_range_title = (isset($settings['y_range_title'])) ? $settings['y_range_title'] : $y_range ;
				$x_range_count = (isset($settings['range_count'])) ? $settings['range_count'][0] : 10 ;
				$y_range_count = (isset($settings['range_count'])) ? $settings['range_count'][1] : 10 ;
				$draw_titles = (isset($settings['draw_titles'])) ? $settings['draw_titles'] : 1 ;
				$draw_axis_points = (isset($settings['draw_axis_points'])) ? $settings['draw_axis_points'] : 1 ;
				$draw_axis_point_titles = (isset($settings['draw_axis_point_titles'])) ? $settings['draw_axis_point_titles'] : 1 ;
				$draw_axis_values = (isset($settings['draw_axis_values'])) ? $settings['draw_axis_values'] : 1 ;
				$draw_axis_value_titles = (isset($settings['draw_axis_value_titles'])) ? $settings['draw_axis_value_titles'] : 0 ;
				// Create the image if necessary
				createimagegd($image, $real_width, $real_height) ;
				// Allocation de quelques couleurs
				$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
				$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
				$grey = imagecolorallocate($image, 0xC5, 0xC5, 0xC5);
				$value_color = imagecolorallocate($image, 0xFF, 0x00, 0x99);
				$bg_color = $white ;
				// Set the X units
				$x_part_separator_title = ($x_range_title[1] - $x_range_title[0]) / $x_range_count ;
				$x_indent = ($x_range[1] - $x_range[0]) ;
				$x_part_separator = $x_indent / $x_range_count ;
				if($x_part_separator < 5)
				{
					$x_range_count = 10 ;
					$x_part_separator = $x_indent / $x_range_count ;
				}
				// Set the Y units
				$y_part_separator_title = ($y_range_title[1] - $y_range_title[0]) / $y_range_count ;
				$y_indent = ($y_range[1] - $y_range[0]) ;
				$y_part_separator = $y_indent / $y_range_count ;
				if($y_part_separator < 5)
				{
					$y_range_count = 10 ;
					$y_part_separator = $y_indent / $y_range_count ;
				}
				// Drawing the values Now
				for($i = 0; $i <= $x_range_count; $i ++)
				{
					if(! isset($values[$i]))
					{
						$values[$i] = 0 ;
					}
					$x = $x_range[0] + $text_width + ($x_part_separator * $i) ;
					$y = intval($y_range[1] - ($y_range[1] * ($values[$i] - $y_range_title[0]) / ($y_range_title[1] - $y_range_title[0]))) ;
					if($draw_axis_values)
					{
						imagefilledrectangle($image, $x - 2, $y - 2, $x + 2, $y + 2, $value_color) ;
					}
					if($draw_axis_value_titles)
					{
						imagestring($image, $text_font_index, $x_range[0] + 5, $y, $values[$i], $value_color) ;
					}
					if($i > 0)
					{
						$prev_x = $x_range[0] + $text_width + ($x_part_separator * ($i - 1)) ;
						$prev_y = intval($y_range[1] - ($y_range[1] * ($values[$i - 1] - $y_range_title[0]) / ($y_range_title[1] - $y_range_title[0]))) ;
						// print $prev_y.' '.$y.'<br />' ;
						imageline($image, $prev_x, $prev_y, $x, $y, $value_color) ;
					}
					// print $y.'<br />' ;
				}
				// exit ;
				imagecolordeallocate($image, $white);
				imagecolordeallocate($image, $black);
				imagecolordeallocate($image, $grey);
				imagecolordeallocate($image, $value_color);
			}
			// Draw a band Graph
			function draw_graph_band(& $image, $values=array(), $title_values=array(), $settings=array())
			{
				// Set the settings
				$text_width = (isset($settings['text_width'])) ? $settings['text_width'] : 40 ;
				$text_height = (isset($settings['text_height'])) ? $settings['text_height'] : 20 ;
				$text_font_index = (isset($settings['text_font_index'])) ? $settings['text_font_index'] : 4 ;
				$real_width = (isset($settings['width'])) ? $settings['width'] : 450 ;
				$real_height = (isset($settings['height'])) ? $settings['height'] : 300 ;
				$width = $real_width - $text_width * 2 ;
				$height = $real_height - $text_height * 2 ;
				$x_range = (isset($settings['x_range'])) ? $settings['x_range'] : array(0, $width) ;
				$y_range = (isset($settings['y_range'])) ? $settings['y_range'] : array(0, $height) ;
				$x_range_title = (isset($settings['x_range_title'])) ? $settings['x_range_title'] : $x_range ;
				$y_range_title = (isset($settings['y_range_title'])) ? $settings['y_range_title'] : $y_range ;
				$x_range_count = (isset($settings['range_count'])) ? $settings['range_count'][0] : 10 ;
				$y_range_count = (isset($settings['range_count'])) ? $settings['range_count'][1] : 10 ;
				$draw_titles = (isset($settings['draw_titles'])) ? $settings['draw_titles'] : 1 ;
				$draw_axis_points = (isset($settings['draw_axis_points'])) ? $settings['draw_axis_points'] : 1 ;
				$draw_axis_point_titles = (isset($settings['draw_axis_point_titles'])) ? $settings['draw_axis_point_titles'] : 1 ;
				$draw_axis_values = (isset($settings['draw_axis_values'])) ? $settings['draw_axis_values'] : 1 ;
				$draw_axis_value_titles = (isset($settings['draw_axis_value_titles'])) ? $settings['draw_axis_value_titles'] : 0 ;
				// Create the image if necessary
				createimagegd($image, $real_width, $real_height) ;
				// Allocation de quelques couleurs
				$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
				$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
				$grey = imagecolorallocate($image, 0xC5, 0xC5, 0xC5);
				$value_color = imagecolorallocate($image, 0x5a, 0x55, 0x4f);
				$bg_value_color = imagecolorallocate($image, 0xD3, 0xCC, 0xC5);
				$bg_color = $white ;
				// Set the X units
				$x_part_separator_title = ($x_range_title[1] - $x_range_title[0]) / $x_range_count ;
				$x_indent = ($x_range[1] - $x_range[0]) ;
				$x_part_separator = $x_indent / $x_range_count ;
				if($x_part_separator < 5)
				{
					$x_range_count = 10 ;
					$x_part_separator = $x_indent / $x_range_count ;
				}
				// Set the Y units
				$y_part_separator_title = ($y_range_title[1] - $y_range_title[0]) / $y_range_count ;
				$y_indent = ($y_range[1] - $y_range[0]) ;
				$y_part_separator = $y_indent / $y_range_count ;
				if($y_part_separator < 5)
				{
					$y_range_count = 10 ;
					$y_part_separator = $y_indent / $y_range_count ;
				}
				// Drawing the values Now
				for($i = 0; $i <= $x_range_count; $i ++)
				{
					if(! isset($values[$i]))
					{
						$values[$i] = 0 ;
					}
					$x = $x_range[0] + $text_width + ($x_part_separator * $i) ;
					$y = intval($y_range[1] - ($y_range[1] * ($values[$i] - $y_range_title[0]) / ($y_range_title[1] - $y_range_title[0]))) ;
					if($draw_axis_values)
					{
						// imagefilledrectangle($image, $x - 2, $y - 2, $x + 2, $y + 2, $value_color) ;
					}
					if($draw_axis_value_titles)
					{
						imagestring($image, $text_font_index, $x_range[0] + 5, $y, $values[$i], $value_color) ;
					}
					if($i > 0)
					{
						$prev_x = $x_range[0] + $text_width + ($x_part_separator * ($i - 1)) ;
						$prev_y = intval($y_range[1] - ($y_range[1] * ($values[$i - 1] - $y_range_title[0]) / ($y_range_title[1] - $y_range_title[0]))) ;
						// print $prev_y.' '.$y.'<br />' ;
						// imageline($image, $prev_x, $prev_y, $x, $y, $value_color) ;
						imagefilledrectangle($image, $prev_x + 1, $prev_y - 1, $x_range[0] + $text_width + ($x_part_separator * $i) - 1, $y_range[1] - 2, $value_color) ;
						imagefilledrectangle($image, $prev_x + 2, $prev_y, $x_range[0] + $text_width + ($x_part_separator * $i) - 2, $y_range[1] - 2, $bg_value_color) ;
					}
					// print $y.'<br />' ;
				}
				// exit ;
				imagecolordeallocate($image, $white);
				imagecolordeallocate($image, $black);
				imagecolordeallocate($image, $grey);
				imagecolordeallocate($image, $value_color);
			}
			// Draw a Stick Graph
			function draw_graph_stick(& $image, $values=array(), $title_values=array(), $settings=array())
			{
				// Set the settings
				$text_width = (isset($settings['text_width'])) ? $settings['text_width'] : 40 ;
				$text_height = (isset($settings['text_height'])) ? $settings['text_height'] : 20 ;
				$text_font_index = (isset($settings['text_font_index'])) ? $settings['text_font_index'] : 4 ;
				$real_width = (isset($settings['width'])) ? $settings['width'] : 450 ;
				$real_height = (isset($settings['height'])) ? $settings['height'] : 300 ;
				$width = $real_width - $text_width * 2 ;
				$height = $real_height - $text_height * 2 ;
				$x_range = (isset($settings['x_range'])) ? $settings['x_range'] : array(0, $width) ;
				$y_range = (isset($settings['y_range'])) ? $settings['y_range'] : array(0, $height) ;
				$stick_width = (isset($settings['stick_width'])) ? $settings['stick_width'] : 4 ;
				$x_range_title = (isset($settings['x_range_title'])) ? $settings['x_range_title'] : $x_range ;
				$y_range_title = (isset($settings['y_range_title'])) ? $settings['y_range_title'] : $y_range ;
				$x_range_count = (isset($settings['range_count'])) ? $settings['range_count'][0] : 10 ;
				$y_range_count = (isset($settings['range_count'])) ? $settings['range_count'][1] : 10 ;
				$draw_titles = (isset($settings['draw_titles'])) ? $settings['draw_titles'] : 1 ;
				$draw_axis_points = (isset($settings['draw_axis_points'])) ? $settings['draw_axis_points'] : 1 ;
				$draw_axis_point_titles = (isset($settings['draw_axis_point_titles'])) ? $settings['draw_axis_point_titles'] : 1 ;
				$draw_axis_values = (isset($settings['draw_axis_values'])) ? $settings['draw_axis_values'] : 1 ;
				$draw_axis_value_titles = (isset($settings['draw_axis_value_titles'])) ? $settings['draw_axis_value_titles'] : 0 ;
				// Create the image if necessary
				createimagegd($image, $real_width, $real_height) ;
				// Allocation de quelques couleurs
				$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
				$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
				$grey = imagecolorallocate($image, 0xC5, 0xC5, 0xC5);
				$value_color = imagecolorallocate($image, 0x5a, 0x55, 0x4f);
				$bg_value_color = imagecolorallocate($image, 0xD3, 0xCC, 0xC5);
				$bg_color = $white ;
				// Set the X units
				$x_part_separator_title = ($x_range_title[1] - $x_range_title[0]) / $x_range_count ;
				$x_indent = ($x_range[1] - $x_range[0]) ;
				$x_part_separator = $x_indent / $x_range_count ;
				if($x_part_separator < 5)
				{
					$x_range_count = 10 ;
					$x_part_separator = $x_indent / $x_range_count ;
				}
				// Set the Y units
				$y_part_separator_title = ($y_range_title[1] - $y_range_title[0]) / $y_range_count ;
				$y_indent = ($y_range[1] - $y_range[0]) ;
				$y_part_separator = $y_indent / $y_range_count ;
				if($y_part_separator < 5)
				{
					$y_range_count = 10 ;
					$y_part_separator = $y_indent / $y_range_count ;
				}
				// Drawing the values Now
				for($i = 0; $i <= $x_range_count; $i ++)
				{
					if(! isset($values[$i]))
					{
						$values[$i] = 0 ;
					}
					$x = $x_range[0] + $text_width + ($x_part_separator * $i) ;
					$y = intval($y_range[1] - ($y_range[1] * ($values[$i] - $y_range_title[0]) / ($y_range_title[1] - $y_range_title[0]))) ;
					if($draw_axis_values)
					{
						imagefilledrectangle($image, $x - $stick_width, $y, $x_range[0] + $text_width + ($x_part_separator * $i) + $stick_width, $y_range[1] - 2, $value_color) ;
					}
					if($draw_axis_value_titles)
					{
						imagestring($image, $text_font_index, $x_range[0] + 5, $y, $values[$i], $value_color) ;
					}
				}
				imagecolordeallocate($image, $white);
				imagecolordeallocate($image, $black);
				imagecolordeallocate($image, $grey);
				imagecolordeallocate($image, $value_color);
			}
			
			// Files & Directories
			function delete_simple_file($file_path)
			{
				if(file_exists($file_path))
				{
					unlink($file_path) ;
				}
			}
			function delete_file($file_path)
			{
				if(is_dir($file_path))
				{
					delete_dir($file_path)  ;
				}
				else
				{
					delete_simple_file($file_path)  ;
				}
			}
			function delete_dir($dir_path)
			{
				if(! is_dir($dir_path))
				{
					return ;
				}
				$dh = opendir($dir_path) ;
				if(! $dh)
				{
					return ;
				}
				$files = array() ;
				while(($file = readdir($dh)) !== false)
				{
					if(preg_match('/\.$/', $file))
					{
						continue ;
					}
					$files[] = $dir_path.'/'.$file ;
				}
				closedir($dh) ;
				foreach($files as $i => $file)
				{
					delete_file($file) ;
				}
				rmdir($dir_path) ;
			}
			function copy_modified_files($file_path_src, $file_path_dest, $min_timestamp)
			{
				if(is_string($min_timestamp))
				{
					$min_timestamp = strtotime($min_timestamp) ;
				}
				$list = list_modified_files($file_path_src, $min_timestamp) ;
				foreach($list as $i => $file_src)
				{
					$file_dest = preg_replace('/^'.preg_quote($file_path_src, "/").'/', $file_path_dest, $file_src) ;
					force_copy_file($file_src, $file_dest) ;
				}
			}
			function list_modified_files($file_path, $min_timestamp)
			{
				$list = array() ;
				if(is_dir($file_path))
				{
					$list = list_modified_files_in_dir($file_path, $min_timestamp) ;
				}
				else
				{
					$list = list_modified_file($file_path, $min_timestamp) ; 
				}
				return $list ;
			}
			function list_modified_file($file_path, $min_timestamp)
			{
				if(! file_exists($file_path))
				{
					return array() ;
				}
				$f = fopen($file_path, "r") ;
				$stat = fstat($f) ;
				fclose($f) ;
				$list = array() ;
				if($stat['mtime'] >= $min_timestamp)
				{
					$list[] = $file_path ;
				}
				return $list ;
			}
			function list_modified_files_in_dir($dir_path, $min_timestamp)
			{
				$dh = opendir($dir_path) ;
				if(! $dh)
				{
					return ;
				}
				$files = array() ;
				while(($file = readdir($dh)) !== false)
				{
					if(ereg('\\.$', $file))
					{
						continue ;
					}
					$files[] = $dir_path.'/'.$file ;
				}
				closedir($dh) ;
				$list = array() ;
				foreach($files as $i => $file)
				{
					$cur_list = list_modified_files($file, $min_timestamp) ;
					foreach($cur_list as $j => $entry)
					{
						$list[] = $entry ;
					}
				}
				return $list ;
			}
			function copy_simple_file($file_path_src, $file_path_dest)
			{
				if(! file_exists($file_path_src))
				{
					return ;
				}
				copy($file_path_src, $file_path_dest) ;
			}
			function copy_new_file($file_path_src, $file_path_dest)
			{
				if(! file_exists($file_path_src))
				{
					return ;
				}
				$fs = fopen($file_path_src, "r") ;
				$fd = fopen($file_path_dest, "w") ;
				while(!feof($fs))
				{
					$text = fgets($fs, 4096) ;
					fwrite($fd, $text) ;
				}
				fclose($fs) ;
				fclose($fd) ;
			}
			function copy_dir($dir_path_src, $dir_path_dest)
			{
				$files_src = array() ;
				$files_dest = array() ;
				$dh = opendir($dir_path_src) ;
				if(! $dh)
				{
					return ;
				}
				while(($file = readdir($dh)) !== false)
				{
					if(preg_match('/\\.$/', $file))
					{
						continue ;
					}
					$files_src[] = $dir_path_src.'/'.$file ;
					$files_dest[] = $dir_path_dest.'/'.$file ;
				}
				closedir($dh) ;
				if(! is_dir($dir_path_dest))
				{
					mkdir($dir_path_dest) ;
					chmod($dir_path_dest, 0777) ;
				}
				foreach($files_src as $i => $file)
				{
					copy_file($file, $files_dest[$i]) ;
				}
			}
			function copy_file($file_path_src, $file_path_dest)
			{
				if(is_dir($file_path_src))
				{
					copy_dir($file_path_src, $file_path_dest) ;
				}
				else
				{
					copy_simple_file($file_path_src, $file_path_dest) ;
				}
			}
			function extract_dirs_paths($file_path)
			{
				$dirs = split('/', $file_path) ;
				for($i=0; $i<count($dirs) - 1; $i++)
				{
					if($i > 0)
					{
						$dirs[$i] = $dirs[$i - 1].'/'.$dirs[$i] ;
					}
				}
				if(count($dirs))
				{
					unset($dirs[count($dirs) - 1]) ;
				}
				return $dirs ;
			}
			function extract_file_name($file_path)
			{
				return basename($file_path) ;
			}
			function extract_file_ext($file_path)
			{
				$ext = '' ;
				if(preg_match('/\.([a-z0-9_\-]+)$/i', $file_path, $match))
				{
					$ext = $match[1] ;
				}
				return strtolower($ext) ;
			}

			if(!function_exists('mime_content_type')) {

				function mime_content_type($filename) {

					$mime_types = array(

						'txt' => 'text/plain',
						'htm' => 'text/html',
						'html' => 'text/html',
						'php' => 'text/html',
						'css' => 'text/css',
						'js' => 'application/javascript',
						'json' => 'application/json',
						'xml' => 'application/xml',
						'swf' => 'application/x-shockwave-flash',
						'flv' => 'video/x-flv',

						// images
						'png' => 'image/png',
						'jpe' => 'image/jpeg',
						'jpeg' => 'image/jpeg',
						'jpg' => 'image/jpeg',
						'gif' => 'image/gif',
						'bmp' => 'image/bmp',
						'ico' => 'image/vnd.microsoft.icon',
						'tiff' => 'image/tiff',
						'tif' => 'image/tiff',
						'svg' => 'image/svg+xml',
						'svgz' => 'image/svg+xml',

						// archives
						'zip' => 'application/zip',
						'rar' => 'application/x-rar-compressed',
						'exe' => 'application/x-msdownload',
						'msi' => 'application/x-msdownload',
						'cab' => 'application/vnd.ms-cab-compressed',

						// audio/video
						'mp3' => 'audio/mpeg',
						'qt' => 'video/quicktime',
						'mov' => 'video/quicktime',

						// adobe
						'pdf' => 'application/pdf',
						'psd' => 'image/vnd.adobe.photoshop',
						'ai' => 'application/postscript',
						'eps' => 'application/postscript',
						'ps' => 'application/postscript',

						// ms office
						'doc' => 'application/msword',
						'rtf' => 'application/rtf',
						'xls' => 'application/vnd.ms-excel',
						'ppt' => 'application/vnd.ms-powerpoint',

						// open office
						'odt' => 'application/vnd.oasis.opendocument.text',
						'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
					);

					$ext = strtolower(array_pop(explode('.',$filename)));
					if (array_key_exists($ext, $mime_types)) {
						return $mime_types[$ext];
					}
					elseif (function_exists('finfo_open')) {
						$finfo = finfo_open(FILEINFO_MIME);
						$mimetype = finfo_file($finfo, $filename);
						finfo_close($finfo);
						return $mimetype;
					}
					else {
						return 'application/octet-stream';
					}
				}
			}
			function force_mkdir($dir_path)
			{
				if(is_dir($dir_path))
				{
					return ;
				}
				$entries = extract_dirs_paths($dir_path) ;
				foreach($entries as $i => $dir)
				{
					if(! is_dir($dir))
					{
						mkdir($dir) ;
						chmod($dir, 0777) ;
					}
				}
				mkdir($dir_path) ;
				chmod($dir_path, 0777) ;
			}
			function force_copy_file($file_path_src, $file_path_dest)
			{
				$dir_path = dirname($file_path_dest) ;
				force_mkdir($dir_path) ;
				copy_simple_file($file_path_src, $file_path_dest) ;
			}
			function content_of_file($file_path)
			{
				if(! file_exists($file_path))
				{
					return false ;
				}
				$ctn = "" ;
				try
				{
					$fh = fopen($file_path, "r") ;
					if($fh)
					{
						while(! feof($fh))
						{
							$ctn .= fgets($fh, 4096) ;
						}
						fclose($fh) ;
					}
				}
				catch(Exception $ex)
				{
					
				}
				return $ctn ;
			}
			function put_content_to_file($file_path, $content)
			{
				if(! file_exists($file_path))
				{
					return "" ;
				}
				$fh = fopen($file_path, "w") ;
				fputs($fh, $content) ;
				fclose($fh) ;
			}
			
			function move_posted_files($dir_path="files")
			{
				foreach($_FILES as $n => $posted_file)
				{
					if(preg_match('/^uploaded_/', $n))
					{
						$rn = preg_replace('/^uploaded_/', '', $n) ;
						$relative_path = $dir_path.'/'.date('U').basename($posted_file['name']) ;
						$uploadfile = APPLICATION_PATH.'/'.$relative_path ;
						if(move_uploaded_file($posted_file['tmp_name'], $uploadfile))
						{
							$_POST[$rn] = APPLICATION_URL.'/'.$relative_path ;
						}
					}
				}
			}
			
			$DEFAULT_ICON_NAME = 'images/page.gif' ;
			$DEFAULT_ICON_PATH = $DEFAULT_ICON_NAME ;
			if(defined('APPLICATION_URL'))
			{
				$DEFAULT_ICON_PATH = APPLICATION_URL.'/'.$DEFAULT_ICON_NAME ;
			}
			
			if(! function_exists('bcmod'))
			{
				function bcmod($div, $denom)
				{
					if($denom == 0)
						return 0 ;
					return ($div - intval($div / $denom) * $denom) ;
				}
				function bcdiv($div, $denom)
				{
					if($denom == 0)
						return 0 ;
					return intval($div / $denom) ;
				}
			}
			
			
			function init_download($filepath, $filename="", $download_rate=0)
			{
				if($filename == "")
				{
					$filename = str_replace(dirname($filepath).'/', "", $filepath) ;
				}
				if(! preg_match('/\.[a-zA-Z0-9_]+$/', $filename))
				{
					$ext = extract_file_ext($filepath) ;
					$filename .= '.'.$ext ;
				}
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private",false);
				header("Content-Type: application/octet-stream");
				header("Content-Disposition: attachment; filename=\"".$filename."\";");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($filepath));
				if(! $download_rate)
				{
					readfile($filepath);
				}
				else
				{
					$file = fopen($filepath, "r");
					while(! feof($file))
					{
						print fread($file, round($download_rate * 1024));
						flush();
						sleep(1);
					}
					fclose($file);
				}
				exit ;
			}
			
			function format_number_size($value, $maxLength=0)
			{
				$valueStr = "$value" ;
				if($maxLength == 0)
					return $valueStr ;
				if($maxLength < strlen($valueStr))
					return $valueStr ;
				$result = str_repeat("0", $maxLength - strlen($valueStr)).$valueStr ;
				return $result ;
			}
			
		/**
		  * A function for easily uploading files. This function will automatically generate a new 
		  *        file name so that files are not overwritten.
		  * Taken From: http://www.bin-co.com/php/scripts/upload_function/
		  * Arguments:    $file_id- The name of the input field contianing the file.
		  *                $folder    - The folder to which the file should be uploaded to - it must be writable. OPTIONAL
		  *                $types    - A list of comma(,) seperated extensions that can be uploaded. If it is empty, anything goes OPTIONAL
		  * Returns  : This is somewhat complicated - this function returns an array with two values...
		  *                The first element is randomly generated filename to which the file was uploaded to.
		  *                The second element is the status - if the upload failed, it will be 'Error : Cannot upload the file 'name.txt'.' or something like that
		**/
			function upload_file($file_id, $folder="", $types="")
			{
				/*
				if(! isset($_FILES[$file_id]))
				{
					return array('','No file specified') ;
				}
			    if(! isset($_FILES[$file_id]['name']))
				{
					return array('','No file specified');
				}
				*/
			}
		/*
		function upload_file($file_id, $folder="", $types="")
		{
			if(! isset($_FILES[$file_id]))
				return array('','No file specified') ;
		    if(! isset($_FILES[$file_id]['name']))
				return array('','No file specified');

		    $file_title = $_FILES[$file_id]['name'];
		    //Get file extension
		    $ext_arr = explode("\.",basename($file_title));
		    $ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension

		    //Not really uniqe - but for all practical reasons, it is
		    $uniqer = substr(md5(uniqid(rand(),1)),0,5);
		    $file_name = $uniqer . '_' . $file_title;//Get Unique Name

		    $all_types = explode(",",strtolower($types));
		    if($types) {
		        if(in_array($ext,$all_types));
		        else {
		            $result = "'".$_FILES[$file_id]['name']."' is not a valid file."; //Show error if any.
		            return array('',$result);
		        }
		    }

		    //Where the file must be uploaded to
		    if($folder) $folder .= '/';//Add a '/' at the end of the folder
		    $uploadfile = $folder . $file_name;

		    $result = '';
		    //Move the file from the stored location to the new location
		    if (!move_uploaded_file($_FILES[$file_id]['tmp_name'], $uploadfile)) {
		        $result = "Cannot upload the file '".$_FILES[$file_id]['name']."'"; //Show error if any.
		        if(!file_exists($folder)) {
		            $result .= " : Folder don't exist.";
		        } elseif(! is_writable($folder)) {
		            $result .= " : Folder not writable.";
		        } elseif(!is_writable($uploadfile)) {
		            $result .= " : File not writable.";
		        }
		        $file_name = '';
		        
		    } else {
		        if(! $_FILES[$file_id]['size']) { //Check if the file is made
		            @unlink($uploadfile); //Delete the Empty file
		            $file_name = '';
		            $result = "Empty file found - please use a valid file."; //Show the error message
		        } else {
		            chmod($uploadfile,0777); //Make it universally writable.
		        }
		    }
		    return array($file_name, $result);
		}
		*/
			if(! function_exists('utf8_urldecode'))
			{
				function utf8_urldecode($str) {
					$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
					return html_entity_decode($str,null,'UTF-8');;
				}
			}
			
			function call_user_func_list($func_list, $value='', $args=array())
			{
				$result = $value ;
				if($func_list == '')
				{
					return $result ;
				}
				$funcs = explode(",", $func_list) ;
				foreach($funcs as $i => $func)
				{
					if(function_exists($func))
					{
						$call_args = array_merge(array($result), (isset($args[$i])) ? $args[$i] : array()) ;
						$result = call_user_func_array($func, $call_args) ;
					}
				}
				return $result ;
			}
		}
		if(! function_exists('_parse_pattern'))
		{
			function _parse_pattern($pattern, $data=array(), $prefix="")
			{
				if(! is_string($pattern))
				{
					$pattern = "$pattern" ;
				}
				//_d_info("rrrr") ;
				if(! is_array($data))
				{
					$data = array($data) ;
				}
				$result = $pattern ;
				//_d_info("sss") ;
				foreach($data as $n => $v)
				{
					if(is_array($v) || is_object($v))
					{
						$result = _parse_pattern($result, $prefix.".".$n, $v) ;
					}
					else
					{
						$v = "$v" ;
						$pattern_prefix = "" ;
						if($prefix != "")
						{
							$pattern_prefix = $prefix."." ;
						}
						$result = str_ireplace("\${".$pattern_prefix.$n."}", $v, $result) ;
					}
				}
				// _d_step($result) ;
				//_d_info("mmmm") ;
				return $result ;
			}
		}
		if(! function_exists('extract_array_without_prefix'))
		{
			function extract_array_without_prefix($haystack, $prefix='')
			{
				return extract_array_without_vertices($haystack, $prefix, '') ;
			}
			function extract_array_without_suffix($haystack, $suffix='')
			{
				return extract_array_without_vertices($haystack, '', $suffix) ;
			}
			function extract_array_without_vertices($haystack, $prefix='', $suffix='')
			{
				$result = array() ;
				foreach($haystack as $n => $v)
				{
					$key = $n ;
					$ok = 1 ;
					if($prefix != '')
					{
						if(preg_match('/^'.$prefix.'/', $n))
						{
							$ok = 0 ;
						}
					}
					if($suffix != '')
					{
						if(preg_match('/'.$suffix.'$/', $n))
						{
							$ok = 0 ;
						}
					}
					if($ok)
					{
						$result[$key] = $v ;
					}
				}
				return $result ;
			}
			function extract_array_without_keys($haystack, $keys)
			{
				$result = array() ;
				foreach($haystack as $key => $val)
				{
					if(! in_array($key, $keys))
					{
						$result[$key] = $haystack[$key] ;
					}
				}
				return $result ;
			}
			function array_extract_value_for_keys($haystack, $keys)
			{
				$result = array() ;
				if(count($haystack) == 0 || count($keys) == 0)
				{
					return $result ;
				}
				foreach($keys as $i => $key)
				{
					if(isset($haystack[$key]))
					{
						$result[$key] = $haystack[$key] ;
					}
				}
				return $result ;
			}
			if(! function_exists('remove_url_params'))
			{
				function remove_url_params($url)
				{
					$attrs = explode("?", $url, 2) ;
					return $attrs[0] ;
				}
			}
			if (! function_exists('http_build_query_string'))
			{
				function http_build_query_string($data, $prefix='', $sep='', $key='', $raw=false) {
					if(! $data)
					{
						return '' ;
					}
					$ret = array();
					foreach ((array)$data as $k => $v) {
						if (is_int($k) && $prefix != null) {
							$k = (($raw) ? rawurlencode($prefix . $k) : urlencode($prefix . $k));
						}
						if ((!empty($key)) || ($key === 0))  $k = $key.'['.(($raw) ? rawurlencode($k) : urlencode($k)).']';
						if (is_array($v) || is_object($v)) {
							array_push($ret, http_build_query($v, '', $sep, $k));
						} else {
							array_push($ret, $k.'='.(($raw) ? rawurlencode($v) : urlencode($v)));
						}
					}
					if (empty($sep)) $sep = ini_get('arg_separator.output') ;
					return implode($sep, $ret);
				}// http_build_query
			}//if
			if(! function_exists('http_build_query'))
			{
				function http_build_query($data, $prefix='', $sep='', $key='') {
					return http_build_query_string($data, $prefix, $sep, $key) ;
				}
			}
			if(! function_exists('array_apply_vertices'))
			{
				function array_apply_vertices($haystack, $prefix='', $suffix='')
				{
					$result = array() ;
					foreach($haystack as $n => $v)
					{
						$result[$prefix.$n.$suffix] = $v ;
					}
					return $result ;
				}
				function array_apply_prefix($haystack, $prefix='')
				{
					return array_apply_vertices($haystack, $prefix, '') ;
				}
				function array_apply_suffix($haystack, $suffix='')
				{
					return array_apply_vertices($haystack, '', $suffix) ;
				}
				function extract_array_with_prefix($haystack, $prefix='')
				{
					return extract_array_with_vertices($haystack, $prefix, '') ;
				}
				function extract_array_with_suffix($haystack, $suffix='')
				{
					return extract_array_with_vertices($haystack, '', $suffix) ;
				}
				function extract_array_with_vertices($haystack, $prefix='', $suffix='')
				{
					$result = array() ;
					foreach($haystack as $n => $v)
					{
						$key = $n ;
						$ok = 1 ;
						if($prefix != '')
						{
							if(! preg_match('/^'.$prefix.'/', $n))
							{
								$ok = 0 ;
							}
							else
							{
								$key = preg_replace('/^'.$prefix.'/', '', $key) ;
							}
						}
						if($suffix != '')
						{
							if(! preg_match('/'.$suffix.'$/', $n))
							{
								$ok = 0 ;
							}
							else
							{
								$key = preg_replace('/'.$suffix.'$/', '', $key) ;
							}
						}
						if($ok)
						{
							$result[$key] = $v ;
						}
					}
					return $result ;
				}
			}
		}
		if(! function_exists('intro'))
		{
			/*
			* @author danp
			* @url http://stackoverflow.com/questions/10152894/php-replacing-special-characters-like-%C3%A0-a-%C3%A8-e
			*/
			function slugify($text,$strict = false, $sep=' ') {
				$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
				// replace non letter or digits by -
				$text = preg_replace('~[^\\pL\d.]+~u', '-', $text);

				// trim
				$text = trim($text, '-');
				setlocale(LC_CTYPE, 'en_GB.utf8');
				// transliterate
				if (function_exists('iconv')) {
				   $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
				}

				// lowercase
				$text = strtolower($text);
				// remove unwanted characters
				$text = preg_replace('~[^-\w.]+~', '', $text);
				if (empty($text)) {
				   return '';
				}
				if ($strict) {
					$text = str_replace(".", "_", $text);
				}
				$text = str_replace("-", $sep, $text) ;
				return $text;
			}
			function intro($Text, $NbWords = 255, $More = "...")
			{
				$Text = strip_tags($Text) ;
				$RetIntro = substr($Text, 0, $NbWords) ;
				if (strlen($Text) < $NbWords)
				{
					return $Text ;
				}
				if ($Text[$NbWords - 1] != ' ' and strlen($Text) > $NbWords)
				{
					for ($i=$NbWords; $i<strlen($Text) and ($Text[$i] != " "); $i++)
					{
						$RetIntro .= $Text[$i] ;
					}
				}
				$RetIntro .= $More ;
				return $RetIntro ;
			}
		}
		if(! function_exists('popularKeywords'))
		{
			function popularKeywords($text, $maxKeywords=8, $minKeywordLength=4) {
				// Replace all non-word chars with comma
				$pattern = '/[0-9\W]/';
				$text = preg_replace($pattern, ',', $text);

				// Create an array from $text
				$text_array = explode(",",$text);
				$keywords = array();
				$keyCounts = array();

				// remove whitespace and lowercase words in $text
				$text_array = array_map("popularKeywords_clearWord", $text_array);

				foreach ($text_array as $term) {
					if(strlen($term) < $minKeywordLength)
						continue ;
					if(! isset($keyCounts[$term]))
						$keyCounts[$term] = 0 ;
					$keyCounts[$term]++ ;
				};
				if(count($keyCounts) <= $maxKeywords)
				{
					return array_keys($keyCounts) ;
				}
				arsort($keyCounts) ;
				
				$keywords = array_slice(array_keys($keyCounts), 0, $maxKeywords) ;
				return $keywords ;
			}
			
			function popularKeywords_clearWord($x)
			{
				return trim(strtolower($x));
			}
		}
	
		if(! function_exists("get_client_ip_server"))
		{
			function get_client_ip_server()
			{
				$ipaddress = '';
				if ($_SERVER['HTTP_CLIENT_IP'])
					$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
				else if($_SERVER['HTTP_X_FORWARDED_FOR'])
					$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
				else if($_SERVER['HTTP_X_FORWARDED'])
					$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
				else if($_SERVER['HTTP_FORWARDED_FOR'])
					$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
				else if($_SERVER['HTTP_FORWARDED'])
					$ipaddress = $_SERVER['HTTP_FORWARDED'];
				else if($_SERVER['REMOTE_ADDR'])
					$ipaddress = $_SERVER['REMOTE_ADDR'];
				else
					$ipaddress = 'UNKNOWN';
				return $ipaddress;
			}
		}
		
	}
	
?>