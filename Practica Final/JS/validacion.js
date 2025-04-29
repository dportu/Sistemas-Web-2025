$(document).ready(function() {
   
    //$("#correoOK").hide();   No hace falta si usamos el html??
	//$("#userOK").hide();


    $("#email").change(function(){
        const campo = $("#email"); // referencia jquery al campo
        campo[0].setCustomValidity(""); // limpia validaciones previas
        // validación html5, porque el campo es <input type="email" ...>
        const esCorreoValido = campo[0].checkValidity();
        
        if (esCorreoValido && correoValidoUCM(campo.val())) {
           // el correo es válido y acaba por @ucm.es: marcamos y limpiamos quejas
            $("#emailIcon").html("&#x2714;").css("color", "green");
            campo[0].setCustomValidity("");
        } else {
           // el correo es válido y acaba por @ucm.es: marcamos y limpiamos quejas
            $("#emailIcon").html("&#x274C;").css("color", "red");
            campo[0].setCustomValidity(
                "El correo debe ser válido y acabar por @ucm.es");
        }
    });


    $("#username").change(function(){
        var url = "comprobarUsuario.php?user=" + $("#username").val();
        $.get(url, usuarioExiste);
    });


    function correoValidoUCM(email) {
       
        var dominio = "@ucm.es";
        if (email.length < dominio.length) {
            return false;
        }
        var emailFinal = email.substring(email.length - dominio.length).toLowerCase();
        return emailFinal === dominio;
    }

    // Función para procesar la respuesta del servidor
    function usuarioExiste(data, status) {
        const campo = $("#username");
        
        if (status == "success") {
            if (data.trim() == "existe") {
                
                $("#userIcon").html("&#x274C;").css("color", "red");
                campo[0].setCustomValidity("El nombre de usuario ya está en uso");
                alert("El nombre de usuario ya está reservado");
            } else if (data.trim() == "disponible") {
                // El usuario está disponible
                $("#userIcon").html("&#x2714;").css("color", "green");
                campo[0].setCustomValidity("");
            }
        } else {
            // Error en la petición AJAX
            $("#userIcon").html("&#x274C;").css("color", "red");
            campo[0].setCustomValidity("Error al comprobar el nombre de usuario");
        }
    }
});