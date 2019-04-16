<?php
	
	if(! defined('PV_REMPL_CONFIG_MEMBERSHIP_CORDOVA'))
	{
		define('PV_REMPL_CONFIG_MEMBERSHIP_CORDOVA', 1) ;
		
		class PvRemplConfigMembershipCordova extends PvRemplisseurConfigMembershipSimple
		{
			public function RemplitDefinitionColActionsTableauMembre(& $table)
			{
				$membership = $table->ZoneParent->Membership ;
				$i = count($table->DefinitionsColonnes) ;
				
				$table->DefinitionsColonnes[$i] = new PvDefinitionColonneDonnees() ;
				$table->DefinitionsColonnes[$i]->Libelle = "Actions" ;
				$table->DefinitionsColonnes[$i]->AlignElement = "center" ;
				$table->DefinitionsColonnes[$i]->TriPossible = 0 ;
				$table->DefinitionsColonnes[$i]->Formatteur = new PvFormatteurColonneLiens() ;
				
				$nomClasse = $this->NomClasseLienModifTableauMembre ;
				if(class_exists($nomClasse))
				{
					$lienModif = new $nomClasse() ;
					$lienModif->FormatIdOnglet = 'modif_membre_${MEMBER_ID}' ;
					$lienModif->FormatTitreOnglet = 'Modifier le membre ${MEMBER_LOGIN}' ;
					$lienModif->FormatLibelle = "Modifier" ;
					$lienModif->FormatURL = 'javascript:pvZoneCordova.afficheEcran("'.$table->ZoneParent->ScriptModifMembre->IDInstanceCalc.'", { idMembre : ${MEMBER_ID}})' ;
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienModif ;
				}
				
				$nomClasse = $this->NomClasseLienChangeMPTableauMembre ;
				if(class_exists($nomClasse))
				{
					$lienChangeMP = new $nomClasse() ;
					$lienChangeMP->FormatIdOnglet = 'change_mp_membre_${MEMBER_ID}' ;
					$lienChangeMP->FormatTitreOnglet = 'Changer mot de passe de ${MEMBER_LOGIN}' ;
					$lienChangeMP->FormatLibelle = "Changer mot de passe" ;
					if($membership->ADActivatedMemberColumn != '')
					{
						$lienChangeMP->NomDonneesValid = "MEMBER_AD_ACTIVATED" ;
						$lienChangeMP->ValeurVraiValid = 0 ;
					}
					$lienChangeMP->FormatURL = 'javascript:pvZoneCordova.afficheEcran("'.$table->ZoneParent->ScriptChangeMPMembre->IDInstanceCalc.'", { idMembre : ${MEMBER_ID}})' ;
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienChangeMP ;
				}
				
				$nomClasse = $this->NomClasseLienSupprTableauMembre ;
				if(class_exists($nomClasse))
				{
					$lienSuppr = new $nomClasse() ;
					$lienSuppr->FormatIdOnglet = 'suppr_membre_${MEMBER_ID}' ;
					$lienSuppr->FormatTitreOnglet = 'Supprimer le membre ${MEMBER_LOGIN}' ;
					$lienSuppr->FormatLibelle = "Supprimer" ;
					$lienSuppr->FormatURL = 'javascript:pvZoneCordova.afficheEcran("'.$table->ZoneParent->ScriptSupprMembre->IDInstanceCalc.'", { idMembre : ${MEMBER_ID}})' ;
					$table->DefinitionsColonnes[$i]->Formatteur->Liens[] = $lienSuppr ;
				}
			}
			public function RemplitFormulaireGlobalMembre(& $form)
			{
				parent::RemplitFormulaireGlobalMembre($form) ;
				$form->CommandeAnnuler->ContenuJsSurClick = 'pvZoneCordova.afficheEcran(&quot;'.$form->ZoneParent->ScriptListeMembres->IDInstanceCalc.'&quot;)' ;
			}
		}
	}
	
?>