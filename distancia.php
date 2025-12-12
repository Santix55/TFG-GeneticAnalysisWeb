<?php
    require('inputs.php');
    session_start();
?>

<div class="vh-100">
	<div class="container py-5 h-100">
		<div class="row d-flex align-items-center justify-content-center h-100">

            <!-- CONTENEDOR IZQUIERDO-ARRIBA -->
            <div class="col-md-12 col-lg-7 col-xl-6">
                <form method="post" action="calcDistancias.php" enctype="multipart/form-data" style="padding-right:100px">
                    <?php inputFile("file") ?>
                    <?php if ( isset($_SESSION['fasta']) ){ ?>
                        <h5>Archivo seleccionado: <?= $_SESSION['fasta'] ?></h5>
                    <?php } ?>
                    <br>
                    <br>
                    <?php inputFile("file-multi",'<i class="bi bi-file-earmark"></i> Selecciona archivo multiFASTA') ?>
                    <?php if ( isset($_SESSION['multifasta']) ){ ?>
                        <h5>Archivo seleccionado: <?= $_SESSION['multifasta'] ?></h5>
                    <?php } ?>
                    <br>
                    <br>
                    <?php inputRange("k","k =", 1, 12, isset($_SESSION['k'])?$_SESSION['k']:1 ) ?>
                    <br>
                    <br>
                    <button type="submit" class="btn btn-primary btn-lg" id="submit-button" disabled><i class="bi bi-calendar3-week"></i> Calcular distancias  </button>
                    <br>
                    <br>
                </form>
            </div>


            <script>
                setTimeout(() => {
                    // Obtén referencias a los elementos
                    const fileInput = document.getElementById('file');
                    const fileInputMulti = document.getElementById('file-multi');
                    const submitButton = document.getElementById('submit-button');

                    // Se comprueba si hay que activar el botón cada vez que se cambia algo de input file
                    fileInput.addEventListener("change", updateSubmitButtonState);
                    fileInputMulti.addEventListener("change", updateSubmitButtonState);

                    // Función para habilitar o deshabilitar el botón de envío según las condiciones
                    function updateSubmitButtonState() {
                        // Verifica si se cumple la condición (k_min <= k_max y se seleccionó un archivo)
                        if (fileInput.files.length > 0 && fileInputMulti.files.length > 0) {
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
            <div class="col-md-12 col-lg-5 col-xl-5" style="height:500px; display: flex; flex-direction: column; justify-content: center; align-items: center;" id="div-graficos">
                <div id="grafico"></div>
                <script>
                    var distancias = <?= isset($_SESSION['distancias'])? $_SESSION['distancias'] : "[]"?>;
                    var eje_x = [];
                    var eje_y = [];
                    var names = [];

                    for(distancia of distancias) {
                        eje_x.push(distancia.x);
                        eje_y.push(distancia.y);
                        names.push(distancia.name);
                    }

                    console.log(distancias);

                    setTimeout(() => {
                        // Datos de muestra para el gráfico de dispersión
                        var data = [{
                            x: eje_x,
                            y: eje_y,
                            text: names,
                            mode: 'markers', // Tipo de gráfico de dispersión
                            type: 'scatter',
                            marker: {
                                size: 12, // Tamaño de los marcadores
                                color: 'blue' // Color de los marcadores
                            }
                        }];

                        // Diseño del gráfico
                        var layout = {
                            title: 'Distancia-Tamaño',
                            xaxis: { title: 'distancia' },
                            yaxis: { title: 'palabras' }
                        };

                        // Configuración del gráfico
                        Plotly.newPlot('grafico', data, layout);
                        Plotly.newPlot('grafico2', data, layout);
                    }, 100);
                </script>
                <br>
                <div id="grafico2" hidden></div>
            </div>

            <?php if (isset($_SESSION['distancias'])) { ?>
            <div class="mi-div">
                <!-- METADATOS -->
                <div class="row d-flex align-items-center justify-content-center h-100">
                    <div class="col-md-9 col-lg-9 col-xl-9 my-3">
                        <label class="form-label"><i class="bi bi-file-earmark"></i> Metadatos TSV</label>
                        <input class="form-control" type="file" id="file-tsv" onchange="cambiarFichero(this)">
                    </div>

                    <div class="col-md-2 col-lg-2 col-xl-1 my-3" id="div-boton" hidden>
                        <button type="button" class="btn btn-primary btn-lg btn-block" onclick="etiquetar()">
                            <i class="bi bi-tags-fill"></i> Etiquetar <br>
                        </button>
                    </div>

                    <div class="row d-flex align-items-center justify-content-center h-100" hidden>
                        <hr class="hr-blurry" data-menuOculto hidden/>

                        <h4 data-menuOculto hidden> Aislar valor </h4>

                        <!-- Seleccionar campo para aislar-->
                        <div class="col-md-12 col-lg-6 col-xl-6 my-3" data-menuOculto hidden>
                            <label class="form-label"> Campo </label>
                            <select class="form-select" aria-label="Default select example" id="campoAislar" onchange="cargarValoresAislar()">
                                <option selected>Open this select menu</option>
                            </select>
                        </div>

                        <!-- Seleccionar valor para aislar -->
                        <div class="col-md-12 col-lg-6 col-xl-6 my-3" id="div-valorAislar" hidden>
                            <label class="form-label"> Valor </label>
                            <select class="form-select" aria-label="Default select example" id="valorAislar" ><!--onchange="seleccionarValorAislar()"-->
                                <option selected>Open this select menu</option>
                            </select>
                        </div>

                        <hr class="hr-blurry" data-menuOculto hidden/>

                        <h4 data-menuOculto hidden data-menuOculto hidden> Colorear </h4>

                        <!-- Seleccionar valor para colorear -->
                        <div class="col-md-12 col-lg-6 col-xl-6 my-3" data-menuOculto hidden>
                            <label class="form-label"> Campo </label>
                            <select class="form-select" aria-label="Default select example" id="campoColores" onchange="seleccionarCampoColores()">
                                <option selected>Open this select menu</option>
                            </select>
                        </div>

                        <!-- Tabla de colores -->
                        <div class="container mt-4" id="div-tabla" hidden>
                            <div class="table-responsive scrollable-table">
                                <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                    <th scope="col" style="width: 10%;">#</th>
                                    <th scope="col" style="width: 50%;">Valor</th>
                                    <th scope="col">Visibilidad</th>
                                    <th scope="col" style="width: 100%;">Color</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                
                </div>

                
                
            </div>


            <!-- TEXTO DESCARGABLE -->
            <div class="mi-div">
                <div class="col-md-12 col-lg-12 col-xl-12">
                    <button type="button" class="btn btn-primary btn-sm" id="copiar" onclick="copiarAlPortapapeles()"><i class="bi bi-clipboard-data"></i> Copiar</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="descargarTexto()"><i class="bi bi-download"></i> Descargar</button>
                
                    <textarea id="distancias-csv" class="form-control monoespaciado" readonly rows="5" style="margin-top: 5px; padding-bottom: 10px"></textarea>
                </div>
                </div>
            
            <script>
                // Cargar resultados en el cuadro de texto
                setTimeout(() => {
                    var texto = "Distancia, Tamaño, NC\n";
                    for (distancia of distancias) {
                        texto += distancia.x + ", " + distancia.y + ", " + distancia.name + "\n";
                    }
                    document.getElementById("distancias-csv").innerHTML = texto;
                }, 150);

                // Añadir lógica al botón de descarga
                function descargarTexto() {
                    // Obtener el contenido del textarea
                    var contenido = document.getElementById("distancias-csv").value;

                    // Crear un Blob
                    var blob = new Blob([contenido], { type: "text/plain" });

                    // Utilizar FileSaver para iniciar la descarga
                    saveAs(blob, "distancias.csv");
                }
                
                function copiarAlPortapapeles() {
                    // Obtener el elemento textarea
                    var textarea = document.getElementById("distancias-csv");

                    // Seleccionar el contenido del textarea
                    textarea.select();

                    // Ejecutar el comando para copiar al portapapeles
                    document.execCommand("copy");

                    // Deseleccionar el texto
                    textarea.setSelectionRange(0, 0);

                    // Mostrar durante un segundo el mensaje de copiado
                    let botonCopiar = document.getElementById("copiar");
                    botonCopiar.innerHTML = "Copiado";
                    setTimeout(() => {
                        botonCopiar.innerHTML = '<i class="bi bi-clipboard-data"></i> Copiar'
                    }, 1000);
                }
            </script>
            <?php } ?>
        </div>
    </div> 
</div>
