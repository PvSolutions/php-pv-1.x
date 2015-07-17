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
		class PvConfigComposantBaseExtJS extends PvConfigElemExtJS
		{
			public $extend = '' ;
			public $alias = '' ;
			public $layout = 'fit' ;
		}
		class PvConfigControllerExtJS extends PvConfigElemExtJS
		{
			public $extend = '' ;
			public $alias = '' ;
		}
		class PvConfigViewportExtJS extends PvConfigComposantBaseExtJS
		{
			public $items = array() ;
		}
		class PvConfigWidgetExtJS extends PvConfigComposantBaseExtJS
		{
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
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->CfgDefExtJS = $this->CreeCfgDefExtJS() ;
				$this->CfgCreaExtJS = $this->CreeCfgCreaExtJS() ;
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
			public function ObtientNomClasseExtJS()
			{
				return $this->ObtientNomAppExtJS().'.'.$this->EspaceNommageExtJS.'.'.(($this->NomClasseExtJS != '') ? $this->NomClasseExtJS : $this->IDInstanceCalc) ;
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
			public function PrepareRenduJS()
			{
				parent::PrepareRenduJS() ;
				$this->CfgDefExtJS->alias = 'widget.'.$this->IDInstanceCalc ;
				if($this->NomClasseExtendExtJS != '')
				{
					$this->CfgDefExtJS->extend = $this->NomClasseExtendExtJS ;
				}
			}
			public function CtnJSDefinition()
			{
				$ctn = '' ;
				$ctn .= 'StructDef_'.$this->IDInstanceCalc.' = '.svc_json_encode($this->CfgDefExtJS).' ;'.PHP_EOL ;
				if($this->CtnFoncInitExtJS != '')
				{
					$ctn .= 'StructDef_'.$this->IDInstanceCalc.'.init = function() {
'.$this->CtnFoncInitExtJS.'
}'.PHP_EOL ;
				}
				if($this->CtnFoncInitComponentExtJS != '')
				{
					$ctn .= 'StructDef_'.$this->IDInstanceCalc.'.initComponent = function() {
'.$this->CtnFoncInitComponentExtJS.'
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
				foreach($this->ElementsExtJS as $nom => & $elem)
				{
					$this->CfgDefExtJS->items[] = & $elem->CfgCreaExtJS ;
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
			public function PrepareRenduJS()
			{
				parent::PrepareRenduJS() ;
				$this->CfgDefExtJS->items = array() ;
				foreach($this->ElementsExtJS as $nom => & $elem)
				{
					$this->CfgDefExtJS->items[] = & $elem->CfgCreaExtJS ;
				}
				$this->CfgDefExtJS->dockedItems = array() ;
				foreach($this->ElemsDockExtJS as $nom => & $elem)
				{
					$this->CfgDefExtJS->dockedItems[] = & $elem->CfgCreaExtJS ;
				}
			}
		}
	}
	
?>