/**
 * Created by nesto_000 on 29/04/15.
 */
var sdt_nameController      =   null;
var sdt_nameAction          =   null;
var sdt_Parameters          =   '';
var urls                    =   null;
var pos_url                 =   0;
var canClick                =   true;
var server = "http://"+window.location.hostname;
$(document).ready(function() {
    initBackNextPage();
    $("#sdt-logout").click(function(){
        $.localStorage.removeItem('sdt_urls');
    })
    if(sdt_nameController=='login'){
        $.localStorage.removeItem('sdt_urls');
        $("#sdt-back-page").prop( "disabled", true );
        $("#sdt-next-page").prop( "disabled", true );
//        $("#sdt-back-page").hide();
//        $("#sdt-next-page").hide();
    }else{
        urls = $.localStorage.getItem('sdt_urls');
        if(urls){
            urls = JSON.parse(urls);
            pos_url=urls.pos;
            if(urls.url[pos_url]!=getUrlPage()){
                pos_url+=1;
                urls.url[pos_url]=getUrlPage();
                urls.pos=pos_url;
                if(urls.url[pos_url+1]){
                    for (var i = pos_url+1; i < urls.url.length; i++) {
                        console.log('eliminando '+urls.url[pos_url+1]);
                        urls.url.splice(pos_url+1,1);
                    }
                }
                $.localStorage.setItem('sdt_urls', JSON.stringify(urls));
            }
            if(urls.pos==0){
                $("#sdt-back-page").prop( "disabled", true );
//                $("#sdt-back-page").hide();
            }else{
                $("#sdt-back-page").prop( "disabled", false );
//                $("#sdt-back-page").show();
            }

            if(urls.url.length==(pos_url+1)){
                $("#sdt-next-page").prop( "disabled", true );
//                $("#sdt-next-page").hide();
            }else{
                $("#sdt-next-page").prop( "disabled", false );
//                $("#sdt-next-page").show();
            }
        }else{
            urls        = new Object();
            urls.url    = [];
            urls.url[pos_url]=getUrlPage();
            urls.pos=pos_url;
            $.localStorage.setItem('sdt_urls', JSON.stringify(urls));
        }
    }

    $("#sdt-back-page").click(function(){
        urls.pos-=1;
        gotoUrl();
    });
    $("#sdt-next-page").click(function(){
        urls.pos+=1;
        gotoUrl();
    });
});
var gotoUrl = function(){
    if(urls.url[urls.pos]){
        $("#sdt-back-page").prop( "disabled", true );
        $("#sdt-next-page").prop( "disabled", true );
        $.localStorage.setItem('sdt_urls', JSON.stringify(urls));
        canClick=false;
        console.log(pos_url + ' ' +server+urls.url[urls.pos]);
        window.location.href = server+urls.url[urls.pos];
    }
}
var getUrlPage = function () {
    return pathname = window.location.pathname;
}
var initBackNextPage = function(){
    var url = getUrlPage();
    url = url.split('/');
    if(url[1]){
        sdt_nameController=url[1];
        if(url[2]){
            sdt_nameAction=url[2];
            sdt_Parameters='';
            if(url[3]){
                for (var i = 3; i < url.length; i++) {
                    sdt_Parameters+=url[i]+'/';
                };
                sdt_Parameters=sdt_Parameters.substring(0, sdt_Parameters.length-1);
            }
        }
    }
}