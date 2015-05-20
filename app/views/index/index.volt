{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {% if v_session %}
        {{ flash.output() }}
    {% endif %}
    {{ cosas }}
    <div class="jumbotron">
        <h1 id="hello,-world!">Hola{{ username|escape|escape_js }}!, Bienvenido a SDT<a class="anchorjs-link" href="#hello,-world!"><span class="anchorjs-icon"></span></a></h1>
        <p>Esta es una plataforma que le ayudara a gestionar mejor su tiempo y a obtener una mayor productividad.</p>
        {% if Not v_session %}
            <p><a class="btn btn-primary btn-lg" href="{{ url() }}login" role="button">Ingresa Aqui</a></p>
        {% endif %}
    </div>
    {% if v_session %}
        <h3>Usa las siguientes herramientas</h3>
        <div class="list-group">
            <a href="{{ url() }}rmregistries" class="list-group-item"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <strong>RM</strong> : Registro Maestro.</a>
            <a href="{{ url() }}checklist" class="list-group-item"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>  <strong>Checklist</strong> : Listado de tareas</a>
            <a href="{{ url() }}htd" class="list-group-item"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <strong>HTD</strong> : Hoja de Trabajo diario.</a>
        </div>
    {% endif %}
{% endblock %}