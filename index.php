<!DOCTYPE html>
<?php
session_start();
$pagina = isset($_SESSION['pagina'])? $_SESSION['pagina'] :'inicio.php';
?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- BOOTSTRAP LINK CSS -->
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
			rel="stylesheet"
			integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
			crossorigin="anonymous">
			
		<!-- SCRIPT LIBCAPAS -->
		<script src="js/libCapas2223.js"></script>
		
		<!-- ICONOS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

		<!-- CHART.JS -->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>

		<!-- SCRIPT DE BOOTSTARPT -->
		<script
		src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
		crossorigin="anonymous"></script>

		<!-- CUENTA REGRESIVA PARA LA FIRMA -->
		<script type="text/javascript" src="js/cuentaAtras.js"></script>

		<!-- PROCESAR METADATOS TSV-->
		<script type="text/javascript" src="js/metadatos.js"></script>

		<!-- HTML2CANVAS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

		<!-- FILE SAVER -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>

		<!-- PLOTLY -->
		<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

		<title>Bioinfo</title>
		<link rel="icon" type="image/x-icon" href="img/logo.png">

		<style>
			/* Estilo del contenedor */
			.mi-div {
				border-radius: 10px; /* Bordes redondeados */
				margin-top: 20px; /* Márgen hacia arriba */
				margin-bottom: 20px; /* Márgen hacia abajo */
				padding: 15px; /* Espaciado interno */
				border: 2px solid #ddd; /* Borde sólido con color gris claro */
			}

			/* Estilo de los elementos internos */
			.mi-div p, .mi-div span {
				margin: 8px; /* Espaciado entre los elementos y el borde interno */
			}

			/* Establecer una altura máxima para la tabla */
			.scrollable-table {
				max-height: 300px; /* Puedes ajustar este valor según tus necesidades */
				overflow-y: auto; /* Agregar una barra de desplazamiento vertical cuando sea necesario */
			}
  		</style>
	</head>

<body onload="Cargar('<?=$pagina?>','cuerpo')">
	<nav class="navbar fixed-top navbar-expand-md navbar-dark bg-dark">
			<div class="container-fluid">
			
				<a class="navbar-brand" href="#"
					style="background: linear-gradient(to right, pink, lightblue); -webkit-background-clip: text; background-clip: text; color: transparent;">
					BioInfo
				</a>
				<img src="https://cdn-icons-png.flaticon.com/512/3263/3263976.png" height="40" alt="CoolBrand">
				
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault">
					<span class="navbar-toggler-icon"> </span>
				</button>
				
				<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				
					<!-- Menú superior -->
					<ul class="navbar-nav me-auto">
						<li class="nav-item"><a class="nav-link" href="#" onclick="SeleccionarTab(); Cargar('./inicio.php','cuerpo')" id="inicio.php">Inicio</a>
						</li>
						<li class="nav-item"><a class="nav-link" href="#" onclick="SeleccionarTab(); Cargar('./gs.php','cuerpo')" id="gs.php"> GS</a>
						</li>
						<li class="nav-item"><a class="nav-link" href="#" onclick="SeleccionarTab(); Cargar('./firma.php','cuerpo')" id="firma.php">Firma</a>
						</li>
						<li class="nav-item"><a class="nav-link" href="#" onclick="SeleccionarTab(); Cargar('./distancia.php','cuerpo')" id="distancia.php">Distancia</a>
						</li>
						<li class="nav-item"><a class="nav-link" href="#" onclick="SeleccionarTab(); Cargar('./diferencia.php','cuerpo')" id="diferencia.php">Diferencia</a>
						</li>
					</ul>

				</div>
			</div>
		</nav>

		<br><br><br>

		

		<?php if (isset($_SESSION['mensaje']) && isset($_SESSION['tipo_mensaje'])) { ?>
			<div class="alert alert-<?=$_SESSION['tipo_mensaje']?>" role="alert" style="margin: 0 10px;" id="alert">
				<table>
					<tr>
						<td><i class="bi bi-x-lg" onclick="CerrarAlert();" style="margin-right: 10px;"></i></td>
						<td><?=$_SESSION['mensaje']?></td>
					</tr>
				</table>
			</div>
		<?php } ?>

		<!-- Lógica del menú -->
		<script>
			function CerrarAlert() {
				document.getElementById("alert").hidden = true;
			}

			let tab_ant = document.getElementById("<?=$pagina?>");
			tab_ant.style.color = "white";

			// Se ejecuta al pulsar en menú
			function SeleccionarTab () {
				tab_ant.removeAttribute("style");

				let tab = event.target;
				tab.style.color = "white";

				tab_ant = tab;

				try { CerrarAlert(); } catch(e) {}
			}
		</script>

		<!-- CUERPO DE LA PÁGINA -->
		<div id="cuerpo" style="margin=50px; padding=50px margin-top=80px"></div> 

		<?php
			unset($_SESSION['mensaje']);
			unset($_SESSION['tipo_mensaje']);
			unset($_SESSION['pagina']);

		?>

</body>
