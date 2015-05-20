{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    {% set security_token = security.getToken() %}
    <div class="row">
        <br/>
        <br/>
        <h1 class="text-center">Editar Perfil</h1><hr>
        {{ flash.output() }}
        <h3>Datos de acceso</h3>
        {{ form('profile/index', 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
        <div class="form-group">
            <div class="col-md-6">
                {{ forms.get('formedit').label('username') }}
                {{ forms.get('formedit').render('username') }}
            </div>
            <div class="col-md-6">
                {{ forms.get('formedit').label('email') }}
                {{ forms.get('formedit').render('email') }}
            </div>
        </div>
        <br/>&nbsp;
        <div class="form-group">
            <div class="col-md-12">
                {{ forms.get('formedit').render('submit') }}
            </div>
        </div>

        {{ forms.get('formedit').render('csrf', ['value': security_token]) }}
        {{ forms.get('formedit').message('csrf') }}
        {{ forms.get('formedit').render('typeForm')}}
        </form>
        <br/>&nbsp;
        <h3>Informacion general</h3>
        {{ form('profile/index', 'method' : 'post', 'class': 'smart-form', 'role': 'form') }}
        <div class="form-group">
            <div class="col-md-4">
                {{ forms.get('profile').label('name') }}
                {{ forms.get('profile').render('name') }}
            </div>
            <div class="col-md-4">
                {{ forms.get('profile').label('last_name') }}
                {{ forms.get('profile').render('last_name') }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                {{ forms.get('profile').label('position') }}
                {{ forms.get('profile').render('position') }}
            </div>
            <div class="col-md-4">
                {{ forms.get('profile').label('company') }}
                {{ forms.get('profile').render('company') }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                {{ forms.get('profile').label('phone') }}
                {{ forms.get('profile').render('phone') }}
            </div>
            <div class="col-md-4">
                {{ forms.get('profile').label('mobile_phone') }}
                {{ forms.get('profile').render('mobile_phone') }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                {{ forms.get('profile').label('description') }}
                {{ forms.get('profile').render('description') }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                {{ forms.get('profile').label('about_bio') }}
                {{ forms.get('profile').render('about_bio') }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                {{ forms.get('profile').label('about_job') }}
                {{ forms.get('profile').render('about_job') }}
            </div>
        </div>
        <br/>&nbsp;
        <div class="form-group">
            <div class="col-md-12">
                {{ forms.get('profile').label('subject_email') }}
                <div class="alert alert-info" role="alert">
                    <strong>Aviso!</strong> Podra usar las siguientes palabras en el asunto:
                    <br/>
                    USERNAME =  {{ username|upper }}<br/>
                    TIME     =  {{ time }} <br/>
                    DATE     =  {{ fechahoySmart }} <br/>
                </div>
                {{ forms.get('profile').render('subject_email') }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                {{ forms.get('profile').label('url_photo') }}
                {{ forms.get('profile').render('url_photo') }}
            </div>

        </div>

        <div class="form-group">
            <div class="col-md-6">
                {{ forms.get('profile').label('navbar_color') }}
                {{ forms.get('profile').render('navbar_color') }}
            </div>
            <div class="col-md-6">
                {{ forms.get('profile').label('body_color') }}
                {{ forms.get('profile').render('body_color') }}
            </div>
        </div>
        <div id="dummy1"></div>
        <div id="dummy2"></div>
        <br/>&nbsp;
        <br/>&nbsp;
        <br/>&nbsp;
        <br/>&nbsp;
        <br/>&nbsp;
        <br/>&nbsp;
        <div class="form-group">
            <div class="col-md-12">
                {{ forms.get('profile').render('submit') }}
            </div>
        </div>
        {{ forms.get('profile').render('csrf', ['value': security_token ]) }}
        {{ forms.get('profile').message('csrf') }}
        {{ forms.get('profile').render('typeForm')}}
        </form>
        <br/>&nbsp;
        <br/>&nbsp;
    </div>
{% endblock %}