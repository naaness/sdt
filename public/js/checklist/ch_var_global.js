/**
 * Created by nesto_000 on 15/04/15.
 */
var server = "http://"+window.location.hostname;
var fechaIni = null; // fecha inicial de la vista
var type_view = 'normal';
var domainbody = null;
var setRangeStart = function(dat){
    fechaIni = dat;
    ntoday = moment(fechaTod).diff(moment.utc(fechaIni), 'days');
    day_week = moment(fechaIni).format('e');
    unitTimes = new Array(); // conservar una relacion bidireccional
    timesUnit = new Array();
    tasksRepat = new Array()
    availableCHecklistEdit();
}

var chEdit = false;
var day_week = false;
var availableCHecklistEdit = function(){
    chEdit=false;
    var pathname = window.location.pathname;
    pathname = pathname.split('/');
    if(pathname[5]){
        if(pathname[5]>0){
            chEdit=true;
        }
    }
}
var fechaFin = null; // fecha final de la vista
var unitTimes = new Array();
var timesUnit = new Array();
var tasksRepat = new Array();
var setRangeEnd = function(dat){
    fechaFin = dat;
}
var ch_user_id = null; // fecha final de la vista
var setUserId = function(dat){
    ch_user_id = dat;
}
var fechaCol = null; // fecha de la unidad de tiempo seleccionada
var fechaTod = moment(new Date()).format('YYYY-MM-DD'); // fecha del dia de hoy en formato Standard
var fechaTod = dateNewStandard(fechaTod); // fecha del dia de hoy en formato Standard
var ntoday   = null; // diferencia entre la fecha inicial del rango y la fecha de hoy.
var ndiff    = null;
var setNDiff = function(dat){
    ndiff = dat;
}
var top_table = null;
var segui =  {
    icon:{
        1:'glyphicon glyphicon-minus',
        2:'glyphicon glyphicon-ok',
        3:'glyphicon glyphicon-remove',
        4:'glyphicon glyphicon-arrow-right'
    },
    class_u:{
        future:{
            1:'vacio',
            2:'chuleado',
            3:'nohizo',
            4:'transferido'
        },
        past:{
            1:'vaciolight',
            2:'chuleadolight',
            3:'nohizolight',
            4:'transferidolight'
        }
    }
};

var class_follow_up =  {
    1:'ch_p_hight',
    2:'ch_p_normal',
    3:'ch_p_low',
    4:'ch_p_informative'
};
var state_task = {
    1:'task-accepted',
    2:'task-wait',
    3:'task-rejected'
}
var last_popover = null ;
var last_unit_move = null ;
var last_uniTime_move = null ;
var click_uniTime_move = 0 ;
var zoom_day = null;