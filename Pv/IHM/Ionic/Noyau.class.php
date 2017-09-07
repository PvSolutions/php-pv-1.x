<?php
	
	if(! defined('PV_NOYAU_IONIC'))
	{
		define('PV_NOYAU_IONIC', 1) ;
		
		class PvObjetBaseIonic
		{
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
			}
			public function ChargeConfig()
			{
			}
			public function EstNul($objet)
			{
				if($objet == null)
					return 1 ;
				$nomClasse = get_class($objet) ;
				$nomClasseObj = get_class($this) ;
				return (in_array($nomClasse, array("PvNul", "stdClass"))) ? 1 : 0 ;
			}
			public function EstNonNul($objet)
			{
				return ($this->EstNul($objet)) ? 0 : 1 ;
			}
			public function EstPasNul($objet)
			{
				return $this->EstNonNul($objet) ;
			}
		}
		
		class PvElemZoneIonic extends PvObjetBaseIonic
		{
			public $NomElementZone ;
			public $ZoneParent ;
			public function AdopteZone($nom, & $zone)
			{
				$this->NomElementZone = $nom ;
				$this->ZoneParent = & $zone ;
				// print get_class($this)." : ".get_class($this->ZoneParent)." kkk<br>" ;
			}
			protected function PrepareFichiers()
			{
			}
			public function GenereFichiers()
			{
			}
			public function CheminProjetIonic()
			{
				return $this->ZoneParent->CheminProjetIonic ;
			}
			public function GenereFichierSrc(& $fichSrc)
			{
				$fichSrc->GenereFichierRelatif($this->CheminProjetIonic()) ;
			}
		}
		
		class PvFichSrcBaseIonic extends PvObjetBaseIonic
		{
			public $CheminRelatif = "" ;
			public function GenereFichierRelatif($cheminProjet)
			{
				if($this->CheminRelatif == "")
				{
					return ;
				}
				$this->GenereFichier($cheminProjet."/src/".$this->CheminRelatif) ;
			}
			public function GenereFichier($cheminFich)
			{
				$fh = fopen($cheminFich, "w") ;
				if(is_resource($fh))
				{
					$this->EcritFluxFichier($fh) ;
					fclose($fh) ;
				}
			}
			protected function EcritFluxFichier(& $fh)
			{
			}
		}
		
		class PvDecoratorCleTsIonic
		{
			public $NomDecorator ;
			public $animations = null ;
			public $changeDetection = null ;
			public $encapsulation = null ;
			public $entryComponents = null ;
			public $exportAs = null ;
			public $host = null ;
			public $inputs = null ;
			public $interpolation = null ;
			public $moduleId = null ;
			public $outputs = null ;
			public $providers = array() ;
			public $queries = null ;
			public $selector = null ;
			public $styleUrls = null ;
			public $styles = null ;
			public $template = null ;
			public $templateUrl = null ;
			public $viewProviders = null ;
			public function CodeSource()
			{
				return $this->NomDecorator.'('.$this->FormatJson().')' ;
			}
			public function FormatJson()
			{
				$props = array() ;
				if($this->animations !== null)
				{
					$props[] = "animations : ".svc_json_encode($this->animations) ;
				}
				if($this->changeDetection !== null)
				{
					$props[] = "changeDetection : ".svc_json_encode($this->changeDetection) ;
				}
				if($this->encapsulation !== null)
				{
					$props[] = "encapsulation : ".svc_json_encode($this->encapsulation) ;
				}
				if($this->entryComponents !== null)
				{
					$props[] = "entryComponents : ".svc_json_encode($this->entryComponents) ;
				}
				if($this->exportAs !== null)
				{
					$props[] = "exportAs : ".svc_json_encode($this->exportAs) ;
				}
				if($this->host !== null)
				{
					$props[] = "host : ".svc_json_encode($this->host) ;
				}
				if($this->inputs !== null)
				{
					$props[] = "inputs : ".svc_json_encode($this->inputs) ;
				}
				if($this->interpolation !== null)
				{
					$props[] = "interpolation : ".svc_json_encode($this->interpolation) ;
				}
				if($this->moduleId !== null)
				{
					$props[] = "moduleId : ".svc_json_encode($this->moduleId) ;
				}
				if($this->outputs !== null)
				{
					$props[] = "outputs : ".svc_json_encode($this->outputs) ;
				}
				if(count($this->providers) > 0)
				{
					$ctn = "providers : [" ;
					foreach($this->providers as $i => $nomClasse)
					{
						$ctn .= (($i > 0) ? "," : "").$nomClasse ;
					}
					$ctn .= "]" ;
					$props[] = $ctn ;
				}
				if($this->queries !== null)
				{
					$props[] = "queries : ".svc_json_encode($this->queries) ;
				}
				if($this->selector !== null)
				{
					$props[] = "selector : ".svc_json_encode($this->selector) ;
				}
				if($this->styleUrls !== null)
				{
					$props[] = "styleUrls : ".svc_json_encode($this->styleUrls) ;
				}
				if($this->styles !== null)
				{
					$props[] = "styles : ".svc_json_encode($this->styles) ;
				}
				if($this->template !== null)
				{
					$props[] = "template : ".svc_json_encode($this->template) ;
				}
				if($this->templateUrl !== null)
				{
					$props[] = "templateUrl : ".svc_json_encode($this->templateUrl) ;
				}
				if($this->viewProviders !== null)
				{
					$props[] = "viewProviders : ".svc_json_encode($this->viewProviders) ;
				}
				if(count($props) == 0)
				{
					return '' ;
				}
				return "{".join($props, ", ")."}" ;
			}
		}
		class PvImportCleTsIonic
		{
			public $CheminRelatif ;
			public $Classes = array() ;
			public function InsereClasses($classes)
			{
				if(is_string($classes))
				{
					$classes = array($classes) ;
				}
				foreach($classes as $i => $classe)
				{
					if(! in_array($classe, $this->Classes))
					{
						$this->Classes[] = $classe ;
					}
				}
			}
			public function CodeSource()
			{
				$ctn = '' ;
				if(count($this->Classes) > 0)
				{
					$ctn .= 'import {'.join(", ",  $this->Classes).'} from \''.$this->CheminRelatif.'\' ;' ;
				}
				else
				{
					$ctn .= 'import \''.$this->CheminRelatif.'\' ;' ;
				}
				return $ctn ;
			}
		}
		class PvClasseCleTsIonic
		{
			public $Exportable = 1 ;
			public $NomClasse ;
			public $MembresCle = array() ;
			public $MtdConstruct ;
			public $MtdsCle = array() ;
			public function __construct()
			{
				$this->MtdConstruct = new PvMtdCleClasseTsIonic() ;
				$this->MtdConstruct->NomMtd = "constructor" ;
			}
			public function & CreeMethode($nom, $arguments=array(), $corps="")
			{
				$mtd = new PvMtdCleClasseTsIonic() ;
				$mtd->NomMtd = $nom ;
				$mtd->Arguments = $arguments ;
				$mtd->CorpsBrut = $corps ;
				return $mtd ;
			}
			public function & InsereMethode($nom, $arguments=array(), $corps="")
			{
				$mtd = $this->CreeMethode($nom, $arguments, $corps) ;
				$this->MtdsCle[$mtd->NomMtd] = & $mtd ;
				return $mtd ;
			}
			public function & InsereMtd($nom, $arguments=array(), $corps="")
			{
				return $this->InsereMethode($nom, $arguments, $corps) ;
			}
			public function & CreeMembre($nom, $valeurDefaut="", $type="")
			{
				$membre = new PvMembreCleClasseTsIonic() ;
				$membre->Nom = $nom ;
				$membre->Type = $type ;
				$membre->ValeurDefaut = $valeurDefaut ;
				return $membre ;
			}
			public function & InsereMembre($nom, $valeurDefaut="", $type="")
			{
				$this->MembresCle[$nom] = $this->CreeMembre($nom, $valeurDefaut, $type) ;
				return $this->MembresCle[$nom] ;
			}
			public function CodeSource()
			{
				$ctn = '' ;
				if($this->NomClasse == "")
				{
					return $ctn ;
				}
				$ctn .= ($this->Exportable == 1) ? 'export ' : '' ;
				$ctn .= 'class '.$this->NomClasse.'{'.PHP_EOL ;
				foreach($this->MembresCle as $i => $mb)
				{
					$ctn .= $mb->CodeSource().PHP_EOL ;
				}
				$ctn .= $this->MtdConstruct->CodeSource(). PHP_EOL ;
				foreach($this->MtdsCle as $i => $mtd)
				{
					$ctn .= $mtd->CodeSource().PHP_EOL ;
				}
				$ctn .= '}' ;
				return $ctn ;
			}
		}
		class PvMtdCleClasseTsIonic
		{
			public $NomMtd ;
			public $TypeRetour ;
			public $Arguments = array() ;
			public $CorpsBrut = "" ;
			public function DefinitArgument($nom)
			{
				if(in_array($nom, $this->Arguments))
				{
					return ;
				}
				$this->Arguments[] = $nom ;
			}
			public function CodeSource()
			{
				return $this->NomMtd.'('.join(", ", $this->Arguments).')'.(($this->TypeRetour != '') ? ':'.$this->TypeRetour : '').' {'.PHP_EOL
					.(($this->CorpsBrut != '') ? $this->CorpsBrut. PHP_EOL : '')
					.'}' ;
			}
		}
		class PvMembreCleClasseTsIonic
		{
			public $Cast ;
			public $Nom ;
			public $Type ;
			public $ValeurDefaut ;
			public function CodeSource()
			{
				$ctn = '' ;
				if($this->Cast != '')
				{
					$ctn .= $this->Cast.' ' ;
				}
				$ctn .= $this->Nom ;
				if($this->Type != '')
				{
					$ctn .= ': '.$this->Type ;
				}
				if($this->ValeurDefaut != '')
				{
					$ctn .= ' = '.$this->ValeurDefaut ;
				}
				$ctn .= ';' ;
				return $ctn ;
			}
		}
		
		class PvPortionHtmlBaseIonic
		{
			public function Rendu()
			{
			}
		}
		
		class PvRenduHtmlIonic extends PvPortionHtmlBaseIonic
		{
			public $Contenu ;
			public function Rendu()
			{
				return $this->Contenu ;
			}
		}
		
		class PvTagAngularIonic extends PvPortionHtmlBaseIonic
		{
		}
		
		class PvTagBaseHtmlIonic extends PvPortionHtmlBaseIonic
		{
			protected $_NomTag = "" ;
			protected $InclureRenduBalise = 1 ;
			protected $InclureRenduAttrs = 1 ;
			protected $InclureRenduTagsFils = 1 ;
			protected $_Attrs = array() ;
			protected $_TagsFils = array() ;
			public function __construct()
			{
				$this->InitConfig() ;
			}
			protected function InitConfig()
			{
			}
			public function DefinitAttr($nom, $valeur)
			{
				$this->_Attrs[$nom] = $valeur ;
			}
			public function DefinitAttrs($attrs=array())
			{
				foreach($attrs as $nom => $valeur)
				{
					$this->_Attrs[$nom] = $valeur ;
				}
			}
			public function Attr($nom, $valeurDefaut=null)
			{
				if(! isset($this->_Attrs[$nom]))
				{
					return $valeurDefaut ;
				}
				return $this->_Attrs[$nom] ;
			}
			public function & Attrs()
			{
				return $this->_Attrs ;
			}
			public function & InsereContent($texte)
			{
				$ctn = new PvTagContentHtmlIonic() ;
				$ctn->DefinitTexte($texte) ;
				return $this->InsereTagFils($ctn) ;
			}
			public function & InsereRendu($rendu)
			{
				$tag = new PvRenduHtmlIonic() ;
				$tag->Contenu = $rendu ;
				return $this->InsereTagFils($tag) ;
			}
			public function DefinitTexte($texte)
			{
				$this->_Texte = $texte ;
			}
			public function & InsereTagFils($tag)
			{
				$this->_TagsFils[] = $tag ;
				return $tag ;
			}
			public function & TagsFils()
			{
				return $this->_TagsFils ;
			}
			public function Rendu()
			{
				$ctn = '' ;
				if($this->InclureRenduBalise == 1)
				{
					$ctn .= '<'.$this->_NomTag ;
					if($this->InclureRenduAttrs == 1)
					{
						foreach($this->_Attrs as $n => $v)
						{
							if($n == $v)
							{
								$ctn .= ' '.$n ;
							}
							else
							{
								$ctn .= ' '.$n.'="'.htmlspecialchars($v).'"' ;
							}
						}
					}
					$ctn .= '>' ;
				}
				if($this->InclureRenduTagsFils == 1)
				{
					foreach($this->_TagsFils as $i => & $tag)
					{
						$ctn .= $tag->Rendu() ;
					}
				}
				if($this->InclureRenduBalise == 1)
				{
					$ctn .= '</'.$this->_NomTag.'>' ;
				}
				return $ctn ;
			}
		}
		class PvTagContentHtmlIonic extends PvTagBaseHtmlIonic
		{
			protected $InclureRenduBalise = 0 ;
			protected $InclureRenduAttrs = 0 ;
			protected $InclureRenduTagsFils = 0 ;
			protected $_Texte ;
			public function Rendu()
			{
				return htmlentities($this->_Texte) ;
			}
		}
		class PvTagRacineHtmlIonic extends PvTagBaseHtmlIonic
		{
			protected $InclureRenduBalise = 0 ;
			protected $InclureRenduTagsFils = 1 ;
		}
		class PvTagIonNav extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-nav" ;
			protected $_Attrs = array("[root]" => "rootPage") ;
		}
		class PvTagIonMenu extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-menu" ;
			protected $_Attrs = array("[content]" => "content") ;
			public $TagContent ;
			public $TagList ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->TagContent = $this->InsereTagFils(new PvTagIonContent()) ;
				$this->TagList = $this->TagContent->InsereTagFils(new PvTagIonList()) ;
			}
			public function & InsereTagFilsList($tag)
			{
				$item = $this->TagList->InsereTagFils(new PvTagIonItem()) ;
				return $item->InsereTagFils($tag) ;
			}
		}
		class PvTagIonHeader extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-header" ;
		}
		class PvTagIonToolbar extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-toolbar" ;
		}
		class PvTagIonContent extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-content" ;
		}
		class PvTagIonNavbar extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-navbar" ;
		}
		class PvTagIonTitle extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-title" ;
		}
		class PvTagIonList extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-list" ;
		}
		class PvTagIonItem extends PvTagBaseHtmlIonic
		{
			protected $_NomTag = "ion-item" ;
		}
		
		class PvFichSrcHtmlIonic extends PvFichSrcBaseIonic
		{
			protected $_TagRacine ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->_TagRacine = new PvTagRacineHtmlIonic() ;
			}
			public function & TagRacine()
			{
				return $this->_TagRacine ;
			}
			public function & DefinitTagRacine($tag)
			{
				$this->_TagRacine = $tag ;
				return $this->_TagRacine ;
			}
			protected function EcritFluxFichier(& $fh)
			{
				fputs($fh, $this->_TagRacine->Rendu()) ;
			}
		}
		class PvFichSrcTsIonic extends PvFichSrcBaseIonic
		{
			public $ImportsGlobaux = array() ;
			public $ImportsLocaux = array() ;
			public $CorpsBrutImport = '' ;
			public $Decorators = array() ;
			public $CorpsBrutDecorator = '' ;
			public $ClassePrinc ;
			public $Classes = array() ;
			public $CorpsBrutSuppl = '' ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ClassePrinc = new PvClasseCleTsIonic() ;
			}
			public function & InsereClasse($nom)
			{
				$classe = new PvClasseCleTsIonic() ;
				$classe->NomClasse = $nom ;
				$this->Classes[$nom] = & $classe ;
				return $classe ;
			}
			public function & InsereDecorator($nom)
			{
				$this->Decorators[$nom] = new PvDecoratorCleTsIonic() ;
				$this->Decorators[$nom]->NomDecorator = $nom ;
				return $this->Decorators[$nom] ;
			}
			public function & InsereComponentDecorator()
			{
				return $this->InsereDecorator("component") ;
			}
			protected function & CreeImport($classes, $cheminRelatif="")
			{
				$import = new PvImportCleTsIonic() ;
				$import->Classes = $classes ;
				$import->CheminRelatif = $cheminRelatif ;
				return $import ;
			}
			public function & InsereImportDirect($cheminRelatif="")
			{
				return $this->InsereImportGlobal(array(), $cheminRelatif) ;
			}
			public function & InsereImportGlobal($classes, $cheminRelatif="")
			{
				if(isset($this->ImportsGlobaux[$cheminRelatif]))
				{
					$import = & $this->ImportsGlobaux[$cheminRelatif] ;
					$import->InsereClasses($classes) ;
				}
				else
				{
					$import = $this->CreeImport($classes, $cheminRelatif) ;
				}
				$this->ImportsGlobaux[$cheminRelatif] = & $import ;
				return $import ;
			}
			public function & InsereImportLocal($classes, $cheminRelatif="")
			{
				if(isset($this->ImportsLocaux[$cheminRelatif]))
				{
					$import = & $this->ImportsLocaux[$cheminRelatif] ;
					$import->InsereClasses($classes) ;
				}
				else
				{
					$import = $this->CreeImport($classes, $cheminRelatif) ;
				}
				$this->ImportsLocaux[$cheminRelatif] = & $import ;
				return $import ;
			}
			public function & InsereImportPageSrc(& $pageSrc)
			{
				return $this->InsereImportLocal(array($pageSrc->NomClasse()), "../".$pageSrc->CheminRelatif()) ;
			}
			public function & InsereImportPageSrcService(& $pageSrc)
			{
				// print "Classe : ".get_class($pageSrc)."\n" ;
				return $this->InsereImportLocal(array($pageSrc->NomClasse()), "../../".$pageSrc->CheminRelatif()) ;
			}
			public function & InsereImportServiceSrc(& $serviceSrc, $nomsClasse=array())
			{
				if(count($nomsClasse) == 0)
				{
					$nomsClasse = array($serviceSrc->NomClasse()) ;
				}
				return $this->InsereImportLocal($nomsClasse, "../".$serviceSrc->CheminRelatif()) ;
			}
			public function & InsereImportServiceSrcPage(& $serviceSrc, $nomsClasse=array())
			{
				if(count($nomsClasse) == 0)
				{
					$nomsClasse = array($serviceSrc->NomClasse()) ;
				}
				return $this->InsereImportLocal($nomsClasse, "../../".$serviceSrc->CheminRelatif()) ;
			}
			protected function EcritFluxFichier(& $fh)
			{
				foreach($this->ImportsGlobaux as $i => $import)
				{
					fputs($fh, $import->CodeSource().PHP_EOL) ;
				}
				foreach($this->ImportsLocaux as $i => $import)
				{
					fputs($fh, $import->CodeSource().PHP_EOL) ;
				}
				if($this->CorpsBrutImport != "")
				{
					fputs($fh, $this->CorpsBrutImport. PHP_EOL) ;
				}
				foreach($this->Decorators as $nom => $decorator)
				{
					fputs($fh, $decorator->CodeSource(). PHP_EOL) ;
				}
				if($this->CorpsBrutDecorator != "")
				{
					fputs($fh, $this->CorpsBrutDecorator. PHP_EOL) ;
				}
				foreach($this->Classes as $i => $classe)
				{
					fputs($fh, $classe->CodeSource().PHP_EOL) ;
				}
				if($this->CorpsBrutSuppl != "")
				{
					fputs($fh, $this->CorpsBrutSuppl. PHP_EOL) ;
				}
			}
		}
		class PvFichSrcJsIonic extends PvFichSrcBaseIonic
		{
		}
		class PvFichSrcCssIonic extends PvFichSrcBaseIonic
		{
		}
		class PvFichSrcScssIonic extends PvFichSrcBaseIonic
		{
		}
		
	}
	
?>