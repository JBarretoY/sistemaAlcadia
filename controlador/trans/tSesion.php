<?php 
	include_once ("../../modelo/constante.php");
	include_once("../../modelo/clases/ExecuteQuery.php");
	include_once("../../modelo/Sesion.php");	
	// --- Sentencia para capturar cada uno de los parametros enviados en el request --- //
	foreach($_POST as $nombre_campo => $valor){
	   $asignacion = "\$" . $nombre_campo . "='".addslashes($valor)."';"; 
	   eval($asignacion);
	}
	switch($accion){	
		case 'iniciarSesion':
			$obj   = new Sesion();
			$datos = $obj -> iniciarSesion($usuario, $clave);
		break; 
		case 'cerrarSesion':
			$obj = new Sesion();
		    $datos = $obj -> cerrarSesion();
		break;

		case 'isActiveSesion':
			$obj = new Sesion();
		    $datos = $obj -> isActiveSesion();
		break;
		
		default:
	    $datos = "Error accion no encontrada";
	}
   
    $salida = json_encode($datos);
    echo $salida;
?>