<?php

include "../../include/conexion.php";
include "../../include/busquedas.php";
include "../../include/funciones.php";
include("../include/verificar_sesion_secretaria.php");
if (!verificar_sesion($conexion)) {
	echo "<script>
				  alert('Error Usted no cuenta con permiso para acceder a esta página');
				  window.location.replace('../login/');
			  </script>";
  }else {

	$archivoTemporal = $_FILES['estudiantes']['tmp_name'];

	require '../../composer/vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	// Cargar el archivo Excel desde la ruta temporal
    $documento = IOFactory::load($archivoTemporal);

    // Seleccionar la primera hoja del documento
    $hoja = $documento->getActiveSheet();

    // Obtener el número total de filas y columnas
    $filas = $hoja->getHighestRow();
    $columnas = $hoja->getHighestColumn();

    // Inicializar un array para almacenar los datos
    $datos = [];

	// Recorrer las filas y columnas para extraer los datos
	for ($fila = 2; $fila <= $filas; $fila++) {
		// Obtener los valores de las celdas
		$dni = $hoja->getCell('A' . $fila)->getValue();
		$apellidoNombre = $hoja->getCell('B' . $fila)->getValue();
		$genero = $hoja->getCell('C' . $fila)->getValue();

		// Almacenar los datos en el array
		$datos[] = [
			'dni' => $dni,
			'apellidoNombre' => $apellidoNombre,
			'genero' => $genero
		];
	}

	// Realizar las manipulaciones necesarias con los datos almacenados en el array $datos

	// Por ejemplo, mostrar los datos
	foreach ($datos as $dato) {
		echo "DNI: {$dato['dni']}, Estudiante: {$dato['apellidoNombre']}, Programa: {$dato['programa']}<br>";
	}

	/*

//verificar si el estudiante ya esta registrado
	$busc_est_car = "SELECT * FROM estudiante WHERE dni='$dni' AND id_programa_estudios='$carrera'";
	$ejec_busc_est_car = mysqli_query($conexion, $busc_est_car);
	$conteo = mysqli_num_rows($ejec_busc_est_car);
if ($conteo > 0) {
		echo "<script>
			alert('El estudiante, ya esta registrado para esta carrera');
			window.history.back();
				</script>
			";
	}else{
	$pass = $dni;
	$pass_secure = password_hash($pass, PASSWORD_DEFAULT);

	$insertar = "INSERT INTO estudiante (dni, apellidos_nombres, id_genero, fecha_nac, direccion, correo, telefono, anio_ingreso, id_programa_estudios, id_semestre, seccion, turno, discapacidad, password, reset_password, token_password) VALUES ('$dni','$nom_ap','$genero', '$fecha_nac', '$direccion', '$email', '$telefono', '$anio_ingreso', '$carrera', '$semestre', '$seccion', '$turno', '$discapacidad', '$pass_secure', 0, '')";
	$ejecutar_insetar = mysqli_query($conexion, $insertar);
	if ($ejecutar_insetar) {
			echo "<script>
                alert('Registro Existoso');
                window.location= '../estudiante.php'
    			</script>";
	}else{
		echo "<script>
			alert('Error al registrar estudiante, por favor verifique sus datos');
			window.history.back();
				</script>
			";
	};

};

*/


mysqli_close($conexion);

}