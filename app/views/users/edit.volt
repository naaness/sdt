{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <div class="row">
        <br/>
        <br/>
        <h1 class="text-center">Editar Perfil</h1><hr>
        {{ form('users/edit/'~id, 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
        {{ flash.output() }}
        <div class="form-group">
            {{ form.label('username') }}
            {{ form.render('username') }}
        </div>
        <div class="form-group">
            {{ form.label('email') }}
            {{ form.render('email') }}
        </div>
        <div class="form-group">
            {{ form.label('role') }}
            {{ form.render('role') }}
        </div>
        <div class="form-group">
            {{ form.render('status') }}
            {{ form.label('status') }}
        </div>
        <div class="form-group">
            <div class="col-md-12"></div>
            {{ form.render('submit') }}
        </div>
        {{ form.render('csrf', ['value': security.getToken()]) }}
        {{ form.message('csrf') }}
        </form>
    </div>
{% endblock %}