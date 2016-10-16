<?php
	
	if(! defined('IMPLEM_SHOPPING_SWS'))
	{
		define('IMPLEM_SHOPPING_SWS', 1) ;
		
		class CfgImplemShoppingSws extends CfgBaseApplImplemSws
		{
			public $NomColPrix = "prix" ;
			public $NomColRemise = "remise" ;
			public $NomColQteDispo = "qte_dispo" ;
			public $ArticleTjrsDispo = 1 ;
		}
		
		class ImplemShoppingSws extends ImplemTableSws
		{
			public $NomRef = "shopping" ;
			public $TitreMenu = "shopping" ;
			public $Titre = "Shopping" ;
			public $ScriptListeBoutiqs ;
			public $ScriptAjoutBoutiq ;
			public $ScriptModifBoutiq ;
			public $ScriptSupprBoutiq ;
			public $ScriptListeShoppings ;
			public $ScriptDetailShopping ;
			public $NomTableBoutique = "boutique_shopping" ;
			public $NomTableCommande = "commande_shopping" ;
			public $NomTableArticle = "article_shopping" ;
			public $NomTableReglement = "reglement_shopping" ;
			public function RemplitMenuSpec(& $menu)
			{
				$menuLst = $menu->InscritSousMenuScript("liste_boutiqs_".$this->NomElementSyst) ;
				$menuLst->Titre = "Boutiques" ;
			}
			public function ObtientUrlAdmin()
			{
				return $this->ScriptListeBoutiqs->ObtientUrl() ;
			}
			protected function RemplitZoneAdminValide(& $zone)
			{
				$this->ScriptListeBoutiqs = $this->InscritNouvScript("liste_boutiqs_".$this->NomElementSyst, new ScriptListeBoutiqsShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptAjoutBoutiq = $this->InscritNouvScript("ajout_boutiq_".$this->NomElementSyst, new ScriptAjoutBoutiqShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptModifBoutiq = $this->InscritNouvScript("modif_boutiq_".$this->NomElementSyst, new ScriptAjoutBoutiqShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptSupprBoutiq = $this->InscritNouvScript("suppr_boutiq_".$this->NomElementSyst, new ScriptSupprBoutiqShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
			}
			public function CreeCfgAppl()
			{
				return new CfgImplemShoppingSws() ;
			}
		}
		
		class ScriptListeBoutiqsShoppingSws extends ScriptAdminImplemBaseSws
		{
			protected $DefColID ;
			protected $DefColTitre ;
			protected $DefColAddr ;
			protected $DefColContact ;
			protected $DefColActs ;
			protected $CmdAjout ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineTablPrinc() ;
			}
			protected function DetermineTablPrinc()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->TablPrinc = new TableauDonneesAdminSws() ;
				$this->TablPrinc->AdopteScript("tablPrinc", $this) ;
				$this->TablPrinc->ChargeConfig() ;
				$this->TablPrinc->ToujoursAfficher = 1 ;
				$this->DefColID = $this->TablPrinc->InsereDefColCachee("id") ;
				$this->DefColTitre = $this->TablPrinc->InsereDefCol("titre", "Titre") ;
				$this->DefColAddr = $this->TablPrinc->InsereDefCol("adresse", "Adresse") ;
				$this->DefColContact = $this->TablPrinc->InsereDefCol("contact", "Contact") ;
				$this->DefColActs = $this->TablPrinc->InsereDefColActions("Actions") ;
				$this->LienModif = $this->TablPrinc->InsereLienAction($this->DefColActs, $implem->ScriptModifBoutiq->ObtientUrlFmt(array("id" => '${id}')), "Modifier") ;
				$this->LienSuppr = $this->TablPrinc->InsereLienAction($this->DefColActs, $implem->ScriptSupprBoutiq->ObtientUrlFmt(array("id" => '${id}')), "Supprimer") ;
				$this->TablPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->TablPrinc->FournisseurDonnees->RequeteSelection = $implem->NomTableBoutique ;
				$this->CmdAjout = $this->TablPrinc->InsereCmdRedirectUrl("cmdAjout", $implem->ScriptAjoutBoutiq->ObtientUrl(), "Cr&eacute;er") ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->RenduEnteteAdmin() ;
				$ctn .= $this->TablPrinc->RenduDispositif() ;
				$ctn .= $this->RenduPiedAdmin() ;
				return $ctn ;
			}
		}
		class ScriptEditBoutiqShoppingSws extends ScriptAdminImplemBaseSws
		{
			protected $FormPrinc ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineFormPrinc() ;
			}
			protected function DetermineFormPrinc()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->FormPrinc = new PvFormulaireDonneesHTML() ;
				$this->InitFormPrinc() ;
				$this->FormPrinc->AdopteScript("formPrinc", $this) ;
				$this->FormPrinc->ChargeConfig() ;
				$this->FormPrinc->MaxFiltresEditionParLigne = 1 ;
				$this->FltID = $this->FormPrinc->InsereFltLgSelectHttpGet("id", "id = <self>") ;
				$this->FltTitre = $this->FormPrinc->InsereFltEditHttpPost("titre", "titre") ;
				$this->FltTitre->Libelle = "Titre" ;
				$this->CompTitre = $this->FltTitre->ObtientComposant() ;
				$this->CompTitre->Largeur = "270px" ;
				$this->FltStatutPubl = $this->FormPrinc->InsereFltEditHttpPost("statut_publication", "statut_publication") ;
				$this->FltStatutPubl->Libelle = "Publi&eacute;" ;
				$this->FltStatutPubl->DeclareComposant("PvZoneSelectBoolHtml") ;
				$this->FltAdr = $this->FormPrinc->InsereFltEditHttpPost("adresse", "adresse") ;
				$this->FltAdr->Libelle = "Adresse" ;
				$this->CompAdr = $this->FltAdr->DeclareComposant("PvZoneMultiligneHtml") ;
				$this->CompAdr->TotalColonnes = 80 ;
				$this->CompAdr->TotalLignes = 5 ;
				$this->FltContact = $this->FormPrinc->InsereFltEditHttpPost("contact", "contact") ;
				$this->FltContact->Libelle = "Contact" ;
				$this->CompContact = $this->FltContact->ObtientComposant() ;
				$this->CompContact->Largeur = "240px" ;
				$this->FltBp = $this->FormPrinc->InsereFltEditHttpPost("bp", "bp") ;
				$this->FltBp->Libelle = "Boite postale" ;
				$this->CompBp = $this->FltBp->ObtientComposant() ;
				$this->CompBp->Largeur = "240px" ;
				$this->FormPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->FormPrinc->FournisseurDonnees->RequeteSelection = $implem->NomTableBoutique ;
				$this->FormPrinc->FournisseurDonnees->TableEdition = $implem->NomTableBoutique ;
				$this->FormPrinc->RedirigeAnnulerVersUrl($implem->ScriptListeBoutiqs->ObtientUrl()) ;
			}
			protected function InitFormPrinc()
			{
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->RenduEnteteAdmin() ;
				$ctn .= $this->FormPrinc->RenduDispositif() ;
				$ctn .= $this->RenduPiedAdmin() ;
				return $ctn ;
			}
		}
		class ScriptAjoutBoutiqShoppingSws extends ScriptEditBoutiqShoppingSws
		{
			protected function InitFormPrinc()
			{
				parent::InitFormPrinc() ;
				$this->FormPrinc->InclureElementEnCours = 0 ;
				$this->FormPrinc->InclureTotalElements = 0 ;
				$this->FormPrinc->NomClasseCommandeExecuter = "PvCommandeAjoutElement" ;
				$this->FormPrinc->LibelleCommandeExecuter = "Ajouter" ;
			}
		}
		class ScriptModifBoutiqShoppingSws extends ScriptEditBoutiqShoppingSws
		{
			protected function InitFormPrinc()
			{
				parent::InitFormPrinc() ;
				$this->FormPrinc->InclureElementEnCours = 1 ;
				$this->FormPrinc->InclureTotalElements = 1 ;
				$this->FormPrinc->NomClasseCommandeExecuter = "PvCommandeModifElement" ;
				$this->FormPrinc->LibelleCommandeExecuter = "Modifier" ;
			}
		}
		class ScriptSupprBoutiqShoppingSws extends ScriptEditBoutiqShoppingSws
		{
			protected function InitFormPrinc()
			{
				parent::InitFormPrinc() ;
				$this->FormPrinc->Editable = 0 ;
				$this->FormPrinc->NomClasseCommandeExecuter = "PvCommandeSupprElement" ;
				$this->FormPrinc->LibelleCommandeExecuter = "Supprimer" ;
			}
		}
		
		class FormAjoutArticleShoppingSws extends PvFormulaireDonneesHtml
		{
			public $NomImplemPage = "shopping" ;
			public $InclureTotalElements = 0 ;
			public $InclureElementEnCours = 0 ;
			public $MaxFiltresEditionParLigne = 1 ;
			public $InscrireCommandeAnnuler = 0 ;
			public $LibelleCommandeExecuter = "Ajouter au panier" ;
			public $NomClasseCommandeExecuter = "CmdAjoutArticleShoppingsSws" ;
			public $FltStatutPubl ;
			public $FltId ;
			public $FltIdEntite ;
			public $FltTitreEntite ;
			public $FltIdCtrl ;
			public $FltQte ;
			protected function ObtientImplemPage()
			{
				return ImplemPageBaseSws::ObtientImplemPageComp($this) ;
			}
			public function ChargeConfig()
			{
				parent::ChargeConfig() ;
				$implem = $this->ObtientImplemPage() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				// Fournisseur de donnees
				$this->FournisseurDonnees = $this->ScriptParent->CreeFournDonnees() ;
				$this->FournisseurDonnees->RequeteSelection = $implem->NomTable ;
				$this->FournisseurDonnees->TableEdition = $implem->NomTable ;
				// Criteres validation
				$this->CommandeExecuter->InsereCritereFormatEmail(array($implem->NomParamEmail)) ;
				$this->CommandeExecuter->InsereCritereNonVide(array($implem->NomParamNom, $implem->NomParamContenu)) ;
				// Securite
				if($implem->SecuriserEdition)
				{
					$this->FltCaptcha = $this->InsereFltEditHttpPost($implem->NomParamCaptcha) ;
					$this->FltCaptcha->Libelle = "Code de s&eacute;curit&eacute;" ;
					$this->CompCaptcha = $this->FltCaptcha->DeclareComposant("PvZoneCommonCaptcha") ;
					$this->CompCaptcha->ActionAffichImg->Params = array($entite->NomParamId => $entite->LgnEnCours["id"]) ;
					$this->CommandeExecuter->InsereNouvCritere(new CritrCodeSecurValideCmtSws()) ;
				}
				// Messages
				$this->CommandeExecuter->MessageSuccesExecution = $implem->MsgSuccesSoumetCmt ;
			}
			protected function ChargeFiltresSelection()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$implem = $this->ObtientImplemPage() ;
				$bd = $entite->ModuleParent->ObtientBDSupport() ;
				$this->FltSelectIdEntite = $this->InsereFltSelectHttpGet($entite->NomParamId, "id_entite=<self>") ;
			}
			protected function ChargeFiltresEdition()
			{
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				$implem = $this->ObtientImplemPage() ;
				// ID Entite
				$this->FltIdEntite = $this->InsereFltEditFixe("id_entite", $entite->LgnEnCours["id"], "id_entite") ;
				// Titre Entite
				$this->FltTitreEntite = $this->InsereFltEditFixe("titre_entite", $entite->LgnEnCours["titre"], "titre_entite") ;
				// Nom Entite
				$this->FltNomElementModule = $this->InsereFltEditFixe("nom_entite", $entite->NomElementModule, "nom_entite") ;
				// Nom
				$this->FltQte = $this->InsereFltEditHttpPost($implem->NomParamNom, $implem->NomColNom) ;
				$this->FltQte->Libelle = "Quantit&eacute;" ;
				$this->FltQte->AccepteTagsHtml = 0 ;
				$this->FltQte->Obligatoire = 1 ;
			}
		}
		
		class CmdAjoutArticleShoppingsSws extends PvCommandeExecuterBase
		{
			protected function ExecuteInstructions()
			{
			}
		}
	}
	
?>