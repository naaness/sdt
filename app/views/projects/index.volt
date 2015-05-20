{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h1 class="text-center">{{ team.name|escape_js|escape }}</h1><hr>
    <p>{{ team.description }}</p>
    <h2>Proyectos</h2>
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
                <td><a href="{{ url() }}tasks/index/{{ team_id }}/{{ item.id }}">{{ item.name|escape_js|escape }}</a></td>
                <td>{% if item.status == 1 %}<span class="label label-success">Activo</span>{% else %}<span class="label label-danger">Eliminado</span>{% endif %}</td>
                {% if item.user_id == user_id AND item.status %}
                    <td>
                        <a href="{{ url() }}projects/edit/{{ team_id }}/{{ item.id }}"><button class="btn btn-default">Editar</button></a>
                        &nbsp;
                        <a href="{{ url() }}projects/delete/{{ team_id }}/{{ item.id }}"><button class="btn btn-default">Eliminar</button></a>
                    </td>
                {% else %}
                    <td>
                        <span class="label label-default">Sin Acciones</span>
                    </td>
                {% endif %}
            </tr>
        {% endfor %}
        </tbody>
        <tbody>
        <tr>
            <td colspan="4">
                <div align="center">
                    {{ link_to("projects/index/"~ team_id, '<i class="glyphicon glyphicon-fast-backward"></i> Primera','class':'btn btn-default') }}
                    {{ link_to("projects/index/"~ team_id ~"?page="~ page.before, '<i class="glyphicon glyphicon-backward"></i> Anterior','class':'btn btn-default') }}
                    Página {{ page.current  }} de {{ page.total_pages  }}
                    {{ link_to("projects/index/"~ team_id ~"?page="~ page.next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>','class':'btn btn-default') }}
                    {{ link_to("projects/index/"~ team_id ~"?page="~ page.last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>','class':'btn btn-default') }}

                </div>
            </td>
        <tr>
        </tbody>
    </table>
    {% if canCreate %}
    <a href="{{ url() }}projects/add/{{ team_id }}"><button class="form-control btn btn-default">Crear Proyecto</button></a>
    {% endif %}
{% endblock %}