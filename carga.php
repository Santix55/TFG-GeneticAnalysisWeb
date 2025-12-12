<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        img {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>
<body>
    <img src="img/loading.gif" alt="Imagen en el centro">



<?php
    sleep(5);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       cambiarPagina("gs.php","primary","hola");    
    }
?>