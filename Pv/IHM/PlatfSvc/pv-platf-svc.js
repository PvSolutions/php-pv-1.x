PvPlatfSvc = {} ;
PvPlatfSvc.scriptEnCours = {} ;
PvPlatfSvc.AdressePage = function() {
	var _self = this ;
	_self._paramsGet = {} ;
	_self.redirige = function(url) {
		window.location = url ;
	} ;
	_self.calculeParamsGet = function() {
		var parser = document.createElement("a") ;
		parser.href = window.location.href ;
		if(parser.query !== undefined) {
			var paramsQueryStr = parser.query.split("&") ;
			for(var i=0; i<paramsQueryStr.length; i++) {
				var attrsParamGet = paramsQueryStr[i] ;
				_self.paramsGet[attrsParamGet[0]] = (attrsParamGet.length > 1) ? attrsParamGet[1] : null ;
			}
		}
	} ;
	_self.paramsGet = function(nomParams) {
		var result = {} ;
		for(var i=0; i<nomParams.length; i++) {
			result[nomParams[i]] = _self.paramGet(nomParam[i]) ;
		}
		return result ;
	}
	_self.paramGet = function(nomParam, valeurDefaut) {
		if(valeurDefaut === undefined) {
			valeurDefaut = '' ;
		}
		return (_self._paramsGet[nomParam] !== undefined) ? _self._paramsGet[nomParam] : valeurDefaut ;
	}
	_self.calculeParamsGet() ;
} ;
PvPlatfSvc.BoiteDlg = function() {
	var _self = this ;
	_self.afficheSpec = function(titre, message, niveau) {
		var srcDlg = "natif" ;
		if(jQuery.dialog) {
			if(jQuery("#boite-dlg-platfsvc").length == 1) {
				srcDlg = "jqueryui" ;
			}
		}
		switch(srcDlg) {
			
			case "natif" :
			{
				alert(niveau + "# - " + titre + "\n" + message) ;
			}
			break ;
		}
	} ;
	_self.afficheNotif = function(titre, message) {
		_self.afficheSpec(titre, message, "ok") ;
	} ;
	_self.afficheErreur = function(titre, message) {
		_self.afficheSpec(titre, message, "erreur") ;
	} ;
	_self.afficheException = function(titre, message) {
		_self.afficheSpec(titre, message, "exception") ;
	} ;
	_self.afficheExceptionAjax = function(jqXHR, textStatus, errorThrown) {
		var msg = "" ;
		if(textStatus !== undefined && textStatus !== null)	{
			msg = textStatus.toString() ;
		}
		if(jqXHR.status !== undefined && jqXHR.status !== null && jqXHR.status !== 200) {
			msg += " : " + jqXHR.status.toString() ;
		}
		_self.afficheException("Erreur AJAX", "") ;
	} ;
} ;
PvPlatfSvc.SessionStorage = function() {
    var _self = this ;
    _self.setCookie = function (cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    };
    _self.getCookie = function (cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)===' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length,c.length);
            }
        }
        return "";
    } ;
    _self.supportsStorage = function() {
        return typeof(sessionStorage) !== "undefined" ;
    };
    _self.encodeKey = function (key) {
        return key ;
    };
    _self.setKey = function (key, value) {
        key = _self.encodeKey(key) ;
        if (_self.supportsStorage()) {
            sessionStorage.setItem(key, value) ;
        }
        else {
            _self.setCookie(key, value, 30) ;
        }
    };
    _self.getKey = function (key) {
        key = _self.encodeKey(key) ;
        if (_self.supportsStorage()) {
            return sessionStorage.getItem(key) ;
        }
        else {
            return _self.getCookie(key) ;
        }
    };
    _self.removeKey = function (key) {
        key = _self.encodeKey(key) ;
        if (_self.supportsStorage()) {
            sessionStorage.removeItem(key) ;
        }
        else {
            _self.setCookie(key, "", -1) ;
        }
    };
};
PvPlatfSvc.boiteDlg = new PvPlatfSvc.BoiteDlg() ;
PvPlatfSvc.sessionStorage = new PvPlatfSvc.SessionStorage() ;
PvPlatfSvc.adressePage = new PvPlatfSvc.AdressePage() ;
