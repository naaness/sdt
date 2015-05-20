{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <h1 class="text-center">Crear Checklist</h1><hr>

    {{ form('packages/add/'~team_id, 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
    {{ flash.output() }}
    <div class="form-group">
        <div class="col-md-10">
            {{ form.label('name') }}
            {{ form.render('name') }}
        </div>
        <div class="col-md-2">
            {{ form.label('code') }}
            {{ form.render('code') }}
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            {{ form.label('description') }}
            {{ form.render('description') }}
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <span>&nbsp;</span>
        </div>
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