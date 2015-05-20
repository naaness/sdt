{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h2>Informacion de la Tarea</h2>

    <div class="row" id="task-info-content">
        <div class="col-lg-12">
            <div class="col-lg-3">
                <label>Nombre</label>
            </div>
            <div class="col-lg-9">
                <p>{{ task.name }}</p>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="col-lg-3">
                <label>Descripcion</label>
            </div>
            <div class="col-lg-9">
                <p>{{ task.description }}</p>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="col-lg-3">
                <label>Prioridad</label>
            </div>
            <div class="col-lg-9">
                <p>{{ task.priorities.name }}</p>
            </div>
        </div>
        {% if task.user_id > 0 %}
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Responsable</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.users.username  }}</p>
                </div>
            </div>
        {% endif %}
        <div class="col-lg-12">
            <div class="col-lg-3">
                <label>Estado</label>
            </div>
            <div class="col-lg-9">
                {% if task.status == 1 %}
                    <span class="label label-success">Aceptado</span>
                {% elseif task.status == 2 %}
                    <span class="label label-primary">En espera</span>
                {% elseif task.status == 3 %}
                    <span class="label label-danger">Rechazado</span>
                {% endif %}
            </div>
        </div>
        {% if task.status == 3 %}
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Motivo del rechazo</label>
                </div>
                <div class="col-lg-9">
                    {% for comment in task.comments %}
                        <p><strong>{{ comment.users.username }}</strong>: {{ comment.comment }}</p>
                    {% endfor %}
                </div>
            </div>
            {% if reactive %}
                <div class="col-lg-12">
                    <div class="col-lg-3">
                        <label>Reactivar Tarea</label>
                    </div>
                    <div class="col-lg-9">
                        <a href="{{ url() }}tasks/reactiveTask/{{ task.id }}">Click aqui para REACTIVAR la tarea</a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-3">
                        <label>Eliminar Tarea</label>
                    </div>
                    <div class="col-lg-9">
                        <a href="{{ url() }}tasks/deleteTask/{{ task.id }}">Click aqui para ELIMINAR la tarea</a>
                    </div>
                </div>
            {% endif %}
        {% endif %}
    </div>
    {% if task.project_id > 0 %}
        <h2>Informacion del proyecto</h2>
        <div class="row" id="task-info-content">
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Nombre</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.projects.name }}</p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Descripcion</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.projects.description }}</p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Lider del proyecto</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.projects.users.username }}</p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Integrantes</label>
                </div>
                <div class="col-lg-9">
                    {% for userProject in task.projects.usersProjects %}
                        {{ userProject.users.username }},
                    {% endfor %}
                </div>
            </div>
        </div>

        <h2>Informacion del euipo de trabajo</h2>
        <div class="row" id="task-info-content">
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Nombre</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.projects.teams.name }}</p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Descripcion</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.projects.teams.description }}</p>
                </div>
            </div>
        </div>
    {% endif %}
    {% if task.package_id > 0 %}
        <h2>Informacion del checklist</h2>
        <div class="row" id="task-info-content">
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Nombre</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.packages.name }}</p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Descripcion</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.packages.description }}</p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Lider del proyecto</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.packages.users.username }}</p>
                </div>
            </div>
        </div>
        <h2>Informacion del euipo de trabajo</h2>
        <div class="row" id="task-info-content">
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Nombre</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.packages.teams.name }}</p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <label>Descripcion</label>
                </div>
                <div class="col-lg-9">
                    <p>{{ task.packages.teams.description }}</p>
                </div>
            </div>
        </div>
    {% endif %}
{% endblock %}