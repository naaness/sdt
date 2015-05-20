<!DOCTYPE html>
<html lang="es" data-ng-app="app">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Sistema del Dominio del tiempo">
        <meta name="author" content="nestor.andres.a@gmail.com">
        <link href="{{ url() }}/img/phalcon.ico" rel="icon" type="image/ico">
        <link rel="stylesheet" type="text/css" href="{{ url() }}/css/loader.css">
        {{ assets.outputCss() }}
    </head>
    <body id="body-color">
    <div class="container">
    {% block content %}
        <!-- Collect the nav links, forms, and other content for toggling -->
        <nav class="navbar navbar-inverse navbar-fixed-top" id="navbar-color">
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
                    {% if session.has('userId') %}
                        <ul class="nav navbar-nav navbar-left">
                            <li><a href="{{ url() }}rmregistries" data-toggle="tooltip" data-placement="bottom" title="Registro Maestro"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></li>
                            <li><a data-toggle="tooltip" data-placement="bottom" title="Checklist"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></a></li>
                            <li><a href="{{ url() }}htd" data-toggle="tooltip" data-placement="bottom" title="Hoja de Trabajo DIario"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></a></li>
                        </ul>
                    {% endif %}
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="{{ url() }}">Inicio</a></li>
                        {% if v_session is defined %}
                            {% if session.has('userId') %}
                                <li><a id="ver-pasos-dia">Planeacion diaria</a></li>
                                <li><a href="{{ url() }}posts">Foro</a></li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Perfil <span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ url() }}profile">Informacion General</a></li>
                                        <li class="divider"></li>
                                        <li><a href="{{ url() }}profile/changePassword">Cambiar Contraseña</a></li>
                                    </ul>
                                </li>
                                {% if type_role is defined %}
                                    {% if type_role == "admin" Or type_role == "superadmin" %}
                                        <li class="dropdown">
                                            <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin <span class="caret"></span></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li class="dropdown-header">Access Control</li>
                                                <li><a href="{{ url() }}users">Users</a></li>
                                            </ul>
                                        </li>
                                    {% endif %}
                                {% endif %}
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="alert-user">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                        <span class="caret"></span>
                                    </a>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="alert-alert">
                                        {% set hide_count = true %}
                                        {% if numitemsalert is defined %}
                                            {% if numitemsalert > 0 %}
                                                {% set hide_count = false %}
                                            {% endif %}
                                        {% endif %}
                                        <span class="label label-danger" id="cont-alert" {% if hide_count %} style="display: none;{% endif %}">{% if not hide_count %}{{ numitemsalert }}{% endif %}</span>
                                        <span class="glyphicon glyphicon-bell" aria-hidden="true"></span>
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" id="content-alert">
                                        {% if itemsalert is defined %}
                                            {{ itemsalert }}
                                        {% endif %}
                                    </ul>

                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="alert-notification">
                                        {% set hide_count = true %}
                                        {% if con_notification is defined %}
                                            {% if con_notification > 0 %}
                                                {% set hide_count = false %}
                                            {% endif %}
                                        {% endif %}
                                        <span class="label label-danger" id="cont-notification" {% if hide_count %} style="display: none;{% endif %}">{% if not hide_count %}{{ con_notification }}{% endif %}</span>
                                        <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" id="content-notification">
                                        {% if itemsnotification is defined %}
                                            {{ itemsnotification }}
                                        {% endif %}
                                    </ul>
                                </li>
                                <li><a href="{{ url() }}logout">Salir</a></li>
                            {% else %}
                                <li><a href="{{ url() }}login">Login</a></li>
                            {% endif %}
                        {% else %}
                            <li><a href="{{ url() }}login">Login</a></li>
                        {% endif %}
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
                        <h4 class="modal-title" id="myModalLabel">5 Pasos para la Planeacion Diaria
                            <button type="button" id="sdt-cinco-pasos" class="btn btn-default btn-xs">Desactivar</button>
                        </h4>

                    </div>
                    <div class="modal-body">
                        <table class="table table-hover">
                            <tbody>
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
                                <td><em>Calendario Mensual.</em> <small>Si hay algo para hoy copialo a tu agenda del dia.</small></td>
                                <td>
                                    <input type="checkbox" name="rm-cinco-pasos-3" id="rm-cinco-pasos-3" value="0" aria-label="Checkbox without label text">
                                </td>
                            </tr>
                            <tr>
                                <td> -</td>
                                <td><em>Agenda del dia de hoy.</em> <small>Revisa citas que habias programado.</small></td>
                                <td>
                                    <input type="checkbox" name="rm-cinco-pasos-4" id="rm-cinco-pasos-4" value="0" aria-label="Checkbox without label text">
                                </td>
                            </tr>
                            <tr>
                                <td> -</td>
                                <td><em>Agenda semanal.</em> <small>Copia de la agenda semanal "por default" a la agenda de hoy.</small></td>
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
                                <td><em>Revisa tareas de ayer sin cerrrar.</em> <small>Deben estar completadas, eliminadas o transferidas.</small></td>
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
                                <td><em>Identifica que tareas puedes delegar.</em> <small>Identifica a quien y define el pedido.</small></td>
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
                                <td><em>Para cada punto planea.</em><small>Decide que hacer yprograma citas y tareas futuras.</small></td>
                                <td>
                                    <input type="checkbox" name="rm-cinco-pasos-10" id="rm-cinco-pasos-10" value="0" aria-label="Checkbox without label text" >
                                </td>
                            </tr>
                            <tr>
                                <td> -</td>
                                <td><em>Revisa tu Registro Maestro Satelite.</em> <small></small></td>
                                <td>
                                    <input type="checkbox" name="rm-cinco-pasos-11" id="rm-cinco-pasos-11" value="0" aria-label="Checkbox without label text" >
                                </td>
                            </tr>
                            <tr class="active">
                                <td><strong>5.</strong></td>
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

        {% if breadcrumb is defined %}
            {{ breadcrumb }}
        {% endif %}

        {#Efecto de cargado#}
        <div class="spinner" id="spinner" style="position: fixed;display: none">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    {% endblock %}
    </div>
    {{ assets.outputJs() }}
    </body>
</html>