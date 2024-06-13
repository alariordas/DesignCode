<?php
// prueba.php

// Definir la función que maneja las opciones
function manejarOpciones($opcion, $numero) {
    return "<div class=\"$opcion\"><h1>La palabra generada es....</h1><p>$opcion</p><p>$numero</p></div>";
}

// Verificar si es una solicitud POST y si se han recibido opción y número
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['opcion']) && isset($_POST['numero'])) {
    $opcion = $_POST['opcion'];
    $numero = intval($_POST['numero']);
    echo manejarOpciones($opcion, $numero);
    exit; // Terminar la ejecución del script después de manejar las opciones
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo PHP y JavaScript</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.filter-button');
            limpiarGeneradas();

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    button.classList.toggle('active'); // Alternar clase 'active' al hacer clic
                    ejecutarEjemplo();
                });
            });

            function distribuirSuma(total, partes) {
                const resultado = new Array(partes).fill(Math.floor(total / partes));
                let resto = total % partes;
                for (let i = 0; i < resto; i++) {
                    resultado[i]++;
                }
                return resultado;
            }

            function ejecutarEjemplo() {
                var opciones = [];
                var todasOpciones = ['item1', 'item2', 'item3', 'item4', 'item5', 'item6'];

                buttons.forEach(button => {
                    if (button.classList.contains('active')) {
                        opciones.push(button.dataset.filter); // Usar data-filter en lugar de textContent
                    }
                });

                // Obtener las opciones ya generadas desde localStorage
                var generadas = JSON.parse(localStorage.getItem('generadas')) || {};

                // Calcular la distribución de la suma de 30
                var incrementos = distribuirSuma(30, opciones.length);

                opciones.forEach(function(opcion, index) {
                    var contador = generadas[opcion] || 0;
                    contador += incrementos[index];

                    // Verificar si el elemento ya está visible y quitar el display none
                    var elementos = document.getElementsByClassName(opcion);
                    for (var i = 0; i < elementos.length; i++) {
                        elementos[i].style.display = '';
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "prueba.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var nuevoContenido = document.createElement("div");
                            nuevoContenido.innerHTML = xhr.responseText;
                            document.getElementById("resultado").appendChild(nuevoContenido);

                            // Actualizar el contador en localStorage
                            generadas[opcion] = contador;
                            localStorage.setItem('generadas', JSON.stringify(generadas));
                        }
                    };

                    var data = "opcion=" + encodeURIComponent(opcion) + "&numero=" + contador;
                    xhr.send(data);
                });

                // Ocultar las opciones deseleccionadas
                todasOpciones.forEach(function(opcion) {
                    if (!opciones.includes(opcion)) {
                        var elementos = document.getElementsByClassName(opcion);
                        for (var i = 0; i < elementos.length; i++) {
                            elementos[i].style.display = 'none';
                        }
                    }
                });
            }

            // Limpiar las opciones generadas en localStorage
            function limpiarGeneradas() {
                localStorage.removeItem('generadas');
                document.getElementById("resultado").innerHTML = '';
            }
        });
    </script>
</head>
<body>
    <div>
        <button type="button" class="filter-button" data-filter="item1">Item 1</button>
        <button type="button" class="filter-button" data-filter="item2">Item 2</button>
        <button type="button" class="filter-button" data-filter="item3">Item 3</button>
        <button type="button" class="filter-button" data-filter="item4">Item 4</button>
        <button type="button" class="filter-button" data-filter="item5">Item 5</button>
        <button type="button" class="filter-button" data-filter="item6">Item 6</button>
    </div>
    <button onclick="limpiarGeneradas()">Limpiar Generadas</button>
    <section id="resultado"></section>
</body>
</html>
