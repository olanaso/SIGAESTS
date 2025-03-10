<?php

	include("../include/conexion.php");
	include("../include/busquedas.php");
	include("../include/funciones.php");
	include 'include/verificar_sesion_estudiante.php';
    include("../empresa/include/consultas.php");

	if (!verificar_sesion($conexion)) {
		echo "<script>
                  alert('Error Usted no cuenta con permiso para acceder a esta página');
                  window.location.replace('index.php');
          </script>";
	} else {

		$id_estudiante_sesion = buscar_estudiante_sesion($conexion, $_SESSION['id_sesion_est'], $_SESSION['token']);
		$b_estudiante = buscarEstudianteById($conexion, $id_estudiante_sesion);
		$r_b_estudiante = mysqli_fetch_array($b_estudiante);
        
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="Content-Language" content="es-ES">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Bolsa Laboral<?php include("../include/header_title.php"); ?></title>
        <!--icono en el titulo-->
        <link rel="shortcut icon" href="../img/favicon.ico">
        <!-- Bootstrap -->
        <link href="../Gentella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="../Gentella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- NProgress -->
        <link href="../Gentella/vendors/nprogress/nprogress.css" rel="stylesheet">
        <!-- iCheck -->
        <link href="../Gentella/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
        <!-- bootstrap-progressbar -->
        <link href="../Gentella/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
        <!-- JQVMap -->
        <link href="../Gentella/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet" />
        <!-- bootstrap-daterangepicker -->
        <link href="../Gentella/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="../Gentella/build/css/custom.min.css" rel="stylesheet">
        <!-- Script obtenido desde CDN jquery -->
        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

        <style>
        .comenzar{
            background-color: #337AB7;
        }
        .proceso{
            background-color: #26B99A;
        }
        .finalizar{
            background-color: #F0AD4E;
        }
        .Finalizado{
            background-color: #D9534F;
        }
        </style>

    </head>
    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <!--menu-->
                <?php

                $per_select = $_SESSION['periodo'];
                include("include/menu.php");
                $b_perido = buscarPeriodoAcadById($conexion, $_SESSION['periodo']);
                $r_b_per = mysqli_fetch_array($b_perido);
                ?>

                <!-- page content -->
                <div class="right_col" role="main">


                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="">
                                    <h2 align="center">Convocatorias Laborales de Empresas</h2>
                                    <br>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                <div class="col-lg-4">
                                    <div><b>Filtrar Por Administrador de Convocatoria: </b></div>
                                        <div class="form-group ">
                                        <select id="filtro_administrado" class="form-control">
                                            <option value="">TODOS</option>
                                            <option value="EMPRESA">EMPRESA</option>
                                            <option value="INSTITUTO">INSTITUTO</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div><b>Filtrar Por Estado: </b></div>
                                        <div class="form-group ">
                                        <select id="filtro_estado" class="form-control">
                                            <option value="">TODOS</option>
                                            <option value="Por comenzar">POR COMENZAR</option>
                                            <option value="En proceso">EN PROCESO</option>
                                            <option value="Finalizado">FINALIZADO</option>
                                        </select>
                                        </div>
                                    </div>
                                    <br><br><br><br>
                                    <div class="">
                                    <table id="convocatorias" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Administrado Por</th>
                                            <th>Nombre de la empresa, persona natural o jurídica</th>
                                            <th>Título de la convocatoria</th>
                                            <th>Lugar de Trabajo</th>
                                            <th>Modalidad</th>
                                            <th>Turno</th>
                                            <th>Salario</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            $resultado1 = buscarOfertasDisponiblesByPrograma($conexion, $r_b_estudiante['id_programa_estudios']);
                                            // Obtener resultados de la segunda consulta
                                            $resultado2 = buscarOfertasEstudiante($conexion, $id_estudiante_sesion);
                                            
                                            // Array para almacenar las ofertas de la primera consulta que no están en la segunda
                                            $diferencia = array();

                                            $cantidad = 0;
                                            
                                            // Iterar sobre los resultados de la primera consulta
                                            while ($oferta1 = mysqli_fetch_assoc($resultado1)) {
                                                // Bandera para indicar si la oferta está en la segunda consulta
                                                $encontrado = false;
                                                // Iterar sobre los resultados de la segunda consulta
                                                while ($oferta2 = mysqli_fetch_assoc($resultado2)) {
                                                    // Si la oferta de la primera consulta está en la segunda, marcar como encontrada y salir del bucle
                                                    if ($oferta1 == $oferta2) {
                                                        $encontrado = true;
                                                        break;
                                                    }
                                                }
                                                // Si la oferta de la primera consulta no está en la segunda, agregarla al array de diferencia
                                                if (!$encontrado) {
                                                    $diferencia[] = $oferta1;
                                                }
                                                // Restablecer el puntero del resultado de la segunda consulta
                                                mysqli_data_seek($resultado2, 0);
                                            }

                                            foreach ($diferencia as $ofertas) {
                                                $cantidad++;
                                        ?>
                                        <tr>
                                            <td><?php echo $cantidad; ?></td>
                                            <td class="green"><i class="fa fa-building"></i> <b>EMPRESA</b></td>
                                            <td><?php echo $ofertas['empresa']; ?></td>
                                            <td><?php echo $ofertas['titulo']; ?></td>
                                            <td><?php echo $ofertas['ubicacion']; ?></td>
                                            <td><?php echo $ofertas['modalidad']; ?></td>
                                            <td><?php echo $ofertas['turno']; ?></td>
                                            <td><?php echo $ofertas['salario']; ?></td>
                                            <td>
                                                <span class="badge <?php echo determinarEstado($ofertas['fecha_inicio'], $ofertas['fecha_fin'])?>"><?php echo determinarEstado($ofertas['fecha_inicio'], $ofertas['fecha_fin']) ?></span>
                                            </td>
                                            <td>
                                                <a href="detalle_convocatoria_empresa.php?id= <?php echo $ofertas['id']?>" class="btn btn-success" data-toggle="tooltip" data-original-title="Ver Detalles" data-placement="bottom"><i class="fa fa-eye"></i></a>
                                        </td>
                                        </tr>  
                                        <?php
                                                };
                                        ?>

                                        <?php 
                                            $resultado1 = buscarOfertasDisponiblesByProgramaIestp($conexion, $r_b_estudiante['id_programa_estudios']);
                                
                                            while ($ofertas = mysqli_fetch_array($resultado1)) {
                                                $cantidad++;
                                                $res_postulado = buscarOfertaPostuladaInstituto($conexion, $ofertas['id']);
                                                $es_postulado = mysqli_fetch_array($res_postulado);
                                                if(!$es_postulado){ ?>
                                                   <tr>
                                                        <td><?php echo $cantidad; ?></td>
                                                        <td class="blue"><i class="fa fa-bank"></i>  <b>INSTITUTO</b></td>
                                                        <td><?php echo $ofertas['empresa']; ?></td>
                                                        <td><?php echo $ofertas['titulo']; ?></td>
                                                        <td><?php echo $ofertas['ubicacion']; ?></td>
                                                        <td><?php echo $ofertas['modalidad']; ?></td>
                                                        <td><?php echo $ofertas['turno']; ?></td>
                                                        <td><?php echo $ofertas['salario']; ?></td>
                                                        <td>
                                                            <span class="badge <?php echo determinarEstado($ofertas['fecha_inicio'], $ofertas['fecha_fin'])?>"><?php echo determinarEstado($ofertas['fecha_inicio'], $ofertas['fecha_fin']) ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="detalle_convocatoria.php?id= <?php echo $ofertas['id']?>" class="btn btn-success" data-toggle="tooltip" data-original-title="Ver Detalles" data-placement="bottom"><i class="fa fa-eye"></i></a>
                                                    </td>
                                                    </tr>  
                                                <?php} ?>
                                        
                                        <?php } }?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /page content -->

                <!-- footer content -->
                <?php
                include("../include/footer.php");
                ?>
                <!-- /footer content -->
            </div>
        </div>

        <!-- jQuery -->
        <script src="../Gentella/vendors/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../Gentella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- FastClick -->
        <script src="../Gentella/vendors/fastclick/lib/fastclick.js"></script>
        <!-- NProgress -->
        <script src="../Gentella/vendors/nprogress/nprogress.js"></script>
        <!-- iCheck -->
        <script src="../Gentella/vendors/iCheck/icheck.min.js"></script>
        <!-- Datatables -->
        <script src="../Gentella/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="../Gentella/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="../Gentella/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="../Gentella/vendors/jszip/dist/jszip.min.js"></script>
        <script src="../Gentella/vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="../Gentella/vendors/pdfmake/build/vfs_fonts.js"></script>

        <!-- Custom Theme Scripts -->
        <script src="../Gentella/build/js/custom.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#convocatorias').DataTable({
                    "language": {
                        "processing": "Procesando...",
                        "lengthMenu": "Mostrar _MENU_ registros",
                        "zeroRecords": "No se encontraron resultados",
                        "emptyTable": "Ningún dato disponible en esta tabla",
                        "sInfo": "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
                        "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "search": "Buscar:",
                        "infoThousands": ",",
                        "loadingRecords": "Cargando...",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        },
                    }
                });

            });
        </script>
    <script>
      $(document).ready(function () {
        var table = $('#convocatorias').DataTable();

        // Custom filter for Programa de Estudios
        $('#filtro_administrado').on('change', function () {
          var filtro = $(this).val();
          table.column(1).search(filtro).draw();
        }    
      );
      });
    </script>

    <script>
      $(document).ready(function () {
        var table = $('#convocatorias').DataTable();
        // Filtro por estado
        $('#filtro_estado').on('change', function () {
          var filtro = $(this).val();
          table.column(8).search(filtro).draw();
        }
                
      );
      });
    </script>

        <?php mysqli_close($conexion); ?>
    </body>

    </html>
<?php }
