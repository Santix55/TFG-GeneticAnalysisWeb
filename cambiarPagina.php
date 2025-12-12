<?php


function cambiarPagina ($pagina, $tipo=NULL, $mensaje=NULL) {
    $_SESSION['tipo_mensaje'] = $tipo;
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['pagina'] = $pagina;
    header("Location: index.php");
    exit;
}

?>