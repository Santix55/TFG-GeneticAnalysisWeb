<?php
require('cambiarPagina.php');
session_start();

// COMPROBAR QUE ESTA USANDO POST
if (!$_SERVER['REQUEST_METHOD'] === 'POST') {
    cambiarPagina("distancia.php","danger","No se esta utilizando el método POST");
}

// COMPROBAR EL PARÁMETRO K DEL FORMULARIO
if (!isset($_REQUEST['k'])) {
    cambiarPagina("distancia.php","danger","Faltan párametros en el formulario");
}
$k = $_REQUEST['k'];

if (!is_numeric($k)) {
    cambiarPagina("distancia.php","danger","El páramtro k debe de ser númerico");
}
$k = (int) $k;
$_SESSION['k'] = $k;

if ($k<1||$k>12) {
    cambiarPagina("distancia.php","danger","k debe estar en 1 y 12");
}

// COMPROBAR FICHERO FASTA
if (!isset($_FILES['file'])) {
    cambiarPagina("distancia.php","danger","Falta el input file en el formulario");
}

$file = $_FILES['file'];

if($file['error'] !== UPLOAD_ERR_OK) {
    cambiarPagina("distancia.php","danger","Error al cargar el archivo. Código de error: " . $file['error']);
}

$nombreArchivo = $file['name'];
$tamañoArchivo = $file['size'];
$ubicaciónInput = $file['tmp_name'];

if($tamañoArchivo > 200*1024*1024) {  // Si el tamaño supera los 200MB
    unlink($ubicaciónInput);
    cambiarPagina("firma.php","danger","El archivo FASTA debe de pesar como máximo 200MB");
}

// COMPROBAR FICHERO MULTIFASTA
if (!isset($_FILES['file-multi'])) {
    unlink($ubicaciónInput);
    cambiarPagina("distancia.php","danger","Falta el input file en el formulario");
}

$fileMulti = $_FILES['file-multi'];

if($fileMulti['error'] !== UPLOAD_ERR_OK) {
    unlink($ubicaciónInput);
    cambiarPagina("distancia.php","danger","Error al cargar el archivo. Código de error: " . $fileMulti['error']);
}

$nombreArchivoMulti = $fileMulti['name'];
$tamañoArchivoMulti = $fileMulti['size'];
$ubicaciónInputMulti = $fileMulti['tmp_name'];

if($tamañoArchivoMulti > 200*1024*1024) {  // Si el tamaño supera los 200MB
    unlink($ubicaciónInput);
    unlink($ubicaciónInputMulti);
    cambiarPagina("firma.php","danger","El archivo multiFASTA debe de pesar como máximo 200MB");
}

// CALCULAR DISTANCIAS
$comando = "./bin/distancias $ubicaciónInput $ubicaciónInputMulti $k 2>&1";
exec($comando ,$output, $return);
unlink($ubicaciónInput);
unlink($ubicaciónInputMulti);
if ($return != 0) {
    cambiarPagina("distancia.php","danger","El programa distancias no terminó correctamente. <br> Comando: $comando <br> Retorno: $return <br> Salida: <br>".implode("\n", $output));
}

$_SESSION['fasta'] = htmlspecialchars($nombreArchivo, ENT_QUOTES, 'UTF-8');
$_SESSION['multifasta'] = htmlspecialchars($nombreArchivoMulti, ENT_QUOTES, 'UTF-8');

$_SESSION['distancias'] = implode($output);
cambiarPagina("distancia.php","success","Se calcularon todas las distancias satisfactoriamente");