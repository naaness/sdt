{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <h1 class="text-center">Editar Etiqueta</h1><hr>

    {{ form('rmlabels/edit/', 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
    {{ flash.output() }}
    <div class="form-group">
        {{ form.render('id') }}
    </div>
    <div class="form-group">
        {{ form.label('name') }}
        {{ form.render('name') }}
    </div>
    <div class="form-group">
        {{ form.render('color') }}
    </div>
    <div class="form-group">
        {{ form.render('b_color') }}
    </div>
    <div class="form-group">
        {{ form.render('b_color_checked') }}
    </div>
    <div class="form-group">
        <label for="example">Colores de la Etiqueta</label><br>
        <label id="example1" style="color: {{ label.color }}; background-color:{{ label.b_color }}">123. abc. DEF. GHI</label>&nbsp;
        <label id="example2" style="color: {{ label.color }}; background-color:{{ label.b_color_checked }}">123. abc. DEF. GHI</label><br/>
        <div id="content-colors"></div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-4 text-center">
            </div>
            <div class="col-md-2 text-center">
                <div id="colorwheel_1"></div>
            </div>
            <div class="col-md-2 text-center">
                <div id="colorwheel_2"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ form.label('rm_font_id') }}
        {{ form.render('rm_font_id') }}
    </div>
    <div class="form-group">
        {{ form.label('rm_size_id') }}
        {{ form.render('rm_size_id') }}
    </div>
    <div class="form-group">
        <div class="col-md-12"></div>
        {{ form.render('submit') }}
    </div>
    {{ form.render('csrf', ['value': security.getToken()] ) }}
    {{ form.message('csrf') }}
    </form>

{% endblock %}