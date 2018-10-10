<?php
	
	if(! defined('PV_PROTOCOLE_APPEL_DISTANT'))
	{
		define('PV_PROTOCOLE_APPEL_DISTANT', 1) ;
		if(! defined('EXPAT_XML_INCLUDED'))
		{
			include dirname(__FILE__)."/../../../ExpatXml/ExpatXml.class.php" ;
		}
		
		class PvStructMsgAppelDistant
		{
			public $MembreRacine ;
			public function __construct()
			{
				$this->MembreRacine = new PvMembreVarStructMsgAppelDistant() ;
			}
			public function & DefinitMembre($membre)
			{
				$this->MembreRacine = & $membre ;
				return $membre ;
			}
			public function & DefinitMembreChoix()
			{
				return $this->DefinitMembre(new PvMembreObjetStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreObjet()
			{
				return $this->DefinitMembre(new PvMembreObjetStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreArray()
			{
				return $this->DefinitMembre(new PvMembreArrayStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreDate()
			{
				return $this->DefinitMembre(new PvMembreDateStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreInt()
			{
				return $this->DefinitMembre(new PvMembreIntStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreString()
			{
				return $this->DefinitMembre(new PvMembreStringStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreDouble()
			{
				return $this->DefinitMembre(new PvMembreDoubleStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreFloat()
			{
				return $this->DefinitMembre(new PvMembreFloatStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreBool()
			{
				return $this->DefinitMembre(new PvMembreBoolStructMsgAppelDistant()) ;
			}
		}
		class PvMembreBaseStructMsgAppelDistant
		{
			public $Nom ;
			public $MinOccurs = 0 ;
			public $MaxOccurs = 1 ;
			public function ContenuTypeWsdl()
			{
				$ctn = '' ;
				return $ctn ;
			}
			public function Type()
			{
				return "base" ;
			}
			public function AccepteValeur($valeur)
			{
				return 0 ;
			}
		}
		class PvMembreStringStructMsgAppelDistant extends PvMembreBaseStructMsgAppelDistant
		{
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'"' ;
				$ctn .= ' type="s:string" />' ;
				return $ctn ;
			}
			public function Type()
			{
				return "string" ;
			}
			public function AccepteValeur($valeur)
			{
				return is_scalar($valeur) ;
			}
		}
		class PvMembreDateStructMsgAppelDistant extends PvMembreBaseStructMsgAppelDistant
		{
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'"' ;
				$ctn .= ' type="s:dateTime" />' ;
				return $ctn ;
			}
			public function Type()
			{
				return "date" ;
			}
			public function AccepteValeur($valeur)
			{
				return is_string($valeur) ;
			}
		}
		class PvMembreIntStructMsgAppelDistant extends PvMembreBaseStructMsgAppelDistant
		{
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'"' ;
				$ctn .= ' type="s:int" />' ;
				return $ctn ;
			}
			public function Type()
			{
				return "int" ;
			}
			public function AccepteValeur($valeur)
			{
				return is_int($valeur) ;
			}
		}
		class PvMembreFloatStructMsgAppelDistant extends PvMembreBaseStructMsgAppelDistant
		{
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'"' ;
				$ctn .= ' type="s:float" />' ;
				return $ctn ;
			}
			public function Type()
			{
				return "float" ;
			}
			public function AccepteValeur($valeur)
			{
				return is_float($valeur) ;
			}
		}
		class PvMembreDoubleStructMsgAppelDistant extends PvMembreBaseStructMsgAppelDistant
		{
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'"' ;
				$ctn .= ' type="s:double" />' ;
				return $ctn ;
			}
			public function Type()
			{
				return "double" ;
			}
			public function AccepteValeur($valeur)
			{
				return is_double($valeur) ;
			}
		}
		class PvMembreBoolStructMsgAppelDistant extends PvMembreBaseStructMsgAppelDistant
		{
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'"' ;
				$ctn .= ' type="s:boolean" />' ;
				return $ctn ;
			}
			public function Type()
			{
				return "bool" ;
			}
			public function AccepteValeur($valeur)
			{
				return $valeur == 1 || $valeur == 0 || $valeur == "true" || $valeur = "false" ;
			}
		}
		class PvMembreArrayStructMsgAppelDistant extends PvMembreBaseStructMsgAppelDistant
		{
			public $Membre ;
			public $NomMembre = "item" ;
			public $MaxOccurences = 0 ;
			public function __construct()
			{
				$this->Membre = new PvMembreVarStructMsgAppelDistant() ;
			}
			public function ContenuTypeWsdl()
			{
				$this->Membre->Nom = $this->NomMembre ;
				$this->Membre->MaxOccurs = "unbounded" ;
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'">'.PHP_EOL ;
				$ctn .= '<s:complexType>'.PHP_EOL ;
				$ctn .= '<s:sequence>'.PHP_EOL ;
				$ctn .= $this->Membre->ContenuTypeWsdl() ;
				$ctn .= '</s:sequence>'.PHP_EOL ;
				$ctn .= '</s:complexType>'.PHP_EOL ;
				$ctn .= '</s:element>' ;
				return $ctn ;
			}
			public function Type()
			{
				return "array" ;
			}
			public function & DefinitMembre($membre)
			{
				$this->Membre = & $membre ;
				return $membre ;
			}
			public function & DefinitMembreObjet()
			{
				return $this->DefinitMembre(new PvMembreObjetStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreChoix()
			{
				return $this->DefinitMembre(new PvMembreChoixStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreArray()
			{
				return $this->DefinitMembre(new PvMembreArrayStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreDate()
			{
				return $this->DefinitMembre(new PvMembreDateStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreInt()
			{
				return $this->DefinitMembre(new PvMembreIntStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreString()
			{
				return $this->DefinitMembre(new PvMembreStringStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreDouble()
			{
				return $this->DefinitMembre(new PvMembreDoubleStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreFloat()
			{
				return $this->DefinitMembre(new PvMembreFloatStructMsgAppelDistant()) ;
			}
			public function & DefinitMembreBool()
			{
				return $this->DefinitMembre(new PvMembreBoolStructMsgAppelDistant()) ;
			}
			public function AccepteValeur($valeur)
			{
				if(! is_array($valeur))
				{
					return false ;
				}
				$ok = true ;
				foreach($valeur as $i => $valMembre)
				{
					if(! $this->Membre->AccepteValeur($valMembre))
					{
						$ok = false ;
						break ;
					}
				}
				return $ok ;
			}
		}
		class PvMembreObjetStructMsgAppelDistant extends PvMembreBaseStructMsgAppelDistant
		{
			public $Membres = array() ;
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'">'.PHP_EOL ;
				$ctn .= '<s:complexType>'.PHP_EOL ;
				$ctn .= '<s:sequence>'.PHP_EOL ;
				foreach($this->Membres as $n => $membre)
				{
					$membre->Nom = $n ;
					$ctn .= $membre->ContenuTypeWsdl().PHP_EOL ;
				}
				$ctn .= '</s:sequence>'.PHP_EOL ;
				$ctn .= '</s:complexType>'.PHP_EOL ;
				$ctn .= '</s:element>' ;
				return $ctn ;
			}
			public function Type()
			{
				return "object" ;
			}
			public function AccepteValeur($valeur)
			{
				$ok = false ;
				if(is_object($valeur) || is_array($valeur))
				{
					$ok = true ;
					foreach($this->Membres as $nom => $membre)
					{
						$valMembre = (is_object($valeur)) ? $valeur->$nom : $valeur[$nom] ;
						if(! empty($valMembre) && ! $membre->AccepteValeur($valMembre))
						{
							$ok = false ;
							break ;
						}
					}
				}
				return $ok ;
			}
			public function & InsereMembre($nom, $membre)
			{
				$membre->Nom = $nom ;
				$this->Membres[$nom] = & $membre ;
				return $membre ;
			}
			public function & InsereMembreObjet($nom)
			{
				return $this->InsereMembre($nom, new PvMembreObjetStructMsgAppelDistant()) ;
			}
			public function & InsereMembreChoix($nom)
			{
				return $this->InsereMembre($nom, new PvMembreChoixStructMsgAppelDistant()) ;
			}
			public function & InsereMembreArray($nom)
			{
				return $this->InsereMembre($nom, new PvMembreArrayStructMsgAppelDistant()) ;
			}
			public function & InsereMembreDate($nom)
			{
				return $this->InsereMembre($nom, new PvMembreDateStructMsgAppelDistant()) ;
			}
			public function & InsereMembreInt($nom)
			{
				return $this->InsereMembre($nom, new PvMembreIntStructMsgAppelDistant()) ;
			}
			public function & InsereMembreString($nom)
			{
				return $this->InsereMembre($nom, new PvMembreStringStructMsgAppelDistant()) ;
			}
			public function & InsereMembreDouble($nom)
			{
				return $this->InsereMembre($nom, new PvMembreDoubleStructMsgAppelDistant()) ;
			}
			public function & InsereMembreFloat($nom)
			{
				return $this->InsereMembre($nom, new PvMembreFloatStructMsgAppelDistant()) ;
			}
			public function & InsereMembreBool($nom)
			{
				return $this->InsereMembre($nom, new PvMembreBoolStructMsgAppelDistant()) ;
			}
		}
		class PvMembreVarStructMsgAppelDistant extends PvMembreObjetStructMsgAppelDistant
		{
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'"' ;
				$ctn .= ' type="s:string" />' ;
				return $ctn ;
			}
			public function Type()
			{
				return "variable" ;
			}
			public function AccepteValeur($valeur)
			{
				return 1 ;
			}
		}
		class PvMembreChoixStructMsgAppelDistant extends PvMembreObjetStructMsgAppelDistant
		{
			public function ContenuTypeWsdl()
			{
				$ctn = '<s:element name="'.$this->Nom.'" minOccurs="'.$this->MinOccurs.'" maxOccurs="'.$this->MaxOccurs.'">'.PHP_EOL ;
				$ctn .= '<s:complexType>'.PHP_EOL ;
				$ctn .= '<s:sequence>'.PHP_EOL ;
				$ctn .= '<s:choice>'.PHP_EOL ;
				foreach($this->Membres as $n => $membre)
				{
					$membre->Nom = $n ;
					$ctn .= $membre->ContenuTypeWsdl().PHP_EOL ;
				}
				$ctn .= '</s:choice>'.PHP_EOL ;
				$ctn .= '</s:sequence>'.PHP_EOL ;
				$ctn .= '</s:complexType>'.PHP_EOL ;
				$ctn .= '</s:element>' ;
				return $ctn ;
			}
			public function Type()
			{
				return "choice" ;
			}
		}
		
		class PvContenuAppelDistant
		{
			public $nomMethode ;
			public $args ;
		}
		class PvContenuJsonAppelDistant
		{
			public $jsonrpc ;
			public $method ;
			public $params ;
			public $version = "1.0" ;
			public $id ;
		}
		
		class PvProtocoleBaseAppelDistant extends PvElemZoneAppelDistant
		{
			public $Encodage = "utf-8" ;
			public function NomBindingWsdl()
			{
				return $this->ZoneParent->NomService.$this->NomElementZone ;
			}
			public function RemplitWsdl(& $wsdl)
			{
				$wsdl->ContenusBinding[$this->NomBindingWsdl()] = $this->RenduBindingWsdl() ; 
				$wsdl->ContenusService[] = '<wsdl:port name="'.$this->NomBindingWsdl().'" binding="tns:'.$this->NomBindingWsdl().'">
<soap:address location="'.remove_url_params(get_current_url()).'" />
</wsdl:port>' ;
			}
			protected function RenduBindingWsdl()
			{
				$ctn = '' ;
				foreach($this->ZoneParent->MethodesDistantes as $nom => $mtdDist)
				{
					$ctn .= '<wsdl:operation name="'.$nom.'">
<soap:operation soapAction="http://tempuri.org/'.$nom.'" style="document" />
<wsdl:input>
<soap:body use="literal" />
</wsdl:input>
<wsdl:output>
<soap:body use="literal" />
</wsdl:output>'.(($this->ZoneParent->InclureFaultWsdl == 1) ? PHP_EOL
.'<wsdl:fault name="'.$this->ZoneParent->NomService.'Fault">
<soap:fault name="'.$this->ZoneParent->NomService.'Fault" use="literal"/>
</wsdl:fault>' : '').'
</wsdl:operation>'.PHP_EOL ;
				}
				return $ctn ;
			}
			public function EnteteHttp($nom, $valeurParDefaut='')
			{
				return $this->ZoneParent->EnteteHttp($nom, $valeurParDefaut) ;
			}
			public function ContenuAppelRecu()
			{
				return $this->ZoneParent->AppelRecu->Contenu ;
			}
			public function NomProtocole()
			{
				return "base" ;
			}
			public function EstActif()
			{
			}
			public function Wsdl()
			{
				return '' ;
			}
			protected function CreeContenu()
			{
				return new PvContenuAppelDistant() ;
			}
			public function DecodeContenu()
			{
				$ctn = $this->CreeContenu() ;
				return $ctn ;
			}
			public function EncodeContenu($contenu)
			{
				return serialize($resultat) ;
			}
			public function EncodeResultat($resultat)
			{
				return serialize($resultat) ;
			}
		}
		class PvProtocNatifAppelDistant extends PvProtocoleBaseAppelDistant
		{
			public function NomProtocole()
			{
				return "natif" ;
			}
			public function EstActif()
			{
				$accept = $this->EnteteHttp("Accept") ;
				if($accept == "*/*" || $accept == "text/javascript" || $accept == "application/*" || stripos($accept, "application/json") !== false)
				{
					return 1 ;
				}
				return 0 ;
			}
			protected function CreeContenu()
			{
				return new PvContenuAppelDistant() ;
			}
			public function DecodeContenu()
			{
				$ctn = $this->CreeContenu() ;
				$appel = null ;
				if($this->ContenuAppelRecu() != '')
				{
					$appel = svc_json_decode($this->ContenuAppelRecu()) ;
				}
				if($appel != null)
				{
					$ctn->nomMethode = $appel->method ;
					if(isset($appel->args))
					{
						$ctn->args = $appel->args ;
					}
					elseif(isset($appel->params))
					{
						$ctn->args = $appel->params ;
					}
				}
				return $ctn ;
			}
			public function EncodeContenu($contenu)
			{
				$appel = new PvContenuJsonAppelDistant() ;
				$appel->method = $contenu->nomMethode ;
				$appel->params = $contenu->args ;
				return svc_json_encode($appel) ;
			}
			public function EncodeResultat($resultat)
			{
				return svc_json_encode($resultat) ;
			}
		}
		
		class PvProtocSoapBaseAppelDistant extends PvProtocoleBaseAppelDistant
		{
			public $XmlnsTem = "http://tempuri.org/" ;
			public $PrefixeMembre = "TEM:" ;
			public $XmlnsSoap = "http://www.w3.org/2003/05/soap-envelope" ;
			public $XmlnsXsi = "http://www.w3.org/2001/XMLSchema-instance" ;
			public $XmlnsXsd = "http://www.w3.org/2001/XMLSchema" ;
			public $PrefixeEnvRecu ;
			protected function EncodeFault($resultat)
			{
				$ctn = '' ;
				if($this->ZoneParent->InclureFaultWsdl == 0)
				{
					$ctn .= '<soap:Fault>
<soap:Code>
<soap:Value>'.$resultat->erreur->code.'</soap:Value>
</soap:Code>
<soap:Reason>
<soap:Text>'.htmlentities($resultat->erreur->message, ENT_COMPAT).'</soap:Text>
</soap:Reason>
</soap:Fault>' ;
				}
				else
				{
					$nomMethode = $this->ZoneParent->ValeurParamMtdDist ;
					/*
					if($nomMethode != '')
					{
						$ctn .= '<'.$nomMethode.'Response xmlns="'.$this->XmlnsTem.'">'.PHP_EOL ;
					}
					*/
					$ctn .= '<soap:Fault>
<faultcode>SOAP:Server</faultcode>
<faultstring>Erreur rencontree</faultstring>
<detail>
<'.$this->ZoneParent->NomService.'Fault xmlns="'.$this->XmlnsTem.'">
<errorCode>'.$resultat->erreur->code.'</errorCode>
<errorMessage>'.htmlentities($resultat->erreur->message, ENT_COMPAT).'</errorMessage>
</'.$this->ZoneParent->NomService.'Fault>
</detail>
</soap:Fault>' ;
					/*
					if($nomMethode != '')
					{
						$ctn .= '</'.$nomMethode.'Response>'.PHP_EOL ;
					}
					*/
				}
				return $ctn ;
			}
			protected function DecodeNodeMembre($node, $membreStruct)
			{
				$ctn = '' ;
				switch($membreStruct->Type())
				{
					case "variable" : {
						if(count($node->ChildNodes) > 0)
						{
							$ctn = new StdClass ;
							foreach($node->ChildNodes as $i => $childNode)
							{
								$nomProp = str_replace($this->PrefixeMembre, "", $childNode->Name) ;
								$ctn->$nomProp = $this->DecodeNodeMembre($childNode, $membreStruct) ;
							}
						}
						else
						{
							$ctn = $node->Content ;
						}
					}
					break ;
					case "object" : {
						if(count($node->ChildNodes) > 0)
						{
							$ctn = new StdClass() ;
							foreach($node->ChildNodes as $i => $childNode)
							{
								$nomProp = "" ;
								$membreStructTmp = null ;
								foreach($membreStruct->Membres as $nom => $membreStructTmp)
								{
									if(strtoupper($this->PrefixeMembre.$membreStructTmp->Nom) == $childNode->Name || strtoupper($membreStructTmp->Nom) == $childNode->Name)
									{
										$nomProp = $membreStructTmp->Nom ;
										break ;
									}
								}
								if($nomProp != '')
								{
									$ctn->$nomProp = $this->DecodeNodeMembre($childNode, $membreStructTmp) ;
								}
							}
						}
					}
					break ;
					case "array" : {
						if(count($node->ChildNodes) > 0)
						{
							$ctn = array() ;
							foreach($node->ChildNodes as $i => $childNode)
							{
								$nomProp = $childNode->Name ;
								if($nomProp == strtoupper($this->PrefixeMembre.$membreStruct->NomMembre)|| $nomProp == strtoupper($membreStruct->NomMembre))
								{
									$ctn[] = $this->DecodeNodeMembre($childNode, $membreStruct->Membre) ;
								}
							}
						}
					}
					break ;
					case "string" : {
						$ctn = $node->Content ;
					}
					break ;
					case "int" : {
						$ctn = intval($node->Content) ;
					}
					break ;
					case "double" : {
						$ctn = doubleval($node->Content) ;
					}
					break ;
					case "float" : {
						$ctn = floatval($node->Content) ;
					}
					break ;
					case "bool" : {
						$ctn = ($node->Content == "true" || $node->Content == 1) ? 1 : 0 ;
					}
					break ;
				}
				return $ctn ;
			}
			protected function EncodeValeurMembre($valeur, $membreStruct)
			{
				$ctn = '' ;
				if(! $membreStruct->AccepteValeur($valeur))
				{
					return $ctn ;
				}
				switch($membreStruct->Type())
				{
					case "variable" :
					{
						if(is_object($valeur)) {
							$ctn .= svc_json_encode($valeur) ;
						}
						elseif(is_array($valeur)) {
							$ctn .= svc_json_encode($valeur) ;
						}
						elseif(is_resource($valeur)) {
							$ctn .= 'Resource' ;
						}
						else {
							$ctn .= htmlentities($valeur, ENT_COMPAT | ENT_IGNORE | ENT_XML1, 'utf-8') ;
						}
					}
					break ;
					case "string" :
					{
						// return $this->EncodeTexteHtml($valeur) ;
						return ($this->ZoneParent->NettoieCaractsSoap == 1) ? htmlentities($valeur, ENT_COMPAT | ENT_SUBSTITUTE | ENT_XML1, 'utf-8') : encode_html_symbols($valeur) ;
					}
					break ;
					case "date" :
					{
						if(is_numeric($valeur))
						{
							return str_replace("T", " ", $valeur) ;
						}
						elseif(preg_match('/\d+\-\d+\-\d+ \d+\:\d+\:\d+/', $valeur))
						{
							return str_replace(" ", "T", $valeur) ;
						}
						else
						{
							return '' ;
						}
					}
					break ;
					case "int" :
					{
						return intval($valeur) ;
					}
					break ;
					case "float" :
					{
						return floatval($valeur) ;
					}
					break ;
					case "double" :
					{
						return doubleval($valeur) ;
					}
					break ;
					case "bool" :
					{
						return ($valeur) ? 'true' : 'false' ;
					}
					break ;
					case "object" :
					{
						if(is_object($valeur))
						{
							foreach($membreStruct->Membres as $nom => $membreStructTmp)
							{
								$ctn .= '<'.htmlspecialchars($nom).'>' ;
								if(isset($valeur->$nom))
								{
									$valeurTmp = $valeur->$nom ;
									$ctn .= $this->EncodeValeurMembre($valeurTmp, $membreStructTmp) ;
								}
								$ctn .= '</'.htmlspecialchars($nom).'>' ;
							}
						}
					}
					break ;
					case "choice" :
					{
						if(is_object($valeur))
						{
							foreach($membreStruct->Membres as $nom => $membreStructTmp)
							{
								if(isset($valeur->$nom) && $valeur->$nom != null)
								{
									$ctn .= '<'.htmlspecialchars($nom).'>' ;
									$valeurTmp = $valeur->$nom ;
									$ctn .= $this->EncodeValeurMembre($valeurTmp, $membreStructTmp) ;
									$ctn .= '</'.htmlspecialchars($nom).'>' ;
									break ;
								}
							}
						}
					}
					break ;
					case "array" :
					{
						if(is_array($valeur))
						{
							foreach($valeur as $i => $valeurTmp)
							{
								$ctn .= '<'.htmlspecialchars($membreStruct->NomMembre).'>' ;
								$ctn .= $this->EncodeValeurMembre($valeurTmp, $membreStruct->Membre) ;
								$ctn .= '</'.htmlspecialchars($membreStruct->NomMembre).'>' ;
							}
						}
					}
					break ;
				}
				return $ctn ;
			}
		}
		class PvProtocSoap1_1AppelDistant extends PvProtocSoapBaseAppelDistant
		{
			public function RemplitWsdl(& $wsdl)
			{
				parent::RemplitWsdl($wsdl) ;
				$wsdl->ContenusBinding[$this->NomBindingWsdl()] = '<soap:binding transport="http://schemas.xmlsoap.org/soap/http" />'.PHP_EOL
.$wsdl->ContenusBinding[$this->NomBindingWsdl()] ;
			}
			public function NomProtocole()
			{
				return "soap_1_1" ;
			}
			public function EstActif()
			{
				$contentType = $this->EnteteHttp("Content-Type") ;
				// $soapAction = $this->EnteteHttp("SOAPAction") ;
				if($contentType == "text/xml")
				{
					return 1 ;
				}
				return 0 ;
			}
			public function DecodeContenu()
			{
				$ctn = $this->CreeContenu() ;
				$enteteAction = $this->ZoneParent->EnteteHttp("SOAPAction") ;
				if($enteteAction != '')
				{
					$enteteAction = str_replace('"', '', $enteteAction) ;
					$enteteAction = str_replace('\'', '', $enteteAction) ;
					$ctn->nomMethode = substr($enteteAction, strlen($this->XmlnsTem)) ;
				}
				$parser = new ExpatXmlParser() ;
				$rootDoc = $parser->ParseContent($this->ContenuAppelRecu()) ;
				$rootNode = $rootDoc->RootNode() ;
				$envelopAttrs = explode(":", $rootNode->Name, 2) ;
				$this->PrefixeEnvRecu = $envelopAttrs[0] ;
				if($rootNode->Name != $this->PrefixeEnvRecu.":ENVELOPE")
				{
					return $ctn ;
				}
				$bodyNodes = $rootNode->GetChildNodesByTagName($this->PrefixeEnvRecu.":BODY") ;
				if(count($bodyNodes) != 1)
				{
					return $ctn ;
				}
				if($ctn->nomMethode == "")
				{
					$tempNodes = $bodyNodes[0]->ChildNodes ;
					foreach($this->ZoneParent->MethodesDistantes as $nom => $mtdDist)
					{
						for($j=0; $j<count($tempNodes); $j++)
						{
							if($tempNodes[$j]->Name == $this->PrefixeMembre.strtoupper($nom) || $tempNodes[$j]->Name == strtoupper($nom))
							{
								$ctn->nomMethode = $nom ;
								$methodNodes = array($tempNodes[$j]) ;
								break ;
							}
						}
						if($ctn->nomMethode != "")
						{
							break ;
						}
					}
				}
				else
				{
					$methodNodes = $bodyNodes[0]->GetChildNodesByTagName(strtoupper($ctn->nomMethode)) ;
					if(count($methodNodes) == 0)
					{
						$methodNodes = $bodyNodes[0]->GetChildNodesByTagName($this->PrefixeMembre.strtoupper($ctn->nomMethode)) ;
					}
				}
				if(count($methodNodes) != 1)
				{
					return $ctn ;
				}
				$methodeDist = $this->ZoneParent->ObtientMethodeDistante($ctn->nomMethode) ;
				$ctn->args = $this->DecodeNodeMembre($methodNodes[0], $methodeDist->StructRequete->MembreRacine) ;
				return $ctn ;
			}
			public function EncodeResultat($resultat)
			{
				Header("Content-Type: text/xml; charset=".$this->Encodage) ;
				$ctn = '' ;
				$nomMethode = $this->ZoneParent->ValeurParamMtdDist ;
				$ctn .= '<?xml version="1.0" encoding="'.$this->Encodage.'"?>
<soap:Envelope xmlns:soap="'.$this->XmlnsSoap.'" xmlns:xsi="'.$this->XmlnsXsi.'" xmlns:xsd="'.$this->XmlnsXsd.'">'.PHP_EOL ;
				$ctn .= '<soap:Body>'.PHP_EOL ;
				if($resultat->ErreurTrouvee())
				{
					$ctn .= $this->EncodeFault($resultat).PHP_EOL ;
				}
				else
				{
					$ctn .= '<'.$nomMethode.'Response xmlns="'.$this->XmlnsTem.'">'.PHP_EOL ;
					$ctn .= $this->EncodeValeurMembre($resultat->valeur, $this->ZoneParent->MtdDistSelect->StructReponse->MembreRacine) ;
					$ctn .= '</'.$nomMethode.'Response>'.PHP_EOL ;
				}
				$ctn .= '</soap:Body>'.PHP_EOL ;
				$ctn .= '</soap:Envelope>' ;
				return $ctn ;
			}
		}
		class PvProtocSoap1_2AppelDistant extends PvProtocSoapBaseAppelDistant
		{
			public function RemplitWsdl(& $wsdl)
			{
				$wsdl->AttrsDefinition["xmlns:soap12"] = "http://schemas.xmlsoap.org/wsdl/soap12/" ;
				parent::RemplitWsdl($wsdl) ;
				$wsdl->ContenusBinding[$this->NomBindingWsdl()] = '<soap12:binding transport="http://schemas.xmlsoap.org/soap/http" />'.PHP_EOL
.$wsdl->ContenusBinding[$this->NomBindingWsdl()] ;
			}
			public function NomProtocole()
			{
				return "soap_1_2" ;
			}
			public function EstActif()
			{
				$contentType = $this->EnteteHttp("Content-Type") ;
				if($contentType == "application/soap+xml")
				{
					return 1 ;
				}
				return 0 ;
			}
			public function DecodeContenu()
			{
				$ctn = $this->CreeContenu() ;
				$enteteAction = $this->ZoneParent->EnteteContentType("action") ;
				if($enteteAction != '')
				{
					$enteteAction = str_replace('"', '', $enteteAction) ;
					$enteteAction = str_replace('\'', '', $enteteAction) ;
					$ctn->nomMethode = substr($enteteAction, strlen($this->XmlnsTem)) ;
				}
				$parser = new ExpatXmlParser() ;
				$rootDoc = $parser->ParseContent($this->ContenuAppelRecu()) ;
				$rootNode = $rootDoc->RootNode() ;
				$envelopAttrs = explode(":", $rootNode->Name, 2) ;
				$this->PrefixeEnvRecu = $envelopAttrs[0] ;
				if($rootNode->Name != $this->PrefixeEnvRecu.":ENVELOPE")
				{
					return $ctn ;
				}
				$bodyNodes = $rootNode->GetChildNodesByTagName($this->PrefixeEnvRecu.":BODY") ;
				if(count($bodyNodes) != 1)
				{
					return $ctn ;
				}
				if($ctn->nomMethode == "")
				{
					$tempNodes = $bodyNodes[0]->ChildNodes ;
					foreach($this->ZoneParent->MethodesDistantes as $nom => $mtdDist)
					{
						for($j=0; $j<count($tempNodes); $j++)
						{
							if($tempNodes[$j]->Name == $this->PrefixeMembre.strtoupper($nom) || $tempNodes[$j]->Name == strtoupper($nom))
							{
								$ctn->nomMethode = $nom ;
								$methodNodes = array($tempNodes[$j]) ;
								break ;
							}
						}
						if($ctn->nomMethode != "")
						{
							break ;
						}
					}
				}
				else
				{
					$methodNodes = $bodyNodes[0]->GetChildNodesByTagName(strtoupper($ctn->nomMethode)) ;
					if(count($methodNodes) == 0)
					{
						$methodNodes = $bodyNodes[0]->GetChildNodesByTagName($this->PrefixeMembre.strtoupper($ctn->nomMethode)) ;
					}
				}
				if(count($methodNodes) != 1)
				{
					return $ctn ;
				}
				$methodeDist = $this->ZoneParent->ObtientMethodeDistante($ctn->nomMethode) ;
				$ctn->args = $this->DecodeNodeMembre($methodNodes[0], $methodeDist->StructRequete->MembreRacine) ;
				return $ctn ;
			}
			public function EncodeResultat($resultat)
			{
				Header("Content-Type: application/soap+xml; charset=".$this->Encodage) ;
				$ctn = '' ;
				$nomMethode = $this->ZoneParent->ValeurParamMtdDist ;
				$ctn .= '<?xml version="1.0" encoding="'.$this->Encodage.'"?>
<soap:Envelope xmlns:soap="'.$this->XmlnsSoap.'" xmlns:xsi="'.$this->XmlnsXsi.'" xmlns:xsd="'.$this->XmlnsXsd.'">'.PHP_EOL ;
				$ctn .= '<soap:Body>'.PHP_EOL ;
				if($resultat->ErreurTrouvee())
				{
					$ctn .= $this->EncodeFault($resultat).PHP_EOL ;
				}
				else
				{
					$ctn .= '<'.$nomMethode.'Response xmlns="'.$this->XmlnsTem.'">'.PHP_EOL ;
					$ctn .= $this->EncodeValeurMembre($resultat->valeur, $this->ZoneParent->MtdDistSelect->StructReponse->MembreRacine) ;
					$ctn .= '</'.$nomMethode.'Response>'.PHP_EOL ;
				}
				$ctn .= '</soap:Body>'.PHP_EOL ;
				$ctn .= '</soap:Envelope>' ;
				return $ctn ;
			}
		}
	}
	
?>