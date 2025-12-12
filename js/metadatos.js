/**
 * Archivo para procesar los metadatos formato TSV
 * filtrar y etiquetar por colores las distancias
 */

function mostrar(id) {
    document.getElementById(id).removeAttribute("hidden");
}

function ocultar(id) {
    document.getElementById(id).hidden = true;
}

function borrarHijos (elemento) {
    elemento.innerHTML = "";
}

/**
 * Array de objetos que almacenan que representan los campos del fichero TSV
 * cada objeto tiene los siguientes atributos:
 * .nombre  = string nombre del campo
 * .valores = Set de strings que representan los valores que pueden tomar dicho campo
 */
let campos = [];

/**
 * Array bidimensional (Matriz) que contiene el valor que toma un punto en determinado campo.
 * 1er índice: Índice del campo a clasificar
 * 2o  índice: Índice del punto
 */
let metadatos = [];

/**
 * Rueda de colores para asignarle por defecto a las etiquetas
 */
const colores = [
  '#0000FF', // Azul
  '#FF0000', // Rojo
  '#00FF00', // Verde
  '#FFFF00', // Amarillo
  '#FF1493', // Rosa
  '#8A2BE2', // Morado
  '#00FFFF', // Cian
  '#FF4500', // Naranja
  '#32CD32', // Verde lima
  '#1E90FF', // Azul claro
  '#FF69B4', // Rosa claro
  '#800080', // Púrpura
  '#FFD700', // Dorado
  '#A52A2A', // Marrón
  '#7FFF00', // Verde amarillento
  '#4682B4'  // Azul acero
];

/**
 * Se invoca cada vez que se cambia selecciona un nuevo fichero de metadatos en formato TSV.
 * Lee los metadatos y almacena en las estructura de datos campos y metadatos.
 * Una vez leídos se muestra en la interfaz un selector con el campo por el cual se quiere etiqueta
 * y la lista de colores que se le asigna a cada valor.
 * @param {input type="file"} fileInput 
 */
function cambiarFichero(fileInput) {
    // VER SI SE HA EXISTE EL FICHERO TSV
    const file = fileInput.files[0];
    console.log("abriendo metadatos: "+file.name);
    if(!file) {
        alert("Selecciona un archivo antes de intentar leerlo");
        return;
    }

    // LECTURA DEL FICHERO TSV
    const reader = new FileReader();
    reader.readAsText(file);
    reader.onload = (e)=>{
        let texto = reader.result;
        console.log(texto);

        // OBTENER LOS CAMPOS Y VALORES DEL TSV
        campos = [];

        // 1º.- Obtención de los campos (1ª entrada)
        let nombreCampo = "";
        let idx_1erSalto = texto.length;   //< índice del primer tabulador

        for (let i=0; i<texto.length; i++) {
            let c = texto[i];
            if(c==='\n') {
                idx_1erSalto = i;
                campos.push({nombre: nombreCampo, valores: new Set()});
                break;
            } else if(c==='\t') {
                campos.push({nombre: nombreCampo, valores: new Set()});
                nombreCampo = "";
            } else {
                nombreCampo += c;
            }
        }

        // metadatos = Array(campos.length).fill([]); // <-- ERROR LLENAS TODOS LOS ELEMENTOS CON LA MISMA REFENCIA
        metadatos = [];
        for(let c=0; c<campos.length; c++) metadatos.push([]);

        // 2º.- Obtención de los valores (resto de entradas)
        let valor = "";
        let idx_campo = 0;  //< índice del campo en el que se va a guardar
        let n_linea = 0;    //< número de línea que se esta anilizando, sin tener en cuenta la 1a  

        for(let i=idx_1erSalto+1; i<texto.length; i++) {
            let c = texto[i];
            if(c==='\n'){
                campos[idx_campo].valores.add(valor);
                metadatos[idx_campo][n_linea] = valor;
                //console.log(`idx_campo: ${idx_campo}\tn_linea: ${n_linea}\tvalor: ${valor}`)
                valor = "";
                idx_campo = 0;
                n_linea++;
            } else if(c==='\t') {
                if(idx_campo >= campos.length) {
                    alert("Error de formato TSV: Se ha encontrado una entrada con más campos de los declarados inicialmente");
                    return;
                }
                campos[idx_campo].valores.add(valor);
                metadatos[idx_campo][n_linea] = valor;
                //console.log(`idx_campo: ${idx_campo}\tn_linea: ${n_linea}\tvalor: ${valor}`)
                valor = "";
                idx_campo++;
            } else {
                valor += c;
            }
        }

        //console.log(campos);
        console.log(metadatos);

        // HACER VISIBLES TODOS LOS ELEMENTOS DEL MENÚ OCULTO
        let elementosMenuOculto = document.querySelectorAll('[data-menuOculto]');
        elementosMenuOculto.forEach(function(elemento) {
            elemento.removeAttribute("hidden");
        });


        cargarCampos("campoColores");
        cargarCampos("campoAislar");
        mostrar("div-boton");

    }
}

/**
 * Carga los campos del TSV en un select.
 * @param {string} id Identificador select
 */
function cargarCampos (id) {
    let select = document.getElementById(id);
    borrarHijos(select);

    let option = document.createElement("option");
    option.value = -1;
    option.text = "Ninguno";
    select.appendChild(option);

    let i=0;
    for(campo of campos) {
        option = document.createElement("option");
        option.value = i++;
        option.text = campo.nombre;
        select.appendChild(option);
    }
}

/**
 * Cargar los valores del TSV del campo seleccionado en 
 * el select de aislar valores y lo hace visible
 */
function cargarValoresAislar () {
    let selectCampo = document.getElementById("campoAislar");

    if(selectCampo.value=="-1") {   // Ninguno seleccionado
        ocultar("div-valorAislar");
        return;
    }

    let selectValor = document.getElementById("valorAislar");
    borrarHijos(selectValor);

    let option = document.createElement("option");
    option.value = -1;
    option.text = "Ninguno";
    selectValor.appendChild(option);

    for(valor of campos[selectCampo.value].valores) {
        option = document.createElement("option");
        option.value = valor;
        option.text = valor;
        selectValor.appendChild(option);
    }

    mostrar("div-valorAislar");
}

/**
 * Se llama cada vez que se cambia el campo de colores.
 * Añade en una tabla tantas filas como valores existentes que pueda tomar un campo,
 * en cada fila se muestra el nombre del campo, junto un botón para cambiar su visibilidad y selector de colores.
 */
function seleccionarCampoColores () {
    let select = document.getElementById("campoColores");

    if(select.value=="-1") {   // Ninguno seleccionado
        ocultar("div-tabla");
        return;
    }

    let tbody = document.getElementById("tbody");
    borrarHijos(tbody);

    let i=0;
    for(valor of campos[select.value].valores) {
        nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <th id=${valor}-idx scope="row">${i+1}</th>
            <td>${valor}</td>
            <td class="text-right">  
                <button class="btn btn-primary" onclick="cambiarVisibilidad('${valor}');" data-visible="true" id="${valor}-visibilidad" style="width: 100%; height:2.5em;"><i class="bi bi-eye-fill"></i></button>
            </td>
            <td class="text-center">
                <input type="color" id="${valor}-color" style="width: 100%; height:2.5em;" value="${colores[i%16]}">
            </td>
        `;
        tbody.appendChild(nuevaFila);
        i++;
    }

    // mostrar("div-tabla"); // La asignación de colores por la tabla dejó de ir
}

/**
 * Se llama cada vez que se presiona el botón que cambia la visibilidad.
 * @param {string} valor Nombre del valor a cambiar la visibilidad.
 */
function cambiarVisibilidad (valor) {
    console.log(valor);

    let boton = document.getElementById(valor+"-visibilidad");
    let visible = boton.dataset.visible;

    if (visible == "true") {    // Si estaba en visible pasa a invisible
        boton.dataset.visible = false;
        boton.innerHTML = `<i class="bi bi-eye-slash-fill"></i>`;
        ocultar(valor+"-color");
    } else {                    // Si estaba a invisible pasa a visible
        boton.dataset.visible = true;
        boton.innerHTML = `<i class="bi bi-eye-fill"></i>`;
        mostrar(valor+"-color");
    }

    console.log(visible);
}

/**
 * Se llama cada vez que se presiona el botón "Etiquetar".
 * Recoge la información de la tabla de colores y visibilidad del campo que se quiere etiquetar
 * y vuelve a construir la gráfica de puntos clasificando y filtrando los puntos por el campo especificado
 */
function etiquetar() {
    let campo_idx_aislar = document.getElementById("campoAislar").value; // índice del valor a aislar

    let campo_idx = document.getElementById("campoColores").value; // índice del campo escogido para colorear

    // AGREGAR A DIFERENTES DATASETS DEPENDIENDO DEL CAMPO DEL COLOR //
    let datasets;
    if(campo_idx == -1) { // NO COLOREAR
        console.log("Ningún campo para colorear seleccionado");

        // Crear un dataset con todos los datos
        datasets = [{
            mode: 'markers',
            type: 'scatter',
            x:    [],
            y:    [],
            text: [],
            marker: {
                size: 12,
                color: 'blue'
            },
            text_aislar: [] //< almacena el valor del campo que hay que aislar
        }]

        let i=0;
        for(distancia of distancias) {
            datasets[0].x.push(distancia.x);
            datasets[0].y.push(distancia.y);
            datasets[0].text.push(distancia.nombre);

            if(campo_idx_aislar != -1) {
                datasets[0].text_aislar.push(metadatos[campo_idx_aislar][i++]);
            }
        }

    }
    else { // COLOREAR
        let valores = [...campos[campo_idx].valores]; // array de valores posibles del campo de color escogido

        // Crear datasets con todos los valores del campo elegido
        datasets = valores.map( v => {
            return {
                mode: 'markers',
                type: 'scatter',
                name: v,
                x:    [],
                y:    [],
                text: [],
                marker: {
                    size: 12,                                             // tamaño de los marcadores
                    color: 'document.getElementById(v+"-color").value'    // color elegido en la interfaz
                },
                text_aislar: [] //< almacena el valor del campo que hay que aislar
            };
        });

        // Almacenar cada una de las distancias en el dataset que toca
        for(let i=0; i<distancias.length; i++) {
            let valor = metadatos[campo_idx][i];                                        // valor por el cual el punto es etiquetado
            console.log(valor);
            let valor_idx = parseInt(document.getElementById(valor+"-idx").innerHTML)-1;// consultar en la interfaz el índice del valor de clasificación
            console.log(valor_idx);
            let distancia = distancias[i];                                              // punto con .x .y .nombre

            datasets[valor_idx].x.push( distancia.x );
            datasets[valor_idx].y.push( distancia.y );
            datasets[valor_idx].text.push( distancia.nombre );

            if(campo_idx_aislar != -1) {
                datasets[valor_idx].text_aislar.push(metadatos[campo_idx_aislar][i]);
            }
        }

        // Quitar aquellos datos que son no-visibles
        datasets = datasets.filter(d =>
            document.getElementById(d.name+"-visibilidad").dataset.visible == "true"
        );
    }

    // DISEÑO //
    const diseño = {
        title: 'Distancia-Tamaño',
        xaxis: { title: 'distancia' },
        yaxis: { title: 'palabras' }
    };

    // AISLAR EL VALOR DEL CAMPO SELECCIONADO EN OTRA TABLA //
    let valor_aislar = document.getElementById("valorAislar").value;     // string valor a aislar

    if(campo_idx_aislar == -1 || valor_aislar == "Ninguno") {    // NO AISLAR
        ocultar("grafico2");
        Plotly.newPlot('grafico', datasets, diseño);

        document.getElementById("div-graficos").style.height = "500px";
    }
    else {  // AISLAR
        let datasets2 = JSON.parse(JSON.stringify(datasets)); // hacer copia por valor
        console.log(datasets2);

        for(let i=0; i<datasets.length; i++) { // recorrer datasets
            let dataset = datasets[i];
            let dataset2 = datasets2[i];

            let indicesConElValor = [];
            for(let j=0; j<dataset.x.length; j++) {  // recorrer puntos
                if(dataset.text_aislar[j] == valor_aislar) {
                    indicesConElValor.push(j);
                }
            }

            // Elimina los elementos con el valor seleccionado a asilar en el dataset
            dataset.x = dataset.x.filter((valor, indice) => !indicesConElValor.includes(indice));
            dataset.y = dataset.y.filter((valor, indice) => !indicesConElValor.includes(indice));
            dataset.text = dataset.text.filter((valor, indice) => !indicesConElValor.includes(indice));

            // Deja solo los elementos con el valor seleccionado a asilar en el dataset2
            dataset2.x = dataset.x.filter((valor, indice) => indicesConElValor.includes(indice));
            dataset2.y = dataset.y.filter((valor, indice) => indicesConElValor.includes(indice));
            dataset2.text = dataset.text.filter((valor, indice) => indicesConElValor.includes(indice));
        }

        const diseño2 = {
            title: valor_aislar,
            xaxis: { title: 'distancia' },
            yaxis: { title: 'palabras' }
        };

        diseño.title = "no "+valor_aislar;

        Plotly.newPlot('grafico', datasets, diseño);
        Plotly.newPlot('grafico2', datasets2, diseño2);
        mostrar("grafico2");

        console.log(datasets2);

        document.getElementById("div-graficos").style.height = "1000px";
    }
}