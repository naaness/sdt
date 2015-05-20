{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h2>Tareas</h2>
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
                <td><a href="{{ url() }}tasks/view/{{ team_id }}/{{ project_id }}/{{ package_id }}/{{ model }}/{{ item.tasks.id }}">{{ item.tasks.name }}</a></td>
                <td>{% if item.tasks.status == 1 %}<span class="label label-success">Aceptado</span>{% elseif item.tasks.status==2 %}<span class="label label-primary">En espera</span>{% elseif item.tasks.status==3 %}<span class="label label-danger">Rechazado</span>{% endif %}</td>

            </tr>
        {% endfor %}
        </tbody>
        <tbody>
        <tr>
            <td colspan="4">
                <div align="center">
                    {{ link_to("tasks/", '<i class="glyphicon glyphicon-fast-backward"></i> Primera','class':'btn btn-default') }}
                    {{ link_to("tasks/?page="~ page.before, '<i class="glyphicon glyphicon-backward"></i> Anterior','class':'btn btn-default') }}
                    {{ link_to("tasks/?page="~ page.next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>','class':'btn btn-default') }}
                    {{ link_to("tasks/?page="~ page.last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>','class':'btn btn-default') }}
                    Página {{ page.current  }} de {{ page.total_pages  }}
                </div>
            </td>
        <tr>
        </tbody>
    </table>
    {% if can_add %}
        <a href="{{ url() }}tasks/add/{{ team_id }}/{{ project_id }}/{{ package_id }}/{{ model }}"><button class="form-control btn btn-default">Crear Tarea</button></a>
    {% endif %}

    <h3>Checklist</h3>
    <div class="row">
        <div id="checklist" class="col-md-12" style="height : 400px;">
            <div id="ch_contenido">



            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="sdt-info-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Informacion</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sdt-info-task-id" value="" />
                    <div class="row" id="task-info-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}