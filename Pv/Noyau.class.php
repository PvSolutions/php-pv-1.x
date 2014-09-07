<?php
	
	/*
	 *
	 Librairies PV = PERSONAL VIEW
	 * */
	if(! defined('PV_NOYAU'))
	{
		if(! defined('UTILS_INCLUDED'))
		{
			include dirname(__FILE__)."/../misc/utils.php" ;
		}
		if(! defined('SERVICES_JSON_SLICE'))
		{
			include dirname(__FILE__)."/../misc/Services_JSON.class.php" ;
		}
		if(! defined('COMMON_DB_INCLUDED'))
		{
			include dirname(__FILE__)."/../CommonDB/Base.class.php" ;
		}
		if(! defined('PV_JOURNAL_TRACES'))
		{
			include dirname(__FILE__)."/JournalTraces.class.php" ;
		}
		define('PV_NOYAU', 1) ;
		
		class PvObjet
		{
			public $ID = "" ;
			public $IDInstance = "" ;
			public $IDInstanceCalc = "" ;
			public $NomClasseInstance = "" ;
			public $IndiceInstance = 0 ;
			static $TotalInstances = 0 ;
			public function ObtientValStatique($nomPropriete, $valParDefaut=false)
			{
				return $this->ObtientValeurStatique($nomPropriete, $valParDefaut) ;
			}
			public function AffecteValStatique($nomPropriete, $valParDefaut=false)
			{
				return $this->AffecteValeurStatique($nomPropriete, $valParDefaut) ;
			}
			public function ObtientValeurStatique($nomPropriete, $valeurDefaut=false)
			{
				$valeur = $valeurDefaut ;
				try { eval('$valeur = '.get_class($this).'::$'.$nomPropriete.' ;') ; } catch(Exception $ex) {} ;
				return $valeur ;
			}
			public function AffecteValeurStatique($nomPropriete, $valeur)
			{
				try { eval(get_class($this).'::$'.$nomPropriete.' = $valeur ;') ; } catch(Exception $ex) {}
				return $valeur ;
			}
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
				$this->InitConfigInstance() ;
			}
			protected function InitConfigInstance()
			{
				$totalInstances = $this->ObtientValeurStatique("TotalInstances") ;
				$totalInstances++ ;
				$this->NomClasseInstance = get_class($this) ;
				$this->IndiceInstance = $totalInstances ;
				$this->AffecteValeurStatique("TotalInstances", $totalInstances) ;
				$this->IDInstance = $this->NomClasseInstance."_".$this->IndiceInstance ;
				$this->IDInstanceCalc = $this->IDInstance ;
			}
			public function ChargeConfig()
			{
			}
			public function EstNul($objet)
			{
				if($objet == null)
					return 1 ;
				return (get_class($objet) == "PvNul") ? 1 : 0 ;
				// return (get_class($objet) == "PvNul" or get_class($objet) == get_class($this)) ? 1 : 0 ;
			}
			public function EstNonNul($objet)
			{
				return ($this->EstNul($objet)) ? 0 : 1 ;
			}
			public function EstPasNul($objet)
			{
				return $this->EstNonNul($objet) ;
			}
			public function ValeurNulle()
			{
				return new PvNul() ;
			}
			public function & ObjetNul()
			{
				$objet = $this->ValeurNulle() ;
				return $objet ;
			}
			public function CorrigeChemin($cheminFichier)
			{
				$resultat = $cheminFichier ;
				if(DIRECTORY_SEPARATOR != "/")
					$resultat = str_replace("/", DIRECTORY_SEPARATOR, $resultat) ;
				if(DIRECTORY_SEPARATOR != "\\")
					$resultat = str_replace("\\", DIRECTORY_SEPARATOR, $resultat) ;
				return $resultat ;
			}
		}
		class PvNul extends PvObjet
		{
			public $EstNul = 1 ;
		}
		
		class PvApplication extends PvObjet
		{
			public $CheminIconeElem = "images/icone-elem-app.png" ;
			public $CheminMiniatureElem = "images/miniature-elem-app.png" ;
			public $ModelesOperation = array() ;
			public $IHMs = array() ;
			public $SystTrad ;
			public $BasesDonnees = array() ;
			public $ProcessusPersistants = array() ;
			public $ServicesPersists = array() ;
			public $TachesProgs = array() ;
			public $ServicesRequete = array() ;
			public $Elements = array() ;
			public $CheminFichierElementActif = "" ;
			public $CheminFichierAbsolu = "" ;
			public $CheminFichierRelatif = "../.." ;
			public $NomElementActif = "" ;
			public $ElementActif = null ;
			public $ElementHorsLigne = null ;
			public $DebogageActive = 0 ;
			public function Debogue($niveau, $message)
			{
				if(! $this->DebogageActive)
				{
					return ;
				}
			}
			public function ChargeConfig()
			{
				$this->ChargeBasesDonnees() ;
				$this->ChargeSystTrad() ;
				$this->ChargeIHMs() ;
				$this->ChargeTachesProgs() ;
				$this->ChargeServicesPersists() ;
				$this->ChargeServicesRequete() ;
				$this->ChargeElementHorsLigne() ;
			}
			protected function ChargeBasesDonnees()
			{
			}
			public function Traduit($nomExpr, $params=array(), $valParDefaut='', $nomTrad='')
			{
				return $this->SystTrad->Execute($nomExpr, $params, $valParDefaut, $nomTrad) ;
			}
			public function ActiveTraducteur($nomTrad)
			{
				return $this->SystTrad->ActiveTraducteur($nomTrad) ;
			}
			public function CreeSystTrad()
			{
				return new PvSystemeTradsBase() ;
			}
			protected function ChargeSystTrad()
			{
				$this->SystTrad = $this->CreeSystTrad() ;
				$this->SystTrad->ChargeConfig() ;
			}
			protected function ChargeIHMs()
			{
			}
			protected function ChargeTachesProgs()
			{
			}
			protected function ChargeServicesPersists()
			{
			}
			protected function ChargeServicesRequete()
			{
			}
			protected function ChargeElementHorsLigne()
			{
				$this->ElementHorsLigne = null ;
			}
			public function InscritElement($nom, & $element)
			{
				if(isset($this->Elements[$nom]))
				{
					die("Impossible d'inscrire l'element ".$nom.". Il existe deja.") ;
				}
				$this->Elements[$nom] = & $element ;
				$element->AdopteApplication($nom, $this) ;
			}
			public function InscritIHM($nom, & $ihm)
			{
				$this->IHMs[$nom] = & $ihm ;
				$this->InscritElement($nom, $ihm) ;
			}
			public function InscritModeleOperation($nom, & $modeleOperation)
			{
				$this->ModelesOperation[$nom] = & $modeleOperation ;
				$this->InscritElement($nom, $modeleOperation) ;
			}
			public function InscritProcessusPersistant($nom, & $processusPersistant)
			{
				$this->ProcessusPersistants[$nom] = & $processusPersistant ;
				$this->InscritElement($nom, $processusPersistant) ;
			}
			public function InscritTacheProg($nom, & $tacheProg)
			{
				$this->TachesProgs[$nom] = & $tacheProg ;
				$this->InscritElement($nom, $tacheProg) ;
			}
			public function InscritServicePersist($nom, & $srvPersist)
			{
				$this->ServicesPersists[$nom] = & $srvPersist ;
				$this->InscritElement($nom, $srvPersist) ;
			}
			public function InscritServiceRequete($nom, & $serviceRequete)
			{
				$this->ServicesRequete[$nom] = & $serviceRequete ;
				$this->InscritElement($nom, $serviceRequete) ;
			}
			public function InscritBaseDonnees($nom, & $bd)
			{
				$this->BasesDonnees[$nom] = & $bd ;
				// $this->InscritElement($nom, $bd) ;
			}
			public function EnregIHM(& $ihm)
			{
				$this->InscritIHM($ihm->IDInstance, $ihm) ;
			}
			public function EnregModeleOperation(& $modeleOperation)
			{
				$this->InscritModeleOperation($modeleOperation->IDInstance, $modeleOperation) ;
			}
			public function EnregProcessusPersistant(& $processusPersistant)
			{
				$this->InscritProcessusPersistant($processusPersistant->IDInstance, $processusPersistant) ;
			}
			public function EnregServiceRequete(& $serviceRequete)
			{
				$this->InscritServiceRequete($serviceRequete->IDInstance, $serviceRequete) ;
			}
			public function DeclareIHM($nom='', $classeIHM='', $cheminFichier='')
			{
				if(! class_exists($classeIHM))
				{
					die("La classe $classIHM n'existe pas. Elle ne peut pas etre inscrte comme zone IHM") ;
				}
				$ihm = new $classeIHM() ;
				if($nom == '')
				{
					$nom = "IHM_".(count($this->IHMs) + 1) ;
				}
				$ihm->CheminFichierRelatif = $cheminFichier ;
				$nomPropriete = 'IHM'.ucfirst($nom) ;
				$this->$nomPropriete = & $ihm ;
				$this->InscritIHM($nom, $ihm) ;
			}
			public function DetecteElementActif()
			{
				$ok = 0 ;
				$this->DetecteCheminFichierElementActif() ;
				$this->ElementActif = $this->ValeurNulle() ;
				$this->NomElementActif = "" ;
				$cles = array_keys($this->Elements) ;
				foreach($cles as $i => $cle)
				{
					$element = & $this->Elements[$cle] ;
					$ok = $element->EstActif($this->CheminFichierAbsolu, $this->CheminFichierElementActif) ;
					if($ok)
					{
						$this->NomElementActif = $element->NomElementApplication ;
						$this->ElementActif = & $element ;
						break ;
					}
				}
				return $ok ;
			}
			public function ExecuteElementActif()
			{
				if($this->EstPasNul($this->ElementActif))
				{
					$this->ElementActif->ChargeConfig() ;
					$this->ElementActif->Execute() ;
				}
				else
				{
					if($this->EstPasNul($this->ElementHorsLigne))
					{
						$this->ElementHorsLigne->ChargeConfig() ;
						$this->ElementHorsLigne->Execute() ;
					}
				}
			}
			protected function DetecteCheminFichierElementActif()
			{
				$this->CheminFichierAbsolu = dirname(__FILE__) ;
				if($this->CheminFichierRelatif != "")
				{
					$this->CheminFichierAbsolu .= "/".$this->CheminFichierRelatif ;
				}
				$this->CheminFichierAbsolu = realpath($this->CheminFichierAbsolu) ;
				if(php_sapi_name() == "cli")
				{
					$this->CheminFichierElementActif = $_SERVER["argv"][0] ;
				}
				else
				{
					$this->CheminFichierElementActif = $_SERVER["SCRIPT_FILENAME"] ;
				}
				$this->CheminFichierElementActif = realpath($this->CheminFichierElementActif) ;
				// echo "Chemin actif : ".$this->CheminFichierElementActif."\n" ;
			}
			public function Execute()
			{
				// print_r("MMMM ".count($this->IHMs)) ;
				$this->ChargeConfig() ;
				$this->DetecteElementActif() ;
				$this->ExecuteElementActif() ;
			}
		}
		
		class PvElementApplication extends PvObjet
		{
			public $ApplicationParent = null ;
			public $NomElementApplication = "" ;
			public $CheminFichierRelatif = "" ;
			public $AccepterTousChemins = 0 ;
			public $Titre ;
			public $CheminIcone ;
			public $CheminMiniature ;
			public $Description ;
			public $DelaiExecution = 0 ;
			public $DebutExecution = 0 ;
			public $FinExecution = 0 ;
			public $TempsExecution = 0 ;
			public $DelaiExecutionPrec = 0 ;
			public function ObtientCheminIcone()
			{
				if($this->CheminIcone != '')
					return $this->CheminIcone ;
				return $this->ApplicationParent->CheminIconeElem;
			}
			public function ObtientCheminMiniature()
			{
				if($this->CheminMiniature != '')
					return $this->CheminMiniature ;
				return $this->ApplicationParent->CheminMiniatureElem ;
			}
			public function ObtientTitre()
			{
				if($this->Titre != '')
					return $this->Titre ;
				return ucfirst($this->NomElementApplication) ;
			}
			public function ObtientDescription()
			{
				return $this->Description ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeConfigMailsPourAlertes() ;
				$this->ChargeResponsables() ;
				$this->ChargeSupports() ;
				$this->ChargeDeveloppeurs() ;
			}
			public function Traduit($nomExpr, $params=array(), $valParDefaut='', $nomTrad='')
			{
				return $this->ApplicationParent->Traduit($nomExpr, $params, $valParDefaut, $nomTrad) ;
			}
			public function ActiveTraducteur($nomTrad)
			{
				return $this->ApplicationParent->ActiveTraducteur($nomTrad) ;
			}
			public function AdopteApplication($nom, & $application)
			{
				$this->ApplicationParent = & $application ;
				$this->NomElementApplication = $nom ;
				// print get_class($this)."<br>" ;
			}
			public function EstActif($cheminFichierAbsolu, $cheminFichierElementActif)
			{
				if($this->AccepterTousChemins)
				{
					return 1 ;
				}
				$cheminFichier = realpath($cheminFichierAbsolu.DIRECTORY_SEPARATOR.$this->CheminFichierRelatif) ;
				// echo get_class($this).' : '.$cheminFichierAbsolu.DIRECTORY_SEPARATOR.$this->CheminFichierRelatif.' hhh<br>' ;
				$ok = ($this->CorrigeChemin($cheminFichier) == $this->CorrigeChemin($cheminFichierElementActif)) ? 1 : 0 ;
				return $ok ;
			}
			protected function DemarreExecution()
			{
				if($this->DelaiExecution > 0)
				{
					$this->DelaiExecutionPrec = @set_time_limit($this->DelaiExecution) ;
					$this->DebutExecution = date("U") ;
				}
			}
			public function ObtientTempsExecution()
			{
				return $this->FinExecution - $this->DebutExecution ;
			}
			protected function TermineExecution()
			{
				@set_time_limit($this->DelaiExecutionPrec) ;
				$this->FinExecution = date("U") ;
			}
			public function Execute()
			{
				$this->DemarreExecution() ;
				$this->TermineExecution() ;
			}
		}
		
		class PvIHM extends PvElementApplication
		{
			public $TypeIHM = "indefini" ;
		}
		class PvModeleOperation extends PvElementApplication
		{
			public $Entree = null ;
			public $Sortie = null ;
		}
		class PvProcessusPersistant extends PvElementApplication
		{
			public $PID = 0 ;
			public $MaxRepetitions = 0 ;
			public $TotalRepetitions = 0 ;
			public $Arreter = 0 ;
			public $MotifArret = null ;
			public $DelaiAttenteEntreRepetitions = 60 ;
			public $DelaiMaxTraitement = 0 ;
			public $AccepterPlusieursInstances = 0 ;
			public $TuerAncienneInstance = 0 ;
			public function RepeteBoucle()
			{
				$this->TotalRepetitions = 0 ;
				while(! $this->Arreter && $this->MaxRepetitionsAtteint())
				{
					$this->ExecuteTraitement() ;
					if($this->DelaiAttenteEntreRepetitions > 0)
					{
						$this->PauseBoucle() ;
					}
					$this->TotalRepetitions++ ;
				}
			}
			protected function DefinitMotifArret($code, $libelle, $valeurs=array())
			{
				$this->MotifArret = new PvMotifArretProcessusPers() ;
				$this->MotifArret->Code = $code ;
				$this->MotifArret->Libelle = $libelle ;
				$this->MotifArret->Valeurs = $valeurs ;
			}
			public function PauseBoucle()
			{
				sleep($this->DelaiAttenteEntreRepetitions) ;
			}
			public function Execute()
			{
				if($this->EnExecution())
				{
					return ;
				}
				$this->PrepareEnvironnement() ;
				$this->RepeteBoucle() ;
			}
			protected function PrepareEnvironnement()
			{
				$this->ConfirmeExecution() ;
			}
			protected function EnExecution()
			{
				$ok = 0 ;
				return $ok ;
			}
			protected function ConfirmeExecution()
			{
				$this->PID = getmypid() ;
			}
			public function ExecuteTraitement()
			{
			}
			public function MaxRepetitionsAtteint()
			{
				return ($this->MaxRepetitions <= 0 || $this->MaxRepetitions <= $this->TotalRepetitions) ? 1 : 0 ;
			}
		}
		
		class PvTacheProg extends PvElementApplication
		{
			public $Declenchs = array() ;
			public function Execute()
			{
			}
		}
		class PvServicePersist extends PvElementApplication
		{
			public $Arreter = 0 ;
			public function EstPret()
			{
				return 1 ;
			}
			public function FonctionneBien()
			{
				return 1 ;
			}
			protected function RepeteBoucle()
			{
				while(! $this->Arreter)
				{
					$this->ExecuteSession() ;
				}
			}
			protected function ExecuteSession()
			{
			}
			protected function PrepareEnvironnement()
			{
			}
			public function Execute()
			{
				$this->PrepareEnvironnement() ;
				$this->RepeteBoucle() ;
			}
		}
		
		class PvMotifArretProcessusPers
		{
			public $Code = '' ;
			public $Libelle = '' ;
			public $Valeurs = array() ;
		}
		class PvServiceRequete extends PvElementApplication
		{
		}
		
		class PvSystemeTradsBase extends PvObjet
		{
			public $Traducteurs = array() ;
			public $NomTraducteurActif ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeTraducteurs() ;
				$this->DetecteTraducteurActif() ;
			}
			protected function DetecteTraducteurActif()
			{
				if(count($this->Traducteurs) > 0)
				{
					$nomTrads = array_keys($this->Traducteurs) ;
					$this->NomTraducteurActif = $nomTrads[0] ;
				}
			}
			protected function ChargeTraducteurs()
			{
			}
			public function TraducteurActif()
			{
				$nomTrad = $this->NomTraducteurActif ;
				$traducteur = new PvTraducteur() ;
				$traducteur->EstNul = 1 ;
				if(isset($this->Traducteurs[$nomTrad]))
				{
					$traducteur = & $this->Traducteurs[$nomTrad] ;
				}
				return $traducteur ;
			}
			public function Execute($nomExpr, $params=array(), $valParDefaut='', $nomTrad='')
			{
				$traducteur = null;
				if($nomTrad == '' || ! isset($this->Traducteurs[$nomTrad]))
				{
					$traducteur = & $this->Traducteurs[$nomTrad] ;
				}
				else
				{
					$traducteur = $this->TraducteurActif() ;
				}
				return $traducteur->Execute($nomExpr, $params, $valParDefaut) ;
			}
			public function ActiveTraducteur($nomTrad='')
			{
				if(! isset($this->Traducteurs[$nomTrad]))
				{
					return ;
				}
				$this->NomTraducteurActif = $nomTrad ;
			}
		}
		class PvTraducteur extends PvObjet
		{
			public $Exprs = array() ;
			public $IdLangue = 0 ;
			public $NomLangue = "" ;
			public $LibelleLangue = "" ;
			public $EstNul = 0 ;
			public function Execute($nomExpr, $params=array(), $valParDefaut='')
			{
				$val = $valParDefaut ;
				if(isset($this->Exprs[$nomExpr]))
				{
					$val = _parse_pattern($this->Exprs[$nomExpr], $params) ;
				}
				return $val ;
			}
		}
		
		if(! function_exists('RemoveMagicQuotes'))
		{
			function RemoveMagicQuotes ($postArray, $trim = false)
			{
				if (get_magic_quotes_gpc() == 1)
				{
						if ( is_array($postArray) )
						{
								$newArray = array();   
							 
								foreach ($postArray as $key => $val)
								{
										if (is_array($val))
										{
												$newArray[$key] = removeMagicQuotes ($val, $trim);
										}
										else
										{
												if ($trim == true)
												{
														$val = trim($val);
												}
												$newArray[$key] = stripslashes($val);
										}
								}
								return $newArray;
						}
						else
						{
								return stripcslashes($postArray);
						}
				}
				else
				{
						return $postArray;   
				}
			}
		}
		
		if(php_sapi_name() != 'cli' && get_magic_quotes_gpc() == 1)
		{
			$_PHP_POST = $_POST ;
			$_PHP_GET = $_GET ;
			$_PHP_COOKIE = $_COOKIE ;
			
			$_GET = RemoveMagicQuotes($_GET) ;
			$_POST = RemoveMagicQuotes($_POST) ;
			$_COOKIE = RemoveMagicQuotes($_COOKIE) ;
		}
	}
	
?>