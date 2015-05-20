{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h1 class="text-center">{{ team.name|escape_js|escape }}</h1><hr>
    <p>{{ team.description }}</p>
    <h2>Checklists</h2>
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
                <td><a href="{{ url() }}tasks/index/{{ team_id }}/0/{{ item.id }}/0">{{ item.name |escape_js|escape}}</a></td>
                <td>{% if item.status == 1 %}<span class="label label-success">Activo</span>{% else %}<span class="label label-danger">No activo</span>{% endif %}</td>
                <td>
                    {% if user_package[item.id] is defined %}
                        {% if user_package[item.id] %}
                            <a href="{{ url() }}packages/noUsePackage/{{ team_id }}/{{ item.id }}"><button class="btn btn-default">Dejar de usar</button></a>
                        {% else %}
                            <a href="{{ url() }}packages/usePackage/{{ team_id }}/{{ item.id }}"><button class="btn btn-default">Usar</button></a>
                        {% endif %}
                    {% else %}
                        <a href="{{ url() }}packages/createUsePackage/{{ team_id }}/{{ item.id }}"><button class="btn btn-default">Usar</button></a>
                    {% endif %}
                    {% if item.user_id == user_id %}
                        <a href="{{ url() }}packages/edit/{{ team_id }}/{{ item.id }}"><button class="btn btn-default">Editar</button></a>
                        &nbsp;
                        <a href="{{ url() }}packages/delete/{{ team_id }}/{{ item.id }}"><button class="btn btn-default">Eliminar</button></a>
                    {% endif %}
                </td>

            </tr>
        {% endfor %}
        </tbody>
        <tbody>
        <tr>
            <td colspan="4">
                <div align="center">
                    {{ link_to("packages/index/"~ team_id, '<i class="glyphicon glyphicon-fast-backward"></i> Primera','class':'btn btn-default') }}
                    {{ link_to("packages/index/"~ team_id ~"?page="~ page.before, '<i class="glyphicon glyphicon-backward"></i> Anterior','class':'btn btn-default') }}
                    Página {{ page.current  }} de {{ page.total_pages  }}
                    {{ link_to("packages/index/"~ team_id ~"?page="~ page.next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>','class':'btn btn-default') }}
                    {{ link_to("packages/index/"~ team_id ~"?page="~ page.last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>','class':'btn btn-default') }}

                </div>
            </td>
        <tr>
        </tbody>
    </table>
    {% if canCreate %}
    <a href="{{ url() }}packages/add/{{ team_id }}"><button class="form-control btn btn-default">Crear Checklists</button></a>
    {% endif %}
{% endblock %}