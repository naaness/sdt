<table width="100%" style="width: 100%">
    <tbody>
    <tr>
        <td style="padding:40px 0  0 0;">
            <p style="color:#000;font-size: 16px;line-height:24px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:normal;">
            <p style="font-size: 13px;line-height:24px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;">
            <table cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                {% if html_alert is defined %}
                    {% if html_alert!="" %}
                        <tr>
                            <td height="55px" width="561px" style="height:55px;font-family:Arial;font-size:14px;font-weight:bold;color:#fff;background-color:#000000;padding-left:20px" colspan="3">Estas son las tareas pendientes</td>
                        </tr>
                        <tr style="font-family:Arial;font-size:14px;font-weight:normal;font-weight:normal;color:#151515">
                            <td height="40px" style="height:25px;border-right:2px solid #ffffff;padding-left:15px;border-bottom:2px solid #ffffff;background-color:#b1b1b1">
                                <strong>Descripcion</strong>
                            </td>
                            <td height="40px" style="width:70px;height:25px;border-right:2px solid #ffffff;border-bottom:2px solid #ffffff;background-color:#b1b1b1;text-align:center;">
                                <strong>Ejecutar</strong>
                            </td>
                            <td height="40px" style="width:70px;height:25px;border-right:2px solid #ffffff;border-bottom:2px solid #ffffff;background-color:#b1b1b1;text-align:center;">
                                <strong>Prioridad</strong>
                            </td>
                        </tr>
                        {{ html_alert }}
                    {% endif %}
                {% endif %}
                {% if html_alert is defined %}
                    {% if html_notification!="" %}
                        <tr>
                            <td height="55px" width="561px" style="height:55px;font-family:Arial;font-size:14px;font-weight:bold;color:#fff;background-color:#000000;padding-left:20px" colspan="3">Estas son tus notificaciones</td>
                        </tr>
                        {{ html_notification }}
                    {% endif %}
                {% endif %}
                <tr>
                    <td height="40px" width="561px" style="height:55px;font-family:Arial;font-size:14px;font-weight:bold;color:#fff;background-color:#000000;padding-left:20px;text-align:center;" colspan="3">
                        <a href="{{ url() }}">
                            <button type="button">Ir a SDT</button>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
            </p>
        </td>
    </tr>
    </tbody>
</table>