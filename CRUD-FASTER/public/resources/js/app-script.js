$(document).ready(function () {

    // Poner el paso uno
    $("#pasos-container").html(showPaso1);

    $('#btn-iniciar-mapeo').click(function (event) {
        event.preventDefault();

        var db_name = $('#db-name').val();
        var db_user = $('#db-user').val();
        var db_pass = $('#db-password').val();
        var operation = 1;

        if (db_name == "") {

            alert("Debes indicar el nombre de la base de datos para poder hacer el mapeo.");

        } else if (db_user == "") {

            alert("Debes indicar el usuario de tu base de datos para poder hacer el mapeo.");

        } else {

            var data_send = {
                db_name,
                db_user,
                db_pass,
                operation
            }

            $.ajax({
                    url: "../../controllers/app-controller.php",
                    data: data_send,
                    method: 'post',
                    dataType: 'json',
                    cache: false,
                })
                .done(function (serverResponse) {

                    if (serverResponse['rta'] != 'error') {

                        var table_name = serverResponse['msg'];

                        $("#dinamic-msgs-container").html("<p>Se encontró la tabla: " + table_name + "</p>");

                        // Mostrar paso 2
                        // $("#pasos-container").html(showPaso2);

                        executePaso2(table_name);

                    } else {

                        $("#dinamic-msgs-container").html("<p>Error: " + serverResponse['msg'] + "</p>");

                    }

                });

        }

    });

});

function showPaso1() {

    return html_string = '<div class="paso-container" id="paso-1-container">' +
        '<h2>Paso 1</h2>' +
        '<p>Completa la siguiente información para empezar a realizar el mapeo de las bases de datos.</p>' +
        '<form id="data-conection-form">' +
        '<div class="contenedor-input">' +
        '<label for="db-name">BASE DE DATOS:</label>' +
        '<input type="text" id="db-name" class="mi-input" placeholder="Ej: mi_base_de_datos">' +
        '</div>' +
        '<div class="contenedor-input">' +
        '<label for="db_user">Usuario:</label>' +
        '<input type="text" id="db-user" placeholder="Ej: root">' +
        '</div>' +
        '<div class="contenedor-input">' +
        '<label for="db_password">Contraseña:</label>' +
        '<input type="text" name="db-password" id="db-password" placeholder="Se puede dejar vacío">' +
        '</div>' +
        '<div class="contenedor-input">' +
        '<button id="btn-iniciar-mapeo">Iniciar Mapeo</button>' +
        '</div>' +
        '</form>' +
        '</div>';
}

function showPaso2() {
    return html_string = '<div class="paso-container" id="paso-2-container">' +
        '<h2>Paso 1</h2>' +
        '<p>Estas son las sentencias SQL que se crearán para tu Base de Datos:</p>' +
        '<div class="sentences-display">' +
        '<div class="sentence-container">' +
        '<h3>SELECT * FROM table_name</h3>' +
        '</div>' +
        '<div class="sentence-container">' +
        '<h3>SELECT columna_1 FROM table_name</h3>' +
        '</div>' +
        '<div class="sentence-container">' +
        '<h3>SELECT columna_2 FROM table_name</h3>' +
        '</div>' +
        '<div class="sentence-container">' +
        '<h3>SELECT columna_3 FROM table_name</h3>' +
        '</div>' +
        '<div class="sentence-container">' +
        '<h3>SELECT columna_n FROM table_name</h3>' +
        '</div>' +
        '<div class="sentence-container">' +
        '<h3>INSERT columna_n FROM table_name</h3>' +
        '</div>' +
        '<div class="sentence-container">' +
        '<h3>UPDATE columna_n FROM table_name</h3>' +
        '</div>' +
        '<div class="sentence-container">' +
        '<h3>DELETE columna_n FROM table_name</h3>' +
        '</div>' +
        '</div>';
}

function executePaso2(arg_tableName) {

    var table_name = arg_tableName;
    var operation = 2;

    var data_send = {
        table_name,
        operation
    }

    $.ajax({
            url: '../../controllers/app-controller.php',
            data: data_send,
            method: 'post',
            dataType: 'json',
            cache: false,
        })
        .done(function (serverResponse) {

            if (serverResponse['rta'] != "error") {

                $("#dinamic-msgs-container").html("<p>" + serverResponse['msg'] + "</p>");

            } else {

                $("#dinamic-msgs-container").html("<p>Error: " + serverResponse['msg'] + "</p>");

            }

        });

}