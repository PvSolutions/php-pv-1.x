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
		
		class PvCfgBaseJQueryUi
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
		
		class PvContenuWidgetJQueryUi extends PvComposantIUBase
		{
			public $ComposantsIUFils = array() ;
			public $Visible = 1 ;
			public function RenduPossible()
			{
				return ($this->Visible == 1) ? 1 : 0 ;
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
		class PvWidgetBaseJQueryUi extends PvComposantIUBase
		{
			public $Draggable = 0 ;
			public $AttrsDraggable = array() ;
			public $Droppable = 0 ;
			public $Resizable = 0 ;
			public $Selectable = 0 ;
			public $Hide = 0 ;
			public $DefinitionsJs = "" ;
			public $ContenuHtml = "" ;
			public function ObtientNomVarJs()
			{
				return 'obj'.$this->IDInstanceCalc.'' ;
			}
			protected function RenduDefinitionsJs()
			{
				$ctn = '' ;
				$ctn .= '<script language="javascript">
var obj'.$this->IDInstanceCalc.' = null ;
jQuery(function() {
	obj'.$this->IDInstanceCalc.' = jQuery("'.$this->IDInstanceCalc.'") ;
'.$this->DetermineDefinitionsJs().'
}) ;
</script>' ;
				return $ctn ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '<div' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'">' ;
				$ctn .= $this->RenduContenuHtml() ;
				$ctn .= '</div>' ;
				$ctn .= '<script type="text/javascript">'.PHP_EOL ;
				$ctn .= $this->RenduDefinitionsJs() ;
				$ctn .= '</script>' ;
				return $ctn ;
			}
			protected function DetermineDefinitionsJs()
			{
				$ctn = '' ;
				$ctn .= $this->DefinitionsJsNouvInst() ;
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
		
		class PvOngletJQueryUi extends PvContenuWidgetJQueryUi
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
				return '<li href="#'.$this->IDInstanceCalc.'">'.htmlentities($this->Titre).'</li>' ;
			}
		}
		class PvTabsJQueryUi extends PvWidgetBaseJQueryUi
		{
			public $Onglets = array() ;
			public $Collapsible = 1 ;
			public $HeightStyle = 1 ;
			public $Active = 1 ;
			public function CreeOnglet()
			{
				return new PvOngletJQueryUi() ;
			}
			public function & InsereOngletComp($titre, & $comp)
			{
				$nouvOnglet = $this->CreeOnglet() ;
				$nouvOnglet->Titre = $titre ;
				$this->InscritOnglet($nouvOnglet) ;
				$nouvOnglet->DeclareCompSupport($comp) ;
				return $nouvOnglet ;
			}
			public function & InscritOnglet(& $onglet)
			{
				$index = count($this->Onglets) ;
				$this->Onglets[$index] = & $onglet ;
				$onglet->AdopteTabs($index, $this) ;
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
					$ctnContenus .= $onglet->ObtientInterieurHtml() ;
				}
			}
		}
		
		class PvCfgDialogJQueryUi extends PvCfgBaseJQueryUi
		{
			public $title ;
			public $collapsible ;
		}
		
		class PvDialogJQueryUi extends PvContenuWidgetJQueryUi
		{
			public $Ouverture ;
			public $CheminIcone ;
			public $Titre ;
			public $Boutons = array() ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->Ouverture = new PvOuvrBaseDialogJQueryUi() ;
			}
			protected function RenduFenetre()
			{
				$ctn = '' ;
				$ctn .= '<div id="Fenetre'.$this->IDInstanceCalc.'" class="ui-dialog">'.PHP_EOL ;
				$ctn .= $this->ObtientInterieurHtml().PHP_EOL ;
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function RenduBoutons()
			{
				$ctn = '' ;
				foreach($this->Boutons as $i => $bouton)
				{
					$ctn .= $bouton->RenduDefinition($this) ;
				}
				return $ctn ;
			}
			public function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->Ouverture->Rendu($this) ;
				return $ctn ;
			}
		}
		
		class PvOuvrBaseDialogJQueryUi
		{
			public $Indefini = 0 ;
			public function Rendu(& $dialog)
			{
				$ctn = $this->RenduBrut($dialog) ;
				return $ctn ;
			}
			protected function RenduBrut(& $dialog)
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
		class PvBtnOuvrDialogJQueryUi extends PvOuvrBaseDialogJQueryUi
		{
			protected function RenduBrut(& $dialog)
			{
				$ctn = '' ;
				return $ctn ;
			}
		}
		class PvAutoOuvrDialogJQueryUi extends PvOuvrBaseDialogJQueryUi
		{
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