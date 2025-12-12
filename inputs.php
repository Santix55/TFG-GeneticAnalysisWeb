<?php
/* Selector de ficheros FATSA guardador o temporales */
function inputFile ($id, $texto='<i class="bi bi-file-earmark"></i> Selecciona archivo FASTA') {
    ?>
        <div class="mb-3">
            <label for="formFile" class="form-label"><?=$texto?></label>
            <input class="form-control" type="file" id="<?=$id?>" name="<?=$id?>">
        </div>
    <?php
}


function inputRange ($id, $texto, $min, $max, $value=NULL) {
    if ($value===NULL) {$value = $min;}
    ?>
        <div class="input-group mb-3 d-flex align-items-center justify-content-between">
            <table>
                <tr>
                    <td><label for="customRange3" class="form-label"><?=$texto?>   </label></td>
                    <td><input type="number" min="<?=$min?>" max="<?=$max?>" id="<?=$id?>Number" value="<?=$value?>" class="form-control" style="width:75px; margin:0 10px;" oninput="ActualizarRango_<?=$id?>()"> </td>
                </tr>
            </table>

            <input type="range" class="form-range" min="<?=$min?>" max="<?=$max?>" step="1" id="<?=$id?>" name="<?=$id?>" value="<?=$value?>" oninput="ActualizarNumero_<?=$id?>()">
        </div>

        <script>
            function ActualizarRango_<?=$id?> () {
                let valor = event.target.value;
                if (valor < <?=$min?>) {value = <?=$min?>}
                if (valor > <?=$max?>) {value = <?=$max?>}
                event.target.value = valor;
                document.getElementById("<?=$id?>").value = valor;
            }

            function ActualizarNumero_<?=$id?> () {
                document.getElementById("<?=$id?>Number").value = event.target.value;
            }
        </script>
    <?php
}
?>
