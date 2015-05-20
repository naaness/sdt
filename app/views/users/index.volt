{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h2 class="text-center">Lista de Usuarios</h2>
    <div class="bootstrap_buttons">
        <button type="button" class="reset btn btn-primary" data-column="0" data-filter=""><i class="icon-white icon-refresh glyphicon glyphicon-refresh"></i> Reiniciar Filtros</button>
    </div>
    <div id="demo">
        <table class="tablesorter">
            <thead>
                <tr>
                    <th>Nombre Usuario</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Empresa</th>
                    <th class="filter-select filter-exact" data-placeholder="Estados" >Estado</th>
                    <th class="filter-select filter-exact" data-placeholder="Roles">Role</th>
                    <th class="filter-false">Acciones</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Nombre Usuario</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Empresa</th>
                    <th>Estado</th>
                    <th>Role</th>
                    <th>Acciones</th>
                </tr>
                <tr>
                    <th colspan="7" class="ts-pager form-horizontal">
                        <button type="button" class="btn first"><i class="icon-step-backward glyphicon glyphicon-step-backward"></i></button>
                        <button type="button" class="btn prev"><i class="icon-arrow-left glyphicon glyphicon-backward"></i></button>
                        <span class="pagedisplay"></span> <!-- this can be any element, including an input -->
                        <button type="button" class="btn next"><i class="icon-arrow-right glyphicon glyphicon-forward"></i></button>
                        <button type="button" class="btn last"><i class="icon-step-forward glyphicon glyphicon-step-forward"></i></button>
                        <select class="pagesize input-mini" title="Select page size">
                            <option selected="selected" value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                        </select>
                        <select class="pagenum input-mini" title="Select page number"></select>
                    </th>
                </tr>
                </tfoot>
            <tbody>
            {% for user in users %}
                <tr>
                    <td><a href="{{ url() }}users/view/{{ user.id }}">{{ user.username }}</a></td>
                    {% if user.profiles.id is defined %}
                        <td>{{user.profiles.name}}</td>
                        <td>{{user.profiles.last_name}}</td>
                        <td>{{user.profiles.company}}</td>
                    {% else %}
                        <td></td>
                        <td></td>
                        <td></td>
                    {% endif %}
                    <td class="text-center">{% if user.status == 1 %}<span class="label label-success">Activo</span>{% else %}<span class="label label-danger">No activo</span>{% endif %}</td>
                    <td>{{ roles[user.role] }}</td>
                    <td>
                        <a href="{{ url() }}users/edit/{{ user.id }}"><button class="btn btn-default">Editar</button></a>
                        &nbsp;
                        <a href="{{ url() }}users/changePasswordAdmin/{{ user.id }}"><button class="btn btn-default">Contraseña</button></a>
                        &nbsp;
                        <a href="{{ url() }}users/delete/{{ user.id }}"><button class="btn btn-default">Eliminar</button></a>
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>

    <a href="{{ url() }}users/add"><button class="form-control btn btn-default">Crear un Usuario</button></a>

{% endblock %}