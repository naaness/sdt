{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}

    <h1 class="text-center">Checklist General</h1>
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{ url() }}teams">Administracion de Tareas</a></li>
        <li role="presentation"><a href="{{ url() }}checklist/myProjects">Mis proyectos</a></li>
        <li role="presentation"><a href="{{ url() }}checklist/projects">Proyectos</a></li>
        <li role="presentation"><a href="{{ url() }}checklist/delegatesToMe">Delegadas a mi</a></li>
        <li role="presentation"><a href="{{ url() }}checklist/myDelegates">Mis delegadas</a></li>
        <li role="presentation"><a href="{{ url() }}tasks">Mis Tareas</a></li>
    </ul>
    <div class="row">
        <div id="checklist" class="col-md-12" style="height : 500px;">
            <div id="ch_contenido">


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
                    <label for="nombre_tarea">Nombre de la Tarea</label>
                    <input value="" type="text" class="form-control" id="sdt-new-task-name" placeholder="Nombre de la tarea"/>
                    <label for="nombre_tarea">Descripcion</label>
                    <textarea class="form-control" id="sdt-new-task-description"></textarea>
                    <label for="fecha_tarea">Fecha de terminación o entrega</label>
                    <input class="form-control datepicker" id="sdt-new-task-fecha" value="{{ hoy }}"></input>
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
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="sdt-repeat-op1" class="col-sm-3 control-label">Se repite:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="sdt-repeat-op1" name="op1">
                                    <option value="1">Cada día</option>
                                    <option value="2">Todos los días laborales (de lunes a viernes)</option>
                                    <option value="3">Todos los lunes, miércoles y viernes</option>
                                    <option value="4">Todos los martes y jueves</option>
                                    <option value="5" selected>Cada semana</option>
                                    <option value="6">Cada mes</option>
                                    <option value="7">Cada año</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="sdt-visible-op2">
                            <label for="sdt-repeat-op2" class="col-sm-3 control-label">Repetir cada:</label>
                            <div class="col-sm-2">
                                <select class="form-control" id="sdt-repeat-op2" name="op2">
                                    {% for index in 1..30 %}
                                        <option value="{{ index }}">{{ index }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <p class="form-control-static" id="sdt-text-op2">días</p>
                            </div>
                        </div>

                        <div class="form-group" id="sdt-visible-op3">
                            <label for="sdt-repeat-op4" class="col-sm-3 control-label">Repetir el:</label>
                            <div class="col-sm-9">
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="op3-L" name="op3-L" value="L"> L
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="op3-M" name="op3-M" value="M"> M
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="op3-X" name="op3-X" value="X"> X
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="op3-J" name="op3-J" value="J" checked> J
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="op3-V" name="op3-V" value="V"> V
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="op3-S" name="op3-S" value="S"> S
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="op3-D" name="op3-D" value="D"> D
                                </label>
                            </div>
                        </div>
                        <div class="form-group" id="sdt-visible-op4" style="display: none">
                            <label for="sdt-repeat-op4" class="col-sm-3 control-label">Repetir cada:</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="op4" id="op4-1" value="1" checked> día del mes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="op4" id="op4-2" value="2"> día de la semana
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sdt-repeat-op4" class="col-sm-3 control-label">Empezar el:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control datepicker" id="op5" name="op5" placeholder="" value="{{ hoy }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sdt-repeat-op5" class="col-sm-3 control-label">Finaliza:</label>
                            <div class="col-sm-9">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="op6" id="op6-1" value="1" aria-label="111" checked>
                                        Nunca
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="op6" id="op6-2" value="2" aria-label="222">
                                        Después de
                                        <input type="number" min="1" id="op7" name="op7" placeholder="" disabled>
                                        repeticiones
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                        <input type="radio" name="op6" id="op6-3" value="3" aria-label="333">
                                        El
                                        <input type="text" id="op8" name="op8" placeholder="" class="datepicker" disabled>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sdt-repeat-op4" class="col-sm-3 control-label">Resumen : </label>
                            <div class="col-sm-9">
                                <p class="form-control-static" id="sdt-restul-repeat">Cada semana los jueves</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-default btn-sm btn-block" id="sdt-repeat-submit">Listo</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
{% endblock %}