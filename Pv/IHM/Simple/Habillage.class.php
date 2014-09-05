<?php
	
	if(! defined('PV_HABILLAGE_IHM_SIMPLE'))
	{
		if(! defined('PV_COMPOSANT_UI'))
		{
			include dirname(__FILE__)."/../ComposantIU.class.php" ;
		}
		if(! defined('PV_NOYAU_SIMPLE_IHM'))
		{
			include dirname(__FILE__)."/Noyau.class.php" ;
		}
		define('PV_HABILLAGE_IHM_SIMPLE', 1) ;
		
		class PvHabillageSimpleBase extends PvObjet
		{
			public $Rendu = "" ;
			public $UtiliserThemeJQueryUi = 1 ;
			public function AppliqueSur(& $composantIU)
			{
				$this->Rendu = "" ;
				$succes = 1 ;
				switch(strtolower($composantIU->TypeComposant))
				{
					case "tableaudonneeshtml" :
					{
						$this->AppliqueSurTableauDonneesHTML($composantIU) ;
					}
					break ;
					case "formulairedonneeshtml" :
					{
						$this->AppliqueSurFormulaireDonneesHTML($composantIU) ;
					}
					break ;
					case "barremenuhtml" :
					{
						$this->AppliqueSurBarreMenuHTML($composantIU) ;
					}
					break ;
					default :
					{
						$succes = 0 ;
					}
					break ;
				}
				return $succes ;
			}
			protected function AppliqueSurBarreMenuHTML(& $composantIU)
			{
				if($composantIU->ZoneParent->InclureJQueryUi && $this->UtiliserThemeJQueryUi)
				{
					$this->AppliqueSurBarreMenuJQueryUI($composantIU) ;
				}
			}
			protected function AppliqueSurBarreMenuJQueryUI(& $composantIU)
			{
				$ctn = 'jQuery(function() {
		var selection = jQuery('.svc_json_encode("#".$composantIU->IDInstanceCalc).') ;
		if(selection.length == 0)
		{
			return ;
		}'.PHP_EOL ;
				if(strpos($composantIU->NomClasseCSSMenuRacine, 'menu_horiz') !== false)
				{
					$ctn .= 'selection.addClass("ui-widget ui-widget-content") ;
			selection.css("margin-bottom", "8px") ;
			selection.find("a").hover(function(){
				jQuery(this).parent().addClass("ui-state-hover").css({ fontWeight : "normal", border : "0px"}) ;
			},
			function(){
				jQuery(this).parent().removeClass("ui-state-hover") ;
			}) ;' ;
				}
				elseif(strpos($composantIU->NomClasseCSSMenuRacine, 'cadre_menu') !== false)
				{
					$ctn .= 'selection.addClass("ui-widget ui-widget-content") ;
			selection.css("margin-bottom", "8px") ;
			selection.find("div").css("padding", "4px") ;
			selection.find(".'.$composantIU->NomClasseCSSMenuNv1.'").addClass("ui-widget ui-state-default") ;
			selection.find(".'.$composantIU->NomClasseCSSMenuNv2.' a").hover(function(){
				jQuery(this).parent().addClass("ui-state-hover").css({ fontWeight : "normal", border : "0px"}) ;
			},
			function(){
				jQuery(this).parent().removeClass("ui-state-hover") ;
			}) ;
			selection.find(".cadre-sous-menu").addClass("ui-widget ui-widget-content") ;' ;
				}
				$ctn .= PHP_EOL .'}) ;' ;
				$this->Rendu .= $composantIU->ZoneParent->RenduContenuJsInclus($ctn) ;
			}
			protected function AppliqueSurTableauDonneesHTML(& $composantIU)
			{
				if($composantIU->ZoneParent->InclureJQueryUi && $this->UtiliserThemeJQueryUi)
				{
					$this->AppliqueSurTableauDonneesJQueryUI($composantIU) ;
				}
			}
			protected function AppliqueSurTableauDonneesJQueryUI(& $composantIU)
			{
				$ctn = 'jQuery(function() {
		if(jQuery('.svc_json_encode("#".$composantIU->IDInstanceCalc).').length > 0)
		{
			var selection = jQuery('.svc_json_encode("#".$composantIU->IDInstanceCalc).').find(".FormulaireFiltres")
			selection.addClass("ui-widget ui-widget-content") ;
			selection.find("th").addClass("ui-widget-header").css("padding", "4px") ;
			selection.css("padding", "4px") ;
			selection.find(".Boutons").find("button").button() ;
			jQuery('.svc_json_encode('#'.$composantIU->IDInstanceCalc).').find(".BlocCommandes")
				.addClass("ui-widget ui-widget-content")
				.css({"margin-bottom" : "4px", "margin-top" : "4px", "padding" : "4px"})
				.find(".Commande")
				.button() ;
		}
	}) ;'.PHP_EOL ;
				if(($composantIU->FiltresSoumis() || ! $composantIU->PossedeFiltresRendus()) && count($composantIU->ElementsEnCours) > 0)
				{
					$ctn .= '	jQuery(function() {
		var selection = jQuery('.svc_json_encode('#'.$composantIU->IDInstanceCalc).').find(".RangeeDonnees") ;
		selection.addClass("ui-widget").css("border", "0px") ;
		// alert(selection.find(".Entete td").length) ;
		selection.find(".Contenu td").addClass("ui-widget-content").hover(
			function() {
				jQuery(this).closest("tr").find("td").addClass("ui-widget-header ui-state-hover").css("font-weight", "normal") ;
			},
			function() {
				jQuery(this).closest("tr").find("td").removeClass("ui-widget-header ui-state-hover").css("font-weight", "normal") ;
			}
		) ;
		selection.find(".Entete > td").addClass("ui-widget-header") ;
		selection.find(".Entete > th").addClass("ui-widget-header") ;
	}) ;'.PHP_EOL ;
				}
				elseif(! $composantIU->FiltresSoumis() || ! $composantIU->PossedeFiltresRendus())
				{
					$ctn .= '	jQuery(function() {
		var selection = jQuery('.svc_json_encode('#'.$composantIU->IDInstanceCalc).').find(".FiltresNonRenseignes") ;
		selection.addClass("ui-widget-content ui-priority-secondary").css("padding", "4px") ;
	}) ;'.PHP_EOL ;
				}
				if($composantIU->TotalElements > 0)
				{
					$ctn .= '	jQuery(function() {
		var selection = jQuery('.svc_json_encode('#'.$composantIU->IDInstanceCalc).').find(".NavigateurRangees") ;
		if(selection.length > 0)
		{
			var ancCouleurPolice = selection.find(".LiensRangee a").css("color") ;
			selection.addClass("ui-widget ui-widget-content ui-state-active") ;
			selection.find(".LiensRangee a").button().css("color", ancCouleurPolice) ;
		}
	})' ;
				}
				$this->Rendu .= $composantIU->ZoneParent->RenduContenuJsInclus($ctn) ;
			}
			protected function AppliqueSurFormulaireDonneesHTML(& $composantIU)
			{
				// echo "classe : ".$composantIU->TypeComposant.'<br>' ;
				if($composantIU->ZoneParent->InclureJQueryUi && $this->UtiliserThemeJQueryUi)
				{
					$this->AppliqueSurFormulaireDonneesJQueryUI($composantIU) ;
				}
			}
			protected function AppliqueSurFormulaireDonneesJQueryUI(& $composantIU)
			{
				if(! $composantIU->CacherFormulaireFiltres)
				{
					$this->Rendu .= '<script type="text/javascript">
	jQuery(function() {
		var composant = jQuery('.svc_json_encode("#".$composantIU->IDInstanceCalc).') ;
		composant.addClass("ui-widget") ;
		var selection = composant.find(".FormulaireFiltres") ;
		if(selection.length > 0)
		{
			selection.addClass("ui-widget ui-widget-content") ;
		}
		selection = composant.find(".BlocCommandes") ;
		if(selection.length > 0)
		{
			var ancTaillePolice = selection.css("font-size") ;
			selection.addClass("ui-widget ui-widget-content ui-state-active").css("font-size", ancTaillePolice) ;
			selection.find(" > button").button() ;
		}
		composant.find(".Erreur").addClass("ui-state-error") ;
		composant.find(".Succes").addClass("ui-state-highlight") ;
	}) ;
</script>' ;
				}
			}
		}
	}
	
?>