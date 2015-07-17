<?php
	
	if(! defined('PV_COMPOSANT_DONNEES_EXT_JS'))
	{
		if(! defined('PV_COMPOSANT_NOYAU_EXT_JS'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_COMPOSANT_DONNEES_EXT_JS', 1) ;
		
		class PvConfigGridPanelExtJS extends PvConfigWidgetExtJS
		{
			public $store ;
			public $columns = array() ;
		}
		class PvConfigFormExtJS extends PvConfigWidgetExtJS
		{
		}
		class PvGridPanelExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.grid.Panel" ;
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigGridPanelExtJS() ;
			}
		}
		class PvGridExtJS extends PvGridPanelExtJS
		{
		}
		
		class PvFormPanelExtJS extends PvWidgetExtJS
		{
			public $NomClasseExtendExtJS = "Ext.form.Panel" ;
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigFormExtJS() ;
			}
		}
		class PvFormExtJS extends PvFormPanelExtJS
		{
		}
		
		class PvFiltreDonneesExtJS extends PvFiltreDonneesBase
		{
			public $NomClasseComposant = "PvTextFieldExtJS" ;
		}
		
		class PvAdaptTableauDonneesExtJS extends PvTableauDonneesHtml
		{
			public function ProduitElementExtJS($nom, & $widget)
			{
				$panel = $widget->InsereElementExtJS($nom, new PvPanelTableauDonneesExtJS()) ;
				$panel->FormFiltres = $panel->InsereElementExtJS('formFiltres', new PvFormExtJS()) ;
				foreach($this->FiltresSelection as $i => & $filtre)
				{
					$compFlt = $filtre->CreeComposant() ;
					$panel->FormFiltres->InsereElementExtJS('filtre_'.$i, $filtre) ;
				}
				$panel->GridPanelDonnees = $panel->InsereElementExtJS('gridPanelDonnees', new PvGridPanelExtJS()) ;
				return $panel ;
			}
		}
		class PvPanelTableauDonneesExtJS extends PvPanetExtJS
		{
			public $FormFiltres ;
			public $GridPanelDonnees ;
			public $Store ;
			public $Model ;
			public $Navigateur ;
		}
		
		class PvConfigStoreExtJS extends PvConfigElemExtJS
		{
			public $extend ;
			public $model ;
			public $autoload = true ;
			public $proxy ;
			public function __construct()
			{
				$this->proxy = new PvConfigProxyStoreExtJS() ;
			}
		}
		class PvConfigModelExtJS extends PvConfigElemExtJS
		{
			public $extend ;
			public $requires = array() ;
			public $fields = array() ;
		}
		
		class PvConfigProxyStoreExtJS
		{
			public $type = 'ajax' ;
			public $api ;
			public $reader ;
			public function __construct()
			{
				$this->api = new PvConfigApiProxyStoreExtJS() ;
				$this->reader = new PvConfigReaderProxyStoreExtJS() ;
			}
		}
		class PvConfigApiProxyStoreExtJS
		{
			public $read ;
			public $create ;
			public $update ;
			public $destroy ;
		}
		class PvConfigReaderProxyStoreExtJS
		{
			public $type = 'json' ;
			public $root ;
			public $successProperty ;
		}
		
		class PvStoreExtJS extends PvElemComposantExtJS
		{
			public $EspaceNommageExtJS = "store" ;
			public $NomClasseExtendExtJS = "Ext.data.Store" ;
		}
		class PvModelExtJS extends PvElemComposantExtJS
		{
			public $EspaceNommageExtJS = "model" ;
			public $NomClasseExtendExtJS = "Ext.data.Model" ;
		}
	}
	
?>