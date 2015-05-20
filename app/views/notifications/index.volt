{% extends "templates/base.volt" %}

{% block title %} {{ title_view }}{% endblock %}

{% block content %}
    {{ super() }}
    {{ flash.output() }}
    {{ link_to("notifications/", '<i class="glyphicon glyphicon-fast-backward"></i> Primera','class':'btn btn-default') }}
    {{ link_to("notifications/?page="~ page.before, '<i class="glyphicon glyphicon-backward"></i> Anterior','class':'btn btn-default') }}
    Página {{ page.current  }} de {{ page.total_pages  }}
    {{ link_to("notifications/?page="~ page.next, 'Siguiente <i class="glyphicon glyphicon-forward"></i>','class':'btn btn-default') }}
    {{ link_to("notifications/?page="~ page.last, 'Ultimo <i class="glyphicon glyphicon-fast-forward"></i>','class':'btn btn-default') }}

    <div class="list-group">
        {{ html_not }}
    </div>

{% endblock %}