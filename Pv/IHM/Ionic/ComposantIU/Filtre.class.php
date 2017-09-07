<?php
	
	if(! defined('PV_FILTRE_IU_IONIC'))
	{
		define('PV_FILTRE_IU_IONIC', 1) ;
		
		class PvFiltreIUBaseIonic extends PvObjet
		{
			public $Obligatoire = 0 ;
			public $PageSrcParent ;
			public $ZoneParent ;
			public $ApplicationParent ;
			public $NomElementPageSrc = "" ;
			public $NomElementZone = "" ;
			public $NomGroupeFiltre = "" ;
			public $TypeLiaisonParametre = "" ;
			public $Role = "base" ;
			public $Liaison ;
			public $Composant ;
			public $Libelle = "" ;
			public $CheminIcone = "" ;
			public $NomClasseCSS = "" ;
			public $NomClasseCSSIcone = "" ;
			public $EspaceReserve = "" ;
			public $NomClasseComposant = "PvTagIonInput" ;
			public $NomComposant = "" ;
			public $NomParametreLie = "" ;
			public $NomParametreDonnees = "" ;
			public $AliasParametreDonnees = "" ;
			public $NomClasseLiaison = null ;
			public $ExpressionDonnees = "" ;
			public $NomColonneLiee = "" ;
			public $ExpressionColonneLiee = "" ;
			public $NePasInclureSiVide = 1 ;
			public $ValeurParDefaut ;
			public $ValeurVide ;
			public $ValeurParametre ;
			public $ValeurBrute = "" ;
			public $DejaLie = 0 ;
			public $Invisible = 0 ;
			public $EstEtiquette = 0 ;
			public $LectureSeule = 0 ;
			public $NePasLierColonne = 0 ;
			public $NePasLireColonne = 0 ;
			public $NePasLierParametre = 0 ;
			public $NePasIntegrerParametre = 0 ;
			public $AppliquerCorrecteurValeur = 1 ;
			public $CorrecteurValeur ;
			public $FormatteurEtiquette ;
			public function ImpressionEnCours()
			{
				return $this->EstPasNul($this->ZoneParent) && $this->ZoneParent->ImpressionEnCours() ;
			}
			public function AdopteZone($nom, & $zone)
			{
				$this->ZoneParent = & $zone ;
				$this->ApplicationParent = & $zone->ApplicationParent ;
				$this->NomElementZone = $nom ;
			}
			public function AdoptePageSrc($nom, & $pageSrc)
			{
				$this->PageSrcParent = & $pageSrc ;
				$this->NomElementPageSrc = $nom ;
				$this->AdopteZone($pageSrc->NomElementZone."_".$nom, $pageSrc->ZoneParent) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$this->ChargeComposant() ;
			}
			protected function CorrigeConfig()
			{
				if($this->NomParametreDonnees == '' && $this->NomElementPageSrc != '')
					$this->NomParametreDonnees = $this->NomElementPageSrc ;
				if($this->NomParametreLie == '' && $this->NomElementPageSrc != '')
					$this->NomParametreLie = $this->NomElementPageSrc ;
			}
			public function NePasInclure()
			{
				if($this->NePasInclureSiVide && ! $this->Obligatoire)
				{
					return ($this->ValeurVide == $this->ValeurParametre) ;
				}
				return 0 ;
			}
			public function RenduPossible()
			{
				return (! $this->Invisible && ($this->TypeLiaisonParametre == 'request')) ? 1 : 0 ;
			}
			public function ObtientValeurParametre()
			{
				return "" ;
			}
			public function CorrigeNomParametreLie()
			{
				if($this->NomParametreLie == '')
				{
					if($this->NomElementPageSrc != '')
						$this->NomParametreLie = $this->NomElementPageSrc ;
					else
						$this->NomParametreLie = $this->IDInstanceCalc ;
				}
			}
			public function ObtientLibelle()
			{
				$libelle = $this->Libelle ;
				if($libelle == '')
				{
					$libelle = $this->NomElementPageSrc ;
				}
				return $libelle ;
			}
			public function Lie()
			{
				$this->CorrigeConfig() ;
				if($this->DejaLie == 1)
				{
					return $this->ValeurParametre ;
				}
				$this->ValeurParametre = $this->ValeurParDefaut ;
				// echo $this->NomParametreDonnees ;
				if($this->Invisible == 1 || $this->NePasLierParametre == 1)
				{
					return $this->ValeurParametre ;
				}
				$valeurParametre = $this->ObtientValeurParametre() ;
				if($valeurParametre !== $this->ValeurVide || $this->ValeurVide !== null)
				{
					$this->ValeurParametre = $valeurParametre ;
				}
				$this->DejaLie = 1 ;
				return $this->ValeurParametre ;
			}
			public function DefinitColLiee($nomCol)
			{
				$this->NomColonneLiee = $nomCol ;
				$this->NomParametreDonnees = $nomCol ;
			}
			public function ObtientNomComposant()
			{
				$this->CorrigeNomParametreLie() ;
				$nomComposant = $this->NomParametreLie ;
				return $nomComposant ;
			}
			public function ObtientIDElementHtmlComposant()
			{
				if($this->EstNul($this->Composant))
				{
					$this->DeclareComposant($this->NomClasseComposant) ;
				}
				if($this->EstNul($this->Composant))
					return "" ;
				$iDInstanceCalc = $this->Composant->IDInstanceCalc ;
				return $iDInstanceCalc ;
			}
			public function ObtientIDComposant()
			{
				return $this->ObtientIDElementHtmlComposant() ;
			}
			public function Rendu(& $pageSrc)
			{
				if($this->EstEtiquette || $this->ImpressionEnCours())
				{
					return $this->DeploieEtiquette($pageSrc) ;
				}
				if($this->EstNul($this->Composant))
				{
					$this->DeclareComposant($this->NomClasseComposant) ;
				}
				if($this->EstNul($this->Composant))
				{
					return "<ion-content>(Composant nul)</ion-content>" ;
				}
				$this->Composant->Valeur = $this->Lie() ;
				$this->Composant->EspaceReserve = $this->EspaceReserve ;
				if($this->Composant->EspaceReserve == "" && $this->ZoneParent->LibelleEspaceReserveFiltres == 1)
				{
					$this->Composant->EspaceReserve = $this->Libelle ;
				}
				$this->Composant->FiltreParent = $this ;
				$this->Composant->DeploieDispositif($pageSrc) ;
				$ctn = $this->Composant->RenduDispositif() ;
				$this->Composant->FiltreParent = null ;
				return $ctn ;
			}
			public function Etiquette(& $pageSrc)
			{
				if($this->EstNul($this->Composant))
				{
					$this->DeclareComposant($this->NomClasseComposant) ;
				}
				if($this->EstNul($this->Composant))
				{
					return "<ion-content>(Composant nul)</ion-content>" ;
				}
				// $this->Composant->Valeur = $this->FormatteurEtiquette->Applique(html_entity_decode($this->Lie()), $this) ;
				$this->Composant->Valeur = $this->Lie() ;
				$this->Composant->FiltreParent = $this ;
				$this->Composant->DeploieDispositif($pageSrc) ;
				$ctn = $this->Composant->RenduEtiquette() ;
				$this->Composant->FiltreParent = null ;
				return $ctn ;
			}
			public function PeutCalculerElemsRendu()
			{
				return $this->Composant->PeutCalculerElemsRendu() ;
			}
			public function InitComposant()
			{
			}
			public function & ObtientComposant()
			{
				if($this->EstNul($this->Composant))
					return $this->DeclareComposant($this->NomClasseComposant) ;
				return $this->Composant ;
			}
			public function & DeclareComposant($nomClasseComposant)
			{
				$this->Composant = $this->ValeurNulle() ;
				$this->NomClasseComposant = $nomClasseComposant ;
				if(class_exists($nomClasseComposant))
				{
					$this->Composant = new $nomClasseComposant() ;
					$this->Composant->AdoptePageSrc($this->ObtientNomComposant(), $this->PageSrcParent) ;
					$this->InitComposant() ;
					$this->Composant->ChargeConfig() ;
				}
				return $this->Composant ;
			}
			public function RemplaceComposant($nouvComposant)
			{
				$this->Composant = $nouvComposant ;
				$this->Composant->AdoptePageSrc($this->ObtientNomComposant(), $this->PageSrcParent) ;
				$this->InitComposant() ;
				$this->Composant->ChargeConfig() ;
			}
			public function FormatTexte()
			{
				$valTemp = $this->Lie() ;
				return $valTemp ;
			}
			public function ValeurTs()
			{
				return 'null' ;
			}
			public function DefinitionTs(& $classeTs)
			{
			}
			public function FournitMethodesDistantes()
			{
				$comp = $this->ObtientComposant() ;
				if($this->EstPasNul($comp))
				{
					$comp->FiltreParent = & $this ;
					$comp->FournitMethodesDistantes() ;
				}
			}
		}
		
		class PvFiltreDistantIonic extends PvFiltreIUBaseIonic
		{
			public function ObtientValeurParametre()
			{
				$zone = & $this->ZoneParent ;
				$valeur = $this->ValeurParDefaut ;
				if($this->ZoneParent->PossedeMtdDistSelect())
				{
					$param = $this->ZoneParent->MtdDistSelect->Param() ;
					$nomParamLie = $this->NomParametreLie ;
					if($this->NomGroupeFiltre != "")
					{
						$nomGroupeFiltre = $this->NomGroupeFiltre ;
						if(isset($param->Args->$nomGroupeFiltre) && isset($param->Args->$nomGroupeFiltre->$nomParamLie))
						{
							$valeur = $param->Args->$nomGroupeFiltre->$nomParamLie ;
						}
					}
					elseif(isset($param->Args->$nomParamLie))
					{
						$valeur = $param->Args->$nomParamLie ;
					}
				}
				return $valeur ;
			}
		}
		
		class PvFiltreHttpRequestIonic extends PvFiltreDistantIonic
		{
			public $TypeLiaisonParametre = "request" ;
			public function ValeurTs()
			{
				return 'this.'.$this->IDInstanceCalc ;
			}
			public function DefinitionTs(& $classeTs)
			{
				$classeTs->InsereMembre($this->IDInstanceCalc, svc_json_encode($this->ValeurParDefaut)) ;
			}
		}
		
		class PvFiltreTsIonic extends PvFiltreDistantIonic
		{
			public $TypeLiaisonParametre = "ts" ;
			public $CorpsBrutTs ;
			public function ValeurTs()
			{
				return 'this.'.$this->IDInstanceCalc.'()' ;
			}
			public function DefinitionTs(& $classeTs)
			{
				$mtd = $classeTs->InsereMethode($this->IDInstanceCalc) ;
				$mtd->CorpsBrut = $this->CorpsBrutTs ;
			}
		}
		
		class PvFiltreFixeIonic extends PvFiltreIUBaseIonic
		{
			public $TypeLiaisonParametre = "fixe" ;
			public function ValeurTs()
			{
				return svc_json_encode($this->Lie()) ;
			}
			public function Lie()
			{
				$this->CorrigeConfig() ;
				if($this->DejaLie == 1)
				{
					return $this->ValeurParametre ;
				}
				$this->ValeurParametre = $this->ValeurParDefaut ;
				if($this->Invisible == 1 || $this->NePasLierParametre == 1)
				{
					return $this->ValeurParametre ;
				}
				$this->DejaLie = 1 ;
				return $this->ValeurParametre ;
			}
		}
	}
	
?>