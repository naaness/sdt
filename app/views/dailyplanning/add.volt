{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <div class="row">

        <table class="table table-hover">
            <tbody>
                <tr class="active">
                    <td><strong id="dp-order-r"></strong></td>
                    <td>
                        <strong id="dp-message"></strong>
                        <small id="dp-submessage"></small>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <h1 class="text-center">Agregar Paso del dia</h1><hr>
        {{ form('dailyplanning/add', 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
        {{ flash.output() }}
        <div class="row">
            <div class="col-xs-1">
                {{ form.label('order_r') }}
                {{ form.render('order_r') }}
            </div>
            <div class="col-xs-6">
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