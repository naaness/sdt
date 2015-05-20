{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <div class="row">
        <br/>
        <br/>
        <h1 class="text-center">Formulario de Login</h1><hr>
        {{ form('login/index', 'method' : 'post', 'class': 'smart-form', 'role': 'form', 'id':'form-normal') }}
        {{ flash.output() }}
        <div class="form-group">
            {{ form.label('username_email') }}
            {{ form.render('username_email') }}
        </div>
        <div class="form-group">
            {{ form.label('password') }}
            {{ form.render('password') }}
        </div>
        <div class="form-group">
            {{ form.render('remember') }}
            {{ form.label('remember') }}
        </div>
        <div class="form-group">
            <div class="col-md-12"></div>
            {{ form.render('submit') }}
        </div>
        {{ form.render('csrf', ['value': security.getToken()] ) }}
        {{ form.message('csrf') }}
        </form>
    </div>

{% endblock %}