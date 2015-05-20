{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h2>Lista de Equipos de Trabajo</h2>
    <table class="table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        {% for item in page.items %}
            <tr>
                <td><a href="{{ url() }}teams/view/{{ item.id }}">{{ item.name }}</a></td>
                <td>{% if item.status == 1 %}<span class="label label-success">Activo</span>{% else %}<span class="label label-danger">No activo</span>{% endif %}</td>
                <td>
                    {% if item.leader_id==user_id %}
                            <a href="{{ url() }}teams/edit/{{ item.id }}"><button class="btn btn-default">Editar</button></a>
                            &nbsp;
                            <a href="{{ url() }}teams/delete/{{ item.id }}"><button class="btn btn-default">Eliminar</button></a>
                        </td>
                    {% else %}
                        <span class="label label-default">Sin acciones</span>
                    {% endif %}
                </td>
            </tr>
        {% endfor %}
        </tbody>
        <tbody>
        <tr>
            <td colspan="4">
                <div align="center">
                    {{ link_to("teams/", '<i class="glyphicon glyphicon-fast-backward"></i> Primera','class':'btn btn-default') }}
                    {{ link_to("teams/?page="~ page.before, '<i class="glyphicon glyphicon-backward"></i> Anterior','class':'btn btn-default') }}
                    Página {{ page.current  }} de {{ page.total_pages  }}
                    {{ link_to("teams/?page="~ page.next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>','class':'btn btn-default') }}
                    {{ link_to("teams/?page="~ page.last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>','class':'btn btn-default') }}
                </div>
            </td>
        <tr>
        </tbody>
    </table>
    <a href="{{ url() }}teams/add"><button class="form-control btn btn-default">Crear un Equipo de trabajo</button></a>
{% endblock %}