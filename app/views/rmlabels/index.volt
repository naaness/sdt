{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h2>Lista de Etiquetas</h2>
    <table class="table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Normal</th>
            <th>Seleccionado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        {% for item in page.items %}
            <tr>
                <td><a href="{{ url() }}rmlabels/edit/{{ item.id }}">{{ item.name }}</a></td>
                <td><div style="color: {{ item.color }};background-color: {{ item.b_color }};font-family: {{ item.rmFonts.name }}">Abcd..</div></td>
                <td><div style="color: {{ item.color }};background-color: {{ item.b_color_checked }}; font-family: {{ item.rmFonts.name }}">Abcd..</div></td>
                <td>
                    <a href="{{ url() }}rmlabels/edit/{{ item.id }}"><button class="btn btn-default">Editar</button></a>
                    &nbsp;
                    <a href="{{ url() }}rmlabels/delete/{{ item.id }}"><button class="btn btn-default">Eliminar</button></a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
        <tbody>
        <tr>
            <td colspan="4">
                <div align="center">
                    {{ link_to("rmlabels/", '<i class="glyphicon glyphicon-fast-backward"></i> Primera','class':'btn btn-default') }}
                    {{ link_to("rmlabels/?page="~ page.before, '<i class="glyphicon glyphicon-backward"></i> Anterior','class':'btn btn-default') }}
                    {{ link_to("rmlabels/?page="~ page.next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>','class':'btn btn-default') }}
                    {{ link_to("rmlabels/?page="~ page.last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>','class':'btn btn-default') }}
                    Página {{ page.current  }} de {{ page.total_pages  }}
                </div>
            </td>
        <tr>
        </tbody>
    </table>

    <a href="{{ url() }}rmlabels/add"><button class="form-control btn btn-default">Crear una Etiqueta</button></a>

{% endblock %}