/**
 * Created by nesto_000 on 18/05/15.
 */
var EncryptedLocalStorage = (function(global) {
    'use strict';

// Constructor
    var EncryptedLocalStorage = function(secret) {
            if (!secret) {
                throw 'Missing secret!';
            }

            this.secret = secret;
        },

    // Private methods
        encrypt = function(object, secret) {
            var message = JSON.stringify(object);
            return CryptoJS.TripleDES.encrypt(message, secret);
        },

        decrypt = function(encrypted, secret) {
            var decrypted = CryptoJS.TripleDES.decrypt(encrypted, secret);
            return JSON.parse(decrypted.toString(CryptoJS.enc.Utf8));
        };

    // Public API
    EncryptedLocalStorage.prototype = {

        get: function(key) {
            var encrypted = global.localStorage.getItem(key);
            return encrypted && decrypt(encrypted, this.secret);
        },

        set: function(key, object) {
            if (!object) {
                this.remove(key);
                return;
            }

            var encrypted = encrypt(object, this.secret);
            global.localStorage.setItem(key, encrypted);
        },

        remove: function(key) {
            global.localStorage.removeItem(key);
        }
    };

    return EncryptedLocalStorage;

}(window));


// Usage
var els = new EncryptedLocalStorage(s_c_j);
//els.set('item', { number: 123 });
//
//console.log('encrypted', window.localStorage.getItem('item'));
//console.log('decrypted', els.get('item'));
//
//els.remove('key');
//console.log('Removed from localstorage');

var getDaysChanged = function(){
    return getLocalS('sdt_days_changed');
}
var getLocalLabels = function(){
    return getLocalS('sdt_l');
}
var getLocalRegistries = function(todayRm){
    if(!todayRm){
        todayRm=rm_goday;
    }
    return getLocalS('sdt_r_'+todayRm);
}
var getLocalRegistriesChange = function(todayRm){
    if(!todayRm){
        todayRm=rm_goday;
    }
    return getLocalS('sdt_r_changes'+todayRm);
}
var getLocalS = function(key){
    var datos = els.get(key);
    if(datos==null){
        datos=[];
    }
    return datos;
//    var datos  = $.localStorage.getItem(key);
//    if(datos){
//        datos = JSON.parse(datos);
//    }else{
//        datos = [];
//    }
//    return datos;
}
var setLocalRegistries = function(){
//    $.localStorage.setItem('sdt_r_'+rm_goday, JSON.stringify(reg_local));
    els.set('sdt_r_'+rm_goday, reg_local);
    setLocalRegistriesChange();
    resetSycn();
}
var setLocalRegistriesChange = function(){
    var obja = createChangesRm();
    var index_days = $.inArray(rm_goday, dayChanges);
    if(obja!=null){
//        $.localStorage.setItem('sdt_r_changes'+rm_goday, JSON.stringify(obja));
        els.set('sdt_r_changes'+rm_goday, obja);
        if(index_days==-1){
            dayChanges.push(rm_goday);
        }
    }else{
        $.localStorage.removeItem('sdt_r_changes'+rm_goday);
//        els.remove('sdt_r_changes'+rm_goday);
        if(index_days>-1){
            dayChanges.splice(index_days, 1);
        }
    }
//    $.localStorage.setItem('sdt_days_changed', JSON.stringify(dayChanges));
    els.set('sdt_days_changed', dayChanges);
}

var createChangesRm = function(){
    // eliminar registros asd
    var changesRm = new Object();
    changesRm.time_sync = time_sycn;
    changesRm.delete    = [];
    changesRm.update    = [];
    changesRm.news      = [];
    var changed = false;
    for (var i = 0; i < reg_local_del.length; i++) {
        var objd        =   new Object();
        objd.id         =   reg_local_del[i].id;
        changesRm.delete.push(objd);
        changed=true;
    }
    for (var i = 0; i < reg_local.length; i++) {
        if(reg_local[i].id.indexOf('rm')==-1){
            var update = seeChangesRegistry(reg_local[i].id);
            if(update!=null){
                //console.log(reg_local[i].id+' '+update)
                changesRm.update.push(update); //{id:#,changes:[registry,order_r...]}
                changed=true;
            }
        }
    }
    for (var i = 0; i < reg_local.length; i++) {
        if(reg_local[i].id.indexOf('rm')>-1){
            //console.log(reg_local[i].id)
            var objd        =   new Object();
            objd.id         =   reg_local[i].id;
            changesRm.news.push(objd.id);
            changed=true;
        }
    }
    if(changed){
        console.log('changes : Yes')
        return changesRm;
    }else{
        console.log('changes : No');
        return null;
    }
}
var seeChangesRegistry = function(id_tr){
    var update = null;
    if(registries_old[id_tr]){
        if(registries_old[id_tr].numbering!=registries[id_tr].numbering){
//            update +=   '&numbering='+registries[id_tr].numbering;
            console.log(id_tr+' numbering');
            update = createObjChange(update,id_tr);
            update.changes.push('numbering');
        }
        if(registries_old[id_tr].order_r!=registries[id_tr].order_r){
//            update +=   '&order_r='+registries[id_tr].order_r;
            console.log(id_tr+' order_r');
            update = createObjChange(update,id_tr);
            update.changes.push('order_r');
        }
        if(registries_old[id_tr].acordion!=registries[id_tr].acordion){
//            update +=   '&acordion='+registries[id_tr].acordion;
            console.log(id_tr+' acordion');
            update = createObjChange(update,id_tr);
            update.changes.push('acordion');
        }
        if(registries_old[id_tr].rm_label_id!=registries[id_tr].rm_label_id){
//            update +=   '&rm_label_id='+registries[id_tr].rm_label_id;
            console.log(id_tr+' rm_label_id');
            update = createObjChange(update,id_tr);
            update.changes.push('rm_label_id');
        }

        if(registries_old[id_tr].registry.trim()!==registries[id_tr].registry.trim()){
//            update +=   '&registry='+registries[id_tr].registry;
            console.log(id_tr+' registry');
            update = createObjChange(update,id_tr);
            update.changes.push('registry');
        }
        if(registries_old[id_tr].checked!==registries[id_tr].checked){
//            update +=   '&checked='+registries[id_tr].checked;
            console.log(id_tr+' checked');
            update = createObjChange(update,id_tr);
            update.changes.push('checked');
        }
    }
    return update;
}
var createObjChange = function(update,id_tr){
    if(update==null){
        update          =   new Object();
        update.id       =   id_tr;
        update.changes  =   [];
    }
    return update;
}