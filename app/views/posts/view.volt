{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <div class="posts view">
        <legend>{{ post.name |escape|escape_js }}<span style="font-size:12px" >  Creado por {{ post.users.username }}</span></legend>

        <div>{{ post.post|escape_js }}</div>
    </div>

    <legend></legend>
    <div>
        <div class="pull-right">
            <a href="{{ url() }}posts/dislike/{{ post.id }}" class="btn btn-danger btn-xs" >
                <span class="glyphicon glyphicon-thumbs-down"></span>
                {% if post.count_dislikes>0 %}
                    {{ post.count_dislikes }}
                {% endif %}
            </a>
            <a href="{{ url() }}posts/like/{{ post.id }}" class="btn btn-success btn-xs">
                <span class="glyphicon glyphicon-thumbs-up"></span>
                {% if post.count_likes>0 %}
                    {{ post.count_likes }}
                {% endif %}
            </a>
            <button type="button" class="btn btn-default btn-xs new-post" id="idp_{{ post.id }}_{{ post.id }}">Responder</button>
        </div>
    </div>
    <br>

    <ul class="list-group">
        <li class="list-group-item">
            {% set nume_ini = 0 %}
            {% set primer_foto = 0 %}
            {% for dato in posts %}
                {% set num = explode('.', dato.order_r) %}
                {% set num = count(num) %}
                {% set primer_foto = 0 %}
                {% if num > 1 %}
                    {% set primer_foto = 1 %}
                {% endif %}
                {% if (nume_ini-num) >= 0 %}
                    {% for i in 0..(nume_ini-num) %}
                        </div>
                    </li>
                </ul>
                    {% endfor %}
                {% endif %}
                <ul class="media-list">
                    <li class="media" style="width: 100%;">
                        <div class="media-left">
                            <div style="width:{{ primer_foto*50 }}px"></div>
                        </div>
                        <div class="media-body" style="width: 100%;">
                            <div class="panel panel-default" >
                                {% set fec = explode(' ', dato.created) %}
                                {% set fec = explode('-', fec[0]) %}
                                <div class="panel-heading">{{ fec[2]}} de {{ months[fec[1]] }} del {{ fec[0]}}, {{ dato.users.username }}: </div>
                                <div class="panel-body">
                                    <div>{{ str_replace("||mas||","+",str_replace("||igu||","=",str_replace("||ans||", "&", dato.post))) |escape_js }}</div>
                                    <div class="pull-right">
                                        <a href="{{ url() }}posts/dislike/{{ dato.id }}" class="btn btn-danger btn-xs" >
                                            <span class="glyphicon glyphicon-thumbs-down"></span>
                                            {% if dato.count_dislikes>0 %}
                                                {{ dato.count_dislikes }}
                                            {% endif %}
                                        </a>
                                        <a href="{{ url() }}posts/like/{{ dato.id }}" class="btn btn-success btn-xs">
                                            <span class="glyphicon glyphicon-thumbs-up"></span>
                                            {% if dato.count_likes>0 %}
                                                {{ dato.count_likes }}
                                            {% endif %}
                                        </a>
                                        <button type="button" class="btn btn-default btn-xs new-post" id="idp_{{ dato.id }}_{{ post.id }}">Responder</button>
                                    </div>
                                </div>
                            </div>
                {% set nume_ini = num  %}
            {% endfor %}
            {% if nume_ini > 0 %}
                        </div>
                    </li>
                </ul>
            {% endif %}
        </li>
    </ul>


    <!-- Modal -->
    <div class="modal fade" id="new-post-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Responder Post</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="post-id" value="" />
                    <input type="hidden" id="post-post-id" value="" />
                    <label for="nombre_tarea">Mensaje</label>
                    <textarea class="ckeditor" id="post-post"></textarea>
                    </br>
                    <div>
                        <button class="btn btn-primary" id="post-submit" style="width:100%" >Crear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}