<?php
	
	if(! defined('PV_NOYAU_ZONE_BOOTSTRAP'))
	{
		define('PV_NOYAU_ZONE_BOOTSTRAP', 1) ;
		
		class PvHabillageBootstrapBase extends PvHabillageSimpleBase
		{
			protected function AppliqueSurBarreMenuHTML(& $composantIU)
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
				$ctn = 'jQuery(function() {
		if(jQuery('.svc_json_encode("#".$composantIU->IDInstanceCalc).').length > 0)
		{
			var selection = jQuery('.svc_json_encode("#".$composantIU->IDInstanceCalc).').find(".FormulaireFiltres") ;
			if(selection.length > 0)
			{
				selection.addClass("table-bordered") ;
				selection.find("th").addClass("bg-primary").css("padding", "4px") ;
				selection.find("td").css("padding", "4px") ;
				selection.find(":input").addClass("form-control") ;
				selection.find(".Boutons :button").removeClass("form-control").addClass("btn btn-primary") ;
				selection.css("padding", "4px") ;
			}
		}
	}) ;'.PHP_EOL ;
				if(($composantIU->FiltresSoumis() || ! $composantIU->PossedeFiltresRendus()) && count($composantIU->ElementsEnCours) > 0)
				{
					$ctn .= '	jQuery(function() {
		var selection = jQuery('.svc_json_encode('#'.$composantIU->IDInstanceCalc).').find(".RangeeDonnees") ;
		selection.addClass("table-bordered") ;
		// alert(selection.find(".Entete td").length) ;
		selection.find(".Entete > th").addClass("bg-primary") ;
		selection.find(".Contenu td").hover(
			function() {
				jQuery(this).closest("tr").find("td").addClass("active") ;
			},
			function() {
				jQuery(this).closest("tr").find("td").removeClass("active") ;
			}
		) ;
		selection.find(".Entete > td").addClass("bg-primary") ;
	}) ;'.PHP_EOL ;
				}
				elseif(! $composantIU->FiltresSoumis() || ! $composantIU->PossedeFiltresRendus())
				{
					$ctn .= '	jQuery(function() {
		var selection = jQuery('.svc_json_encode('#'.$composantIU->IDInstanceCalc).').find(".FiltresNonRenseignes") ;
		selection.addClass("bg-danger").css("padding", "4px") ;
	}) ;'.PHP_EOL ;
				}
				if($composantIU->TotalElements > 0)
				{
					$ctn .= '	jQuery(function() {
		var selection = jQuery('.svc_json_encode('#'.$composantIU->IDInstanceCalc).').find(".NavigateurRangees") ;
		if(selection.length > 0)
		{
			selection.addClass("bg-info") ;
			selection.find(".LiensRangee a").addClass("btn btn-primary") ;
			// selection.find(":input").addClass("form-control") ;
		}
	})' ;
				}
				$this->Rendu .= $composantIU->ZoneParent->RenduContenuJsInclus($ctn) ;
			}
			protected function AppliqueSurFormulaireDonneesHTML(& $composantIU)
			{
				if(! $composantIU->CacherFormulaireFiltres)
				{
					$this->Rendu .= '<script type="text/javascript">
	jQuery(function() {
		var composant = jQuery('.svc_json_encode("#".$composantIU->IDInstanceCalc).') ;
		var selection = composant.find(".FormulaireFiltres") ;
		if(selection.length > 0)
		{
			selection.addClass("table-bordered").css("padding", "4px") ;
			selection.find(":input").addClass("form-control") ;
		}
		selection = composant.find(".BlocCommandes") ;
		if(selection.length > 0)
		{
			selection.addClass("table-bordered bg-info").css("padding", "4px") ;
			selection.find(":button").addClass("btn btn-primary") ;
		}
		composant.find(".Erreur").addClass("bg-success") ;
		composant.find(".Succes").addClass("bg-danger") ;
	}) ;
</script>' ;
				}
			}

		}
		
		class PvZoneBaseBootstrap extends PvZoneWebSimple
		{
			public $ViewportMeta = "width=device-width, initial-scale=1, maximum-scale=1" ;
			public $NomClasseScriptConnexion = "PvScriptConnexionBootstrap" ;
			public $CheminJsBootstrap = "js/bootstrap.min.js" ;
			public $CheminCSSBootstrap = "css/bootstrap.min.css" ;
			public $CheminCSSThemeBootstrap = "css/bootstrap-theme.min.css" ;
			public $InclureJQuery = 1 ;
			public $InclureBootstrap = 1 ;
			public $NomClasseHabillage = "PvHabillageBootstrapBase" ;
			public function InclutLibrairiesExternes()
			{
				if(! $this->InclureJQuery)
				{
					$this->InclureJQuery = 1 ;
				}
				// Inscrire CSS
				$this->InscritLienCSS($this->CheminCSSBootstrap) ;
				parent::InclutLibrairiesExternes() ;
				// Placer Bootstrap après JQuery
				$ctnJs = new PvLienFichierJs() ;
				$ctnJs->Src = $this->CheminJsBootstrap ;
				array_splice($this->ContenusJs, 2, 0, array($ctnJs)) ;
			}
		}
		
		class PvZoneBootstrap extends PvZoneBaseBootstrap
		{
		}
	}
	
?>