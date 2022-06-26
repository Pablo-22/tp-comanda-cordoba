<?php

class ArchivoController {
    

    public static function LeerArchivo($path) {
        $fileContent = '';
        if(file_exists($path)){
            $file = fopen($path, 'r');
            if ($file && filesize($path)) {
                $fileContent = fread($file, filesize($path));
            }
            fclose($file);
        }
        return $fileContent;
    }

    public static function LeerJson($path){
        $rawCsv = Datos::LeerArchivo($path);
        $outputArray = json_decode($rawCsv);

        return $outputArray;
    }

    public static function Guardar($fileContent, $pathToFile){
        $file = fopen($pathToFile, 'w');
        fwrite($file, $fileContent);
        fclose($file);
    }

    public static function GuardarJson($obj, $pathToFile){
        $strContent = json_encode($obj, JSON_PRETTY_PRINT);
        Datos::Guardar($strContent, $pathToFile);
    }


    public static function JsonAdd($obj, $pathToFile) {
        $fileContent = array();
        $readedContent = Datos::LeerJson($pathToFile);
        if ($readedContent) {
            $fileContent = $readedContent;
        }
        array_push($fileContent, $obj);
        $strContent = json_encode($fileContent, JSON_PRETTY_PRINT);
        Datos::Guardar($strContent, $pathToFile);
    }
    
    public static function CreateDir($path){
        $dirArray = explode('\\', $path);
        $auxPath = '';
        for ($i=0; $i < count($dirArray) - 1; $i++) { 
            $auxPath .= $dirArray[$i] . '\\';
            if(!is_dir($auxPath)) {
                mkdir($auxPath, 0777);
            }
        }
    }


    public static function guardarArchivo($path, $overwrite, $maxSize, $allowedExtensions){
        $uploadOk = FALSE;
        if (isset($_FILES['archivo']['name']) && isset($path) ) {
            //INDICO CUAL SERA EL DESTINO DEL ARCHIVO SUBIDO
            $destino = $path;
            
            $uploadOk = TRUE;

            //PATHINFO RETORNA UN ARRAY CON INFORMACION DEL PATH
            //RETORNA : NOMBRE DEL DIRECTORIO; NOMBRE DEL ARCHIVO; EXTENSION DEL ARCHIVO
            
            //PATHINFO_DIRNAME - retorna solo nombre del directorio
            //PATHINFO_BASENAME - retorna solo el nombre del archivo (con la extension)
            //PATHINFO_EXTENSION - retorna solo extension
            //PATHINFO_FILENAME - retorna solo el nombre del archivo (sin la extension)
            
            //echo var_dump( pathinfo($destino));die();
            $fileNameArray = explode('.', $_FILES['archivo']['name']);
            $tipoArchivo = '.' . end($fileNameArray);


            
            
            //VERIFICO QUE EL ARCHIVO NO EXISTA
            if (!$overwrite && file_exists($destino)) {
                return "El archivo ya existe. Verifique!!!";
                $uploadOk = FALSE;
            }
            
            //VERIFICO EL TAMAÑO MAXIMO QUE PERMITO SUBIR
            if ($maxSize && $_FILES["archivo"]["size"] > $maxSize) {
                return "El archivo es demasiado grande. Verifique!!!";
                $uploadOk = FALSE;
            }

            //echo $tipoArchivo. '<br>';
            //echo var_dump($allowedExtensions);
            
            //SOLO PERMITO CIERTAS EXTENSIONES
            if(!in_array($tipoArchivo, $allowedExtensions)) {
                return "La extensión del archivo no está permitida";
                $uploadOk = FALSE;
            }

            ArchivoController::CreateDir($path);
        }
            
        //VERIFICO SI HUBO ALGUN ERROR, CHEQUEANDO $uploadOk
        if ($uploadOk === FALSE) {
            
            return "<br/>NO SE PUDO SUBIR EL ARCHIVO.";
            
        } else {
			//var_dump($destino);
            //MUEVO EL ARCHIVO DEL TEMPORAL AL DESTINO FINAL
            if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $destino)) {
                return "<br/>El archivo ". basename( $_FILES["archivo"]["name"]). " ha sido subido exitosamente.";
            } else {
                return "<br/>Lamentablemente ocurri&oacute; un error y no se pudo subir el archivo.";
            }
        }
    }


	public static function CsvToArray($csvStr) {
		echo $csvStr;
		$finalArray = array();
		if ($csvStr) {
			$finalArray = explode(PHP_EOL, $csvStr);

			foreach($finalArray as $key => $row){
				if ($row) {   
					$finalArray[$key] = explode(',', $row);
				}
			}
		}

		return $finalArray;
	} 

	public static function ReadCsv($path){
		$rawCsv = ArchivoController::LeerArchivo($path);
		var_dump($rawCsv);
		$outputArray = ArchivoController::CsvToArray($rawCsv);

		return $outputArray;
	}

	public static function ToCsv($matrix) {
		$csv = '';
		//echo var_dump($matrix);
		foreach ($matrix as $row) {
			foreach ($row as $value) {
				$csv .= $value . ',';
			}
			$csv = substr($csv, 0, -1); //Remove last comma
			$csv .= PHP_EOL; //Add line break at the end
		}
		return $csv;
	}

}