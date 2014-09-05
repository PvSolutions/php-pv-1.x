<?php
	
	if(! defined('AK_MEMBERSHIP'))
	{
		if(! defined('AK_CORE'))
		{
			include dirname(__FILE__)."/Core.class.php" ;
		}
		class AkMembershipLdapAuth
		{
			public $Host = "" ;
			public $Port = 636 ;
			protected $Connection = false ;
			protected $ErrorMessage = "" ;
			protected $ErrorNo = "" ;
			public $ProtocolV1Enabled = 1 ;
			public $ProtocolV2Enabled = 1 ;
			protected function ClearError()
			{
				$this->SetError(0, '') ;
			}
			protected function SetError($errorNo, $errorMessage)
			{
				$this->ErrorCode = $errorCode ;
				$this->ErrorMessage = $errorMessage ;
			}
			protected function SetConnectionError()
			{
				$this->SetError(ldap_errno($this->Connection), ldap_error($this->Connection)) ;
			}
			protected function Open()
			{
				try { $this->Connection = ldap_connect($this->Host, $this->Port) ; } catch(Exception $ex) { $this->ConnectionError = $ex->getMessage() ; }
				if($this->Connection == false)
				{
					$this->SetError(-2, 'Impossible de se connecter au serveur ldap '.$this->Host.':'.$this->Port) ;
				}
				if($this->Connection !== false)
				{
					if($this->ProtocolV1Enabled)
					{
						$ok = ldap_set_option($this->Connection, LDAP_OPT_PROTOCOL_VERSION, 3) ;
						if(! $ok)
							$this->SetError(-2, "Protocole Ldap V1 inapplicable");
					}
					if($this->ProtocolV2Enabled)
					{
						$ok = ldap_set_option($this->Connection, LDAP_OPT_REFERRALS, 0) ;
						if(! $ok)
							$this->SetError(-2, "Protocole Ldap V2 inapplicable");
					}
					$this->SetConnectionOptions() ;
				}
				return ($this->Connection != false) ? 1 : 0 ;
			}
			protected function SetConnectionOptions()
			{
			}
			public function Verify($user, $password)
			{
				if(! $this->Open())
				{
					return 0 ;
				}
				$link = ldap_bind($this->Connection, $user, $password);
				$ok = ($link != false) ? 1 : 0 ;
				if(! $ok)
				{
					$this->SetErrorFromConnection() ;
				}
				else
				{
					ldap_unbind($link) ;
				}
				$this->Close() ;
				return $ok ;
			}
			protected function Close()
			{
				unset($this->Connection) ;
				$this->Connection = false ;
			}
		}
		
		class AkMembershipBase extends AkItemBase
		{
			public $ParentArea = null ;
			public $MemberLogged = null ;
			public $MemberClassName = "AkMember" ;
			public $GuestMemberId = "" ;
			public $UseGuestMember = 0 ;
			public $RootMemberId = "" ;
			public $UseRootMember = 1 ;
			public $SessionMemberId = false ;
			public $SessionMemberKey = "login" ;
			public $SessionSource = "SESSION" ;
			public $SessionTimeout = 0 ;
			protected function InitConfig(& $parent)
			{
				$this->ParentArea = & $parent ;
			}
			public function GetSessionValue($key, $defaultValue=false)
			{
				$value = $defaultValue ;
				switch(strtoupper($this->SessionSource))
				{
					case "SESSION" :
					case "SESSIONS" :
					{
						// print_r($_SESSION) ;
						if(isset($_SESSION[$key]))
							$value = $_SESSION[$key] ;
					}
					break ;
					case "COOKIE" :
					case "COOKIES" :
					{
						if(isset($_COOKIE[$key]))
							$value = $_COOKIE[$key] ;
					}
					break ;
				}
				return $value ;
			}
			public function SetSessionValue($key, $value="")
			{
				switch(strtoupper($this->SessionSource))
				{
					case "SESSION" :
					case "SESSIONS" :
					{
						if($value === null)
							unset($_SESSION[$key]) ;
						else
							$_SESSION[$key] = $value ;
					}
					break ;
					case "COOKIE" :
					case "COOKIES" :
					{
						setcookie($key, $value) ;
					}
					break ;
				}
			}
			public function LoadSession()
			{
				$this->SessionMemberId = $this->GetSessionValue($this->SessionMemberKey) ;
				if($this->SessionMemberId === false && $this->UseGuestMember && $this->GuestMemberId != false)
				{
					$this->SessionMemberId = $this->GuestMemberId ;
				}
				$this->MemberLogged = $this->NullValue() ;
				// print 'Sssion ID : '.$this->SessionMemberId ;
				// exit ;
				if(! empty($this->SessionMemberId))
					$this->MemberLogged = $this->FetchMember($this->SessionMemberId) ;
			}
			public function SaveSession($memberId)
			{
				$this->SetSessionValue($this->SessionMemberKey, $memberId) ;
			}
			public function ClearSession()
			{
				$this->SetSessionValue($this->SessionMemberKey, null) ;
			}
			public function ValidateConnection($login, $password)
			{
				return $this->IdMemberNotFoundValue ;
			}
			public function LogonMember($memberId)
			{
				$this->SaveSession($memberId) ;
			}
			public function LogoutMember($memberId)
			{
				$this->ClearSession() ;
			}
			public function FetchMemberRow($memberId)
			{
				return array() ;
			}
			public function FetchProfileRows($profileId)
			{
				return array() ;
			}
			protected function CreateMember()
			{
				$member = $this->NullValue() ;
				$className = $this->MemberClassName ;
				if(class_exists($className))
				{
					$member = new $className() ;
					$member->ParentMembership = & $this ;
				}
				return $member ;
			}
			public function FetchMember($memberId)
			{
				$row = $this->FetchMemberRow($memberId) ;
				return $this->FetchMemberFromRow($row) ;
			}
			public function FetchMemberRowByLogin($login)
			{
				$row = $this->FetchMemberRowByLogin($memberId) ;
				return $this->FetchMemberFromRow($row) ;
			}
			public function FetchMemberFromRow($row)
			{
				$member = $this->NullValue() ;
				if(empty($row))
				{
					return $member ;
				}
				$member = $this->CreateMember() ;
				$member->LoadConfig() ;
				if($member != null)
				{
					$member->ImportConfigFromRow($row) ;
				}
				return $member ;
			}
			public function FetchMemberByLogin($login)
			{
				$row = $this->FetchMemberRowByLogin($memberId) ;
				return $this->FetchMemberFromRow($row) ;
			}
			public function InsertMemberRow($memberRow)
			{
				return 0 ;
			}
			public function UpdateMemberRow($memberId, $memberRow)
			{
				return 0 ;
			}
			public function DeleteMemberRow($memberId)
			{
				return 0 ;
			}
			public function InsertProfileRow($profileRow)
			{
				return 0 ;
			}
			public function UpdateProfileRow($profileId, $profileRow)
			{
				return 0 ;
			}
			public function DeleteProfileRow($profileId)
			{
				return 0 ;
			}
			public function InsertRoleRow($roleRow)
			{
				return 0 ;
			}
			public function UpdateRoleRow($roleId, $roleRow)
			{
				return 0 ;
			}
			public function DeleteRoleRow($roleId)
			{
				return 0 ;
			}
			public function FetchMemberRange($start, $max, $filters=array())
			{
				return 0 ;
			}
			public function FetchMemberTotal($filters=array())
			{
				return 0 ;
			}
			public function FetchProfileRange($start, $max, $filters=array())
			{
				return 0 ;
			}
			public function FetchProfileTotal($filters=array())
			{
				return 0 ;
			}
			public function FetchRoleRange($start, $max, $filters=array())
			{
				return 0 ;
			}
			public function FetchRoleTotal($filters=array())
			{
				return 0 ;
			}
			public function Run()
			{
				// echo "mmmm" ;
				$this->LoadSession() ;
			}
		}
		class AkMember extends CommonEntityRow
		{
			public $StoreData = 1 ;
			public $Id ;
			public $Login ;
			public $Password ;
			public $FirstName ;
			public $LastName ;
			public $Email ;
			public $Enable ;
			public $ADActivated = 0 ;
			public $MustChangePassword = 0 ;
			public $TotalLoginAttempt = 0 ;
			public $Contact = "" ;
			public $Address = "" ;
			public $ProfileId = 0;
			public $Profile = null;
			public $ParentMembership = null;
			public $ProfileClassName = "AkProfile" ;
			protected function InitConfig(& $parent)
			{
				parent::InitConfig($parent) ;
				$this->ParentMembership = & $parent ;
			}
			public function LoadConfig()
			{
				$this->Profile = $this->CreateProfile() ;
			}
			protected function CreateProfile()
			{
				$className = $this->ProfileClassName ;
				$profile = null ;
				if(! class_exists($className))
					return ;
				$profile = new $className() ;
				return $profile ;
			}
			public function ImportConfigFromRow($row)
			{
				parent::ImportConfigFromRow($row) ;
				$this->LoadProfile($this->ProfileId) ;
			}
			public function LoadProfile($profileId)
			{
				$rows = $this->ParentMembership->FetchProfileRows($profileId) ;
				// print $this->ParentMembership->Database->LastSqlText ;
				if($rows == null)
					return null ;
				$this->Profile->ImportConfigFromRows($rows) ;
			}
			protected function ImportConfigFromRowValue($name, $value)
			{
				$success = parent::ImportConfigFromRowValue($name, $value) ;
				if($success)
					return 1 ;
				$success = 1 ;
				switch($name)
				{
					case "MEMBER_ID" :
					{
						$this->Id = $value ;
					}
					break ;
					case "MEMBER_LOGIN" :
					{
						$this->Login = $value ;
					}
					break ;
					case "MEMBER_PASSWORD" :
					{
						$this->Password = $value ;
					}
					break ;
					case "MEMBER_EMAIL" :
					{
						$this->Email = $value ;
					}
					break ;
					case "MEMBER_FIRST_NAME" :
					{
						$this->FirstName = $value ;
					}
					break ;
					case "MEMBER_LAST_NAME" :
					{
						$this->LastName = $value ;
					}
					break ;
					case "MEMBER_ENABLE" :
					{
						$this->Enable = $value ;
					}
					break ;
					case "MEMBER_CONTACT" :
					{
						$this->Contact = $value ;
					}
					break ;
					case "MEMBER_ADDRESS" :
					{
						$this->Address = $value ;
					}
					break ;
					case "MEMBER_AD_ACTIVATED" :
					{
						$this->ADActivated = $value ;
					}
					break ;
					case "MEMBER_MUST_CHANGE_PASSWORD" :
					{
						$this->MustChangePassword = $value ;
					}
					break ;
					case "MEMBER_PROFILE" :
					{
						$this->ProfileId = $value ;
					}
					break ;
					default :
					{
						$success = 0 ;
					}
					break ;
				}
				return $success ;
			}
		}
		class AkPrivilege extends CommonEntityRow
		{
			public $Id = "" ;
			public $Name = "" ;
			public $RoleId = "" ;
			public $Title = "" ;
			public $Description = "" ;
			public $Enabled = 0 ;
			protected function ImportConfigFromRowValue($name, $value)
			{
				$success = parent::ImportConfigFromRowValue($name, $value) ;
				if($success)
				{
					return $success ;
				}
				$success = 1 ;
				switch($name)
				{
					case "PRIVILEGE_ID" :
					{
						$this->Id = $value ;
					}
					break ;
					case "ROLE_ID" :
					{
						$this->RoleId = $value ;
					}
					break ;
					case "ROLE_NAME" :
					{
						$this->Name = $value ;
					}
					break ;
					case "ROLE_TITLE" :
					{
						$this->Title = $value ;
					}
					break ;
					case "ROLE_DESCRIPTION" :
					{
						$this->Description = $value ;
					}
					break ;
					case "PRIVILEGE_ENABLED" :
					{
						$this->Enabled = $value ;
					}
					break ;
					default :
					{
						$success = 0 ;
					}
					break ;
				}
				return $success ;
			}
		}
		class AkProfile extends CommonEntityRow
		{
			public $Privileges = array() ;
			protected $ImportRowIndex = -1 ;
			public function ImportConfigFromRows($rows)
			{
				$this->ImportRowIndex = 0 ;
				foreach($rows as $i => $row)
				{
					if($i == 0)
					{
						$this->ImportConfigFromRow($row) ;
					}
					$this->ImportRowIndex = $i ;
					$privilege = $this->CreatePrivilege() ;
					$privilege->ImportConfigFromRow($row) ;
					$this->Privileges[$privilege->Name] = $privilege ;
				}
			}
			protected function ImportConfigFromRowValue($name, $value)
			{
				$success = parent::ImportConfigFromRowValue($name, $value) ;
				if($success)
					return 1 ;
				$success = 1 ;
				switch($name)
				{
					case "PROFILE_ID" :
					{
						$this->Id = $value ;
					}
					break ;
					case "PROFILE_TITLE" :
					{
						$this->Title = $value ;
					}
					break ;
					case "PROFILE_DESCRIPTION" :
					{
						$this->Name = $value ;
					}
					break ;
					default :
					{
						$success = 0 ;
					}
					break ;
				}
				return $success ;
			}
			protected function CreatePrivilege()
			{
				return new AkPrivilege() ;
			}
		}
		
		class AkSqlMembership extends AkMembershipBase
		{
			public $Database = null ;
			public $IdMemberNotFoundValue = -1 ;
			public $MemberTable = "membership_member" ;
			public $IdMemberColumn = "id" ;
			public $IdMemberIgnoreUpdate = 1 ;
			public $IdMemberInsertExpr = "" ;
			public $LoginMemberCaseInsensitive = 1 ;
			public $LoginMemberColumn = "login_member" ;
			public $LoginMemberAlias = "" ;
			public $LoginMemberLabel = "Login" ;
			public $PasswordMemberColumn = "password_member" ;
			public $PasswordMemberExpr = "PASSWORD" ;
			public $PasswordMemberAlias = "" ;
			public $PasswordMemberLabel = "Mot de passe" ;
			public $EmailMemberColumn = "email" ;
			public $EmailMemberAlias = "" ;
			public $EmailMemberLabel = "Email" ;
			public $FirstNameMemberColumn = "first_name" ;
			public $FirstNameMemberAlias = "" ;
			public $FirstNameMemberLabel = "Nom" ;
			public $LastNameMemberColumn = "last_name" ;
			public $LastNameMemberAlias = "" ;
			public $LastNameMemberLabel = "Pr&eacute;nom" ;
			public $AddressMemberColumn = "address" ;
			public $AddressMemberAlias = "" ;
			public $AddressMemberLabel = "Adresse" ;
			public $ContactMemberColumn = "contact" ;
			public $ContactMemberAlias = "" ;
			public $ContactMemberLabel = "Contacts" ;
			public $ADActivatedMemberColumn = "" ;
			public $ADActivatedMemberAlias = "" ;
			public $ADActivatedMemberLabel = "Authentifier par Active Directory" ;
			public $ADActivatedMemberTrueValue = "1" ;
			public $ADUserMemberColumn = '' ;
			public $ADUserMemberAlias = '' ;
			public $ADUserMemberLabel = 'Session sur Active Directory' ;
			public $ADDomainMemberDefaultValue = '' ;
			public $ADDomainMemberColumn = '' ;
			public $ADDomainMemberAlias = '' ;
			public $ADDomainMemberLabel = 'Domaine sur Active Directory' ;
			public $EnableMemberColumn = "enabled" ;
			public $EnableMemberAlias = "" ;
			public $EnableMemberLabel = "Activer" ;
			public $EnableMemberTrueValue = "1" ;
			public $MustChangePasswordMemberColumn = "" ;
			public $MustChangePasswordMemberAlias = "" ;
			public $MustChangePasswordMemberLabel = "Doit changer le mot de passe" ;
			public $MustChangePasswordMemberTrueValue = "1" ;
			public $DisableMemberOnDelete = 1 ;
			public $DisableRoleOnDelete = 1 ;
			public $DisableProfileOnDelete = 1 ;
			public $ProfileMemberColumn = "profile_id" ;
			public $ProfileMemberForeignKey = "id" ;
			public $ProfileMemberAlias = "" ;
			public $ProfileMemberLabel = "Profil" ;
			public $ProfileTable = "membership_profile" ;
			public $IdProfileInsertExpr = "" ;
			public $IdProfileIgnoreUpdate = 1 ;
			public $IdProfileColumn = "id" ;
			public $TitleProfileColumn = "title" ;
			public $TitleProfileAlias = "" ;
			public $TitleProfileFormatErrorLabel = "Le titre n'a pas le bon format" ;
			public $TitleProfileFormatErrorAlias = "" ;
			public $TitleProfileFoundErrorLabel = "Le titre d&eacute;fini est d&eacute;j&agrave; utilis&eacute;" ;
			public $TitleProfileFoundErrorAlias = "" ;
			public $TitleProfileLabel = "Titre" ;
			public $DescriptionProfileColumn = "description" ;
			public $DescriptionProfileAlias = "" ;
			public $DescriptionProfileLabel = "Description" ;
			public $EnableProfileColumn = "enabled" ;
			public $EnableProfileAlias = "" ;
			public $EnableProfileTrueValue = "1" ;
			public $EnableProfileLabel = "Activer" ;
			public $RoleListProfileLabel = "Roles" ;
			public $RoleListProfileAlias = "Roles" ;
			public $PrivilegeTable = "membership_privilege" ;
			public $IdPrivilegeInsertExpr = "" ;
			public $IdPrivilegeIgnoreUpdate = 1 ;
			public $IdPrivilegeColumn = "id" ;
			public $EnablePrivilegeColumn = "active" ;
			public $EnablePrivilegeTrueValue = "1" ;
			public $ProfilePrivilegeColumn = "profile_id" ;
			public $ProfilePrivilegeForeignKey = "id" ;
			public $RolePrivilegeColumn = "role_id" ;
			public $RolePrivilegeForeignKey = "id" ;
			public $RoleTable = "membership_role" ;
			public $IdRoleColumn = "id" ;
			public $NameRoleColumn = "name" ;
			public $NameRoleLabel = "Code" ;
			public $NameRoleAlias = "" ;
			public $NameRoleFormatErrorLabel = "Le nom n'a pas le bon format" ;
			public $NameRoleFormatErrorAlias = "" ;
			public $NameRoleFoundErrorLabel = "Le nom d&eacute;fini est d&eacute;j&agrave; utilis&eacute;" ;
			public $NameRoleFoundErrorAlias = "" ;
			public $TitleRoleColumn = "title" ;
			public $TitleRoleAlias = "" ;
			public $TitleRoleLabel = "Titre" ;
			public $SimilarProfileFoundErrorLabel = "Un profil avec le même titre existe d&eacute;j&agrave;" ;
			public $SimilarProfileFoundErrorAlias = "" ;
			public $SimilarRoleFoundErrorLabel = "Un rôle avec le même titre ou le même nom existe d&eacute;j&agrave;" ;
			public $SimilarRoleFoundErrorAlias = "" ;
			public $DescriptionRoleColumn = "description" ;
			public $DescriptionRoleAlias = "" ;
			public $DescriptionRoleLabel = "Description" ;
			public $EnableRoleColumn = "enabled" ;
			public $EnableRoleLabel = "Activer" ;
			public $EnableRoleTrueValue = "1" ;
			public $EnableRoleAlias = "" ;
			public $ProfileListRoleLabel = "Profils" ;
			public $ProfileListRoleAlias = "" ;
			public $IdRoleInsertExpr = "" ;
			public $IdRoleIgnoreUpdate = 1 ;
			public $OldPasswordMemberLabel = "Mot de passe actuel" ;
			public $OldPasswordMemberAlias = "" ;
			public $NewPasswordMemberLabel = "Nouveau mot de passe" ;
			public $NewPasswordMemberAlias = "" ;
			public $ConfirmPasswordMemberLabel = "Confirmer le nouveau mot de passe" ;
			public $ConfirmPasswordMemberAlias = "" ;
			public $NewPasswordMemberFormatErrorLabel = "Le nouveau mot de passe n'a pas le bon format." ;
			public $NewPasswordMemberFormatErrorAlias = "" ;
			public $LoginMemberFormatErrorLabel = "Le login a un mauvais format" ;
			public $LoginMemberFormatErrorAlias = "" ;
			public $PasswordMemberFormatErrorLabel = "Le mot de passe a un mauvais format" ;
			public $PasswordMemberFormatErrorAlias = "" ;
			public $LastNameMemberFormatErrorLabel = "Le nom doit avoir au moins 4 caract&egrave;res et 255 au maximum" ;
			public $LastNameMemberFormatErrorAlias = "" ;
			public $FirstNameMemberFormatErrorLabel = "Le prenom doit avoir au moins 4 caract&egrave;res et 255 au maximum" ;
			public $FirstNameMemberFormatErrorAlias = "" ;
			public $EmailMemberFormatErrorLabel = "L'adresse email a un mauvais format" ;
			public $EmailMemberFormatErrorAlias = "" ;
			public $SimilarMemberFoundErrorLabel = "Un membre avec le même login, le même mot de passe ou le même email existe d&eacute;j&agrave;" ;
			public $SimilarMemberFoundErrorAlias = "" ;
			public $ConfirmPasswordMemberMatchLabel = "Vous n'avez pas confirm&eacute; le mot de passe" ;
			public $ConfirmPasswordMemberMatchAlias = "" ;
			public $ChangePasswordMemberSameLabel = "L'ancien mot de passe et le nouveau ne peuvent pas etre pareils" ;
			public $ChangePasswordMemberSameAlias = "" ;
			public $OldPasswordMemberMatchLabel = "L'ancien mot de passe n'est pas correct" ;
			public $OldPasswordMemberMatchAlias = "" ;
			public $IdAlternateRoleAfterDelete = 0 ;
			public $IdAlternatePrivilegeAfterDelete = 0 ;
			public $LdapConnections = array() ;
			public $TriggerInsertProfileRow = 1 ;
			public $TriggerDeleteProfileRow = 1 ;
			// The membership will insert into privilege tables the
			public $TriggerInsertRoleRow = 1 ;
			public $TriggerDeleteRoleRow = 1 ;
			public $LastValidateError = '' ;
			const VALIDATE_ERROR_NONE = "" ;
			const VALIDATE_ERROR_MEMBER_NOT_FOUND = "member_not_found" ;
			const VALIDATE_ERROR_MEMBER_NOT_ENABLED = "member_not_enabled" ;
			const VALIDATE_ERROR_PASSWORD_INCORRECT = "password_incorrect" ;
			const VALIDATE_ERROR_AD_AUTH_FAILED = "ad_auth_failed" ;
			const VALIDATE_ERROR_OTHER = "member_connection_impossible" ;
			public function FetchSimilarRole($idRoleExclude, $nameRole='', $titleRole='')
			{
				$sql = 'select * from ('.$this->SqlAllRoles().') ROLE_TABLE where ROLE_ID <> '.$this->Database->ParamPrefix.'roleId and (ROLE_NAME = '.$this->Database->ParamPrefix.'roleName or ROLE_TITLE = '.$this->Database->ParamPrefix.'roleTitle)' ;
				$row = $this->Database->FetchSqlRow($sql, array('roleId' => $idRoleExclude, 'roleName' => $nameRole, 'roleTitle' => $titleRole)) ;
				return $row ;
			}
			public function FetchSimilarProfile($idProfileExclude, $titleProfile='')
			{
				$sql = 'select * from ('.$this->SqlAllProfiles().') PROFILE_TABLE where PROFILE_ID <> '.$this->Database->ParamPrefix.'profileId and PROFILE_TITLE = '.$this->Database->ParamPrefix.'profileTitle' ;
				$row = $this->Database->FetchSqlRow($sql, array('profileId' => $idProfileExclude, 'profileTitle' => $titleProfile)) ;
				return $row ;
			}
			public function FetchSimilarMember($idMemberExclude, $login, $password='', $email='')
			{
				$sql = 'select * from ('.$this->SqlAllMembers().') MEMBER_TABLE where MEMBER_ID <> '.$this->Database->ParamPrefix.'memberId and (MEMBER_LOGIN = '.$this->Database->ParamPrefix.'login OR MEMBER_PASSWORD = '.$this->Database->ParamPrefix.'memberPassword OR MEMBER_EMAIL='.$this->Database->ParamPrefix.'email)' ;
				$row = $this->Database->FetchSqlRow(
					$sql,
					array(
						'memberId' => $idMemberExclude,
						'login' => $login,
						'memberPassword' => $password,
						'email' => $email,
					)
				) ;
				return $row ;
			}
			public function ADActivatedMemberFalseValue()
			{
				return ($this->ADActivatedMemberTrueValue == "1") ? 0 : "1" ;
			}
			public function MustChangePasswordMemberFalseValue()
			{
				return ($this->MustChangePasswordMemberTrueValue == "1") ? 0 : "1" ;
			}
			public function EnableMemberFalseValue()
			{
				return ($this->EnableMemberTrueValue == "1") ? 0 : "1" ;
			}
			public function EnableProfileFalseValue()
			{
				return ($this->EnableProfileTrueValue == "1") ? 0 : "1" ;
			}
			public function EnablePrivilegeFalseValue()
			{
				return ($this->EnablePrivilegeTrueValue == "1") ? 0 : "1" ;
			}
			public function SqlAllMembers()
			{
				$sql = '' ;
				if($this->MemberTable == '' || $this->ProfileTable == '' || $this->IdMemberColumn == '' || $this->LoginMemberColumn == '' || $this->PasswordMemberColumn == '' || $this->IdProfileColumn == '')
				{
					die('Definition du membership non complête !!!') ;
				}
				$sql .= 'SELECT 1 MEMBER_REQUEST' ;
				$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->IdMemberColumn).' MEMBER_ID' ;
				{
					$sql .= ', ' ;
					if($this->LoginMemberCaseInsensitive)
					{
						$sql .= 'lower(' ;
					}
					$sql .= $this->Database->EscapeFieldName("MEMBER_TABLE", $this->LoginMemberColumn) ;
					if($this->LoginMemberCaseInsensitive)
					{
						$sql .= ')' ;
					}
					$sql .= ' MEMBER_LOGIN' ;
				}
				$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->PasswordMemberColumn).' MEMBER_PASSWORD' ;
				if($this->EmailMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->EmailMemberColumn).' MEMBER_EMAIL' ;
				}
				if($this->FirstNameMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->FirstNameMemberColumn).' MEMBER_FIRST_NAME' ;
				}
				if($this->LastNameMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->LastNameMemberColumn).' MEMBER_LAST_NAME' ;
				}
				if($this->AddressMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->AddressMemberColumn).' MEMBER_ADDRESS' ;
				}
				if($this->ContactMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->ContactMemberColumn).' MEMBER_CONTACT' ;
				}
				if($this->EnableMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->EnableMemberColumn).' MEMBER_ENABLE' ;
				}
				if($this->ADActivatedMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->ADActivatedMemberColumn).' MEMBER_AD_ACTIVATED' ;
				}
				if($this->MustChangePasswordMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->MustChangePasswordMemberColumn).' MEMBER_MUST_CHANGE_PASSWORD' ;
				}
				else
				{
					$sql .= ', 0 MEMBER_MUST_CHANGE_PASSWORD' ;
				}
				if($this->ADUserMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->ADUserMemberColumn).' MEMBER_AD_USER' ;
				}
				else
				{
					$sql .= ', \'\' MEMBER_AD_USER' ;
				}
				if($this->ADDomainMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->ADDomainMemberColumn).' MEMBER_AD_DOMAIN' ;
				}
				else
				{
					$sql .= ', \''.$this->ADDomainMemberDefaultValue.'\' MEMBER_AD_DOMAIN' ;
				}
				if($this->ProfileMemberColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->ProfileMemberColumn).' MEMBER_PROFILE' ;
				}
				$sql .= ', '.$this->Database->EscapeFieldName("PROFILE_TABLE", $this->IdProfileColumn).' PROFILE_ID' ;
				if($this->TitleProfileColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("PROFILE_TABLE", $this->TitleProfileColumn).' PROFILE_TITLE' ;
				}
				if($this->DescriptionProfileColumn != '')
				{
					$sql .= ', '.$this->Database->EscapeFieldName("PROFILE_TABLE", $this->DescriptionProfileColumn).' PROFILE_DESCRIPTION' ;
				}
				$sql .= $this->ExtraColsAllMembers() ;
				$sql .= ' FROM '.$this->Database->EscapeTableName($this->MemberTable).' MEMBER_TABLE LEFT JOIN '.$this->Database->EscapeTableName($this->ProfileTable).' PROFILE_TABLE on '.$this->Database->EscapeFieldName("MEMBER_TABLE", $this->ProfileMemberColumn).' = '.$this->Database->EscapeFieldName("PROFILE_TABLE", $this->ProfileMemberForeignKey) ;
				$sql .= $this->ExtraExprAllMembers() ;
				return $sql ;
			}
			protected function ExtraColsAllMembers()
			{
			}
			protected function ExtraExprAllMembers()
			{
			}
			public function SqlAllRoles()
			{
				$sql = '' ;
				$sql .= 'select ' ;
				$sql .= $this->IdRoleColumn.' ROLE_ID' ;
				$sql .= ", ".(($this->NameRoleColumn != '') ? $this->Database->EscapeFieldName($this->RoleTable, $this->NameRoleColumn) : "''").' ROLE_NAME' ;
				$sql .= ", ".(($this->TitleRoleColumn != '') ? $this->Database->EscapeFieldName($this->RoleTable, $this->TitleRoleColumn) : "''").' ROLE_TITLE' ;
				$sql .= ", ".(($this->DescriptionRoleColumn != '') ? $this->Database->EscapeFieldName($this->RoleTable, $this->DescriptionRoleColumn) : "''").' ROLE_DESCRIPTION' ;
				$sql .= ", ".(($this->EnableRoleColumn != '') ? $this->Database->EscapeFieldName($this->RoleTable, $this->EnableRoleColumn) : "''").' ROLE_ENABLED' ;
				$sql .= ' from '.$this->Database->EscapeTableName($this->RoleTable) ;
				return $sql ;
			}
			public function SqlAllProfiles()
			{
				$sql = '' ;
				$sql .= 'select ' ;
				$sql .= $this->Database->EscapeFieldName('PROFILE_TABLE', $this->IdProfileColumn).' PROFILE_ID' ;
				$sql .= ", ".(($this->TitleProfileColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->TitleProfileColumn) : "''").' PROFILE_TITLE' ;
				$sql .= ", ".(($this->DescriptionProfileColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->DescriptionProfileColumn) : "''").' PROFILE_DESCRIPTION' ;
				$sql .= ", ".(($this->EnableProfileColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->EnableProfileColumn) : "''").' PRIVILEGE_ENABLED' ;
				$sql .= ' from '.$this->Database->EscapeTableName($this->ProfileTable).' PROFILE_TABLE' ;
				return $sql ;
			}
			public function SqlProfilesForNewRole()
			{
				$sql = '' ;
				$sql .= 'select ' ;
				$sql .= '0 ROLE_ID' ;
				$sql .= ", ".$this->Database->EscapeFieldName('PROFILE_TABLE', $this->IdProfileColumn).' PROFILE_ID' ;
				$sql .= ", ".(($this->NameRoleColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->TitleProfileColumn) : "''").' PROFILE_TITLE' ;
				$sql .= ", ".(($this->DescriptionProfileColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->DescriptionProfileColumn) : "''").' PROFILE_DESCRIPTION' ;
				$sql .= ", ".(($this->EnableProfileColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->EnableProfileColumn) : "'0'").' PROFILE_ENABLED' ;
				$sql .= ", 1 PRIVILEGE_ENABLED" ;
				$sql .= ' from '.$this->Database->EscapeTableName($this->ProfileTable).' PROFILE_TABLE' ;
				return $sql ;
			}
			public function SqlProfilesForRole($idRole=0)
			{
				$sql = '' ;
				$sql .= 'select ' ;
				$sql .= $this->Database->EscapeFieldName('ROLE_TABLE', $this->IdRoleColumn).' ROLE_ID' ;
				$sql .= ", ".$this->Database->EscapeFieldName('PROFILE_TABLE', $this->IdProfileColumn).' PROFILE_ID' ;
				$sql .= ", ".(($this->NameRoleColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->TitleProfileColumn) : "''").' PROFILE_TITLE' ;
				$sql .= ", ".(($this->DescriptionProfileColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->DescriptionProfileColumn) : "''").' PROFILE_DESCRIPTION' ;
				$sql .= ", ".(($this->EnableProfileColumn != '') ? $this->Database->EscapeFieldName('PROFILE_TABLE', $this->EnableProfileColumn) : "'0'").' PROFILE_ENABLED' ;
				$sql .= ", ".(($this->EnablePrivilegeColumn != '') ? $this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->EnablePrivilegeColumn) : "'0'").' PRIVILEGE_ENABLED' ;
				$sql .= ' from '.$this->Database->EscapeTableName($this->ProfileTable).' PROFILE_TABLE' ;
				$sql .= ' left join '.$this->Database->EscapeTableName($this->PrivilegeTable).' PRIVILEGE_TABLE' ;
				$sql .= ' ON '.$this->Database->EscapeFieldName('PROFILE_TABLE', $this->ProfilePrivilegeForeignKey).' = '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->ProfilePrivilegeColumn) ;
				$sql .= ' left join '.$this->Database->EscapeTableName($this->RoleTable).' ROLE_TABLE' ;
				$sql .= ' ON '.$this->Database->EscapeFieldName('ROLE_TABLE', $this->RolePrivilegeForeignKey).' = '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->RolePrivilegeColumn) ;
				return $sql ;
			}
			public function SqlRolesForNewProfile()
			{
				$sql = '' ;
				$sql .= 'select ' ;
				$sql .= '0 PROFILE_ID' ;
				$sql .= ", ".$this->Database->EscapeFieldName('ROLE_TABLE', $this->IdRoleColumn).' ROLE_ID' ;
				$sql .= ", ".(($this->TitleRoleColumn != '') ? $this->Database->EscapeFieldName('ROLE_TABLE', $this->TitleRoleColumn) : "''").' ROLE_TITLE' ;
				$sql .= ", ".(($this->DescriptionRoleColumn != '') ? $this->Database->EscapeFieldName('ROLE_TABLE', $this->DescriptionRoleColumn) : "''").' ROLE_DESCRIPTION' ;
				$sql .= ", ".(($this->EnableRoleColumn != '') ? $this->Database->EscapeFieldName('ROLE_TABLE', $this->EnableRoleColumn) : "'0'").' ROLE_ENABLED' ;
				$sql .= ", 1 PRIVILEGE_ENABLED" ;
				$sql .= ' from '.$this->Database->EscapeTableName($this->RoleTable).' ROLE_TABLE' ;
				return $sql ;
			}
			public function SqlRolesForProfile($idProfile=0)
			{
				$sql = '' ;
				$sql .= 'select ' ;
				$sql .= $this->Database->EscapeFieldName('PROFILE_TABLE', $this->IdProfileColumn).' PROFILE_ID' ;
				$sql .= ", ".$this->Database->EscapeFieldName('ROLE_TABLE', $this->IdRoleColumn).' ROLE_ID' ;
				$sql .= ", ".(($this->NameRoleColumn != '') ? $this->Database->EscapeFieldName('ROLE_TABLE', $this->NameRoleColumn) : "''").' ROLE_TITLE' ;
				$sql .= ", ".(($this->DescriptionRoleColumn != '') ? $this->Database->EscapeFieldName('ROLE_TABLE', $this->DescriptionRoleColumn) : "''").' ROLE_DESCRIPTION' ;
				$sql .= ", ".(($this->EnableRoleColumn != '') ? $this->Database->EscapeFieldName('ROLE_TABLE', $this->EnableRoleColumn) : "'0'").' ROLE_ENABLED' ;
				$sql .= ", ".(($this->EnablePrivilegeColumn != '') ? $this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->EnablePrivilegeColumn) : "'0'").' PRIVILEGE_ENABLED' ;
				$sql .= ' from '.$this->Database->EscapeTableName($this->RoleTable).' ROLE_TABLE' ;
				$sql .= ' left join '.$this->Database->EscapeTableName($this->PrivilegeTable).' PRIVILEGE_TABLE' ;
				$sql .= ' ON '.$this->Database->EscapeFieldName('ROLE_TABLE', $this->RolePrivilegeForeignKey).' = '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->RolePrivilegeColumn) ;
				$sql .= ' left join '.$this->Database->EscapeTableName($this->ProfileTable).' PROFILE_TABLE' ;
				$sql .= ' ON '.$this->Database->EscapeFieldName('PROFILE_TABLE', $this->ProfilePrivilegeForeignKey).' = '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->ProfilePrivilegeColumn) ;
				return $sql ;
			}
			protected function SqlValidateConnection()
			{
				$sql = 'SELECT ALL_MEMBER_TABLE.*' ;
				{
					$sql .= ', ' ;
					if($this->LoginMemberCaseInsensitive)
					{
						$sql .= 'lower(' ;
					}
					$sql .= $this->Database->ParamPrefix.'RequestLogin' ;
					if($this->LoginMemberCaseInsensitive)
					{
						$sql .= ')' ;
					}
					$sql .= ' REQUEST_LOGIN' ;
				}
				{
					$sql .= ', ' ;
					if($this->PasswordMemberExpr != '')
					{
						$sql .= $this->PasswordMemberExpr.'(' ;
					}
					$sql .= $this->Database->ParamPrefix.'RequestPassword' ;
					if($this->PasswordMemberExpr != '')
					{
						$sql .= ')' ;
					}
					$sql .= ' REQUEST_PASSWORD' ;
				}
				$sql .= ' FROM ('.$this->SqlAllMembers().') ALL_MEMBER_TABLE WHERE MEMBER_LOGIN='.$this->Database->ParamPrefix.'RequestLogin' ;
				if($this->EnableMemberColumn != '')
				{
					$sql .=  ' AND MEMBER_ENABLE='.$this->Database->ParamPrefix.'CorrectEnabled' ;
				}
				return $sql ;
			}
			public function ValidateConnection($login, $password)
			{
				$sql = $this->SqlValidateConnection() ;
				$this->LastValidateError = AkSqlMembership::VALIDATE_ERROR_NONE ;
				$params = array('RequestLogin' => $login, 'RequestPassword' => $password) ;
				if($this->EnableMemberColumn != "")
				{
					$params["CorrectEnabled"] = $this->EnableMemberTrueValue ;
				}
				$requestRow = $this->Database->FetchSqlRow(
					$sql,
					$params
				) ;
				// print_r($this->Database) ;
				$idMember = $this->IdMemberNotFoundValue ;
				$ok = 0 ;
				if(count($requestRow))
				{
					if($this->EnableMemberColumn == '' || $requestRow["MEMBER_ENABLE"] == $this->EnableMemberTrueValue)
					{
						$adActivated = 0 ;
						if($this->ADActivatedMemberColumn != "")
						{
							if($requestRow["MEMBER_AD_ACTIVATED"] == $this->ADActivatedMemberTrueValue)
							{
								$adActivated = 1 ;
							}
						}
						if($adActivated == 0)
						{
							if($requestRow["REQUEST_PASSWORD"] == $requestRow["MEMBER_PASSWORD"])
							{
								$idMember = $requestRow["MEMBER_ID"] ;
							}
							else
							{
								$this->LastValidateError = AkSqlMembership::VALIDATE_ERROR_PASSWORD_INCORRECT ;
							}
						}
						else
						{
						}
					}
					else
					{
						$this->LastValidateError = AkSqlMembership::VALIDATE_ERROR_MEMBER_NOT_ENABLED ;
					}
				}
				else
				{
					$this->LastValidateError = AkSqlMembership::VALIDATE_ERROR_MEMBER_NOT_FOUND ;
				}
				return $idMember ;
			}
			protected function SqlFetchMemberRow($memberId)
			{
				$sql = 'SELECT ALL_MEMBER_TABLE.* FROM ('.$this->SqlAllMembers().') ALL_MEMBER_TABLE WHERE MEMBER_ID='.$this->Database->ParamPrefix.'MemberId' ;
				return $sql ;
			}
			protected function SqlFetchMemberRowByLogin($login)
			{
				$sql = 'SELECT ALL_MEMBER_TABLE.* FROM ('.$this->SqlAllMembers().') ALL_MEMBER_TABLE WHERE MEMBER_LOGIN='.$this->Database->ParamPrefix.'MemberLogin' ;
				return $sql ;
			}
			public function FetchMemberRow($memberId)
			{
				return $this->Database->FetchSqlRow(
					$this->SqlFetchMemberRow($memberId),
					array(
						"MemberId" => $memberId
					)
				) ;
			}
			public function FetchMemberRowByLogin($memberId)
			{
				return $this->Database->FetchSqlRow(
					$this->SqlFetchMemberRowByLogin($memberId),
					array(
						"MemberId" => $memberId
					)
				) ;
			}
			protected function SqlAllPrivileges()
			{
				$sql = "" ;
				$sql .= 'SELECT 0 PROFILE_REQUEST' ;
				if($this->IdProfileColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('PROFILE_TABLE', $this->IdProfileColumn).' PROFILE_ID' ;
				}
				if($this->TitleProfileColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('PROFILE_TABLE', $this->TitleProfileColumn).' PROFILE_TITLE' ;
				}
				if($this->DescriptionProfileColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('PROFILE_TABLE', $this->DescriptionProfileColumn).' PROFILE_DESCRIPTION' ;
				}
				if($this->IdPrivilegeColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->IdPrivilegeColumn).' PRIVILEGE_ID' ;
				}
				if($this->RolePrivilegeColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->RolePrivilegeColumn).' PRIVILEGE_ROLE' ;
				}
				if($this->ProfilePrivilegeColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->ProfilePrivilegeColumn).' PRIVILEGE_PROFILE' ;
				}
				if($this->EnablePrivilegeColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->EnablePrivilegeColumn).' PRIVILEGE_ENABLED' ;
				}
				if($this->IdRoleColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('ROLE_TABLE', $this->IdRoleColumn).' ROLE_ID' ;
				}
				if($this->NameRoleColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('ROLE_TABLE', $this->NameRoleColumn).' ROLE_NAME' ;
				}
				if($this->TitleRoleColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('ROLE_TABLE', $this->TitleRoleColumn).' ROLE_TITLE' ;
				}
				if($this->DescriptionRoleColumn != "")
				{
					$sql .= ', '.$this->Database->EscapeFieldName('ROLE_TABLE', $this->DescriptionRoleColumn).' ROLE_DESCRIPTION' ;
				}
				$sql .= ' FROM '.$this->Database->EscapeTableName($this->RoleTable).' ROLE_TABLE' ;
				$sql .= ' LEFT JOIN '.$this->Database->EscapeTableName($this->PrivilegeTable).' PRIVILEGE_TABLE' ;
				$sql .= ' ON '.$this->Database->EscapeFieldName('ROLE_TABLE', $this->RolePrivilegeForeignKey).'='.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->RolePrivilegeColumn) ;
				$sql .= ' LEFT JOIN '.$this->Database->EscapeTableName($this->ProfileTable).' PROFILE_TABLE' ;
				$sql .= ' ON '.$this->Database->EscapeFieldName('PRIVILEGE_TABLE', $this->ProfilePrivilegeColumn).'='.$this->Database->EscapeFieldName('PROFILE_TABLE', $this->ProfilePrivilegeForeignKey) ;
				return $sql ;
			}
			protected function SqlFetchProfileRows()
			{
				$sql = 'SELECT T1.* FROM ('.$this->SqlAllPrivileges().') T1 WHERE PROFILE_ID='.$this->Database->ParamPrefix.'ProfileId' ;
				return $sql ;
			}
			public function FetchProfileRows($profileId)
			{
				return $this->Database->FetchSqlRows(
					$this->SqlFetchProfileRows(),
					array(
						"ProfileId" => $profileId
					)
				) ;
			}
			public function InsertMemberRow($memberRow)
			{
				if($this->PasswordMemberExpr != '')
				{
					$memberRow[$this->Database->ExprKeyName][$this->PasswordMemberColumn] = $this->PasswordMemberExpr.'('.$this->ExprParamPattern.')' ;
				}
				$ok = $this->Database->InsertRow(
					$this->MemberTable,
					$memberRow
				) ;
				return $ok ;
			}
			public function UpdateMemberRow($memberId, $memberRow)
			{
				if($this->PasswordMemberExpr != '')
				{
					$memberRow[$this->Database->ExprKeyName][$this->PasswordMemberColumn] = $this->PasswordMemberExpr.'('.$this->ExprParamPattern.')' ;
				}
				$ok = $this->Database->UpdateRow(
					$this->MemberTable,
					$memberRow,
					$this->Database->EscapeFieldName($this->MemberTable, $this->IdMemberColumn).' = '.$this->Database->ParamPrefix.'IdCurrentMember',
					array(
						'IdCurrentMember' => $memberId
					)
				) ;
				return $ok ;
			}
			public function DeleteMemberRow($memberId)
			{
				$ok = $this->Database->DeleteRow(
					$this->MemberTable,
					$this->Database->EscapeFieldName($this->MemberTable, $this->IdMemberColumn).' = '.$this->Database->ParamPrefix.'IdCurrentMember',
					array('IdCurrentMember' => $memberId)
				) ;
				return $ok ;
			}
			public function InsertProfileRow($profileRow)
			{
				$ok = $this->Database->InsertRow(
					$this->ProfileTable,
					$profileRow
				) ;
				return $ok ;
			}
			public function UpdateProfileRow($profileId, $profileRow)
			{
				$ok = $this->Database->UpdateRow(
					$this->ProfileTable,
					$profileRow,
					$this->Database->EscapeFieldName($this->ProfileTable, $this->IdProfileColumn).' = '.$this->Database->ParamPrefix.'IdCurrentProfile',
					array(
						'IdCurrentProfile' => $profileId
					)
				) ;
				return $ok ;
			}
			public function DeleteProfileRow($profileId)
			{
				$ok = $this->Database->DeleteRow(
					$this->ProfileTable,
					$this->Database->EscapeFieldName($this->ProfileTable, $this->IdProfileColumn).' = '.$this->Database->ParamPrefix.'IdCurrentProfile',
					array('IdCurrentProfile' => $profileId)
				) ;
				return $ok ;
			}
			public function InsertRoleRow($roleRow)
			{
				$ok = $this->Database->InsertRow(
					$this->RoleTable,
					$roleRow
				) ;
				return $ok ;
			}
			public function UpdateRoleRow($roleId, $roleRow)
			{
				$ok = $this->Database->UpdateRow(
					$this->RoleTable,
					$roleRow,
					$this->Database->EscapeFieldName($this->RoleTable, $this->IdRoleColumn).' = '.$this->Database->ParamPrefix.'IdCurrentRole',
					array(
						'IdCurrentRole' => $roleId
					)
				) ;
				return $ok ;
			}
			public function DeleteRoleRow($roleId)
			{
				$ok = $this->Database->DeleteRow(
					$this->RoleTable,
					$this->Database->EscapeFieldName($this->RoleTable, $this->IdRoleColumn).' = '.$this->Database->ParamPrefix.'IdCurrentRole',
					array('IdCurrentRole' => $roleId)
				) ;
				return $ok ;
			}
		}		
		class AkSqlMember extends AkMember
		{
		}
		class AkSqlProfile extends AkProfile
		{
		}
	}
	
?>