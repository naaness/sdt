var server = "http://"+window.location.hostname;
var last_asnwer=null;
$(document).ready(function(){
	$('.new-post').click(function(){

		if (last_asnwer) {
			last_asnwer.html('');
		};
		// crear un div con el contenido del post
		var html = 	'<div>'+
					'<input type="hidden" id="post-id" value="" />'+
					'<input type="hidden" id="post-post-id" value="" />'+
					'<label for="nombre_tarea">Mensaje</label>'+
					'<textarea class="ckeditor" id="post-post"></textarea>'+
					'</br>'+
					'<div>'+
					'<button class="btn btn-primary" id="post-submit" style="width:100%" >Enviar Respuesta</button>'+
					'</div>';

		var parent = $(this).parent();
		parent = parent.parent();
		last_asnwer = $(html);
		parent.append(last_asnwer);

		CKEDITOR.replace( 'post-post' );

		var _id = $(this).attr('id');
		_id = _id.split('_');
		$("#post-id").val(_id[1]);
		$("#post-post-id").val(_id[2]);

		$('#post-submit').click(function(){
			$('#post-submit').attr('disabled', true);

			var content = CKEDITOR.instances['post-post'].getData();
			content = content.replace(/&nbsp;/g, " ");
			content = content.replace(/&/g, "||ans||");
			content = content.replace(/=/g, "||igu||");
			content = content.replace(/\+/g, '||mas||');
			$.post(server+'/posts/newPost','id='+$("#post-id").val()+'&id_post='+$("#post-post-id").val()+'&post='+content, function(datos){
				if (datos!="None" && datos!="") {
					window.location.href =  window.location.href;
				};
			}, 'json');
		});
	});

	
});