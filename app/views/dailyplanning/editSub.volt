{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <div class="row">

        <table class="table table-hover">
            <tbody>
            <tr>
                <td> -</td>
                <td>
                    <strong id="dp-message"></strong>
                    <small id="dp-submessage"></small>
                </td>
                <td></td>
            </tr>
            </tbody>
        </table>

        <h1 class="text-center">Editar Sub Paso del dia</h1><hr>
        {{ form('dailyplanning/editSub/'~id, 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
        {{ flash.output() }}
        <div class="row">
            <div class="col-xs-7">
                {{ form.label('message') }}
                {{ form.render('message') }}
            </div>
            <div class="col-xs-5">
                {{ form.label('submessage') }}
                {{ form.render('submessage') }}
            </div>
        </div>
        <br/>
        <div class="form-group">
            <div class="col-md-12"></div>
            {{ form.render('submit') }}
        </div>
        {{ form.render('csrf', ['value': security.getToken()]) }}
        {{ form.message('csrf') }}
        </form>
    </div>
{% endblock %}