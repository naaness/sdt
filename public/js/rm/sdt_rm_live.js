/**
 * Created by nesto_000 on 9/05/15.
 */
Offline.options = {checkOnLoad: true};
Offline.on("up", checkifbackonline);
Offline.on("down", checkifbackoffline);
function checkifbackonline(evt){
    OnLineSync=true;
    $("#rm-bt-email").fadeIn();
    $("#rm-bt-goTags").fadeIn();
    startLineSecure();
}
function checkifbackoffline(evt){
    OnLineSync=false;
    $("#rm-bt-email").fadeOut();
    $("#rm-bt-goTags").fadeOut();
}

$(document).ready(function () {
    dayChanges = getDaysChanged();
    Offline.check();
    navRM();

    startLineSecure();

    time_cal = setInterval(showDays, 300);

    $("#sync-rm").click(function(){
        console.log(registries_old.filter(Boolean))
    });

    $("#rm_project_task").html('');
    $("#rm_project_task").append('<option value="0" >--Sin proyecto--</option>');
    populateDelegates();
    $.post(server+'/projects/getProjects', function(datos){
        for(var i=0; i<datos.length;i++){
            $("#rm_project_task").append('<option value="'+datos[i].id+'" >'+datos[i].name+'</option>');
        }
    }, 'json');

    $("#rm_project_task").change(function(){
        if($("#rm_project_task").val()>0){
            $.post(server+'/users/getUsersProjects','id='+$("#rm_project_task").val(), function(datos){
                var user_id =datos.user_id;
                var users =datos.users;
                $("#rm_delegate").html('');
                if(users.length>0){
                    for(var i=0; i<users.length;i++){
                        var user_a='';
                        if(users[i].id==user_id){
                            user_a='selected';
                        }
                        $("#rm_delegate").append('<option value="'+users[i].id+'" '+user_a+'>'+users[i].username+'</option>')
                    }
                    $("#htd-tarea-tarea").prop("disabled", false);
                }else{
                    $("#rm_delegate").append('<option value="0" >--Sin usuarios--</option>');
                    $("#htd-tarea-tarea").prop("disabled", true);
                }

            }, 'json').fail(function() {
                errorConexcionRM();
            });
        }else{
            populateDelegates();
        }
    });

    $('#htd-tarea-tarea').click(function(){
        // rm_name_to_htd
        var id_dia = $("#rm-id-rm").val();
        var fecha_tras = $("#rm_date_to_htd").val();
        var nombre = $("#rm_name_to_htd").val();
        var delegate = $("#rm_delegate").val();
        var proyecto = $("#rm_project_task").val();
        $('#htd-tarea-tarea').button('loading');
        $.post(server+'/tasks/traslateRMtoHTD','id_rm='+id_dia+"&date="+fecha_tras+"&name="+nombre+"&description="+$("#rm_description_to_htd").val()+"&priority="+$("#rm_priority_to_htd").val()+'&delegate='+delegate+'&project='+proyecto, function(datos){
            if (datos!="None" && datos!="") {
                $('#myModalRMtoHTD').modal('hide');
                $('#htd-tarea-tarea').button('reset');

            };
        }, 'json');
    });

    // Buscar Palabra
    $('#loading-sowrds-rm').click(function () {
        showSpinner($("#lista-Palabras"));
        $("#lista-Palabras").html('');
        var encabezado = $('<a class="list-group-item active" >Lista de palabras encontradas</a>');
        $("#lista-Palabras").append(encabezado);
        $.post(server+'/rmregistries/search_word','word='+$("#sword-to-search").val(), function(datos){
            if (datos=='Other') {

            }else{
                if (datos!="None") {
                    hideSpinner($("#lista-Palabras"));
                    for (var i = 0; i < datos.length; i++) {
                        var lista = $('<a class="list-group-item search-day-rm '+datos[i].day+'" style="cursor:pointer;"><span class="badge">'+datos[i].day+'</span>'+datos[i].html+'</a>');
                        $("#lista-Palabras").append(lista);
                    }
                    $("#lista-Palabras").attr("style","height: 300px; overflow-y: scroll;");
                };
            }
        }, 'json').fail(function() {
            errorConexcionRM();
        });
    });

    // Enviar por correo
    $("#submit_email").click(function(){
        Offline.check();
        $('#submit_email').attr("disabled", true);
        $.post(server+'/rmregistries/send_email','day='+$("#rm_searchdate").val()+'&emails='+$("#correo").val()+'&subject='+$("#asunto").val(), function(datos){
            if (datos!="None") {
                if (datos=='Other') {

                }else{
                    hideSpinner($("#asunto"));
                    $('#submit_email').attr("disabled", false);
                    alert("Correo enviado");
                }
            }else{
                alert("Ha ocurrido un error en el envio");
            }
            $('#rm_btn_mail').modal('hide');
        }, 'json').fail(function() {
            hideSpinner($("#asunto"));
            alert("Ha ocurrido un error al enviar el correo, por favor revise el acceso a internet e intente de nuevo.");
            errorConexcion();
        });
    });

});

var getRegistries = function () {
    stopSycn();
    showSpinner($("#rm_contenido"));
    $("#sdt-rm-date-string").text(dateToString(rm_goday));
    $("#rm_searchdate").val(rm_goday);

    reg_local_del=[];
    var r_l_d = [];
    reg_local_cha = getLocalRegistriesChange();
    if(reg_local_cha.length){
        reg_local_del = reg_local_cha.delete;
        for (var i = 0; i < reg_local_del.length; i++) {
            r_l_d[reg_local_del[i].id]=reg_local_del[i].id;
        }
    }

    registries_old = [];
    if(Offline.state == 'up'){
        $.post(server + '/rmregistries/getRegistriesJson', 'date=' + rm_goday,function (datos) {
            time_sycn = moment(new Date()).diff(new Date(datos.time), 'seconds');
            // console.log(time_sycn)
            hideSpinner($("#rm_contenido"));
            var lbs = datos.labels;
            createSelectsTags(lbs);
            //Iniciar sincronizacion 1
            initSyncRm();
            // Si el id cotniene las letras rm entonces crear el registro en la Web
            // Si el registro contiene la propiedad update1 significa que el registro se debe actualizar en la web,
            // Si el objeto sdr_del_#fecha# contiene elementos , estos eliminaran los registros en la web
//            $.localStorage.setItem('sdt_l', JSON.stringify(lbs));
            els.set('sdt_l', lbs);

            var rms = getLocalRegistries();
            var rms_l = [];
            var rms_pos = [];
            for (var i = 0; i < rms.length; i++) {
                rms[i].registry = decodeContent(rms[i].registry);
                rms_l[rms[i].id]=rms[i];
                rms_pos[rms[i].id]=i;
                registries_old[rms[i].id] = rms[i];
                // console.log(registries_old[rms[i].id].numbering)
            }
            for (var i = 0; i < datos.rms.length; i++) {
                if(!r_l_d[datos.rms[i].id]){
                    datos.rms[i].registry = decodeContent(datos.rms[i].registry);
                    // datos.rms[i].created  = moment(new Date(datos.rms[i].created)).add('seconds', time_sycn).format('YYYY-MM-DD HH:mm:ss');
                    if(datos.rms[i].modified=='0000-00-00 00:00:00'){
                        datos.rms[i].modified = moment(new Date(datos.rms[i].created)).add('seconds', time_sycn).format('YYYY-MM-DD HH:mm:ss');
                    }else{
                        datos.rms[i].modified = moment(new Date(datos.rms[i].modified)).add('seconds', time_sycn).format('YYYY-MM-DD HH:mm:ss');
                    }
                    if(rms_l[datos.rms[i].id]){
                        //
                        var dateA = moment(new Date( rms[rms_pos[datos.rms[i].id]].modified ));
                        var dateB = moment(new Date( datos.rms[i].modified ));
                        var dife = dateA.diff(dateB, 'seconds');
//                    console.log(dife+" "+datos.rms[i].modified+" "+rms[rms_pos[datos.rms[i].id]].modified)
                        if(Math.abs(dife)>2){ // Si no, la diferencia es muy pequeña para considerarlo como una actualizacion
                            if(dife<0){
                                rms[rms_pos[datos.rms[i].id]].checked       =   datos.rms[i].checked;
                                rms[rms_pos[datos.rms[i].id]].acordion      =   datos.rms[i].acordion;
                                rms[rms_pos[datos.rms[i].id]].registry      =   datos.rms[i].registry;
                                rms[rms_pos[datos.rms[i].id]].rm_label_id   =   datos.rms[i].rm_label_id;
                                rms[rms_pos[datos.rms[i].id]].numbering     =   datos.rms[i].numbering;
                                rms[rms_pos[datos.rms[i].id]].modified      =   datos.rms[i].modified; // Local actualizado
                                //console.log("Local   "+datos.rms[i].modified+" "+rms[rms_pos[datos.rms[i].id]].modified)
                            }else{
//                                rms[rms_pos[datos.rms[i].id]].update = 1; // Actualizar la web
                                //console.log("Web     "+datos.rms[i].modified+" "+rms[rms_pos[datos.rms[i].id]].modified)
                            }
                            registries_old[datos.rms[i].id] = JSON.parse(JSON.stringify(datos.rms[i]));
                        }else{
//                    console.log("Nada2 "+rms[rms_pos[datos.rms[i].id]].created+" "+rms[rms_pos[datos.rms[i].id]].modified)
                        }

                    }else{
                        rms.push(datos.rms[i]);
                        registries_old[datos.rms[i].id] =JSON.parse(JSON.stringify(datos.rms[i]));
                    }
                    //console.log(registries_old[datos.rms[i].id].numbering)
                }
            }
            endSyncRm();

            builtHtmlRm(rms);
            setLocalRegistries();
            seeTr();
            SyncCan=true;
            resetSycn()
        }, 'json').fail(function () {
            registries_old=getLocalRegistries();
            loadOffline();
            SyncCan=true;
            resetSycn()
        });
    }else{
        loadOffline();
        SyncCan=true;
    }
}

var loadOffline = function(){
    time_sycn = 0;
    lbs = getLocalLabels();
    createSelectsTags(lbs);
    builtHtmlRm(getLocalRegistries());
    errorConexcionRM();
    seeTr();
    registries_old = JSON.stringify(registries_old);
    registries_old = JSON.parse(registries_old);
}
var initSyncRm = function(){
    $("#on-off-line").find('span').addClass('rm-sync');
}
var endSyncRm = function(){
    $("#on-off-line").find('span').removeClass('rm-sync');
}
var createSelectsTags = function(lbs){
    labels = [];
    labelsHtml='<option value="0">Por defecto</option>';
    for (var i = 0; i < lbs.length; i++) {
        labels[lbs[i].id] = lbs[i];
        labelsHtml+='<option value="'+lbs[i].id+'">'+lbs[i].name+'</option>';
    }
}

var builtHtmlRm = function (datos) {
    $(".notebook").html('');
    var rms = datos;

    var html = '';
    registries = [];
    // console.log(registries_old)
    var last_tr = null;
    var last_numbering = null;
    for (var i = 0; i < rms.length; i++) {
        if(rms[i].status){
            registries[rms[i].id] = rms[i];

            var html = createHtmlTr(rms[i].id,last_tr);
            var new_tr = $(html);
            $(".notebook").append(new_tr);
            addPopoverRm(new_tr.find('.check'));
            builtFunctions(new_tr);
            ch_tr_id=rms[i].id;
            changeSeen();
            last_tr = rms[i].id;
            last_numbering = rms[i].numbering;
        }
    }
    if(registries_old.length==0){
        registries_old      =   JSON.stringify(registries);
        registries_old      =   JSON.parse(registries_old);
    }

    ch_tr_id=null;
    ch_tr_id_temp=null;
    html = '<div class="rm-tr" id="tr_new">' +
        '<div class="rm-number-dummy"></div>' +
        '<div class="rm-content-blackboard">' +
        '<div class="rm-blackboard " style="margin-left: 10px; word-break: break-all;" id="tr_new_click">' +
        '<span><small><em>Click aqui para iniciar a escribir... </small></em><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></span>' +
        '</div>' +
        '</div>' +
        '</div>';
    $(".notebook").append(html);

    builtFunctions();
    calculateHeightTrNew();
    sanityNumbering();

    $("#tr_new_click").click(function(){
        var time_today = moment(new Date()).format('YYYY-MM-DD HH:mm:ss');
        var rm_number = $( ".rm-number" ).last();
        var last_numering   =   "1";
        var last_order      =   "0";
        var last_label      =   "0";
        ch_tr_id            =   null;
        if(rm_number.length){
            last_numering   =   rm_number.text();
            last_order      =   $( ".rm-number").length;
            ch_tr_id        =   getIdTrN(rm_number);
            last_label      =   registries[ch_tr_id].rm_label_id;
        }

        var id_temp = 'rm'+Math.floor((Math.random() * 1000000) + 1);
        registries[id_temp] = new Object();
        registries[id_temp].checked     = 0;
        registries[id_temp].created     = time_today;
        registries[id_temp].modified    = time_today;
        registries[id_temp].day         = dateFormantStandar(rm_goday)+' '+moment(new Date()).format('HH:mm:ss'); // cambiarlo al dia rm_goday
        registries[id_temp].id          = id_temp;
        registries[id_temp].numbering   = last_numering;
        registries[id_temp].order_r     = last_order;
        registries[id_temp].registry    = "";
        registries[id_temp].rm_label_id = last_label;
        registries[id_temp].status      = 1;
        registries[id_temp].user_id     = 0;
        registries[id_temp].acordion    = 1;

        var html = createHtmlTr(id_temp,ch_tr_id);
        var new_tr = $(html);
        $(this).parent().parent().before(new_tr);
        addPopoverRm(new_tr.find('.check'));
        sanityNumbering();
        ch_tr_id = id_temp;
        new_tr.find('.rm-blackboard').find('span').remove();
        new_tr.find('.rm-blackboard').focus();
        builtFunctions(new_tr);
    });
}
var createHtmlTr = function(id_tr){
    var re = registries[id_tr];
    var style = getStyle(id_tr);
    var ico = 'plus';
    if(registries[id_tr].acordion==1){
        ico = 'minus';
    }
    html =  '<div class="rm-tr" id="tr_' + id_tr + '" >' +
                '<div class="rm-td-check" >&nbsp;' +
                    '<button type="button" class="btn btn-default dropdown-toggle btn-xs check" data-toggle="popover" id="op_'+id_tr+'">'+
                        '<span class="caret"></span>'+
                    '</button>' +
                    '<span class="glyphicon glyphicon-'+ico+'-sign rm-acordion rm-invisible"></span>' +
                '</div>' +
                '<div class="rm-number" >' + re.numbering + '</div>' +
                '<div class="rm-content-blackboard">' +
                    '<div class="rm-blackboard" style="margin-left: 5px; word-break: break-all;" contenteditable="true">' +
                        '<span style="' + style + '">' + re.registry + '</span>' +
                    '</div>' +
                '</div>' +
                '<div></div>' +
            '</div>';
    return html;
}
var applyOpacity = function (color, op) {
    var rgbaCol = 'rgba(' + parseInt(color.slice(-6, -4), 16)
        + ',' + parseInt(color.slice(-4, -2), 16)
        + ',' + parseInt(color.slice(-2), 16)
        + ',' + op + ')';
    return rgbaCol;
}

var calculateHeightTrNew = function () {
    var height      =   $(".notebook").height();
    var height2     =   $("#tr_new").height();
    if (height < 500) {
        $("#tr_new").height(500 - (height-height2));
    } else {
        $("#tr_new").height(height2);
    }
}

var sanitizeContent = function (e) {
    var e_clone = $(e).html();
    var tag = 'span';
    var regex = new RegExp('<' + tag + '[^><]*>|<.' + tag + '[^><]*>', 'g')
    e_clone = e_clone.replace(regex, "");
    e_clone = e_clone.replace(/&nbsp;/g, " ");
    e_clone = e_clone.replace(/&/g, "||ans||");
    e_clone = e_clone.replace(/=/g, "||igu||");
    e_clone = e_clone.replace(/\+/g, '||mas||');
    return e_clone;
}

var decodeContent = function(text){
    text = text.replace(/\|\|ans\|\|/g, "&");
    text = text.replace(/\|\|igu\|\|/g, "=");
    text = text.replace(/\|\|mas\|\|/g, "+");
    return text;
}

var normalizeStyleSpan = function (bb) {
    // remover los elementos que no necesitamos
    $(bb).find('*').each(function () {
        $(this).removeAttr('style');
        $(this).removeAttr('href');
        $(this).removeAttr('class');
        $(this).removeAttr('title');
    })
    var id_tr = getIdTr(bb);
    applyStyleBlackboard(id_tr);

}
var applyStyleBlackboard = function(id_tr){
    var style = getStyle(id_tr);
    $('#tr_'+id_tr+' .rm-blackboard').find('span').each(function () {
        $(this).attr('style',style);
    });
}
var repositionLabels = function(nu){
    var str = $(nu).text().trim();
    var res = str.split(".");
    var niv = res.length;
    var tdpp = $(nu).parent();
    var hijop1 = $(tdpp).find("div[class^='rm-content-blackboard']");
    var Left1 = $(nu).css("Left");
    var marginLeft2 = $(hijop1).css("marginLeft");
    Left1 = Left1.substring(0,Left1.length-2);
    marginLeft2 = marginLeft2.substring(0,marginLeft2.length-2);
    $(nu).animate({
        left: '+=' + (100 + (12 * (niv - 1)-Left1)) + 'px'
    }, 500);
    $(hijop1).animate({
        marginLeft: '+=' + (130 + (32 * (niv - 1)-marginLeft2)) + 'px'
    }, 500);
}

var builtFunctions = function(tr_obj){
    var rm_number       =   $(tr_obj).find('.rm-number');
    var rm_blackboard   =   $(tr_obj).find('.rm-blackboard');
    var rm_check        =   $(tr_obj).find('.check');
    var rm_acordion     =   $(tr_obj).find('.rm-acordion');

//    $(".rm-number").each(function (index, elemento) {
//        repositionLabels(elemento);
//    });

    // repositionLabels($(rm_number));
    rm_blackboard.keypress(function(e){
        if (e.keyCode == 13) {
            if (e.shiftKey === false) {
                e.preventDefault();
                ch_tr_id = getIdTr(this);
                var id_temp = 'rm'+Math.floor((Math.random() * 1000000) + 1);
                registries[id_temp] = new Object();
                registries[id_temp].checked     = 0;
                registries[id_temp].created     = registries[ch_tr_id].created;
                registries[id_temp].day         = registries[ch_tr_id].day;
                registries[id_temp].id          = id_temp;
                registries[id_temp].numbering   = registries[ch_tr_id].numbering;
                registries[id_temp].order_r     = registries[ch_tr_id].order_r;
                registries[id_temp].registry    = "";
                registries[id_temp].rm_label_id = registries[ch_tr_id].rm_label_id;
                registries[id_temp].status      = registries[ch_tr_id].status;
                registries[id_temp].user_id     = registries[ch_tr_id].user_id;
                registries[id_temp].acordion    = registries[ch_tr_id].acordion;

                var html = createHtmlTr(id_temp,ch_tr_id);
                var new_tr = $(html);
                $("#tr_"+ch_tr_id).after(new_tr);
                addPopoverRm(new_tr.find('.check'));
                sanityNumbering();
                ch_tr_id = id_temp;
                new_tr.find('.rm-blackboard').find('span').remove();
                new_tr.find('.rm-blackboard').focus();
                builtFunctions(new_tr);
                setLocalRegistries();
            }
        }else{
            if($(this).text()==''){
                var style = getStyle(ch_tr_id);
                $(this).html('<span style="'+style+'">&nbsp;</span>');
                $(this).find('span').focus();
            }
        }
    });
    rm_blackboard.click(function(e){
        ch_tr_id = getIdTr(this);
        seenPopover($(this));
//        sanitizeContent(this);
    });

    rm_blackboard.on('input propertychange', function(e){
        normalizeStyleSpan(this);
        if(registries[ch_tr_id]){
            registries[ch_tr_id].registry       =   sanitizeContent(this);
        }
        console.log(registries[ch_tr_id].registry + ' ' +registries_old[ch_tr_id].registry);
        updateTempLocal();
        setLocalRegistries();
    });
    // dar y quitar sangria
    rm_blackboard.keydown(function(e){
        var keyCode = e.keyCode || e.which;
        if (e.shiftKey === true && keyCode == 9) {
            e.preventDefault();
            rm_upLevel();
            setLocalRegistries();
        } else if (keyCode == 9) {
            e.preventDefault();
            rm_downLevel();
            setLocalRegistries();
        }

    });
    // Llamar el popover para cada registro, ocultando los demas y reposicionandolo.
    rm_check.click(function(e){
        seenPopover($(this));
        rm_last_popover = $(this);
        ch_tr_id = getIdTr($(this));
        ch_tr_id_temp = ch_tr_id;
        var id_unit_time = $(".popover.fade.bottom.in").attr('id');
        if(id_unit_time){
            $(this).addClass('check-visible');
        }
        $('.popover.in').css('left','3px')
        $('.popover.bottom.in > .arrow').css('left','40%');


        $(".tags").val(registries[ch_tr_id].rm_label_id);

        //Visto / No visto
        changeSeen();
        $(".rm-seen").click(function(){
            registries[ch_tr_id].checked = registries[ch_tr_id].checked ? 0:1;
            ch_tr_id = ch_tr_id_temp;
            changeSeen();
            applyStyleBlackboard(ch_tr_id);
            updateTempLocal();
            setLocalRegistries();
        });
        // Disminuir sangria
        $(".rm-lv-up").click(function(){
            ch_tr_id = ch_tr_id_temp;
            rm_upLevel();
            updateTempLocal();
            setLocalRegistries();
        });
        // Aumentar sangria
        $(".rm-lv-down").click(function(){
            ch_tr_id = ch_tr_id_temp;
            rm_downLevel();
            updateTempLocal();
            setLocalRegistries();
        });
        $(".rm-new-paragraph").click(function(){
            ch_tr_id = ch_tr_id_temp;
            var my_blackboard   =   $("#tr_" + ch_tr_id).find(".rm-blackboard");
            var style           =   getStyle(ch_tr_id);
            var html            =   '<div><span class="" style="'+style+'"><br></span></div>';
            my_blackboard.append(html);
        });
        $(".tags").change(function(){
            ch_tr_id = ch_tr_id_temp;
            var label_before = registries[ch_tr_id].rm_label_id;
            registries[ch_tr_id].rm_label_id = $(this).val();
            applyStyleBlackboard(ch_tr_id);
            var segui = 0;
            var my_num_length = registries[ch_tr_id].numbering.length;
            var my_numering = registries[ch_tr_id].numbering;
            $(".rm-number").each(function(index, elemento){
                var tr_id = getIdTrN($(elemento));
                var numering = $(elemento).text();
                if (segui){
                    if(registries[tr_id].rm_label_id==0 || registries[tr_id].rm_label_id == label_before){
                        if(numering.substring(0, my_num_length)==my_numering){
                            registries[tr_id].rm_label_id = registries[ch_tr_id].rm_label_id;
                            applyStyleBlackboard(tr_id);
                        }
                    }
                }else{
                    if(numering==my_numering){
                        segui=1;
                    }
                }
            });
            updateTempLocal();
            setLocalRegistries();
        });
        // su el registro maestro no se ha sincronizado no se puede convertir en tarea
        if(ch_tr_id_temp.indexOf('rm')==0){
            $('.rm_to_htd').prop("disabled", true);
        }
        // Crear tareadel registro Maestro al HTD
        $('.rm_to_htd').click(function(){
            ch_tr_id = ch_tr_id_temp;
            $("#rm-id-rm").val(ch_tr_id);
            // No mas de 30 caracteres o 4 palabras
            var texto = registries[ch_tr_id].registry;
            texto=texto.trim();
            var arrTexto = texto.split(" ");
            texto="";
            for (var i = 0; i < arrTexto.length; i++) {
                if (texto.length<=20) {
                    texto = texto + arrTexto[i] + " ";
                };
            };
            $("#rm_name_to_htd").val(texto);
            $("#rm_description_to_htd").val(registries[ch_tr_id].registry);
            $('#myModalRMtoHTD').modal('show');
            seenPopover($(this));
        });

        // Eliminar un registro
        $('.rm-delete').click(function(){
            if (confirm("Realmente desea eliminar este registro?")) {
                ch_tr_id = ch_tr_id_temp;
                var my_numering     =   registries[ch_tr_id].numbering;
                var my_num_length   =   my_numering.length;
                var segui           =   0;
                var last_numering   =   null;
                $(".rm-number").each(function (index, elemento) {
                    var numering = $(elemento).text().trim();
                    if (segui) {
                        if(numering.substring(0, my_num_length)==my_numering){
                            // numering = last_numering + numering.substring(my_num_length);
                            numering = numering.substring(my_num_length+last_numering.length);
                            var tr_id = getIdTrN($(elemento));
                            registries[tr_id].acordion=1;
                            $("#tr_" + tr_id).attr('style','display:block');
                            // console.log(registries[tr_id].numbering + '  ' + registries[tr_id].acordion);
                        }
                    }else{
                        if (my_numering == numering) {
                            segui               =   1;
                            if(!last_numering){
                                last_numering       =   numering;
                            }
                        }else{
                            last_numering       =   numering;
                        }
                    }
                    $(elemento).text(numering);
                });
                $("#tr_"+ch_tr_id).remove();
                registries[ch_tr_id].status = 0;
                if(registries[ch_tr_id].id.indexOf('rm')==-1){
                    reg_local_del.push(registries[ch_tr_id]);
                }
                sanityNumbering();
                calculateHeightTrNew();
                updateTempLocal();
                setLocalRegistries();
            }
        });
    });
    // Crear acordeon
    rm_acordion.click(function(e){
        ch_tr_id = getIdTr($(this));
        var cl = 'glyphicon glyphicon-minus-sign';
        if(registries[ch_tr_id].acordion==0){
            registries[ch_tr_id].acordion = 1;
        }else{
            registries[ch_tr_id].acordion = 0;
            cl = 'glyphicon glyphicon-plus-sign';
        }
        updateTempLocal();
        $(this).attr('class',cl+ ' rm-acordion');
        seeTr();
        calculateHeightTrNew();
        setLocalRegistries();
    });
}
var seeTr = function(){
    var num_last_level = [];
    $(".rm-number").each(function(index, elemento){
        var tr_id = $(elemento).parent().attr('id');
        tr_id = tr_id.split('_');
        tr_id = tr_id[1];
        var str = $(elemento).text();
        var res = str.split(".");
        var niv = res.length;
        var visible=0;
        if(niv==1){
            visible=1;
            num_last_level[1]=registries[tr_id].acordion;
        }else{
            num_last_level[niv]= (registries[tr_id].acordion && num_last_level[niv-1])?1:0;
            if (num_last_level[niv-1]==1){
                visible=1;
            }
        }

        if(visible){
            $("#tr_" + tr_id).attr('style','display:block');
        }else{
            $("#tr_" + tr_id).attr('style','display:none');
        }
    });
}

var updateTempLocal = function(){
    if(registries[ch_tr_id]){
        if(registries[ch_tr_id].hasOwnProperty('modified')){
            var time_today                      =   moment(new Date()).format('YYYY-MM-DD HH:mm:ss');
            registries[ch_tr_id].modified       =   time_today;
            reg_local[local_reg[ch_tr_id]]      =   registries[ch_tr_id];
        }
    }
}

var seenPopover = function(obj){
    if (rm_last_popover) {
        $(rm_last_popover).removeClass('check-visible');
        if (rm_last_popover.attr('id')!=$(obj).attr('id')) {
            rm_last_popover.popover('hide');
        }else{
            rm_last_popover=null;
        }
    }
}
var changeSeen = function(){
    if(registries[ch_tr_id].checked==1){
        $(".rm-seen").find('.glyphicon').attr('class','glyphicon glyphicon-eye-close');
        $("#tr_"+ch_tr_id).find('.rm-number').fadeTo( "slow", 0.50 );
        $("#tr_"+ch_tr_id).find('.rm-blackboard').fadeTo( "slow", 0.50 );
    }else{
        $(".rm-seen").find('.glyphicon').attr('class','glyphicon glyphicon-eye-open');
        $("#tr_"+ch_tr_id).find('.rm-number').fadeTo( "slow", 1 );
        $("#tr_"+ch_tr_id).find('.rm-blackboard').fadeTo( "slow", 1 );
    }
}
var getIdTr = function (bb) {
    var id_tr = $(bb).parent().parent().attr('id');
    id_tr = id_tr.split('_');
    return id_tr[1];
}
var getIdTrN = function (bb) {
    var id_tr = $(bb).parent().attr('id');
    id_tr = id_tr.split('_');
    return id_tr[1];
}

var navRM = function () {
    $("#rm_left_day").click(function () {
        rm_goday = moment.utc(dateNewStandard(rm_goday)).add('days', -1).format('DD/MM/YYYY');
        getRegistries();
    });
    $("#rm_right_day").click(function () {
        rm_goday = moment.utc(dateNewStandard(rm_goday)).add('days', +1).format('DD/MM/YYYY');
        getRegistries();
    });
    $("#rm_btn_hoy").click(function () {
        rm_go_Today();
    });
    $('#rm_searchRM').on('click', '.search-day-rm', function () {
        var clase = $(this).attr("class");
        var fecha = clase.split(" ");
        rm_goday = fecha[2];
        getRegistries();
    });
    $("#rm_searchdate").change(function () {
        rm_goday = $("#rm_searchdate").val();
        getRegistries();
    });
    $("#rm_searchdate").click(function () {
        fechaG = null;
    });
}

var rm_go_Today = function () {
    rm_goday = moment(rm_today).format('DD/MM/YYYY');
    // rm_goday = '21/07/2014';
    getRegistries();
}
var errorConexcionRM = function () {
    hideSpinner($("#rm_contenido"));
    console.log("error de conexion");
    //errorConexcion();
}
var showDays = function () {
    var mes = $(".ui-datepicker-month").val();
    if (!(typeof mes == "undefined")) {
        mes = parseInt(mes) + 1;
        if (mes < 10) {
            mes = "0" + mes;
        }
        var anio = $(".ui-datepicker-year").val();
        var fecha = "01/" + mes + "/" + anio;
        if (fechaG != fecha) {
            $.post(server + '/rmregistries/get_days', 'date=' + fecha,function (datos) {
                $("#ui-datepicker-div td").each(function (index, elemento) {
                    hijo = $(elemento).find("a");
                    dia = hijo.text().trim();
                    if (dia != "") {
                        if (dia < 10) {
                            dia = "0" + dia
                        };
                        if (typeof datos.days[dia] != "undefined") {
                            hijo.css("color", "blue");
                            if (datos.days[dia]) {
                                hijo.css("color", "#20ba13");
                            };
                        };
                    };
                });
            }, 'json').fail(function () {
                console.log("error de conexion");
                SOnline = false;
                errorConexcion();
            });
            fechaG = fecha;
        };
    }
}

var rm_downLevel = function () {
    // ch_tr_id
    var my_numering         = $("#tr_" + ch_tr_id).find(".rm-number").text();
    var my_levels           = my_numering.split(".");
    var my_level            = my_levels.length;
    var my_num_length       = my_numering.length;
    var my_last_num_lev     = parseInt(my_levels[my_level-1]);

    if(my_last_num_lev!=1){
        var sigui = 0;
        var last_numering       = "";
        var my_new_numering     = "";
        $(".rm-number").each(function (index, elemento) {
            var numering = $(elemento).text().trim();
            if (sigui == 1) {
                if(numering.substring(0, my_num_length)==my_numering){
                    numering = my_new_numering + numering.substring(my_num_length);
                    fixPosLabels(elemento);
                }
            }
            if (sigui == 0) {
                if (my_numering == numering) {
                    sigui               = 1;
                    var leves           = last_numering.split(".");
                    if(my_level<leves.length){
                        my_new_numering = '';
                        for (var i = 0; i <= my_level; i++) {
                            my_new_numering+=leves[i];
                            my_new_numering+=".";
                        };
                        my_new_numering=my_new_numering.slice(0,my_new_numering.length-1);
                    }else{
                        my_new_numering = last_numering+".1";
                    }
                    numering            = my_new_numering;
                    fixPosLabels(elemento);
                }else{
                    last_numering       = numering;
                }
            }
            $(elemento).text(numering);
        });
    }
    sanityNumbering();
}

var rm_upLevel = function () {
    // ch_tr_id
    var my_numering         = $("#tr_" + ch_tr_id).find(".rm-number").text();
    var my_levels           = my_numering.split(".");
    var my_level            = my_levels.length;
    var my_num_length       = my_numering.length;
    var sigui = 0;
    var last_numering       = "";
    var my_new_numering     = "";
    $(".rm-number").each(function (index, elemento) {
        var numering = $(elemento).text().trim();
        if (sigui == 1) {
            if(numering.substring(0, my_num_length)==my_numering){
                numering = my_new_numering + numering.substring(my_num_length);
                fixPosLabels(elemento);
            }
        }
        if (sigui == 0) {
            if (my_numering == numering) {
                sigui               = 1;
                var leves           = last_numering.split(".");
                my_new_numering = '';
                for (var i = 0; i < my_level-1; i++) {
                    my_new_numering+=leves[i];
                    my_new_numering+=".";
                };
                my_new_numering=my_new_numering.slice(0,my_new_numering.length-1);
                numering            = my_new_numering;
                fixPosLabels(elemento);
            }else{
                last_numering       = numering;
            }
        }
        $(elemento).text(numering);
    });
    sanityNumbering();
}

var sanityNumbering = function(){
    // Esta funcion intenta establecer la numeracion
    // segun la sangria que tenga.
    var actual_level = 1;
    var primero = true;
    var num_last_level = [];
    var last_tr = null;
    var last_numbering = null;

    reg_local = [];
    local_reg = [];
    var cont = 0;
    var heightL = 0;
    $(".rm-number").each(function(index, elemento){
        // cambiar solo los del mismo nivel en el rango
        var str = $(elemento).text();
        var res = str.split(".");
        var niv = res.length;
        if (primero) {
            primero=false;
            num_last_level[1]="1";
            $(elemento).text(num_last_level[1]);
            actual_level=1;
        }else{
            if (!(num_last_level.hasOwnProperty(niv))) {
                num_last_level[niv]=num_last_level[(niv-1)]+'.1';
                $(elemento).text(num_last_level[niv]);
            }else{
                if (niv>actual_level) {
                    num_last_level[niv]=num_last_level[(niv-1)]+'.1';
                    $(elemento).text(num_last_level[niv]);
                }else{
                    $(elemento).text(incLevel(num_last_level[niv]));
                }
            }
            actual_level=niv;
        }
        repositionLabels(elemento);
        // almacenar la la ultima numeracion del nivel
        str = $(elemento).text();
        var id_tr = getIdTrN($(elemento));

        registries[id_tr].numbering     =   str;
        registries[id_tr].order_r       =   index+1;
        num_last_level[niv]             =   str;
        reg_local[cont]                 =   registries[id_tr];
        local_reg[id_tr]                =   cont;
        cont+=1;
        if(last_tr){
            if(registries[id_tr].numbering.indexOf(last_numbering)==0){
                $("#tr_"+last_tr).find('.rm-acordion').removeClass('rm-invisible');
            }else{
                $("#tr_"+last_tr).find('.rm-acordion').addClass('rm-invisible');
            }
        }
        last_tr = id_tr;
        last_numbering = str;
    });
}

var incLevel = function(num){
    num = ''+num+'';
    var numarr = num.split(".");
    var nivel = numarr.length;
    var newnum ="";
    // nivel de incremento
    for (var i = 0; i < numarr.length; i++) {
        if (i==nivel-1) {
            newnum+=(parseInt(numarr[i])+1);
        }else{
            newnum+=numarr[i];
        }
        newnum+=".";
    };
    newnum=newnum.slice(0,newnum.length-1);
    return newnum;
}

var fixPosLabels = function(nu){
//    var str = $(nu).text().trim();
//    var res = str.split(".");
//    var niv = res.length;
//    var tdpp = $(nu).parent();
//    var hijop1 = $(tdpp).find("div[class^='rm-content-blackboard']");
//    $(nu).css("marginLeft",(10 + (12 * (niv - 1)))+ 'px');
//    $(hijop1).css("marginLeft",(130 + (32 * (niv - 1)))+ 'px');
}
var addPopoverRm = function (obj){
    // Agregar los popover con botones
    var html = '<div style="width: 78px">' +
                    '<button type="button" class="btn btn-default rm-seen" style="width: 78px">'+
                        '<span class="glyphicon glyphicon-eye-close"></span>'+
                    '</button>' +
                    '<div class="input-group">'+
                        '<span class="input-group-btn" style="width:0%">'+
                            '<button type="button" class="btn btn-default rm-lv-up">'+
                                '<span class="glyphicon glyphicon-chevron-left"></span>'+
                            '</button>'+
                            '<button type="button" class="btn btn-default rm-lv-down">'+
                                '<span class="glyphicon glyphicon-chevron-right"></span>'+
                            '</button>'+
                        '</span>'+
                    '</div>'+
                    '<div class="input-group">'+
                        '<span class="input-group-btn" style="width:0%">'+
                            '<button type="button" class="btn btn-default rm-new-paragraph" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Crear interlineado">'+
                                '<span class="glyphicon glyphicon-text-height"></span>'+
                            '</button>'+
                            '<button type="button" class="btn btn-default rm_to_htd">'+
                                '<span class="glyphicon glyphicon-share-alt"></span>'+
                            '</button>'+
                        '</span>'+
                    '</div>'+
                    '<select class="btn btn-default tags" style="width: 78px">'+
                        labelsHtml+
                    '</select>'+
                    '<button type="button" class="btn btn-default rm-delete" style="width: 78px">'+
                        '<span class="glyphicon glyphicon-trash"></span>'+
                    '</button>' +
                '</div>';
    $(obj).popover({
        placement: 'bottom',
        html: 'true',
        content : html
    });
}

var getStyle = function(id_tr){
    var background_color_tag    = applyOpacity('#ffffff', '0.1');
    var color_tag               =   '';
    if (registries[id_tr].rm_label_id > 0) {
        color_tag = labels[registries[id_tr].rm_label_id].color;
        background_color_tag = labels[registries[id_tr].rm_label_id].b_color;
//        if (registries[id_tr].checked == 1) {
//            background_color_tag = labels[registries[id_tr].rm_label_id].b_color_checked;
//        } else {
//            background_color_tag = labels[registries[id_tr].rm_label_id].b_color;
//        }
    }
    return 'background-color: '+background_color_tag+';color: '+color_tag;
}

var populateDelegates = function(){
    $.post(server+'/users/getUsers', function(datos){
        var user_id =datos.user_id;
        var users =datos.users;
        $("#rm_delegate").html('');
        for(var i=0; i<users.length;i++){
            var user_a='';
            if(users[i].id==user_id){
                user_a='selected';
            }
            $("#rm_delegate").append('<option value="'+users[i].id+'" '+user_a+'>'+users[i].username+'</option>')
        }
    }, 'json');
}