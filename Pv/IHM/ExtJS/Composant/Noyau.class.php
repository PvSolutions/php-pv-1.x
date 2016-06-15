<?php
	
	if(! defined('PV_COMPOSANT_NOYAU_EXT_JS'))
	{
		define('PV_COMPOSANT_NOYAU_EXT_JS', 1) ;
		
		class PvFoncListenerBaseExtJS
		{
			protected $NomEvt = "base" ;
			protected $ArgsEvt = "" ;
			public $Ctn = "" ;
			public function ObtientNomEvt()
			{
				return $this->NomEvt ;
			}
			public function ObtientArgs()
			{
				return $this->ArgsEvt ;
			}
			public function ObtientDef()
			{
				$ctn = 'function ('.$this->ArgsEvt.') {
'.$this->Ctn.'
}' ;
				return $ctn ;
			}
		}
		class PvFoncListenerClickExtJS extends PvFoncListenerBaseExtJS
		{
			protected $NomEvt = "click" ;
			protected $ArgsEvt = "widget, item" ;
		}
		class PvFoncListenerItemclickExtJS extends PvFoncListenerBaseExtJS
		{
			protected $NomEvt = "itemdblclick" ;
			protected $ArgsEvt = "widget, item" ;
		}
		class PvFoncListenerItemdblclickExtJS extends PvFoncListenerBaseExtJS
		{
			protected $NomEvt = "itemdblclick" ;
			protected $ArgsEvt = "widget, item" ;
		}
		
		class PvConfigInstItemExtJS
		{
			public $xtype ;
			public $hidden = false ;
			public $disabled = false ;
		}
		
		class PvConfigElemExtJS
		{
		}
		class PvConfigApplicationExtJS extends PvConfigElemExtJS
		{
			public $autoCreateViewport = true ;
			public $name = "" ;
			public $controllers = array() ;
		}
		class PvConfigClasseExtJS extends PvConfigElemExtJS
		{
			public $extend = '' ;
		}
		class PvConfigComposantBaseExtJS extends PvConfigClasseExtJS
		{
			public $alias = '' ;
			public $layout = 'fit' ;
		}
		class PvConfigControllerExtJS extends PvConfigElemExtJS
		{
			public $extend = '' ;
			public $alias = '' ;
			public $stores = array() ;
			public $models = array() ;
		}
		class PvConfigViewportExtJS extends PvConfigComposantBaseExtJS
		{
			public $items = array() ;
		}
		class PvConfigWidgetExtJS extends PvConfigComposantBaseExtJS
		{
			public $id = '' ;
			public $iconCls = '' ;
			public $items = array() ;
			public $dockedItems = array() ;
			public $requires = array() ;
		}
		
		class PvConfigInstWidgetExtJS extends PvConfigInstItemExtJS
		{
			public $height = null ;
			public $html = '' ;
			public $title = '' ;
			public $width = '100%' ;
			public $flex = '' ;
			public $region = '' ;
			public $layout = 'auto' ;
			public $margin = 0 ;
			public $columnWidth = 0 ;
		}
		
		class PvComposantBaseExtJS extends PvComposantIUBase
		{
			public $CfgDefExtJS ;
			public $CfgCreaExtJS ;
			public $EspaceNommageExtJS = "component" ;
			public $NomClasseExtJS = "" ;
			public $ElementsExtJS = array() ;
			public $CreaAuto = 1 ;
			public $DefAuto = 1 ;
			protected $InclutXTypeCrea = 1 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CfgDefExtJS = $this->CreeCfgDefExtJS() ;
				$this->CfgCreaExtJS = $this->CreeCfgCreaExtJS() ;
				if($this->InclutXTypeCrea)
					$this->CfgCreaExtJS->xtype = $this->IDInstanceCalc ;
			}
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigComposantBaseExtJS() ;
			}
			protected function CreeCfgCreaExtJS()
			{
				return new PvConfigComposantBaseExtJS() ;
			}
			protected function ObtientNomAppExtJS()
			{
				return $this->ZoneParent->NomAppExtJS ;
			}
			public function InsereElementExtJS($nom, $elem)
			{
				return $this->InscritElementExtJS($nom, $elem) ;
			}
			public function InscritElementExtJS($nom, & $elem)
			{
				$this->ElementsExtJS[$nom] = & $elem ;
				$elem->AdopteComposantParentExtJS($nom, $this) ;
				return $elem ;
			}
			public function ObtientAliasClasseExtJS()
			{
				return (($this->NomClasseExtJS != '') ? $this->NomClasseExtJS : $this->IDInstanceCalc) ;
			}
			public function ObtientNomClasseExtJS()
			{
				return $this->ObtientNomAppExtJS().'.'.$this->EspaceNommageExtJS.'.'.$this->ObtientAliasClasseExtJS() ;
			}
			public function ObtientLienClasseExtJS()
			{
				return $this->ObtientAliasClasseExtJS()."@".$this->ObtientNomAppExtJS().'.'.$this->EspaceNommageExtJS ;
			}
			public function RenduComposantExtJS()
			{
				$ctn = '' ;
				$this->PrepareRenduJS() ;
				$ctn .= $this->RenduElementsExtJS() ;
				$ctnDef = $this->CtnJSDefinition() ;
				if($ctnDef != '')
				{
					$ctn .= $ctnDef.PHP_EOL ;
				}
				if($this->CreaAuto)
				{
					$ctnCrea = $this->CtnJSCreation() ;
					if($ctnCrea != '')
					{
						$ctn .= $ctnCrea.PHP_EOL ;
					}
				}
				return $ctn ;
			}
			public function PrepareRenduJS()
			{
				if(! $this->DefAuto)
				{
					$this->CfgCreaExtJS->xtype = $this->NomClasseExtendExtJS ;
				}
			}
			public function CtnJSCreation()
			{
				return '' ;
			}
			public function CtnJSDefinition()
			{
				return '' ;
			}
			public function CtnJSInitComposant()
			{
				return '' ;
			}
			public function RenduElementsExtJS()
			{
				$ctn = '' ;
				foreach($this->ElementsExtJS as $nom => & $elem)
				{
					$ctn .= $elem->RenduComposantExtJS() ;
				}
				return $ctn ;
			}
		}
		class PvElemComposantExtJS extends PvComposantBaseExtJS
		{
			public $ComposantParentExtJS ;
			public $NomElementComposantExtJS ;
			public $NomClasseExtendExtJS ;
			public $CtnFoncInitExtJS = '' ;
			public $CtnFoncInitComponentExtJS = '' ;
			public $ListenersExtJS = array() ;
			protected $DefItemsDansInitComponent = 0 ;
			protected $AutoFixerAlias = 1 ;
			public function & InsereListenerExtJS($listener)
			{
				return $this->InscritListenerExtJS($listener) ;
			}
			public function & InscritListenerExtJS(& $listener)
			{
				$this->ListenersExtJS[$listener->ObtientNomEvt()] = & $listener ;
				return $listener ;
			}
			public function & InsereListenerClick($ctn='')
			{
				$listener = $this->InsereListenerExtJS(new PvFoncListenerClickExtJS()) ;
				$listener->Ctn = $ctn ;
				return $listener ;
			}
			public function & InsereListenerItemclick($ctn='')
			{
				$listener = $this->InsereListenerExtJS(new PvFoncListenerItemclickExtJS()) ;
				$listener->Ctn = $ctn ;
				return $listener ;
			}
			public function & InsereListenerItemdblclick($ctn='')
			{
				$listener = $this->InsereListenerExtJS(new PvFoncListenerItemdblclickExtJS()) ;
				$listener->Ctn = $ctn ;
				return $listener ;
			}
			public function DevientElementExtJS($nom, & $composantParent)
			{
				$composantParent->InscritElementExtJS($nom, $this) ;
			}
			public function AdopteComposantParentExtJS($nom, & $composantParent)
			{
				$this->ComposantParentExtJS = & $composantParent ;
				$this->NomElementComposantExtJS = $nom ;
				$this->AdopteZone($composantParent->IDInstanceCalc.'_'.$nom, $composantParent->ZoneParent) ;
			}
			public function ObtientCfgDefItems()
			{
				$items = array() ;
				foreach($this->ElementsExtJS as $nom => & $elem)
				{
					$items[] = & $elem->CfgCreaExtJS ;
				}
				return $items ;
			}
			public function PrepareRenduJS()
			{
				parent::PrepareRenduJS() ;
				if($this->AutoFixerAlias)
				{
					$this->CfgDefExtJS->alias = 'widget.'.$this->IDInstanceCalc ;
				}
				if($this->NomClasseExtendExtJS != '')
				{
					$this->CfgDefExtJS->extend = $this->NomClasseExtendExtJS ;
				}
			}
			public function CtnJSDefinition()
			{
				$ctn = '' ;
				if(! $this->DefAuto)
				{
					return $ctn ;
				}
				$ctn .= 'StructDef_'.$this->IDInstanceCalc.' = '.svc_json_encode($this->CfgDefExtJS).' ;'.PHP_EOL ;
				if($this->CtnFoncInitExtJS != '')
				{
					$ctn .= 'StructDef_'.$this->IDInstanceCalc.'.init = function() {
'.$this->CtnFoncInitExtJS.'
}'.PHP_EOL ;
				}
				$items = $this->ObtientCfgDefItems() ;
				if($this->CtnFoncInitComponentExtJS != '' || ($this->DefItemsDansInitComponent && count($items) > 0))
				{
					$ctnInit = $this->CtnFoncInitComponentExtJS ;
					if($this->DefItemsDansInitComponent)
					{
						if($ctnInit != '')
						{
							$ctnInit .= PHP_EOL ;
						}
						$ctnInit .= 'this.items = '.svc_json_encode($items).' ;'.PHP_EOL ;
						$ctnInit .= 'this.callParent(arguments) ;' ;
					}
					$ctn .= 'StructDef_'.$this->IDInstanceCalc.'.initComponent = function() {
'.$ctnInit.'
}'.PHP_EOL ;
				}
				if(count($this->ListenersExtJS))
				{
					$ctn .= 'StructDef_'.$this->IDInstanceCalc.'.listeners = StructDef_'.$this->IDInstanceCalc.'.listeners || [] ;'.PHP_EOL ;
					foreach($this->ListenersExtJS as $i => $listener)
					{
						$ctn .= 'StructDef_'.$this->IDInstanceCalc.'.listeners.'.$listener->ObtientNomEvt().' = '.$listener->ObtientDef().' ;'.PHP_EOL ;
					}
				}
				$ctn .= 'Ext.define('.svc_json_encode($this->ObtientNomClasseExtJS()).', StructDef_'.$this->IDInstanceCalc.') ;' ;
				return $ctn ;
			}
			protected function InstrJSSupplInitComponents()
			{
				return "" ;
			}
			public function ObtientNomClasseExtJS()
			{
				if(! $this->DefAuto)
				{
					return $this->NomClasseExtendExtJS ;
				}
				return parent::ObtientNomClasseExtJS() ;
			}
			public function InstrJSCrea()
			{
				return 'Ext.create('.svc_json_encode($this->ObtientNomClasseExtJS()).', '.svc_json_encode($this->CfgCreaExtJS).')' ;
			}
		}
		
		class PvApplicationExtJS extends PvComposantBaseExtJS
		{
			public $EspaceNommageExtJS = "application" ;
			public $NomClasseExtJS = "" ;
			public $Controllers = array() ;
			public $Stores = array() ;
			public $Models = array() ;
			public $ControllerParDefaut ;
			public $Viewport ;
			public function InsereController($nom, $controller)
			{
				return $this->InscritController($nom, $controller) ;
			}
			public function InscritController($nom, & $controller)
			{
				$this->Controllers[$nom] = & $controller ;
				$this->InscritElementExtJS($nom, $controller) ;
				return $controller ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeViewport() ;
				$this->ChargeControllerParDefaut() ;
			}
			protected function ChargeViewport()
			{
				$this->Viewport = new PvViewportExtJS() ;
				$this->InscritElementExtJS('viewport', $this->Viewport) ;
				$this->Viewport->ChargeConfig() ;
			}
			protected function ChargeControllerParDefaut()
			{
				$this->ControllerParDefaut = new PvControllerExtJS() ;
				$this->InscritController('parDefaut', $this->ControllerParDefaut) ;
				$this->ControllerParDefaut->ChargeConfig() ;
			}
			protected function CreeCfgCreaExtJS()
			{
				return new PvConfigApplicationExtJS() ;
			}
			public function CtnJSCreation()
			{
				$ctn = '' ;
				$ctn .= 'StructInst_'.$this->IDInstanceCalc.' = '.svc_json_encode($this->CfgCreaExtJS).' ;'.PHP_EOL ;
				$ctn .= 'Ext.application(StructInst_'.$this->IDInstanceCalc.') ;' ;
				return $ctn ;
			}
			public function PrepareRenduJS()
			{
				$this->CfgCreaExtJS->name = $this->ObtientNomAppExtJS() ;
				$this->CfgCreaExtJS->controllers = array() ;
				foreach($this->Controllers as $nom => & $controller)
				{
					$this->CfgCreaExtJS->controllers[] = $controller->IDInstanceCalc ;
				}
			}
		}
		
		class PvViewportExtJS extends PvElemComposantExtJS
		{
			public $EspaceNommageExtJS = "view" ;
			public $NomClasseExtJS = "Viewport" ;
			public $NomClasseExtendExtJS = 'Ext.container.Viewport' ;
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigViewportExtJS() ;
			}
			public function PrepareRenduJS()
			{
				parent::PrepareRenduJS() ;
				$this->CfgDefExtJS->layout = "border" ;
				$this->CfgDefExtJS->items = array() ;
				if(! $this->DefItemsDansInitComponent)
				{
					$this->CfgDefExtJS->items = $this->ObtientCfgDefItems() ;
				}
			}
		}
		
		class PvControllerExtJS extends PvElemComposantExtJS
		{
			public $EspaceNommageExtJS = "controller" ;
			public $NomClasseExtendExtJS = "Ext.app.Controller" ;
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigControllerExtJS() ;
			}
		}
		
		class PvFiltreDonneesExtJS extends PvFiltreDonneesHttpGet
		{
			public $TypeLiaisonParametre = "extjs" ;
			public $NomClasseComposant = "PvTextFieldExtJS" ;
		}
		
		class PvConfigStoreExtJS
		{
			public $extend ;
			public $model ;
			public $autoLoad = true ;
			public $proxy ;
			public function __construct()
			{
				$this->proxy = new PvConfigProxyStoreExtJS() ;
			}
		}
		class PvConfigModelExtJS
		{
			public $extend ;
			public $requires = array("Ext.data.reader.Json") ;
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
			public $successProperty = "succes" ;
		}
		
		class PvStoreExtJS extends PvElemComposantExtJS
		{
			public $EspaceNommageExtJS = "store" ;
			public $NomClasseExtendExtJS = "Ext.data.Store" ;
			public $ModelExtJS ;
			public $ProxyExtJS ;
			protected $AutoFixerAlias = 0 ;
			protected $InclutXTypeCrea = 0 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->ModelExtJS = new PvModelExtJS() ;
				$this->ProxyExtJS = new PvProxyStoreExtJS() ;
			}
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigStoreExtJS() ;
			}
			public function PrepareRenduJS()
			{
				parent::PrepareRenduJS() ;
				$this->CfgDefExtJS->proxy = & $this->ProxyExtJS->CfgCreaExtJS ;
				$this->CfgDefExtJS->model = $this->ModelExtJS->ObtientNomClasseExtJS() ;
			}
			public function AdopteComposantParentExtJS($nom, & $compParent)
			{
				parent::AdopteComposantParentExtJS($nom, $compParent) ;
				$this->ModelExtJS->AdopteComposantParentExtJS($nom."_model", $this) ;
				$this->ModelExtJS->ChargeConfig() ;
				$this->ProxyExtJS->AdopteComposantParentExtJS($nom."_proxy", $this) ;
				$this->ProxyExtJS->ChargeConfig() ;
				$this->ZoneParent->ApplicationExtJS->ControllerParDefaut->CfgDefExtJS->stores[] = $this->ObtientLienClasseExtJS() ;
			}
			public function RenduComposantExtJS()
			{
				$ctn = '' ;
				$ctn .= $this->ModelExtJS->RenduComposantExtJS() ;
				$ctn .= parent::RenduComposantExtJS() ;
				return $ctn ;
			}
		}
		class PvModelExtJS extends PvElemComposantExtJS
		{
			protected $AutoFixerAlias = 0 ;
			public $EspaceNommageExtJS = "model" ;
			public $NomClasseExtendExtJS = "Ext.data.Model" ;
			public function AdopteComposantParentExtJS($nom, & $compParent)
			{
				parent::AdopteComposantParentExtJS($nom, $compParent) ;
				$this->ZoneParent->ApplicationExtJS->ControllerParDefaut->CfgDefExtJS->models[] = $this->ObtientLienClasseExtJS() ;
			}
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigModelExtJS() ;
			}
		}
		class PvProxyStoreExtJS extends PvElemComposantExtJS
		{
			public $EspaceNommageExtJS = "proxy" ;
			public $NomClasseExtendExtJS = "Ext.data.proxy" ;
			public $CreaAuto = 0 ;
			protected $InclutXTypeCrea = 0 ;
			protected function CreeCfgCreaExtJS()
			{
				return new PvConfigProxyStoreExtJS() ;
			}
		}
		
		class PvWidgetExtJS extends PvElemComposantExtJS
		{
			public $EspaceNommageExtJS = "component" ;
			public $ElemsDockExtJS = array() ;
			public function & InsereElemDockExtJS($elem)
			{
				return $this->InscritElemDockExtJS($elem) ;
			}
			public function & InscritElemDockExtJS(& $elem)
			{
				$this->ElemsDockExtJS[] = & $elem ;
				$elem->AdopteComposantParentExtJS('elem_dock_'.count($this->ElemsDockExtJS), $this) ;
				return $elem ;
			}
			protected function CreeCfgDefExtJS()
			{
				return new PvConfigWidgetExtJS() ;
			}
			protected function CreeCfgCreaExtJS()
			{
				return new PvConfigInstWidgetExtJS() ;
			}
			public function ObtientCfgDefDockedItems()
			{
				$items = array() ;
				foreach($this->ElemsDockExtJS as $nom => & $elem)
				{
					$items[] = & $elem->CfgCreaExtJS ;
				}
				return $items ;
			}
			public function PrepareRenduJS()
			{
				parent::PrepareRenduJS() ;
				$this->CfgCreaExtJS->id = $this->IDInstanceCalc ;
				$this->CfgDefExtJS->items = array() ;
				$this->CfgDefExtJS->dockedItems = array() ;
				if(! $this->DefItemsDansInitComponent)
				{
					$this->CfgDefExtJS->items = $this->ObtientCfgDefItems() ;
					$this->CfgDefExtJS->dockedItems = $this->ObtientCfgDefDockedItems() ;
				}
			}
		}
	}
	
?>