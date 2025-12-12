<?php
    require('inputs.php');
    session_start();
?>

<div class="vh-100">
	<div class="container py-5 h-100">
		<div class="row d-flex align-items-center justify-content-center h-100">

            <div class="col-md-12 col-lg-7 col-xl-6">
                <!-- CONTENEDOR IZQUIERDO-ARRIBA -->
                <form method="post" action="calcGS.php" enctype="multipart/form-data">
                    <?php inputFile("file") ?>
                    <br>
                    <br>
                    <?php inputRange("k_min","k_min =", 1, 12, isset($_SESSION['k_min'])?$_SESSION['k_min']:1 ) ?>
                    <br>
                    <br>
                    <?php inputRange("k_max","k_max =", 1 , 12, isset($_SESSION['k_min'])?$_SESSION['k_max']:1) ?>
                    <br>
                    <br>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="contar0kmers" <?= isset($_SESSION['contar0kemrs'])?"checked":"" ?> >
                        <label class="form-check-label" for="flexSwitchCheckDefault">Contar k-mers con frecuencia 0</label>
                    </div>
                    <br>
                    <br>
                    <button type="submit" class="btn btn-primary btn-lg" id="submit-button" disabled>Calcular GS <i class="bi bi-bar-chart-line"></i></button>
                    <br>
                    <br>
                </form>

                <script>
                    // LÓGICA DEL BOTÓN DE SUBMIT
                    setTimeout(() => {
                        // Obtén referencias a los elementos
                        const fileInput = document.querySelector('input[type="file"]');
                        const kMinInput = document.querySelector('input[name="k_min"]');
                        const kMaxInput = document.querySelector('input[name="k_max"]');
                        const submitButton = document.getElementById('submit-button');


                        // Agrega un evento de escucha para cada cambio en los elementos relevantes
                        fileInput.addEventListener("change", updateSubmitButtonState);
                        kMinInput.addEventListener("input", updateSubmitButtonState);
                        kMaxInput.addEventListener("input", updateSubmitButtonState);

                        // Función para habilitar o deshabilitar el botón de envío según las condiciones
                        function updateSubmitButtonState() {
                            // Verifica si se cumple la condición (k_min <= k_max y se seleccionó un archivo)
                            if (parseInt(kMinInput.value) <= parseInt(kMaxInput.value) && fileInput.files.length > 0) {
                                submitButton.disabled = false;
                                console.log("habilitar");
                            } else {
                                submitButton.disabled = true;
                                console.log("deshabilitar");
                            }
                        }
                    },100);
                </script>

            </div>

            <div class="col-md-12 col-lg-5 col-xl-5">
                <!--CONTENEDOR DERECHO-ABAJO -->
                <div style="width: 100%; height:500px; display: flex; justify-content: center; align-items: center;">
                    
                    <canvas id="grafico" style="background-color: white"></canvas>
                    <?php
                        if(isset($_SESSION['resultados']))
                            $resultados = $_SESSION['resultados'];
                        else
                            $resultados = "[]";

                        if(isset($_SESSION['k_min']))
                            $min = $_SESSION['k_min'];
                        else
                            $min = 1;

                        if(isset($_SESSION['k_max']))
                            $max = $_SESSION['k_max'];
                        else
                            $max = 1;
                    ?>
                    <script>
                        var resultados = <?=$resultados?>;
                        var min = <?=$min?>;
                        var max = <?=$max?>;

                        // Definir labels
                        var labels = [];
                        for(let i=min; i<resultados.length+min; i++) {
                            labels.push("k="+i);
                        }

                        // Definir colores. Todos azul, menos el máximo que es rojo
                        var AZUL = "rgba(75, 192, 192, 0.2)"
                        var ROJO = "rgba(255, 0, 0, 0.2)";

                        var AZUL_B = "rgba(75, 192, 192, 1)";
                        var ROJO_B = "rgba(255, 0, 0, 1)";

                        var colores = new Array(resultados.length).fill(AZUL);
                        var bordes = new Array(resultados.length).fill(AZUL_B);
                        var MAX_GS = Math.max(...resultados);

                        for(let i=0; i<resultados.length; i++) {
                            if(resultados[i] == MAX_GS) {
                                colores[i] = ROJO;
                                bordes[i] = ROJO_B;
                                break;
                            }
                        }

                        var datos = {
                            labels: ["k=1"],
                            datasets: [{
                                label: "GS",
                                backgroundColor: colores,
                                borderColor: bordes,
                                borderWidth: 1,
                                data: resultados
                            }]
                        };

                        // Asignar labels solo si hay datos disponibles
                        console.log(labels);
                        if(labels != []) {datos.labels = labels};

                        // Configuración del gráfico
                        var configuracion = {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        };

                        // Crear un objeto de gráfico de barras
                        var ctx = document.getElementById('grafico').getContext('2d');
                        var grafico = new Chart(ctx, {
                            type: 'bar',
                            data: datos,
                            options: configuracion
                        });
                    </script>

                    <?php if(isset($_SESSION['resultados'])) { /* ?>
                        <br>
                        <button type="submit" type="button" class="btn btn-success" onclick="descargar()"> <i class="bi bi-download"></i> Descargar gráfico </button>
                        <script>
                        function descargar() {
                            let canvas = document.getElementById("grafico");

                            let blob = canvas.toBlob(function(blob) {
                                saveAs(blob, "grafico.jpg");
                            }, "image/jpeg");
                        }
                        </script>
                    <?php  */} ?>
                </div>
            </div>
        </div>
    </div>
</div>

