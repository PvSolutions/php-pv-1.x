<?php
	
	if(! defined('PV_NOYAU_CLIENT_SOCKET'))
	{
		define('PV_NOYAU_CLIENT_SOCKET', 1) ;
		
		class PvClientSocketBase
		{
			public $Hote = "localhost" ;
			public $Port = 1111 ;
			public $Scheme = "tcp" ;
			public $DelaiExpirOuvr = 5 ;
			public $DelaiExpirRecup = 2 ;
			public $DelaiExpirEnvoi = 2 ;
			public $DelaiExpirTrait = 5 ;
			public $SupportClt = null ;
			public $FermeSupportAuto = 1 ;
			protected function ExtraitUrl()
			{
				return $this->Scheme."://".$this->Hote.":".$this->Port ;
			}
			protected function OuvreSupportClt()
			{
				$this->SupportClt = null ;
                                $this->SupportClt = stream_socket_client($this->ExtraitUrl(), $codeErr, $msgErr, $this->DelaiExpirOuvr) ;
                                if(! $this->SupportClt)
                                {
                                }
			}
			protected function FermeSupportClt()
			{
				if($this->SupportClt == null || ! is_resource($this->SupportClt))
				{
					return ;
				}
				fclose($this->SupportClt) ;
				$this->SupportClt = null ;
			}
		}
	}
	
?>