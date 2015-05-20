{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <h1 class="text-center">Crear Equipo de Trabajo</h1><hr>

    {{ form('teams/add/', 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
    {{ flash.output() }}
    <div class="form-group">
        {{ form.label('name') }}
        {{ form.render('name') }}
    </div>
    <div class="form-group">
        {{ form.label('description') }}
        {{ form.render('description') }}
    </div>
    <div class="form-group">
        {{ form.label('users_ids[]') }}
        {{ form.render('users_ids[]') }}
    </div>

    <div class="form-group">
        <div class="col-md-12">
            {{ form.render('submit') }}
        </div>

    </div>
    {{ form.render('csrf', ['value': security.getToken()] ) }}
    {{ form.message('csrf') }}
    </form>
{% endblock %}