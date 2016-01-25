<?php
session_start();

function replace_accents($str) {
  $str = htmlentities($str, ENT_COMPAT, "UTF-8");
  $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/','$1',$str);
  return html_entity_decode($str);
}

include_once("class_Datos.php"); 
//include_once(RUTAP."include/funciones_php.php");

class Fachada
{
	private $mbd;    //____variable que contiene la conexion activa  a la base de datos

	public function abrir($bd, $servidor, $usuario, $clave, $puerto)
	{    //____ini_open
		$this->mbd=new datos($bd, $servidor, $usuario, $clave, $puerto);
		$this->mbd->conectar();				     
	}    //____fii_open

        public function insertar($tabla,$columnaValor)
	{    //____ini_insertar
         		$clave;
			$valor;
                        $sql;
			$col='(';    //____necesario para concatenar las columnas a ser afectadas en la tabla	
			$val='(';    //____necesario para concatenar los valores a ser insertados en la tabla	
	
			//_____recorremos el arreglo asociativo para extraer los valores pasados
			while(list($clave,$valor)=each($columnaValor)) 
			{	//____while_ini
				$col.=$clave.', ';
				//$val.="'".$valor."', ";
				$val.=$valor.", ";
			}	//____while_fin

			//____recorremos el arreglo asociativo para extraer los valores pasados
			$col=substr($col,0,strlen($col)-2);	//____retiro el ultimo caracter ingresado por el ciclo
			$val=substr($val,0,strlen($val)-2);	//____retiro el ultimo caracter ingresado por el ciclo
			$col.=')';//cerramos las columnas
			$val.=')';//cerramos los valores ha ingresar
	
			//____construimos el string sql que vamos ha ejecutar
			$sql='INSERT INTO '.$tabla.' '.$col.' VALUES '.$val;
			//En caso de ser campo DATE y la fecha la queremos dejar vacia. Mandamos DEFAULT pero debemos quitar '' para que acepte el dato.
			//$sql=str_replace("'DEFAULT'","DEFAULT",$sql);			
			$resp=$this->mbd->consulta($sql);
//$F=fopen("C:/Archivos de programa/Apache Group/Apache2/htdocs/sicodi/registros/insertar.txt","a");
$F=fopen($_SERVER["DOCUMENT_ROOT"].RUTA."/registros/insertar.txt","a");
fputs($F,$_SESSION["usuario"]." ".date("d/m/Y h:i:s a", time())."\r\n".$sql."\r\n\r\n");


			if($resp==UNO)
			{    //____Verificamos que la  consulta se ejecuto correctamente
				$accion="I";//registramos la accion como insertar
				//$this->registrarBitacora($tabla,$accion,$sql);
			}
			return $resp;
				//return $sql;
		}	//____fin_insertar


        public function modificar($tabla, $columnaValor, $condicion)
        {   //____ini_modificar
	    	while(list($clave,$valor)=each($columnaValor))  
            	{
	        	$cadena.=$clave."=".$valor.", ";
            	}                        
            	$cadena=substr($cadena,0,strlen($cadena)-2);
            	$sql='UPDATE '.$tabla.' SET '.$cadena.' WHERE '.$condicion;
//$F=fopen("C:/Archivos de programa/Apache Group/Apache2/htdocs/sicodi/registros/modificar.txt","a");
$F=fopen($_SERVER["DOCUMENT_ROOT"].RUTA."/registros/modificar.txt","a");
fputs($F,$_SESSION["usuario"]." ".date("d/m/Y h:i:s a", time())."\r\n".$sql."\r\n\r\n");
		$resp=$this->mbd->consulta($sql);
		
		if($resp==UNO)
		{	//____Verificamos que la  consulta se ejecuto correctamente
			$accion="M";//registramos la accion como modificar
				//$this->registrarBitacora($tabla,$accion,$sql);
		}
		return $resp;
		//return $sql;
	}	//____fin_modificar 





	public function eliminar($tabla, $condicion)
        {	//____ini_eliminar
			$sql='DELETE FROM '.$tabla.' WHERE '.$condicion;
//$F=fopen("C:/Archivos de programa/Apache Group/Apache2/htdocs/sicodi/registros/eliminar.txt","a");
$F=fopen($_SERVER["DOCUMENT_ROOT"].RUTA."registros/eliminar.txt","a");
fputs($F,$_SESSION["usuario"]." ".date("d/m/Y h:i:s a", time())."\r\n".$sql."\r\n\r\n");
			$resp=$this->mbd->consulta($sql);
			if($resp==UNO)
			{	//____Verificamos que la  consulta se ejecuto correctamente
				$accion="E";//registramos la accion como eliminar
				//$this->registrarBitacora($tabla,$accion,$sql);
			}
            return $resp;
            //return $sql;
        }	//____fin_eliminar 

        public function consultar($sql,$tipo)
        {	//____ini_consultar
			if ($_SESSION["usuario"]!="")
			{
				//$F=fopen("C:/Archivos de programa/Apache Group/Apache2/htdocs/sicodi/registros/consultar.txt","a");
				$F=fopen($_SERVER["DOCUMENT_ROOT"].RUTA."registros/consultar.txt","a");
				fputs($F,$_SESSION["usuario"]." ".date("d/m/Y h:i:s a", time())."\r\n".$sql."\r\n\r\n");
			}
	        $this->mbd->consulta($sql);                    
            switch($tipo)
            {	//____swicth_ini
	        case 'OBJETO':
                	return $this->mbd->getObjeto();
                	break;
                case 'ARREGLO':
                	return $this->mbd->getArregloAsociativo();
			break;
		case 'ARREGLO_ASOC':
			return $this->mbd->getArregloAsociativo();
			break;
		case 'N_FILAS':
			return $this->mbd->getNumRegistro();
			break;
		case 'RECURSO':
			return $this->mbd->getRecurso();
			break;
		default:
			return CERO;
			break;
		}	//____swicth_fin
        }	//____fin_consultar


	public function devolverObjeto()
        {
            return $this->mbd->getObjeto();
        }
}	

?>
