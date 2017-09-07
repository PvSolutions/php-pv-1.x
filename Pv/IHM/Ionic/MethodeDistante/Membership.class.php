<?php
	
	if(! defined('PV_METHODE_DISTANTE_MEMBERSHIP_IONIC'))
	{
		define('PV_METHODE_DISTANTE_MEMBERSHIP_IONIC', 1) ;
		
		class PvMtdDistNoyauMembershipIonic extends PvMethodeDistanteNoyauIonic
		{
			protected function & Membership()
			{
				return $this->ZoneParent->Membership ;
			}
		}
		
		class PvMtdDistAuthentifieIonic extends PvMtdDistNoyauMembershipIonic
		{
			protected function ExecuteInstructions()
			{
				$membership = $this->Membership() ;
				$login = $this->FiltreEditionParam("login") ;
				$motPasse = $this->FiltreEditionParam("motPasse") ;
				// file_put_contents("aaa.txt", "mmm") ;
				$idMembre = $membership->ValidateConnection($login, $motPasse) ;
				if($idMembre != $membership->IdMemberNotFoundValue)
				{
					$lgnMembre = $membership->FetchMember($idMembre) ;
					$lgnMembre->ParentMembership = null ;
					$this->ConfirmeSucces($lgnMembre) ;
					// file_put_contents("mmm.txt", print_r($lgnMembre, true)) ;
				}
				else
				{
					$this->RenseigneErreur(1, "Utilisateur / Mot de passe incorrect", "invalid_credentials") ;
				}
			}
		}
	}
	
?>