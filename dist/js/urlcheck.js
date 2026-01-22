 var getQuery = function () {
    var url_params = {};
    var query = window.location.search.substring(1);
      if (query.length === 0) return false;
      var vars = query.split("&");
      for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if (typeof url_params[pair[0]] === "undefined") {
          url_params[pair[0]] = pair[1];
        } else if (typeof url_params[pair[0]] === "string") {
          var arr = [ url_params[pair[0]], pair[1] ];
          url_params[pair[0]] = arr;
        } else {
          url_params[pair[0]].push(pair[1]);
        }
      } 
     return url_params;
};