{% extends "templates/blank.volt" %}
{% block content %}
{% if name_rm is defined %}
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
    <h4 class="modal-title" id="myModalLabel">{{ name_rm }}</h4>
    <span>Desde esta caja modal no es posible modificar el registro</span>
    </div>
{% endif %}
<table id="sample" class="table table-hover" width="100%">
    <tbody>
    {% if cont>0 %}
        {% for registry in registries %}
            <tr id="tr_{{ registry.rmRegistries.id }}"  style="{% if (registry.rmRegistries.checked ==1) %}background-color:#f1f1f1{% endif %}">
                <td width="10px">
                    <div class="btn-group text-center" data-toggle="buttons">
                        {% set color_fondo = registry.rmLabels.b_color %}
                        {% set active = '' %}
                        {% if (registry.rmRegistries.checked ==1) %}
                            {% set color_fondo=registry.rmLabels.b_color_checked %}
                            {% set active = 'active' %}
                        {% endif %}
                        <label class="btn btn-default {{ active }} chek">
                        </label>
                    </div>
                </td>
                <td>
                    <div style="float:left;">
                        <div class="numerar" style="margin-left:12px">{{ registry.rmRegistries.numbering }}</div>
                    </div>
                    {% if (registry.rmRegistries.rm_label_id ==0) %}
                        <div class="conpizarron" style="margin-left:32px;color:#000000;background-color:transparent;border-radius:3px" name="0">
                            <div class="pizarron" style="margin-left:5px;font-family:Klavika;font-size:14px;word-break: break-all;" contenteditable="true">{{ registry.rmRegistries.registry }}
                            </div>
                        </div>
                    {% else %}
                        <div class="conpizarron" style="margin-left:32px;color:{{ registry.rmLabels.color }};background-color:{{ color_fondo }};border-radius:3px" name="{{ registry.rmRegistries.rm_label_id }}">
                            <div class="pizarron" style="margin-left:5px;font-family:Klavika;font-size:14px;word-break: break-all;" contenteditable="true">{{ str_replace("||mas||","+",str_replace("||igu||","=",str_replace("||ans||", "&", registry.rmRegistries.registry))) }}
                            </div>
                        </div>
                    {% endif %}
                </td>
            </tr>
        {% endfor %}
    {% else %}
        <tr>
            <td width="10px">
                <div class="btn-group text-center" data-toggle="buttons">
                    <label class="btn btn-default chek">
                    </label>
                </div>
            </td>
            <td>
                <div style="float:left">
                    <div class="numerar" style="margin-left:12px">1</div>
                </div>
                <div class="conpizarron" style="margin-left:32px;border-radius:3px;color:#000000" name="0" >
                    <div class="pizarron" style="margin-left:5px;font-family:Klavika;font-size:14;font-weight:bold" contenteditable="true">
                    </div>
                </div>
            </td>
        </tr>
    {% endif %}
    </tbody>
</table>
<div class="btn-group" id="rm_tool" style="display: none;">
    <button type="button" class="btn btn-default" id="rm_uplevel" >
        <span class="glyphicon glyphicon-chevron-left" ></span>
    </button>
    <button type="button" class="btn btn-default" id="rm_downlevel" >
        <span class="glyphicon glyphicon-chevron-right" ></span>
    </button>

    <button type="button" class="btn btn-default" id="rm_new_parraf" >
        <span class="glyphicon glyphicon-text-height" ></span>
    </button>
    <button type="button" class="btn btn-default" id="rm_to_htd" >
        <span class="glyphicon glyphicon-share-alt" ></span>
    </button>
    <select id="Setiqueta" class="btn btn-default">
        <option value="0">Por defecto</option>
        {% for label in labels %}
            <option value="{{ label.id }}">{{ label.name }}</option>
        {% endfor %}
    </select>
    <button type="button" class="btn btn-default" id="rm_eliminar" >
        <span class="glyphicon glyphicon-trash" ></span>
    </button>
</div>
{% endblock %}