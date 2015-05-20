{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <div class="row">
        <h1 class="text-center">Formulario de Registro</h1><hr>
        {{ form('users/add', 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
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
            {{ form.label('password') }}
            {{ form.render('password') }}
        </div>
        <div class="form-group">
            {{ form.label('confirmPassword') }}
            {{ form.render('confirmPassword') }}
        </div>
        <div class="form-group">
            {{ form.label('role') }}
            {{ form.render('role') }}
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