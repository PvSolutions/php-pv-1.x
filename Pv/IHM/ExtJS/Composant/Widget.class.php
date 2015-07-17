<?php
	
	if(! defined('PV_COMPOSANT_WIDGET_EXT_JS'))
	{
		if(! defined('PV_COMPOSANT_NOYAU_EXT_JS'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_COMPOSANT_WIDGET_EXT_JS', 1) ;
		
		class PvConfigWindowExtJS extends PvConfigWidgetExtJS
		{
			public $buttons = array() ;
		}
		class PvConfigInstWindowExtJS extends PvConfigInstItemExtJS
		{
			public $autoShow = true ;
			public $modal = true ;
			public $height = null ;
			public $title = '' ;
			public $width = '100%' ;
			public $layout = 'fit' ;
		}
		class PvConfigButtonExtJS extends PvConfigWidgetExtJS
		{
			public $text = "" ;
		}
		
		class PvPanelExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.panel.Panel" ;
		}		
		class PvToolbarExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.toolbar.Toolbar" ;
		}
		class PvWindowExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.window.Window" ;
			public $BoutonsExtJS = array() ;
			public $InscrireBoutonFermer = 0 ;
			public $BoutonFermer = null ;
			public $LibelleBoutonFermer = "Fermer" ;
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeBoutonFermer() ;
			}
			protected function ChargeBoutonFermer()
			{
				if(! $this->InscrireBoutonFermer)
				{
					return  ;
				}
				$this->BoutonFermer = $this->InsereBoutonExtJS(new PvButtonExtJS()) ;
				$this->BoutonFermer->CfgCreaExtJS->text = $this->LibelleBoutonFermer ;
				$this->BoutonFermer->InsereListenerClick('widget.up("window").close() ;') ;
			}
			public function & InsereBoutonExtJS($btn)
			{
				return $this->InscritBoutonExtJS($btn) ;
			}
			public function & InscritBoutonExtJS(& $btn)
			{
				$this->BoutonsExtJS[] = & $btn ;
				$btn->AdopteComposantParentExtJS('button_'.count($this->BoutonsExtJS), $this) ;
				return $btn ;
			}
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigWindowExtJS() ;
			}
			protected function CreeCfgCreaExtJS()
			{
				return new PvConfigInstWindowExtJS() ;
			}
			public function PrepareRenduJS()
			{
				parent::PrepareRenduJS() ;
				$this->CfgDefExtJS->buttons = array() ;
				foreach($this->BoutonsExtJS as $nom => & $elem)
				{
					$this->CfgDefExtJS->buttons[] = & $elem->CfgCreaExtJS ;
				}
			}
			public function RenduElementsExtJS()
			{
				$ctn = parent::RenduElementsExtJS() ;
				foreach($this->BoutonsExtJS as $i => & $elem)
				{
					$ctn .= $elem->RenduComposantExtJS() ;
				}
				return $ctn ;
			}
		}
		class PvButtonExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.button.Button" ;
			protected function CreeCfgCreaExtJS()
			{
				return new PvConfigButtonExtJS() ;
			}
		}
		
		class PvTextFieldExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.form.field.Field" ;
		}
	}
	
?>