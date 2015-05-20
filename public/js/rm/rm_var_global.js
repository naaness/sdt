/**
 * Created by nesto_000 on 9/05/15.
 */
var server = "http://" + window.location.hostname;
var rm_today = new Date();
var rm_goday = null;
var fechaG = null;
var time_cal = null;
var labels = null;
var registries = [];
var registries_old = [];
var reg_local = [];
var reg_local_del = [];
var local_reg = [];
var ch_tr_id = null;
var ch_tr_id_temp = null;
var rm_last_popover = null;
var labelsHtml = null;
var dayChanges = [];
var time_sycn = null;
var OnLineSync  = true;