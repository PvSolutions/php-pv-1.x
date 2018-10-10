<?php
	
	if(! defined('PV_COMPOSANT_IU'))
	{
		if(! defined('PV_NOYAU_IHM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_COMPOSANT_IU', 1) ;
		
		class PvInfoRenduElementAccessible
		{
			public $Index = -1 ;
			public $TimestampDebut = 0 ;
		}
		
		class PvElementAccessible extends PvObjet
		{
			public $UrlsReferantsSurs = array() ;
			public $HotesReferantsSurs = array() ;
			public $RefererHoteLocal = 0 ;
			public $RefererUrlLocale = 0 ;
			public $ScriptsReferantsSurs = array() ;
			public $RefererScriptLocal = 0 ;
			public $NecessiteMembreConnecte = 0 ;
			public $Privileges = array() ;
			public $MessageMalRefere = "<p>Ce composant n'est pas bien refere. Il ne peut etre affiche</p>" ;
			public $MessageInaccessible = "<p>Vous n'avez pas les droits necessaires pour afficher ce composant.</p>" ;
			public $MaxRendus = 0 ;
			public $MessageMaxRendusAtteint = "<p>Vous avez atteint le maximum de rendus autoris&eacute;s</p>" ;
			public $DelaiExpirRendu = 300 ;
			public $InfosRendu = array() ;
			protected function RestaureInfosRendu()
			{
				if(! in_array($_SESSION[$this->IDInstanceCalc."_InfosRendu"], $_SESSION))
				{
					$this->InfosRendu = array() ;
					return ;
				}
				$this->InfosRendu = unserialize($_SESSION[$this->IDInstanceCalc."_InfosRendu"]) ;
			}
			protected function SauveInfosRendu()
			{
				$_SESSION[$this->IDInstanceCalc."_InfosRendu"] = serialize($this->InfosRendu) ;
			}
			protected function InsereInfoRenduEnCours()
			{
				if($this->MaxRendus == 0)
				{
					return ;
				}
				$info = new PvInfoRenduElementAccessible() ;
				$info->TimestampDebut = date("U") ;
				$info->Index = count($this->InfosRendu) ;
				$this->InfosRendu[] = & $info ;
			}
			protected function RetireInfoRenduEnCours()
			{
				if($this->MaxRendus == 0)
				{
					return ;
				}
				array_splice($this->InfosRendu, $info->Index, 1) ;
			}
			protected function VideRendusExpires()
			{
				$indexes = array() ;
				foreach($this->InfosRendu as $i => $info)
				{
					if($info->TimestampDebut + $this->DelaiExpirRendu <= date("U"))
					{
						$indexes[] = $i ;
					}
				}
				if(count($indexes) > 0)
				{
					$infosRendu = $this->InfosRendu ;
					$this->InfosRendu = array() ;
					foreach($infosRendu as $i => $info)
					{
						if(in_array($i, $indexes))
						{
							continue ;
						}
						$info->Index = count($this->InfosRendu) ;
						$this->InfosRendu[] = $info ;
					}
				}
			}
			public function MaxRendusAtteint()
			{
				if($this->MaxRendus <= 0)
				{
					return 0 ;
				}
				$ok = 0 ;
				$this->RestaureInfosRendu() ;
				$this->VideRendusExpires() ;
				if(count($this->InfosRendu) > $this->MaxRendus)
				{
					$ok = 1 ;
				}
				$this->SauveInfosRendu() ;
				return $ok ;
			}
			public function ImpressionEnCours()
			{
				return $this->EstPasNul($this->ZoneParent) && $this->ZoneParent->ImpressionEnCours() ;
			}
			public function EstAccessible()
			{
				if(! $this->NecessiteMembreConnecte)
				{
					return 1 ;
				}
				return $this->ZoneParent->PossedePrivileges($this->Privileges) ;
			}
			public function EstBienRefere()
			{
				return PvVerificateurReferantsSursWeb::Valide($this) ;
			}
			protected function RenduInaccessible()
			{
				$ctn = $this->MessageInaccessible ;
				return $ctn ;
			}
			protected function RenduMalRefere()
			{
				$ctn = $this->MessageMalRefere ;
				return $ctn ;
			}
		}
		
		class PvComposantIUBase extends PvElementAccessible
		{
			public $ContenuAvantRendu = "" ;
			public $ContenuApresRendu = "" ;
			public $FiltreParent = null ;
			public $ZoneParent = null ;
			public $ScriptParent = null ;
			public $ComposantIUParent = null ;
			public $ApplicationParent = null ;
			public $NomElementScript = "" ;
			public $NomElementZone = "" ;
			public $FournisseurDonnees = null ;
			public $TypeComposant = "base" ;
			public $FonctionComposant = "base" ;
			public $SignatureComposant = "" ;
			public $Visible = 1 ;
			public $Bloque = 0 ;
			public $NomClasseFournisseurDonnees = "PvFournisseurDonneesSql" ;
			protected function RenduBloque()
			{
				$this->Bloque = 0 ;
				$ctn = null ;
				if(! $this->EstBienRefere())
				{
					$this->Bloque = 1 ;
					return $this->RenduMalRefere() ;
				}
				if(! $this->EstAccessible())
				{
					$this->Bloque = 1 ;
					return $this->RenduInaccessible() ;
				}
				return $ctn ;
			}
			protected function DefinitRenduBloque($msg)
			{
				$this->Bloque = 1 ;
				return $msg ;
			}
			protected function RenduException($exception)
			{
				return $this->ZoneParent->RenduException($exception) ;
			}
			protected function AfficheExceptionFournisseurDonnees()
			{
				echo $this->RenduExceptionFournisseurDonnees() ;
			}
			protected function RenduExceptionFournisseurDonnees()
			{
				if($this->EstNul($this->FournisseurDonnees))
				{
					return "" ;
				}
				// print_r($this->FournisseurDonnees) ;
				if($this->FournisseurDonnees->ExceptionTrouvee())
				{
					return $this->RenduException($this->FournisseurDonnees->DerniereException) ;
				}
				return "" ;
			}
			public function & InsereActionAvantRendu($nomAction, $action)
			{
				$this->InscritActionAvantRendu($nomAction, $action) ;
				return $action ;
			}
			public function InscritActionAvantRendu($nomAction, & $action)
			{
				$this->ZoneParent->ActionsAvantRendu[$nomAction] = & $action ;
				$action->AdopteComposantIU($nomAction, $this) ;
			}
			public function InscritActionApresRendu($nomAction, & $action)
			{
				$this->ZoneParent->ActionsApresRendu[$nomAction] = & $action ;
				$action->AdopteComposantIU($nomAction, $this) ;
			}
			public function AdopteComposantIU($nom, & $comp)
			{
				$this->NomElementComposantIU = $nom ;
				$this->ComposantIUParent = & $null ;
				$this->AdopteScript($comp->NomElementScript.'_'.$nom, $comp->ScriptParent) ;
			}
			public function AdopteFiltre($nom, & $filtre)
			{
				$this->NomElementFiltre = $nom ;
				$this->FiltreParent = & $filtre ;
				$this->AdopteScript($filtre->NomElementScript.'_'.$nom, $filtre->ScriptParent) ;
			}
			public function AdopteScript($nom, & $script)
			{
				$this->NomElementScript = $nom ;
				$this->ScriptParent = & $script ;
				$this->AdopteZone($script->NomElementZone.'_'.$nom, $script->ZoneParent) ;
			}
			public function AdopteZone($nom, & $zone)
			{
				$this->NomElementZone = $nom ;
				$this->ZoneParent = & $zone ;
				$this->ApplicationParent = & $zone->ApplicationParent ;
			}
			public function PrepareRendu()
			{
			}
			public function RenduDispositif()
			{
				if($this->Visible == 0)
				{
					return '' ;
				}
				$ctn = $this->RenduBloque() ;
				if($this->Bloque == 1)
				{
					return $ctn ;
				}
				if($this->MaxRendusAtteint())
				{
					return $this->MessageMaxRendusAtteint ;
				}
				$this->TraduitMessages() ;
				$this->InsereInfoRenduEnCours() ;
				$ctn .= $this->RenduDispositifBrut() ;
				$this->RetireInfoRenduEnCours() ;
				return $ctn ;
			}
			protected function TraduitMessages()
			{
			}
			protected function RenduDispositifBrut()
			{
				return "" ;
			}
			protected function RenduLienJs($url)
			{
				if($this->EstPasNul($this->ZoneParent) && $this->ZoneParent->InclureCtnJsEntete == 0)
				{
					return $this->ZoneParent->InscritLienJs($url) ;
				}
				return '<script type="text/javascript" src="'.htmlspecialchars($url).'"></script>' ;
			}
			protected function RenduLienJsCmpIE($url, $versionMin=9)
			{
				if($this->EstPasNul($this->ZoneParent) && $this->ZoneParent->InclureCtnJsEntete == 0)
				{
					return $this->ZoneParent->InscritLienJsCmpIE($url, $versionMin) ;
				}
				return '<script type="text/javascript" src="'.htmlspecialchars($url).'"></script>' ;
			}
			protected function RenduLienCSS($url)
			{
				/*
				if($this->EstPasNul($this->ZoneParent) && $this->ZoneParent->InclureCtnJsEntete == 0)
				{
					return $this->ZoneParent->InscritLienCSS($url) ;
				}
				*/
				return '<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($url).'" />' ;
			}
			protected function RenduContenuJs($ctn)
			{
				if($this->EstPasNul($this->ZoneParent) && $this->ZoneParent->InclureCtnJsEntete == 0)
				{
					return $this->ZoneParent->InscritContenuJs($ctn) ;
				}
				return '<script type="text/javascript">
'.$ctn.'
</script>' ;
			}
			protected function RenduContenuJsCmpIE($ctn, $versionMin=9)
			{
				if($this->EstPasNul($this->ZoneParent) && $this->ZoneParent->InclureCtnJsEntete == 0)
				{
					return $this->ZoneParent->InscritContenuJsCmpIE($ctn, $versionMin) ;
				}
				return '<script type="text/javascript">
'.$ctn.'
</script>' ;
			}
			protected function RenduContenuCSS($ctn)
			{
				if($this->EstPasNul($this->ZoneParent) && $this->ZoneParent->InclureCtnJsEntete == 0)
				{
					return $this->ZoneParent->InscritContenuCSS($ctn) ;
				}
				return '<style type="text/css">
'.$ctn.'
</style>' ;
			}
			public function RenduEtiquette()
			{
			}
		}
		
		class PvVerificateurReferantsSursWeb
		{
			public static function Valide($objet)
			{
				$ok = 0 ;
				$urlLocale = get_current_url() ;
				if($objet->RefererHoteLocal == 1)
				{
					$partiesLocales = parse_url($urlLocale) ;
					if(isset($partiesLocales["host"]) || ! in_array($partiesLocales["host"], $objet->HotesReferantsSurs))
					{
						$objet->HotesReferantsSurs[] = $partiesLocales["host"] ;
					}
				}
				$paramsUrl = explode("?", $urlLocale, 2) ;
				if($objet->RefererUrlLocale == 1)
				{
					if(! in_array($paramsUrl[0], $objet->UrlsReferantsSurs))
					{
						$objet->UrlsReferantsSurs[] = $paramsUrl[0] ;
					}
				}
				if($objet->RefererScriptLocal == 1)
				{
					if(! in_array($objet->ZoneParent->ValeurParamScriptAppele, $objet->UrlsReferantsSurs))
					{
						$objet->ScriptsReferantsSurs[] = $objet->ZoneParent->ValeurParamScriptAppele ;
					}
				}
				if(! count($objet->UrlsReferantsSurs) && ! count($objet->HotesReferantsSurs) && ! count($objet->ScriptsReferantsSurs))
				{
					return 1 ;
				}
				$urlReferant = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : "" ;
				if($urlReferant == "")
					return 0 ;
				$nomScriptReferant = $objet->ZoneParent->NomScriptParDefaut ;
				$partiesScriptReferant = explode("?", $urlReferant) ;
				$urlReferant = $partiesScriptReferant[0] ;
				$chaineReqReferant = (isset($partiesScriptReferant[1])) ? $partiesScriptReferant[1] : '' ;
				@parse_str($chaineReqReferant, $paramsReqReferant) ;
				if(isset($paramsReqReferant[$objet->ZoneParent->NomParamScriptAppele]))
				{
					$nomScriptReferant = $paramsReqReferant[$objet->ZoneParent->NomParamScriptAppele] ;
				}
				$partiesReferant = parse_url($urlReferant) ;
				$hoteReferant = "" ;
				if(isset($partiesReferant["host"]))
					$hoteReferant = $partiesReferant["host"] ;
				if($hoteReferant == "")
					return 0 ;
				if(count($objet->UrlsReferantsSurs) > 0 && ! in_array($urlReferant, $objet->UrlsReferantsSurs))
				{
					return 0 ;
				}
				if(count($objet->HotesReferantsSurs) > 0 && ! in_array($hoteReferant, $objet->HotesReferantsSurs))
				{
					return 0 ;
				}
				// echo "Script referant : $nomScriptReferant" ;
				if($urlReferant != $paramsUrl[0] || (count($objet->ScriptsReferantsSurs) > 0 && ! in_array($nomScriptReferant, $objet->ScriptsReferantsSurs)))
				{
					return 0 ;
				}
				return 1 ;
			}
		}
		class PvVerificateurSoumissionWeb
		{
			public static function Valide($objet)
			{
				// if($objet->)
			}
		}
		
		class PvLienFichierCSS extends PvComposantIUBase
		{
			public $TypeComposant = "LienFichierCSS" ;
			public $Href = "" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '<link rel="stylesheet" type="text/css" href="'.$this->Href.'" />' ;
				return $ctn ;
			}
		}
		class PvBaliseCSS extends PvComposantIUBase
		{
			public $TypeComposant = "BaliseCSS" ;
			public $Definitions = "" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<style type="text/css">'.PHP_EOL ;
				$ctn .= $this->Definitions. PHP_EOL ;
				$ctn .= '</style>' ;
				return $ctn ;
			}
		}
		class PvLienFichierJs extends PvComposantIUBase
		{
			public $TypeComposant = "FichierJs" ;
			public $Src = "" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '<script type="text/javascript" src="'.$this->Src.'"></script>' ;
				return $ctn ;
			}
		}
		class PvLienFichierJsCmpIE extends PvLienFichierJs
		{
			public $VersionMin = 9 ;
			protected function RenduDispositifBrut()
			{
				$ctn = '<!--[if lt IE '.intval($this->VersionMin).']>'.PHP_EOL ;
				$ctn .= parent::RenduDispositifBrut().PHP_EOL ;
				$ctn .= '<![endif]-->' ;
				return $ctn ;
			}
		}
		class PvBaliseJs extends PvComposantIUBase
		{
			public $TypeComposant = "BaliseJs" ;
			public $Definitions = "" ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<script language="javascript">'.PHP_EOL ;
				$ctn .= $this->Definitions. PHP_EOL ;
				$ctn .= '</script>' ;
				return $ctn ;
			}
		}
		class PvBaliseJsCmpIE extends PvBaliseJs
		{
			public $VersionMin = 9 ;
			protected function RenduDispositifBrut()
			{
				$ctn = '<!--[if lt IE '.intval($this->VersionMin).']>'.PHP_EOL ;
				$ctn .= parent::RenduDispositifBrut().PHP_EOL ;
				$ctn .= '<![endif]-->' ;
				return $ctn ;
			}
		}
		
		class PvComposantIUDonnees extends PvComposantIUBase
		{
			public $TypeComposant = "ComposantDonnees" ;
			public $Editable = 1 ;
			public $NomParamIdCommande = "Commande" ;
			public $ValeurParamIdCommande = "" ;
			public $ParamsGetSoumetFormulaire = array() ;
			public $ChampsGetSoumetFormulaire = array() ;
			public $DesactBtnsApresSoumiss = 1 ;
			public $ForcerDesactCache = 0 ;
			public $SuffixeParamIdAleat = "id_aleat" ;
			public $InstrsJSAvantSoumetForm = "" ;
			public function CreeFournDonneesDirect($vals, $nomCle='')
			{
				$fourn = new PvFournisseurDonneesDirecte() ;
				if($nomCle == '')
					$nomCle = 'Principale' ;
				$fourn->Valeurs[$nomCle] = $vals ;
				return $fourn ;
			}
			public function CreeFournDonneesSql(& $bd, $reqSelect='', $tablEdit='')
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = & $bd ;
				$fourn->RequeteSelection = $reqSelect ;
				$fourn->TableEdition = $tablEdit ;
				return $fourn ;
			}
			public function DeclareFournDonneesDirect($valeurs, $nomCle='')
			{
				$this->FournisseurDonnees = $this->CreeFournDonneesDirect($valeurs, $nomCle) ;
			}
			public function DeclareFournDonneesSql(& $bd, $reqSelect='', $tablEdit='')
			{
				$this->FournisseurDonnees = $this->CreeFournDonneesSql($bd, $reqSelect, $tablEdit) ;
			}
			public function NomParamIdAleat()
			{
				return $this->IDInstanceCalc."_".$this->SuffixeParamIdAleat ;
			}
			protected function CtnJsActualiseFormulaireFiltres()
			{
				$ctn = '' ;
				$ctn .= 'ActualiseFormulaire'.$this->IDInstanceCalc.'()' ;
				return $ctn ;
			}
			protected function DeclarationSoumetFormulaireFiltres($filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$filtresGets = array() ;
				$nomFiltresGets = array() ;
				$filtresGetsEdit = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					if($filtres[$nomFiltre]->TypeLiaisonParametre == "get")
					{
						$filtresGetsEdit[] = $filtres[$nomFiltre]->ObtientIDElementHtmlComposant() ;
						$nomFiltresGets[] = $filtres[$nomFiltre]->NomParametreLie ;
					}
				}
				foreach($this->ChampsGetSoumetFormulaire as $n => $v)
				{
					$filtresGetsEdit[] = $v ;
				}
				foreach($this->ParamsGetSoumetFormulaire as $n => $v)
				{
					$filtresGets[] = $v ;
				}
				$params = extract_array_without_keys($_GET, $nomFiltresGets) ;
				// print_r($nomFiltresGets) ;
				$filtresGets = array_unique($filtresGets) ;
				$indexMinUrl = (count($params) > 0) ? 0 : 1 ;
				$urlFormulaire = remove_url_params(get_current_url()) ;
				$urlFormulaire .= '?'.http_build_query_string($params) ;
				$instrDesactivs = '' ;
				if($this->DesactBtnsApresSoumiss)
				{
					$instrDesactivs = '		for(var i=0; i<form.elements.length; i++)
		{
			var elem = form.elements[i] ;
			if(elem.type == "submit")
			{
				if(elem.disabled != undefined)
					elem.disabled = "disabled" ;
				else
					elem.setAttribute("disabled", "disabled") ;
			}
		}'.PHP_EOL ;
				}
				if($this->ForcerDesactCache)
				{
					$urlFormulaire .= '&'.urlencode($this->NomParamIdAleat()).'='.htmlspecialchars(rand(0, 999999)) ;
				}
				$ctn = '<script type="text/javascript">
	function SoumetFormulaire'.$this->IDInstanceCalc.'(form)
	{
		var urlFormulaire = "'.$urlFormulaire.'" ;
		///JJJ
		var parametresGet = '.json_encode($filtresGetsEdit).' ;
		if(parametresGet != undefined )
		{
			for(var i=0; i<parametresGet.length; i++)
			{
				if(i >= '.$indexMinUrl.')
				{
					urlFormulaire += "&" ;
				}
				var nomParam = parametresGet[i] ;
				var valeurParam = "" ;
				var elementParam = document.getElementById(nomParam) ;
				if(elementParam != null)
				{
				
					nomParam = elementParam.name ;
					valeurParam = elementParam.value ;
					elementParam.disabled = "disabled" ;
				}
				urlFormulaire += encodeURIComponent(nomParam) + "=" + encodeURIComponent(valeurParam) ;
			}
		}
		// alert(urlFormulaire) ;
'.$instrDesactivs.'		form.action = urlFormulaire ;
		return true ;
	}
	function ActualiseFormulaire'.$this->IDInstanceCalc.'()
	{
'.$this->CtnJsActualiseFormulaireFiltres().' ;
	}
</script>' ;
				return $ctn ;
			}
			protected function DeclarationJsActiveCommande()
			{
				$ctn = '' ;
				$ctn .= '<input type="hidden" name="'.$this->IDInstanceCalc.'_'.$this->NomParamIdCommande.'" value="" />'.PHP_EOL ;
				$ctn .= '<script type="text/javascript">
	if(typeof '.$this->IDInstanceCalc.'_ActiveCommande != "function")
	{
		function '.$this->IDInstanceCalc.'_ActiveCommande(btn)
		{
			document.getElementsByName("'.$this->IDInstanceCalc.'_'.$this->NomParamIdCommande.'")[0].value = (btn.rel != undefined) ? btn.rel : btn.getAttribute("rel") ;
			return true ;
		}
	}
</script>' ;
				return $ctn ;
			}
			protected function ChargeFournisseurDonnees()
			{
				$nomClasse = $this->NomClasseFournisseurDonnees ;
				$this->FournisseurDonnees = null ;
				if(class_exists($nomClasse))
				{
					$this->FournisseurDonnees = new $nomClasse() ;
					$this->FournisseurDonnees->ChargeConfig() ;
				}
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeFournisseurDonnees() ;
			}
			public function PrepareRendu()
			{
				parent::PrepareRendu() ;
			}
		}
	}
	
?>