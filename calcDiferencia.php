Calculando diferencia...
<?php
require('cambiarPagina.php');
session_start();

// COMPROBAR QUE ESTA USANDO POST
if (!$_SERVER['REQUEST_METHOD'] === 'POST') {
    cambiarPagina("diferencia.php","danger","No se esta utilizando el método POST");
}

// COMPROBAR EL PARÁMETROS DEL FORMULARIO
if (!isset($_REQUEST['k'])) {
    cambiarPagina("diferencia.php","danger","Faltan párametros en el formulario");
}
$k = $_REQUEST['k'];

if (!is_numeric($k)) {
    cambiarPagina("diferencia.php","danger","El páramtro k debe de ser númerico");
}
$k = (int) $k;
$_SESSION['k'] = $k;

if ($k<1||$k>12) {
    cambiarPagina("diferencia.php","danger","k debe estar en 1 y 12");
}

// COMPROBAR FICHERO
if (!isset($_FILES['file'])) {
    cambiarPagina("diferencia.php","danger","Falta el input file en el formulario");
}

$file = $_FILES['file'];

if($file['error'] !== UPLOAD_ERR_OK) {
    cambiarPagina("diferencia.php","danger","Error al cargar el archivo. Código de error: " . $file['error']);
}

$nombreArchivo = $file['name'];
$tamañoArchivo = $file['size'];
$ubicaciónInput = $file['tmp_name'];

// COMPROBAR TAMAÑO DEL FICHERO
if($tamañoArchivo > 200*1024*1024) {  // Si el tamaño supera los 200MB
    unlink($ubicaciónInput);
    cambiarPagina("diferencia.php","danger","El primer archivo debe de pesar como máximo 200MB");
}

// COMPROBAR FICHERO 2
if (!isset($_FILES['file2'])) {
    cambiarPagina("diferencia.php","danger","Falta el input file en el formulario");
}

$file2 = $_FILES['file2'];

if($file2['error'] !== UPLOAD_ERR_OK) {
    cambiarPagina("diferencia.php","danger","Error al cargar el archivo. Código de error: " . $file2['error']);
}

$nombreArchivo2 = $file2['name'];
$tamañoArchivo2 = $file2['size'];
$ubicaciónInput2 = $file2['tmp_name'];

// COMPROBAR TAMAÑO DEL FICHERO
if($tamañoArchivo2 > 200*1024*1024) {  // Si el tamaño supera los 200MB
    unlink($ubicaciónInput);
    cambiarPagina("diferencia.php","danger","El segundo archivo debe de pesar como máximo 200MB");
}

$uniqid = bin2hex(uniqid('temp_',true));
$ubicaciónOutput = "diferencias/$uniqid.pgm";
$ubicaciónOutputJPG = "diferencias/$uniqid.jpg";

// GENERAR diferenciaS EN FORMATO PGM Y JPG
// Generar diferencia PGM
exec("./bin/diferencia $ubicaciónInput $ubicaciónInput2 $ubicaciónOutput $k 2>&1", $output, $return);
unlink($ubicaciónInput);
if ($return != 0) {
    cambiarPagina("diferencia.php","danger","El programa diferencia no terminó correctamente. Retorno: $return <br> Salida: <br>".implode("\n", $output));
}

// Generar diferencia JPG a partir de la PGM en JPG
exec("convert $ubicaciónOutput $ubicaciónOutputJPG 2>&1", $output_conv, $return_conv);
if ($return_conv != 0) {
    cambiarPagina("diferencia.php","danger","No se pudo convertir la diferencia PGM en JPG. Retorno: $return_conv <br> Salida: <br>".implode("\n", $output_conv));
}

$_SESSION['diferencia'] = $uniqid; // identificador de la diferencia sin extensión

// PROGRAMAR BORRADO DE LAS diferenciaS DESPUÉS DE VALIDEZ+1 MINUTOS //
const TIEMPO_VALIDEZ = 2;                               // tiempo de válidez del enlace
$_SESSION['caducidad-dif'] = time()+TIEMPO_VALIDEZ*60;      // segundos en el que va a caducar el link

// Borrado de la diferencia PGM
$comando_rm = 'echo "rm '.$ubicaciónOutput.'" | at now + '.(TIEMPO_VALIDEZ+1).' minutes 2>&1';
exec($comando_rm, $output_rm, $return_rm);
if ($return_rm != 0) {
    cambiarPagina("diferencia.php","danger","Progrmación del borrado del PGM falló. Retorno: $return_rm<br> Comando: $comando_rm<br> Salida: <br>".implode("\n", $output_rm));
}

// Borrado de la diferencia JPG
exec('echo "rm '.$ubicaciónOutputJPG.'" | at now + '.(TIEMPO_VALIDEZ+1).' minutes 2>&1', $output_rm2, $return_rm2);
if ($return_rm2 != 0) {
    cambiarPagina("diferencia.php","danger","Progrmación del borrado del JPG falló. Retorno: $return_rm2 <br> Salida: <br>".implode("\n", $output_rm2));
}



cambiarPagina("diferencia.php","success","La diferencia se generó correctamente");
