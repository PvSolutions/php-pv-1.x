<?php
	
	if(! defined('PV_AUTH_RESTFUL'))
	{
		define('PV_AUTH_RESTFUL', 1) ;
		
		class PvAuthBaseRestful
		{
			public function CreeSession(& $api)
			{
				return 0 ;
			}
			public function SupprimeSession(& $api)
			{
				return 0 ;
			}
			public function ChargeSession(& $api)
			{
				return 0 ;
			}
		}
		
		class PvAuthDistRestful extends PvAuthBaseRestful
		{
			public function CreeSession(& $api)
			{
				
			}
			public function SupprimeSession(& $api)
			{
				return 0 ;
			}
			public function ChargeSession(& $api)
			{
				if($api->Requete->EnteteAuthType == 'bearer')
				{
					$token = $api->Requete->EnteteAuthCredentials ;
					$bd = $api->BDMembership() ;
					$idMembre = $bd->FetchSqlValue(
						'select member_id from '.$bd->EscapeVariableName($api->NomTableSession).' where token='.$bd->ParamPrefix.'token',
						array("token" => $token),
						'member_id',
						0
					) ;
					if($idMembre !== null)
					{
						if($idMembre > 0)
						{
							$api->Membership->LoadMember($idMembre) ;
						}
						else
						{
							$api->Reponse->ConfirmeNonAutoris() ;
						}
					}
					else
					{
						$api->Reponse->ConfirmeErreurInterne() ;
					}
				}
			}
		}
	}
	
?>