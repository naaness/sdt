/**
 * Created by nesto_000 on 8/05/15.
 */
$(function() {
    var _getKeysURL     = 'http://' + window.location.hostname+'/login/getPublicKey';
    var _handshakeURL   = 'http://' + window.location.hostname+'/login/handshake';
    $("#form-normal").jCryption({
        getKeysURL:_getKeysURL,
        handshakeURL:_handshakeURL
    });
});