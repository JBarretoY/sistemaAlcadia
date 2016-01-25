<?php
    define("CERO",0);
    define("UNO",1);
    define("OBJETO",0);
    define("ARREGLO",1);
    define("ARREGLO_ASOC",2);
    define("N_FILAS",3);
    define("ESPACIO"," "); //EXPERIMENTAL
    define("VERDADERO",true);
    define("FALSO",false);
	define("RUTA","/sicodi/");//ruta de la carpeta raíz del servidor (document root)

    define("BD","raytin");
    define("SERVIDOR","localhost");
    define("USUARIO","wilfredo");
    define("CLAVE","14009534");

	class datos
	{
		private $mnombreBD;
		private $mservidor;
		private $musuario;
		private $mclave;
		private $mresultado;
		private $bd;
		
		//____Constructor de la clase
		function __construct($mnombreBD, $mservidor, $musuario, $mclave)
		{
			 $this->mnombreBD = $mnombreBD;
			 $this->mservidor = $mservidor;
			 $this->musuario = $musuario;
			 $this->mclave = $mclave;
			 /*$valor=$this->mnombreBD.', '.$this->mservidor.', '.$this->musuario.', '.$this->mclave;
           $XX=fopen("C:/irving.txt","w");
                fputs($XX,$valor);*/
		}

		function __destruct()
		{
			//$bd->mysql_close();
			//mysql_free_result( $this->mresultado );
		}

		//Metodo para ralizar la conexion a la base de datos 
		function conectar()
		{
			try
			{
				//$this->bd = @pg_connect("dbname=$this->mnombreBD port=$this->mpuerto host=$this->mservidor user=$this->musuario password=$this->mclave");

  
			 $this->bd = @mysql_connect($this->mservidor, $this->musuario, $this->mclave);
 				@mysql_select_db($this->mnombreBD, $this->bd) or die("Error: ".mysql_error());
  
				if( !$this->bd )
				{
					throw new Exception();
				}
			}
			catch( Exception $e )
			{
				die( $e->getMessage() );
			}
		}//____fin conectar

		//____Metodo para realizar la consulta
		function consulta( $consulta )
		{
	        try
			{
				$this->mresultado = @mysql_query($consulta);
				if( !$this->mresultado )
				{
					throw new Exception("No se pudo realizar la consulta.");
				}
			}
			catch( Exception $e )
			{
				return CERO;
			}
			return UNO;
		}


		//____Metodo que devuelve un arreglo los nombres de las columnas de una o mas tablas
		function getRecurso()
		{
			$i = @mysql_num_fields($this->mresultado);
			for ($j=0; $j<$i; $j++) 
				$arreglo[$j] = mysql_field_name($this->mresultado, $j);
			return $arreglo;
		}
		
		//____Metodo que devuelve los registros de la consulta como un array asociativo
		function getArregloAsociativo()
		{
/*			$arreglo = @mysql_fetch_array($this->mresultado);
			return $arreglo;*/
			$i = @mysql_num_fields($this->mresultado);
			for ($j=0; $j<$i; $j++) 
				$NomColumnas[$j] = mysql_field_name($this->mresultado, $j);
			
			$n=0;
			while ($valor=@mysql_fetch_row( $this->mresultado))
			{	
				$k=0;
				for ($k=0; $k<$i; $k++) 
					$arreglo[$NomColumnas[$k]] = $valor[$k];
				$matriz[$n]=$arreglo;
				$n=$n+1;
			}	
			
			return $matriz;
		}

		//____Metodo que devuelve un registro de la consulta
		function getFila()
		{
			$arreglo = @mysql_fetch_row( $this->mresultado );
			return $arreglo; 
		}

		//____Metodo que devuelve los registros de la consulta como un objeto
		function getObjeto()
		{
			$arreglo = @mysql_fetch_object( $this->mresultado );
			return $arreglo; 
		}

		function getNumRegistro()
		{
			$i = @mysql_num_rows( $this->mresultado );
			return $i;
		}
	}//____fin sigaDatos
?>
