{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <h1 class="text-center">Editar Foro</h1><hr>

    {{ form('posts/edit/'~post_id, 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
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
            {{ form.label('post') }}
            {{ form.render('post') }}
        </div>
    </div>
    <div>&nbsp;</div>
    <br/>
    <div class="form-group">
        <div class="col-md-12">
            {{ form.render('submit') }}
        </div>
    </div>
    {{ form.render('csrf', ['value': security.getToken()] ) }}
    {{ form.message('csrf') }}
    </form>
{% endblock %}