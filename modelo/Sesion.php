<?php 
include_once("clases/ExecuteQuery.php");

	class Sesion{

		public function iniciarSesion($usuario,$clave){

			$bd = new ExecuteQuery();
			$bd->abrir(BD, SERVIDOR, USUARIO, CLAVE, PUERTO);

			$finalUsuario = pg_escape_string($usuario);
			$finalClave   = pg_escape_string($clave);

			$sql = "SELECT * FROM usuario WHERE usuario = '$finalUsuario' AND clave = '$finalClave'";

			$output = $bd->consultar($sql, 'ARREGLO');

			if ($output) {

				session_start();

				$obj   = new OAuthProvider();
				$token = $obj->generateToken(20);

				$_SESSION['usuario'] = $output[0]['usuario'];
				$_SESSION['clave']   = $output[0]['clave'];
				$_SESSION['token']	 = $token;
				$_SESSION['bool']    = TRUE;

				return TRUE;
			}else{
				return FALSE;
			}
		}

		public function cerrarSesion(){

			session_start();
			session_destroy();
			session_unset();

			return TRUE;
		}	

		public function isActiveSesion(){
			session_start();

			if (empty($_SESSION['usuario']) || empty($_SESSION['clave']) || empty($_SESSION['token']) || 
				empty($_SESSION['bool'])) {
				
				return FALSE;
			}else{
				return TRUE;
			}			
		}
	}
?>