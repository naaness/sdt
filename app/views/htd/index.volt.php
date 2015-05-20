<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Sistema del Dominio del tiempo">
        <meta name="author" content="nestor.andres.a@gmail.com">
        <link href="<?php echo $this->url->get(); ?>/img/phalcon.ico" rel="icon" type="image/ico">
        <title> <?php echo $title_view; ?></title>
        
            <link rel="stylesheet" type="text/css" href="<?php echo $this->url->get(); ?>/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="<?php echo $this->url->get(); ?>/css/loader.css">
            <link rel="stylesheet" type="text/css" href="<?php echo $this->url->get(); ?>/css/offline/offline.css">
            <link rel="stylesheet" type="text/css" href="<?php echo $this->url->get(); ?>/css/offline/offline-language-spanish.css">

        
        <?php echo $this->assets->outputCss(); ?>
    </head>
    <body <?php if (isset($body_color)) { ?>style="background-color:<?php echo $body_color; ?>" <?php } ?> id="body-color">
    <div class="container">
        
    
            <!-- Collect the nav links, forms, and other content for toggling -->
            <nav class="navbar navbar-inverse navbar-fixed-top" <?php if (isset($navbar_color)) { ?>style="background-color:<?php echo $navbar_color; ?>" <?php } ?> id="navbar-color">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="/">SDT</a>
                    </div>
                    <div id="navbar" class="collapse navbar-collapse">
                        <?php if ($this->session->has('userId')) { ?>
                            <ul class="nav navbar-nav navbar-left">
                                <li><a href="<?php echo $this->url->get(); ?>rmregistries" data-toggle="tooltip" data-placement="bottom" title="Registro Maestro"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></li>
                                <li><a href="<?php echo $this->url->get(); ?>checklist" data-toggle="tooltip" data-placement="bottom" title="Checklist"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></a></li>
                                <li><a href="<?php echo $this->url->get(); ?>htd" data-toggle="tooltip" data-placement="bottom" title="Hoja de Trabajo DIario"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></a></li>
                            </ul>
                        <?php } ?>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="<?php echo $this->url->get(); ?>">Inicio</a></li>
                            <?php if (isset($v_session)) { ?>
                                <?php if ($this->session->has('userId')) { ?>
                                    <li><a href="#" id="ver-pasos-dia">Planeacion diaria</a></li>
                                    <li><a href="<?php echo $this->url->get(); ?>posts">Foro</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Perfil <span class="caret"></span></a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="<?php echo $this->url->get(); ?>profile">Informacion General</a></li>
                                            <li><a href="<?php echo $this->url->get(); ?>profile/changePassword">Cambiar Contraseña</a></li>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo $this->url->get(); ?>teams">Equipos de trabajo</a></li>
                                        </ul>
                                    </li>
                                    <?php if (isset($type_role)) { ?>
                                        <?php if ($type_role == 'admin' || $type_role == 'superadmin') { ?>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin <span class="caret"></span></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li class="dropdown-header">Access Control</li>
                                                    <li><a href="<?php echo $this->url->get(); ?>users">Usuarios</a></li>
                                                    <?php if ($this->session->get('userId') == 24 || $this->session->get('userId') == 5 || $this->session->get('userId') == 39) { ?>
                                                        <li><a href="<?php echo $this->url->get(); ?>dailyplanning">Planeación diaria</a></li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="alert-user">
                                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                            <span class="caret"></span>
                                        </a>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="alert-alert">
                                            <?php $hide_count = true; ?>
                                            <?php if (isset($numitemsalert)) { ?>
                                                <?php if ($numitemsalert > 0) { ?>
                                                    <?php $hide_count = false; ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <span class="label label-danger" id="cont-alert" <?php if ($hide_count) { ?> style="display: none;<?php } ?>"><?php if (!$hide_count) { ?><?php echo $numitemsalert; ?><?php } ?></span>
                                            <span class="glyphicon glyphicon-bell" aria-hidden="true"></span>
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu" role="menu" id="content-alert">
                                            <?php if (isset($itemsalert)) { ?>
                                                <?php echo $itemsalert; ?>
                                            <?php } ?>
                                        </ul>

                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="alert-notification">
                                            <?php $hide_count = true; ?>
                                            <?php if (isset($con_notification)) { ?>
                                                <?php if ($con_notification > 0) { ?>
                                                    <?php $hide_count = false; ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <span class="label label-danger" id="cont-notification" <?php if ($hide_count) { ?> style="display: none;<?php } ?>"><?php if (!$hide_count) { ?><?php echo $con_notification; ?><?php } ?></span>
                                            <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu" role="menu" id="content-notification">
                                            <?php if (isset($itemsnotification)) { ?>
                                                <?php echo $itemsnotification; ?>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <li><a href="<?php echo $this->url->get(); ?>logout" id="sdt-logout">Salir</a></li>
                                <?php } else { ?>
                                    <li><a href="<?php echo $this->url->get(); ?>login">Login</a></li>
                                <?php } ?>
                            <?php } else { ?>
                                <li><a href="<?php echo $this->url->get(); ?>login">Login</a></li>
                            <?php } ?>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </nav>

            <!-- Modal -->
            <div class="modal fade" id="myModalFiveSteps" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel">Pasos para la Planeacion Diaria
                                <button type="button" id="sdt-cinco-pasos" class="btn btn-default btn-xs">Desactivar</button>
                            </h4>

                        </div>
                        <div class="modal-body">
                            <table class="table table-hover">
                                <tbody id="body-daily-planning">
                                <tr class="active">
                                    <td><strong>1.</strong></td>
                                    <td><strong>Encuentra el foco y la emocion adecuada.</strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><em>Visualiza - ... un cuadro inspirador del futuro.</em></td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-1" id="rm-cinco-pasos-1" value="0" aria-label="Checkbox without label text">
                                    </td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><em>Asegurate de no tener distracciones</em>.</td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-2" id="rm-cinco-pasos-2" value="0" aria-label="Checkbox without label text">
                                    </td>
                                </tr>
                                <tr class="active">
                                    <td><strong>2.</strong></td>
                                    <td><strong>Revisa citas programadas para hoy.</strong></td>
                                    <td></td>
                                </tr>
                                
                                    
                                    
                                    
                                        
                                    
                                
                                
                                    
                                    
                                    
                                        
                                    
                                
                                <tr>
                                    <td> -</td>
                                    <td><em>Agenda semanal.</em> <small>Revisa la agenda semanal por defecto.</small></td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-5" id="rm-cinco-pasos-5" value="0" aria-label="Checkbox without label text" >
                                    </td>
                                </tr>
                                <tr class="active">
                                    <td><strong>3.</strong></td>
                                    <td><strong>Revisa tareas.</strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><em>Revisa tareas de ayer sin completar.</em> <small>Deben estar completadas, eliminadas o transferidas.</small></td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-6" id="rm-cinco-pasos-6" value="0" aria-label="Checkbox without label text">
                                    </td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><em>Identifica tareas para hoy.</em><small></small></td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-7" id="rm-cinco-pasos-7" value="0" aria-label="Checkbox without label text" >
                                    </td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><em>Identifica que tareas puedes delegar.</em> <small>Desde tu checklist o HTD.</small></td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-8" id="rm-cinco-pasos-8" value="0" aria-label="Checkbox without label text" >
                                    </td>
                                </tr>
                                <tr class="active">
                                    <td><strong>4.</strong></td>
                                    <td><strong>Planea y programa.</strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><em>Revisa tu registro maestro de ayer.</em> <small>100% de tu atencion a cada punto registrado.</small></td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-9" id="rm-cinco-pasos-9" value="0" aria-label="Checkbox without label text" >
                                    </td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><em>Para cada punto planea.</em><small>Decide que hacer y programa citas y tareas futuras.</small></td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-10" id="rm-cinco-pasos-10" value="0" aria-label="Checkbox without label text" >
                                    </td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><em>Revisa tus indicadores, metas, proyectos personales, CRM's, reportes, etc.</em> <small></small></td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-11" id="rm-cinco-pasos-11" value="0" aria-label="Checkbox without label text" >
                                    </td>
                                </tr>
                                <tr class="active">
                                    <td><strong>5.</strong></td>
                                    <td><strong>Revisa tus notificaciones</strong></td>
                                    <td></td>
                                </tr>
                                <tr class="active">
                                    <td><strong>6.</strong></td>
                                    <td><strong>Personaliza este paso.</strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td> -</td>
                                    <td><input type="text" class="form-control" id="rm-cinco-text-12" placeholder="Ingresa aqui tu paso personalizado" value="" maxlength="100"> </td>
                                    <td>
                                        <input type="checkbox" name="rm-cinco-pasos-12" id="rm-cinco-pasos-12" value="0" aria-label="Checkbox without label text" >
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <br/>
            <br/>
            <?php if (isset($breadcrumb)) { ?>
                <?php echo $breadcrumb; ?>
            <?php } ?>
            
            <div class="spinner" id="spinner" style="position: fixed;display: none">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
            <div >
                <div class="pull-left">
                    <nav>
                        <ul class="pager">
                            <li disabled id="sdt-back-page">
                                <a style="color: #000000">
                                    <i class="icon-white glyphicon glyphicon-chevron-left"></i>
                                    Regresar
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="pull-right">
                    <nav>
                        <ul class="pager">
                            <li disabled id="sdt-next-page">
                                <a style="color: #000000">
                                    Siguiente
                                    <i class="icon-black glyphicon glyphicon-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        
    <?php echo $this->flash->output(); ?>
    <input type="hidden" id="htd-today" value="<?php echo $today; ?>">
    <input type="hidden" id="htd-htd-token" value="">

    <h1 class="text-center" id="sdt-htd-date-string"></h1>
    <?php if ($path != '') { ?>
        <div class="bootstrap_buttons">
            <a href="<?php echo $path; ?>">
                <button type="button" class="reset btn btn-success" data-column="0" data-filter=""><i class="icon-white icon-transfer glyphicon glyphicon-transfer"></i> Sincronizar con Google Calendar</button>
            </a>
        </div>
        <br/>
        <a href="https://security.google.com/settings/security/permissions?pli=1">Si ya ha sincronizado y presenta problemas, desactive sus permisos aquí.</a>
    <?php } else { ?>
        <div class="bootstrap_buttons">
            <a href="https://security.google.com/settings/security/permissions?pli=1">
                <button type="button" class="reset btn btn-danger" data-column="0" data-filter=""><i class="icon-white icon-transfer glyphicon glyphicon-transfer"></i> Desactivar Sincronizacion</button>
            </a>
        </div>
    <?php } ?>


    <div id="hoja_diario" class="col-md-12" style="height : 300px;">
        <div id="head-htd" class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <div class="col-md-3 col-md-offset-4">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">Fecha </button>
                            </span>
                            <input type="text" class="form-control datepicker" placeholder="Buscar fecha" value="<?php echo $today; ?>" id="htd_searchdate">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="htd-today-go">Hoy</button>
                            </span>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                </div>
            </div>
        </div>
        <div id="calendar" style="width:50%;height : 300px; float: left;" >

        </div>
        <div id="htd_contenedor" class="list-group" style="width:48%; overflow-y: scroll;height : 455px; float: right;" >

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-transfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Trasladar Tarea</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_dia" id="sdt-dia" value="" />
                    <input type="hidden" name="fecha_trans" id="fecha_trans" value="" />
                    <input type="hidden" name="obj_json" id="sdt-id_td" value="" />
                    <label for="nombre_tarea">Fecha donde desea trasladar esta tarea</label>
                    <input value="" type="text" class="form-control datepicker" id="sdt-fecha_traslado" placeholder="Fecha de traslado"/>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="sdt-guadar-traslado" data-loading-text="Guardando..." data-dismiss="modal">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-new-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Nueva Tarea</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="obj_json" id="sdt-date" value="" />
                    <input type="hidden" name="sdt-new-task-fecha" id="sdt-new-task-fecha" value="" />

                    <label for="sdt-new-task-project">Selecione el Proyecto</label>
                    <select class="form-control" id="sdt-new-task-project" >
                    </select>
                    <label for="nombre_tarea">Nombre de la Tarea</label>
                    <input value="" type="text" class="form-control" id="sdt-new-task-name" placeholder="Nombre de la tarea"/>
                    <label for="nombre_tarea">Descripcion</label>
                    <textarea class="form-control" id="sdt-new-task-description"></textarea>
                    <label for="sdt-new-task-delegate">Delegar a</label>
                    <select class="form-control" id="sdt-new-task-delegate" required="required">
                    </select>
                    <label for="nombre_tarea">Prioridad</label>
                    <select class="form-control" id="sdt-new-task-priority" required="required">
                        <option value="1">Alta</option>
                        <option value="2">Media</option>
                        <option value="3">Baja</option>
                        <option value="4">Informativa</option>
                    </select>
                    <div>
                        <button class="btn btn-primary" id="sdt-new-task-submit" style="width:100%" >Crear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-actual-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Editar Tarea</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sdt-actual-task-id" value="" />
                    <label for="nombre_tarea">Nombre de la Tarea</label>
                    <input value="" type="text" class="form-control" id="sdt-actual-task-name" placeholder="Nombre de la tarea"/>
                    <label for="nombre_tarea">Descripcion</label>
                    <textarea class="form-control" id="sdt-actual-task-description"></textarea>
                    <label for="nombre_tarea">Prioridad</label>
                    <select class="form-control" id="sdt-actual-task-priority" required="required">
                        <option value="1">Alta</option>
                        <option value="2">Media</option>
                        <option value="3">Baja</option>
                        <option value="4">Informativa</option>
                    </select>
                    <div>
                        <button class="btn btn-primary" id="sdt-actual-task-submit" style="width:100%" >Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-rm-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="contendor-rm-htd-temp">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-info-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Informacion</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sdt-info-task-id" value="" />
                    <div class="row" id="task-info-content">
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>
    <div id="footer">
        

        
    </div>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/offline/offline.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/jquery.min.js"></script>
    <?php echo $this->assets->outputJs(); ?>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/sdt_general/jquery.html5storage.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/sdt_general/back_next_page.js"></script>

    </body>
</html>