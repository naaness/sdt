{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h2 class="text-center">Plantilla basica para la Planeación diaria</h2>
    <table class="table table-hover">
        <tbody>
            {% for dp in dps %}
                {% if dp.header %}
                    <tr class="active">
                        <td><strong>{{ dp.order_r }}.</strong></td>
                        <td><strong>{{ dp.message }}.</strong> <small>{{ dp.submessage }}</small>.</td>
                        <td>
                            <a href="{{ url() }}dailyplanning/edit/{{ dp.id }}"><button class="btn btn-default">Editar</button></a>
                            &nbsp;
                            <a href="{{ url() }}dailyplanning/addSub/{{ dp.id }}"><button class="btn btn-default">Agregar Subpaso</button></a>
                            &nbsp;
                            <a href="{{ url() }}dailyplanning/delete/{{ dp.id }}"><button class="btn btn-default">Eliminar</button></a>
                        </td>
                    </tr>
                {% else %}
                    <tr>
                        <td> -</td>
                        <td><em>{{ dp.message }}.<small>{{ dp.submessage }}</small>.</em></td>
                        <td>
                            <a href="{{ url() }}dailyplanning/editSub/{{ dp.id }}"><button class="btn btn-default">Editar</button></a>
                            &nbsp;
                            <a href="{{ url() }}dailyplanning/delete/{{ dp.id }}"><button class="btn btn-default">Eliminar</button></a>
                        </td>
                    </tr>
                {% endif %}
            {% endfor %}
        </tbody>
    </table>
    <a href="{{ url() }}dailyPlanning/add"><button class="form-control btn btn-default">Agregar Paso del dia</button></a>
{% endblock %}