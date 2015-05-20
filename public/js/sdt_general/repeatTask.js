/**
 * Created by nesto_000 on 9/04/15.
 */
var opt = new Object();
var posth = 0;

var initTaskRepeat = function(){
    $("#sdt-repeat-op1").change(function(){
        changeMainOption();
    });
    $("#op5").change(function(){
        var nu = $("input[name='op6']:checked").val();
        if (nu=='3'){
            changeDateEnd();
        }
        chekOption();
    });
    $("#op6-1").click(function(){
        $("#op7").attr("disabled", true);
        $("#op8").attr("disabled", true);
        $("#op7").val("");
        $("#op8").val("");
        chekOption();
    });
    $("#op6-2").click(function(){
        $("#op7").attr("disabled", false);
        if($("#sdt-repeat-op1").val()==1 || $("#sdt-repeat-op1").val()==7 ){
            $("#op7").val(5);
        }else {
            $("#op7").val(35);
        }

        $("#op8").attr("disabled", true);
        $("#op8").val("");
        chekOption();
    });
    $("#op6-3").click(function(){
        $("#op7").attr("disabled", true);
        $("#op8").attr("disabled", false);
        $("#op7").val("");
        changeDateEnd();
        chekOption();
    })

    $("#op7").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
            // Allow: Ctrl+C
            (e.keyCode == 67 && e.ctrlKey === true) ||
            // Allow: Ctrl+X
            (e.keyCode == 88 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

    });
    $("#op7").keyup(function (e) {
        if($(this).val()==""){
            $(this).val("1");
        }
        chekOption();
    });
    $("#sdt-repeat-op2").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
            // Allow: Ctrl+C
            (e.keyCode == 67 && e.ctrlKey === true) ||
            // Allow: Ctrl+X
            (e.keyCode == 88 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

    });
    $("#sdt-repeat-op2").change(function (e) {
        if($(this).val()==""){
            $(this).val("1");
        }
        chekOption();
    });
    $("input[id^='op3-']").click(function(){
        chekOption();
    });
    $("input[id^='op4-']").click(function(){
        chekOption();
    });
    $("#op8").change(function(){
        chekOption();
    });

    $("#sdt-repeat-submit").click(function(){
        setConfigurateRepat();
        $("#sdt-repeat-task").modal('hide');
        $.post(server+'/checklist/addRepeat','opt='+JSON.stringify(opt), function(datos){
            // verificar si una unidad de tiempo nueva
            if(datos.old_unid_t){
                var uni_basic = uniBasic(datos.old_unid_t);
                clearFileDummyAfter(uni_basic,datos.old_unid_t);
                changeInds(uni_basic,datos.old_unid_t,datos.unid_time_id);
                $("#"+datos.old_unid_t).attr("id",datos.unid_time_id);
            }
            // tipo de construccion, boorar las siguiente o conservar
            if(datos.built_type==2){
                clearFile(datos.task_id,datos.start_day);
            }else{
                clearFileDummy(datos.unid_time_id);
            }
            $("#"+datos.unid_time_id).removeClass(segui.class_u.future[2]);
            $("#"+datos.unid_time_id).removeClass(segui.class_u.future[3]);
            $("#"+datos.unid_time_id).addClass(segui.class_u.future[1]);
            var circle = $("#"+datos.unid_time_id).find('.circle');
            circle.removeClass(segui.icon[2]);
            circle.removeClass(segui.icon[3]);
            circle.addClass(segui.icon[1]);

            createRepeat(datos, true);
        }, 'json').fail(function() {
            console.log("error de conexion");
        });
    });
}
var setConfigurateRepat = function(){
    opt.unid_time_id    =   $("#sdt-repeat-dia").val();
    opt.task_id         =   getTdIdTask($("#sdt-repeat-id_td").val());
    opt.options         =   $("#sdt-repeat-op1").val(); // primer select
    opt.each_period     =   parseInt($("#sdt-repeat-op2").val()); // segundo select
    opt.day_L           =   $('input#op3-L').is(':checked')?1:0; // Lunes
    opt.day_M           =   $('input#op3-M').is(':checked')?1:0; // Martes
    opt.day_X           =   $('input#op3-X').is(':checked')?1:0; // Miercoles
    opt.day_J           =   $('input#op3-J').is(':checked')?1:0; // Jueves
    opt.day_V           =   $('input#op3-V').is(':checked')?1:0; // Viernes
    opt.day_S           =   $('input#op3-S').is(':checked')?1:0; // Sabado
    opt.day_D           =   $('input#op3-D').is(':checked')?1:0; // Domingo
    opt.month_week      =   $("input[name='op4']:checked").val(); // 1: dias del mes, 2: dia de la semana
    opt.start_day       =   $("#op5").val(); // fecha inicio
    opt.N_R_T           =   parseInt($("input[name='op6']:checked").val()); // 1: Nunca, 2: repeticiones, 3: hasta
    opt.repeat_interval =   parseInt($("#op7").val()); // repeticiones
    // debo encontrar la fecha de la siguiente accion
    opt.end_day = null;


    opt.next_day = getNextDay(opt,opt.start_day);


    // debo encontrar la fecha final de la tarea
    if(opt.N_R_T==1){
        opt.end_day         =   '31/12/9999'; // fecha final
    }else if(opt.N_R_T==2){
        var feSta   =   dateNewStandard(opt.start_day);
        results     =   createRecurrentEvent(opt,feSta,fechaIni);
        opt.end_day         =   results[results.length-1];
        if(opt.next_day){
            if(opt.next_day>opt.end_day){
                opt.next_day=null;
            }
        }
        opt.end_day         =   moment.utc(opt.end_day).format('DD/MM/YYYY');
    }else{
        opt.end_day         =   dateNewStandard($("#op8").val());
        if(opt.next_day){
            if(opt.next_day>opt.end_day){
                opt.next_day=null;
            }
        }
        opt.end_day         =   $("#op8").val(); // fecha final
    }
    if(opt.next_day){
        opt.next_day=moment.utc(opt.next_day).format('DD/MM/YYYY');
    }
    opt.day_position    =   posth;
    opt.built_type      =   parseInt($("input[name='op9']:checked").val()); //1: eliminar datos furutos, 2: conservarlos
}
var getNextDay = function(opt,start_day){
    var repet = opt.repeat_interval;
    opt.repeat_interval=2;
    var feSta = dateNewStandard(start_day);
    var results = createRecurrentEvent(opt,feSta,fechaIni);
    opt.repeat_interval = repet;
    var next_day = null;
    if(results.length>1){
        next_day        =   results[1];
        if(opt.end_day){
            if ( next_day > dateNewStandard(opt.end_day)){
                next_day = null;
            }
        }
    }
    return next_day;
}
var createRepeat = function(opt, leader){
    var feIni = fechaIni;
    var feEnd = moment.utc(fechaFin).add('days', -1).toDate();
    var feSta = dateNewStandard(opt.start_day);
    var rowT  = null;
    var colT  = null;
    var obj_tr = $("#tr_"+opt.task_id);
    if(obj_tr.length==0){
        // crear el Tr
    }else{
        // encontrar la fila
        rowT = findNRowTable(obj_tr);
    }

    var results = createRecurrentEvent(opt,feSta,feIni,feEnd);
    if(!unitTimes[opt.unid_time_id]){
        unitTimes[opt.unid_time_id] = new Object();
        unitTimes[opt.unid_time_id].opt = opt;
        unitTimes[opt.unid_time_id].ut = [];
    }
    if(!tasksRepat[opt.task_id]){
        tasksRepat[opt.task_id]=true;
    }
    for (var i = 0; i < results.length; i++) {
        if(results[i]>=feIni){
            // console.log(results[i].toLocaleString());
            var colT =  moment.utc(results[i]).diff(moment.utc(feIni), 'days');
            colT+=5;
            var _ut = $("#"+opt.task_id+'_'+rowT+'_'+colT).find(".circle");
            if (_ut.length==0) {
                var id_temp = opt.task_id+'_'+opt.unid_time_id+'_'+Math.floor((Math.random() * 1000000) + 1);
//                console.log(opt.unid_time_id+ ' + '+id_temp);
                unitTimes[opt.unid_time_id].ut.push(id_temp);
//                console.log(id_temp+ ' / '+opt.unid_time_id);
                timesUnit[id_temp] = opt.unid_time_id;
                // console.log("#"+opt.task_id+'_'+rowT+'_'+colT + "  "+id_temp);
                addUnitTimeNull($("#"+opt.task_id+'_'+rowT+'_'+colT),id_temp,true);
            }else{
                _ut = $("#"+opt.task_id+'_'+rowT+'_'+colT).find(".orange");
                if(opt.unid_time_id!=_ut.attr("id")){
                    unitTimes[opt.unid_time_id].ut.push(_ut.attr("id"));
                    timesUnit[_ut.attr("id")] = opt.unid_time_id;
                }
            }
        }
    }

    percentRow(opt.task_id);

}

var createRecurrentEvent = function(opt,feSta,feIni,feEnd){
    var wd = [];
    var bmd = [];
    var freq = RRule.WEEKLY;
    if (opt.options==1){
        freq = RRule.DAILY
    }else if (opt.options==2){
        //opt.each_period=1;
        wd = [RRule.MO, RRule.TU, RRule.WE, RRule.TH, RRule.FR];
    }else if (opt.options==3){
        //opt.each_period=1;
        wd = [RRule.MO, RRule.WE, RRule.FR];
    }else if (opt.options==4){
        opt.each_period=1;
        wd = [RRule.TU, RRule.TH];
    }else if (opt.options==5){
        if(oneToTrue(opt.day_L)){
            wd.push(RRule.MO);
        }
        if(oneToTrue(opt.day_M)){
            wd.push(RRule.TU);
        }
        if(oneToTrue(opt.day_X)){
            wd.push(RRule.WE);
        }
        if(oneToTrue(opt.day_J)){
            wd.push(RRule.TH);
        }
        if(oneToTrue(opt.day_V)){
            wd.push(RRule.FR);
        }
        if(oneToTrue(opt.day_S)){
            wd.push(RRule.SA);
        }
        if(oneToTrue(opt.day_D)){
            wd.push(RRule.SU);
        }
    }else if (opt.options==6){
        freq = RRule.MONTHLY;
        if(opt.month_week==1){
            wd = null;
            bmd = [parseInt(moment.utc(feSta).format('DD'))];
        }else if(opt.month_week==2){
            var th = -1;
            if(opt.day_position>0){
                th=opt.day_position;
            }
            var d = moment.utc(feSta).format('E');
            if (d==1){
                wd.push(RRule.MO.nth(th));
            }else if (d==2){
                wd.push(RRule.TU.nth(th));
            }else if (d==3){
                wd.push(RRule.WE.nth(th));
            }else if (d==4){
                wd.push(RRule.TH.nth(th));
            }else if (d==5){
                wd.push(RRule.FR.nth(th));
            }else if (d==6){
                wd.push(RRule.SA.nth(th));
            }else if (d==7){
                wd.push(RRule.SU.nth(th));
            }
        }
    }else if (opt.options==6){
        freq = RRule.YEARLY;
    }

    if(opt.end_day){
        var feFin = dateNewStandard(opt.end_day);
        var endDate = feEnd;

        if(moment.utc(feEnd).diff(moment.utc(feFin), 'days')>=0){
            endDate = feFin;
        }
        // Create a rule:
        var opcions = {
            freq: freq,
            dtstart: feSta,
            until: endDate,
            count: 100000,
            interval: parseInt(opt.each_period),
            byweekday: wd,
            bymonthday:bmd
        };
    }else{
        // Create a rule:
        var opcions = {
            freq: freq,
            dtstart: feSta,
            count: opt.repeat_interval,
            interval: parseInt(opt.each_period),
            byweekday: wd,
            bymonthday:bmd
        };
    }
//    var opcions =  {
//       freq: RRule.MONTHLY,
//       dtstart: new Date(2015, 3, 16, 0, 0, 0),
//       until: new Date(2015, 6, 28, 0, 0, 0),
//       count: 100000,
//       bymonthday: [16]
//    }
    var rule = new RRule(opcions);
    return rule.all();
}
var chekOption = function(){
    opcion1();
    opcion2();
    opcion3();
    opcion4();
    opcion5();
    opcion6();
    opcion7();
}
var opcion1 = function(){
    var result = "";
    if($("#sdt-repeat-op1").val()==1){
        result = "Cada ";
        var nu = $("#sdt-repeat-op2").val();
        nu = nu>1? nu+' dias': ' dia'
        result+=nu;
        result=finRepeticion(result);
        $("#sdt-restul-repeat").text(result);
    }
}
var opcion2 = function(){
    var result = "";
    if($("#sdt-repeat-op1").val()==2){
        result = "Cada semana los dias laborales";
        result=finRepeticion(result);
        $("#sdt-restul-repeat").text(result);
    }
}

var opcion3 = function(){
    var result = "";
    if($("#sdt-repeat-op1").val()==3){
        result = "Cada semana los lunes, miércoles y viernes";
        result=finRepeticion(result);
        $("#sdt-restul-repeat").text(result);
    }
}
var opcion4 = function(){
    var result = "";
    if($("#sdt-repeat-op1").val()==4){
        result = "Cada semana los martes y jueves";
        result=finRepeticion(result);
        $("#sdt-restul-repeat").text(result);
    }
}
var opcion5 = function(){
    var result = "";
    if($("#sdt-repeat-op1").val()==5){
        var nu = $("#sdt-repeat-op2").val();
        if(nu==1){
            result = "Cada semana";
        }else{
            result = "Cada "+nu+" semanas";
        }
        var result2 = " los";
        var conT = $("input[id^='op3-']:checked").length;
        if(conT==0){
            // selecionar el dia de hoy
            var diaHoy = dateNewStandard($("#op5").val());
            var d = moment.utc(diaHoy).format('E');
            if (d==1){
                $('#op3-L').prop('checked', true);
            }else if (d==2){
                $('#op3-M').prop('checked', true);
            }else if (d==3){
                $('#op3-X').prop('checked', true);
            }else if (d==4){
                $('#op3-J').prop('checked', true);
            }else if (d==5){
                $('#op3-V').prop('checked', true);
            }else if (d==6){
                $('#op3-S').prop('checked', true);
            }else if (d==7){
                $('#op3-D').prop('checked', true);
            }
        }
        var con = 0;

        if ($('input#op3-L').is(':checked')) {
            con+=1;
            result2+= ' lunes';
        }
        if ($('input#op3-M').is(':checked')) {
            con+=1;
            result2+=diasSemana(con,conT,'martes');
        }
        if ($('input#op3-X').is(':checked')) {
            con+=1;
            result2+=diasSemana(con,conT,'miercoles');
        }
        if ($('input#op3-J').is(':checked')) {
            con+=1;
            result2+=diasSemana(con,conT,'jueves');
        }
        if ($('input#op3-V').is(':checked')) {
            con+=1;
            result2+=diasSemana(con,conT,'viernes');
        }
        if(con==5){
            result2=" los dias laborales";
        }
        if ($('input#op3-S').is(':checked')) {
            con+=1;
            result2+=diasSemana(con,conT,'sabados');
        }
        if ($('input#op3-D').is(':checked')) {
            con+=1;
            result2+=diasSemana(con,conT,'domingos');
        }
        if(con==7){
            result2=" todos los dias";
        }
        result+=result2;
        result=finRepeticion(result);
        $("#sdt-restul-repeat").text(result);
    }
}

var opcion6 = function(){
    var result = "";
    if($("#sdt-repeat-op1").val()==6){
        var nu = $("#sdt-repeat-op2").val();
        if(nu==1){
            result = "Cada mes el";
        }else{
            result = "Cada "+nu+" meses el";
        }
        var nu = $("input[name='op4']:checked").val();
        if (nu=='1'){
            result+=' dia '+dayOfDate($("#op5").val());
        }else if (nu=='2'){
            result+=encontrarPosicionDia($("#op5").val());
        }
        result=finRepeticion(result);
        $("#sdt-restul-repeat").text(result);
    }
}

var opcion7 = function(){
    var result = "";
    if($("#sdt-repeat-op1").val()==7){
        result = "Cada ";
        var nu = $("#sdt-repeat-op2").val();
        if(nu==1){
            result="Anualmente";
        }else{
            result+=' '+nu+' años';
        }
        var fecha = new Date(dateFormantStandar($("#op5").val()));
        result+=' el '+ moment.utc(fecha).format('D')+' de '+ moment.utc(fecha).format('MMMM');
        result=finRepeticion(result);
        $("#sdt-restul-repeat").text(result);
    }
}

var finRepeticion = function(result){
    var nu = $("input[name='op6']:checked").val();
    if (nu=='2'){
        nu=$("#op7").val();
        if(nu==1){
            result="Una vez";
        }else{
            nu=', '+nu+ ' veces';
            result+=nu;
        }
    }
    if (nu=='3'){
        nu=', hasta el '+datePretty();
        result+=nu;
    }
    return result;
}
var encontrarPosicionDia = function(date){
    var fechaini = date.split('/');
    var newFecha = fechaini[2]+'-'+fechaini[1]+'-01';
    var oldFecha = fechaini[2]+'-'+fechaini[1]+'-'+fechaini[0];
    newFecha = moment.utc(new Date(newFecha));
    oldFecha = moment.utc(new Date(oldFecha));
    var month = fechaini[1];
    var monthNext = fechaini[1];
    var dias = [];
    var secu='';
    var nume = 0;
    while (month==monthNext){
        var index = newFecha.format('dddd');
        if(dias.indexOf(index)==-1){
            dias.push(index);
            dias[index]=0;
        }
        dias[index]+=1;
        if(oldFecha.diff(newFecha, 'days')==0){
            secu = ' '+secuenciaDia(dias[oldFecha.format('dddd')])+ ' '+oldFecha.format('dddd');
            nume=dias[index];
        }
        newFecha = newFecha.add('days', 1);
        monthNext = newFecha.format('MM');
    }
    index = oldFecha.format('dddd');
    if(nume==dias[index]){
        posth=0;
        return ' ultimo '+index;
    }else{
        posth=nume;
        return secu;
    }

}
var secuenciaDia = function(num){
    var arr = new Array();
    arr[1]='primer';
    arr[2]='segundo';
    arr[3]='tercer';
    arr[4]='cuarto';
    arr[5]='ultimo';

    return arr[num];
}
var diasSemana = function(con,conT, dia){
    if(con==1){
        return ' '+dia;
    }else if(con==conT){
        return ' y '+dia;
    }else{
        return ', '+dia;
    }
}
var changeDateEnd=function(){
    var today = $("#op5").val();
    var todayStandard = dateFormantStandar(today);
    var todayPretty = moment.utc(new Date(todayStandard)).add('days', 7).format('DD/MM/YYYY');
    $("#op8").val(todayPretty);
}
var datePretty=function(){
    var todayStandard = new Date(dateFormantStandar($("#op8").val()));
    return moment.utc(todayStandard).format('D')+' de '+moment.utc(todayStandard).format('MMMM')+' del '+moment.utc(todayStandard).format('YYYY');
}

var dayOfDate = function(date){
    var todayStandard = dateFormantStandar(date);
    return moment.utc(new Date(todayStandard)).format('D');
}

var giveConfig = function(opt){
    $("#sdt-repeat-op1").val(opt.options); // primer select
    $("#sdt-repeat-op2").val(opt.each_period); // segundo select

    $('#op3-L').prop('checked', oneToTrue(opt.day_L));
    $('#op3-M').prop('checked', oneToTrue(opt.day_M));
    $('#op3-X').prop('checked', oneToTrue(opt.day_X));
    $('#op3-J').prop('checked', oneToTrue(opt.day_J));
    $('#op3-V').prop('checked', oneToTrue(opt.day_V));
    $('#op3-S').prop('checked', oneToTrue(opt.day_S));
    $('#op3-D').prop('checked', oneToTrue(opt.day_D));
    if(opt.month_week==1){
        $("#op4-1").prop('checked', true);
    }else if(opt.month_week==2){
        $("#op4-2").prop('checked', true);
    }
    $("#op5").val(standardToNormal(opt.start_day)); // fecha inicio

    $("#op7").val(opt.repeat_interval); // repeticiones
    $("#op8").val(standardToNormal(opt.end_day)); // fecha final
    posth =  opt.day_position;

    if(opt.N_R_T==1){
        $("#op6-1").prop('checked', true);
        $("#op7").val("");
        $("#op8").val("");
        $("#op7").attr("disabled", true);
        $("#op8").attr("disabled", true);
    }else if(opt.N_R_T==2){
        $("#op6-2").prop('checked', true);
        $("#op8").val("");
        $("#op7").attr("disabled", false);
        $("#op8").attr("disabled", true);
    }else if(opt.N_R_T==3){
        $("#op6-3").prop('checked', true);
        $("#op7").val("");
        $("#op7").attr("disabled", true);
        $("#op8").attr("disabled", false);
    }

    changeMainOption();
}

var oneToTrue = function(val){
    if(val==1){
        return true;
    }else{
        return false;
    }
}

var configRepeatDefault = function(){
    $("#sdt-repeat-op1 option[value=1]").attr("selected",true); // primer select
    $("#sdt-repeat-op2").val(1); // segundo select

    $('#op3-L').prop('checked', false);
    $('#op3-M').prop('checked', false);
    $('#op3-X').prop('checked', false);
    $('#op3-J').prop('checked', false);
    $('#op3-V').prop('checked', false);
    $('#op3-S').prop('checked', false);
    $('#op3-D').prop('checked', false);

    $("#op4-1").prop('checked', true);
    $("#op4-2").prop('checked', false);

    $("#op7").val(""); // repeticiones
    $("#op8").val(""); // fecha final
    posth =  0;

    $("#op6-1").prop('checked', true);
    $("#op7").val("");
    $("#op8").val("");

    changeMainOption();
}

var changeMainOption = function(){
    if($("#sdt-repeat-op1").val()==1){
        $("#sdt-visible-op2").css("display", "");
        $("#sdt-visible-op3").css("display", "none");
        $("#sdt-visible-op4").css("display", "none");
        $("#sdt-text-op2").text("dias");
    }else if($("#sdt-repeat-op1").val()==2){
        $("#sdt-visible-op2").css("display", "none");
        $("#sdt-visible-op3").css("display", "none");
        $("#sdt-visible-op4").css("display", "none");
    }else if($("#sdt-repeat-op1").val()==3){
        $("#sdt-visible-op2").css("display", "none");
        $("#sdt-visible-op3").css("display", "none");
        $("#sdt-visible-op4").css("display", "none");
    }else if($("#sdt-repeat-op1").val()==4){
        $("#sdt-visible-op2").css("display", "none");
        $("#sdt-visible-op3").css("display", "none");
        $("#sdt-visible-op4").css("display", "none");
    }else if($("#sdt-repeat-op1").val()==5){
        $("#sdt-visible-op2").css("display", "");
        $("#sdt-visible-op3").css("display", "");
        $("#sdt-visible-op4").css("display", "none");
        $("#sdt-text-op2").text("semanas");
    } else if($("#sdt-repeat-op1").val()==6){
        $("#sdt-visible-op2").css("display", "");
        $("#sdt-visible-op3").css("display", "none");
        $("#sdt-visible-op4").css("display", "");
        $("#sdt-text-op2").text("meses");
    } else if($("#sdt-repeat-op1").val()==7){
        $("#sdt-visible-op2").css("display", "");
        $("#sdt-visible-op3").css("display", "none");
        $("#sdt-visible-op4").css("display", "none");
        $("#sdt-text-op2").text("años");
    }
    chekOption();
}

var getHTDrepeatUltimate = function(dato,id_temp,day){
    opt.unid_time_id    =   id_temp;
    opt.task_id         =   dato.tasks.id;
    opt.options         =   dato.tasksRepeats.options; // primer select
    opt.each_period     =   parseInt(dato.tasksRepeats.each_period); // segundo select
    opt.day_L           =   dato.tasksRepeats.day_L; // Lunes
    opt.day_M           =   dato.tasksRepeats.day_M; // Martes
    opt.day_X           =   dato.tasksRepeats.day_X; // Miercoles
    opt.day_J           =   dato.tasksRepeats.day_J; // Jueves
    opt.day_V           =   dato.tasksRepeats.day_V; // Viernes
    opt.day_S           =   dato.tasksRepeats.day_S; // Sabado
    opt.day_D           =   dato.tasksRepeats.day_D; // Domingo
    opt.month_week      =   dato.tasksRepeats.month_week; // 1: dias del mes, 2: dia de la semana
    opt.start_day       =   dato.tasksRepeats.start_day; // fecha inicio
    opt.N_R_T           =   parseInt(dato.tasksRepeats.N_R_T); // 1: Nunca, 2: repeticiones, 3: hasta
    opt.repeat_interval =   parseInt(dato.tasksRepeats.repeat_interval); // repeticiones
    // debo encontrar la fecha de la siguiente accion
    opt.end_day = moment.utc(day).format('YYYY-MM-DD');
    var results = createRecurrentEvent(opt,dateNewStandard(opt.start_day),day,day);
    return results;
}
var createHtmlTaskRepat = function(obj){
    $(".sdt-task-repeat").html('');
    var html =  '<form class="form-horizontal">'+
                '<div class="form-group">'+
                    '<input type="hidden" id="sdt-repeat-dia" value="0"/>'+
                    '<input type="hidden" id="sdt-repeat-id_td" value="0"/>'+
                    '<label for="sdt-repeat-op1" class="col-sm-3 control-label">Se repite:</label>'+
                    '<div class="col-sm-8">'+
                        '<select class="form-control" id="sdt-repeat-op1" name="op1">'+
                            '<option value="1" >Cada día</option>'+
                            '<option value="2" >Todos los días laborales (de lunes a viernes)</option>'+
                            '<option value="3" >Todos los lunes, miércoles y viernes</option>'+
                            '<option value="4" >Todos los martes y jueves</option>'+
                            '<option value="5" >Cada semana</option>'+
                            '<option value="6" >Cada mes</option>'+
                            '<option value="7" >Cada año</option>'+
                        '</select>'+
                    '</div>'+
                '</div>'+
                '<div class="form-group" id="sdt-visible-op2">'+
                    '<label for="sdt-repeat-op2" class="col-sm-3 control-label">Repetir cada:</label>'+
                    '<div class="col-sm-2">'+
                        '<input class="form-control" type="number" min="1" id="sdt-repeat-op2" name="op2" value="1">'+
                    '</div>'+
                    '<div class="col-sm-1">'+
                        '<p class="form-control-static" id="sdt-text-op2">días</p>'+
                    '</div>'+
                '</div>'+
                '<div class="form-group" id="sdt-visible-op3" style="display: none">'+
                    '<label for="sdt-repeat-op4" class="col-sm-3 control-label">Repetir el:</label>'+
                    '<div class="col-sm-9">'+
                        '<label class="checkbox-inline">'+
                            '<input type="checkbox" id="op3-L" name="op3-L" value="L"> L'+
                        '</label>'+
                        '<label class="checkbox-inline">'+
                            '<input type="checkbox" id="op3-M" name="op3-M" value="M"> M'+
                        '</label>'+
                        '<label class="checkbox-inline">'+
                            '<input type="checkbox" id="op3-X" name="op3-X" value="X"> X'+
                        '</label>'+
                        '<label class="checkbox-inline">'+
                            '<input type="checkbox" id="op3-J" name="op3-J" value="J"> J'+
                        '</label>'+
                        '<label class="checkbox-inline">'+
                            '<input type="checkbox" id="op3-V" name="op3-V" value="V"> V'+
                        '</label>'+
                        '<label class="checkbox-inline">'+
                            '<input type="checkbox" id="op3-S" name="op3-S" value="S"> S'+
                        '</label>'+
                        '<label class="checkbox-inline">'+
                            '<input type="checkbox" id="op3-D" name="op3-D" value="D"> D'+
                        '</label>'+
                    '</div>'+
                '</div>'+
                '<div class="form-group" id="sdt-visible-op4" style="display: none">'+
                    '<label for="sdt-repeat-op4" class="col-sm-3 control-label">Repetir cada:</label>'+
                    '<div class="col-sm-9">'+
                        '<label class="radio-inline">'+
                            '<input type="radio" name="op4" id="op4-1" value="1" checked> día del mes'+
                        '</label>'+
                        '<label class="radio-inline">'+
                            '<input type="radio" name="op4" id="op4-2" value="2"> día de la semana'+
                        '</label>'+
                    '</div>'+
                '</div>'+
                '<div class="form-group">'+
                    '<label for="sdt-repeat-op4" class="col-sm-3 control-label">Empezar el:</label>'+
                    '<div class="col-sm-8">'+
                        '<input type="text" class="form-control datepicker" id="op5" name="op5" placeholder="" value="" disabled>'+
                    '</div>'+
                '</div>'+
                '<div class="form-group">'+
                    '<label for="sdt-repeat-op5" class="col-sm-3 control-label">Finaliza:</label>'+
                    '<div class="col-sm-9">'+
                        '<div class="radio">'+
                            '<label>'+
                                '<input type="radio" name="op6" id="op6-1" value="1" checked>'+
                                'Nunca'+
                            '</label>'+
                        '</div>'+
                        '<div class="radio">'+
                            '<label>'+
                                '<input type="radio" name="op6" id="op6-2" value="2">'+
                                'Después de '+
                                '<input type="number" min="1" id="op7" name="op7" placeholder="" disabled> '+
                                'repeticiones'+
                            '</label>'+
                        '</div>'+
                        '<div class="radio">'+
                            '<label>'+
                                '<input type="radio" name="op6" id="op6-3" value="3" aria-label="333">'+
                                'El '+
                                '<input type="text" id="op8" name="op8" placeholder="" class="datepicker" disabled>'+
                            '</label>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="form-group">'+
                    '<label for="sdt-op9" class="col-sm-3 control-label">Aplicar cambios:</label>'+
                    '<div class="col-sm-9">'+
                        '<div class="radio">'+
                            '<label>'+
                                '<input type="radio" name="op9" id="op9-1" value="1" checked>'+
                                'Conservando datos siguientes'+
                            '</label>'+
                        '</div>'+
                        '<div class="radio">'+
                            '<label>'+
                                '<input type="radio" name="op9" id="op9-2" value="2">'+
                                'Construir todo apartir de aqui'+
                            '</label>'+
                        '</div>'+
                    '</div>'+
                '<div class="form-group">'+
                    '<label for="sdt-repeat-op4" class="col-sm-3 control-label">Resumen : </label>'+
                        '<div class="col-sm-9">'+
                            '<p class="form-control-static" id="sdt-restul-repeat">Cada semana los jueves</p>'+
                        '</div>'+
                    '</div>'+
                    '<div class="form-group">'+
                        '<div class="col-sm-12">'+
                            '<button type="button" class="btn btn-default btn-sm btn-block" id="sdt-repeat-submit">Listo</button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</form>';

    $(obj).html(html);
    opt = new Object();
    $( "#op8" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy'
    });
    $( "#datepicker" ).datepicker({ minDate: "-1D", maxDate: "+1M +10D" });
    initTaskRepeat();
}