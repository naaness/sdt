/**
 * Created by nesto_000 on 16/05/15.
 */
var SyncDays            =   -1;
var SyncdayChanges      =   [];
var Syncreg_local_cha   =   [];
var SyncC_delete        =   -1;
var SyncC_update        =   -1;
var SyncC_create        =   -1;
var Syncrms             =   [];
var Sycnrms_l           =   [];
var Sycnrms_l_pos       =   [];
var SyncCan             =   false;
var Sycntemp_id         =   false;
var SynnTime            =   null;
var SyncLineSecure      =   false;
var SyncSecrect         =   null;
console.log('aqui')
var startLineSecure = function(){
    if(!SyncLineSecure){


        SyncSecrect         =   $.jCryption.encrypt(createRandomString(), createRandomString(10));
        var _getKeysURL     = 'http://' + window.location.hostname+'/login/getPublicKey';
        var _handshakeURL   = 'http://' + window.location.hostname+'/login/handshake';
        $.jCryption.authenticate(SyncSecrect, _getKeysURL, _handshakeURL, function(AESKey) {
            console.log('Linea segura')
            SyncLineSecure = true;
            rm_go_Today();
        }, function() {
            console.log('Linea NO segura')
            SyncLineSecure = false;
            rm_go_Today();
        });
    }
}
var createRandomString = function(long){
    if(!long){
        long=5;
    }
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for( var i=0; i < long; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

var stopSycn =  function(){
    clearInterval(SynnTime);
}
var resetSycn = function(){
    clearInterval(SynnTime);
    SynnTime            =   setInterval(function () {initSyncRm()},3000);
}
var initSyncRm = function(){
    if(SyncCan && OnLineSync && SyncLineSecure){
        SyncAnimationOn();
        SyncCan=false;
        SyncDays            =   -1;
        SyncdayChanges      =   getDaysChanged();
        SyncDaysProcess();
    }
}
var SyncDaysProcess = function(){
    SyncDays+=1;
    if(SyncDays<SyncdayChanges.length){
        console.log('Sincornizando dia : '+SyncdayChanges[SyncDays])
        Syncreg_local_cha   =   getLocalRegistriesChange(SyncdayChanges[SyncDays]);
        if(Syncreg_local_cha.hasOwnProperty('delete')){
            SyncC_delete        =   -1;
            SyncDataUpdateCreate();
            SyncdeleteRegistries();
        }else{
            var daysSync    =   getDaysChanged();
            var index_days  =  searchValue(daysSync,SyncdayChanges[SyncDays]);
            if(index_days>-1){
                daysSync.splice(index_days, 1);
            }
//            $.localStorage.setItem('sdt_days_changed', JSON.stringify(daysSync));
            els.set('sdt_days_changed',daysSync);
            SyncDays-1;
            SyncDaysProcess();
        }
    }else{
        SyncAnimationOff();
        SyncCan=true;
    }
}
var SyncDataUpdateCreate = function(){
    Syncrms                             =   getLocalRegistries(SyncdayChanges[SyncDays]);
    Sycnrms_l                           =   [];
    Sycnrms_l_pos                       =   [];
    for (var i = 0; i < Syncrms.length; i++) {
        Syncrms[i].registry             =   decodeContent(Syncrms[i].registry);
        Sycnrms_l[Syncrms[i].id]        =   Syncrms[i];
        Sycnrms_l_pos[Syncrms[i].id]    =   i;
    }
}
var SyncdeleteRegistries = function(){
    SyncC_delete+=1;
    if(SyncC_delete<Syncreg_local_cha.delete.length){
        var encryptedString = $.jCryption.encrypt('id=' + Syncreg_local_cha.delete[SyncC_delete].id+'&status=0', SyncSecrect);

        $.ajax({
            url: server + '/rmregistries/updateRegistry',
            dataType: "json",
            type: "POST",
            data: {
                jCryption: encryptedString
            },
            success: function(datos) {
                console.log('Registro eliminado : '+Syncreg_local_cha.delete[SyncC_delete].id);
                if(rm_goday!=SyncdayChanges[SyncDays]){
                    Syncreg_local_cha.delete.splice(SyncC_delete, 1);
                    SyncC_delete-=1;
                    updateChangesSync();
                }else{
                    reg_local_cha = getLocalRegistriesChange();
                    reg_local_del = reg_local_cha.delete;
                    for (var i = 0; i < reg_local_del.length; i++) {
                        if(reg_local_del[i].id==Syncreg_local_cha.delete[SyncC_delete].id){
                            reg_local_del.splice(i,1);
                            i=reg_local_del.length+1;
                        }
                    }
                    setLocalRegistriesChange();
                }
                SyncdeleteRegistries();
            },
            fail: function(){
                StopSycnRm();
            }
        });
    }else{
        SyncC_update        =   -1;
        SyncupdateRegistries();
    }
}
var SyncupdateRegistries = function(){
    SyncC_update+=1
    if(SyncC_update<Syncreg_local_cha.update.length){
        var parameters      =   '';
        var SycnTemp        =   Syncreg_local_cha.update[SyncC_update].changes
        var Sycntemp_id     =   Syncreg_local_cha.update[SyncC_update].id;
        if(rm_goday!=SyncdayChanges[SyncDays] || registries.length==0){

            for (var i = 0; i < Syncreg_local_cha.update.length; i++) {
                if(SycnTemp[i]=='numbering'){
                    parameters+='&'+SycnTemp[i]+'='+Sycnrms_l[Sycntemp_id].numbering;
                }
                if(SycnTemp[i]=='order_r'){
                    parameters+='&'+SycnTemp[i]+'='+Sycnrms_l[Sycntemp_id].order_r;
                }
                if(SycnTemp[i]=='acordion'){
                    parameters+='&'+SycnTemp[i]+'='+Sycnrms_l[Sycntemp_id].acordion;
                }
                if(SycnTemp[i]=='rm_label_id'){
                    parameters+='&'+SycnTemp[i]+'='+Sycnrms_l[Sycntemp_id].rm_label_id;
                }
                if(SycnTemp[i]=='registry'){
                    parameters+='&'+SycnTemp[i]+'='+Sycnrms_l[Sycntemp_id].registry;
                }
                if(SycnTemp[i]=='checked'){
                    parameters+='&'+SycnTemp[i]+'='+Sycnrms_l[Sycntemp_id].checked;
                }
            }
        }else{
            for (var i = 0; i < Syncreg_local_cha.update.length; i++) {
                if(SycnTemp[i]=='numbering'){
                    parameters+='&'+SycnTemp[i]+'='+registries[Sycntemp_id].numbering;
                }
                if(SycnTemp[i]=='order_r'){
                    parameters+='&'+SycnTemp[i]+'='+registries[Sycntemp_id].order_r;
                }
                if(SycnTemp[i]=='acordion'){
                    parameters+='&'+SycnTemp[i]+'='+registries[Sycntemp_id].acordion;
                }
                if(SycnTemp[i]=='rm_label_id'){
                    parameters+='&'+SycnTemp[i]+'='+registries[Sycntemp_id].rm_label_id;
                }
                if(SycnTemp[i]=='registry'){
                    parameters+='&'+SycnTemp[i]+'='+registries[Sycntemp_id].registry;
                }
                if(SycnTemp[i]=='checked'){
                    parameters+='&'+SycnTemp[i]+'='+registries[Sycntemp_id].checked;
                }
            }
        }

        var encryptedString = $.jCryption.encrypt('id=' + Sycntemp_id +parameters, SyncSecrect);

        $.ajax({
            url: server + '/rmregistries/updateRegistry',
            dataType: "json",
            type: "POST",
            data: {
                jCryption: encryptedString
            },
            success: function(response) {
                console.log('Registro Actualizado : '+Sycnrms_l[Sycntemp_id].id);
                if(rm_goday!=SyncdayChanges[SyncDays]){
                    Syncreg_local_cha.update.splice(SyncC_update, 1);
                    SyncC_update-=1;
                    updateChangesSync();
                }else{
                    registries_old[Sycnrms_l[Sycntemp_id].id]   =   JSON.stringify(registries[Sycnrms_l[Sycntemp_id].id]);
                    registries_old[Sycnrms_l[Sycntemp_id].id]   =   JSON.parse(registries_old[Sycnrms_l[Sycntemp_id].id]);
                    setLocalRegistriesChange();
                }
                SyncupdateRegistries();
            },
            fail: function(){
                StopSycnRm();
            }
        });
    }else{
        SyncC_create        =   -1;
        SynccreateRegistries();
    }
}

var SynccreateRegistries = function(){
    SyncC_create+=1;
    if(SyncC_create<Syncreg_local_cha.news.length){
        Sycntemp_id         =   Syncreg_local_cha.news[SyncC_create];
        if(Sycnrms_l[Sycntemp_id]){
            var parameters      =   '';
            parameters         +=   'date='+SyncdayChanges[SyncDays];
            parameters         +=   '&order_r='+Sycnrms_l[Sycntemp_id].order_r;
            parameters         +=   '&acordion='+Sycnrms_l[Sycntemp_id].acordion;
            parameters         +=   '&numbering='+Sycnrms_l[Sycntemp_id].numbering;
            parameters         +=   '&rm_label_id='+Sycnrms_l[Sycntemp_id].rm_label_id;
            parameters         +=   '&checked='+Sycnrms_l[Sycntemp_id].checked;
            parameters         +=   '&registry='+Sycnrms_l[Sycntemp_id].registry;

            var encryptedString = $.jCryption.encrypt(parameters, SyncSecrect);

            $.ajax({
                url: server + '/rmregistries/createRegistry',
                dataType: "json",
                type: "POST",
                data: {
                    jCryption: encryptedString
                },
                success: function(datos) {
                    if(datos!="None"){
                        // Eliminar de la lista los datos eliminados
                        console.log('Registro Creado '+Sycnrms_l[Sycntemp_id].id);
                        if(rm_goday!=SyncdayChanges[SyncDays]){
                            Syncrms[Sycnrms_l_pos[Sycntemp_id]].id                  =   datos;
                            Sycnrms_l_pos[datos]                                    =   Sycnrms_l_pos[Sycntemp_id];

//                        $.localStorage.setItem('sdt_r_'+SyncdayChanges[SyncDays], JSON.stringify(Syncrms));
                            els.set('sdt_r_'+SyncdayChanges[SyncDays],Syncrms);
                            Syncreg_local_cha.news.splice(SyncC_create, 1);
                            SyncC_create-=1;
                            updateChangesSync();
                        }else{
                            $("#tr_"+Sycnrms_l[Sycntemp_id].id).attr("id","tr_"+datos);
                            registries[Sycnrms_l[Sycntemp_id].id].id        =   datos;
                            registries[datos]                               =   JSON.parse(JSON.stringify(registries[Sycnrms_l[Sycntemp_id].id]));
                            registries_old[datos]                           =   JSON.parse(JSON.stringify(registries[Sycnrms_l[Sycntemp_id].id]));
                            var arrTem = [];
                            for (var indice in registries){
                                if(indice!=Sycntemp_id){
                                    arrTem[indice]=registries[indice]
                                }
                            }
                            registries = arrTem;
                            sanityNumbering();
                            setLocalRegistries();
                        }
                        SynccreateRegistries();
                    }
                },
                fail: function(){
                    StopSycnRm();
                }
            });
        }else{
            SynccreateRegistries();
        }
    }else{
        //Quitar el dia de actualizacion
        SyncDaysProcess();
    }
}

var SyncAnimationOn = function(){
    $("#sync-rm").attr('class','btn btn-success');
    $("#sync-rm").find('span').addClass('rm-sync');
}
var SyncAnimationOff = function(){
    $("#sync-rm").attr('class','btn btn-default');
    $("#sync-rm").find('span').removeClass('rm-sync');
}
var StopSycnRm = function(){
    SyncDays            =   SyncdayChanges.length;
    SyncC_delete        =   Syncreg_local_cha.delete.length;
    SyncC_update        =   Syncreg_local_cha.update.length;
    SyncC_create        =   Syncreg_local_cha.news.length;
    SyncAnimationOff();
    SyncCan             =   true;
    SyncDaysProcess();
}
var updateChangesSync = function(){
    var dataSync = null;
    if(Syncreg_local_cha.delete.length>0 || Syncreg_local_cha.update.length>0 || Syncreg_local_cha.news.length>0){
        dataSync=Syncreg_local_cha;
    }
    var obja        =   dataSync;
    var daysSync    =   getDaysChanged();
    var index_days  =  searchValue(daysSync,SyncdayChanges[SyncDays]);
    if(obja!=null){
//        $.localStorage.setItem('sdt_r_changes'+SyncdayChanges[SyncDays], JSON.stringify(obja));
        els.set('sdt_r_changes'+SyncdayChanges[SyncDays], obja);
        if(index_days==-1){
            daysSync.push(SyncdayChanges[SyncDays]);
        }
    }else{
        $.localStorage.removeItem('sdt_r_changes'+SyncdayChanges[SyncDays]);
//        els.remove('sdt_r_changes'+SyncdayChanges[SyncDays]);
        if(index_days>-1){
            daysSync.splice(index_days, 1);
        }
    }
//    $.localStorage.setItem('sdt_days_changed', JSON.stringify(daysSync));
    els.set('sdt_days_changed',daysSync);
}
var searchValue = function(arrayEl,value){
    for(var sy=0;sy<=arrayEl.length;sy++){
        if(arrayEl[sy]==value){
            return sy;
        }
    }
    return -1;
}