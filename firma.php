<?php
    require('inputs.php');
    session_start();
?>

<div class="vh-100">
	<div class="container py-5 h-100">
		<div class="row d-flex align-items-center justify-content-center h-100">

            <div class="col-md-12 col-lg-7 col-xl-6">
                <!-- CONTENEDOR IZQUIERDO-ARRIBA -->
                <form method="post" action="calcFirma.php" enctype="multipart/form-data">
                    <?php inputFile("file") ?>
                    <br>
                    <br>
                    <?php inputRange("k","k =", 1, 12, isset($_SESSION['k'])?$_SESSION['k']:1 ) ?>
                    <br>
                    <br>
                    <button type="submit" class="btn btn-primary btn-lg" id="submit-button" disabled>Generar Firma  <i class="bi bi-image"></i></button>
                    <br>
                    <br>
                </form>
            </div>


            <script>
                setTimeout(() => {
                    // Obtén referencias a los elementos
                    const fileInput = document.querySelector('input[type="file"]');
                    const submitButton = document.getElementById('submit-button');

                    // Se comprueba si hay que activar el botón cada vez que se cambia algo de input file
                    fileInput.addEventListener("change", updateSubmitButtonState);

                    // Función para habilitar o deshabilitar el botón de envío según las condiciones
                    function updateSubmitButtonState() {
                        // Verifica si se cumple la condición (k_min <= k_max y se seleccionó un archivo)
                        if (fileInput.files.length > 0) {
                            submitButton.disabled = false;
                            console.log("habilitar");
                        } else {
                            submitButton.disabled = true;
                            console.log("deshabilitar");
                        }
                    }
                },100);
            </script>

            <!-- CONTENEDOR DERECHO-ABAJO -->
            <div class="col-md-12 col-lg-5 col-xl-5">
                <?php if ( isset($_SESSION['firma']) && isset($_SESSION['caducidad']) && $_SESSION['caducidad']>time() ) {  ?>
                    <div class="card mx-auto" style="width: 18rem;">
                        <img class="card-img-top" src="firmas/<?=$_SESSION['firma']?>.jpg" alt="La imagen ha sido borrada porque ha excedido el tiempo">
                            <div class="card-body">
                            <h5 class="card-title" id="tiempo">Card title</h5>
                            <p class="card-text">Una vez el contador llegue a 0 la descarga dejará de estar disponible</p>
                            
                            <form action="descargarFirma.php" method="post">
                                <input type="hidden" name="firma" value="<?=$_SESSION['firma']?>">
                                <button type="submit" id="descargar" name="descargar" class="btn btn-success"><i class="bi bi-download"></i> Descargar imagen.pgm</button>
                            </form> 
                        </div>
                    </div>

                    <script>
                        comenzarCuenta(<?=$_SESSION['caducidad']-time()?>);

                        /*
                        setTimeout(
                            ()=>{document.getElementById("descargar").disabled = true;},
                            <?=$_SESSION['caducidad']-time()?> * 1000 + 1000 // se le añade 100ms para que se cargue el botón
                        );
                        */
                    </script>


                <?php } ?>
            </div>
        </div>
    </div> 
</div>
