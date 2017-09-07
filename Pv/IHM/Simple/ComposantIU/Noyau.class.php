<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_BASE'))
	{
		if(! defined('PV_COMPOSANT_UI'))
		{
			include dirname(__FILE__)."/../../ComposantIU.class.php" ;
		}
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/../Noyau.class.php" ;
		}
		if(! defined('PV_FOURNISSEUR_DONNEES_SIMPLE'))
		{
			include dirname(__FILE__)."/../FournisseurDonnees.class.php" ;
		}
		define('PV_COMPOSANT_SIMPLE_IU_BASE', 1) ;
		
		class PvLienCommandeFormulaireDonnees
		{
			public $Libelle ;
			public $Url ;
			public $FenetreCible ;
			public $ClassesCSS = array() ;
			public function __construct($url, $libelle)
			{
				$this->Url = $url ;
				$this->Libelle = $libelle ;
			}
			public function RenduDispositif(& $form, $index)
			{
				return '<a'.((count($this->ClassesCSS) > 0) ? ' class="'.join(' ', $this->ClassesCSS).'"' : '').' href="'.htmlspecialchars($this->Url).'"'.(($this->FenetreCible != '') ? ' target="'.$this->FenetreCible.'"' : '').'>'.$this->Libelle.'</a>' ;
			}
		}
		
		class PvSrcValsSupplLgnDonnees
		{
			public $InclureHtml = 0 ;
			public $SuffixeHtml = "_html" ;
			public $InclureUrl = 0 ;
			public $SuffixeUrl = "_query_string" ;
			public $LignesDonneesBrutes = null ;
			public function Applique(& $composant, $ligneDonnees)
			{
				$this->LigneDonneesBrutes = $ligneDonnees ;
				// print_r($ligneDonneesBrutes) ;
				if($this->InclureHtml)
				{
					$ligneDonnees = array_merge(
						$ligneDonnees,
						array_apply_suffix(array_map('htmlentities', $this->LigneDonneesBrutes), $this->SuffixeHtml)
					) ;
				}
				if($this->InclureUrl)
				{
					$ligneDonnees = array_merge(
						$ligneDonnees,
						array_apply_suffix(
							array_map(
								'urlencode',$this->LigneDonneesBrutes
							), $this->SuffixeUrl
						)
					) ;
				}
				return $ligneDonnees ;
			}
		}
		
		class PvMsgActionNotificationWeb
		{
			public $Contenu ;
			public $TypeErreur = "" ;
		}
		
		class PvCfgAppelAjaxActionSimple
		{
			public $Async = true ;
			public $InstrsSucces = "" ;
			public $InstrsEchec = "" ;
			public $InstrsChargement = "" ;
		}
		
		class PvActionBaseZoneWebSimple extends PvObjet
		{
			public $ZoneParent ;
			public $NomElementZone = "" ;
            /*
             * Script parent
             * 
             * @var PvScriptWebSimple
             */
			public $ScriptParent ;
			public $NomElementScript = "" ;
			public $ComposantIUParent ;
			public $NomElementComposantIU = "" ;
			public $Params = array() ;
			public $Privileges = array() ;
			public $NecessiteMembreConnecte = 0 ;
			public $ApplicationParent ;
			public function IdMembreSession()
			{
				if($this->ZoneParent->NomClasseMembership == '')
				{
					return -1 ;
				}
				$classe = $this->ZoneParent->NomClasseMembership ;
				$membership = new $classe($this->ZoneParent) ;
				$idSession = $membership->GetSessionValue($membership->SessionMemberKey) ;
				return $idSession ;
			}
			public function EstAccessible()
			{
				if(! $this->NecessiteMembreConnecte)
				{
					return 1 ;
				}
				return $this->ZoneParent->PossedePrivileges($this->Privileges) ;
			}
			public function Invoque($params=array(), $valeurPost=array(), $async=1)
			{
				$urlAct = $this->ObtientUrl($params) ;
				return PvApplication::TelechargeUrl($urlAct, $valeurPost, $async) ;
			}
			public function InstrsJsAppelAjax($params=array(), $valeurPost=array(), $cfg=null)
			{
				if($cfg == null)
				{
					$cfg = new PvCfgAppelAjaxActionSimple() ;
				}
				$urlAct = $this->ObtientUrl($params) ;
				$methode = (! empty($valeurPost) && count($valeurPost) > 0) ? "POST" : "GET" ;
				return 'var xhttp_'.$this->IDInstanceCalc.' = new XMLHttpRequest();
xhttp_'.$this->IDInstanceCalc.'.onreadystatechange = function() {
if (xhttp_'.$this->IDInstanceCalc.'.readyState == 4)
{
if(xhttp_'.$this->IDInstanceCalc.'.status == 200)
{
'.$cfg->InstrsSucces.'
}
else
{
'.$cfg->InstrsEchec.'
}
}
else
{
'.$cfg->InstrsChargement.'
}
}
xhttp_'.$this->IDInstanceCalc.'.open("'.$methode.'", '.svc_json_encode($urlAct).', '.svc_json_encode($cfg->Async).') ;
xhttp_'.$this->IDInstanceCalc.'.send() ;' ;
			}
			public function InsereAppelAjax($params=array(), $valeurPost=array(), $cfg=null)
			{
				$this->ZoneParent->InsereContenuCSS($params, $valeurPost, $cfg) ;
			}
			public function ObtientUrl($params=array())
			{
				if($this->EstPasNul($this->ScriptParent))
				{
					$url = update_url_params(
						$this->ScriptParent->ObtientUrl(),
						array_merge(
							$this->Params,
							$params,
							array($this->ZoneParent->NomParamActionAppelee => $this->NomElementZone)
						)
					) ;
					return $url ;
				}
				if($this->EstNul($this->ZoneParent))
				{
					return false ;
				}
				$chaineParams = http_build_query_string(array_merge($this->Params, $params)) ;
				if($chaineParams != '')
					$chaineParams = "&".$chaineParams ;
				$url = $this->ZoneParent->ObtientUrl()."?".urlencode($this->ZoneParent->NomParamActionAppelee).'='.urlencode($this->NomElementZone).$chaineParams ;
				return $url ;
			}
			public function ObtientUrlFmt($params=array(), $autresParams=array())
			{
				$url = $this->ObtientUrl($autresParams) ;
				foreach($params as $nom => $val)
				{
					$url .= '&'.urlencode($nom).'='.$val ;
				}
				return $url ;
			}
			public function AdopteZone($nom, & $zone)
			{
				$this->ZoneParent = & $zone ;
				$this->NomElementZone = $nom ;
				$this->ApplicationParent = & $zone->ApplicationParent ;
			}
			public function AdopteScript($nom, & $script)
			{
				$this->ScriptParent = & $script ;
				$this->NomElementScript = $nom ;
				$this->AdopteZone($this->ScriptParent->NomElementZone."_".$this->NomElementScript, $script->ZoneParent) ;
			}
			public function AdopteComposantIU($nom, & $composant)
			{
				$this->ComposantIUParent = & $composant ;
				$this->NomElementComposantIU = $nom ;
				$this->AdopteScript($this->ComposantIUParent->NomElementScript."_".$this->NomElementComposantIU, $composant->ScriptParent) ;
			}
			public function Accepte($valeurAction)
			{
				// echo 'Nom elem : '.$valeurAction ;
				return ($this->NomElementZone == $valeurAction) ? 1 : 0 ;
			}
			public function Execute()
			{
			}
		}
		class PvActionImprimeScript extends PvActionBaseZoneWebSimple
		{
			public function Execute()
			{
				$this->ZoneParent->DemarreRenduImpression() ;
				echo $this->ZoneParent->RenduEnteteDocument() ;
				echo '<body onload="window.print() ;">' ;
				echo $this->ZoneParent->ScriptPourRendu->RenduDispositif() ;
				echo $this->ZoneParent->RenduPiedDocument() ;
				echo '</body>
</html>' ;
				$this->ZoneParent->TermineRenduImpression() ;
				exit ;
			}
		}
		class PvActionNotificationWeb extends PvActionBaseZoneWebSimple
		{
			protected $Message ;
			public function & ObtientMessage()
			{
				return $this->Message ;
			}
			public function PossedeMessage()
			{
				return $this->Message->Contenu != "" ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Message = new PvMsgActionNotificationWeb() ;
			}
			protected function ConfirmeMessage($msg, $typeErreur="")
			{
				$this->Message->Contenu = $msg ;
				$this->Message->TypeErreur = $typeErreur ;
			}
			public function ConfirmeSucces($msg)
			{
				$this->ConfirmeMessage($msg, "") ;
			}
			public function RenseigneErreur($msg)
			{
				$this->ConfirmeMessage($msg, "erreur") ;
			}
			public function ConfirmeErreur($msg)
			{
				$this->ConfirmeMessage($msg, "erreur") ;
			}
			public function ConfirmeException($msg)
			{
				$this->ConfirmeMessage($msg, "exception") ;
			}
			public function Execute()
			{
			}
		}
		class PvActionRenduPageWeb extends PvActionBaseZoneWebSimple
		{
			public $TitreDocument ;
			public $ContenusCSS = array() ;
			public $ContenusJs = array() ;
			public $CtnExtraHead ;
			public $InclureCtnJsEntete = 0 ;
			public $CtnAttrsBody = "" ;
			public function InscritContenuCSS($contenu)
			{
				$ctnCSS = new PvBaliseCSS() ;
				$ctnCSS->Definitions = $contenu ;
				$this->ContenusCSS[] = $ctnCSS ;
			}
			public function InscritLienCSS($href)
			{
				$ctnCSS = new PvLienFichierCSS() ;
				$ctnCSS->Href = $href ;
				$this->ContenusCSS[] = $ctnCSS ;
			}
			public function InscritContenuJs($contenu)
			{
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritContenuJsCmpIE($contenu, $versionMin=9)
			{
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritLienJs($src)
			{
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function InscritLienJsCmpIE($src, $versionMin=9)
			{
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
			}
			public function RenduLienCSS($href)
			{
				$ctnCSS = new PvLienFichierCSS() ;
				$ctnCSS->Href = $href ;
				return $ctnCSS->RenduDispositif() ;
			}
			public function RenduContenuCSS($contenu)
			{
				$ctnCSS = new PvBaliseCSS() ;
				$ctnCSS->Definitions = $contenu ;
				return $ctnCSS->RenduDispositif() ;
			}
			public function RenduContenuJsInclus($contenu)
			{
				$ctn = '' ;
				$ctnJs = new PvBaliseJs() ;
				$ctnJs->Definitions = $contenu ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduContenuJsCmpIEInclus($contenu, $versionMin=9)
			{
				$ctn = '' ;
				$ctnJs = new PvBaliseJsCmpIE() ;
				$ctnJs->Definitions = $contenu ;
				$ctnJs->VersionMin = $versionMin ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduLienJsInclus($src)
			{
				$ctn = '' ;
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $src ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			public function RenduLienJsCmpIEInclus($src, $versionMin=9)
			{
				$ctn = '' ;
				$ctnJs = new PvLienFichierJsCmpIE() ;
				$ctnJs->Src = $src ;
				$ctnJs->VersionMin = $versionMin ;
				$this->ContenusJs[] = $ctnJs ;
				if(! $this->InclureCtnJsEntete)
				{
					$this->ContenusJs[] = $ctnJs ;
				}
				else
				{
					$ctn = $ctnJs->RenduDispositif() ;
				}
				return $ctn ;
			}
			protected function RenduCtnsCSS()
			{
				$ctn = '' ;
				for($i=0; $i<count($this->ContenusCSS); $i++)
				{
					$ctnCSS = $this->ContenusCSS[$i] ;
					$ctn .= $ctnCSS->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduCtnsJs()
			{
				$ctn = '' ;
				for($i=0; $i<count($this->ContenusJs); $i++)
				{
					$ctnJs = $this->ContenusJs[$i] ;
					$ctn .= $ctnJs->RenduDispositif().PHP_EOL ;
				}
				return $ctn ;
			}
			protected function RenduEnteteDoc()
			{
				$ctn = '' ;
				$ctn .= '<!doctype html>'.PHP_EOL ;
				$ctn .= '<head>'.PHP_EOL ;
				$ctn .= '<title>'.$this->TitreDocument.'</title>'.PHP_EOL ;
				$ctn .= $this->RenduCtnsCSS() ;
				if($this->InclureCtnJsEntete == 1)
				{
					$ctn .= $this->RenduCtnsJs() ;
				}
				$ctn .= $this->CtnExtraHead ;
				$ctn .= '</head>'.PHP_EOL ;
				$ctn .= '<body'.(($this->CtnAttrsBody != '') ? ' '.$this->CtnAttrsBody :  '').'>';
				return $ctn ;
			}
			protected function RenduPiedDoc()
			{
				$ctn = '' ;
				if($this->InclureCtnJsEntete == 0)
				{
					$ctn .= $this->RenduCtnsJs() ;
				}
				$ctn .= '</body>'.PHP_EOL ;
				$ctn .= '</html>' ;
				return $ctn ;
			}
			protected function PrepareDoc()
			{
			}
			public function Execute()
			{
				$this->PrepareDoc() ;
				echo $this->RenduEnteteDoc() ;
				echo $this->RenduCorpsDoc() ;
				echo $this->RenduPiedDoc() ;
				exit ;
			}
			protected function RenduCorpsDoc()
			{
				return '' ;
			}
		}
		class PvActionPageWeb extends PvActionRenduPageWeb
		{
		}
		class PvActionSoumetFormSimple extends PvActionRenduPageWeb
		{
			public $ParamsGet = array() ;
			public $ParamsPost = array() ;
			public $DelaiEnvoi = 0 ;
			public $UrlEnvoi = "" ;
			public $MsgChargement = "Veuillez patienter..." ;
			public $CtnAttrsBody = 'onload="demarreSoumissForm() ;"' ;
			protected function RenduCorpsDoc()
			{
				$urlEnvoi = update_url_params($this->UrlEnvoi, $this->ParamsGet) ;
				$ctn = '' ;
				$ctn .= '<div class="msg-chargement" align="center">'.$this->MsgChargement.'</div>'.PHP_EOL ;
				$ctn .= '<form action="'.htmlentities($urlEnvoi).'" id="formSoumis" method="post">'.PHP_EOL ;
				foreach($this->ParamsPost as $n => $v)
				{
					$ctn .= '<input type="hidden" name="'.htmlentities($n).'" value="'.htmlentities($v).'" />'.PHP_EOL ;
				}
				$ctn .= '</form>' ;
				$ctn .= '<script type="text/javascript">
	function demarreSoumissForm()
	{
		var delai = '.intval($this->DelaiEnvoi).' ;
		var formSoumisNode = document.getElementById("formSoumis") ;
		if(delai > 0) {
			setTimeout(function() { formSoumisNode.submit() ; }, delai * 1000) ;
		}
		else
		{
			formSoumisNode.submit() ;
		}
	}
</script>' ;
				return $ctn ;
			}
		}
		class PvActionResultatJSONZoneWeb extends PvActionBaseZoneWebSimple
		{
			public $Resultat = null ;
			public $InclureEnteteContenu = 0 ;
			public function Execute()
			{
				if(! is_object($this->Resultat))
				{
					$this->Resultat = new StdClass() ;
				}
				$this->ConstruitResultat() ;
				if($this->InclureEnteteContenu)
				{
					Header('Content-Type:application/json'."\r\n") ;
				}
                echo @svc_json_encode($this->Resultat) ;
				$this->ZoneParent->AnnulerRendu = 1 ;
				exit ;
			}
			protected function ConstruitResultat()
			{
			}
		}
		class PvActionEnvoiJSON extends PvActionResultatJSONZoneWeb
		{
		}
		class PvActionEnvoiFichierBaseZoneWeb extends PvActionBaseZoneWebSimple
		{
			public $UtiliserTypeMime = 0 ;
			public $UtiliserFichierSource = 1 ;
			public $UtiliserFichierAttache = 1 ;
			public $TypeMime = "" ;
			public $DispositionFichierAttache = "inline" ;
			public $NomFichierAttache = "" ;
			public $ExtensionFichierAttache = "" ;
			public $CheminFichierSource = "" ;
			public $TailleContenu = 0 ;
			public $SupprimerCaractsSpec = 1 ;
			public $AutresEntetes = array() ;
			protected function CalculeTailleContenu()
			{
			}
			public function Execute()
			{
				$this->DetermineFichierAttache() ;
				$this->CalculeTailleContenu() ;
				$this->AfficheEntetes() ;
				$this->AfficheContenu() ;
				exit ;
			}
			protected function DetermineFichierAttache()
			{
				/*
				if($this->ExtensionFichierAttache == "")
				{
					$this->NomFichierAttache = $this->NomElementZone.".".$this->ExtensionFichierAttache ;
				}
				*/
				$infosFich = @pathinfo($this->CheminFichierSource) ;
				if($this->ExtensionFichierAttache == "" && $this->CheminFichierSource != "")
				{
					$this->ExtensionFichierAttache = $infosFich["extension"] ;
				}
			}
			public function SupprimeCaractsSpec($valeur)
			{
				return preg_replace('/[^a-z0-9_\.]/i', '_', $valeur) ;
			}
			protected function AfficheEntetes()
			{
				// echo $this->SupprimeCaractsSpec($this->NomFichierAttache) ;
				// exit ;
				if($this->UtiliserFichierSource == 1 && $this->TypeMime != "")
				{
					Header("Content-type:".$this->TypeMime."\r\n") ;
				}
				if($this->UtiliserFichierAttache == 1 && $this->NomFichierAttache != "")
				{
					Header("Content-disposition:".$this->DispositionFichierAttache."; filename=".$this->SupprimeCaractsSpec($this->NomFichierAttache).(($this->ExtensionFichierAttache != '') ? '.'.$this->ExtensionFichierAttache : '')."\r\n") ;
				}
				if($this->TailleContenu > 0)
				{
					Header("Content-Length:".$this->TailleContenu."\r\n") ;
				}
				foreach($this->AutresEntetes as $i => $entete)
				{
					Header($entete."\r\n") ;
				}
			}
			protected function AfficheContenu()
			{
				if($this->UtiliserFichierSource && $this->CheminFichierSource != "")
				{
					if(file_exists($this->CheminFichierSource))
					{
						$fr = @fopen($this->CheminFichierSource, "rb") ;
						if($fr !== false)
						{
							while(! feof($fr))
							{
								echo fgets($fr) ;
							}
							fclose($fr) ;
						}
						else
						{
							die("Impossible d'acceder au fichier ".$this->CheminFichierSource.". Verifier que les droits en acces et lecture sont bien octroyes") ;
						}
					}
					else
					{
						die("Le fichier ".$this->CheminFichierSource." n'existe pas.") ;
					}
				}
			}
		}
		class PvActionTelechargFichier extends PvActionEnvoiFichierBaseZoneWeb
		{
			public $TypeMime = "application/octet-stream" ;
			public $AutresEntetes = array("Pragma: public", "Expires: 0", "Cache-Control: must-revalidate, post-check=0, pre-check=0", "Content-Transfer-Encoding: binary") ;
			public $DispositionFichierAttache = "attachment" ;
		}
		class PvActionEnvoiFichierJSZoneWeb extends PvActionEnvoiFichierBaseZoneWeb
		{
			public $TypeMime = "text/javascript" ;
			public $ExtensionFichierAttache = "js" ;
		}
		class PvActionEnvoiFichierCSSZoneWeb extends PvActionEnvoiFichierBaseZoneWeb
		{
			public $TypeMime = "text/css" ;
			public $ExtensionFichierAttache = "css" ;
		}
		
		class PvDessinateurRenduBase
		{
			public $FiltresCaches = array() ;
			public function Execute(& $script, & $composant, $parametres)
			{
				return "" ;
			}
			protected function RenduFiltre(& $filtre, & $composant)
			{
				$ctn = '' ;
				// print $filtre->NomParametreLie.' : '.$filtre->EstEtiquette.'<br>' ;
				if($composant->Editable && $filtre->EstEtiquette == 0)
				{
					// $ctn .= $filtre->Lie() ;
					$ctn .= $filtre->Rendu() ;
				}
				else
				{
					$ctn .= $filtre->Etiquette() ;
				}
				return $ctn ;
			}
		}
		class PvDessinateurRenduHtmlFiltresDonnees extends PvDessinateurRenduBase
		{
			public $Largeur = "" ;
			public $MaxFiltresParLigne = 2 ;
			public $InclureRenduLibelle = 1 ;
			public $LargeurLibelles = "" ;
			public $LargeurEditeurs = "" ;
			public $InclureSeparateurFiltres = 1 ;
			public $ValeurSeparateurFiltres = "&nbsp;" ;
			protected function RenduMarquesFiltre(& $marques)
			{
				$ctn = '' ;
				foreach($marques as $i => $marque)
				{
					$ctn .= ' <span style="color:'.$marque->CouleurPolice.';">'.$marque->Contenu.'</span>' ;
				}
				return $ctn ;
			}
			protected function RenduLibelleFiltre(& $filtre)
			{
				$ctn = '' ;
				$ctn .= '<label for="'.$filtre->ObtientIDElementHtmlComposant().'">' ;
				$ctn .= $this->RenduMarquesFiltre($filtre->PrefixesLibelle) ;
				$ctn .= $filtre->ObtientLibelle() ;
				$ctn .= $this->RenduMarquesFiltre($filtre->SuffixesLibelle) ;
				$ctn .= '</label>' ;
				return $ctn ;				
			}
			public function Execute(& $script, & $composant, $parametres)
			{
				$filtres = $composant->ExtraitFiltresDeRendu($parametres, $this->FiltresCaches) ;
				$ctn = '' ;
				$ctn .= '<table' ;
				if($this->Largeur != '')
				{
					$ctn .= ' width="'.$this->Largeur.'"' ;
				}
				$ctn .= '>'.PHP_EOL ;
				$colonnesTotalFusionnees = $this->MaxFiltresParLigne * 2 ;
				if($this->InclureSeparateurFiltres)
				{
					$colonnesTotalFusionnees += ($this->MaxFiltresParLigne - 1) ;
				}
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				// echo count($filtres) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $filtres[$nomFiltre] ;
					if($filtre->LectureSeule)
					{
						$ctn .= '<input type="hidden" id="'.htmlentities($filtre->ObtientIDComposant()).'" name="'.htmlentities($filtre->ObtientNomComposant()).'" value="'.htmlentities($filtre->Lie()).'" />'.PHP_EOL ;
						continue ;
					}
					if($filtreRendus % $this->MaxFiltresParLigne == 0)
					{
						$ctn .= '<tr>'.PHP_EOL ;
					}
					if($filtreRendus % $this->MaxFiltresParLigne > 0)
					{
						$ctn .= '<td>'.$this->ValeurSeparateurFiltres.'</td>'.PHP_EOL ;
					}
					if($this->InclureRenduLibelle)
					{
						$ctn .= '<td' ;
						$ctn .= ' valign="top"' ;
						$ctn .= '>'.PHP_EOL ;
						$ctn .= '<label for="'.$filtre->ObtientIDElementHtmlComposant().'">'.$this->RenduLibelleFiltre($filtre).'</label>'.PHP_EOL ;
						$ctn .= '</td>'.PHP_EOL ;
					}
					$ctn .= '<td' ;
					$ctn .= ' valign="top"' ;
					$ctn .= '>'.PHP_EOL ;
					$ctn .= $this->RenduFiltre($filtre, $composant).PHP_EOL ;
					$ctn .= '</td>'.PHP_EOL ;
					$filtreRendus++ ;
					if($filtreRendus % $this->MaxFiltresParLigne == 0)
					{
						$ctn .= '</tr>'.PHP_EOL ;
					}
				}
				if($filtreRendus % $this->MaxFiltresParLigne != 0)
				{
					$colonnesFusionnees = ($this->MaxFiltresParLigne - ($filtreRendus % $this->MaxFiltresParLigne)) * (($this->InclureRenduLibelle) ? 2 : 1) ;
					$colonnesFusionnees += ($this->MaxFiltresParLigne - 1) ;
					$ctn .= '<td colspan="'.$colonnesFusionnees.'">&nbsp;</td>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$ctn .= '</table>' ;
				return $ctn ;
			}
			public function VersionTexte(& $composant, $parametres)
			{
				$filtres = $composant->ExtraitFiltresDeRendu($parametres) ;
				$nomFiltres = array_keys($filtres) ;
				$ctn = '' ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$ctn .= $this->RenduLibelleFiltre($filtre) ;
					$ctn .= ' : ' ;
					$ctn .= $filtre->Etiquette() ;
					$ctn .= "\r\n" ;
				}
				return $ctn ;
			}
		}
		class PvDessinateurLigneFiltresDonnees extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public $Largeur = "" ;
			public $InclureRenduLibelle = 1 ;
			public function Execute(& $script, & $composant, $parametres)
			{
				$filtres = $composant->ExtraitFiltresDeRendu($parametres, $this->FiltresCaches) ;
				$ctn = '' ;
				$ctn .= '<table' ;
				if($this->Largeur != '')
				{
					$ctn .= ' width="'.$this->Largeur.'"' ;
				}
				$ctn .= '>'.PHP_EOL ;
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $parametres[$i] ;
					if(! $filtre->RenduPossible())
					{
						continue ;
					}
					if($this->InclureRenduLibelle)
					{
						$ctn .= '<tr>'.PHP_EOL ;
						$ctn .= '<td' ;
						$ctn .= ' valign="top"' ;
						$ctn .= '>'.PHP_EOL ;
						$ctn .= $this->RenduLibelleFiltre($filtre).PHP_EOL ;
						$ctn .= '</td>'.PHP_EOL ;
						$ctn .= '</tr>'.PHP_EOL ;
					}
					$ctn .= '<tr>'.PHP_EOL ;
					$ctn .= '<td' ;
					$ctn .= ' valign="top"' ;
					$ctn .= '>'.PHP_EOL ;
					$ctn .= $this->RenduFiltre($filtre, $composant).PHP_EOL ;
					$ctn .= '</td>'.PHP_EOL ;
					$ctn .= '</tr>'.PHP_EOL ;
				}
				$ctn .= '</table>' ;
				return $ctn ;
			}
		}
		class PvDessinFltsDonneesHtml extends PvDessinateurRenduHtmlFiltresDonnees
		{
		}
		class PvDessinFltsIllustrHtml extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public static $StyleGlobalInclus = 0 ;
			public $AlignIcone = "droite" ;
			public static function RenduStyleGlobal()
			{
				$val = PvDessinFltsIllustrHtml::$StyleGlobalInclus ;
				if($val == 1)
				{
					return "" ;
				}
				return '<style type="text/css">
.editeur-illustr { 
    position: relative;
	margin-bottom:12px ;
}
.editeur-illustr .icone-illustr {
  position: absolute;
  padding: 10px;
  pointer-events: none;
}
.illustr-gauche .icone-illustr  { left:  0px;}
.illustr-droite .icone-illustr { right: 0px;}

/* add padding  */
.illustr-gauche > input, .illustr-gauche > select { padding-left:  30px; }
.illustr-droite > input, .illustr-droite > select { padding-right: 30px; }
</style>' ;
				PvDessinFltsIllustrHtml::$StyleGlobalInclus = 1 ;
			}
			public function Execute(& $script, & $composant, $parametres)
			{
				$filtres = $composant->ExtraitFiltresDeRendu($parametres, $this->FiltresCaches) ;
				$ctn = '' ;
				$ctn .= PvDessinFltsIllustrHtml::RenduStyleGlobal() ;
				$alignIcone = ($this->AlignIcone == "droite") ? "droite" : "gauche" ;
				$ctn .= '<div' ;
				if($this->Largeur != '')
				{
					$ctn .= ' style="width:'.$this->Largeur.'px"' ;
				}
				$ctn .= '>'.PHP_EOL ;
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $filtres[$nomFiltre] ;
					if($filtre->LectureSeule)
					{
						$ctn .= '<input type="hidden" id="'.htmlentities($filtre->ObtientIDComposant()).'" name="'.htmlentities($filtre->ObtientNomComposant()).'" value="'.htmlentities($filtre->Lie()).'" />'.PHP_EOL ;
						continue ;
					}
					$ctn .= '<div class="editeur-illustr">'.PHP_EOL ;
					if($alignIcone == "gauche")
					{
						$ctn .= $this->RenduIconeFiltre($alignIcone, $filtre) ;
					}
					$ctn .= $this->RenduFiltre($filtre, $composant).PHP_EOL ;
					if($alignIcone == "droite")
					{
						$ctn .= $this->RenduIconeFiltre($alignIcone, $filtre) ;
					}
					$ctn .= '</div>'.PHP_EOL ;
					$filtreRendus++ ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function RenduIconeFiltre($alignIcone, & $filtre)
			{
				$ctn = '' ;
				$ctn .= '<i class="illustr-'.$alignIcone.' '.$filtre->NomClasseCSSIcone.'">'.(($filtre->CheminIcone != "") ? '<img src="'.$filtre->CheminIcone.'" />' : '').'</i>' ;
				return $ctn ;
			}
		}
		
		class PvDessinateurRenduModeleFiltresDonnees extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public $ContenuModele ;
			public $ContenuAvantModeleFiltre ;
			public $ContenuModeleFiltre ;
			public $ContenuApresModeleFiltre ;
			public $ContenuModeleUse ;
			protected function DetecteContenuModeleUse(& $filtres)
			{
				$this->ContenuModeleUse = $this->ContenuModele ;
				if($this->ContenuModeleUse == '')
				{
					$this->ContenuModeleUse = $this->ContenuAvantModeleFiltre ;
					$nomFiltres = array_keys($filtres) ;
					foreach($nomFiltres as $i => $nomFiltre)
					{
						$filtre = & $filtres[$nomFiltre] ;
						$this->ContenuModeleUse .= _parse_pattern($this->ContenuModeleFiltre, array("Libelle" => $filtre->NomParametreLie.".Libelle", "Valeur" => $filtre->NomParametreLie.".Valeur")) ;
					}
					$this->ContenuModeleUse .= $this->ContenuApresModeleFiltre ;
				}
			}
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$filtres = $composant->ExtraitFiltresDeRendu($parametres, $this->FiltresCaches) ;
				$this->DetecteContenuModeleUse($filtres) ;
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				$params = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $parametres[$i] ;
					$params = array_merge($params, $this->ExtraitParamsModeleFiltre($filtre)) ;
				}
				$ctn = _parse_pattern($this->ContenuModeleUse, $params) ;
				return $ctn ;
			}
			protected function ExtraitParamsModeleFiltre(& $filtre)
			{
				$params = array() ;
				$params[$filtre->NomParametreLie.".Libelle"] = $this->RenduLibelleFiltre($filtre) ;
				$params[$filtre->NomParametreLie.".Etiquette"] = $this->RenduLibelleFiltre($filtre) ;
				$params[$filtre->NomParametreLie.".Valeur"] = $filtre->Lie() ;
				$params[$filtre->NomParametreLie.".ValUrl"] = urlencode($params[$filtre->NomParametreLie.".Valeur"]) ;
				$params[$filtre->NomParametreLie.".ValEntiteHtml"] = htmlentities($params[$filtre->NomParametreLie.".Valeur"]) ;
				$params[$filtre->NomParametreLie.".ValAttrHtml"] = htmlspecialchars($params[$filtre->NomParametreLie.".Valeur"]) ;
				return $params ;
			}
		}
		class PvDessinModeleFltsDonnees extends PvDessinateurRenduModeleFiltresDonnees
		{
		}
		
		class PvDessinFiltresDonneesBootstrap extends PvDessinateurRenduHtmlFiltresDonnees
		{
			public $ColXs = "" ;
			public $ColSm = "" ;
			public $ColMd = "" ;
			public $ColLd = "" ;
			public $UtiliserContainerFluid = 1 ;
			public $InclureRenduLibelle = 1 ;
			public $EditeurSurligne = 0 ;
			public $ColXsLibelle = 4 ;
			public $MaxFiltresParLigne = 1 ;
			protected function ObtientColXs($maxFiltres)
			{
				return ($this->ColXs != '') ? $this->ColXs :
					(($this->ColLd != '') ? $this->ColLd : 
						(($this->ColMd != '') ? $this->ColMd : 
							($this->ColSm != '') ? $this->ColSm : intval(12 / $maxFiltres)
						)
					) ;
			}
			protected function RenduFiltre(& $filtre, & $composant)
			{
				$ctn = '' ;
				if($composant->Editable)
				{
					if($filtre->EstNul($filtre->Composant))
					{
						$filtre->DeclareComposant($filtre->NomClasseComposant) ;
					}
					if($filtre->EstPasNul($filtre->Composant))
					{
						if(! in_array("form-control", $filtre->Composant->ClassesCSS))
						{
							$filtre->Composant->ClassesCSS[] = "form-control" ;
						}
					}
					$ctn .= $filtre->Rendu() ;
				}
				else
				{
					$ctn .= $filtre->Etiquette() ;
				}
				return $ctn ;
			}
			public function Execute(& $script, & $composant, $parametres)
			{
				if($this->EditeurSurligne == 1 && $this->InclureLibelle == 1)
				{
					return $this->RenduEditeursSurligne($script, $composant, $parametres) ;
				}
				$filtres = $composant->ExtraitFiltresDeRendu($parametres, $this->FiltresCaches) ;
				$ctn = '' ;
				$ctn .= '<fieldset>'.PHP_EOL ;
				$ctn .= '<div' ;
				$ctn .= ' class="'.(($this->UtiliserContainerFluid) ? 'container-fluid' : 'container').'"' ;
				$ctn .= '>'.PHP_EOL ;
				if($this->MaxFiltresParLigne <= 0)
				{
					$this->MaxFiltresParLigne = 1 ;
				}
				$colXs = $this->ObtientColXs($this->MaxFiltresParLigne) ;
				$maxColonnes = 12 / $colXs ;
				$nomFiltres = array_keys($filtres) ;
				$filtreRendus = 0 ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = $filtres[$nomFiltre] ;
					if($filtre->LectureSeule)
					{
						$ctn .= '<input type="hidden" id="'.htmlspecialchars($filtre->ObtientIDComposant()).'" name="'.htmlspecialchars($filtre->ObtientNomComposant()).'" value="'.htmlspecialchars($filtre->Lie()).'" />'.PHP_EOL ;
						continue ;
					}
					if($filtreRendus % $maxColonnes == 0)
					{
						$ctn .= '<div class="row">'.PHP_EOL ;
					}
					$ctn .= '<div class="col-xs-'.$colXs.(($this->ColSm != '') ? ' col-sm-'.$this->ColSm : '').''.(($this->ColMd != '') ? ' col-md-'.$this->ColMd : '').(($this->ColLd != '') ? ' col-ld-'.$this->ColLd : '').'">'.PHP_EOL ;
					$ctn .= '<div class="form-group">'.PHP_EOL ;
					if($this->InclureRenduLibelle)
					{
						if($this->EditeurSurligne == 0)
						{
							$ctn .= '<div class="container-fluid">'.PHP_EOL .'<div class="row">'.PHP_EOL .'<div class="col-xs-'.$this->ColXsLibelle.'">'.PHP_EOL ;
							$ctn .= $this->RenduLibelleFiltre($filtre).PHP_EOL ;
							$ctn .= '</div>'.PHP_EOL .'<div class="col-xs-'.(12 - $this->ColXsLibelle).'">'.PHP_EOL ;
						}
						else
						{
							$ctn .= '<div>'.PHP_EOL .$this->RenduLibelleFiltre($filtre).PHP_EOL .'</div>'.PHP_EOL ;
						}
					}
					if($this->EditeurSurligne == 0)
					{
						$ctn .= $this->RenduFiltre($filtre, $composant).PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL .'</div>'.PHP_EOL .'</div>'.PHP_EOL ;
					}
					else
					{
						$ctn .= '<div>'.PHP_EOL 
							.$this->RenduFiltre($filtre, $composant).PHP_EOL
							.'</div>'.PHP_EOL ;
					}
					$ctn .= '</div>'.PHP_EOL ;
					$ctn .= '</div>'.PHP_EOL ;
					$filtreRendus++ ;
					if($filtreRendus % $maxColonnes == 0)
					{
						$ctn .= '</div>'.PHP_EOL ;
					}
				}
				if($filtreRendus % $maxColonnes != 0)
				{
					$colonnesFusionnees = $maxColonnes - ($filtreRendus % $maxColonnes) ;
					$ctn .= '<div class="col-xs-'.$colonnesFusionnees.'">&nbsp;</div>'.PHP_EOL ;
					$ctn .= '</div>'.PHP_EOL ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</fieldset>' ;
				return $ctn ;
			}
		}
		
		class PvDessinateurRenduHtmlCommandes extends PvDessinateurRenduBase
		{
			public $InclureIcones = 1 ;
			public $InclureLibelle = 1 ;
			public $HauteurIcone = 20 ;
			public $CheminIconeParDefaut = "images/execute_icon.png" ;
			public $SeparateurIconeLibelle = "&nbsp;&nbsp;" ;
			public $SeparateurCommandes = "&nbsp;&nbsp;&nbsp;&nbsp;" ;
			protected function DebutExecParam(& $script, & $composant, $i, $param)
			{
				return "" ;
			}
			protected function FinExecParam(& $script, & $composant, $i, $param)
			{
				return "" ;
			}
			protected function PeutAfficherCmd(& $commande)
			{
				if($commande->Visible == 0 || $commande->EstBienRefere() == 0 || $commande->EstAccessible() == 0)
				{
					return 0 ;
				}
				return 1 ;
			}
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$commandes = $parametres ;
				$nomCommandes = array_keys($commandes) ;
				foreach($nomCommandes as $i => $nomCommande)
				{
					$commande = & $commandes[$nomCommande] ;
					if($this->PeutAfficherCmd($commande) == 0)
					{
						continue ;
					}
					if($ctn != '')
					{
						$ctn .= $this->SeparateurCommandes. PHP_EOL ;
					}
					if($commande->UtiliserRenduDispositif)
					{
						$ctn .= $commande->RenduDispositif() ;
					}
					else
					{
						$ctn .= $this->DebutExecParam($script, $composant, $i, $commande) ;
						if($commande->ContenuAvantRendu != '')
						{
							$ctn .= $commande->ContenuAvantRendu ;
						}
						$ctn .= '<button id="'.$commande->IDInstanceCalc.'" class="Commande '.$commande->NomClsCSS.'" type="submit" rel="'.$commande->NomElementSousComposantIU.'"' ;
						$ctn .= ' onclick="'.$composant->IDInstanceCalc.'_ActiveCommande(this) ;"' ;
						if($this->InclureLibelle == 0)
						{
							$ctn .= ' title="'.htmlspecialchars($commande->Libelle).'"' ;
						}
						$ctn .= '>'.PHP_EOL ;
						if($this->InclureIcones)
						{
							$cheminIcone = $this->CheminIconeParDefaut ;
							if($commande->CheminIcone != '')
							{
								$cheminIcone = $commande->CheminIcone ;
							}
							if(file_exists($cheminIcone))
							{
								$ctn .= '<img src="'.$cheminIcone.'" height="'.$this->HauteurIcone.'" border="0" />' ;
							}
							if($commande->InclureLibelle == 1)
							{
								$ctn .= $this->SeparateurIconeLibelle ;
							}
						}
						if($this->InclureLibelle)
						{
							$ctn .= $commande->Libelle ;
						}
						$ctn .= '</button>'.PHP_EOL ;
						if($commande->ContenuApresRendu != '')
						{
							$ctn .= $commande->ContenuApresRendu ;
						}
						$ctn .= $this->FinExecParam($script, $composant, $i, $commande) ;
					}
				}
				return $ctn ;
			}
		}
		class PvDessinCmdsHtml extends PvDessinateurRenduHtmlCommandes
		{
		}
		
		class PvDessinCommandesBootstrap extends PvDessinateurRenduHtmlCommandes
		{
			public $InclureGlyphicons = 0 ;
			public $GlyphiconParDefaut = "glyphicon-flash" ;
			public $ClasseCSSPanel = "panel-default" ;
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$commandes = $parametres ;
				$nomCommandes = array_keys($commandes) ;
				foreach($nomCommandes as $i => $nomCommande)
				{
					$commande = & $commandes[$nomCommande] ;
					if($this->PeutAfficherCmd($commande) == 0)
					{
						continue ;
					}
					if($ctn != '')
					{
						$ctn .= $this->SeparateurCommandes. PHP_EOL ;
					}
					if($commande->UtiliserRenduDispositif)
					{
						$ctn .= $commande->RenduDispositif() ;
					}
					else
					{
						$ctn .= $this->DebutExecParam($script, $composant, $i, $commande) ;
						if($commande->ContenuAvantRendu != '')
						{
							$ctn .= $commande->ContenuAvantRendu ;
						}
						$classeBtn = $commande->ObtientValSuppl("classe-btn", "btn-default") ;
						$ctn .= '<button id="'.$commande->IDInstanceCalc.'" class="Commande btn '.$commande->NomClsCSS.' '.$classeBtn.'" type="submit" rel="'.$commande->NomElementSousComposantIU.'"' ;
						$ctn .= ' onclick="'.$composant->IDInstanceCalc.'_ActiveCommande(this) ;"' ;
						if($this->InclureLibelle == 0)
						{
							$ctn .= ' title="'.htmlspecialchars($commande->Libelle).'"' ;
						}
						$ctn .= '>'.PHP_EOL ;
						if($this->InclureGlyphicons == 1)
						{
							$glyphicon = $this->GlyphiconParDefaut ;
							if($commande->ObtientValSuppl("glyphicon") != '')
							{
								$glyphicon = $commande->ObtientValSuppl("glyphicon") ;
							}
							$ctn .= '<i class="glyphicon '.$glyphicon.'"></i>'.PHP_EOL ;
						}
						if($this->InclureLibelle)
						{
							$ctn .= $commande->Libelle ;
						}
						$ctn .= '</button>'.PHP_EOL ;
						if($commande->ContenuApresRendu != '')
						{
							$ctn .= $commande->ContenuApresRendu ;
						}
						$ctn .= $this->FinExecParam($script, $composant, $i, $commande) ;
					}
				}
				return $ctn ;
			}
		}
		
		class PvDessinRangeeDonneesBase extends PvDessinateurRenduBase
		{
		}
		class PvDessinFormFiltresDonneesBase extends PvDessinateurRenduBase
		{
		}
		
		class PvNavigateurRangeesDonneesBase
		{
			public function Execute(& $script, & $composant)
			{
				return $this->ExecuteInstructions($script, $composant) ;
			}
			protected function ExecuteInstructions(& $script, & $composant)
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
		
		class PvBaliseHtmlBase extends PvComposantIUBase
		{
			public $IDElementHtml = "" ;
			public $NomElementHtml = "" ;
			public $TitreElementHtml = "" ;
			public $ClassesCSS = array() ;
			public $StyleCSS = "" ;
			public static $SourceInclus = 0 ;
			public static $CheminSource = "" ;
			public $BaliseInclusionSource = null ;
			protected function InclutSource()
			{
				if($this->ObtientValeurStatique('SourceInclus') == 1 || $this->ObtientValeurStatique('CheminSource') == "")
				{
					return "" ;
				}
				$this->BaliseInclusionSource = new PvLienFichierJs() ;
				$this->BaliseInclusionSource->Src = $this->ObtientValeurStatique('CheminSource') ;
				$this->BaliseInclusionSource->AdopteScript("source".get_class($this), $this->ScriptParent) ;
				$this->AffecteValeurStatique('SourceInclus', 1) ;
				return $this->BaliseInclusionSource->RenduDispositif() ;
			}
			public function CorrigeIDsElementHtml()
			{
				if($this->NomElementHtml == '')
				{
					$this->NomElementHtml = $this->NomElementScript ;
				}
			}
		}
		class PvPortionRenduHtml extends PvComposantIUBase
		{
			public $Contenu = '' ;
			protected function RenduDispositifBrut()
			{
				return $this->Contenu ;
			}
		}
		
		class EncBasePortionRenduFmt
		{
			public $Prefixe ;
			public $AppliquerTout = 0 ;
			public $NomParams = array() ;
			public function __construct($prefixe='')
			{
				$this->Prefixe = $prefixe;
			}
			public function Execute($params=array(), $elem=array())
			{
				return array() ;
			}
		}
		class EncUrlPortionRenduFmt extends EncBasePortionRenduFmt
		{
			public $AppliquerTout = 1 ;
			public function Execute($params=array(), $elem=array())
			{
				return array_map('urlencode', $params) ;
			}
		}
		class EncDateFrPortionRenduFmt extends EncBasePortionRenduFmt
		{
			public $AppliquerTout = 0 ;
			public function Execute($params=array(), $elem=array())
			{
				return array_map('date_fr', $params) ;
			}
		}
		class EncHtmlEntPortionRenduFmt extends EncBasePortionRenduFmt
		{
			public $AppliquerTout = 1 ;
			public function Execute($params=array(), $elem=array())
			{
				return array_map('htmlentities', $params) ;
			}
		}
		class EncNonVidePortionRenduFmt extends EncBasePortionRenduFmt
		{
			public $AppliquerTout = 0 ;
			public $Contenu = '${luimeme}' ;
			public function Execute($params=array(), $elem=array())
			{
				$results = array() ;
				foreach($params as $nom => $val)
				{
					if($val == "")
					{
						$results[$nom] = "" ;
					}
					else
					{
						$elem["luimeme"] = $val ;
						$elem["this"] = $val ;
						$elem["self"] = $val ;
						$results[$nom] = _parse_pattern($this->Contenu, $elem) ;
					}
				}
				return $results ;
			}
		}
		
		class PvPortionRenduFmt extends PvComposantIUBase
		{
			public $PrefixeEncUrl = "url_" ;
			public $EncoderUrl = 1 ;
			public $PrefixeEncHtmlEnt = "html_" ;
			public $EncoderHtmlEnt = 1 ;
			public $Encodeurs = array() ;
			public $Params = array() ;
			public $Contenu = "" ;
			public $NomClasseCSS ;
			protected $ParamsCalc = array() ;
			protected function RenduVideActif()
			{
				return ($this->Contenu == '') ;
			}
			public function & InsereEncodeurDateFr($nomParams=array(), $prefixe="date_fr")
			{
				$encodeur = new EncDateFrPortionRenduFmt($prefixe) ;
				$encodeur->NomParams = $nomParams ;
				$this->InsereEncodeur($encodeur) ;
				return $encodeur ;
			}
			public function & InsereEncodeurNonVide($nomParams=array(), $contenu='${luimeme}', $prefixe="non_vide")
			{
				$encodeur = new EncNonVidePortionRenduFmt($prefixe) ;
				$encodeur->NomParams = $nomParams ;
				$encodeur->Contenu = $contenu ;
				$this->InsereEncodeur($encodeur) ;
				return $encodeur ;
			}
			public function & InsereEncodeur($encodeur)
			{
				$this->Encodeurs[] = $encodeur ;
				return $encodeur ;
			}
			protected function ObtientEncodeurs()
			{
				$encodeurs = $this->Encodeurs;
				if($this->EncoderUrl)
				{
					$encodeurs[] = new EncUrlPortionRenduFmt($this->PrefixeEncUrl) ;
				}
				if($this->EncoderHtmlEnt)
				{
					$encodeurs[] = new EncHtmlEntPortionRenduFmt($this->PrefixeEncHtmlEnt) ;
				}
				return $encodeurs ;
			}
			protected function DetecteParamsCalc()
			{
				$this->ParamsCalc = $this->Params ;
				$encodeurs = $this->ObtientEncodeurs() ;
				foreach($encodeurs as $i => $encodeur)
				{
					$elem = $this->Params ;
					$valeurs = ($encodeur->AppliquerTout) ? $elem : array_intersect_key($elem, array_flip($encodeur->NomParams)) ;
					$params = $encodeur->Execute($valeurs, $elem) ;
					if(count($params) == 0)
					{
						continue ;
					}
					$params = array_apply_prefix($params, $encodeur->Prefixe) ;
					$this->ParamsCalc = array_merge($this->ParamsCalc, $params) ;
				}
			}
			public function EstVide()
			{
				return (empty($this->Contenu)) ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->RenduVideActif())
				{
					return $ctn ;
				}
				$this->DetecteParamsCalc() ;
				$ctn .= '<div id="'.$this->IDInstanceCalc ;
				if($this->NomClasseCSS != "")
					$ctn .= ' class="'.$this->NomClasseCSS.'"' ;
				$ctn .= '">' ;
				$ctn .= _parse_pattern($this->Contenu, $this->ParamsCalc) ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
		}
		
		class PvComposantIUFiltrable extends PvComposantIUBase
		{
			public $FournisseurDonnees ;
			public $FiltresSelection = array() ;
			public $SourceValeursSuppl ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->SourceValeursSuppl = new PvSrcValsSupplLgnDonnees() ;
			}
			protected function ExtraitValeursLgnDonnees(& $lgn)
			{
				if($this->EstNul($this->SourceValeursSuppl))
				{
					return $lgn ;
				}
				return $this->SourceValeursSuppl->Applique($this, $lgn) ;
			}
			public function & CreeFiltreRef($nom, & $filtreRef)
			{
				$filtre = new PvFiltreDonneesRef() ;
				$filtre->Source = & $filtreRef ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreDonneesFixe() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreCookie($nom)
			{
				$filtre = new PvFiltreDonneesCookie() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreSession($nom)
			{
				$filtre = new PvFiltreDonneesSession() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreMembreConnecte($nom, $nomParamLie='')
			{
				$filtre = new PvFiltreDonneesMembreConnecte() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->NomParametreLie = $nomParamLie ;
				return $filtre ;
			}
			public function & CreeFiltreHttpUpload($nom, $cheminDossierDest="")
			{
				$filtre = new PvFiltreDonneesHttpUpload() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CheminDossier = $cheminDossierDest ;
				return $filtre ;
			}
			public function & CreeFiltreHttpGet($nom)
			{
				$filtre = new PvFiltreDonneesHttpGet() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpPost($nom)
			{
				$filtre = new PvFiltreDonneesHttpPost() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreDonneesHttpRequest() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function CreeFltRef($nom, & $filtreRef)
			{
				return $this->CreeFiltreRef($nom, $filtreRef) ;
			}
			public function CreeFltFixe($nom, $valeur)
			{
				return $this->CreeFiltreRef($nom, $valeur) ;
			}
			public function CreeFltCookie($nom)
			{
				return $this->CreeFiltreCookie($nom) ;
			}
			public function CreeFltSession($nom)
			{
				return $this->CreeFiltreSession($nom) ;
			}
			public function CreeFltMembreConnecte($nom, $nomParamLie='')
			{
				return $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
			}
			public function CreeFltHttpUpload($nom, $cheminDossierDest="")
			{
				return $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
			}
			public function CreeFltHttpGet($nom)
			{
				return $this->CreeFiltreHttpGet($nom) ;
			}
			public function CreeFltHttpPost($nom)
			{
				return $this->CreeFiltreHttpPost($nom) ;
			}
			public function CreeFltHttpRequest($nom)
			{
				return $this->CreeFiltreHttpRequest($nom) ;
			}
			public function & InsereFltSelectRef($nom, & $filtreRef, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreRef($nom, $filtreRef) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectFixe($nom, $valeur, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreFixe($nom, $valeur) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectCookie($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreCookie($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectSession($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreSession($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectMembreConnecte($nom, $nomParamLie='', $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpUpload($nom, $cheminDossierDest="", $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpGet($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpGet($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpPost($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpPost($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltSelectHttpRequest($nom, $exprDonnees='', $nomClsComp='')
			{
				$flt = $this->CreeFiltreHttpRequest($nom) ;
				$flt->ExpressionDonnees = $exprDonnees ;
				if($nomClsComp != '')
					$flt->DeclareComposant($nomClsComp) ;
				$this->FiltresSelection[] = & $flt ;
				return $flt ;
			}
			public function CalculeElementsRendu()
			{
			}
			public function VerifiePreRequisRendu()
			{
				return 1 ;
			}
			public function MsgPreRequisRenduNonVerifies()
			{
				return "(PRE REQUIS DU RENDU NON VERIFIES)" ;
			}
			protected function RenduDispositifBrut()
			{
				$this->CalculeElementsRendu() ;
				if($this->VerifiePreRequisRendu())
				{
					return $this->RenduDispositifBrutSpec() ;
				}
				return $this->MsgPreRequisRenduNonVerifies() ;
			}
			public function ObtientFiltresSelection()
			{
				return $this->FiltresSelection ;
			}
			protected function RenduDispositifBrutSpec()
			{
			}
		}
		class PvComposantJsFiltrable extends PvComposantIUFiltrable
		{
			protected static $SourceIncluse = 0 ;
			public $CfgInit ;
			protected function CreeCfgInit()
			{
				return new StdClass ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CfgInit = $this->CreeCfgInit() ;
			}
			protected function RenduDispositifBrut()
			{
				$this->CalculeElementsRendu() ;
				if($this->VerifiePreRequisRendu())
				{
					$ctn = '' ;
					$ctn .= $this->RenduSourceIncluse() ;
					$ctn .= $this->RenduDispositifBrutSpec() ;
					return $ctn ;
				}
				return $this->MsgPreRequisRenduNonVerifies() ;
			}
			protected function RenduSourceIncluse()
			{
				if($this->ObtientValStatique("SourceIncluse") == 1)
					return "" ;
				$ctn = $this->RenduSourceBrut() ;
				$this->AffecteValStatique("SourceIncluse", 1) ;
				return $ctn ;
			}
			protected function RenduSourceBrut()
			{
				return "" ;
			}
			public function RenduInscritCtnCSS($contenu)
			{
				return $this->RenduInscritContenuCSS($contenu) ;
			}
			public function RenduInscritContenuCSS($contenu)
			{
				$ctn = '' ;
				$ctn .= '<style type="text/css">'.PHP_EOL 
					.$contenu.PHP_EOL 
					.'</style>' ;
				return $ctn ;
			}
			public function RenduInscritLienCSS($chemFich)
			{
				$ctn = '' ;
				$ctn .= '<link rel="stylesheet" type="text/css" href="'.$chemFich.'">' ;
				return $ctn ;
			}
			public function RenduInscritLienJs($chemFich)
			{
				$ctn = '' ;
				if($this->ZoneParent->InclureCtnJsEntete == 0)
				{
					$this->ZoneParent->InscritLienJs($chemFich) ;
				}
				else
				{
					$ctn .= '<script type="text/javascript" src="'.htmlspecialchars($chemFich).'"></script>' ;
				}
				return $ctn ;
			}
			public function RenduInscritContenuJs($contenuJs)
			{
				$ctn = '' ;
				if($this->ZoneParent->InclureCtnJsEntete == 0)
				{
					$this->ZoneParent->InscritContenuJs($contenuJs) ;
				}
				else
				{
					$ctn .= '<script type="text/javascript">'.PHP_EOL 
						.$contenuJs.PHP_EOL
						.'</script>' ;
				}
				return $ctn ;
			}
			public function RenduInscritCtnJs($contenuJs)
			{
				return $this->RenduInscritContenuJs($contenuJs) ;
			}
		}
		
		class PvComposantIUDonneesSimple extends PvComposantIUDonnees
		{
			public function & CreeFiltreRef($nom, & $filtreRef)
			{
				$filtre = new PvFiltreDonneesRef() ;
				$filtre->Source = & $filtreRef ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreFixe($nom, $valeur)
			{
				$filtre = new PvFiltreDonneesFixe() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->ValeurParDefaut = $valeur ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreCookie($nom)
			{
				$filtre = new PvFiltreDonneesCookie() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreSession($nom)
			{
				$filtre = new PvFiltreDonneesSession() ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				return $filtre ;
			}
			public function & CreeFiltreMembreConnecte($nom, $nomParamLie='')
			{
				$filtre = new PvFiltreDonneesMembreConnecte() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->NomParametreLie = $nomParamLie ;
				return $filtre ;
			}
			public function & CreeFiltreHttpUpload($nom, $cheminDossierDest="")
			{
				$filtre = new PvFiltreDonneesHttpUpload() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreDonnees = $nom ;
				$filtre->CheminDossier = $cheminDossierDest ;
				return $filtre ;
			}
			public function & CreeFiltreHttpGet($nom)
			{
				$filtre = new PvFiltreDonneesHttpGet() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpPost($nom)
			{
				$filtre = new PvFiltreDonneesHttpPost() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function & CreeFiltreHttpRequest($nom)
			{
				$filtre = new PvFiltreDonneesHttpRequest() ;
				$filtre->AdopteScript($nom, $this->ScriptParent) ;
				$filtre->NomParametreLie = $nom ;
				$filtre->NomParametreDonnees = $nom ;
				return $filtre ;
			}
			public function CreeFltRef($nom, & $filtreRef)
			{
				return $this->CreeFiltreRef($nom, $filtreRef) ;
			}
			public function CreeFltFixe($nom, $valeur)
			{
				return $this->CreeFiltreRef($nom, $valeur) ;
			}
			public function CreeFltCookie($nom)
			{
				return $this->CreeFiltreCookie($nom) ;
			}
			public function CreeFltSession($nom)
			{
				return $this->CreeFiltreSession($nom) ;
			}
			public function CreeFltMembreConnecte($nom, $nomParamLie='')
			{
				return $this->CreeFiltreMembreConnecte($nom, $nomParamLie) ;
			}
			public function CreeFltHttpUpload($nom, $cheminDossierDest="")
			{
				return $this->CreeFiltreHttpUpload($nom, $cheminDossierDest) ;
			}
			public function CreeFltHttpGet($nom)
			{
				return $this->CreeFiltreHttpGet($nom) ;
			}
			public function CreeFltHttpPost($nom)
			{
				return $this->CreeFiltreHttpPost($nom) ;
			}
			public function CreeFltHttpRequest($nom)
			{
				return $this->CreeFiltreHttpRequest($nom) ;
			}
			public function ExtraitValeursParametre(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$valeurs = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $filtres[$nomFiltre] ;
					$filtre->Lie() ;
					$valeurs[$filtre->NomParametreDonnees] = $filtre->ValeurParametre ;
				}
				return $valeurs ;
			}
			public function ExtraitValeursColonneLiee(& $filtres)
			{
				$nomFiltres = array_keys($filtres) ;
				$valeurs = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $filtres[$nomFiltre] ;
					$filtre->Lie() ;
					$valeurs[$filtre->NomColonneLiee] = $filtre->ValeurParametre ;
				}
				return $valeurs ;
			}
			public function ObtientFiltre(& $filtres, $nomParamLie)
			{
			}
			public function CreeCmdRedirectUrl()
			{
				return new PvCommandeRedirectionHttp() ;
			}
			public function CreeCmdRedirectScript()
			{
				return new PvCommandeRedirectionHttp() ;
			}
			protected function AppliqueHabillage()
			{
				if($this->ZoneParent->EstNul($this->ZoneParent->Habillage))
				{
					return ;
				}
				$this->ZoneParent->Habillage->AppliqueSur($this) ;
				return $this->ZoneParent->Habillage->Rendu ;
			}
			public function ExtraitFiltresDeRendu(& $filtres, $filtresCaches=array())
			{
				$resultats = array() ;
				foreach($filtres as $i => $filtre)
				{
					// print $i.'- '.$filtre->NomParametreLie.' '.$filtre->RenduPossible().'<br />' ;
					if($filtre->RenduPossible() && ! in_array($filtre->NomParametreLie, $filtresCaches))
					{
						$resultats[$i] = & $filtres[$i] ;
					}
				}
				return $resultats ;
			}
			public function ExtraitFiltresAffichables(& $filtres)
			{
				$resultats = array() ;
				foreach($filtres as $i => $filtre)
				{
					if($filtre->RenduPossible() && ! $filtre->LectureSeule)
					{
						$resultats[$i] = & $filtres[$i] ;
					}
				}
				return $resultats ;
			}
		}
		class PvPortionRenduDonneesHtml extends PvComposantIUDonneesSimple
		{
			public $PrefixeEncUrl = "url_" ;
			public $EncoderUrl = 1 ;
			public $PrefixeEncHtmlEnt = "html_" ;
			public $EncoderHtmlEnt = 1 ;
			public $Encodeurs = array() ;
			public $ElementsBruts = array() ;
			public $Elements = array() ;
			public $ElementsTrouves = 0 ;
			public $ParamsSelection = array() ;
			public $RequeteSelection = "" ;
			public $ContenuModele = "" ;
			protected $ContenuModeleUse = "" ;
			protected $ErreurTrouvee = 0 ;
			protected $ContenuErreurTrouvee = "" ;
			protected $MsgSiErreurTrouvee = "Le composant ne peut s'afficher car une erreur est survenue lors de l'affichage." ;
			protected function ObtientEncodeurs()
			{
				$encodeurs = $this->Encodeurs;
				if($this->EncoderUrl)
				{
					$encodeurs[] = new EncUrlPortionRenduFmt($this->PrefixeEncUrl) ;
				}
				if($this->EncoderHtmlEnt)
				{
					$encodeurs[] = new EncHtmlEntPortionRenduFmt($this->PrefixeEncHtmlEnt) ;
				}
				return $encodeurs ;
			}
			public function & InsereEncodeurDateFr($nomParams=array())
			{
				$encodeur = new EncDateFrPortionRenduFmt() ;
				$encodeur->NomParams = $nomParams ;
				$this->InsereEncodeur($encodeur) ;
				return $encodeur ;
			}
			public function & InsereEncodeur($encodeur)
			{
				$this->Encodeurs[] = $encodeur ;
				return $encodeur ;
			}
			public function & InsereEncodeurNonVide($nomParams=array(), $contenu='${luimeme}', $prefixe="non_vide")
			{
				$encodeur = new EncNonVidePortionRenduFmt($prefixe) ;
				$encodeur->NomParams = $nomParams ;
				$encodeur->Contenu = $contenu ;
				$this->InsereEncodeur($encodeur) ;
				return $encodeur ;
			}
			protected function VideErreur()
			{
				$this->ErreurTrouvee = 0 ;
				$this->ContenuErreurTrouvee = "" ;
			}
			protected function ConfirmeErreur($msg)
			{
				$this->ErreurTrouvee = 1 ;
				$this->ContenuErreurTrouvee = $msg ;
			}
			protected function PrepareCalcul()
			{
				$this->ElementsTrouves = 0 ;
				$this->VideErreur() ;
				$this->ElementsBruts = array() ;
				$this->Elements = array() ;
			}
			protected function CalculeElements()
			{
				$this->PrepareCalcul() ;
				if($this->ContenuModele == "")
				{
					$this->ConfirmeErreur("Le modele est vide") ;
					return ;
				}
				$this->ElementsBruts = $this->FournisseurDonnees->ExecuteRequete($this->RequeteSelection, $this->ParamsSelection) ;
				// echo $this->FournisseurDonnees->BaseDonnees->ConnectionException ;
				if(! is_array($this->ElementsBruts))
				{
					$this->ConfirmeErreur("La recuperation des elements a echoue") ;
					return ;
				}
				$this->ElementsTrouves = (count($this->ElementsBruts) > 0) ? 1 : 0 ;
				$this->Elements = array() ;
				foreach($this->ElementsBruts as $i => $elem)
				{
					$this->Elements[$i] = $this->ExtraitElementCalc($elem) ;
				}
			}
			protected function ExtraitElementCalc($elem)
			{
				$encodeurs = $this->ObtientEncodeurs() ;
				$result = $elem ;
				foreach($encodeurs as $i => $encodeur)
				{
					$valeurs = ($encodeur->AppliquerTout) ? $elem : array_intersect_key($elem, array_flip($encodeur->NomParams)) ;
					$params = $encodeur->Execute($valeurs, $elem) ;
					if(count($params) == 0)
					{
						continue ;
					}
					$params = array_apply_prefix($params, $encodeur->Prefixe) ;
					$result = array_merge($result, $params) ;
				}
				return $result ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$this->CalculeElements() ;
				if($this->ErreurTrouvee)
				{
					$ctn .= $this->RenduErreurTrouvee() ;
					return $ctn ;
				}
				$ctn .= $this->ContenuAvantRendu ;
				foreach($this->Elements as $i => $elem)
				{
					$ctn .= _parse_pattern($this->ContenuModele, $elem) ;				
				}
				$ctn .= $this->ContenuApresRendu ;
				return $ctn ;
			}
			protected function RenduErreurTrouvee()
			{
				return '<div class="error">'.$this->MsgSiErreurTrouvee.'</div>' ;
			}
		}
		class PvPortionDonneesHtml extends PvPortionRenduDonneesHtml
		{
		}
		
		class PvCommandeComposantIUBase extends PvElementAccessible
		{
			public $Visible = 1 ;
			public $NecessiteFormulaireDonnees = 0 ;
			public $NecessiteTableauDonnees = 0 ;
			public $UtiliserRenduDispositif = 0 ;
			public $FormulaireDonneesParent = null ;
			public $TableauDonneesParent = null ;
			public $ScriptParent = null ;
			public $ZoneParent = null ;
			public $ApplicationParent = null ;
			public $NomElementFormulaireDonnees = "" ;
			public $NomElementSousComposantIU = "" ;
			public $CheminIcone ;
			public $Libelle = "" ;
			public $NomClsCSS = "" ;
			public $ContenuAvantRendu = "" ;
			public $ContenuApresRendu = "" ;
			public $InfoBulle = "" ;
			public $MessageErreurExecution = "La commande a &eacute;t&eacute; ex&eacute;cut&eacute;e avec des erreurs" ;
			public $MessageSuccesExecution = "La commande a &eacute;t&eacute; ex&eacute;cut&eacute;e avec succ&egrave;s" ;
			public $MessageExecution = "" ;
			public $StatutExecution = 0 ;
			public $Criteres = array() ;
			public $Actions = array() ;
			public $SeparateurCriteresNonRespectes = "<br/>" ;
			public $Liens = array() ;
			public $InscrireLienAnnuler = 0 ;
			public $InscrireLienReprendre = 0 ;
			public $UrlLienAnnuler = "" ;
			public $UrlLienReprendre = "" ;
			public function EstSucces()
			{
				return $this->StatutExecution == 1 ;
			}
			public function & InsereLien($url, $titre)
			{
				$lien = new PvLienCommandeFormulaireDonnees($url, $titre) ;
				$this->Liens[] = & $lien ;
				return $lien ;
			}
			public function ObtientLiens()
			{
				$liens = $this->Liens ;
				$form = & $this->FormulaireDonneesParent ;
				if($form->InscrireCommandeAnnuler == 1 && $this->InscrireLienAnnuler == 1 && $this->UrlLienAnnuler != '')
				{
					$lienAnnul = new PvLienCommandeFormulaireDonnees(
						$this->UrlLienAnnuler,
						$this->LibelleLienAnnuler
					) ;
					$liens[] = $lienAnnul ;
				}
				if($this->InscrireLienReprendre == 1)
				{
					$lienReprendre = new PvLienCommandeFormulaireDonnees(
						$form->ObtientUrlInitiale(),
						$this->LibelleLienReprendre
					) ;
					$liens[] = $lienReprendre ;
				}
				return $liens ;
			}
			public function PrepareRendu(& $composant)
			{
			}
			protected function AdopteComposantIU($nom, &$composant)
			{
				$this->NomElementSousComposantIU = $nom ;
				$this->ScriptParent = & $composant->ScriptParent ;
				$this->ZoneParent = & $composant->ZoneParent ;
				$this->ApplicationParent = & $composant->ApplicationParent ;
			}
			public function AdopteFormulaireDonnees($nom, & $formulaireDonnees)
			{
				$this->NomElementFormulaireDonnees = $nom ;
				$this->FormulaireDonneesParent = & $formulaireDonnees ;
				$this->AdopteComposantIU($nom, $formulaireDonnees) ;
			}
			public function AdopteTableauDonnees($nom, & $tableauDonnees)
			{
				$this->NomElementTableauDonnees = $nom ;
				$this->TableauDonneesParent = & $tableauDonnees ;
				$this->AdopteComposantIU($nom, $tableauDonnees) ;
			}
			public function InscritCritere(& $critere)
			{
				$this->Criteres[] = & $critere ;
				$critere->AdopteCommande(count($this->Criteres), $this) ;
			}
			public function InscritCritr(& $critere)
			{
				$this->InscritCritere($critere) ;
			}
			public function & InsereCritereFormatUrl($nomFiltres = array())
			{
				$critere = new PvCritereFormatUrl() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritereFormatMotPasse($nomFiltres = array())
			{
				$critere = new PvCritereFormatMotPasse() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritereFormatLogin($nomFiltres = array())
			{
				$critere = new PvCritereFormatLogin() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritereFormatEmail($nomFiltres = array())
			{
				$critere = new PvCritereFormatEmail() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritereNonVide($nomFiltres = array())
			{
				$critere = new PvCritereNonVide() ;
				$this->InscritCritere($critere) ;
				call_user_func_array(array(& $critere, 'CibleFiltres'), $nomFiltres) ;
				return $critere ;
			}
			public function & InsereCritrNonVide($nomFiltres = array())
			{
				$critere = $this->InsereCritereNonVide($nomFiltres) ;
				return $critere ;
			}
			public function & InscritNouvActCmd($actCmd, $nomFiltresCibles=array())
			{
				return $this->InscritActCmd($actCmd, $nomFiltresCibles) ;
			}
			public function InscritNouvAction($actCmd)
			{
				$this->InscritActCmd($actCmd) ;
			}
			public function InscritActCmd(& $actCmd, $nomFiltresCibles=array())
			{
				$this->Actions[] = & $actCmd ;
				$actCmd->AdopteCommande(count($this->Actions), $this) ;
				call_user_func_array(array($actCmd, 'CibleFiltres'), $nomFiltresCibles) ;
				return $actCmd ;
			}
			public function InscritAction(& $actCmd)
			{
				$this->InscritActCmd($actCmd) ;
			}
			protected function VideStatutExecution()
			{
				$this->MessageExecution = "" ;
				$this->StatutExecution = 1 ;
			}
			public function RenseigneErreur($messageErreur="")
			{
				$this->MessageExecution = $messageErreur ;
				$this->StatutExecution = 0 ;
			}
			protected function ConfirmeSucces($msgSucces = '')
			{
				$this->StatutExecution = 1 ;
				$this->MessageExecution = ($msgSucces == '') ? $this->MessageSuccesExecution : $msgSucces ;
			}
			protected function ExecuteInstructions()
			{
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
			}
			public function & InsereCritere($nomClasse, $nomFiltresCibles=array())
			{
				if(! class_exists($nomClasse))
				{
					die("La classe '$nomClasse' n'existe pas") ;
				}
				$critere = new $nomClasse() ;
				$this->InsereNouvCritere($critere, $nomFiltresCibles) ;
				return $critere ;
			}
			public function & InsereActCmd($nomClasse, $nomFiltresCibles=array())
			{
				if(! class_exists($nomClasse))
				{
					die("La classe '$nomClasse' n'existe pas") ;
				}
				$actCmd = new $nomClasse() ;
				$this->InscritNouvActCmd($actCmd, $nomFiltresCibles) ;
				return $actCmd ;
			}
			public function & InsereAction($nomClasse, $nomFiltresCibles=array())
			{
				$action = $this->InsereActCmd($nomClasse, $nomFiltresCibles) ;
				return $action ;
			}
			public function & InsereNouvCritere($critere, $nomFiltresCibles=array())
			{
				$this->InscritCritere($critere) ;
				call_user_func_array(array($critere, 'CibleFiltres'), $nomFiltresCibles) ;
				return $critere ;
			}
			public function & InsereNouvActCmd($actCmd, $nomFiltresCibles=array())
			{
				$this->InscritAction($actCmd) ;
				call_user_func_array(array($actCmd, 'CibleFiltres'), $nomFiltresCibles) ;
				return $actCmd ;
			}
			public function & InsereNouvAction($action, $nomFiltresCibles=array())
			{
				$action = $this->InsereActCmd($nomClasse, $nomFiltresCibles) ;
				return $action ;
			}
			public function Execute()
			{
				if(($this->NecessiteFormulaireDonnees && $this->EstNul($this->FormulaireDonneesParent)) || ($this->NecessiteTableauDonnees && $this->EstNul($this->TableauDonneesParent)))
				{
					return ;
				}
				$this->VideStatutExecution() ;
				if(! $this->RespecteCriteres())
				{
					return ;
				}
				// echo $this->MessageExecution ;
				$this->VerifiePreRequis() ;
				if($this->StatutExecution == 0)
				{
					return ;
				}
				$this->ExecuteInstructions() ;
				if($this->StatutExecution == 0)
				{
					return ;
				}
				$this->ExecuteActions() ;
			}
			protected function VerifiePreRequis()
			{
				
			}
			protected function VerifieFichiersUpload(& $filtres)
			{
				foreach($filtres as $n => & $flt)
				{
					if($flt->Role == "http_upload" && $flt->ToujoursRenseignerFichier == 1 && $flt->Lie() == '')
					{
						$this->RenseigneErreur($flt->LibelleErreurTelecharg) ;
					}
				}
			}
			protected function RespecteCriteres()
			{
				$indCriteres = array_keys($this->Criteres) ;
				$messageErreurs = array() ;
				foreach($indCriteres as $i => $indCritere)
				{
					$critere = & $this->Criteres[$indCritere] ;
					if($critere->EstRespecte() == 0)
					{
						$messageErreurs[] = $critere->MessageErreur ;
					}
				}
				$ok = 1 ;
				if(count($messageErreurs) > 0)
				{
					$this->RenseigneErreur(join($this->SeparateurCriteresNonRespectes, $messageErreurs)) ;
					$ok = 0 ;
				}
				return $ok ;
			}
			protected function ExecuteActions()
			{
				$nomActions = array_keys($this->Actions) ;
				// print_r($this->NomElementFormulaireDonnees." : ".$nomActions) ;
				if(count($nomActions) > 0)
				{
					if($this->MessageExecution == '')
					{
						$this->MessageExecution = $this->MessageSuccesExecution ;
					}
					foreach($nomActions as $i => $nomAction)
					{
						$action = & $this->Actions[$nomAction] ;
						$action->Execute() ;
					}
				}
			}
			public function RenduDispositif()
			{
				if($this->Visible == 0)
				{
					return '' ;
				}
				if(! $this->EstBienRefere())
				{
					return $this->RenduMalRefere() ;
				}
				if(! $this->EstAccessible())
				{
					return $this->RenduInaccessible() ;
				}
				$ctn .= $this->RenduDispositifBrut() ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				return "" ;
			}
			public function InclureEnvoiFiltres()
			{
				return 1 ;
			}
		}
		class PvCommandeRedirectionHttp extends PvCommandeComposantIUBase
		{
			public $NecessiteFormulaireDonnees = 0 ;
			public $NecessiteTableauDonnees = 0 ;
			public $Url = "" ;
			public $NomScript = "" ;
			public $Parametres = array() ;
			public $Script = null ;
			protected function ObtientUrl()
			{
				$url = $this->Url ;
				$script = null ;
				if($this->NomScript != "" && isset($this->ZoneParent->Scripts[$this->NomScript]))
				{
					$script = $this->ZoneParent->Scripts[$this->NomScript] ;
				}
				if($this->EstNul($script) && $this->EstPasNul($this->Script))
				{
					$script = $this->Script ;
				}
				if($this->EstPasNul($script))
				{
					$url = $script->ObtientUrl() ;
				}
				if($url != '' && count($this->Parametres) > 0)
				{
					$url = update_url_params($url, $this->Parametres) ;
				}
				return $url ;
			}
			protected function ExecuteInstructions()
			{
				$url = $this->ObtientUrl() ;
				if($url == '')
				{
					$this->RenseigneErreur("URL non definie pour la commande ".$this->IDInstanceCalc) ;
					return ;
				}
				redirect_to($url) ;
			}
		}
		class PvCommandeOuvrePopup extends PvCommandeRedirectionHttp
		{
			public $NomFenetre = "" ;
			public $CoinGaucheEcran = "" ;
			public $CoinHautEcran = "" ;
			public $LargeurPopup = "" ;
			public $HauteurPopup = "" ;
			public $LargeurIntern = "" ;
			public $HauteurIntern = "" ;
			public $BarreAdrUrl = "" ;
			public $BarreDefil = "" ;
			public $BarreStatut = "" ;
			public $BarreOutils = "" ;
			public $BarreMenus = "" ;
			public $Dependant = "" ;
			public $CoinGauche = "" ;
			public $CoinHaut = "" ;
			public $RaccourcisClavier = "" ;
			public $Redimens = "" ;
			protected function ObtientNomFenetre()
			{
				$nomFenetre = $this->NomFenetre ;
				if($nomFenetre == "")
				{
					$nomFenetre = $this->IDInstanceCalc ;
				}
				return $nomFenetre ;
			}
			public function ObtientParamsOuverture()
			{
				$params = array() ;
				if($this->LargeurPopup != "")
					$params["width"] = $this->LargeurPopup ;
				if($this->HauteurPopup != "")
					$params["height"] = $this->HauteurPopup ;
				if($this->LargeurIntern != "")
					$params["innerWidth"] = $this->LargeurIntern ;
				if($this->HauteurIntern != "")
					$params["innerHeight"] = $this->HauteurIntern ;
				if($this->BarreAdrUrl != "")
					$params["location"] = $this->BarreAdrUrl ;
				if($this->BarreDefil != "")
					$params["scrollbars"] = $this->BarreDefil ;
				if($this->BarreStatut != "")
					$params["status"] = $this->BarreStatut ;
				if($this->BarreOutils != "")
					$params["toolbar"] = $this->BarreOutils ;
				if($this->BarreMenus != "")
					$params["menubar"] = $this->BarreMenus ;
				if($this->Redimens != "")
					$params["resizable"] = $this->Redimens ;
				if($this->CoinGauche != "")
					$params["left"] = $this->CoinGauche ;
				if($this->CoinHaut != "")
					$params["top"] = $this->CoinHaut ;
				if($this->CoinGaucheEcran != "")
					$params["screenX"] = $this->CoinGaucheEcran ;
				if($this->CoinHautEcran != "")
					$params["screenY"] = $this->CoinHautEcran ;
				if($this->LargeurPopup != "")
					$params["width"] = $this->LargeurPopup ;
				if($this->HauteurPopup != "")
					$params["height"] = $this->HauteurPopup ;
				if($this->LargeurIntern != "")
					$params["innerWidth"] = $this->LargeurIntern ;
				if($this->HauteurIntern != "")
					$params["innerHeight"] = $this->HauteurIntern ;
				return $params ;
			}
			protected function ExecuteInstructions()
			{
			}
		}
		class PvCommandeActionNotification extends PvCommandeComposantIUBase
		{
			public $ActionNotification ;
			protected function ExecuteInstructions()
			{
				if($this->EstNul($this->ActionNotification))
				{
					$this->RenseigneErreur("L'action rattach&eacute;e &agrave; la commande est nulle ou n'existe pas.") ;
					return ;
				}
				$this->ActionNotification->Execute() ;
				$msg = $this->ActionNotification->ObtientMessage() ;
				if($msg->TypeErreur == "")
				{
					$this->ConfirmSucces($msg->Contenu) ;
				}
				else
				{
					$this->ConfirmErreur($msg->Contenu) ;
				}
			}
		}
		
		class PvFilArianeDonneesHtml extends PvComposantIUDonneesSimple
		{
			public $NomClasseCSS = "FilAriane" ;
			public $NomClasseCSSLien = "" ;
			public $DefsLien = array() ;
			protected $LgnsLien = array() ;
			protected $CtnsLien = array() ;
			public $FiltresSelection = array() ;
			public $FournisseurDonnees ;
			public $SeparateurLiens = ' &gt; ' ;
			public $CacherSiVide = 1 ;
			public $InclureLienAccueil = 1 ;
			public $TitreLienAccueil = "Accueil" ;
			public $UrlLienAccueil = "?" ;
			public $NomClasseFournisseurDonnees = "PvFournisseurDonneesBase" ;
			protected function InitFournisseurDonnees()
			{
				if($this->EstNul($this->FournisseurDonnees) && $this->NomClasseFournisseurDonnees != "")
				{
					$nomClasse = $this->NomClasseFournisseurDonnees ;
					if(class_exists($nomClasse))
					{
						$this->FournisseurDonnees = new $nomClasse() ;
					}
				}
				if(! $this->EstNul($this->FournisseurDonnees))
				{
					$this->ChargeConfigFournisseurDonnees() ;
					$this->FournisseurDonnees->ChargeConfig() ;
				}
			}
			protected function ChargeConfigFournisseurDonnees()
			{
			}
			protected function CalculeElementsRendu()
			{
				$fourn = & $this->FournisseurDonnees ;
				$paramsSelect = $fourn->ParamsSelection ;
				$this->LgnsLien = array() ;
				foreach($this->DefsLien as $i => $defLien)
				{
					$lienTrouve = 0 ;
					if($defLien->RequeteSelection != '')
					{
						$fourn->RequeteSelection = $defLien->RequeteSelection ;
						$lgnPrec = array() ;
						do
						{
							$flts = $this->FiltresSelection ;
							$fourn->ParamsSelection = $paramsSelect ;
							foreach($lgnPrec as $nom => $valeur)
							{
								$nomFlt = "lgn_prec_".$nom ;
								$fourn->ParamsSelection[$nom] = $valeur ;
							}
							$lgn = $fourn->SelectElements(array(), $flts) ;
							if(is_array($lgn) && count($lgn) > 0)
							{
								$this->CtnsLien[] = $this->CreeCtnLien($defLien, $lgn) ;
								$lienTrouve = 1 ;
							}
							$lgnPrec = $lgn ;
						}
						while($defLien->Recursif == 1) ;
					}
					else
					{
						$lgn = array() ;
						$this->CtnsLien[] = $this->CreeCtnLien($defLien, $lgn) ;
						$lienTrouve = 1 ;
					}
					if($defLien->Obligatoire && $lienTrouve == 0)
					{
						break ;
					}
				}
			}
			protected function CtnsLienRendu()
			{
				$ctnsLien = $this->CtnsLien ;
				if($this->InclureLienAccueil == 1)
				{
					$ctnLien = new PvCtnLienFilArianeDonnees() ;
					$ctnLien->Titre = $this->TitreLienAccueil ;
					$ctnLien->Url = $this->UrlLienAccueil ;
					$ctnsLien[] = $ctnLien ;
				}
				return $ctnsLien ;
			}
			protected function CreeCtnLien($defLien, $lgn)
			{
				$ctnLien = new PvCtnLienFilArianeDonnees() ;
				$ctnLien->Titre = _parse_pattern($defLien->FormatTitre, $lgn) ;
				$ctnLien->Url = _parse_pattern($defLien->FormatUrl, $lgn) ;
				$ctnLien->AttrsHtmlExtra = $defLien->AttrsHtmlExtra ;
				return $ctnLien ;
			}
			protected function RenduDispositifBrut()
			{
				$this->InitFournisseurDonnees() ;
				if(! $this->EstNul($this->FournisseurDonnees))
				{
					$this->ChargeConfigFournisseurDonnees() ;
				}
				$this->CalculeElementsRendu() ;
				if($this->CacherSiVide == 0 || $this->LiensTrouves())
				{
					$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="'.$this->NomClasseCSS.'">' ;
					if($this->EstVide() == 0)
					{
						$ctn .= $this->RenduLiens() ;
					}
					$ctn .= '</div>' ;
				}
				return $ctn ;
			}
			protected function RenduLiens()
			{
				$ctn = '' ;
				$ctnsLien = $this->CtnsLienRendu() ;
				for($i=count($ctnsLien) - 1; $i >= 0; $i--)
				{
					$ctnLien = $ctnsLien[$i] ;
					if($i < count($ctnsLien) - 1)
					{
						$ctn .= $this->SeparateurLiens ;
					}
					$ctn .= '<a href="'.$ctnLien->Url.'"'.(($ctnLien->AttrsHtmlExtra != '') ? ' '.$ctnLien->AttrsHtmlExtra : '').''.(($this->NomClasseCSSLien != '') ? ' class="'.$this->NomClasseCSSLien.'"' : '').'>'.$ctnLien->Titre.'</a>' ;
				}
				return $ctn ;
			}
			public function LiensTrouves()
			{
				return (count($this->CtnsLien) > 0) ;
			}
			public function EstVide()
			{
				return ($this->LiensTrouves() == false) ;
			}
			public function InsereDefLien($requeteSelect, $formatUrl, $formatTitre)
			{
				$lien = new PvDefLienFilArianeDonnees() ;
				$lien->RequeteSelection = $requeteSelect ;
				$lien->FormatUrl = $formatUrl ;
				$lien->FormatTitre = $formatTitre ;
				$this->DefsLien[] = & $lien ;
				return $lien ;
			}
			public function InsereDefLienStatique($formatUrl, $formatTitre)
			{
				$lien = new PvDefLienFilArianeDonnees() ;
				$lien->FormatUrl = $formatUrl ;
				$lien->FormatTitre = $formatTitre ;
				$this->DefsLien[] = & $lien ;
				return $lien ;
			}
			public function InsereDefLienFixe($formatUrl, $formatTitre)
			{
				return $this->InsereDefLienStatique($formatUrl, $formatTitre) ;
			}
		}
		class PvDefLienFilArianeDonnees extends PvObjet
		{
			public $RequeteSelection ;
			public $FormatTitre ;
			public $FormatUrl ;
			public $AttrsHtmlExtra ;
			public $NomClasseCSS ;
			public $Recursif = 0 ;
			public $Obligatoire = 1 ;
		}
		class PvCtnLienFilArianeDonnees
		{
			public $Titre ;
			public $Url ;
			public $AttrsHtmlExtra ;
		}
		
		class PvElementCommandeBase extends PvElementAccessible
		{
			public $TypeElementCommande = "base" ;
			public $FiltresCibles = array() ;
			public $IndiceCommande = -1 ;
			public $CommandeParent = null ;
			public $ScriptParent = null ;
			public $ZoneParent = null ;
			public $ApplicationParent = null ;
			public $FormulaireDonneesParent = null ;
			public function AdopteCommande($indice, & $commande)
			{
				$this->IndiceCommande = $indice ;
				$this->CommandeParent = & $commande ;
				$this->FormulaireDonneesParent = & $commande->FormulaireDonneesParent ;
				$this->ScriptParent = & $commande->FormulaireDonneesParent->ScriptParent ;
				$this->ZoneParent = & $commande->FormulaireDonneesParent->ZoneParent ;
				$this->ApplicationParent = & $commande->FormulaireDonneesParent->ApplicationParent ;
			}
			protected function LieFiltresCibles()
			{
				$this->FormulaireDonneesParent->LieFiltres($this->FiltresCibles) ;
			}
			public function CibleTousFiltres()
			{
				if($this->EstNul($this->FormulaireDonneesParent))
				{
					return ;
				}
				$nomFiltres = array_keys($this->FormulaireDonneesParent->FiltresEdition) ;
				$this->FiltresCibles = array() ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$this->FiltresCibles[] = & $this->FormulaireDonneesParent->FiltresEdition[$nomFiltre] ;
				}
			}
			public function CibleFiltres()
			{
				if($this->EstNul($this->FormulaireDonneesParent))
				{
					return ;
				}
				$args = func_get_args() ;
				// print_r($args) ;
				$nomFiltres = array_keys($this->FormulaireDonneesParent->FiltresEdition) ;
				// print_r($nomFiltres) ;
				foreach($nomFiltres as $i => $nomFiltre)
				{
					$filtre = & $this->FormulaireDonneesParent->FiltresEdition[$nomFiltre] ;
					if(in_array($filtre->NomElementScript, $args) || in_array($nomFiltre, $args, true))
					{
						$this->FiltresCibles[] = & $filtre ;
					}
				}
			}
		}
	}
	
?>