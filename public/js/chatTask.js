/**
 * Created by nesto_000 on 7/04/15.
 */
var last_range      =   null;
var active_scroll   =   false;
var chat_task_id    =   0;
var chat_li_id      =   0;
var SyncSecrect     =   null;

$(document).ready(function() {
    startLineSecure();
    $("#btn-chat").click(function(){
        if(SyncSecrect){
            if($("#btn-input-chat").val().trim()!=''){
                showSpinner($("#panel-body"));
                $("#btn-chat").prop("disabled", true);
                var encryptedString = $.jCryption.encrypt('id='+$("#sdt-task-chat").val()+'&msg='+$("#btn-input-chat").val(), SyncSecrect);
                $.ajax({
                    url: server + '/tasks/setTaskChat',
                    dataType: "json",
                    type: "POST",
                    data: {
                        jCryption: encryptedString
                    },
                    success: function(datos) {
                        hideSpinner($("#panel-body"));
                        var user_id = datos.user_id;
                        var dateNow = datos.dateNow;
                        var name = datos.name;
                        var align = "left";
                        var obj = new Object();
                        obj.message = $("#btn-input-chat").val();
                        obj.date = dateNow;
                        $('#panel-body-chat').append(htmlLeftChat(obj,name,align,dateNow));
                        $("#btn-input-chat").val('');
                        $("#btn-chat").prop("disabled", false);
                        var con = $('#panel-body-chat').find('li').length;
                        $('#panel-body').animate({ scrollTop: con*100 }, 1000);
                    },
                    fail: function(){
                        hideSpinner($("#panel-body"));
                        $("#btn-chat").prop("disabled", false);
                        alert('Ha ocurrido un error en el envio, por favor revise el acceso a internet e intente de nuevo.');
                    }
                });
            }
        }
    });

    // get chat task
    setInterval(getNewsMessagesTask, 10000);
});

var getNewsMessagesTask = function(){
    $.post(server+'/tasks/getNewsMessagesTask', function(datos){
        for (var i = 0; i < datos.length; i++) {
            $("#chat_"+datos[i].task_id).addClass('chat-news');
            chat_task_id=0;
        }
    }, 'json');
}
var initTaskChat = function(){
    $(".task-chat").click(function(e){
        getchatTask($(this));
    })
    $('#panel-body').scroll(function(){
        if(SyncSecrect){
            if(active_scroll){
                if($("#panel-body").scrollTop()==0){
                    last_range+=1;
                    active_scroll=false;
                    showSpinner($("#panel-body"));
                    var encryptedString = $.jCryption.encrypt('id='+chat_task_id+'&range='+last_range, SyncSecrect);
                    $.ajax({
                        url: server + '/tasks/getTaskChatScroll',
                        dataType: "json",
                        type: "POST",
                        data: {
                            jCryption: encryptedString
                        },
                        success: function(datos) {
                            hideSpinner($("#panel-body"));
                            var messages = datos.messages;
                            if(messages.length>0){
                                var user_id = datos.user_id;
                                var dateNow = datos.dateNow;

                                var id_targe = chat_li_id;
                                for (var i = 0; i < messages.length; i++) {
                                    var name = messages[i].username;
                                    if(messages[i].name){
                                        name = messages[i].name + ' ' + messages[i].last_name ;
                                    }
                                    var align="right";
                                    if(user_id==messages[i].tasksMessages.user_id){
                                        align = "left";
                                    }
                                    var last_li_id = chat_li_id;
                                    $(htmlLeftChat(messages[i].tasksMessages,name,align,dateNow)).insertBefore("#line-chat-"+last_li_id);
                                }
                                $("#panel-body").scrollTop(100*6);
                            }
                            active_scroll=true;
                        },
                        fail: function(){
                            hideSpinner($("#panel-body"));
                        }
                    });
                }
            }
        }
    });
}
var getchatTask = function(obj){
    if(SyncSecrect){
        // Obtener el id
        var _id = obj.attr('id');
        _id = _id.split('_');
        _id = _id[1];
        if(_id>0){
            $("#sdt-task-chat").val(_id);
            showSpinner($("#ch_content"));
            if(chat_task_id!=_id){
                active_scroll=false;
                last_range=0;
                chat_li_id=0;
                var encryptedString = $.jCryption.encrypt('id='+_id, SyncSecrect);
                $.ajax({
                    url: server + '/tasks/getTaskChat',
                    dataType: "json",
                    type: "POST",
                    data: {
                        jCryption: encryptedString
                    },
                    success: function(response) {
                        var datos = $.jCryption.decrypt(response.encrypted, SyncSecrect);
                        datos = JSON.parse(datos);
                        chat_task_id=_id;
                        hideSpinner($("#ch_content"));
                        var user_id = datos.user_id;
                        var dateNow = datos.dateNow;
                        var messages = datos.messages;
                        $("#sdt-chat-title").text(datos.taskname);
                        $('#panel-body-chat').html('');
                        for (var i = 0; i < messages.length; i++) {
                            var name = messages[i].username;
                            if(messages[i].name){
                                name = messages[i].name + ' ' + messages[i].last_name ;
                            }
                            var align="right";
                            if(user_id==messages[i].tasksMessages.user_id){
                                align = "left";
                            }
                            if(chat_li_id==0){
                                $('#panel-body-chat').append(htmlLeftChat(messages[i].tasksMessages,name,align,dateNow));
                            }else{
                                var last_li_id = chat_li_id;
                                $(htmlLeftChat(messages[i].tasksMessages,name,align,dateNow)).insertBefore("#line-chat-"+last_li_id);
                            }
                        }
                        $("#chat_"+_id).removeClass('chat-news');
                        $('#sdt-chat-task').modal('show');
                        $('#panel-body').animate({ scrollTop: messages.length*100 }, 1000);
                        active_scroll=true;
                    },
                    fail: function(){
                        hideSpinner($("#panel-body"));
                        alert('Error de conexión, por favor revise el acceso a internet e intente de nuevo.');
                    }
                });
            }else{
                $('#sdt-chat-task').modal('show');
                hideSpinner($("#ch_content"));
            }
        }
    }
}

var htmlLeftChat = function( obj, name, align,dateNow){
    var dateC = moment(new Date(obj.date));
    var dateB = moment(new Date(dateNow));

    var hace = '';
    var dife = dateB.diff(dateC, 'year');
    if (dife>0){
        hace = 'Hace '+dife+' año'+(dife>1?'s':'');
    }else{
        dife = dateB.diff(dateC, 'months');
        if (dife>0){
            hace = 'Hace '+dife+' mes'+(dife>1?'es':'');
        }else{
            dife = dateB.diff(dateC, 'days');
            if (dife>0){
                hace = 'Hace '+dife+' dia'+(dife>1?'s':'');
            }else{
                dife = dateB.diff(dateC, 'hours');
                if (dife>0){
                    hace = 'Hace '+dife+' hora'+(dife>1?'s':'');
                }else{
                    dife = dateB.diff(dateC, 'minutes');
                    if (dife>0){
                        hace = 'Hace '+dife+' minuto'+(dife>1?'s':'');
                    }else{
                        dife = dateB.diff(dateC, 'seconds');
                        if (dife>0){
                            hace = 'Hace '+dife+' segundo'+(dife>1?'s':'');
                        }else{
                            hace = 'Hace un momento';

                        }
                    }
                }
            }
        }
    }


    chat_li_id+=1;
    if(align=="left"){
        return $('<li id="line-chat-'+chat_li_id+'" class="'+align+' clearfix">'+
            '<div class="chat-body clearfix">'+
            '<div class="header">'+
            '<strong class="primary-font">'+name+'</strong> <small class="pull-right text-muted">'+
            '<span class="glyphicon glyphicon-time"></span>'+hace+'</small>'+
            '</div>'+
            '<p>'+ obj.message +
            '</p>'+
            '</div>'+
            '</li>');
    }else{
        return $('<li id="line-chat-'+chat_li_id+'" class="'+align+' clearfix">'+
            '<div class="chat-body clearfix">'+
            '<div class="header">'+
            '<small class=" text-muted"><span class="glyphicon glyphicon-time"></span>'+hace+'</small>'+
            '<strong class="pull-right primary-font">'+name+'</strong>'+
            '</div>'+
            '<p>'+ obj.message +
            '</p>'+
            '</div>'+
            '</li>');
    }
}

var startLineSecure = function(){
    if(SyncSecrect==null){
        SyncSecrect         =   $.jCryption.encrypt(createRandomString(), createRandomString(10));
        var _getKeysURL     = 'http://' + window.location.hostname+'/login/getPublicKey';
        var _handshakeURL   = 'http://' + window.location.hostname+'/login/handshake';
        $.jCryption.authenticate(SyncSecrect, _getKeysURL, _handshakeURL, function(AESKey) {
            console.log('Linea segura')
            SyncLineSecure = true;
        }, function() {
            console.log('Linea NO segura')
            SyncLineSecure = false;
        });
    }
}
var createRandomString = function(long){
    if(!long){
        long=5;
    }
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+{=+*";

    for( var i=0; i < long; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}