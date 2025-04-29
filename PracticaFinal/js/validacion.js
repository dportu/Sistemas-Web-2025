$(document).ready(function() {

	$("#correoOK").hide();
	$("#userOK").hide();

	$("#email").change(function(){
		const campo = $("#email"); // referencia jquery al campo
		campo[0].setCustomValidity(""); // limpia validaciones previas

		// validación html5, porque el campo es <input type="email" ...>
		const esCorreoValido = campo[0].checkValidity();
		if (esCorreoValido && correoValido(campo.val())) {
			// el correo es válido y acaba por @ucm.es: marcamos y limpiamos quejas
		
			// coloca la marca correcta
			$("#correoMal").hide();
			$("#correoOK").show();

			campo[0].setCustomValidity("");
		} else {			
			// correo invalido: ponemos una marca y nos quejamos

			// coloca la marca correcta
			$("#correoMal").show();
			$("#correoOK").hide();

			campo[0].setCustomValidity(
				"El correo debe ser válido. Ejemplo: ejemplo@gmail.com");
		}
	});

	
	$("#username").change(function(){
		var url = "comprobarUsuario.php?user=" + $("#username").val();
		$.get(url,usuarioExiste);
});


	function correoValido(correo) {
		var arroba = correo.indexOf("@");
		correo = correo.substring(arroba,correo.length);
		var punto = correo.indexOf(".");
		correo = correo.substring(punto + 1,correo.length);
		return ( arroba > 0 && punto > 1 && correo.length > 0);
	}

	function usuarioExiste(data,status) {
		if (data == "existe") {
			$("#userMal").show();
			$("#userOK").hide();
			$("#username").focus(); //Devuelvo el foco
			alert("El usuario ya existe, escoge otro");
		}
		else if (data == "disponible") {
			$("#userOK").show();
			$("#userMal").hide();
		}
	}
			
})