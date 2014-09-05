<?php
	
	if(! defined('UI_FEATURES_CHARISMA'))
	{
		if(! defined('PV_COMPOSANT_SIMPLE_IU_ELEM_FORM'))
		{
			include dirname(__FILE__)."/../../Simple/ComposantIU/ElementFormulaire.class.php" ;
		}
		define('UI_FEATURES_CHARISMA', 1) ;
		
		class WellTopBlockCharisma extends PvComposantIUBase
		{
			public $NomClasseCSSIcone = '' ;
			public $NomClasseCSSNotif = 'notification' ;
			public $Titre = '' ;
			public $Descriptions = array() ;
			public $TexteNotif = '' ;
			public $Url = '' ;
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= '<a data-rel="tooltip" title="'.$this->Titre.'" class="well span3 top-block" href="'.$this->Url.'">'.PHP_EOL ;
				$ctn .= '<span class="icon32 '.$this->NomClasseCSSIcone.'"></span>'.PHP_EOL ;
				foreach($this->Descriptions as $i => $desc)
				{
					$ctn .= '<div>'.$desc.'</div>'.PHP_EOL ;
				}
				$ctn .= '<span class="'.$this->NomClasseCSSNotif.'">'.$this->TexteNotif.'</span>'.PHP_EOL ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
		}
		class WellTopBlock1Charisma extends WellTopBlockCharisma
		{
			public $NomClasseCSSIcone = 'icon-color icon-star-on' ;
			public $NomClasseCSSNotif = 'notification' ;
			public $Titre = '4 Nouvelles inscriptions' ;
			public $Descriptions = array('Membres Pro', '1') ;
			public $TexteNotif = '1' ;
			public $Url = '?' ;
		}
		class WellTopBlock2Charisma extends WellTopBlockCharisma
		{
			public $NomClasseCSSIcone = 'icon-color icon-user' ;
			public $NomClasseCSSNotif = 'notification green' ;
			public $Titre = '4 Nouvelles inscriptions' ;
			public $Descriptions = array('Membres Pro', '1') ;
			public $TexteNotif = '1' ;
			public $Url = '?' ;
		}
		class WellTopBlock3Charisma extends WellTopBlockCharisma
		{
			public $NomClasseCSSIcone = 'icon-color icon-cart' ;
			public $NomClasseCSSNotif = 'notification yellow' ;
			public $Titre = '4 Nouvelles inscriptions' ;
			public $Descriptions = array('Membres Pro', '1') ;
			public $TexteNotif = '1' ;
			public $Url = '?' ;
		}
		class WellTopBlock4Charisma extends WellTopBlockCharisma
		{
			public $NomClasseCSSIcone = 'icon-color icon-envelope-closed' ;
			public $NomClasseCSSNotif = 'notification red' ;
			public $Titre = '4 Nouvelles inscriptions' ;
			public $Descriptions = array('Membres Pro', '1') ;
			public $TexteNotif = '1' ;
			public $Url = '?' ;
		}
				
		class OngletTabCharisma extends PvObjet
		{
			public $Titre ;
			public $IndOnglet = 0 ;
			public $CompContenu ;
			public $MsgContenu ;
			public function RenduTitre()
			{
				return $this->Titre ;
			}
			public function RenduContenu()
			{
				$ctn = '' ;
				if($this->EstPasNul($this->CompContenu))
				{
					$ctn .= $this->CompContenu->RenduDispositif() ;
				}
				else
				{
					$ctn .= $this->RenduContenuBrut() ;
				}
				return $ctn ;
			}
			public function CreeCompTexte($texte)
			{
				$comp = new PvPortionRenduHtml() ;
				$comp->Contenu = $texte ;
				return $comp ;
			}
			protected function RenduContenuBrut()
			{
			}
		}
		class TabCharisma extends PvComposantIUBase
		{
			public $Onglets = array() ;
			public $MsgSansOnglet = 'Le composant ne poss&egrave;de pas d\'onglet' ;
			protected function CreeOnglet()
			{
				return new OngletTabCharisma() ;
			}
			public function InscritTexte($titre, $msg)
			{
				$onglet = $this->CreeOnglet() ;
				$onglet->Titre = $titre ;
				$onglet->CompContenu = $onglet->CreeCompTexte($msg) ;
				$onglet->CompContenu->AdopteScript($onglet->IDInstanceCalc, $this->ScriptParent) ;
				$onglet->CompContenu->ChargeConfig() ;
				$this->InscritOnglet($onglet) ;
			}
			public function InscritNouvOnglet($onglet)
			{
				$this->InscritOnglet() ;
			}
			public function InscritOnglet(& $onglet)
			{
				$this->Onglets[] = & $onglet ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				ComposantsIUCharisma::$TabActive = $this ;
				if(count($this->Onglets) == 0)
				{
					$ctn = $this->MsgSansOnglet ;
					ComposantsIUCharisma::$TabActive = null ;
					return $ctn ;
				}
				$ctn .= '<div id="'.$this->IDInstanceCalc.'">'.PHP_EOL ;
				$ctn .= '<ul id="navigateur-'.$this->IDInstanceCalc.'" class="nav nav-tabs">'.PHP_EOL ;
				foreach($this->Onglets as $i => $onglet)
				{
					ComposantsIUCharisma::$OngletActif = $onglet ;
					$ctn .= '<li>'.PHP_EOL ;
					$ctn .= '<a href="#onglet-'.$onglet->IDInstanceCalc.'">'.PHP_EOL ;
					$ctn .= $onglet->RenduTitre() ;
					$ctn .= '</a>'.PHP_EOL ;
					$ctn .= '</li>'.PHP_EOL ;
					ComposantsIUCharisma::$OngletActif = null ;
				}
				$ctn .= '</ul>'.PHP_EOL ;
				$ctn .= '<div id="contenu-'.$this->IDInstanceCalc.'" class="tab-content">'.PHP_EOL ;
				foreach($this->Onglets as $i => $onglet)
				{
					ComposantsIUCharisma::$OngletActif = $onglet ;
					$ctn .= '<div id="onglet-'.$onglet->IDInstanceCalc.'" class="tab-pane">'.PHP_EOL ;
					$ctn .= $onglet->RenduContenu() ;
					$ctn .= '</div>'.PHP_EOL ;
					ComposantsIUCharisma::$OngletActif = null ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('jQuery("#navigateur-'.$this->IDInstanceCalc.' a:first").tab("show");
jQuery("#navigateur-'.$this->IDInstanceCalc.' a").click(function (e) {
e.preventDefault();
jQuery(this).tab("show");
});') ;
				ComposantsIUCharisma::$TabActive = null ;
				return $ctn ;
			}
		}
		
		class PvComposantExtCharisma extends PvComposantIUBase
		{
			static $SourceIncluse = 0 ;
			public function InclutSource()
			{
				$sourceInc = $this->ObtientValStatique("SourceIncluse") ;
				if($sourceInc)
				{
					return "" ;
				}
				return $this->InclutSourceInt() ;
			}
			protected function InclutSourceInt()
			{
			}
			protected function RenduExtension()
			{
				return parent::RenduDispositifBrut() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->InclutSource() ;
				$ctn .= $this->RenduExtension() ;
				return $ctn ;
			}
		
		}
		class PvElementFormCharisma extends PvElementFormulaireHtml
		{
			static $SourceIncluse = 0 ;
			public function InclutSource()
			{
				$sourceInc = $this->ObtientValStatique("SourceIncluse") ;
				if($sourceInc)
				{
					return "" ;
				}
				return $this->InclutSourceInt() ;
			}
			protected function InclutSourceInt()
			{
			}
			protected function RenduEditeur()
			{
				return parent::RenduDispositifBrut() ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$ctn .= $this->InclutSource() ;
				$ctn .= $this->RenduEditeur() ;
				return $ctn ;
			}
		}
		
		class UploadifyCharisma extends PvElementFormCharisma
		{
			public $ActTelecharg ;
			public function AdopteScript($nom, & $script)
			{
				parent::AdopteScript($nom, $script) ;
				$this->ActTelecharg = new ActTelechargCharisma() ;
			}
			protected function RenduDispositifBrut()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$styleCSS = '' ;
				$ctn .= '<input name="'.$this->NomElementHtml.'"' ;
				$ctn .= ' id="'.$this->IDInstanceCalc.'"' ;
				$ctn .= ' type="file"' ;
				$ctn .= ' data-no-uniform="true"' ;
				// $ctn .= ' value="'.htmlentities($this->Valeur).'"' ;
				$ctn .= ' />' ;
				return $ctn ;
			}
		}
		class ActTelechargCharisma extends PvActionResultatJSONZoneWeb
		{
			public $Uploader ;
		}
		
		class RatyCharisma extends PvElementFormCharisma
		{
			static $SourceIncluse = 0 ;
			public $ValeurMin = 1 ;
			public $ValeurMax = 5 ;
			public $Valeur = 1 ;
			public $Ecart = 1 ;
			public $LectureSeule = 0 ;
			public $NomFichierEtoileOff = "" ;
			public $NomFichierEtoileOn = "" ;
			protected function ObtientConfigJsonRaty()
			{
				return svc_json_encode(
					array(
						'score' => $this->Valeur,
						'scoreName' => $this->NomElementHtml,
						'number' => $this->ValeurMax - $this->ValeurMin + 1,
						'numberMax' => ($this->ValeurMax - $this->ValeurMin + 1) * $this->Ecart,
						'readOnly' => ($this->LectureSeule) ? true : false,
						'starOn' => ($this->NomFichierEtoileOn == '') ? 'star-on.png' : $this->NomFichierEtoileOn,
						'starOff' => ($this->NomFichierEtoileOff == '') ? 'star-off.png' : $this->NomFichierEtoileOff,
						'path' => $this->ZoneParent->CheminDossierImgs,
					)
				) ;
			}
			protected function RenduEditeur()
			{
				$this->CorrigeIDsElementHtml() ;
				$this->CorrigeValeur() ;
				$ctn = '' ;
				$ctn .= '<div id="'.$this->IDInstanceCalc.'" class="raty"></div>' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('jQuery("#'.$this->IDInstanceCalc.'").raty('.$this->ObtientConfigJsonRaty().') ;') ;
				return $ctn ;
			}
			protected function CorrigeValeur()
			{
				if($this->Valeur < $this->ValeurMin || $this->Valeur > $this->ValeurMax)
				{
					$this->Valeur = $this->ValeurMin ;
				}
			}
			protected function InclutSourceInt()
			{
				$ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->ZoneParent->CheminDossierJs.'/jquery.raty.min.js') ;
				return $ctn ;
			}
		}
		class IphoneStyleCharisma extends PvElementFormCharisma
		{
			public $ValeurSelection = 1 ;
			public $LibelleSelect = 'Oui' ;
			public $LibelleNonSelect = 'Non' ;
			protected function RenduEditeur()
			{
				$this->CorrigeIDsElementHtml() ;
				$ctn = '' ;
				$ctn .= '<input data-no-uniform="true" value="'.htmlentities($this->ValeurSelection).'" '.(($this->Valeur == $this->ValeurSelection) ? ' checked' : '').' type="checkbox" class="iphone-toggle" id="'.$this->IDInstanceCalc.'" name="'.htmlentities($this->NomElementHtml).'">' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('jQuery(function () { jQuery("#'.$this->IDInstanceCalc.'").iphoneStyle('.$this->ObtientConfigJsonIphoneStyle().') ; }) ;') ;
				return $ctn ;
			}
			protected function ObtientConfigJsonIphoneStyle()
			{
				return svc_json_encode(
					array(
						'checkedLabel' => $this->LibelleSelect,
						'uncheckedLabel' => $this->LibelleNonSelect
					)
				) ;
			}
			protected function InclutSourceInt()
			{
				$ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->ZoneParent->CheminDossierJs.'/jquery.iphone.toggle.js') ;
				$ctn .= $this->ZoneParent->RenduLienCSS($this->ZoneParent->CheminDossierCSS.'/jquery.iphone.toggle.css') ;
				return $ctn ;
			}
		}
		class SliderCharisma extends PvElementFormCharisma
		{
			public $Animate = "" ;
			public $Disabled = 0 ;
			public $Min = 0 ;
			public $Max = 100 ;
			public $Orientation = "horizontal" ;
			public $Range = 0 ;
			public $Step = 1 ;
			protected function ObtientConfigBrut()
			{
				$options = array(
					'value' => (intval($this->Valeur) < $this->Min) ? $this->Min : intval($this->Valeur),
					'animate' => (empty($this->Animate)) ? false : $this->Animate,
					'disabled' => ($this->Disabled) ? true : false,
					'min' => $this->Min,
					'max' => $this->Max,
					'orientation' => $this->Orientation,
					'range' => ($this->Range) ? true : false,
					'step' => $this->Step
				) ;
				return $options ;
			}
			protected function ObtientConfigJson()
			{
				return svc_json_encode($this->ObtientConfigBrut()) ;
			}
			protected function RenduDispositifBrut()
			{
				$ctn = '' ;
				$this->CorrigeIDsElementHtml() ;
				$ctn .= '<div class="slider" id="Slider'.$this->IDInstanceCalc.'"></div>' ;
				$ctn .= '<input type="hidden" name="'.$this->NomElementHtml.'" id="'.$this->IDInstanceCalc.'" value="'.htmlentities($this->Valeur).'" />' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('
var sliderConfig = '.$this->ObtientConfigJson().' ;
jQuery("#Slider'.$this->IDInstanceCalc.'").slider(sliderConfig)
.on("slidechange", function(event, ui) {
	jQuery("#'.$this->IDInstanceCalc.'").attr("value", ui.value) ;
}) ;') ;
				return $ctn ;
			}
		}
		class AutogrowTextareaCharisma extends PvElementFormCharisma
		{
			static $SourceIncluse = 0 ;
			protected function InclutSourceInt()
			{
				$ctn = '' ;
				$ctn .= $this->ZoneParent->RenduLienJsInclus($this->ZoneParent->CheminDossierJs.'/jquery.autogrow-textarea.js') ;
				return $ctn ;
			}
			protected function RenduEditeur()
			{
				$ctn = '' ;
				$this->CorrigeIDsElementHtml() ;
				$ctn .= '<textarea class="autogrow" name="'.htmlentities($this->NomElementHtml).'" id="'.$this->IDInstanceCalc.'"'.$this->RenduAttrStyleCSS().'>' ;
				$ctn .= htmlentities($this->Valeur) ;
				$ctn .= '</textarea>' ;
				$ctn .= $this->ZoneParent->RenduContenuJsInclus('jQuery("#'.$this->IDInstanceCalc.'").autogrow() ;') ;
				return $ctn ;
			}
		}
		
		class PvCommandeOuvreOngletCharisma extends PvCommandeRedirectionHttp
		{
			// public $FeneParent
			public $IdOnglet = "" ;
			public $TitreOnglet = "" ;
			public $IconeOnglet = "" ;
			public $CheminIcone = "" ;
			public $NomCadreConteneur = "parent" ;
			public $NomFoncConteneur = "ouvreOngletCadre" ;
			public $AccepteArgsFonc = 1 ;
			public $UrlIndispensable = 1 ;
			public $RafraichOnglActif = 0 ;
			public $OptionsOnglet = array() ;
			protected function CalculeConfigOnglet()
			{
				if($this->IdOnglet == "")
				{
					$this->IdOnglet = uniqid() ;
				}
				if($this->NomScript != "" && isset($this->ZoneParent->Scripts[$this->NomScript]))
				{
					$script = & $this->ZoneParent->Scripts[$this->NomScript] ;
					if($this->TitreOnglet == "")
					{
						$this->TitreOnglet = $script->Titre ;
					}
				}
			}
			protected function ExecuteInstructions()
			{
				$this->CalculeConfigOnglet() ;
				$url = '' ;
				if($this->UrlIndispensable && $this->AccepteArgsFonc)
				{
					$url = $this->ObtientUrl() ;
					if($url == '')
					{
						$this->RenseigneErreur("URL non definie pour la commande ".$this->IDInstanceCalc) ;
						return ;
					}
				}
				$args = ($this->AccepteArgsFonc) ? svc_json_encode($this->IdOnglet).',
			'.svc_json_encode($this->CheminIcone).',
			'.svc_json_encode($this->TitreOnglet).',
			'.svc_json_encode($url) : '' ;
				if(count($this->OptionsOnglet) > 0)
				{
					$args .= ', '.svc_json_encode($this->OptionsOnglet) ;
				}
				$ctn = '<script type="text/javascript">
	jQuery(function() {
		'.$this->NomCadreConteneur.'.'.$this->NomFoncConteneur.'('.$args.') ;
	}) ;'.PHP_EOL ;
				if($this->RafraichOnglActif)
				{
					$ctn .= $this->NomCadreConteneur.'.rafraichitOngletActif() ;'.PHP_EOL ;
				}
				$ctn .= '</script>' ;
				if($this->EstPasNul($this->TableauDonneesParent))
				{
					$this->TableauDonneesParent->ContenuAvantRendu .= $ctn ;
				}
				elseif($this->EstPasNul($this->FormulaireDonneesParent))
				{
					$this->FormulaireDonneesParent->ContenuAvantRendu .= $ctn ;
				}
			}
		}

		class TableauDonneesBaseCharisma extends PvTableauDonneesHtml
		{
			public function InscritCmdPrimary($nomCmd, & $cmd)
			{
				$this->AppliqueCmdPrimary($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdDanger($nomCmd, & $cmd)
			{
				$this->AppliqueCmdDanger($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdWarning($nomCmd, & $cmd)
			{
				$this->AppliqueCmdWarning($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdSuccess($nomCmd, & $cmd)
			{
				$this->AppliqueCmdSuccess($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdInfo($nomCmd, & $cmd)
			{
				$this->AppliqueCmdInfo($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdInverse($nomCmd, & $cmd)
			{
				$this->AppliqueCmdInverse($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function AppliqueCmdPrimary(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-primary' ;
			}
			public function AppliqueCmdDanger(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-danger' ;
			}
			public function AppliqueCmdWarning(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-warning' ;
			}
			public function AppliqueCmdSuccess(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-success' ;
			}
			public function AppliqueCmdInfo(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-info' ;
			}
			public function AppliqueCmdInverse(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-inverse' ;
			}
		}
		
		class FormulaireDonneesBaseCharisma extends PvFormulaireDonneesHtml
		{
			public function InscritCmdPrimary($nomCmd, & $cmd)
			{
				$this->AppliqueCmdPrimary($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdDanger($nomCmd, & $cmd)
			{
				$this->AppliqueCmdDanger($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdWarning($nomCmd, & $cmd)
			{
				$this->AppliqueCmdWarning($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdSuccess($nomCmd, & $cmd)
			{
				$this->AppliqueCmdSuccess($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdInfo($nomCmd, & $cmd)
			{
				$this->AppliqueCmdInfo($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function InscritCmdInverse($nomCmd, & $cmd)
			{
				$this->AppliqueCmdInverse($cmd) ;
				parent::InscritCmd($nomCmd, $cmd) ;
			}
			public function AppliqueCmdPrimary(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-primary' ;
			}
			public function AppliqueCmdDanger(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-danger' ;
			}
			public function AppliqueCmdWarning(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-warning' ;
			}
			public function AppliqueCmdSuccess(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-success' ;
			}
			public function AppliqueCmdInfo(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-info' ;
			}
			public function AppliqueCmdInverse(& $cmd)
			{
				$cmd->NomClsCSS = 'btn btn-inverse' ;
			}
		}
		
	}
	
?>