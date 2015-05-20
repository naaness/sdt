{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    <h1 class="text-center">Perfil de usuario</h1><hr>
    {% set username = user.username %}
    {% set nulo = '...' %}
    {% set position = nulo %}
    {% set company = nulo %}
    {% set phone = nulo %}
    {% set mobile_phone = nulo %}
    {% set description = nulo %}
    {% set about_bio = nulo %}
    {% set about_job = nulo %}
    {% if user.profiles %}
        {% set username = user.profiles.name ~" "~user.profiles.last_name %}
        {% set position = user.profiles.position %}
        {% set company = user.profiles.company %}
        {% set phone = user.profiles.phone %}
        {% set mobile_phone = user.profiles.mobile_phone %}
        {% set description = user.profiles.description %}
        {% set about_bio = user.profiles.about_bio %}
        {% set about_job = user.profiles.about_job %}
    {% endif %}
    <h2>{{ username |escape_js|escape }}</h2>
    <div class="col-lg-12 col-sm-12 ">
        <div class="row profile">
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-xs-5 col-sm-12">
                        <h4 class="gray-light">Informacion General</h4>
                        <ul class="profile-details">
                            <div><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> puesto</div>
                            {{ position ? position : nulo |escape_js|escape}}
                            <div><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> empresa</div>
                            {{ company ? company : nulo |escape_js|escape}}
                        </ul>
                        <h4>Informacion de contacto</h4>
                        <ul class="profile-details">
                            <div><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> teléfono</div>
                            {{ phone ? phone : nulo |escape_js|escape}}
                            <div><span class="glyphicon glyphicon-phone" aria-hidden="true"></span> celular</div>
                            {{ mobile_phone ? mobile_phone : nulo |escape_js|escape}}
                            <div><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> correo</div>
                            {{ user.email }}
                        </ul>
                    </div>
                </div><!--/row-->
            </div><!--/col-->
            <div class="col-sm-9">
                <ul class="nav nav-tabs" id="myTabProfile">
                    <li class="active"><a href="#skills">Habilidades</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="skills">
                        <div class="row">
                            <div class="col-sm-12">
                                <h2>Acerca de mi</h2>
                                <p>{{ description ? description : nulo |escape_js|escape}}</p>
                                <h2>Mi trayectoria profesional</h2>
                                <p>{{ about_bio ? about_bio : nulo |escape_js|escape}}</p>
                                <h2>Acerca de mi trabajo</h2>
                                <p>{{ about_job ? about_job : nulo |escape_js|escape}}</p>
                            </div><!--/col-->
                           </div><!--/col-->
                        </div><!--/row-->
                    </div>
                    <div class="tab-pane" id="myprojects">
                        <div class="row">
                            <div class="col-sm-12">
                            </div><!--/col-->
                        </div><!--/col-->
                    </div><!--/row-->
                </div>
            </div><!--/col-->
        </div><!--/profile-->

    </div>
{% endblock %}