<?php
require('cambiarPagina.php');
session_start();

if (!isset($_POST['descargar'])) {
    cambiarPagina("firma.php","danger","No se esta utilizando el método POST");
}

if (!isset($_POST['firma'])) {
    cambiarPagina("firma.php","danger","No se ha pasado el nombre del archivo como párametro");
}

$rutaArchivo = "firmas/".$_POST['firma'].".pgm"; // ruta al archivo que se descargará
$nombreDescarga = $_POST['firma'].".pgm";        // nombre que se mostrará al descargar

if (!file_exists($rutaArchivo)) {
    cambiarPagina("firma.php","danger","El archivo ha sido borrado, vuelve a generar la firma");
}

// Forzar la descarga del archivo
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $nombreDescarga . '"');
readfile($rutaArchivo);

cambiarPagina("firma.php","success","La firma se esta descargando");
?>