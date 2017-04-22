<?php
	
	if(! defined('IMPLEM_SHOPPING_SWS'))
	{
		define('IMPLEM_SHOPPING_SWS', 1) ;
		
		class FormDonneesBaseImplemShoppingSws extends PvFormulaireDonneesHtml
		{
			public function ObtientImplemPage()
			{
				return $this->ScriptParent->ObtientImplemPage() ;
			}
			public function ObtientImplemPageInterne()
			{
				return ImplemPageBaseSws::ObtientImplemPageComp($this) ;
			}
		}
		
		class CfgImplemShoppingSws extends CfgBaseApplImplemSws
		{
			public $NomColPrix = "prix" ;
			public $PrixParDefaut = 0 ;
			public $NomColRemise = "remise" ;
			public $NomColQteDispo = "qte_dispo" ;
			public $ArticleTjrsDispo = 1 ;
		}
		
		class MoyPaiemtBaseShoppingSws
		{
			public $FltIdentif1 ;
			public $FltIdentif2 ;
			public $FltIdentif3 ;
			public $FltIdentif4 ;
			public $FltIdentif5 ;
			public $FltIdentif6 ;
			public $FltIdentif7 ;
			public $FltIdentif8 ;
			public function EstIndefini()
			{
				return 0 ;
			}
			public function EstActive()
			{
				return 1 ;
			}
			public function Id()
			{
				return "base" ;
			}
			public function Titre()
			{
				return "base" ;
			}
			public function RemplitFormEditRglt(& $form)
			{
			}
			public function PrepareScriptRgltCmd(& $implem, & $script, $lgnRglt)
			{
			}
			public function RenduScriptRgltCmd(& $implem, & $script, $lgnRglt)
			{
			}
		}
		class MoyPaiemtIndefShoppingSws extends MoyPaiemtBaseShoppingSws
		{
			public function EstIndefini()
			{
				return 1 ;
			}
		}
		class MoyPaiemtEmailShoppingSws extends MoyPaiemtBaseShoppingSws
		{
			protected $IdBoutique = 0 ;
			protected $ConfirmAct = 0 ;
			protected $EnvoiMailSucces = 0 ;
			public $FmtDemConfirmEnvoiMail = '<p>&nbsp;</p>
<p>Voulez-vous confirmer l\'envoi de mail de votre commande par <b>${titre}</b></p>' ;
			public $FmtSuccesConfirmEnvoiMail = '<p class="Succes">Votre commande a &eacute;t&eacute; envoy&eacute;e avec succ&ecirc;s</p>' ;
			public $FmtEchecConfirmEnvoiMail = '<p class="Erreur">Erreur survenue lors de l\'envoi de votre commande. Veuillez r&eacute;essayer ult&eacute;rieurement.</p>' ;
			public $LibelleLienRetour = 'Retour au panier' ;
			public function Id()
			{
				return "email" ;
			}
			public function Titre()
			{
				return "Email" ;
			}
			public function RemplitFormEditRglt(& $form)
			{
				$this->FltIdentif1 = $form->InsereFltEditHttpPost("identifiant1", "identifiant1") ;
				$this->FltIdentif1->Libelle = "Email exp&eacute;diteur" ;
				$this->CompIdentif1 = $this->FltIdentif1->ObtientComposant() ;
				$this->CompIdentif1->Largeur = "300px" ;
				$this->FltIdentif2 = $form->InsereFltEditHttpPost("identifiant2", "identifiant2") ;
				$this->FltIdentif2->Libelle = "Email destinataire" ;
				$this->CompIdentif2 = $this->FltIdentif2->ObtientComposant() ;
				$this->CompIdentif2->Largeur = "300px" ;
				$this->FltIdentif3 = $form->InsereFltEditHttpPost("identifiant3", "identifiant3") ;
				$this->FltIdentif3->Libelle = "Sujet du mail" ;
				$this->FltIdentif3->ValeurParDefaut = 'R&eacute;ception commande ${id}' ;
				$this->CompIdentif3 = $this->FltIdentif3->ObtientComposant() ;
				$this->CompIdentif3->Largeur = "350px" ;
				$this->FltIdentif4 = $form->InsereFltEditHttpPost("identifiant4", "identifiant4") ;
				$this->FltIdentif4->Libelle = "Corps du mail" ;
				$this->FltIdentif4->ValeurParDefaut = '<p>Bonjour All,</p>
<p>Vous venez de recevoir une commande dont voici les d&eacute;tails :</p>
${details_commande}
<p>Cdt.</p>' ;
				$this->CompIdentif4 = $this->FltIdentif4->DeclareComposant("PvCkEditor") ;
			}
			public function PrepareScriptRgltCmd(& $implem, & $script, $lgnRglt)
			{
				$this->ConfirmAct = _GET_def("confirme_action") ;
				if($this->ConfirmAct == 1)
				{
					$this->ConfirmActEnvoiMail($implem, $script, $lgnRglt) ;
				}
			}
			protected function ConfirmActEnvoiMail(& $implem, & $script, $lgnRglt)
			{
				$bd = $script->ObtientBDSupport() ;
				$lgnCmd = $bd->FetchSqlRow("select * from ".$bd->EscapeTableName($implem->NomTableCommande)." where id=:id", array("id" => $script->IdCommande)) ;
				$lgnsArticle = $implem->LgnsDetailsCommande($script->IdCommande) ;
				$donneesMail = $lgnCmd ;
				$donneesMail["date_commande_fr"] = date_fr($lgnCmd["date_commande"]) ;
				$donneesMail["details_commande"] = $implem->RenduDetailsCommande($lgnsArticle, $script->FormPrinc) ;
				$sujetMail = _parse_pattern($lgnRglt["identifiant3"], $donneesMail) ;
				$corpsMail = _parse_pattern($lgnRglt["identifiant4"], $donneesMail) ;
				$this->EnvoiMailSucces = send_html_mail($lgnRglt["identifiant2"], $sujetMail, $corpsMail, $lgnRglt["identifiant1"]) ;
				$this->IdBoutique = $lgnCmd["id_boutique"] ;
				$bd->RunSql("INSERT INTO rglt_email_shopping (id_commande, id_reglement, email_from, email_to, sujet_mail, corps_mail, envoi_succes) values(:idCommande, :idRglt, :from, :to, :sujet, :corps, :envoiSucces)", array(
					"idCommande" => $script->IdCommande,
					"idRglt" => $lgnRglt["id"],
					"from" => $lgnRglt["identifiant1"],
					"to" => $lgnRglt["identifiant2"],
					"sujet" => $sujetMail,
					"corps" => $corpsMail,
					"envoiSucces" => ($this->EnvoiMailSucces) ? 1 : 0,
				)) ;
				// $this->EnvoiMailSucces = 1 ;
				if($this->EnvoiMailSucces == true)
				{
					$implem->ValideConfirmCommande($script->IdCommande, $lgnRglt["id"]) ;
				}
				$script->AfficherCommande = 0 ;
			}
			public function RenduScriptRgltCmd(& $implem, & $script, $lgnRglt)
			{
				$ctn = '' ;
				if($this->ConfirmAct == 0)
				{
					$ctn .= _parse_pattern($this->FmtDemConfirmEnvoiMail, $lgnRglt) ;
					$ctn .= '<p>
	<a href="'.$implem->ScriptChoixRgltCmd->ObtientUrlParam(array("id" => $lgnRglt["id_boutique"])).'">Annuler</a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="'.$implem->ScriptProcessRgltCmd->ObtientUrlParam(array("id" => $lgnRglt["id"], "confirme_action" => 1)).'">Confirmer</a>
</p>' ;
				}
				else
				{
					if($this->EnvoiMailSucces)
					{
						$ctn .= _parse_pattern($this->FmtSuccesConfirmEnvoiMail, $lgnRglt) ;
					}
					else
					{
						$ctn .= _parse_pattern($this->FmtEchecConfirmEnvoiMail, $lgnRglt) ;
					}
					$ctn .= '<p><a href="'.$implem->ScriptPanierMembre->ObtientUrlParam(array("id" => $lgnRglt["id_boutique"])).'">'.$this->LibelleLienRetour.'</a></p>' ;
				}
				return $ctn ;
			}
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
			public $ScriptListeRglts ;
			public $ScriptAjoutRglt ;
			public $ScriptModifRglt ;
			public $ScriptSupprRglt ;
			public $ScriptChoixRgltCmd ;
			public $ScriptProcessRgltCmd ;
			public $ScriptEditExpeditCmd ;
			public $FormCmdArticle ;
			protected $MoysPaiemt = array() ;
			public $NomTableBoutique = "boutique_shopping" ;
			public $NomTableCommande = "commande_shopping" ;
			public $NomTableArticle = "article_shopping" ;
			public $NomTableRglt = "reglement_shopping" ;
			public $TitreScriptListeBoutiqs = "Liste des boutiques" ;
			public $TitreScriptAjoutBoutiq = "Ajout de boutique" ;
			public $TitreScriptModifBoutiq = "Modification de boutique" ;
			public $TitreScriptSupprBoutiq = "Suppression de boutique" ;
			public $TitreScriptListeRglts = "Liste des r&egrave;glements" ;
			public $TitreScriptAjoutRglt = "Ajout de r&egrave;glement" ;
			public $TitreScriptModifRglt = "Modification de r&egrave;glement" ;
			public $TitreScriptSupprRglt = "Suppression de r&egrave;glement" ;
			public $TitreScriptPanier = "Votre caddie" ;
			public $TitreScriptChoixRgltCmd = "Choix du moyen de r&egrave;glement" ;
			public $TitreScriptProcessRgltCmd = "Paiement de la commande" ;
			public $TitreScriptEditExpeditCmd = "Coordonn&eacute;es" ;
			public $LibCmdExecFormExped = "Mettre &agrave; jour" ;
			public $MsgSuccesCmdArticle = 'L\'article a &eacute;t&eacute; ajout&eacute; au caddie. <a href="${url_panier}">Acc&eacute;der au panier</a>' ;
			public $MsgPanierVide = 'Vous n\'avez pas encore d\'article command&eacute; dans votre panier.' ;
			public $IdsBoutiqueConsult = array() ;
			public $IdPremBoutiqueConsult = 0 ;
			protected $_IdBoutiqueSelect = 0 ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->InitMoysPaiemt() ;
			}
			protected function InitMoysPaiemt()
			{
				$this->InsereMoyPaiemt(new MoyPaiemtEmailShoppingSws()) ;
			}
			public function IdBoutiqueSelect()
			{
				return $this->_IdBoutiqueSelect ;
			}
			public function ExisteMoyPaiemt($id)
			{
				return (isset($this->MoysPaiemt[$id])) ? 1 : 0 ;
			}
			public function IdsMoysPaiemt()
			{
				return array_keys($this->MoysPaiemt) ;
			}
			public function ObtientMoyPaiemt($id)
			{
				return (isset($this->MoysPaiemt[$id])) ? $this->MoysPaiemt[$id] : null ;
			}
			public function CreeFournDonneesMoysPaiemt()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$fourn->ChargeConfig() ;
				$valeurs = array() ;
				$i = 0 ;
				foreach($this->MoysPaiemt as $i => $moyPaiemt)
				{
					if($moyPaiemt->EstActive() == 0)
					{
						continue ;
					}
					$valMoyPaiemt = array(
						"index" => $i,
						"id" => $moyPaiemt->Id(),
						"titre" => $moyPaiemt->Titre(),
					) ;
					$valeurs[] = $valMoyPaiemt ;
				}
				$fourn->Valeurs["moysPaiemt"] = $valeurs ;
				return $fourn ;
			}
			protected function & InsereMoyPaiemt($moyPaiemt)
			{
				$this->MoysPaiemt[$moyPaiemt->Id()] = & $moyPaiemt ;
				return $moyPaiemt ;
			}
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
				// Boutiques
				$this->ScriptListeBoutiqs = $this->InscritNouvScript("liste_boutiqs_".$this->NomElementSyst, new ScriptListeBoutiqsShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptListeBoutiqs->TitreDocument = $this->TitreScriptListeBoutiqs ;
				$this->ScriptListeBoutiqs->Titre = $this->TitreScriptListeBoutiqs ;
				$this->ScriptAjoutBoutiq = $this->InscritNouvScript("ajout_boutiq_".$this->NomElementSyst, new ScriptAjoutBoutiqShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptAjoutBoutiq->TitreDocument = $this->TitreScriptAjoutBoutiq ;
				$this->ScriptAjoutBoutiq->Titre = $this->TitreScriptAjoutBoutiq ;
				$this->ScriptModifBoutiq = $this->InscritNouvScript("modif_boutiq_".$this->NomElementSyst, new ScriptModifBoutiqShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptModifBoutiq->TitreDocument = $this->TitreScriptModifBoutiq ;
				$this->ScriptModifBoutiq->Titre = $this->TitreScriptModifBoutiq ;
				$this->ScriptListeRglts = $this->InscritNouvScript("liste_rglts_".$this->NomElementSyst, new ScriptListeRgltsShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptListeRglts->TitreDocument = $this->TitreScriptListeRglts ;
				$this->ScriptListeRglts->Titre = $this->TitreScriptListeRglts ;
				$this->ScriptSupprBoutiq = $this->InscritNouvScript("suppr_boutiq_".$this->NomElementSyst, new ScriptSupprBoutiqShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptSupprBoutiq->TitreDocument = $this->TitreScriptSupprBoutiq ;
				$this->ScriptSupprBoutiq->Titre = $this->TitreScriptSupprBoutiq ;
				// Reglements
				$this->ScriptListeRglts = $this->InscritNouvScript("liste_rglts_".$this->NomElementSyst, new ScriptListeRgltsShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptListeRglts->TitreDocument = $this->TitreScriptListeRglts ;
				$this->ScriptListeRglts->Titre = $this->TitreScriptListeRglts ;
				$this->ScriptAjoutRglt = $this->InscritNouvScript("ajout_rglt_".$this->NomElementSyst, new ScriptAjoutRgltShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptAjoutRglt->TitreDocument = $this->TitreScriptAjoutRglt ;
				$this->ScriptAjoutRglt->Titre = $this->TitreScriptAjoutRglt ;
				$this->ScriptModifRglt = $this->InscritNouvScript("modif_rglt_".$this->NomElementSyst, new ScriptModifRgltShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptModifRglt->TitreDocument = $this->TitreScriptModifRglt ;
				$this->ScriptModifRglt->Titre = $this->TitreScriptModifRglt ;
				$this->ScriptListeRglts = $this->InscritNouvScript("liste_rglts_".$this->NomElementSyst, new ScriptListeRgltsShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptListeRglts->TitreDocument = $this->TitreScriptListeRglts ;
				$this->ScriptListeRglts->Titre = $this->TitreScriptListeRglts ;
				$this->ScriptSupprRglt = $this->InscritNouvScript("suppr_rglt_".$this->NomElementSyst, new ScriptSupprRgltShoppingSws(), $zone, $this->ObtientPrivilegesEdit()) ;
				$this->ScriptSupprRglt->TitreDocument = $this->TitreScriptSupprRglt ;
				$this->ScriptSupprRglt->Titre = $this->TitreScriptSupprRglt ;
			}
			protected function CreeScriptVersImprCmd()
			{
				return new ScriptVersImprCmdShoppingSws() ;
			}
			protected function RemplitZonePublValide(& $zone)
			{
				$this->ScriptPanierMembre = $this->InscritNouvScript("panier_".$this->NomElementSyst, new ScriptPanierMembreShoppingSws(), $zone) ;
				$this->ScriptPanierMembre->TitreDocument = $this->TitreScriptPanier ;
				$this->ScriptPanierMembre->Titre = $this->TitreScriptPanier ;
				$this->ScriptChoixRgltCmd = $this->InscritNouvScript("choix_rglt_cmd_".$this->NomElementSyst, new ScriptChoixRgltCmdShoppingSws(), $zone) ;
				$this->ScriptChoixRgltCmd->TitreDocument = $this->TitreScriptChoixRgltCmd ;
				$this->ScriptChoixRgltCmd->Titre = $this->TitreScriptChoixRgltCmd ;
				$this->ScriptProcessRgltCmd = $this->InscritNouvScript("process_rglt_cmd_".$this->NomElementSyst, new ScriptProcessRgltCmdShoppingSws(), $zone) ;
				$this->ScriptProcessRgltCmd->TitreDocument = $this->TitreScriptProcessRgltCmd ;
				$this->ScriptProcessRgltCmd->Titre = $this->TitreScriptProcessRgltCmd ;
				$this->ScriptEditExpeditCmd = $this->InscritNouvScript("edit_expedit_cmd_".$this->NomElementSyst, new ScriptEditExpeditCmdShoppingSws(), $zone) ;
				$this->ScriptEditExpeditCmd->TitreDocument = $this->TitreScriptEditExpeditCmd ;
				$this->ScriptEditExpeditCmd->Titre = $this->TitreScriptEditExpeditCmd ;
				$this->ScriptVersImprCmd = $this->InscritNouvScript("vers_impr_cmd_".$this->NomElementSyst, $this->CreeScriptVersImprCmd(), $zone) ;
			}
			public function CreeCfgAppl()
			{
				return new CfgImplemShoppingSws() ;
			}
			protected function CreeFormCmdArticle()
			{
				return new FormCmdArticleShoppingSws() ;
			}
			protected function InitFormCmdArticle(& $form)
			{
			}
			public function ProduitFormCmdArticle(& $script, $id=0)
			{
				$form = $this->CreeFormCmdArticle() ;
				$form->IdBoutique = (in_array($id, $this->IdsBoutiqueConsult)) ? $id : $this->IdPremBoutiqueConsult ;
				$form->NomImplemPage = $this->NomElementSyst ;
				$this->InitFormCmdArticle($form) ;
				$form->AdopteScript("formCmdArticle", $script) ;
				$form->ChargeConfig() ;
				$this->ChargeFormCmdArticle($form) ;
				return $form ;
			}
			protected function CreeEtatPanier()
			{
				return new CompEtatPanierMembreShoppingSws() ;
			}
			public function ProduitEtatPanier(& $script, $id=0)
			{
				$comp = $this->CreeEtatPanier() ;
				$comp->IdBoutique = (in_array($id, $this->IdsBoutiqueConsult)) ? $id : $this->IdPremBoutiqueConsult ;
				$comp->NomImplemPage = $this->NomElementSyst ;
				$comp->AdopteScript("etatPanier", $script) ;
				$comp->ChargeConfig() ;
				return $comp ;
			}
			protected function ChargeFormCmdArticle(& $form)
			{
			}
			public function ValideConfirmCommande($idCommande, $idRglt)
			{
				$bd = $this->ObtientBDSupport() ;
				$ok = $bd->RunSql("update ".$bd->EscapeTableName($this->NomTableCommande)." set est_confirme=1, date_confirmation=".$bd->SqlNow().", id_reglement=:idRglt where id=:id", array("id" => $idCommande, "idRglt" => $idRglt)) ;
				// print_r($bd) ;
				return $ok ;
			}
			public function ValidePaiementCommande($idCommande)
			{
				$bd = $this->ObtientBDSupport() ;
				return $bd->RunSql("update ".$bd->EscapeTableName($this->NomTableCommande)." set est_paye=1, date_paiement=".$bd->SqlNow()." where id=:id", array("id" => $idCommande)) ;
			}
			public function LgnsDetailsCommande($idCommande)
			{
				$bd = $this->ObtientBDSupport() ;
				$sql = "select t1.nom_client, t1.prenom_client, t1.email_client, t1.bp_client, t1.adresse_client, t1.contact_client, t1.extra1_client, t1.extra2_client, t1.extra3_client, t1.extra4_client, t1.client_aussi_dest, t1.nom_dest, t1.prenom_dest, t1.email_dest, t1.bp_dest, t1.adresse_dest, t1.contact_dest, t1.extra1_dest, t1.extra2_dest, t1.extra3_dest, t1.extra4_dest, t2.*,
	t2.prix * t2.quantite montant
from ".$bd->EscapeTableName($this->NomTableCommande)." t1
inner join ".$bd->EscapeTableName($this->NomTableArticle)." t2 on t1.id = t2.id_commande
where id_commande=:id" ;
				return $bd->FetchSqlRows($sql, array("id" => $idCommande)) ;
			}
			public function RenduDetailsCommande(& $lgnsArticle, & $form)
			{
				$ctn = '' ;
				$totalTTC = 0 ;
				$ctn = '' ;
				$editable = $form->Editable ;
				$inclureEntete = $form->InclureEnteteCommande ;
				$inclureLienVersImpr = $form->InclureLienImprCommande ;
				if(count($lgnsArticle) > 0)
				{
					$lgnCommande = & $lgnsArticle[0] ;
					$ctn .= '<h2 align="right">Commande N&deg; '.$lgnCommande["id_commande"].'</h2>'.PHP_EOL ;
				}
				if($inclureLienVersImpr == 1)
				{
					$ctn .= '<p>&bull; <a href="'.$this->ScriptVersImprCmd->ObtientUrlParam(array("id" => $this->IdBoutiqueSelect())).'" target="_blank">Imprimer</a></p>'.PHP_EOL ;
				}
				// print_r($lgnsArticle) ;
				if($inclureEntete == 1)
				{
					$ctn .= '<table width="100%" cellspacing="0" cellpadding="4" class="DetailsCommande">
<tr>
<td width="50%" valign="left" valign="top">'.PHP_EOL ;
					$ctn .= '<h3>Livrer &agrave;</h3>'.PHP_EOL ;
					$ctn .= '<h4>'.htmlentities($lgnCommande["nom_dest"]).' '.htmlentities($lgnCommande["prenom_dest"]).'</h4>'.PHP_EOL ;
					$ctn .= '<div>'.htmlentities($lgnCommande["adresse_dest"]).'</div>'.PHP_EOL ;
					$ctn .= '<div>'.htmlentities($lgnCommande["contact_dest"]).'</div>'.PHP_EOL ;
					$ctn .= '<div>'.htmlentities($lgnCommande["email_dest"]).'</div>'.PHP_EOL ;
					$ctn .= '</td>
<td width="50%" valign="right">'.PHP_EOL ;
					$ctn .= '<h3>Client factur&eacute;</h3>'.PHP_EOL ;
					$ctn .= '<h4>'.htmlentities($lgnCommande["nom_client"]).' '.htmlentities($lgnCommande["prenom_client"]).'</h4>'.PHP_EOL ;
					$ctn .= '<div>'.htmlentities($lgnCommande["adresse_client"]).'</div>'.PHP_EOL ;
					$ctn .= '<div>'.htmlentities($lgnCommande["contact_client"]).'</div>'.PHP_EOL ;
					$ctn .= '<div>'.htmlentities($lgnCommande["email_client"]).'</div>'.PHP_EOL ;
					$ctn .= '</td>
</tr>
</table>
<p>&nbsp;</p>' ;
				}
				$ctn .= '<table width="100%" cellspacing="0" cellpadding="4" class="DetailsCommande">'.PHP_EOL ;
				$ctn .= '<tr>' ;
				if($editable == 1)
				{
					$ctn .= '<th width="7%">Supprimer ?</th>' ;
				}
				$ctn .= '<th width="*">Article</th>
<th width="20%">PU</th>
<th width="14%">Quantit&eacute;</th>
<th width="20%">Montant</th>
</tr>'.PHP_EOL ;
				foreach($lgnsArticle as $i => $lgn)
				{
					$prix = intval($lgn["prix"]) ;
					$quantite = intval($lgn["quantite"]) ;
					$montant = intval($lgn["prix"]) * intval($lgn["quantite"]) ;
					$totalTTC += $montant ;
					$ctn .= '<tr>' ;
					if($editable == 1)
					{
						$ctn .= '<td align="center"><input type="checkbox" name="suppr_'.htmlspecialchars($lgn["id"]).'" value="1" /></td>' ;
					}
					$ctn .= '<td>'.htmlentities($lgn["titre_entite"]).'</td>' ;
					$ctn .= '<td align="right">'.htmlentities($prix).'</td>' ;
					$ctn .= '<td align="center">'.(($editable == 1) ? '<input type="text" name="qte_'.htmlspecialchars($lgn["id"]).'" value="'.htmlspecialchars($quantite).'" style="width:50px" />' : intval($quantite)).'</td>' ;
					$ctn .= '<td align="right">'.$montant.'</td>
</tr>'.PHP_EOL ;
				}
				$ctn .= '<tr>
	<th colspan="'.(($editable == 1) ? 4 : 3).'" align="left">Total TTC</th>
	<th align="right">'.$totalTTC.'</th>
</tr>'.PHP_EOL ;
				$ctn .= '</table>' ;
				return $ctn ;
			}
			public function PrepareScriptConsult(& $script, & $entite)
			{
				$bd = $this->ObtientBDSupport() ;
				$lgns = $bd->FetchSqlRows("select id from ".$bd->EscapeTableName($this->NomTableBoutique)." where statut_publication=1") ;
				foreach($lgns as $i => $lgn)
				{
					$this->IdsBoutiqueConsult[] = $lgn["id"] ;
				}
				$this->IdPremBoutiqueConsult = (count($this->IdsBoutiqueConsult) > 0) ? $this->IdsBoutiqueConsult[0] : 0 ;
			}
			public function IdCommandeMembreConnecte($idBoutique, & $zone)
			{
				$idCommande = 0 ;
				$idSession = session_id() ;
				// echo $idSession ;
				$bd = $this->ObtientBDSupport() ;
				$paramsCmd = array(
					"idSession" => $idSession,
					"idMembre" => $zone->IdMembreConnecte(),
					"membreConnecte" => ($zone->PossedeMembreConnecte()) ? 1 : 0,
					"idBoutique" => $idBoutique,
				) ;
				$sqlCmd = "select t1.id_boutique, t2.id, t2.id_session from
(select id id_boutique from ".$bd->EscapeTableName($this->NomTableBoutique)." where id=:idBoutique) t1
left join (select * from ".$bd->EscapeTableName($this->NomTableCommande)." where est_confirme=0 and ((id_membre = :idMembre and :membreConnecte = 1) or (:membreConnecte = 0 and id_session=:idSession))) t2
on t1.id_boutique = t2.id_boutique" ;
				$lgnCommande = $bd->FetchSqlRow($sqlCmd, $paramsCmd) ;
				if(count($lgnCommande) > 0 && $lgnCommande["id"] == "")
				{
					$ok = $bd->InsertRow(
						$this->NomTableCommande,
						array(
							"id_boutique" => $idBoutique,
							"id_session" => $idSession,
							"id_membre" => $zone->IdMembreConnecte(),
						)
					) ;
					if($ok)
					{
						$idCommande = $bd->FetchSqlValue($sqlCmd, $paramsCmd, "id", 0) ;
					}
				}
				elseif(count($lgnCommande) > 0)
				{
					$idCommande = $lgnCommande["id"] ;
					if($lgnCommande["id_session"] != $idSession)
					{
						$ok = $bd->UpdateRow(
							$this->NomTableCommande,
							array("id_session" => $idSession),
							"id = :idCommande",
							array("idCommande" => $idCommande)
						) ;
					}
				}
				$this->_IdBoutiqueSelect = $idBoutique ;
				return $idCommande ;
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
				$this->LienModif = $implem->InsereIconeActionModif($this->TablPrinc, $this->DefColActs, $implem->ScriptModifBoutiq->ObtientUrlFmt(array("id" => '${id}'))) ;
				$this->LienRglts = $implem->InsereIconeAction($this->TablPrinc, $this->DefColActs, $implem->ScriptListeRglts->ObtientUrlFmt(array("id" => '${id}')), "images/icones/reglements-boutique.png", "Règlements") ;
				$this->LienSuppr = $implem->InsereIconeActionSuppr($this->TablPrinc, $this->DefColActs, $implem->ScriptSupprBoutiq->ObtientUrlFmt(array("id" => '${id}'))) ;
				$this->TablPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->TablPrinc->FournisseurDonnees->RequeteSelection = $implem->NomTableBoutique ;
				$this->CmdAjout = $implem->InsereCmdAjoutTabl($this->TablPrinc, $implem->ScriptAjoutBoutiq->ObtientUrl()) ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->TablPrinc->RenduDispositif() ;
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
				$this->CompContact->Largeur = "270px" ;
				$this->FltEmail = $this->FormPrinc->InsereFltEditHttpPost("email", "email") ;
				$this->FltEmail->Libelle = "Email" ;
				$this->CompEmail = $this->FltEmail->ObtientComposant() ;
				$this->CompEmail->Largeur = "240px" ;
				$this->FltBp = $this->FormPrinc->InsereFltEditHttpPost("bp", "bp") ;
				$this->FltBp->Libelle = "Boite postale" ;
				$this->CompBp = $this->FltBp->ObtientComposant() ;
				$this->CompBp->Largeur = "240px" ;
				$this->FormPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->FormPrinc->FournisseurDonnees->RequeteSelection = $implem->NomTableBoutique ;
				$this->FormPrinc->FournisseurDonnees->TableEdition = $implem->NomTableBoutique ;
				$this->FormPrinc->RedirigeAnnulerVersUrl($implem->ScriptListeBoutiqs->ObtientUrl()) ;
				$this->ChargeFormPrinc() ;
			}
			protected function ChargeFormPrinc()
			{
			}
			protected function InitFormPrinc()
			{
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->FormPrinc->RenduDispositif() ;
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
		class ScriptSupprBoutiqShoppingSws extends ScriptModifBoutiqShoppingSws
		{
			protected function InitFormPrinc()
			{
				parent::InitFormPrinc() ;
				$this->FormPrinc->Editable = 0 ;
				$this->FormPrinc->NomClasseCommandeExecuter = "PvCommandeSupprElement" ;
				$this->FormPrinc->LibelleCommandeExecuter = "Supprimer" ;
			}
		}
		
		class ScriptCompositBoutiqShoppingSws extends ScriptEditBoutiqShoppingSws
		{
			protected function InitFormPrinc()
			{
				parent::InitFormPrinc() ;
				$this->FormPrinc->InclureElementEnCours = 1 ;
				$this->FormPrinc->InclureTotalElements = 1 ;
				$this->FormPrinc->Editable = 0 ;
				$this->FormPrinc->LibelleCommandeExecuter = "Modifier" ;
			}
			protected function ChargeFormPrinc()
			{
				$this->FltAdr->Invisible = 1 ;
				$this->FltEmail->Invisible = 1 ;
				$this->FltContact->Invisible = 1 ;
				$this->FltBp->Invisible = 1 ;
			}
		}
		class ScriptLstCompositBoutiqShoppingSws extends ScriptCompositBoutiqShoppingSws
		{
			protected $TablSecond ;
			protected $DefColActs ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineTablSecond() ;
			}
			protected function DetermineTablSecond()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->TablSecond = new TableauDonneesAdminSws() ;
				$this->TablSecond->ToujoursAfficher = 1 ;
				$this->InitTablSecond() ;
				$this->TablSecond->AdopteScript("tablSecond", $this) ;
				$this->TablSecond->ChargeConfig() ;
				$this->TablSecond->FournisseurDonnees = $implem->CreeFournDonnees() ;
				$this->FltIdBoutiq = $this->TablSecond->InsereFltSelectHttpGet("id", "id_boutique = <self>") ;
				$this->FltIdBoutiq->Obligatoire = 1 ;
				$this->FltIdBoutiq->LectureSeule = 1 ;
				$this->ChargeTablSecond() ;
				$this->DefColActs = $this->TablSecond->InsereDefColActions("Actions") ;
				$this->ChargeLiensActsTablSecond() ;
			}
			protected function InitTablSecond()
			{
			}
			protected function ChargeTablSecond()
			{
			}
			protected function ChargeLiensActsTablSecond()
			{
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= parent::RenduSpecifique() ;
				if($this->FormPrinc->ElementEnCoursTrouve)
				{
					$ctn .= $this->TablSecond->RenduDispositif() ;
					// print_r($this->TablSecond->FournisseurDonnees) ;
				}
				return $ctn ;
			}
		}
		
		class ScriptEtapeRgltBaseShoppingSws extends ScriptBaseSws
		{
			public $IdCommande = 0 ;
			public $AfficherCommande = 1 ;
			public $FormPrinc = null ;
			public $FormPrincEditable = 0 ;
			public $DoitContenirArticle = 1 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineFormPanier() ;
			}
			protected function DetermineFormPanier()
			{
				$this->FormPrinc = new FormPanierMembreShoppingSws() ;
				$this->FormPrinc->Editable = $this->FormPrincEditable ;
				$this->FormPrinc->AdopteScript("formPrinc", $this) ;
				$this->FormPrinc->ChargeConfig() ;
			}
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				if(! $ok)
				{
					return false ;
				}
				$this->DetermineIdCommande() ;
				if($this->IdCommande == 0)
				{
					return false ;
				}
				if($this->DoitContenirArticle == 1)
				{
					$implem = $this->ObtientImplemPage() ;
					$bd = $this->ObtientBDSupport() ;
					$total = $bd->FetchSqlValue("select count(0) tt from ".$bd->EscapeTableName($implem->NomTableArticle)." where id_commande=:id", array("id" => $this->IdCommande), "tt", 0) ;
					return $total > 0 ;
				}
				return true ;
			}
			protected function DetermineIdCommande()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->IdCommande = $implem->IdCommandeMembreConnecte(_GET_def("id"), $this->ZoneParent) ;
			}
			public function RenduSpecifique()
			{
				$ctn = "" ;
				if($this->AfficherCommande == 1)
				{
					$ctn .= '<br />' ;
					$ctnForm = $this->FormPrinc->RenduDispositif() ;
					if($this->FormPrinc->PossedeArticles())
					{
						$ctn .= $ctnForm ;
					}
					else
					{
						$implem = $this->ObtientImplemPage() ;
						$ctn .= $implem->MsgPanierVide ;
					}
				}
				return $ctn ;
			}
		}
		class ScriptPanierMembreShoppingSws extends ScriptEtapeRgltBaseShoppingSws
		{
			public $FormPrincEditable = 1 ;
			public $DoitContenirArticle = 0 ;
		}
		class ScriptChoixRgltCmdShoppingSws extends ScriptEtapeRgltBaseShoppingSws
		{
			public $MsgDescChoixRglt = '<p>Veuillez choisir un moyen de paiement</p>' ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->FormPrinc->InclureEnteteCommande = 1 ;
				$this->FormPrinc->InclureLienImprCommande = 1 ;
			}
			protected function RenduBlocRglts()
			{
				$ctn = "" ;
				$implem = $this->ObtientImplemPage() ;
				$bd = $this->ObtientBDSupport() ;
				$lgns = $bd->FetchSqlRows("select * from ".$bd->EscapeTableName($implem->NomTableRglt)." where id_boutique=:idBoutique", array("idBoutique" => _GET_def("id"))) ;
				$ctn .= '<br>' ;
				$ctn .= $this->MsgDescChoixRglt. PHP_EOL ;
				foreach($lgns as $i => $lgn)
				{
					$ctn .= '<p><a href="'.htmlspecialchars($implem->ScriptProcessRgltCmd->ObtientUrlParam(array("id" => $lgn["id"]))).'">'.$lgn["titre"].'</a></p>'.PHP_EOL ;
				}
				return $ctn ;
			}
			public function RenduSpecifique()
			{
				$ctn = parent::RenduSpecifique() ;
				$ctn .= $this->RenduBlocRglts() ;
				return $ctn ;
			}
		}
		class ScriptEditExpeditCmdShoppingSws extends ScriptEtapeRgltBaseShoppingSws
		{
			protected $Form2 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineForm2() ;
			}
			protected function DetermineForm2()
			{
				$implem = $this->ObtientImplemPage() ;
				$bd = $this->ObtientBDSupport() ;
				$lgnMembre = $this->ZoneParent->ObtientMembreConnecte() ;
				$possedeMembreCnx = $this->ZoneParent->ObtientMembreConnecte() ;
				$this->Form2 = new FormDonneesBaseImplemShoppingSws() ;
				$this->Form2->InclureElementEnCours = 1 ;
				$this->Form2->InclureTotalElements = 1 ;
				$this->Form2->NomClasseCommandeExecuter = "CmdModifExpeditCmdShoppingSws" ;
				$this->Form2->LibelleCommandeExecuter = $implem->LibCmdExecFormExped ;
				$this->Form2->AdopteScript("form2", $this) ;
				$this->Form2->ChargeConfig() ;
				$this->Form2->DessinateurFiltresEdition = new DessinFltEditExpedShoppingSws() ;
				$this->FltID = $this->Form2->InsereFltLgSelectFixe("id", $this->IdCommande, "id = <self>") ;
				$this->FltID->Obligatoire = 1 ;
				$this->FltIdMembre = $this->Form2->InsereFltEditFixe("id_membre", $this->ZoneParent->IdMembreConnecte(), "id_membre") ;
				$this->FltIdSession = $this->Form2->InsereFltEditFixe("id_session", session_id(), "id_session") ;
				// Source
				$this->FltNomClt = $this->Form2->InsereFltEditHttpPost("nom_client", "nom_client") ;
				$this->FltNomClt->Libelle = "Nom" ;
				$this->FltPrenomClt = $this->Form2->InsereFltEditHttpPost("prenom_client", "prenom_client") ;
				$this->FltPrenomClt->Libelle = "Prenom" ;
				$this->CompPrenomClt = $this->FltPrenomClt->ObtientComposant() ;
				$this->CompPrenomClt->Largeur = "225px" ;
				$this->FltEmailClt = $this->Form2->InsereFltEditHttpPost("email_client", "email_client") ;
				$this->FltEmailClt->Libelle = "Email" ;
				$this->CompEmailClt = $this->FltEmailClt->ObtientComposant() ;
				$this->CompEmailClt->Largeur = "300px" ;
				$this->FltBpClt = $this->Form2->InsereFltEditHttpPost("bp_client", "bp_client") ;
				$this->FltBpClt->Libelle = "Boite Postale" ;
				$this->CompBpClt = $this->FltBpClt->ObtientComposant() ;
				$this->CompBpClt->Largeur = "225px" ;
				$this->FltAdrClt = $this->Form2->InsereFltEditHttpPost("adresse_client", "adresse_client") ;
				$this->FltAdrClt->Libelle = "Adresse" ;
				$this->CompAdrClt = $this->FltAdrClt->DeclareComposant("PvZoneMultiligneHtml") ;
				$this->CompAdrClt->TotalColonnes = "40" ;
				$this->CompAdrClt->TotalLignes = "4" ;
				$this->FltContactClt = $this->Form2->InsereFltEditHttpPost("contact_client", "contact_client") ;
				$this->FltContactClt->Libelle = "Contact" ;
				$this->CompContactClt = $this->FltContactClt->ObtientComposant() ;
				$this->CompContactClt->Largeur = "180px" ;
				// Destinataire
				$this->FltNomDest = $this->Form2->InsereFltEditHttpPost("nom_dest", "nom_dest") ;
				$this->FltNomDest->Libelle = "Nom" ;
				$this->FltPrenomDest = $this->Form2->InsereFltEditHttpPost("prenom_dest", "prenom_dest") ;
				$this->FltPrenomDest->Libelle = "Prenom" ;
				$this->CompPrenomDest = $this->FltPrenomDest->ObtientComposant() ;
				$this->CompPrenomDest->Largeur = "225px" ;
				$this->FltEmailDest = $this->Form2->InsereFltEditHttpPost("email_dest", "email_dest") ;
				$this->FltEmailDest->Libelle = "Email" ;
				$this->CompEmailDest = $this->FltEmailDest->ObtientComposant() ;
				$this->CompEmailDest->Largeur = "300px" ;
				$this->FltBpDest = $this->Form2->InsereFltEditHttpPost("bp_dest", "bp_dest") ;
				$this->FltBpDest->Libelle = "Boite Postale" ;
				$this->CompBpDest = $this->FltBpDest->ObtientComposant() ;
				$this->CompBpDest->Largeur = "225px" ;
				$this->FltAdrDest = $this->Form2->InsereFltEditHttpPost("adresse_dest", "adresse_dest") ;
				$this->FltAdrDest->Libelle = "Adresse" ;
				$this->CompAdrDest = $this->FltAdrDest->DeclareComposant("PvZoneMultiligneHtml") ;
				$this->CompAdrDest->TotalColonnes = "40" ;
				$this->CompAdrDest->TotalLignes = "4" ;
				$this->FltContactDest = $this->Form2->InsereFltEditHttpPost("contact_dest", "contact_dest") ;
				$this->FltContactDest->Libelle = "Contact" ;
				$this->CompContactDest = $this->FltContactDest->ObtientComposant() ;
				$this->CompContactDest->Largeur = "180px" ;
				if($this->ZoneParent->PossedeMembreConnecte())
				{
					$this->FltNomClt->ValeurParDefaut = $lgnMembre->RawData["MEMBER_LAST_NAME"] ;
					$this->FltPrenomClt->ValeurParDefaut = $lgnMembre->RawData["MEMBER_FIRST_NAME"] ;
					$this->FltEmailClt->ValeurParDefaut = $lgnMembre->RawData["MEMBER_EMAIL"] ;
					$this->FltAdrClt->ValeurParDefaut = $lgnMembre->RawData["MEMBER_ADDRESS"] ;
					$this->FltContactClt->ValeurParDefaut = $lgnMembre->RawData["MEMBER_CONTACT"] ;
				}
				$this->Form2->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->Form2->FournisseurDonnees->RequeteSelection = $implem->NomTableCommande ;
				$this->Form2->FournisseurDonnees->TableEdition = $implem->NomTableCommande ;
				$this->Form2->RedirigeAnnulerVersUrl($implem->ScriptPanierMembre->ObtientUrlParam(array("id" => _GET_def("id")))) ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= parent::RenduSpecifique() ;
				if($this->IdCommande > 0)
				{
					$ctn .= $this->Form2->RenduDispositif() ;
				}
				return $ctn ;
			}
		}
		class ScriptVersImprCmdShoppingSws extends ScriptEtapeRgltBaseShoppingSws
		{
			public $UtiliserCorpsDocZone = 0 ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->FormPrinc->InclureEnteteCommande = 1 ;
				$this->DetermineTitreScript() ;
			}
			protected function DetermineTitreScript()
			{
				if($this->IdCommande != 0)
				{
					// $this->Titre = "Commande N&deg;".$this->IdCommande ;
					$this->TitreDocument = "Facture N&deg;".$this->IdCommande ;
					
				}
			}
			protected function RenduEnteteCmd()
			{
				$ctn = '' ;
				return $ctn ;
			}
			protected function RenduPiedCmd()
			{
				$ctn = '' ;
				return $ctn ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<div class="Imprimable">'.PHP_EOL ;
				$ctn .= $this->RenduEnteteCmd().PHP_EOL ;
				$ctn .= parent::RenduSpecifique().PHP_EOL ;
				$ctn .= $this->RenduPiedCmd().PHP_EOL ;
				$ctn .= '</div>' ;
				$ctn .= '<script type="text/javascript">
	window.onload = function() {
		window.print() ;
	} ;
</script>' ;
				return $ctn ;
			}
		}
		class ScriptProcessRgltCmdShoppingSws extends ScriptEtapeRgltBaseShoppingSws
		{
			protected $LgnRglt ;
			protected $MoyPaiemtSelect ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->FormPrinc->InclureEnteteCommande = 1 ;
			}
			protected function DetermineIdCommande()
			{
				$implem = $this->ObtientImplemPage() ;
				$bd = $this->ObtientBDSupport() ;
				$this->LgnRglt = $bd->FetchSqlRow(
					"select * from ".$bd->EscapeTableName($implem->NomTableRglt)." where id = :id",
					array("id" => _GET_def("id"))
				) ;
				$this->MoyPaiemtSelect = null ;
				if(is_array($this->LgnRglt) && count($this->LgnRglt) > 0)
				{
					$this->MoyPaiemtSelect = $implem->ObtientMoyPaiemt($this->LgnRglt["moyen_paiement"]) ;
					$this->IdCommande = $implem->IdCommandeMembreConnecte($this->LgnRglt["id_boutique"], $this->ZoneParent) ;
					if($this->IdCommande > 0)
					{
						$this->MoyPaiemtSelect->PrepareScriptRgltCmd($implem, $this, $this->LgnRglt) ;
					}
				}
			}
			public function RenduSpecifique()
			{
				$implem = $this->ObtientImplemPage() ;
				$ctn = parent::RenduSpecifique() ;
				if($this->IdCommande > 0)
				{
					$ctn .= $this->MoyPaiemtSelect->RenduScriptRgltCmd($implem, $this, $this->LgnRglt) ;
				}
				return $ctn ;
			}
		}
		
		class ScriptListeRgltsShoppingSws extends ScriptLstCompositBoutiqShoppingSws
		{
			protected function ChargeTablSecond()
			{
				parent::ChargeTablSecond() ;
				$implem = $this->ObtientImplemPage() ;
				$this->DefColId = $this->TablSecond->InsereDefColCachee("id") ;
				$this->DefColTitre = $this->TablSecond->InsereDefCol("titre", "Titre") ;
				$this->CmdAjout = $implem->InsereCmdAjoutTabl($this->TablSecond, $implem->ScriptAjoutRglt->ObtientUrl()) ;
				$this->CmdAjout->Parametres["id_boutique"] = $this->FltID->Lie() ;
				$this->TablSecond->FournisseurDonnees->RequeteSelection = $implem->NomTableRglt ;
			}
			protected function ChargeLiensActsTablSecond()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->LienModif = $implem->InsereIconeActionModif($this->TablSecond, $this->DefColActs, $implem->ScriptModifRglt->ObtientUrlFmt(array("id" => '${id}')));
				$this->LienSuppr = $implem->InsereIconeActionSuppr($this->TablSecond, $this->DefColActs, $implem->ScriptSupprRglt->ObtientUrlFmt(array("id" => '${id}')));
			}
		}
		
		class ScriptEditRgltShoppingSws extends ScriptAdminImplemBaseSws
		{
			protected $FormPrinc ;
			protected $ContextFormPrinc = array() ;
			protected $MoyPaiemtSelect ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineFormPrinc() ;
				$this->DetermineFormMoysPaiemt() ;
			}
			protected function DetermineContextFormPrinc()
			{
				$implem = $this->ObtientImplemPage() ;
				$bd = $this->ObtientBDSupport() ;
				$texteSql = "" ;
				$idRech = 0 ;
				if($this->FormPrinc->InclureElementEnCours == 1)
				{
					$texteSql = 'select t2.id id_boutique, t1.moyen_paiement from '.$bd->EscapeTableName($implem->NomTableRglt).' t1 left join '.$bd->EscapeTableName($implem->NomTableBoutique).' t2 on t1.id_boutique = t2.id where t1.id = :id' ;
					$idRech = _GET_def("id") ;
				}
				else
				{
					$texteSql = 'select t2.id id_boutique, \'\' moyen_paiement from '.$bd->EscapeTableName($implem->NomTableBoutique).' t2 where t2.id = :id' ;
					$idRech = _GET_def("id_boutique") ;
				}
				$this->ContextFormPrinc = $bd->FetchSqlRow($texteSql, array("id" => $idRech)) ;
				// print_r($bd) ;
				if($this->FormPrinc->InclureElementEnCours == 0)
				{
					$this->ContextFormPrinc["moyen_paiement"] = _GET_def("moyen_paiement") ;
				}
				if($this->ContextFormPrinc["moyen_paiement"] == "" || ! $implem->ExisteMoyPaiemt($this->ContextFormPrinc["moyen_paiement"]))
				{
					$idsMoyPaimt = $implem->IdsMoysPaiemt() ;
					$this->ContextFormPrinc["moyen_paiement"] = $idsMoyPaimt[0] ;
				}
				$this->MoyPaiemtSelect = $implem->ObtientMoyPaiemt($this->ContextFormPrinc["moyen_paiement"]) ;
			}
			protected function DetermineFormPrinc()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->FormPrinc = new PvFormulaireDonneesHTML() ;
				$this->InitFormPrinc() ;
				$this->FormPrinc->AdopteScript("formPrinc", $this) ;
				$this->FormPrinc->ChargeConfig() ;
				$this->DetermineContextFormPrinc() ;
				$this->FormPrinc->MaxFiltresEditionParLigne = 1 ;
				$this->FltIdBoutiq = $this->FormPrinc->InsereFltEditHttpPost("id_boutique", "id_boutique") ;
				$this->FltIdBoutiq->Libelle = "Boutique" ;
				$this->CompIdBoutiq = $this->FltIdBoutiq->DeclareComposant("PvZoneCorrespHtml") ;
				$this->CompIdBoutiq->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->CompIdBoutiq->FournisseurDonnees->RequeteSelection = $implem->NomTableBoutique ;
				$this->CompIdBoutiq->NomColonneValeur = "id" ;
				$this->CompIdBoutiq->NomColonneLibelle = "titre" ;
				$this->FltIdBoutiq->ValeurParDefaut = $this->ContextFormPrinc["id_boutique"] ;
				$this->FltID = $this->FormPrinc->InsereFltLgSelectHttpGet("id", "id = <self>") ;
				$this->FltTitre = $this->FormPrinc->InsereFltEditHttpPost("titre", "titre") ;
				$this->FltTitre->Libelle = "Titre" ;
				$this->CompTitre = $this->FltTitre->ObtientComposant() ;
				$this->CompTitre->Largeur = "270px" ;
				$this->FltStatutPubl = $this->FormPrinc->InsereFltEditHttpPost("statut_publication", "statut_publication") ;
				$this->FltStatutPubl->Libelle = "Publi&eacute;" ;
				$this->FltStatutPubl->DeclareComposant("PvZoneSelectBoolHtml") ;
				$this->FltMoyPaiemt = $this->FormPrinc->InsereFltEditHttpPost("moyen_paiement", "moyen_paiement") ;
				$this->FltMoyPaiemt->ValeurParDefaut = $this->ContextFormPrinc["moyen_paiement"] ;
				$this->FltMoyPaiemt->Libelle = "Moyen paiement" ;
				$this->CompMoyPaimt = $this->FltMoyPaiemt->DeclareComposant("PvZoneEtiquetteHtml") ;
				$this->CompMoyPaimt->Libelle = $this->MoyPaiemtSelect->Titre() ;
				$this->FormPrinc->FournisseurDonnees = $this->CreeFournDonnees() ;
				$this->FormPrinc->FournisseurDonnees->RequeteSelection = $implem->NomTableRglt ;
				$this->FormPrinc->FournisseurDonnees->TableEdition = $implem->NomTableRglt ;
				$url = $implem->ScriptListeRglts->ObtientUrlParam(array("id" => $this->ContextFormPrinc["id_boutique"])) ;
				$this->FormPrinc->RedirigeAnnulerVersUrl($url) ;
				$this->ChargeFormPrinc() ;
				$this->MoyPaiemtSelect->RemplitFormEditRglt($this->FormPrinc) ;
			}
			protected function DetermineFormMoysPaiemt()
			{
				$implem = $this->ObtientImplemPage() ;
				$this->Form2 = new PvFormulaireDonneesHtml() ;
				$this->Form2->InclureElementEnCours = 0 ;
				$this->Form2->DispositionComposants = array(4, 1, 2) ;
				$this->Form2->InclureTotalElements = 0 ;
				$this->Form2->InscrireCommandeAnnuler = 0 ;
				$this->Form2->LibelleCommandeExecuter = "Changer" ;
				$this->Form2->AdopteScript("form2", $this) ;
				$this->Form2->ChargeConfig() ;
				$this->FltMoyPaiemt2 = $this->Form2->InsereFltEditHttpGet("moyen_paiement", "moyen_paiement") ;
				$this->FltMoyPaiemt2->Libelle = "Moyen de paiement" ;
				$this->CompMoyPaimt2 = $this->FltMoyPaiemt2->DeclareComposant("PvZoneBoiteSelectHtml") ;
				$this->CompMoyPaimt2->FournisseurDonnees = $implem->CreeFournDonneesMoysPaiemt() ;
				$this->CompMoyPaimt2->NomColonneValeur = "id" ;
				$this->CompMoyPaimt2->NomColonneLibelle = "titre" ;
				$this->FltIdBoutiq2 = $this->Form2->InsereFltEditHttpGet("id_boutique", "id_boutique") ;
				$this->FltIdBoutiq2->LectureSeule = 1 ;
			}
			protected function ChargeFormPrinc()
			{
			}
			protected function InitFormPrinc()
			{
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				if(count($this->ContextFormPrinc) > 0)
				{
					if($this->FormPrinc->InclureElementEnCours == 0)
					{
						$ctn .= $this->Form2->RenduDispositif() ;
						$ctn .= '<br />' ;
					}
					$ctn .= $this->FormPrinc->RenduDispositif() ;
				}
				else
				{
					$ctn .= '<div class="Erreur">La boutique demand&eacute;e n\'existe pas</div>' ;
				}
				return $ctn ;
			}
		}
		class ScriptAjoutRgltShoppingSws extends ScriptEditRgltShoppingSws
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
		class ScriptModifRgltShoppingSws extends ScriptEditRgltShoppingSws
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
		class ScriptSupprRgltShoppingSws extends ScriptModifRgltShoppingSws
		{
			protected function InitFormPrinc()
			{
				parent::InitFormPrinc() ;
				$this->FormPrinc->Editable = 0 ;
				$this->FormPrinc->NomClasseCommandeExecuter = "PvCommandeSupprElement" ;
				$this->FormPrinc->LibelleCommandeExecuter = "Supprimer" ;
				$this->FormPrinc->CacherFormulaireFiltresApresCmd = 1 ;
			}
		}
		
		class CompEtatPanierMembreShoppingSws extends ComposantIUBaseSws
		{
			public $CheminIcone = "" ;
			public $LibPanier = "Mon Panier" ;
			public $LibUnite = "article(s)" ;
			public $IdBoutique = 0 ;
			public $HauteurIcone = 14 ;
			public $MsgCommandeNonTrouvee = "Pas d'article command&eacute;" ;
			protected function RenduDispositifBrut()
			{
				$implem = ImplemPageBaseSws::ObtientImplemPageComp($this) ;
				$bd = $implem->ObtientBDSupport() ;
				$idCmd = $implem->IdCommandeMembreConnecte($this->IdBoutique, $this->ZoneParent) ;
				$ctn = '' ;
				if($idCmd > 0)
				{
					$total = $bd->FetchSqlValue("select count(0) tt from ".$bd->EscapeTableName($implem->NomTableArticle)." where id_commande=:idCmd", array("idCmd" => $idCmd), "tt", 0) ;
					if($this->CheminIcone != "")
					{
						$ctn .= '<img src="'.$this->CheminIcone.'" height="'.$this->HauteurIcone.'" border="0" /> ' ;
					}
					if($this->LibPanier != "")
					{
						$ctn .= $this->LibPanier.' : ' ;
					}
					$ctn .= '<a class="EtatPanier" id="'.$this->IDInstanceCalc.'" href="'.htmlspecialchars($implem->ScriptPanierMembre->ObtientUrlParam(array("id" => $this->IdBoutique))).'">' ;
					$ctn .= $total ;
					if($this->LibUnite != "")
					{
						$ctn .= " ".$this->LibUnite ;
					}
					$ctn .= '</a>' ;
				}
				else
				{
					$ctn .= $this->MsgCommandeNonTrouvee ;
				}
				return $ctn ;
			}
		}
		
		class FormCmdArticleShoppingSws extends FormDonneesBaseImplemShoppingSws
		{
			public $NomImplemPage = "shopping" ;
			public $InclureTotalElements = 0 ;
			public $InclureElementEnCours = 0 ;
			public $MaxFiltresEditionParLigne = 1 ;
			public $InscrireCommandeAnnuler = 0 ;
			public $LibelleCommandeExecuter = "Ajouter au panier" ;
			public $NomClasseCommandeExecuter = "CmdValidCmdArtShoppingSws" ;
			public $FltStatutPubl ;
			public $FltId ;
			public $FltIdEntite ;
			public $FltTitreEntite ;
			public $FltIdCtrl ;
			public $FltQte ;
			protected $ActionSupport ;
			// public $NomClasseCommandeExecuter = "PvCommandeActionNotification" ;
			protected function ChargeActionSupport()
			{
				$this->ActionSupport = $this->InsereActionAvantRendu("valid_cmd_article", new ActValidCmdArticleShoppingSws()) ;
			}
			public function ChargeConfig()
			{
				$this->ChargeActionSupport() ;
				parent::ChargeConfig() ;
				$implem = $this->ObtientImplemPage() ;
				$entite = $this->ScriptParent->ObtientEntitePage() ;
				// Fournisseur de donnees
				$this->FournisseurDonnees = $this->ScriptParent->CreeFournDonnees() ;
				$this->FournisseurDonnees->RequeteSelection = $implem->NomTable ;
				$this->FournisseurDonnees->TableEdition = $implem->NomTable ;
				// Criteres validation
				$this->CommandeExecuter->ActionSupport = & $this->ActionSupport ;
				$this->CommandeExecuter->InsereCritereNonVide(array("quantite")) ;
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
				// $this->CommandeExecuter->MessageSuccesExecution = $implem->MsgSuccesSoumetCmt ;
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
				$implem = $this->ObtientImplemPageInterne() ;
				$cfgEntite = $implem->ObtientCfgEntiteAppl($entite) ;
				// ID Entite
				$this->FltIdEntite = $this->InsereFltEditFixe("id_entite", $entite->LgnEnCours["id"], "id_entite") ;
				// Titre Entite
				$this->FltTitreEntite = $this->InsereFltEditFixe("titre_entite", $entite->LgnEnCours["titre"], "titre_entite") ;
				// Nom Entite
				$this->FltNomElementModule = $this->InsereFltEditFixe("nom_entite", $entite->NomElementModule, "nom_entite") ;
				// Quantite
				$this->FltQte = $this->InsereFltEditHttpPost("quantite", "quantite") ;
				$this->FltQte->Libelle = "Quantit&eacute;" ;
				$this->FltQte->AccepteTagsHtml = 0 ;
				$this->FltQte->Obligatoire = 1 ;
				$this->FltQte->ValeurParDefaut = 1 ;
				$this->CompQte = $this->FltQte->ObtientComposant() ;
				$this->CompQte->Largeur = "30px" ;
				// Prix
				$this->FltPrix = $this->InsereFltEditFixe("prix", (isset($entite->LgnEnCours[$cfgEntite->NomColPrix])) ? $entite->LgnEnCours[$cfgEntite->NomColPrix] : $cfgEntite->PrixParDefaut, "prix") ;
				// echo 'yyy : '.get_class($cfgEntite) ;
			}
		}
		
		class ActValidCmdArticleShoppingSws extends PvActionNotificationWeb
		{
			public $FormulaireDonneesParent ;
			public function Execute()
			{
				$form = & $this->FormulaireDonneesParent ;
				$script = & $this->ScriptParent ;
				$zone = & $this->ZoneParent ;
				$implem = $form->ObtientImplemPageInterne() ;
				$bd = $script->ObtientBDSupport() ;
				$idSession = session_id() ;
				$idCommande = $implem->IdCommandeMembreConnecte($form->IdBoutique, $zone) ;
				if($idCommande == 0)
				{
					$this->RenseigneErreur("La boutique demand&eacute;e n'existe pas.") ;
					return ;
				}
				$qte = $form->FltQte->Lie() ;
				if($qte < 0)
				{
					$this->RenseigneErreur("La quantit&eacute; doit &ecirc;tre superieure &agrave; 0") ;
					return ;
				}
				$membreConnecte = ($script->ZoneParent->PossedeMembreConnecte()) ? 1 : 0 ;
				$sql = 'select t1.*, t2.id id_article, t2.quantite, t2.prix from '.$bd->EscapeTableName($implem->NomTableCommande).' t1 left join (select * from '.$bd->EscapeTableName($implem->NomTableArticle).' where id_entite=:idEntite and nom_entite=:nomEntite) t2 on t1.id = t2.id_commande where id_commande=:idCommande' ;
				$lgn = $bd->FetchSqlRow(
					$sql,
					array(
						"idCommande" => $idCommande,
						"idEntite" => $form->FltIdEntite->Lie(),
						"nomEntite" => $form->FltNomElementModule->Lie()
					)
				) ;
				if(! is_array($lgn))
				{
					$this->RenseigneErreur("Erreur BD : ".$bd->ConnectionException) ;
					return ;
				}
				$ok = 0 ;
				if($lgn["id_article"] != "")
				{
					// Mettre a jour l'article
					$ok = $bd->UpdateRow(
						$implem->NomTableArticle,
						array(
							"quantite" => $lgn["quantite"] + intval($qte)
						),
						"id=:idArticle",
						array("idArticle" => $lgn["id_article"])
					) ;
				}
				else
				{
					// Ajouter l'article
					$ok = $bd->InsertRow(
						$implem->NomTableArticle,
						array(
							"id_entite" => $form->FltIdEntite->Lie(),
							"nom_entite" => $form->FltNomElementModule->Lie(),
							"titre_entite" => $form->FltTitreEntite->Lie(),
							"quantite" => $form->FltQte->Lie(),
							"id_commande" => $idCommande,
							"prix" => intval($form->FltPrix->Lie()),
						)
					) ;
					// print_r($bd) ;
				}
				if($ok == true)
				{
					$paramsMsg = array(
						"id_boutique" => $form->IdBoutique,
						"url_panier" => htmlspecialchars($implem->ScriptPanierMembre->ObtientUrlParam(array("id" => $form->IdBoutique))),
					) ;
					$this->ConfirmeSucces(_parse_pattern($implem->MsgSuccesCmdArticle, $paramsMsg)) ;
				}
				else
				{
					$this->RenseigneErreur("Erreur BD : ".$bd->ConnectionException) ;
				}
			}
		}
		class CmdValidCmdArtShoppingSws extends PvCommandeExecuterBase
		{
			protected function ExecuteInstructions()
			{
				$form = & $this->FormulaireDonneesParent ;
				$script = & $this->ScriptParent ;
				$zone = & $this->ZoneParent ;
				$implem = $form->ObtientImplemPageInterne() ;
				$bd = $script->ObtientBDSupport() ;
				$idSession = session_id() ;
				$idCommande = $implem->IdCommandeMembreConnecte($form->IdBoutique, $zone) ;
				if($idCommande == 0)
				{
					$this->RenseigneErreur("La boutique demand&eacute;e n'existe pas.") ;
					return ;
				}
				$qte = $form->FltQte->Lie() ;
				if($qte < 0)
				{
					$this->RenseigneErreur("La quantit&eacute; doit &ecirc;tre superieure &agrave; 0") ;
					return ;
				}
				$membreConnecte = ($script->ZoneParent->PossedeMembreConnecte()) ? 1 : 0 ;
				$sql = 'select t1.*, t2.id id_article, t2.quantite, t2.prix from '.$bd->EscapeTableName($implem->NomTableCommande).' t1 left join (select * from '.$bd->EscapeTableName($implem->NomTableArticle).' where id_entite=:idEntite and nom_entite=:nomEntite) t2 on t1.id = t2.id_commande where id_commande=:idCommande' ;
				$lgn = $bd->FetchSqlRow(
					$sql,
					array(
						"idCommande" => $idCommande,
						"idEntite" => $form->FltIdEntite->Lie(),
						"nomEntite" => $form->FltNomElementModule->Lie()
					)
				) ;
				if(! is_array($lgn))
				{
					$this->RenseigneErreur("Erreur BD : ".$bd->ConnectionException) ;
					return ;
				}
				$ok = 0 ;
				if($lgn["id_article"] != "")
				{
					// Mettre a jour l'article
					$ok = $bd->UpdateRow(
						$implem->NomTableArticle,
						array(
							"quantite" => $lgn["quantite"] + intval($qte)
						),
						"id=:idArticle",
						array("idArticle" => $lgn["id_article"])
					) ;
				}
				else
				{
					// Ajouter l'article
					$ok = $bd->InsertRow(
						$implem->NomTableArticle,
						array(
							"id_entite" => $form->FltIdEntite->Lie(),
							"nom_entite" => $form->FltNomElementModule->Lie(),
							"titre_entite" => $form->FltTitreEntite->Lie(),
							"quantite" => $form->FltQte->Lie(),
							"id_commande" => $idCommande,
							"prix" => intval($form->FltPrix->Lie()),
						)
					) ;
					// print_r($bd) ;
				}
				if($ok == true)
				{
					$paramsMsg = array(
						"id_boutique" => $form->IdBoutique,
						"url_panier" => htmlspecialchars($implem->ScriptPanierMembre->ObtientUrlParam(array("id" => $form->IdBoutique))),
					) ;
					$this->ConfirmeSucces(_parse_pattern($implem->MsgSuccesCmdArticle, $paramsMsg)) ;
				}
				else
				{
					$this->RenseigneErreur("Erreur BD : ".$bd->ConnectionException) ;
				}
			}
		}
		
		class FormPanierMembreShoppingSws extends FormDonneesBaseImplemShoppingSws
		{
			public $Largeur = "100%" ;
			public $LgnsArticle = array() ;
			public $InclureElementEnCours = 0 ;
			public $InclureTotalElements = 0 ;
			public $FltsSupprArticle = array() ;
			public $FltsQteArticle = array() ;
			public $InclureEnteteCommande = 0 ;
			public $InclureLienImprCommande = 0 ;
			public $NomClasseCommandeExecuter = "CmdMajPanierMembreShoppingSws" ;
			public function ChargeConfig()
			{
				$this->DessinateurFiltresEdition = new DessinFltEditPanierMembreShoppingSws() ;
				parent::ChargeConfig() ;
				$this->CommandeExecuter->Libelle = "Mettre &agrave; jour" ;
				$this->ChargeLgnsArticle() ;
				$this->RedirigeAnnulerVersUrl("?") ;
				$this->CmdChoixRglt = $this->InsereCommande("cmd_choix_rglt_cmd", new CmdRgltPanierMembreShoppingSws()) ;
				$this->CmdChoixRglt->Libelle = "Proc&eacute;der au paiement &gt;&gt;" ;
			}
			protected function ExecuteCommandeSelectionnee()
			{
				parent::ExecuteCommandeSelectionnee() ;
				if($this->PossedeCommandeSelectionnee())
				{
					$this->ChargeLgnsArticle() ;
				}
				if($this->Editable == 0 || count($this->LgnsArticle) == 0)
				{
					$this->CacherBlocCommandes = 1 ;
				}
			}
			protected function ChargeLgnsArticle()
			{
				if($this->ScriptParent->IdCommande == 0)
				{
					return ;
				}
				$implem = $this->ObtientImplemPage() ;
				$this->LgnsArticle = $implem->LgnsDetailsCommande($this->ScriptParent->IdCommande) ;
			}
			protected function ChargeFournDonnees()
			{
				$script = & $this->ScriptParent ;
			}
			protected function ChargeFiltresSelection()
			{
			}
			public function PossedeArticles()
			{
				if($this->ScriptParent->IdCommande == 0 || count($this->LgnsArticle) == 0)
				{
					return false ;
				}
				return true ;
			}
			protected function ChargeFiltresEdition()
			{
				$this->FltIdCommande = $this->InsereFltEditFixe("id_commande", $this->IdCommande, "id_commande") ;
				/*
				foreach($this->LgnsArticle as $i => $lgn)
				{
					$id = $lgn["id"] ;
					$this->FltsSupprArticle[$id] = $this->InsereFltEditHttpPost("suppr_".$id, "suppr_".$id) ;
					$this->FltsSupprArticle[$id]->DeclareComposant("PvZoneCocherHtml") ;
					$this->FltsQteArticle[$id] = $this->InsereFltEditHttpPost("qte_".$id, "qte_".$id) ;
					$this->FltsQteArticle[$id]->ValeurParDefaut = $lgn["quantite"] ;
					$comp = $this->FltsQteArticle[$id]->ObtientComposant() ;
					$comp->Largeur = "50px" ;
				}
				*/
			}
		}
		
		class DessinFltEditPanierMembreShoppingSws extends PvDessinFltsDonneesHtml
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$form = & $composant ;
				$implem = $script->ObtientImplemPage() ;
				return  $implem->RenduDetailsCommande($form->LgnsArticle, $form) ;
			}
		}
		
		class DessinFltEditExpedShoppingSws extends PvDessinFltsDonneesHtml
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= '<table width="100%" cellspacing="0" cellpadding="2">'.PHP_EOL ;
				$ctn .= '<tr><th colspan="2" align="left">Exp&eacute;diteur</th></tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltNomClt).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltNomClt, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltPrenomClt).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltPrenomClt, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltEmailClt).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltEmailClt, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltAdrClt).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltAdrClt, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltBpClt).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltBpClt, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltContactClt).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltContactClt, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr><th colspan="2" align="left">Destinataire</th></tr>'.PHP_EOL ;
				$ctn .= '<tr><td colspan="2"><input type="checkbox" id="'.$this->IDInstanceCalc.'_ClientEstDest" name="ClientEstDest" onchange="'.$this->IDInstanceCalc.'_ActiveEditDest(this.checked);" checked value="1" /><label for="'.$this->IDInstanceCalc.'_ClientEstDest">Utiliser les m&ecirc;mes coordonn&eacute;es</label></td></tr>' ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltNomDest).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltNomDest, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltPrenomDest).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltPrenomDest, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltEmailDest).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltEmailDest, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltAdrDest).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltAdrDest, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltBpDest).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltBpDest, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '<tr>
<td align="left" valign="top">'.$this->RenduLibelleFiltre($script->FltContactDest).'</td>
<td align="left" valign="top">'.$this->RenduFiltre($script->FltContactDest, $composant).'</td>
</tr>'.PHP_EOL ;
				$ctn .= '</table>' ;
				$ctn .= '<script language="javascript">
	function '.$this->IDInstanceCalc.'_ActiveEditDest(ok)
	{
		if(ok)
		{
			document.getElementById("'.$script->FltNomDest->ObtientIDComposant().'").disabled = "disabled" ;
			document.getElementById("'.$script->FltPrenomDest->ObtientIDComposant().'").disabled = "disabled" ;
			document.getElementById("'.$script->FltEmailDest->ObtientIDComposant().'").disabled = "disabled" ;
			document.getElementById("'.$script->FltAdrDest->ObtientIDComposant().'").disabled = "disabled" ;
			document.getElementById("'.$script->FltBpDest->ObtientIDComposant().'").disabled = "disabled" ;
			document.getElementById("'.$script->FltContactDest->ObtientIDComposant().'").disabled = "disabled" ;
		}
		else
		{
			document.getElementById("'.$script->FltNomDest->ObtientIDComposant().'").removeAttribute("disabled") ;
			document.getElementById("'.$script->FltPrenomDest->ObtientIDComposant().'").removeAttribute("disabled") ;
			document.getElementById("'.$script->FltEmailDest->ObtientIDComposant().'").removeAttribute("disabled") ;
			document.getElementById("'.$script->FltAdrDest->ObtientIDComposant().'").removeAttribute("disabled") ;
			document.getElementById("'.$script->FltBpDest->ObtientIDComposant().'").removeAttribute("disabled") ;
			document.getElementById("'.$script->FltContactDest->ObtientIDComposant().'").removeAttribute("disabled") ;
		}
	}
	'.$this->IDInstanceCalc.'_ActiveEditDest(true) ;
</script>' ;
				return $ctn ;
			}
		}
		
		class CmdMajPanierMembreShoppingSws extends PvCommandeExecuterBase
		{
			protected function ExecuteInstructions()
			{
				$script = & $this->ScriptParent ;
				$form = & $this->FormulaireDonneesParent ;
				$implem = $script->ObtientImplemPage() ;
				$bd = $script->ObtientBDSupport() ;
				if($form->Editable == 1)
				{
					foreach($form->LgnsArticle as $i => $lgn)
					{
						$id = $lgn["id"] ;
						$suppr = intval(_POST_def("suppr_".$id)) ;
						$qte = 0 ;
						if($suppr != "1")
						{
							$qte = intval(_POST_def("qte_".$id)) ;
						}
						if($qte <= 0)
						{
							$ok = $bd->DeleteRow($implem->NomTableArticle, "id = :idArticle", array("idArticle" => $id)) ;
						}
						else
						{
							$ok = $bd->UpdateRow(
								$implem->NomTableArticle,
								array("quantite" => $qte),
								"id = :idArticle",
								array("idArticle" => $id)
							) ;
						}
					}
				}
				$this->ConfirmeSucces() ;
			}
		}
		class CmdRgltPanierMembreShoppingSws extends CmdMajPanierMembreShoppingSws
		{
			protected function ExecuteInstructions()
			{
				$implem = $this->ScriptParent->ObtientImplemPage() ;
				parent::ExecuteInstructions() ;
				if($this->StatutExecution == 1)
				{
					redirect_to($implem->ScriptEditExpeditCmd->ObtientUrlParam(array("id" => _GET_def("id")))) ;
				}
			}
		}
		class CmdModifExpeditCmdShoppingSws extends PvCommandeModifElement
		{
			public function ExecuteInstructions()
			{
				$clientEstDest = _POST_def("ClientEstDest") ;
				$script = & $this->ScriptParent ;
				$implem = & $script->ObtientImplemPage() ;
				if($clientEstDest == 1)
				{
					$script->FltNomDest->ValeurParDefaut = $script->FltNomClt->Lie() ;
					$script->FltNomDest->NePasLierParametre = 1 ;
					$script->FltPrenomDest->ValeurParDefaut = $script->FltPrenomClt->Lie() ;
					$script->FltPrenomDest->NePasLierParametre = 1 ;
					$script->FltEmailDest->ValeurParDefaut = $script->FltEmailClt->Lie() ;
					$script->FltEmailDest->NePasLierParametre = 1 ;
					$script->FltAdrDest->ValeurParDefaut = $script->FltAdrClt->Lie() ;
					$script->FltAdrDest->NePasLierParametre = 1 ;
					$script->FltBpDest->ValeurParDefaut = $script->FltBpClt->Lie() ;
					$script->FltBpDest->NePasLierParametre = 1 ;
					$script->FltContactDest->ValeurParDefaut = $script->FltContactClt->Lie() ;
					$script->FltContactDest->NePasLierParametre = 1 ;
				}
				parent::ExecuteInstructions() ;
				if($this->StatutExecution == 1)
				{
					redirect_to($implem->ScriptChoixRgltCmd->ObtientUrlParam(array("id" => _GET_def("id")))) ;
				}
			}
		}
	}
	
?>