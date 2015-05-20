{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <h1 class="text-center">Editar Tarea</h1><hr>

    {{ form('tasks/edit/'~team_id~'/'~project_id~'/'~package_id~'/'~model~'/'~task_id, 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
    {{ flash.output() }}
    <div class="form-group">
        <div class="col-md-12">
            {{ form.render('id') }}
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            {{ form.label('name') }}
            {{ form.render('name') }}
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            {{ form.label('description') }}
            {{ form.render('description') }}
        </div>
    </div>
    {% if project_id %}
        <div class="form-group">
            <div class="col-md-12">
                {{ form.label('user_id') }}
                {{ form.render('user_id') }}
            </div>
        </div>
    {% endif %}
    <div class="form-group">
        <div class="col-md-12">
            {{ form.label('priority_id') }}
            {{ form.render('priority_id') }}
        </div>
    </div>
    <br>
    <div class="form-group">
        <div class="col-md-12">
            <br/>
            {{ form.render('submit') }}
        </div>
    </div>
    {{ form.render('csrf', ['value': security.getToken()] ) }}
    {{ form.message('csrf') }}
    </form>
{% endblock %}