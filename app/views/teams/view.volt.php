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
            <div>
                <div class="pull-left">
                    <button type="button" class="reset btn btn-default" disabled id="sdt-back-page" >
                        <i class="icon-white glyphicon glyphicon-chevron-left"></i>
                        Regresar
                    </button>
                </div>

                <div class="pull-right">
                    <button type="button" class="reset btn btn-default" disabled id="sdt-next-page" >
                        Siguiente
                        <i class="icon-black glyphicon glyphicon-chevron-right"></i>
                    </button>
                </div>
            </div>
        
    <h1 class="text-center">Equipo de Trabajo : <?php echo $this->escaper->escapeHtml($this->escaper->escapeJs($name)); ?></h1><hr>

    <div class="list-group">
        <a href="<?php echo $this->url->get(); ?>projects/index/<?php echo $id; ?>" class="list-group-item">Ver Proyectos</a>
        <a href="<?php echo $this->url->get(); ?>packages/index/<?php echo $id; ?>" class="list-group-item">Ver Checklist</a>
    </div>

    <h3>Integrantes</h3>
    <table class="table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($page->items as $item) { ?>
            <tr>
                <td><a href="<?php echo $this->url->get(); ?>users/view/<?php echo $item->id; ?>"><?php echo $this->escaper->escapeHtml($this->escaper->escapeJs($item->username)); ?></a></td>
                <td><?php if ($item->status == 1) { ?><span class="label label-success">Activo</span><?php } else { ?><span class="label label-danger">No activo</span><?php } ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tbody>
        <tr>
            <td colspan="4">
                <div align="center">
                    <?php echo $this->tag->linkTo(array('teams/view/' . $id, '<i class="glyphicon glyphicon-fast-backward"></i> Primera', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->tag->linkTo(array('teams/view/' . $id . '?page=' . $page->before, '<i class="glyphicon glyphicon-backward"></i> Anterior', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->tag->linkTo(array('teams/view/' . $id . '?page=' . $page->next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->tag->linkTo(array('teams/view/' . $id . '?page=' . $page->last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>', 'class' => 'btn btn-default')); ?>
                    Página <?php echo $page->current; ?> de <?php echo $page->total_pages; ?>
                </div>
            </td>
        <tr>
        </tbody>
    </table>

    </div>
    <div id="footer">
        

        
    </div>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/jquery.min.js"></script>
    <?php echo $this->assets->outputJs(); ?>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/sdt_general/jquery.html5storage.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->url->get(); ?>/js/sdt_general/back_next_page.js"></script>
    </body>
</html>