<?php
	
	if(! defined('PV_COMPOSANT_ADMIN_DIRECTE'))
	{
		if(! defined('PV_IHM_SIMPLE'))
		{
			include dirname(__FILE__)."/../Simple.class.php" ;
		}
		define('PV_COMPOSANT_ADMIN_DIRECTE', 1) ;
		
		class PvConfigFormatteurColonneOuvreOnglet extends PvConfigFormatteurColonneLien
		{
			public $FormatIdOnglet = "" ;
			public $FormatTitreOnglet = "" ;
			public $NomCadreConteneur = "parent" ;
			protected function RenduBrut($donnees)
			{
				$donneesUrl = array_map("urlencode", $donnees) ;
				$href = _parse_pattern($this->FormatURL, $donneesUrl) ;
				$libelle = _parse_pattern($this->FormatLibelle, $donnees) ;
				$idOnglet = _parse_pattern($this->FormatIdOnglet, $donnees) ;
				$titreOnglet = _parse_pattern($this->FormatTitreOnglet, $donnees) ;
				$scriptJs = $this->NomCadreConteneur.'.ouvreOngletCadre('.svc_json_encode($idOnglet).', "", '.svc_json_encode($titreOnglet).', '.svc_json_encode($href).')' ;
				$ctn = '' ;
				$ctn .= '<a href="javascript:'.htmlentities($scriptJs).'"' ;
				if($this->ChaineAttributs != '')
				{
					$ctn .= ' '.$this->ChaineAttributs ;
				}
				if($this->ClasseCSS != '')
				{
					$ctn .= ' class="'.$this->ClasseCSS.'"' ;
				}
				$ctn .= '>' ;
				$ctn .= $this->RenduIcone($donnees, $donneesUrl) ;
				if($this->EncodeHtmlLibelle)
				{
					$libelle = htmlentities($libelle) ;
				}
				$ctn .= $libelle ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
		}
		class PvConfigFormatteurColonneOuvreFenetre extends PvConfigFormatteurColonneLien
		{
			public $FormatIdOnglet = "" ;
			public $FormatTitreOnglet = "" ;
			public $NomCadreConteneur = "parent" ;
			public $OptionsOnglet = array() ;
			public $RafraichOnglActif = 0 ;
			public $UrlOnglActifSurFerm = "" ;
			public function DefinitScriptOnglActifSurFerm(& $script, $params=array())
			{
				$this->UrlOnglActifSurFerm = $script->ObtientUrlParam($params) ;
			}
			protected function RenduBrut($donnees)
			{
				$donneesUrl = array_map("urlencode", $donnees) ;
				$href = _parse_pattern($this->FormatURL, $donneesUrl) ;
				$libelle = _parse_pattern($this->FormatLibelle, $donnees) ;
				$idOnglet = _parse_pattern($this->FormatIdOnglet, $donnees) ;
				$titreOnglet = _parse_pattern($this->FormatTitreOnglet, $donnees) ;
				$args = '' ;
				if($this->UrlOnglActifSurFerm != "")
				{
					$this->OptionsOnglet["UrlOnglActifSurFerm"] = $this->UrlOnglActifSurFerm ;
				}
				if(count($this->OptionsOnglet) > 0)
				{
					$args .= ', '.svc_json_encode($this->OptionsOnglet) ;
				}
				$scriptJs = $this->NomCadreConteneur.'.ouvreFenetreCadre('.svc_json_encode($idOnglet).', "", '.svc_json_encode($titreOnglet).', '.svc_json_encode($href).''.$args.')' ;
				// echo $this->RafraichOnglActif ;
				if($this->RafraichOnglActif)
				{
					$scriptJs .= '; '.$this->NomCadreConteneur.'.rafraichitOngletActif() ;'.PHP_EOL ;
				}
				$ctn = '' ;
				$ctn .= '<a href="javascript:'.htmlentities($scriptJs).'"' ;
				if($this->ChaineAttributs != '')
				{
					$ctn .= ' '.$this->ChaineAttributs ;
				}
				if($this->ClasseCSS != '')
				{
					$ctn .= ' class="'.$this->ClasseCSS.'"' ;
				}
				$ctn .= '>' ;
				$ctn .= $this->RenduIcone($donnees, $donneesUrl) ;
				if($this->EncodeHtmlLibelle)
				{
					$libelle = htmlentities($libelle) ;
				}
				$ctn .= $libelle ;
				$ctn .= '</a>' ;
				return $ctn ;
			}
		}
		
		class PvCommandeOuvreOngletAdminDirecte extends PvCommandeRedirectionHttp
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
			public $UrlOnglActifSurFerm = "" ;
			public $OptionsOnglet = array() ;
			public function DefinitScriptOnglActifSurFerm(& $script, $params=array())
			{
				$this->UrlOnglActifSurFerm = $script->ObtientUrlParam($params) ;
			}
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
				if($this->UrlOnglActifSurFerm != "")
				{
					$this->OptionsOnglet["UrlOnglActifSurFerm"] = $this->UrlOnglActifSurFerm ;
				}
				if(count($this->OptionsOnglet) > 0)
				{
					if($args != '')
						$args .= ', ' ;
					$args .= svc_json_encode($this->OptionsOnglet) ;
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
		class PvCmdFermeOngletActifAdminDirecte extends PvCommandeOuvreOngletAdminDirecte
		{
			public $NomFoncConteneur = "fermeOngletActif" ;
			public $AccepteArgsFonc = 0 ;
		}
		class PvCmdFermeFenetreActiveAdminDirecte extends PvCommandeOuvreOngletAdminDirecte
		{
			public $NomFoncConteneur = "fermeFenetreActive" ;
			public $AccepteArgsFonc = 0 ;
		}
		class PvCommandeOuvreFenetreAdminDirecte extends PvCommandeOuvreOngletAdminDirecte
		{
			public $NomCadreConteneur = "parent" ;
			public $NomFoncConteneur = "ouvreFenetreCadre" ;
			public $AccepteArgsFonc = 1 ;
		}
		
		class PvFormatteurColonneOuvreOnglets extends PvFormatteurColonneLiens
		{
		}
		
		class PvTableauDonneesAdminDirecte extends PvTableauDonneesHtml
		{
			public $InclureCmdRafraich = 1 ;
			public $LibelleCmdRafraich = "Actualiser" ;
			public $CheminIconeCmdRafraich = "images/icones/actualiser.png" ;
			protected function ExtraitCommandesRendu()
			{
				$commandes = $this->Commandes ;
				if($this->InclureCmdRafraich)
				{
					$cmd = new PvCommandeSoumetFiltresTabl() ;
					$cmd->AdopteTableauDonnees("cmdRafraichit", $this) ;
					$cmd->Libelle = $this->LibelleCmdRafraich ;
					$cmd->CheminIcone = $this->CheminIconeCmdRafraich ;
					$commandes["cmdRafraichit"] = & $cmd ;
				}
				// print "kkk : ".count($commandes) ;
				return $commandes ;
			}
			public function CreeLienOuvreOngletAction()
			{
				return new PvConfigFormatteurColonneOuvreFenetre() ;
			}
			public function InsereLienOuvreOngletAction(& $col, $formatUrl='', $formatLib='', $formatIdOnglet='', $formatTitreOnglet='', $optsOnglet=array())
			{
				$lien = null ;
				if($this->EstNul($col) || $col->Formatteur == null)
				{
					return $lien ;
				}
				$lien = $this->CreeLienOuvreFenetreAction() ;
				$lien->FormatURL = $formatUrl ;
				$lien->FormatLibelle = $formatLib ;
				$lien->FormatIdOnglet = $formatIdOnglet ;
				$lien->FormatTitreOnglet = $formatTitreOnglet ;
				$lien->OptionsOnglet = $optsOnglet ;
				$col->Formatteur->Liens[] = & $lien ;
				return $col ;
			}
			public function CreeLienOuvreFenetreAction()
			{
				return new PvConfigFormatteurColonneOuvreFenetre() ;
			}
			public function & InsereLienOuvreFenetreAction(& $col, $formatUrl='', $formatLib='', $formatIdOnglet='', $formatTitreOnglet='', $optsOnglet=array(), $urlOnglActifSurFerm="")
			{
				$lien = null ;
				if($this->EstNul($col) || $col->Formatteur == null)
				{
					return $lien ;
				}
				$lien = $this->CreeLienOuvreFenetreAction() ;
				$lien->FormatURL = $formatUrl ;
				$lien->FormatLibelle = $formatLib ;
				$lien->FormatIdOnglet = $formatIdOnglet ;
				$lien->FormatTitreOnglet = $formatTitreOnglet ;
				$lien->UrlOnglActifSurFerm = $urlOnglActifSurFerm ;
				$lien->OptionsOnglet = $optsOnglet ;
				$col->Formatteur->Liens[] = & $lien ;
				return $lien ;
			}
			public function CreeCmdOuvreOnglet()
			{
				return new PvCommandeOuvreOngletAdminDirecte() ;
			}
			public function & InsereCmdOuvreOngletScript($nomCmd, $url, $libelle, $idOnglet='', $titreOnglet='', $optsOnglet=array())
			{
				$cmd = $this->CreeCmdOuvreOnglet() ;
				$cmd->Url = $url ;
				$cmd->Libelle = $libelle ;
				$cmd->IdOnglet = $idOnglet ;
				$cmd->TitreOnglet = $titreOnglet ;
				$cmd->OptionsOnglet = $optsOnglet ;
				$this->InscritCommande($nomCmd, $cmd) ;
				return $cmd ;
			}
			public function CreeCmdOuvreFenetre()
			{
				return new PvCommandeOuvreFenetreAdminDirecte() ;
			}
			public function & InsereCmdOuvreFenetreScript($nomCmd, $url, $libelle, $idOnglet='', $titreOnglet='', $optsOnglet=array(), $urlOnglActifSurFerm="")
			{
				$cmd = $this->CreeCmdOuvreFenetre() ;
				$cmd->Url = $url ;
				$cmd->Libelle = $libelle ;
				$cmd->IdOnglet = $idOnglet ;
				$cmd->TitreOnglet = $titreOnglet ;
				$cmd->OptionsOnglet = $optsOnglet ;
				$cmd->UrlOnglActifSurFerm = $urlOnglActifSurFerm ;
				$this->InscritCommande($nomCmd, $cmd) ;
				return $cmd ;
			}
		}

	}
	
?>