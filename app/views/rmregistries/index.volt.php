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
    <h1 class="text-center" id="sdt-rm-date-string"></h1>
    <input type="hidden" id="ch-rm-token" value="<?php echo $code_token; ?>">
    <div class="row">
        <div id="registro_maestro" class="col-md-12" style="height : 500px; position: relative;">
            <div id="dv" style="display: none;">
                <table id="tblExport" style="border: 1px solid black;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Registro</th>
                    </tr>
                    </thead>
                    <tbody id="body_rm_none">
                    </tbody>
                </table>
            </div>
            <div id="rm_general">
                <div class="row">
                    <div class="col-lg-8" >
                        <div style="font-size: 25px">
                            <strong>RM</strong> <?php echo $name_user; ?>
                        </div>
                    </div><!-- /input-group -->

                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" id="rm_left_day">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                </button>
                            </span>
                            <input type="text" class="form-control datepicker" id="rm_searchdate" name="vigencia" placeholder="Buscar Fecha" value="<?php echo $fechahoy; ?>">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" id="rm_btn_hoy" data-toggle="tooltip" title="Ir al dia de hoy" data-placement="bottom">
                                    Hoy
                                </button>
                                <button type="button" class="btn btn-default" id="rm_right_day">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                </button>
                                <button type="button" class="btn btn-default" id="sync-rm">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                </button>
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="rm-tools">
                                    <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a data-toggle="modal" data-target="#rm_searchRM" id="bt-searchRM"> <span class="glyphicon glyphicon-search"></span> Buscar</a></li>
                                    <li id="rm-bt-email"><a data-toggle="modal" data-target="#rm_btn_mail"> <span class="glyphicon glyphicon-envelope"></span> Enviar por Correo</a></li>
                                    <li id="rm-bt-goTags"><a href="<?php echo $this->url->get(); ?>rmlabels"> <span class="glyphicon glyphicon-tags"></span> Etiquetas</a></li>
                                    <li><a data-toggle="modal" data-target="#rm_btn_help" > <span class="glyphicon glyphicon-question-sign"></span> Comandos</a></li>
                                </ul>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="progress" style="margin: 0px; height:5px">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" id="rm_progress_success" style="width: 0%; ">
                            </div>
                            <div class="progress-bar progress-bar-danger progress-bar-striped active" id="rm_progress_danger" style="width: 0%; ">
                            </div>
                        </div>
                    </div><!-- /input-group -->
                </div><!-- /.row -->
            </div>
            <div id="rm_contenido" style="height: 500px; overflow-y: scroll;">
                <div class="notebook notebook-1" >
                    <div class="rm-tr" id="tr_new" style="height: 470px;"><div class="rm-number-dummy" style="margin-left: 10px;"></div><div class="rm-content-blackboard" style="margin-left: 130px;"><div class="rm-blackboard " style="margin-left: 10px; word-break: break-all;"><span><small><em>Click aqui para iniciar a escribir...   </em></small><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></span></div></div></div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $security_j; ?>
    <input type="hidden" id="fecha_hoy" value="<?php echo $fechahoy; ?>">

    <!-- Modal -->
    <div class="modal fade" id="rm_btn_help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Comandos Principales</h4>
                </div>
                <div class="modal-body">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <h1>ENTER</h1>
                            <p>Crea un nuevo registro (linea).</p>
                        </div>
                    </div>
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <h1>SHIFT+ENTER</h1>
                            <p>Crea un nuevo parrafo dentro de una linea.</p>
                        </div>
                    </div>
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <h1>TAB</h1>
                            <p>Desplaza el registro a la derecha. El numeral dependera del registro inmediatamento anterior.</p>
                        </div>
                    </div>
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <h1>SHIFT+TAB</h1>
                            <p>Desplaza el registro a la izquierda. El numeral dependera de los registros anteriores.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Buscar Palabra-->
    <div class="modal fade" id="rm_searchRM" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Buscar palabra</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="sword-to-search">
                        <span class="input-group-btn">
                            <button type="button" id="loading-sowrds-rm" data-loading-text="Buscar" class="btn btn-primary">
                                Buscar
                            </button>
                        </span>
                    </div><!-- /input-group -->
                    <p></p>
                    <div class="list-group" id="lista-Palabras">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Enviar correo -->
    <div class="modal fade" id="rm_btn_mail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Enviar Email</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="enviarmail" value="1" />
                    <input type="hidden" name="dia" id="dia" value="{$fecha}" />
                    <div class="input-group" style="width:100%">
                        <span class="input-group-addon" style="width:70px">Email</span>
                        <input type="text" class="form-control" name="correo" id="correo" placeholder="Correo Electronico" />
                    </div>
                    <div class="input-group" style="width:100%">
                        <span class="input-group-addon" style="width:70px">Asunto</span>
                        <input type="text" class="form-control" name="asunto" id="asunto" placeholder="Titulo del correo" value="<?php echo $subject_email; ?>" />
                    </div>
                    <div class="alert alert-info" role="alert">
                        <strong>Aviso!</strong> Podra usar las siguientes palabras en el asunto:
                        <br/>
                        USERNAME =  <?php echo Phalcon\Text::upper($username); ?><br/>
                        TIME     =  <?php echo $fechahoy; ?> <br/>
                        DATE     =  <?php echo $fechahoySmart; ?> <br/>
                        <a href="<?php echo $this->url->get(); ?>profile">Click aqui para editar el asunto por defecto</a>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="submit_email" style="width:100%" >Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModalRMtoHTD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Pasar de RM a HTD</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group col-md-12">
                        <input type="hidden" id="rm-id-rm" value="0" />
                        <label for="rm_project_task">Selecione el Proyecto</label>
                        <select class="form-control" id="rm_project_task" >
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="rm_name_to_htd">Titulo de la Tarea</label>
                        <input value="" type="text" class="form-control" id="rm_name_to_htd" placeholder="Titulo del registro"/>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="rm_name_to_htd">Descripcion de la Tarea</label>
                        <textarea class="form-control" id="rm_description_to_htd"></textarea>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="rm_date_to_htd">Fecha de traslado</label>
                        <input type="text" class="form-control datepicker" id="rm_date_to_htd" placeholder="Buscar Fecha" value="<?php echo $fechahoy; ?>"/>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="rm_delegate">Delegar a</label>
                        <select class="form-control" id="rm_delegate" >
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="nombre_tarea">Prioridad</label>
                        <select class="form-control" id="rm_priority_to_htd" required="required">
                            <option value="1">Alta</option>
                            <option value="2">Media</option>
                            <option value="3">Baja</option>
                            <option value="4">Informativa</option>
                        </select>
                    </div>

                    <div>
                        <button class="form-control" id="htd-tarea-tarea">Enviar</button>
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