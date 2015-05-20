{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h1 class="text-center" id="sdt-rm-date-string"></h1>
    <input type="hidden" id="ch-rm-token" value="{{ code_token }}">
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
                            <strong>RM</strong> {{ name_user }}
                        </div>
                    </div><!-- /input-group -->

                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" id="rm_left_day">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                </button>
                            </span>
                            <input type="text" class="form-control datepicker" id="rm_searchdate" name="vigencia" placeholder="Buscar Fecha" value="{{ fechahoy }}">
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
                                    <li id="rm-bt-goTags"><a href="{{ url() }}rmlabels"> <span class="glyphicon glyphicon-tags"></span> Etiquetas</a></li>
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
    {{ security_j }}
    <input type="hidden" id="fecha_hoy" value="{{ fechahoy }}">

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
                        <input type="text" class="form-control" name="asunto" id="asunto" placeholder="Titulo del correo" value="{{ subject_email }}" />
                    </div>
                    <div class="alert alert-info" role="alert">
                        <strong>Aviso!</strong> Podra usar las siguientes palabras en el asunto:
                        <br/>
                        USERNAME =  {{ username|upper }}<br/>
                        TIME     =  {{ fechahoy }} <br/>
                        DATE     =  {{ fechahoySmart }} <br/>
                        <a href="{{ url() }}profile">Click aqui para editar el asunto por defecto</a>
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
                        <input type="text" class="form-control datepicker" id="rm_date_to_htd" placeholder="Buscar Fecha" value="{{ fechahoy }}"/>
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
{% endblock %}