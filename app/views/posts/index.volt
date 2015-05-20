{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h1 class="text-center">{{ name }}</h1>
    <table class="table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th>Título</th>
            <th>Estado</th>
            <th># Visitas</th>
            <th># Comentarios</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        {% for item in page.items %}
            <tr>
                <td><a href="{{ url() }}posts/view/{{ item.id }}">{{ item.name|escape|escape_js }}</a></td>
                <td>{% if item.status == 1 %}<span class="label label-success">Activo</span>{% else %}<span class="label label-danger">No activo</span>{% endif %}</td>
                <td>{{ item.count_views }}</td>
                <td>{{ item.count_answers }}</td>
                {% if item.user_id == user_id %}
                    <td>
                        <a href="{{ url() }}posts/edit/{{ item.id }}"><button class="btn btn-default">Editar</button></a>
                        &nbsp;
                        <a href="{{ url() }}posts/delete/{{ item.id }}"><button class="btn btn-default">Eliminar</button></a>
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
            <td colspan="5">
                <div align="center">
                    {{ link_to("Posts/index", '<i class="glyphicon glyphicon-fast-backward"></i> Primera','class':'btn btn-default') }}
                    {{ link_to("Posts/index/?page="~ page.before, '<i class="glyphicon glyphicon-backward"></i> Anterior','class':'btn btn-default') }}
                    {{ link_to("Posts/index/?page="~ page.next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>','class':'btn btn-default') }}
                    {{ link_to("Posts/index/?page="~ page.last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>','class':'btn btn-default') }}
                    Página {{ page.current  }} de {{ page.total_pages  }}
                </div>
            </td>
        <tr>
        </tbody>
    </table>

    <a href="{{ url() }}posts/add"><button class="form-control btn btn-default">Crear Foro</button></a>

{% endblock %}