<?php
error_reporting(5);

function completarCodigoCeros($cadena,$tamano){
    $p="";
    for(;$tamano> strlen($cadena);$tamano--)
        $p.="0";
    return ($p.$cadena);
	}

function formatearFecha($cadena){
        list($ano,$mes,$dia)=explode("-",$cadena);
	return "$dia/$mes/$ano";
	}

function desformatearFecha($cadena){
	list($dia,$mes,$ano)=explode("/",$cadena);
	return "$ano-$mes-$dia";
	}

function esBisiesto($ano){
    return (($ano%4==0 && $ano%100!=0)||($ano%400==0)?true:false);
}

function esFechaValida($cadena){
    $FECHA=explode("-",$cadena);
    if(count($FECHA)!=3)
        return false;

    $dia=$FECHA[2];
    if(strlen($dia)!=2 || !is_numeric($dia))
        return false;

    $mes=$FECHA[1];
    if(strlen($mes)!=2 || !is_numeric($mes))
        return false;

    $anio=$FECHA[0];
    if(strlen($anio)!=4 || !is_numeric($anio))
        return false;

    $elMes = intval($mes);
    if($elMes>12)
        return false;

    // MES FEBRERO
    if($elMes == 2){
        if(esBisiesto($anio)){
            if(intval($dia) > 29)
                return false;
            else
                return true;
        }
        else
		{
			if(intval($dia) > 28)
				return false;
			else
				return true;
        }
    }

    //RESTO DE MESES
    if($elMes== 4 || $elMes==6 || $elMes==9 || $elMes==11){
        if(intval($dia) > 30)
            return false;
    }
    else
        if(intval($dia) > 31)
            return false;

    return true;
}

function num_lunes($mes,$anyo)
{
    $diaS=date("w", mktime(0, 0, 0, $mes, 1, $anyo));

    if ($diaS==1)
    {
        $nLunes=5;
        if ($mes==2 && !esBisiesto($anyo))
            $nLunes=4;
    }
    else
    {
        $nLunes=0;
        if ($diaS==0)
            $diaS=7;
        $dia=9-$diaS;
        while(checkdate($mes, $dia, $anyo))
        {
            $nLunes++;
            $dia=$dia+7;
        }
    }
    return $nLunes;
}

function antiguedad($ingreso, $fecha)
{
    list($dia,$mes,$anyo)=explode("/",$ingreso);
    list($dia_act,$mes_act,$anyo_act)=explode("/",$fecha);
    $anyos=$anyo_act-$anyo;
    $meses=$mes_act-$mes;
    if ($anyos==0)
    {
        if ($meses>=0)
        {
            $dias=$dia_act-$dia;
            if ($dias<0)
            {
                $meses--;
                $mes_ant=$mes_act-1;
                $num_dias=date("t", mktime(0, 0, 0, $mes_ant, 1, $anyo_act));
                $dias=$num_dias-$dia+$dia_act;
            }
        }
    }
    elseif ($anyos>0)
    {
        $dias=$dia_act-$dia;
        if ($meses==0)
        {
            if ($dias<0)
            {
                $anyos--;
                $meses=12;
            }
        }
        elseif ($meses<0)
        {
            $anyos--;
            $meses=12+$meses;
        }

        if ($dias<0)
        {
            $meses--;
            $mes_ant=$mes_act-1;
            $num_dias=date("t", mktime(0, 0, 0, $mes_ant, 1, $anyo_act));
            $dias=$num_dias-$dia+$dia_act;
        }
    }
    return "$anyos-$meses-$dias";
}

function ordenar_array() { 
  $n_parametros = func_num_args(); // Obenemos el número de parámetros 
  if ($n_parametros<3 || $n_parametros%2!=1) { // Si tenemos el número de parametro mal... 
    return false; 
  } else { // Hasta aquí todo correcto...veamos si los parámetros tienen lo que debe ser... 
    $arg_list = func_get_args(); 
 
    if (!(is_array($arg_list[0]) && is_array(current($arg_list[0])))) { 
      return false; // Si el primero no es un array...MALO! 
    } 
    for ($i = 1; $i<$n_parametros; $i++) { // Miramos que el resto de parámetros también estén bien... 
      if ($i%2!=0) {// Parámetro impar...tiene que ser un campo del array... 
        if (!array_key_exists($arg_list[$i], current($arg_list[0]))) { 
          return false; 
        } 
      } else { // Par, no falla...si no es SORT_ASC o SORT_DESC...a la calle! 
        if ($arg_list[$i]!=SORT_ASC && $arg_list[$i]!=SORT_DESC) { 
          return false; 
        } 
      } 
    } 
    $array_salida = $arg_list[0]; 
 
    // Una vez los parámetros están bien, procederé a ordenar... 
    $a_evaluar = "foreach (\$array_salida as \$fila){\n"; 
    for ($i=1; $i<$n_parametros; $i+=2) { // Ahora por cada columna... 
      $a_evaluar .= "  \$campo{$i}[] = \$fila['$arg_list[$i]'];\n"; 
    } 
    $a_evaluar .= "}\n"; 
    $a_evaluar .= "array_multisort(\n"; 
    for ($i=1; $i<$n_parametros; $i+=2) { // Ahora por cada elemento... 
      $a_evaluar .= "  \$campo{$i}, SORT_REGULAR, \$arg_list[".($i+1)."],\n"; 
    } 
    $a_evaluar .= "  \$array_salida);"; 
    // La verdad es que es más complicado de lo que creía en principio... :) 
 
    eval($a_evaluar); 
    return $array_salida; 
  } 
} 

function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false, $flag=SORT_STRING) {  
    $position = array();  
    $newRow = array();  
    foreach ($toOrderArray as $key => $row) {  
            $position[$key]  = strtolower($row[$field]);  
            $newRow[$key] = $row;  
    }  
	
    //natcasesort($position);  
	asort($position,$flag); 
	$returnArray = array();  
	foreach ($position as $key => $pos) {       
        $returnArray[] = $newRow[$key];  
    }  
    if ($inverse) {
		$returnArray = array_reverse($returnArray);
    }  
    
    return $returnArray;  
}

function base($num){
	$end=$num-floor($num/10)*10;
	switch ($end){
		case 1: 	return "uno";
		case 2: 	return "dos";
		case 3: 	return "tres";
		case 4: 	return "cuatro";
		case 5: 	return "cinco";
		case 6: 	return "seis";
		case 7: 	return "siete";
		case 8: 	return "ocho";
		case 9: 	return "nueve";
		case 0:
			if($num==0)	return "cero";
			else		return "";
		}
	return $end;
	}

//LA FUNCION DECIMOS ES PARA 99 -> 0 Y LLAMA A LA BASE
function decimos($num){
	if($num<10)
		return base($num);
	$ends=$num-floor($num/100)*100;
	$end=$ends-($num-floor($num/10)*10);
	$endd=floor($ends);
	switch ($end){
		case 10:
			if($ends<16)
				switch($endd){
					case 10: return "diez";
					case 11: return "once ";
					case 12: return "doce";
					case 13: return "trece";
					case 14: return "catorce";
					case 15: return "quince";
					}
			else
				return "dieci".base($num);
		case 20:
			if($ends==20)	return "veinte";
			else 			return "veinti".base($num);
		case 30:
			if($ends==30)	return "treinta";
			else			return "treinta y ".base($num);
		case 40:
			if($ends==40)	return "cuarenta";
			else			return "cuarenta y ".base($num);
		case 50:
			if($ends==50)	return "cincuenta";
			else			return "cincuenta y ".base($num);
		case 60:
			if($ends==60)	return "sesenta";
			else			return "sesenta y ".base($num);
		case 70:
			if($ends==70)	return "setenta";
			else			return "setenta y ".base($num);
		case 80:
			if($ends==80)	return "ochenta";
			else			return "ochenta y ".base($num);
		case 90:
			if($ends==90)	return "noventa";
			else			return "noventa y ".base($num);
		case 0:
			return base($num);
		}
	}

//LA FUNCION CIENTOS ES PARA 99 -> 0 Y LLAMA A DECIMOS
function cientos($num){
	if ($num<100) return decimos($num);
	$ends=$num-floor($num/1000)*1000;
	$end=$ends-($num-floor($num/100)*100);
	switch($end){
		case 100:
			if($ends==100)	return "cien";
			else			return "ciento ".decimos($num);
		case 500:			return "quinientos ".decimos($num);
		case 900:			return "novecientos ".decimos($num);
		case 700:			return "setecientos ".decimos($num);
		case 0:				return decimos($num);
		default:			return base($end/100)."cientos ".decimos($num);
		}
	}
//CIENTOSX es para los miles que terminane en 1
function cientos_x($num){
	$endd=$num-floor($num/10)*10;
	$ends=$endd-floor($endd/10)*10;
	$resultado=cientos($num);
	if($ends==1 && $endd!=11)	return substr($resultado,0,strlen($resultado)-1);
	else						return $resultado;
	}

function miles($num){
	if ($num<1000) 	return cientos($num);
	$ends=$num-floor($num/10000)*10000;
	$end=$ends-($num-floor($num/1000)*1000);
	switch ($end){
		case 1000:
			if($ends==1000)	return "mil";
			else			return "mil ".cientos($num);
		default:
			$mil=base(floor($num/1000));
			if ($mil==0)		return cientos($num);
			else				return $mil." mil ".+cientos($num);
		}
	}

function cientos_de_miles($num){
	if($num<1000) return miles($num);
	$ends=floor(($num-floor($num/1000000)*1000000)/1000);
	if($ends==0)
		return cientos($num);
// 	else if($ends==1)
// 		return "mil ".cientos($num);
	return cientos_x($ends)." mil ".cientos($num);
	}

function millones($num){
	if($num<pow(10,6)) return cientos_de_miles($num);
	$ends=floor($num/pow(10,6));
	$end=$ends-floor($ends/10)*10;
	$resultado=cientos_de_miles($ends);
	if($end==1){
		$parcial=substr($resultado,0,strlen($resultado)-1);
		if($ends<2)	return $parcial." millón ".cientos_de_miles($num);
		else		return $parcial." millones ".cientos_de_miles($num);
		}
	return $resultado." millones ".cientos_de_miles($num);
	}

function Numero2Letras($num){
	$num=number_format("$num",2,".","");
	$ArregloNum=explode(".","$num");
	$str=millones($ArregloNum[0]);
	if(count($ArregloNum)==2)//si hay decimal
		$str.=" con ".$ArregloNum[1]."/100";
	return $str;
	}

function mesEnLetras($mes)
{
	if ($mes==1)
		$mes="ENERO";
	elseif ($mes==2)
		$mes="FEBRERO";
	elseif ($mes==3)
		$mes="MARZO";
	elseif ($mes==4)
		$mes="ABRIL";
	elseif ($mes==5)
		$mes="MAYO";
	elseif ($mes==6)
		$mes="JUNIO";
	elseif ($mes==7)
		$mes="JULIO";
	elseif ($mes==8)
		$mes="AGOSTO";
	elseif ($mes==9)
		$mes="SEPTIEMBRE";
	elseif ($mes==10)
		$mes="OCTUBRE";
	elseif ($mes==11)
		$mes="NOVIEMBRE";
	elseif ($mes==12)
		$mes="DICIEMBRE";

	return $mes;
}

function LimpiarCadena($str){
	return ereg_replace("[^A-Za-z0-9 &aacute;ÁéÉíÍóÓúÚñÑ]","",$str);
}

function convertirMayusculas($cadena)
{
	return strtr(strtoupper($cadena),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
}

function modifyAttribute($code_id, $code_value, $attribute, $value, $table){
    $bd = new Fachada();
    $bd -> abrir(BD, SERVIDOR, USUARIO, CLAVE, PUERTO);

    $array_values = array($attribute => $value);

    $condition = "$code_id=$code_value";
    $result = $bd -> modificar($table, $array_values, $condition);

    return $result;
}
/*
function resizePicture($url,$fileName,$w=null,$h=null)
{
	$len = strlen($fileName);
	$pos = strripos($fileName, ".");
	$ext = strtolower(substr($fileName, ($len-$pos-1)*(-1)));
	$base = substr($fileName, 0, $pos);

	if ($ext=='png')
	{
		$imagen_original = imagecreatefrompng($url.$fileName);		
	}
	else if ($ext=='jpg' || $ext=='jpeg')
	{
		$imagen_original = imagecreatefromjpeg($url.$fileName);		
	}
	else if ($ext=='gif')
	{
		$imagen_original = imagecreatefromgif($url.$fileName);		
	}

	$newImage1=$base."-".$w."x".$h.".".$ext;

	//Cargo en memoria la imagen que quiero redimensionar
	//Obtengo el ancho de la imagen quecargue
	$ancho_original = imagesx($imagen_original);
	//Obtengo el alto de la imagen que cargue
	$alto_original = imagesy($imagen_original);
	
	//SI QUEREMOS UN ANCHO FINAL FIJO, calculamos el ALTO de forma proporcionada
	if($w)
		$ancho_final1 = $w;
	else
		$ancho_final = ($h / $alto_original) * $ancho_original;	
	
	if($h)
		$alto_final1 = $w;
	else
		$alto_final1 = ($w / $ancho_original) * $alto_original;
	//Ancho final en pixeles
	//$alto_final1 = $h;//($ancho_final1 / $ancho_original) * $alto_original;
	//$alto_final3 = ($ancho_final3 / $ancho_original) * $alto_original;
	
	//SI CONOCEMOS UN ALTO FINAL FIJO, calculamos el ANCHO de forma proporcionada
	
	//Para usar este caso, comentar las dos lineas anteriores, y descomentar las dos siguientes a este comentario
	
	//$alto_final = 250; //Alto final en pixeles
	//$ancho_final = ($alto_final / $alto_original) * $ancho_original;
	
	//Creo una imagen vacia, con el alto y el ancho que tendrá la imagen redimensionada
	
	if ($ext=='png' || $ext=='gif')
	{
		$imagen_redimensionada1 = imagecreate($w, $h);
    	$colourBlack = imagecolorallocate($imagen_redimensionada1, 0, 0, 0);
    	imagecolortransparent($imagen_redimensionada1, $colourBlack);	
	}
	else if ($ext=='jpg'  || $ext=='jpeg')
	{
		$imagen_redimensionada1 = imagecreatetruecolor($w, $h);	
	}

	//Copio la imagen original con las nuevas dimensiones a la imagen en blanco que creamos en la linea anterior
	imagecopyresampled($imagen_redimensionada1, $imagen_original, 0, 0, 0, 0, $ancho_final1, $alto_final1, $ancho_original, $alto_original);
	
	//Guardo la imagen ya redimensionada
	imagepng($imagen_redimensionada1, $url.$newImage1);
	
	//Libero recursos, destruyendo las imágenes que estaban en memoria
	imagedestroy($imagen_original);
	imagedestroy($imagen_redimensionada1);

	return $newImage1;
}*/


//----------- MODIFICACIÓN DE LA FUNCIÓN resizePicture ---------//
function resizePicture($url,$fileName,$w=null,$h=null)
{
	//note: for the desired measures, always width is greater than height
	$len = strlen($fileName);
	$pos = strripos($fileName, ".");
	$ext = strtolower(substr($fileName, ($len-$pos-1)*(-1)));
	$base = substr($fileName, 0, $pos);
	
	$newImage=$base."-".$w."x".$h.".".$ext;

	if ($ext=='png')
	{
		$imagen_original = imagecreatefrompng($url.$fileName);		
	}
	else if ($ext=='jpg' || $ext=='jpeg')
	{
		$imagen_original = imagecreatefromjpeg($url.$fileName);		
	}
	else if ($ext=='gif')
	{
		$imagen_original = imagecreatefromgif($url.$fileName);		
	}
		
	//get the width and height of the image uploaded
	$ancho_original = imagesx($imagen_original);
	$alto_original = imagesy($imagen_original);

	$r_desired=$w/$h;
	$r_orig=$ancho_original/$alto_original;
	
	//resize into the smallest measure
	if ($r_orig>=$r_desired)
	{
		$prop=$h/$alto_original;
	}
	else
	{
		$prop=$w/$ancho_original;
	}
	
	$w_final=$ancho_original*$prop;
	$h_final=$alto_original*$prop;
	
	//Create an empty image, with the desired width and height
	if ($ext=='png' || $ext=='gif')
	{
		$imagen_redimensionada1 = imagecreate($w, $h);
    	$colourBlack = imagecolorallocate($imagen_redimensionada1, 0, 0, 0);
    	imagecolortransparent($imagen_redimensionada1, $colourBlack);
	}
	
	$imagen_redimensionada1 = imagecreatetruecolor($w, $h);	
	imagealphablending($imagen_redimensionada1, false);
	
	//resize the image
	imagecopyresampled($imagen_redimensionada1, $imagen_original, 0, 0, 0, 0, $w_final, $h_final, $ancho_original, $alto_original);
	imagesavealpha($imagen_redimensionada1, true);	
	//Save the image
	imagepng($imagen_redimensionada1, $url.$newImage);
	
	//crop the image to the desired size
	imagecopy($img_final, $imagen_redimensionada1, 0, 0, 0, 0, $w, $h);
	//Save the image
	imagepng($img_final, $url.$newImage);
	
	//free from memory
	imagedestroy($imagen_original);
	imagedestroy($imagen_redimensionada1);

	return $newImage;
}
//------------------ 

//----------- MODIFICACIÓN DE LA FUNCIÓN cropPicture ---------//
function cropPicture($url,$fileName,$w=null,$h=null)
{
	//note: for the desired measures, always width is greater than height
	$len = strlen($fileName);
	$pos = strripos($fileName, ".");
	$ext = strtolower(substr($fileName, ($len-$pos-1)*(-1)));
	$base = substr($fileName, 0, $pos);
	
	$newImage=$base."-".$w."x".$h.".".$ext;

	if ($ext=='png')
	{
		$imagen_original = imagecreatefrompng($url.$fileName);		
	}
	else if ($ext=='jpg' || $ext=='jpeg')
	{
		$imagen_original = imagecreatefromjpeg($url.$fileName);		
	}
	else if ($ext=='gif')
	{
		$imagen_original = imagecreatefromgif($url.$fileName);		
	}
		
	//get the width and height of the image uploaded
	$ancho_original = imagesx($imagen_original);
	$alto_original = imagesy($imagen_original);

	$r_desired=$w/$h;
	$r_orig=$ancho_original/$alto_original;
	
	//resize into the smallest measure
	if ($r_orig>=$r_desired)
	{
		$prop=$h/$alto_original;
	}
	else
	{
		$prop=$w/$ancho_original;
	}
	
	$w_final=$ancho_original*$prop;
	$h_final=$alto_original*$prop;
	
	//Create an empty image, with the desired width and height
	if ($ext=='png' || $ext=='gif')
	{
		$imagen_redimensionada1 = imagecreate($w, $h);
    	$colourBlack = imagecolorallocate($imagen_redimensionada1, 0, 0, 0);
    	imagecolortransparent($imagen_redimensionada1, $colourBlack);	
	}
	/*else if ($ext=='jpg'  || $ext=='jpeg')
	{*/
		$imagen_redimensionada1 = imagecreatetruecolor($w, $h);	
	//}
	imagealphablending($imagen_redimensionada1, false);
	
	
	//resize the image
	imagecopyresampled($imagen_redimensionada1, $imagen_original, 0, 0, 0, 0, $w_final, $h_final, $ancho_original, $alto_original);
	imagesavealpha($imagen_redimensionada1, true);		
	//Save the image
	imagepng($imagen_redimensionada1, $url.$newImage);
	
	
	
	//crop the image to the desired size
	imagecopy($img_final, $imagen_redimensionada1, 0, 0, 0, 0, $w, $h);
	
	//Save the image
	imagepng($img_final, $url.$newImage);
	
	//free from memory
	imagedestroy($imagen_original);
	imagedestroy($imagen_redimensionada1);
}
//------------------ 

function pruebaresize($url, $filename, $w, $h, $sel_width, $sel_height, $sel_x, $sel_y)
{
	$len = strlen($filename);
	$pos = strripos($filename, ".");
	$ext = strtolower(substr($filename, ($len-$pos-1)*(-1)));
	$base = substr($filename, 0, $pos);
	
	$newImage=$base."-".$w."x".$h.".".$ext;
	
	$mythumb = new thumb();
	$mythumb->loadImage($url.$filename);
	$mythumb->crop($sel_width, $sel_height, $sel_x, $sel_y);
	$mythumb->save($url.$newImage);
	
	$mythumb = new thumb();
	$mythumb->loadImage($url.$newImage);
	$mythumb->resize($h, 'height');
	$mythumb->save($url.$newImage);
}


function convertDate($fechaFrom, $husoHorarioFrom, $husoHorarioTo)
{
    //$husoHorarioServer=-4.5;
	//$serverTime=date("Y-m-d H:i:s");
    
    //$time = date("Y-m-d H:i:s",strtotime($serverTime) + ($husoHorario-($husoHorarioServer))*60*60);
    
    $resultado=date("Y-m-d H:i:s",strtotime($fechaFrom)+ ($husoHorarioTo-($husoHorarioFrom))*60*60);
    
    return $resultado;
}

function getImageRenamed($image_name,$width,$height)
{
	$len=strlen($image_name);
	$pos=strripos($image_name, ".");
	$ext=strtolower(substr($image_name, $pos+1, $len));
	$base=substr($image_name, 0, $pos);
	$new_name=$base."-".$width."x".$height.".".strtolower($ext);
	return $new_name;
}

function promedioRate($data){
	$suma=0;
	$n=count($data);
	
	for($i=0;$i<$n;$i++){
		$suma+=floatval($data[$i]['co_element']);
	}
	return $suma/$n;
}

function dateToStringCustom($dt,$w_timezone,$w_return,$w_long,$lang="EN")
{
	$wtimezone=0;
	if ($w_timezone)
	{
		$wtimezone=1;
		$timezone=substr($dt, -6);
		$dt=substr($dt,0,strlen($dt)-6);
	}
	
	$wreturn=0;
	if ($w_return)
	{
		$wreturn=$w_return;
	}
	
	$large=0;
	if ($w_long)
	{
		$large=1;
	}

	$dtA=explode(' ',$dt);
	$dt=explode('-',$dtA[0]);
	//var_dump($dt);
	include(RAIZ_WEB . "/web/language/".$lang."/".$lang."_teachlr.php");
	$arregloIDM=$IDM;

	if ($large==1)
	{		
		if($dt[1]=="01"){$dt[1]=$arregloIDM[9988];}
		if($dt[1]=="02"){$dt[1]=$arregloIDM[9989];}
		if($dt[1]=="03"){$dt[1]=$arregloIDM[9990];}
		if($dt[1]=="04"){$dt[1]=$arregloIDM[9991];}
		if($dt[1]=="05"){$dt[1]=$arregloIDM[9992];}
		if($dt[1]=="06"){$dt[1]=$arregloIDM[9993];}
		if($dt[1]=="07"){$dt[1]=$arregloIDM[9994];}
		if($dt[1]=="08"){$dt[1]=$arregloIDM[9995];}
		if($dt[1]=="09"){$dt[1]=$arregloIDM[9996];}
		if($dt[1]=="10"){$dt[1]=$arregloIDM[9997];}
		if($dt[1]=="11"){$dt[1]=$arregloIDM[9998];}
		if($dt[1]=="12"){$dt[1]=$arregloIDM[9999];}
	}
	else
	{		
		if($dt[1]=="01"){$dt[1]=$arregloIDM[9976];}
		if($dt[1]=="02"){$dt[1]=$arregloIDM[9977];}
		if($dt[1]=="03"){$dt[1]=$arregloIDM[9978];}
		if($dt[1]=="04"){$dt[1]=$arregloIDM[9979];}
		if($dt[1]=="05"){$dt[1]=$arregloIDM[9980];}
		if($dt[1]=="06"){$dt[1]=$arregloIDM[9981];}
		if($dt[1]=="07"){$dt[1]=$arregloIDM[9982];}
		if($dt[1]=="08"){$dt[1]=$arregloIDM[9983];}
		if($dt[1]=="09"){$dt[1]=$arregloIDM[9984];}
		if($dt[1]=="10"){$dt[1]=$arregloIDM[9985];}
		if($dt[1]=="11"){$dt[1]=$arregloIDM[9986];}
		if($dt[1]=="12"){$dt[1]=$arregloIDM[9987];}
	}

	$time=explode(':', $dtA[1]);

	if ($wreturn==0)
	{
		if ($lang=="EN"){
			return $dt[1]." ".$dt[0].", ".$dt[2]. " at ".$time[0].":".$time[1];// MM YYYY, DD at HH:MM
		}
		else if ($lang=="ES"){
			return $dt[2]." de ".$dt[1]." de ".$dt[0]." a las ".$time[0].":".$time[1];// DD de MM de YYYY a las HH:MM
		}
	}
    else if ($wreturn==1)
    {
    	if ($lang=="EN"){
        	return $dt[1]." ".$dt[0].", ".$dt[2];// MM YYYY, DD
		}
		else if ($lang=="ES"){
			return $dt[2]." de ".$dt[1]." de ".$dt[0];// DD de MM de YYYY
		}
    }
	else if ($wreturn=="m")
	{
		return $dt[1];// MM
	}
	else if ($wreturn=="d")
	{
		return $dt[2];// DD
	}
	else if ($wreturn=="y")
	{
		return $dt[0];// YYYY
	}
	else if ($wreturn=="t")
	{
		return $time[0].":".$time[1];// HH:MM
	}
}

function limpiar_caracteres_especiales($String) 
{
	$String = strtolower(trim($String));
	$String = str_replace(array("'",'"',"“","”","«","»","<",">","/","\\","|","¿","?","¡","!","(",")","{","}","[","]",".",",",";",":","·","%","&","@","#","$","€","£","¥","¸","~","¬","=","^","´","`","¨","±","+","*","÷","¤","¦","§","¯","µ","¶","¼","½","¾"),"",$String);
	$String = str_replace("_","-",$String);
	$String = str_replace(" ","-",$String);
	$String = str_replace("--","-",$String);
	$String = preg_replace('/--+/', '-', $String); //replaces "--" with "-" recursively
	$String = str_replace(array('à','á','â','ã','ä','å','æ','ª','À','Á','Â','Ã','Ä','Å','Æ'),"a",$String);
	$String = str_replace(array('è','é','ê','ë','È','É','Ê','Ë'),"e",$String);
	$String = str_replace(array('ì','í','î','ï','Ì','Í','Î','Ï'),"i",$String);
	$String = str_replace(array('ò','ó','ô','õ','ö','ø','œ','º','°','Ò','Ó','Ô','Õ','Ö','Ø','Œ'),"o",$String);
	$String = str_replace(array('ù','ú','û','ü','Ù','Ú','Û','Ü'),"u",$String);
	$String = str_replace(array('ñ','Ñ'),"n",$String);
	$String = str_replace(array('ç','Ç','¢','©'),"c",$String);	
	$String = str_replace(array('ý','Ý','ÿ'),"y",$String);
	$String = str_replace('¹',"1",$String);
	$String = str_replace('²',"2",$String);
	$String = str_replace('³',"3",$String); 
	$String = str_replace('®',"r",$String);
	$String = str_replace('×',"x",$String);
	$String = str_replace('ß',"ss",$String);
	$String = str_replace(array('Ð','ð'),"d",$String);
	$String = str_replace(array('Þ','þ'),"th",$String);
	
	//suppress the dash character at the end of the string, if any
	$char= substr($String, -1);
	if ($char=="-")
	{
		$String = substr($String, 0, -1);
	}
	
	//suppress the dash character at the beginning of the string, if any
	$char = substr($String, 0, 1);
	if ($char=="-")
	{
		$String = substr($String, 1);
	} 

	return $String;
}

function pc_permute($items, $perms = array( )) {
    
    if (empty($items)) { 
		$_SESSION['valores_search'][$_SESSION['c-valores_search']]= join('%', $perms);
		$_SESSION['c-valores_search']++;
        
    }  else {
        for ($i = count($items) - 1; $i >= 0; --$i) {
             $newitems = $items;
             $newperms = $perms;
             list($foo) = array_splice($newitems, $i, 1);
             array_unshift($newperms, $foo);
             pc_permute($newitems, $newperms);
         }
    }
}

function validate_gravatar($email) {
	// Craft a potential url and test its headers
	$hash = md5(strtolower(trim($email)));
	$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
	$headers = @get_headers($uri);
	if (!preg_match("|200|", $headers[0])) {
		$has_valid_avatar = FALSE;
	} else {
		$has_valid_avatar = TRUE;
	}
	return $has_valid_avatar;
}

 /**
  * Determine whether an object field matches needle. 
  *
  * Usage Example: 
  * --------------- 

  $arr = array( new stdClass(), new stdClass() ); 
  $arr[0]->colour = 'red'; 
  $arr[1]->colour = 'green'; 
  $arr[1]->state  = 'enabled'; 
  if (in_array_field('red', 'colour', $arr)) 
    echo 'Item exists with colour red.'; 
  if (in_array_field('magenta', 'colour', $arr)) 
   echo 'Item exists with colour magenta.'; 
  if (in_array_field('enabled', 'state', $arr)) 
   echo 'Item exists with enabled state.'; 
  * ---------------
  * @author @_andres_rivas
  * @access public
  * @return bool
  */
function in_array_field($needle, $needle_field, $haystack, $strict = false) { 
	if ($strict) { 
		foreach ($haystack as $item) 
			if (isset($item[$needle_field]) && $item[$needle_field] === $needle) 
				return true; 
	} 
	else { 
		foreach ($haystack as $item) 
			if (isset($item[$needle_field]) && $item[$needle_field] == $needle) 
				return true; 
	} 
	return false; 
}

?>