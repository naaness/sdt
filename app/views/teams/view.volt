{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    <h1 class="text-center">Equipo de Trabajo : {{ name|escape_js|escape}}</h1><hr>

    <div class="list-group">
        <a href="{{ url() }}projects/index/{{ id }}" class="list-group-item">Ver Proyectos</a>
        <a href="{{ url() }}packages/index/{{ id }}" class="list-group-item">Ver Checklist</a>
    </div>

    <h3>Integrantes</h3>
    <table class="table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody>
        {% for item in page.items %}
            <tr>
                <td><a href="{{ url() }}users/view/{{ item.id }}">{{ item.username |escape_js|escape }}</a></td>
                <td>{% if item.status == 1 %}<span class="label label-success">Activo</span>{% else %}<span class="label label-danger">No activo</span>{% endif %}</td>
            </tr>
        {% endfor %}
        </tbody>
        <tbody>
        <tr>
            <td colspan="4">
                <div align="center">
                    {{ link_to("teams/view/"~ id , '<i class="glyphicon glyphicon-fast-backward"></i> Primera','class':'btn btn-default') }}
                    {{ link_to("teams/view/"~ id ~"?page="~ page.before, '<i class="glyphicon glyphicon-backward"></i> Anterior','class':'btn btn-default') }}
                    {{ link_to("teams/view/"~ id ~"?page="~ page.next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>','class':'btn btn-default') }}
                    {{ link_to("teams/view/"~ id ~"?page="~ page.last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>','class':'btn btn-default') }}
                    Página {{ page.current  }} de {{ page.total_pages  }}
                </div>
            </td>
        <tr>
        </tbody>
    </table>
{% endblock %}