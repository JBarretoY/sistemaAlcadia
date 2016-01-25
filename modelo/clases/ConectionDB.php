<?php
include 'adodb5/adodb.inc.php';
$ADODB_CACHE_DIR = '../../tmp/';
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	class ConectionDB{

		private $sManejadorBD;
		private $oBD;
		private $oRS;
		private $sql;

		private $mnombreBD;
		private $mservidor;
		private $musuario;
		private $mclave;
		private $mpuerto;
		//____Constructor de la clase
		function __construct($mnombreBD, $mservidor, $musuario, $mclave, $mpuerto, $sManejadorBD='postgres'){

			 $this->sManejadorBD = $sManejadorBD;
			 $this->mnombreBD = $mnombreBD;
			 $this->mservidor = $mservidor;
			 $this->musuario = $musuario;
			 $this->mclave = $mclave;
			 $this->mpuerto = $mpuerto;
			 /*$valor=$this->mnombreBD.', '.$this->mservidor.', '.$this->musuario.', '.$this->mclave.', '.$this->mpuerto;
			 $XX=fopen($_SERVER["DOCUMENT_ROOT"].RUTA."miconexion.txt","w");
                fputs($XX,$valor);*/
		}

		function __destruct(){

		}
		//Metodo para ralizar la conexion a la base de datos
		function conectar($bDepurar=false){

			$this->oBD = &ADONewConnection($this->sManejadorBD);
			if(!is_object($this->oBD)){
				die('Error de Conexion con el Manejador de base de Datos');
			}//if
			$this->oBD->debug = $bDepurar;
			if(!$this->oBD->IsConnected() && !$this->oBD->Connect($this->mservidor, $this->musuario, $this->mclave, 
				$this->mnombreBD)){
				die('Error de Conexion parametros de conexion invalidos');
			}//if

		}//____fin conectar
		function desconectar(){// metodo para desconectar la conexion a la base de datos
			$this->oBD->Close();
		}// metodo para desconectar la conexion a la base de datos
		//____Metodo para realizar la consulta
		function consulta($consulta){
			$this->sql = $consulta;
			$this->oRS = &$this->oBD->Execute($consulta);
			return is_object($this->oRS) ? UNO : CERO;
		}

		//____Metodo que devuelve un arreglo los nombres de las columnas de una o mas tablas
		function getRecurso(){
			return array_keys($this->oRS->fields);
		}
		//____Metodo que devuelve los registros de la consulta como un array asociativo
		function getArregloAsociativo(){
			if(!is_object($this->oRS)){
				var_dump($this->sql, debug_backtrace());
			}//if

			$aArreglo = $this->oRS->GetArray();
			return empty($aArreglo) ? false : $aArreglo;
		}

		//____Metodo que devuelve un registro de la consulta
		function getFila(){
			return $this->oRS->FetchRow();
		}

		//____Metodo que devuelve los registros de la consulta como un objeto
		function getObjeto(){
			return $this->oRS->FetchObject(false);
		}

		function getNumRegistro(){
			return $this->oRS->RecordCount();
		}
		//____Metodo para realizar la consulta multiples recibe como parámetro de entrada un
		//arreglo con todas las consultas
		function consultasMultiples($arreglo){
			$numConsultas = count($arreglo);
			$this->oBD->StartTrans();
			for ($i=0;$i<$numConsultas;$i++){
				$sql=$arreglo[$i];

				$XX=fopen("/var/www/html/proyecto_mery/transacciones.txt","a");
				fputs($XX,date("d/m/Y h:i a",time())."\r\n".$sql."\r\n\r\n");
				$this->sql = $arreglo[$i];
				$this->oBD->Execute($arreglo[$i]);
			}
			$rr=$this->oBD->CompleteTrans();

			return ($rr)? UNO : CERO;
		}
	}//____fin sigaDatos
?>
