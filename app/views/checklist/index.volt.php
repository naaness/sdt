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

    <div class="text-center">
        <h1  id="ch-title"><?php echo $title; ?></h1>
        <button type="button" id="demo" class="btn btn-default btn-lg" data-demo="">
            <span class="glyphicon glyphicon-play"></span>
            Tutorial
        </button>
    </div>
    <ul class="nav nav-pills">
        <li role="presentation" id="goToType-type0"><a>Tareas Pendientes</a></li>
        <li role="presentation" id="goToType-type1"><a>Checklist General</a></li>
        <li role="presentation" id="goToType-type2"><a>Mis proyectos</a></li>
        <li role="presentation" id="goToType-type3"><a>Proyectos</a></li>
        <li role="presentation" id="goToType-type4"><a>Delegadas a mi</a></li>
        <li role="presentation" id="goToType-type5"><a>Mis delegadas</a></li>
        <li role="presentation" id="goToType-type6"><a>Mis Tareas</a></li>
    </ul>
    <div id="ch_content"class="">
        <div id="main_container" style="padding:0; margin:0;" class="text-center">
            <div id="drag" style="height:400px">
                <table id="tb" style="width:100%" >
                    <colgroup>
                        <col width="30%"/>
                        <col width="30%"/>
                        <col width="44%"/>
                        <col width="1%"/>
                    </colgroup>
                    <tbody>
                    <tr class="rl">
                        <td class="only cdark" id="ch-name-range">

                        </td>
                        <td class="only cdark">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="ch_btn_left">
                                        <span class="glyphicon glyphicon-chevron-left"></span>
                                    </button>
                                </span>
                                <select id="rangoVista" style="display: inline-block;" class="form-control" >
                                    <option value='week' <?php if ($range == 'week') { ?>selected<?php } ?>>semana</option>
                                    <option value="month" <?php if ($range == 'month') { ?>selected<?php } ?>>mes</option>
                                    <option value="trimestre" <?php if ($range == 'trimestre') { ?>selected<?php } ?>>trimestre</option>
                                </select>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="ch_btn_hoy" data-toggle="tooltip" title="Ir al dia de hoy" data-placement="bottom">
                                        Hoy
                                    </button>
                                </span>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="ch_btn_right">
                                        <span class="glyphicon glyphicon-chevron-right"></span>
                                    </button>
                                 </span>
                            </div>
                        </td>
                        <td class="only cdark">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default active" id="ch-mode-1" >
                                    <input type="radio" name="options" autocomplete="off" checked>Normal
                                </label>
                                <label class="btn btn-default" id="ch-mode-2" >
                                    <input type="radio" name="options" autocomplete="off">Agregar
                                </label>
                                <label class="btn btn-default" id="ch-mode-3" >
                                    <input type="radio" name="options" autocomplete="off">Eliminar
                                </label>
                                <label class="btn btn-default" id="ch-mode-4" >
                                    <input type="radio" name="options" autocomplete="off">Mover
                                </label>
                            </div>
                        </td>
                        <td class="only cdark">

                        </td>

                    </tr>
                    </tbody>
                </table>
                <div id="tabla" style="height:350px;position: relative;">
                    <div class="ch-give-width">
                        <div style="position: relative" class="ch-give-width">
                            <table id="tb0" class="table table-condensed ">
                                <tbody>
                                <tr class="rl" id="ch-head1">
                                </tr>
                                <tr class="rl" id="ch-head2" style="font-size: 10pt">
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="position: relative;top: 0px" class="ch-give-width">
                            <table id="tbl" class="table table-condensed">
                                <colgroup>
                                </colgroup>
                                <tbody id="ch-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
                    <div class="alert alert-success" role="alert" id="sdt-success-tranfer" style="display:none">
                        <strong>Bien!</strong> Es una fecha válida para transferir la tarea.
                    </div>
                    <div class="alert alert-danger" role="alert" id="sdt-danger-tranfer"  style="display:none">
                        <strong>Ops!</strong> Recuerda, debe ser una fecha futura.
                    </div>

                    <input type="hidden" name="id_dia" id="sdt-dia" value="" />
                    <input type="hidden" name="fecha_trans" id="fecha_trans" value="" />
                    <input type="hidden" name="obj_json" id="sdt-id_td" value="" />
                    <label for="nombre_tarea">Fecha donde desea trasladar esta tarea</label>
                    <input value="" type="text" class="form-control datepicker" id="sdt-fecha_traslado" placeholder="Fecha de traslado"/>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="sdt-guadar-traslado" data-loading-text="Guardando..." data-dismiss="modal" disabled>Guardar Cambios</button>
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
                    <label for="sdt-new-task-project">Selecione el Proyecto</label>
                    <select class="form-control" id="sdt-new-task-project" >
                    </select>
                    <label for="nombre_tarea">Nombre de la Tarea</label>
                    <input value="" type="text" class="form-control" id="sdt-new-task-name" placeholder="Nombre de la tarea"/>
                    <label for="nombre_tarea">Descripcion</label>
                    <textarea class="form-control" id="sdt-new-task-description"></textarea>
                    <label for="fecha_tarea">Fecha de terminación o entrega</label>
                    <input class="form-control datepicker" id="sdt-new-task-fecha" value="<?php echo $hoy; ?>"></input>
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
                        <button class="btn btn-primary" id="sdt-new-task-submit" style="width:100%" disabled="true">Crear</button>
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

    <!-- Modal -->
    <div class="modal fade" id="myModalRespTarea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Confirma realizar esta tarea?</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ch-RespTareaId" value="0" />
                    <input type="hidden" id="ch-respuesta" value="1" />
                    <div class="form-group col-md-12">
                        <div class="btn-group btn-group-justified" role="group"  data-toggle="buttons">
                            <label class="btn btn-default" id="respuesta_si">
                                <input class="importa" type="radio" name="respues" value="1"> Sí
                            </label>
                            <label class="btn btn-default" id="respuesta_no">
                                <input class="importa" type="radio" name="respues" value="3"> No
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <textarea id="ch-resp-comentario" class="form-control" placeholder="Al rechazar la tarea debe justifiar el porque" style="display:none"></textarea>
                    </div>
                    <div>
                        <button class="form-control" id="submit_respuesta">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-chat-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <span class="glyphicon glyphicon-comment"></span> <span id="sdt-chat-title"></span>
                </div>
                <div class="modal-body" id="panel-body">
                    <ul class="chat" id="panel-body-chat">

                    </ul>
                </div>
                <div class="panel-footer">
                    <div class="input-group">
                        <input type="hidden" id="sdt-task-chat"  value="0"/>
                        <input id="btn-input-chat" type="text" class="form-control input-sm" placeholder="Escriba su mensaje aqui..." />
                        <span class="input-group-btn">
                            <button class="btn btn-warning btn-sm" id="btn-chat">
                                Enviar</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-repeat-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    Repetir
                </div>
                <div class="modal-body sdt-task-repeat" id="sdt-repeat-task-html">

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