$(document).on('ready',function(){
	//Escuchador de Eventos
	$('#send').click(function() {
		iniciarSesion();
	});

	$("#close").click(function() {
		cerrarSesion();
	});
});