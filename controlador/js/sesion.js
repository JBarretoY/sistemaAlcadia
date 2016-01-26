function iniciarSesion(){

	var usuario = $("#usu").val();
	var clave   = $("#pass").val();

	if($.trim(usuario) == '' && $.trim(clave) == ''){
		alert("ERROR... Existen los campos estan vacios");
		return;
	}
	else if($.trim(usuario) == ''){
		alert("ERROR... El campo del usuario esta vacio");
		$("#usu").focus();
		return;
	}
	else if($.trim(clave) == ''){
		alert("ERROR... El campo de la clave esta vacio");
		$("#pass").focus();
		return;
	}else{
		$.ajax( {
			type : 'POST',
			url : "controlador/trans/tSesion.php",
			data :	{ accion: "iniciarSesion", usuario: usuario, clave: clave},
			error : function(xhr, ajaxOptions, thrownError) {
				alert("Ups... Algo esta mal :(");
			},
			success : function(data) {
				//console.log(data);
				var data = eval("(" + data + ")");
				console.log(data);
				if(data){
					moverDirectorioSesion();
					//alert("ERROR... El usuario o la contraseña no coinciden.");
				}else{
					alert("ERROR... Los Datos Introducidos Son Incorrectos");
					return;
				}
			}
		});
	}
}

function moverDirectorioSesion(){
	window.location="vista/index.php";
}

function isActiveSesion(){
	$.ajax( {
		type : 'POST',
		url : "../controlador/trans/tSesion.php",
		data :	{ accion: "isActiveSesion"},
		error : function(xhr, ajaxOptions, thrownError) {
			alert("Ups... Algo esta mal :(");
		},
		success : function(data) {
			
			var data = eval("(" + data + ")");
			console.log(data);
			if(!data){
				alert("ERROR...! La Sesion se ha cerrado por inactividad");
				window.location = "../";
			}
		}
	});
}

function cerrarSesion(){
	$.ajax( {
		type : 'POST',
		url : "../controlador/trans/tSesion.php",
		data :	{ accion: "cerrarSesion"},
		error : function(xhr, ajaxOptions, thrownError) {
			alert("Ups... Algo esta mal :(");
		},
		success : function(data) {
			
			var data = eval("(" + data + ")");
			console.log(data);
			if(data){
				moverDirectorioSesionCerrado();
			}
		}
	});
}

function moverDirectorioSesionCerrado(){
	window.location = "../";
}