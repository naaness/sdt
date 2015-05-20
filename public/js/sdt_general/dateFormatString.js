/**
 * Created by nesto_000 on 11/04/15.
 */
// traducir la fecha
moment.lang('es', {
    months: 'enero_febrero_marzo_abril_mayo_junio_julio_agosto_septiembre_octubre_noviembre_dicembre'.split('_'),
    monthsShort: 'ene._feb._mar._abr._may._jun_jul._ago._sept._oct._nov._dic.'.split('_'),
    weekdays: 'domingo_lunes_martes_miércoles_jueves_viernes_sábado'.split('_'),
    weekdaysShort: 'dom._lun._mar._mie._jeu._vie._sab.'.split('_'),
    weekdaysMin: 'Do_Lu_Ma_Mi_Ju_Vi_Sa'.split('_')
});
moment.lang('es');

var dateToString = function(date_s){
    var string= moment.utc(new Date(dateFormantStandar(date_s))).format('dddd, D MMMM YYYY');
    return string.charAt(0).toUpperCase() + string.slice(1);
}

var dateFormantStandar = function(strg){
    var stringDate = strg;
    if(strg.indexOf('/')>-1){
        stringDate = strg.split('/');
        stringDate = stringDate[2]+'-'+stringDate[1]+'-'+(stringDate[0]);
    }
    return stringDate;
}

var dateNew = function(date_s){
    date_s = date_s.split('/');
    return new Date(date_s[2], date_s[1]-1, date_s[0],0,0,0);
}
var dateNewStandard = function(date_s){
    if(date_s.indexOf('/')>-1){
        date_s = date_s.split('/');
        return new Date(date_s[2], date_s[1]-1, date_s[0],0,0,0);
    }else{
        date_s = date_s.split('-');
        return new Date(date_s[0], date_s[1]-1, date_s[2],0,0,0);
    }
}
var standardToNormal = function (date_s){
    date_s = date_s.split('-');
    return date_s[2]+'/'+date_s[1]+'/'+date_s[0];
}
var dateOperate = function(date_s,days){
    return string= moment.utc(new Date(dateFormantStandar(date_s))).add('days', days).format('DD/MM/YYYY');
}

var diffDates = function (date_start, date_end){
    date_start = moment(date_start);
    date_end = moment(date_end);
    return date_end.diff(date_start, 'days');
}