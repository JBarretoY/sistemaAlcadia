<?php
ini_set('session.gc_maxlifetime',60*60*22);
session_start();
include_once("ConectionDB.php"); 

	class ExecuteQuery{

		private $mbd;    //____variable que contiene la conexion activa  a la base de datos

		public function abrir($bd, $servidor, $usuario, $clave, $puerto){    //____ini_open
			$this->mbd=new ConectionDB($bd, $servidor, $usuario, $clave, $puerto);
			$this->mbd->conectar();				     
		}    //____fii_open
		
		public function cerrar(){    //____ini_cerrar
			$this->mbd->desconectar();				     
		}    //____fii_cerrar
		
        public function insertar($tabla,$columnaValor){    //____ini_insertar
         	$clave;
			$valor;
            $sql;
			$col='(';    //____necesario para concatenar las columnas a ser afectadas en la tabla	
			$val='(';    //____necesario para concatenar los valores a ser insertados en la tabla	
	
			//_____recorremos el arreglo asociativo para extraer los valores pasados
			while(list($clave,$valor)=each($columnaValor)){	//____while_ini
				$col.=$clave.', ';

				//$val.="'".$valor."', ";
				if ($valor=='default')
					$val.=$valor.", ";
				else
					$val.="'".addslashes($valor)."', ";
			}	//____while_fin

			//____recorremos el arreglo asociativo para extraer los valores pasados
			$col=substr($col,0,strlen($col)-2);	//____retiro el ultimo caracter ingresado por el ciclo
			$val=substr($val,0,strlen($val)-2);	//____retiro el ultimo caracter ingresado por el ciclo
			$col.=')';//cerramos las columnas
			$val.=')';//cerramos los valores ha ingresar
	
			//____construimos el string sql que vamos ha ejecutar
			$sql='INSERT INTO '.$tabla.' '.$col.' VALUES '.$val;

			$XX=fopen("/var/www/html/SisAlcaldia/insertar.txt","a");
			fputs($XX,date("d/m/Y h:i a",time())."\r\n".$sql."\r\n\r\n"); 

			$resp=$this->mbd->consulta($sql);

			return $resp;
		}	//____fin_insertar

        public function modificar($tabla, $columnaValor, $condicion)
        {	//____ini_modificar
	        while(list($clave,$valor)=each($columnaValor))  
            {
	            if ($valor=='default')
	            	$cadena.=$clave."=".$valor.", ";
            	else
	            	$cadena.=$clave."='".addslashes($valor)."', ";
            }                        
            $cadena=substr($cadena,0,strlen($cadena)-2);
            $sql='UPDATE '.$tabla.' SET '.$cadena.' WHERE '.$condicion;
$XX=fopen("/var/www/html/SisAlcaldia/modificar.txt","a");
fputs($XX,date("d/m/Y h:i a",time())."\r\n".$sql."\r\n\r\n");

			$resp=$this->mbd->consulta($sql);
            return $resp;
            
		}	//____fin_modificar 


		public function eliminar($tabla, $condicion)
        {	//____ini_eliminar
			$sql='DELETE FROM '.$tabla.' WHERE '.$condicion;
$XX=fopen("/var/www/html/SisAlcaldia/eliminar.txt","a");
fputs($XX,date("d/m/Y h:i a",time())."\r\n".$sql."\r\n\r\n");
			//echo $sql;
			$resp=$this->mbd->consulta($sql);
            return $resp;
        }	//____fin_eliminar 

        public function consultar($sql,$tipo)
        {	//____ini_consultar
$XX=fopen("/var/www/html/SisAlcaldia/consultar.txt","a");
fputs($XX,date("d/m/Y h:i a",time())."\r\n".$sql."\r\n\r\n");
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
				case 'XML':
					return $this->devolverArregloXML($this->mbd->getArregloAsociativo());
				default:
					return CERO;
					break;
			}	//____swicth_fin
        }	//____fin_consultar

		public function devolverObjeto()
        {
            return $this->mbd->getObjeto();
        }		
		

		function procesarTransaccion($tabla,$columnaValor)
        {//Mod por Xio 16-05-08
            $arrayClave = array();
            $valor = array();
            $empezarTransaccion = "begin;";

            for ($i = 0; $i < count($tabla); $i++)//ciclo para las tablas donde se va a insertar
            {
                $arrayClave = array_keys($columnaValor[$i]);//se extraen los indices con claves no secuenciales

                $clave =  implode(",", $arrayClave);//se crea una cadena con las claves extraidas del array (son las columnas de la tabla)

                for ($j = 0; $j < count($arrayClave); $j++)//ciclo para la cantidad de claves del array
                {
                    for ($k = 0; $k < count($columnaValor[$i][$arrayClave[$j]]); $k++)//ciclo de la tabla por las cantidades de valores por clave
                    {
                        $valor[$k][$j] = $columnaValor[$i][$arrayClave[$j]][$k];//se inicializa cada valor por cada clave del array   
                    }
                }

                for ($t = 0; $t <count($valor); $t++)//ciclo de la cantidad de claves
                {
                    $cadenaValor[$t] = "'".$valor[$t][0]."'";
                    for ($q = 1; $q < count($valor[$t]); $q++)//ciclo de la cantidad de valores por clave
                    {   
                        if ($valor[$t][$q] != '')//valida que el valor a insertar no este vacio
                        {
                            $cadenaValor[$t] .= ",'".$valor[$t][$q]."'";//se asiganan los valores de las claves a un array
                        }
                    }
                    $transaccion.= "insert into ".$tabla[$i]."($clave) values (".$cadenaValor[$t].");";//se crea una cadena con los valores de las claves separados por "comas"
                   
                }
            }
            //echo $transaccion;
            $transaccionCompleta = $empezarTransaccion.$transaccion."commit";
            return $this->mbd->consulta($transaccionCompleta);//se ejecuta la transaccion
        }
		
		
		function procesaTransacciones($transaccion,$tabla)
        {                  
            $consultaSecuencia="SELECT last_value as secuencia FROM $tabla";
            $objetoSecuencia=$this->consultar($consultaSecuencia,'OBJETO');       
            $valorInicialSecuencia=$objetoSecuencia->secuencia;
           
            $inicioConsulta="begin;";
            $transaccion= $inicioConsulta.$transaccion."commit";           
           
            if (($this->mbd->consulta($transaccion))==0)
            {               
                $consulta = "SELECT setval($tabla, ".$valorInicialSecuencia.");";//Pendiente por optimizar el restablecimiento de la sencuencia en la Base de datos
                $retornaSecuencia=$this->mbd->consulta($consulta);
                return CERO;
            }else
            {
                return UNO;
            }          
        }
	}
	
	function stripslashes_deep($value)
	{
		$value = is_array($value) ?
			array_map('stripslashes_deep', $value) :
			   stripslashes($value);
	
		return $value;
	}
?>
