{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <h1 class="text-center">Eliminar Paso</h1><hr>

    {{ form('dailyplanning/delete/'~id, 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
    {{ flash.output() }}
    <div class="form-group">
        <div class="col-md-12">
            {{ form.render('id') }}
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <br/>
            {{ form.render('submit') }}
        </div>
    </div>
    {{ form.render('csrf', ['value': security.getToken()] ) }}
    {{ form.message('csrf') }}
    </form>
    <br/>&nbsp;
    <div class="form-group">
        <div class="col-md-12">
            <a href="{{ url() }}dailyPlanning"><button class="form-control btn btn-success">No eliminar</button></a>
        </div>
    </div>
{% endblock %}