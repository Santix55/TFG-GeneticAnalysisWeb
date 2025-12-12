<?php
require('cambiarPagina.php');
session_start();

// COMPROBAR QUE ESTA USANDO POST
if (!$_SERVER['REQUEST_METHOD'] === 'POST') {
    cambiarPagina("firma.php","danger","No se esta utilizando el método POST");
}

// COMPROBAR EL PARÁMETROS DEL FORMULARIO
if (!isset($_REQUEST['k'])) {
    cambiarPagina("firma.php","danger","Faltan párametros en el formulario");
}
$k = $_REQUEST['k'];

if (!is_numeric($k)) {
    cambiarPagina("firma.php","danger","El páramtro k debe de ser númerico");
}
$k = (int) $k;
$_SESSION['k'] = $k;

if ($k<1||$k>12) {
    cambiarPagina("firma.php","danger","k debe estar en 1 y 12");
}

// COMPROBAR FICHEROS
if (!isset($_FILES['file'])) {
    cambiarPagina("firma.php","danger","Falta el input file en el formulario");
}

$file = $_FILES['file'];

if($file['error'] !== UPLOAD_ERR_OK) {
    cambiarPagina("firma.php","danger","Error al cargar el archivo. Código de error: " . $file['error']);
}

$nombreArchivo = $file['name'];
$tamañoArchivo = $file['size'];
$ubicaciónInput = $file['tmp_name'];

// COMPROBAR TAMAÑO DEL FICHERO
if($tamañoArchivo > 200*1024*1024) {  // Si el tamaño supera los 200MB
    unlink($ubicaciónInput);
    cambiarPagina("firma.php","danger","El archivo debe de pesar como máximo 200MB");
}

$uniqid = bin2hex(uniqid('temp_',true));
$ubicaciónOutput = "firmas/$uniqid.pgm";
$ubicaciónOutputJPG = "firmas/$uniqid.jpg";


// GENERAR FIRMAS EN FORMATO PGM Y JPG
// Generar firma PGM
exec("./bin/firma $ubicaciónInput $ubicaciónOutput $k 2>&1", $output, $return);
unlink($ubicaciónInput);
if ($return != 0) {
    cambiarPagina("firma.php","danger","El programa firma no terminó correctamente. Retorno: $return <br> Salida: <br>".implode("\n", $output));
}

// Generar firma JPG a partir de la PGM en JPG
exec("convert $ubicaciónOutput $ubicaciónOutputJPG 2>&1", $output_conv, $return_conv);
if ($return_conv != 0) {
    cambiarPagina("firma.php","danger","No se pudo convertir la firma PGM en JPG. Retorno: $return_conv <br> Salida: <br>".implode("\n", $output_conv));
}

$_SESSION['firma'] = $uniqid; // identificador de la firma sin extensión

// PROGRAMAR BORRADO DE LAS FIRMAS DESPUÉS DE VALIDEZ+1 MINUTOS //
const TIEMPO_VALIDEZ = 2;                               // tiempo de válidez del enlace
$_SESSION['caducidad'] = time()+TIEMPO_VALIDEZ*60;      // segundos en el que va a caducar el link

// Borrado de la firma PGM
$comando_rm = 'echo "rm '.$ubicaciónOutput.'" | at now + '.(TIEMPO_VALIDEZ+1).' minutes 2>&1';
exec($comando_rm, $output_rm, $return_rm);
if ($return_rm != 0) {
    cambiarPagina("firma.php","danger","Progrmación del borrado del PGM falló. Retorno: $return_rm<br> Comando: $comando_rm<br> Salida: <br>".implode("\n", $output_rm));
}

// Borrado de la firma JPG
exec('echo "rm '.$ubicaciónOutputJPG.'" | at now + '.(TIEMPO_VALIDEZ+1).' minutes 2>&1', $output_rm2, $return_rm2);
if ($return_rm2 != 0) {
    cambiarPagina("firma.php","danger","Progrmación del borrado del JPG falló. Retorno: $return_rm2 <br> Salida: <br>".implode("\n", $output_rm2));
}



cambiarPagina("firma.php","success","La firma se generó correctamente");

