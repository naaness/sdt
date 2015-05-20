var server = "http://"+window.location.hostname;
$(document).ready(function() {
	$.post(server+'/users/nombre','name=NESTOR', function(datos){
    	console.log(datos);
    }, 'json').fail(function() {
	    console.log("error de conexion");
	});
});