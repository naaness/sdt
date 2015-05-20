{% extends "templates/blank.volt" %}
{% block content %}
    <input type="hidden" id="ch-start-day" value="{{ fecha[0] }}">
    <input type="hidden" id="ch-start-month" value="{{ fecha[1] }}">
    <input type="hidden" id="ch-start-year" value="{{ fecha[2] }}">
    <input type="hidden" id="ch-ntoday" value="{{ ntoday }}">
    <input type="hidden" id="ch-nweek" value="{{ nweek }}">
    <input type="hidden" id="ch-today" value="{{ today }}">
    <input type="hidden" id="ch-back-range" value="{{ results['header']['start'] }}">
    <input type="hidden" id="ch-next-range" value="{{ results['header']['end'] }}">
    <div id="main_container" style="padding:0; margin:0;" class="text-center">
        <!-- second drag region -->
        <div id="drag" style="height:400px">
            <table id="tb" style="width:100%" >
                <colgroup>
                    <col width="30%"/>
                    <col width="15%"/>
                    <col width="74%"/>
                    <col width="1%"/>
                </colgroup>
                <tbody>
                <tr class="rl">
                    <td class="only cdark">
                        <button type="button" class="btn btn-default" id="ch_btn_hoy" >
                            Hoy
                        </button>
                        {{ results['header']['title'] }}
                    </td>
                    <td class="only cdark">
                        <div class="input-group">
                            <a class="input-group-addon dummy" id="ch_btn_left">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                            </a>
                            <select id="rangoVista" style="display: inline-block;" class="form-control">
                                <option value='week' {% if range=='week' %}selected{% endif %}>semana</option>
                                <option value="month" {% if range=='month' %}selected{% endif %}>mes</option>
                                <option value="trimestre" {% if range=='trimestre' %}selected{% endif %}>trimestre</option>
                            </select>
                            <a class="input-group-addon dummy" id="ch_btn_right">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                            </a>
                        </div>
                    </td>
                    <td class="only cdark">
                        {% if results['editable'] %}
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" id="ch-mode-1" >
                                <input type="radio" name="options" autocomplete="off" checked>Normal
                            </label>
                            <label class="btn btn-default" id="ch-mode-2" >
                                <input type="radio" name="options" autocomplete="off">Agregar
                            </label>
                            <label class="btn btn-default" id="ch-mode-3" >
                                <input type="radio" name="options" autocomplete="off">Eliminar
                            </label>
                            <label class="btn btn-default" id="ch-mode-4" >
                                <input type="radio" name="options" autocomplete="off">Mover
                            </label>
                        </div>
                        {% endif %}
                    </td>
                    <td class="only cdark">

                    </td>

                </tr>
                </tbody>
            </table>
            <div id="tabla" style="height:350px">
                <div style="width: {{ width }}px;-moz-column-width: {{ width }}px">
                    <table id="tb0" class="table table-condensed" width="<?php echo $width; ?>px">
                        <colgroup>
                        </colgroup>
                        <tbody>
                        <tr class="rl">
                            {% for j in 1..(results['header']['diff_days'] +3) %}
                                {% if j==1 %}
                                <td class="only rowhandler fijar1"><div class="row_ch"></div>

                                </td>
                                {% elseif j==2 %}
                                <td class="only cligth fijar2" style="background-color:#FFFFFF">
                                    <div>
                                        <button type="button" class="btn btn-default" id="btn_add_task" style="width:20px;height:20px;" data-toggle="tooltip" data-placement="bottom" title="Nueva Tarea" >
                                            <span class="glyphicon glyphicon-plus" style="font-size: 9pt;top: -3px;left: -5px;" data-toggle="modal" data-target="#myModal"></span>
                                        </button>
                                        <button type="button" class="btn btn-default" id="btn_see_users" style="width:20px;height:20px;" data-toggle="tooltip" data-placement="bottom" title="Ver otros usuarios" >
                                            <span class="glyphicon glyphicon-user" style="font-size: 9pt;top: -3px;left: -5px;" data-toggle="modal" data-target="#myModal2"></span>
                                        </button>
                                        Tareas
                                    </div>
                                </td>
                                {% elseif j==3 %}
                                <td class="only cligth2 fijar3" style="background-color:#FFFFFF">%</td>
                                {% else %}
                                <td class="only cdark cdark_top" {% if (ntoday+4)!=j %} style="background-color:#FFFFFF"{% endif %}>

                                </td>
                                {% endif %}
                            {% endfor %}
                        </tr>
                        <tr class="rl" style="font-size: 10pt">
                            {% for j in 1..(results['header']['diff_days'] +3) %}
                                {% if j==1 %}
                                <td class="only rowhandler fijar1"><div  class="row"></div>
                                    <select id="p_sigla" style="width: 20px;height: 10px;" data-toggle="tooltip" data-placement="bottom" title="Siglas">
                                        <option value="0">Todo</option>
                                    </select>
                                </td>
                                {% elseif j==2 %}
                                <td class="only cligth fijar2" style="background-color:#FFFFFF">
                                    <div class="input-group input-group-sm">
                                        <select id="p_respon" style="width: 20px;height: 10px;display:none" data-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Filtrar por &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Responsable">
                                            <option value="0">Todo</option>
                                        </select>
                                        <select id="p_estado" style="width: 20px;height: 10px;display:none" data-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Filtrar por &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado de la tarea">
                                            <option value="0">Todo</option>
                                        </select>
                                        <select id="p_direct" style="width: 20px;height: 10px;display:none" data-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Filtrar por &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tareas dirigidas">
                                            <option value="0">Todo</option>
                                            <option value="0">Director</option>
                                        </select>
                                        <input type="text" id="p_textTa" placeholder="Titulo de la tarea" value="" data-placement="bottom" title="Filtrar por Titulo de &nbsp;las Tareas">
                                    </div>
                                </td>
                                {% elseif j==3 %}
                                <td class="only cligth2 fijar3" style="background-color:#FFFFFF;"></td>
                                {% else %}
                                <td class="only cdark cdark_bottom {% if (ntoday+4)==j %}today{% endif %}"  {% if (ntoday+4)!=j %} style="background-color:#FFFFFF"{% endif %}>
                                </td>
                                {% endif %}
                            {% endfor %}
                        </tr>
                        </tbody>
                    </table>
                    <table id="tbl" class="table table-condensed" width="<?php echo $width; ?>px">
                        <colgroup>
                        </colgroup>
                        <tbody>
                        {% set i = 0 %}
                        {% for data in results['maindata'] %}
                            {% set i=i+1 %}
                            <tr class="rl" id="tr_{{ data['info']['id'] }}">
                                {% for j in 1..(results['header']['diff_days'] +3) %}
                                    {% if j==1 %}
                                    <td class="only rowhandler fijar1">
                                        <div></div>
                                        <div class="row_ch {% if data['info']['project_id']!=0 %}redondo{% endif %}">{{ data['code'] }}</div></td>
                                    {% elseif j==2 %}
                                    <td class="only cligth fijar2 ch-name-task" {{ data['style'] }} aaaa>
                                        {{ data['info']['name'] }}
                                        <span class="glyphicon glyphicon-info-sign task-info" id="info_{{ data['info']['id'] }}"></span>
                                        <span class="glyphicon glyphicon-comment task-chat" id="chat_{{ data['info']['id'] }}"></span>
                                        {% if data['are_you_leader' ] is defined %}

                                            {% if data['are_you_leader'] %}
                                                <span class="glyphicon glyphicon-ok-circle {{ data['class_state'] }}"></span>
                                            {% elseif data['info']['status']!=1 %}
                                                <span class="glyphicon glyphicon-warning-sign {{ data['class_state'] }}" id="re_{{ data['info']['id'] }}" data-toggle="tooltip" data-placement="right" title="Click Aqui"></span>
                                            {% endif %}
                                        {% else %}
                                            <span class="glyphicon glyphicon-asterisk {{ data['class_state'] }}" ></span>
                                        {% endif %}
                                    </td>
                                    {% elseif j==3 %}
                                    <td class="only cligth2 fijar3" style="background-color:#FFFFFF;">
                                        <div class="text-center percent">
                                            {{ data['info']['percent'] }}
                                        </div>
                                    </td>
                                    {% else %}
                                        {% set new_date = date('d/m/Y', _mktime + 60*60*24*(j-4)) %}
                                        <td class="only cdark {{ data['class_editable'] }} filter-day {% if (ntoday+4)==j %}today{% endif %}"  {% if (ntoday+4)!=j %} style="background-color:#FFFFFF"{% endif %} id="{{ data['info']['id'] }}_{{ i }}_{{ j }}">

                                        {% if data[new_date] is defined %}
                                            <div class="drag orange {% if data['info']['status']==1 %}popover-unit-time{% endif %} dia {{ data[new_date]['class-color'] }}" id="{{ data[new_date]['id'] }}">
                                                <div class="text-center">
                                                    <span class="{{ data[new_date]['class-icon'] }} circle"></span>
                                                </div>
                                            </div>
                                        {% endif %}
                                        </td>
                                    {% endif %}
                                {% endfor %}
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{% endblock %}

