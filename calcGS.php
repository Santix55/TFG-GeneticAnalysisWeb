<?php
require('cambiarPagina.php');
session_start();

if (!$_SERVER['REQUEST_METHOD'] === 'POST') {
    cambiarPagina("gs.php","danger","No se esta utilizando el método POST");
}

if (!(isset($_REQUEST['k_min'])
    &&isset($_REQUEST['k_max']))) {
    cambiarPagina("gs.php","danger","Faltan párametros en el formulario");
}

$k_min  = $_REQUEST['k_min'];
$k_max  = $_REQUEST['k_max'];
$kmers0 = isset($_REQUEST['contar0kmers'])? "true":"false";

if (!is_numeric($k_min) || !is_numeric($k_max)) {
    cambiarPagina("gs.php","danger","Los párametros k_min y k_max deben de ser numéricos");
}

$k_min = (int) $k_min;
$k_max = (int) $k_max;

$_SESSION['k_min'] = $k_min;
$_SESSION['k_max'] = $k_max;
$_SESSION['contar0kemrs'] = $_REQUEST['contar0kmers'];

if ($k_min > $k_max) {
    cambiarPagina("gs.php","danger","k_min debe de ser menor o igual k_max");
}

if ($k_min<1||$k_max>12) {
    cambiarPagina("gs.php","danger","Los parámetros k deben de estar comprendidos entre 1 y 12");
}

if (!isset($_FILES['file'])) {
    cambiarPagina("gs.php","danger","Falta el input file en el formulario");
}

$file = $_FILES['file'];

if($file['error'] !== UPLOAD_ERR_OK) {
    cambiarPagina("gs.php","danger","Error al cargar el archivo. Código de error: " . $file['error']);
}

$nombreArchivo = $file['name'];
$tamañoArchivo = $file['size'];
$ubicaciónTemporal = $file['tmp_name'];

if($tamañoArchivo > 200*1024*1024) {  // Si el tamaño supera los 200MB
    unlink($ubicaciónTemporal);
    cambiarPagina("gs.php","danger","El archivo debe de pesar como máximo 200MB");
}

exec("./bin/gs $ubicaciónTemporal $k_min $k_max $kmers0 2>&1", $output, $return);
unlink($ubicaciónTemporal);

if ($return != 0) {
    cambiarPagina("gs.php","danger","El programa no terminó correctamente. Retorno $return");
}

$_SESSION['resultados'] = implode($output);
cambiarPagina("gs.php","primary", "Se han obtenido los siguientes valores de GS: ".implode($output));
?>