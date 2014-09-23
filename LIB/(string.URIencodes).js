// (STRING.URIENCODES).JS - 	utility in the klib library for
//                 		URI-related encodes.
//                 		Kit Lester 2013



// The following 2 lines make sure that the klib library object exists, and
// then that that the klib.ajax sublibrary object exists

if (!window.klib)	 window.klib	     = {};    // NB: {} is equivalent
if (!window.klib.string) window.klib.string  = {};    // to new Object();




// KLIB.STRING.URIQUESIESOF takes an parameter consisting of data-values only,
// and  returns an  equivalent string in  URI-query format,  with the "value"
// parts URI-encoded (i.e. space into %20, etc.)

klib.string.URIQueriesOf = function (x)
  { var SoFar = ""
    for (n in x) SoFar = (SoFar=="" ? "" : SoFar+"&")+
                         n+"="+
                         encodeURIComponent(x[n])
    return SoFar;
  }
