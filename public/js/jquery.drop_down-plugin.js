(function ($) {
	$.fn.extend({
		drop_down_naan: function(options){
			defaults = {
				listas: [
					{
						valor_x_defecto:0,
						clase_x_defecto:"glyphicon glyphicon-flag",
						elementos:[
							{
								valor:1,
								clase:"glyphicon glyphicon-star"
							},
							{
								valor:2,
								clase:"glyphicon glyphicon-remove"
							},
							{
								valor:3,
								clase:"glyphicon glyphicon-heart"
							},
							{
								valor:4,
								clase:"glyphicon glyphicon-music"
							}
						]
					}
				]
			};
			 
			var options = $.extend({}, defaults, options);

			this.each(function(){
				var $this = $(this);
				// crear un boton para cada div con clase lista
				html='';
				for (var i = 0; i < options.listas.length; i++) {
					var contenedor = $('<div style="float:left;position:relative;"></div>');
					ancho_estilo = "";
					inc_alt=0;
					if (options.listas[i].clase_x_texto) {
						ancho_estilo="width:220px;left:-204px;";
						inc_alt=5;
					};
					
					// var contenedor_ele = $('<div class="conte-elements list-group" style="'+ancho_estilo+'display:none;position:absolute;z-index: 2;height:'+(options.listas[i].elementos.length*(20+inc_alt))+'px;"></div>');
                    var contenedor_ele = $('<div class="conte-elements list-group" style="display:none;position:absolute;z-index: 2;"></div>');
					var obj_lista = counstruir_Lista(options.listas[i]);
				    obj_lista.html('<div class="'+options.listas[i].clase_x_defecto+'"></div>');
					contenedor.append(obj_lista);
					contenedor.append(contenedor_ele);
					var altura =0;
					for (var j = 0; j < options.listas[i].elementos.length; j++) {
						var obj_ele = counstruir_Elemento(options.listas[i].elementos[j],options.listas[i].elemento_click,options.listas[i].clase_x_texto, options.task_id);

						// $(obj_ele).offset({ top: altura });
						altura+=2;
						contenedor_ele.append(obj_ele);
						if (options.listas[i].valor_x_defecto == options.listas[i].elementos[j].valor) {
							obj_lista.html('');
				        	obj_lista.html('<div class="'+options.listas[i].elementos[j].clase+'"></div>');
				        	$(obj_ele).addClass("element-selected");
						};
					};
					$this.append(contenedor);
				};
				
				function counstruir_Lista(elemento){
					var objeto = $(
							'<div class="' + options.clase_cabeza + ' btn btn-default btn-xs">' +
								'<div class="'+ elemento.clase_x_defecto+'"></div>' +
							'</div>'
					)
					.click(function(){
						var elementos = objeto.parent().find('.conte-elements');
						if (objeto.hasClass("dl-down")) {
				        	//elementos.css('display','none');
					        objeto.removeClass("dl-down");
					        $(elementos).hide();
				        }else{
							if ($(elementos).width()>16) {
								$(elementos).css({
									left:-($(elementos).width()-25)+"px"
								})
							};
							// ocultar todos los elementos
							$("#htd_contenedor").find(".conte-elements").each(function (index, ele) {
								// $(ele).fadeOut();
								$(ele).hide();
							});
							$("#htd_contenedor").find(".head-drop-down").each(function (index, ele) {
								$(ele).removeClass("dl-down");
							});
				        	$(elementos).show("blind", [], 500);
					       	objeto.addClass("dl-down");
				        };
				    });
			        return objeto;
			    }
			    function counstruir_Elemento(elemento,funcion,clase_texto,task_id){
					var objeto = $('<a class="list-group-item element-drop-item"><div class="'+options.clase_elemento+' '+elemento.clase+'">&nbsp;'+ elemento.texto +'</div></a>').click(function(){
				        if (funcion(elemento.valor,options.id,task_id,changeId)) {
				        	var obj_anterior = objeto.parent(0).find(".element-selected");
					        if (obj_anterior.length) {
					        	obj_anterior.removeClass("element-selected");
					        };
					        objeto.addClass("element-selected");
					        objeto.parent(0).fadeOut();
					        var cabeza = objeto.parent().parent().find('div:eq(0)');

					        cabeza.attr("class",options.clase_cabeza + ' btn btn-default btn-xs');
					       	cabeza.html('');
					       	if (clase_texto) {
								cabeza.html('<div class="'+clase_texto+'"></div>');
							}else{
								cabeza.html('<div class="'+elemento.clase+'"></div>');
							}
				        };
				        objeto.addClass("element-selected");
					    objeto.parent(0).fadeOut();
				    });
			        return objeto;
			    }
                function changeId (newId){
                    options.id = newId;
                }
			});
		}
	});
})(jQuery)