<?php
require('cambiarPagina.php');
session_start();

if (!isset($_POST['descargar'])) {
    cambiarPagina("diferencia.php","danger","No se esta utilizando el método POST");
}

if (!isset($_POST['diferencia'])) {
    cambiarPagina("diferencia.php","danger","No se ha pasado el nombre del archivo como párametro");
}

$rutaArchivo = "diferencias/".$_POST['diferencia'].".pgm"; // ruta al archivo que se descargará
$nombreDescarga = $_POST['diferencia'].".pgm";        // nombre que se mostrará al descargar

if (!file_exists($rutaArchivo)) {
    cambiarPagina("diferencia.php","danger","El archivo ha sido borrado, vuelve a generar la diferencia");
}

// Forzar la descarga del archivo
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $nombreDescarga . '"');
readfile($rutaArchivo);

cambiarPagina("diferencia.php","success","La diferencia se esta descargando");
?>