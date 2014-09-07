<?php
	
	if(! defined('PV_COMPOSANT_SIMPLE_IU_JQUERY'))
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
		define('PV_COMPOSANT_SIMPLE_IU_JQUERY', 1) ;
		
		class PvOptBaseJQueryUi
		{
			public function CommeJSON()
			{
				return svc_json_encode($this) ;
			}
		}
		
		class PvMenubarJQueryUi extends PvBarreMenuWebBase
		{
			protected static $SourceIncluse = 0 ;
			public $CheminCSS = "css/jquery.ui.menubar.css" ;
			public $CheminJs = "js/jquery-menubar.js" ;
			protected function RenduSourceIncluse()
			{
				$sourceInc = $this->ObtientValeurStatique("SourceIncluse") ;
				if($sourceInc)
				{
					return "" ;
				}
				$ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->CheminJs) ;
				$ctn .= $this->ZoneParent->RenduLienCSS($this->CheminCSS) ;
				$this->AffecteValeurStatique("SourceIncluse", 1) ;
				return $ctn ;
			}
			protected function RenduDefinitionJs()
			{
				$ctn = '' ;
				$ctn .= 'jQuery(function() {
	jQuery("#'.$this->IDInstanceCalc.'").menubar({}) ;
}) ;' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->RenduSourceIncluse() ;
				$ctn .= parent::RenduDispositifBrut() ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus($this->RenduDefinitionJs()) ;
				return $ctn ;
			}
		}
		class PvButtonBarJQueryUi extends PvBarreMenuWebBase
		{
			protected function RenduDefinitionJs()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduMenuRacine(& $menu)
			{
				return $this->RenduMenuNv1($menu) ;
			}
			protected function RenduMenuNv1($menu)
			{
			}
		}
		
		class PvWidgetBaseJQueryUi extends PvComposantIUBase
		{
			public $Opt ;
			public $Draggable = 0 ;
			public $AttrsDraggable = array() ;
			public $Droppable = 0 ;
			public $Resizable = 0 ;
			public $Selectable = 0 ;
			public $Hide = 0 ;
			public $DefinitionsJs = "" ;
			public $ContenuHtml = "" ;
			protected function CreeOpt()
			{
				return new PvOptBaseJQueryUi() ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Opt = $this->CreeOpt() ;
			}
			protected function DefJsOpt()
			{
				return svc_json_encode($this->Opt) ;
			}
			public function ObtientNomVarJs()
			{
				return 'obj'.$this->IDInstanceCalc.'' ;
			}
			protected function RenduDefinitionsJs()
			{
				$ctn = '' ;
				$ctn .= 'var obj'.$this->IDInstanceCalc.' = null ;
jQuery(function() {
	obj'.$this->IDInstanceCalc.' = jQuery("#'.$this->IDInstanceCalc.'") ;
'.$this->DetermineDefinitionsJs().'}) ;' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '<div id="'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				$ctn .= $this->RenduContenuHtml().PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus($this->RenduDefinitionsJs());
				return $ctn ;
			}
			protected function DetermineDefinitionsJs()
			{
				$ctn = '' ;
				if($this->Draggable)
					$ctn .= 'obj'.$this->IDInstanceCalc.'.draggable() ;'.PHP_EOL ;
				if($this->Droppable)
					$ctn .= 'obj'.$this->IDInstanceCalc.'.droppable() ;'.PHP_EOL ;
				if($this->Selectable)
					$ctn .= 'obj'.$this->IDInstanceCalc.'.selectable() ;'.PHP_EOL ;
				if($this->Resizable)
					$ctn .= 'obj'.$this->IDInstanceCalc.'.resizable() ;'.PHP_EOL ;
				return $ctn ;
			}
			protected function DefinitionsJsNouvInst()
			{
				$ctn = 'var obj'.$this->IDInstanceCalc.' ;'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduContenuHtml()
			{
				return $this->ContenuHtml ;
			}
		}
		class PvConteneurJQueryUi extends PvWidgetBaseJQueryUi
		{
			public $ComposantsIUFils = array() ;
			public $Visible = 1 ;
			public function RenduPossible()
			{
				return ($this->Visible == 1) ? 1 : 0 ;
			}
			public function & InsereNouvComp($comp)
			{
				$this->InscritComposantIUFils("", $comp) ;
				return $comp ;
			}
			public function & InsereComp(& $comp)
			{
				$this->InscritComposantIUFils("", $comp) ;
				return $comp ;
			}
			public function InscritComposantIUFils($nom, & $comp)
			{
				if(empty($nom))
				{
					$nom = "Composant_".count($this->ComposantsIUFils) ;
				}
				$this->ComposantsIUFils[$nom] = & $comp ;
				$comp->AdopteComposantIU($nom, $this) ;
			}
			public function InscritNouvComposantIUFils($comp)
			{
				$this->InscritComposantIUFils("", $comp) ;
			}
			public function ObtientInterieurHtml()
			{
				$ctn = '' ;
				foreach($this->ComposantsIUFils as $i => & $comp)
				{
					$ctn .= $comp->RenduDispositif() ;
				}
				return $ctn ;
			}
		}
		
		class PvOngletJQueryUi extends PvConteneurJQueryUi
		{
			public $Titre = "" ;
			public $CheminIcone = "" ;
			public $TabsParent = null ;
			public $IndexElementTabs = -1 ;
			public function AdopteTabs($index, & $tabs)
			{
				$this->IndexElementTabs = $index ;
				$this->TabsParent = & $tabs ;
				$this->AdopteScript($tabs->IDInstanceCalc.'_'.$index, $tabs->ScriptParent) ;
			}
			public function ObtientTitreHtml()
			{
				return '<li><a href="#'.$this->IDInstanceCalc.'">'.htmlentities($this->Titre).'</a></li>' ;
			}
		}
		class PvOptTabsJQueryUi
		{
			public $collapsible = false ;
			public $active = 0 ;
			public $disabled = array() ;
			public $event = "click" ;
			public $heightStyle = "auto" ;
		}
		class PvTabsJQueryUi extends PvWidgetBaseJQueryUi
		{
			public $Onglets = array() ;
			public $Active = 1 ;
			protected function CreeOpt()
			{
				return new PvOptTabsJQueryUi() ;
			}
			public function CreeOnglet()
			{
				return new PvOngletJQueryUi() ;
			}
			public function & InsereNouvOnglet($titre='')
			{
				$onglet = $this->CreeOnglet() ;
				$onglet->Titre = $titre ;
				$this->InscritOnglet($onglet) ;
				return $onglet ;
			}
			public function & InsereOngletComp($titre, & $comp)
			{
				$nouvOnglet = $this->CreeOnglet() ;
				$nouvOnglet->Titre = $titre ;
				$this->InscritOnglet($nouvOnglet) ;
				return $nouvOnglet ;
			}
			public function & InsereNouvOngletComp($titre, $comp)
			{
				return $this->InsereOngletComp($titre, $comp) ;
			}
			public function InscritOnglet(& $onglet)
			{
				$index = count($this->Onglets) ;
				$this->Onglets[$index] = & $onglet ;
				$onglet->AdopteTabs($index, $this) ;
			}
			protected function DetermineDefinitionsJs()
			{
				$ctn = parent::DetermineDefinitionsJs() ;
				$ctn .= 'obj'.$this->IDInstanceCalc.'.tabs('.$this->DefJsOpt().') ;'.PHP_EOL ;
				return $ctn ;
			}
			protected function RenduContenuHtml()
			{
				$ctn = '' ;
				$ctnEntetes = "" ;
				$ctnContenus = "" ;
				foreach($this->Onglets as $i => $onglet)
				{
					if(! $onglet->RenduPossible())
					{
						continue ;
					}
					$ctnEntetes .= $onglet->ObtientTitreHtml() ;
					$ctnContenus .= '<div id="'.$onglet->IDInstanceCalc.'">'.$onglet->ObtientInterieurHtml().'</div>' ;
				}
				if($ctnEntetes != '')
				{
					$ctn = '<ul>'.PHP_EOL.$ctnEntetes.'</ul>'.PHP_EOL.$ctnContenus ;
				}
				return $ctn ;
			}
		}
		
		class PvOptDialogJQueryUi
		{
			public $appendTo = "body" ;
			public $autoOpen = false ;
			public $closeOnEscape = true ;
			public $closeText = "close" ;
			public $dialogClass ;
			public $draggable = true ;
			public $height = "auto" ;
			public $maxHeight = false ;
			public $maxWidth = false ;
			public $minHeight = "150" ;
			public $minWidth = "150" ;
			public $modal = false ;
			public $resizable = true ;
			public $title ;
			public $width = 300 ;
		}
		
		class PvDialogJQueryUi extends PvConteneurJQueryUi
		{
			public $Declencheur ;
			public $CheminIcone ;
			public $Titre ;
			public $Boutons = array() ;
			public $ApparaitAuto = 0 ;
			protected function CreeOpt()
			{
				return new PvOptDialogJQueryUi() ;
			}
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Declencheur = new PvBtnDeclDialogJQueryUi() ;
			}
			protected function DetermineDefinitionsJs()
			{
				$ctn = parent::DetermineDefinitionsJs().PHP_EOL ;
				$ctn .= 'var opt = '.$this->DefJsOpt().' ;
opt.buttons = {} ;'.PHP_EOL ;
				foreach($this->Boutons as $nom => $bouton)
				{
					$ctn .= 'opt.'.$nom.' = '.$bouton->DefJsFonction($this).' ;'.PHP_EOL ;
				}
				$ctn .= 'obj'.$this->IDInstanceCalc.'.dialog(opt) ;' ;
				return $ctn ;
			}
			protected function RenduContenuHtml()
			{
				$ctn = '' ;
				$ctn .= $this->ObtientInterieurHtml().PHP_EOL ;
				return $ctn ;
			}
			protected function ObtientDeclencheur()
			{
				$declencheur = $this->Declencheur ;
				if($this->ApparaitAuto)
				{
					$declencheur = new PvAutoDeclDialogJQueryUi() ;
				}
				return $declencheur ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = parent::RenduDispositifBrut() ;
				$declencheur = $this->ObtientDeclencheur() ;
				$ctn .= $declencheur->RenduContenuHtml($this) ;
				return $ctn ;
			}
		}
		
		class PvCfgFormatColLienJQueryUi extends PvConfigFormatteurColonneLien
		{
			public $Opt ;
			protected function __construct()
			{
				$this->Opt = new PvOptDialogJQueryUi() ;
				// $this->FormatURL = $this->
			}
		}
		
		class PvDeclBaseDialogJQueryUi
		{
			public $Indefini = 0 ;
			public function RenduContenuHtml(& $dialog)
			{
				$ctn = $this->RenduContenuHtmlBrut($dialog) ;
				return $ctn ;
			}
			protected function RenduContenuHtmlBrut(& $dialog)
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
		class PvBtnDeclDialogJQueryUi extends PvDeclBaseDialogJQueryUi
		{
			public $Libelle = "Ouvrir" ;
			protected function RenduContenuHtmlBrut(& $dialog)
			{
				$ctn = '' ;
				$ctn .= '<a href="javascript:;" onclick="'.htmlentities('jQuery("#'.$dialog->IDInstanceCalc.'").dialog("open") ;').'">'.$this->Libelle.'</a>' ;
				return $ctn ;
			}
		}
		class PvAutoDeclDialogJQueryUi extends PvDeclBaseDialogJQueryUi
		{
			protected function RenduContenuHtmlBrut(& $dialog)
			{
				$ctn = '' ;
				$ctn .= $dialog->ZoneParent->RenduContenuJsInclus('jQuery(function() { jQuery("#'.$dialog->IDInstanceCalc.'").dialog("open") ; }) ;') ;
				return $ctn ;
			}
		}
		
		class PvBtnBaseDialogJQueryUi
		{
			public function RenduDefinition(& $dialog)
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
	}
	
?>