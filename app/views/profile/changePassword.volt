{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <div class="row">
        <br/>
        <br/>
        <h1 class="text-center">Cambiar Contraseña</h1><hr>
        {{ form('profile/changePassword', 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
        {{ flash.output() }}
        <div class="form-group">
            {{ form.label('newpassword') }}
            {{ form.render('newpassword') }}
        </div>
        <div class="form-group">
            {{ form.label('confirmPassword') }}
            {{ form.render('confirmPassword') }}
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