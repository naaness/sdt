var salvar = 0;
var fechaG="";
var lastObj=null;
var reSpan=false;
var SOnline=true;
var SpecialCh = new Array();
var offsetT = null;
var numchanges = 0;
var numchanged = 0;
var numerror = 0;
var b_color = new Array();
var time1 = null;
var time2 = null;
var time3 = null;
var time4 = null;
var rm_token = null;
var obj_clear_html = null;
$(document).ready(function(){
	server = "http://"+window.location.hostname;
	rm_token = $("#ch-rm-token").val();
	var cambio=false;
	var numemax=-0;
	
	// copiar codigo HTML
	$("#copy-rm").on("copy", function(e) {
      e.clipboardData.clearData();
      e.clipboardData.setData("text/plain", $('#sample').html());
      e.clipboardData.setData("text/html", $('#sample').html());
      $("#copy-rm").attr("class","btn btn-success");
      e.preventDefault();
    });


    // exportar contenido HTML
    $("#export-rm").click(function () {
    	// Crear encabezado excel
    	var file = {
			worksheets: [[]], // worksheets has one empty worksheet (array)
			creator: 'sdt ',
			created: new Date(),
			lastModifiedBy: 'sdt',
			modified: new Date(),
			activeWorksheet: 0
		}, 
		w = file.worksheets[0]; // cache current worksheet
		w.name = "RM "+$("#rm_searchdate").val();

		$("#sample tr").each(function(index, elemento){
			var r = w.push([]) - 1; // index of current row

    		var numerar_rm = $(elemento).find(".numerar");
    		var td_rm_1 = $('<td>'+numerar_rm.text()+'</td>');
    		w[r].push(numerar_rm.text());

    		var pizarron_rm = $(elemento).find(".pizarron");
    		var td_rm_2 = $('<td>'+pizarron_rm.text().replace("<br>",' ')+'</td>');
    		w[r].push(pizarron_rm.text().replace("<br>",' '));

    	});

    	XLSX(file).href();
    });
	
	SpecialCh["="] = "||igu||";
	SpecialCh["+"] = "||mas||";
	SpecialCh["&"] = "||ans||";

	$("#submit_email").click(function(){
        showSpinner($("#asunto"));
		$('#submit_email').attr("disabled", true);
		$.post(server+'/rmregistries/send_email','day='+$("#rm_searchdate").val()+'&emails='+$("#correo").val()+'&subject='+$("#asunto").val()+'&token='+rm_token, function(datos){
			if (datos!="None") {
				if (datos=='Other') {
			    	
			    }else{
                    hideSpinner($("#asunto"));
					$('#submit_email').attr("disabled", false);
					alert("Correo enviado");
				}
			}else{
				alert("Ha ocurrido un error en el envio");
			}
			$('#rm_btn_mail').modal('hide');
		}, 'json').fail(function() {
		    console.log("error de conexion");
		    SOnline=false;
		    errorConexcion();
		});
	});

	// Cambiar de etiqueta
	$('#registro_maestro').on('change', "#Setiqueta", function(e){
		if (lastObj) {
			var contenedor = $(lastObj).parent("div:eq(0)");
			var valore = $("#Setiqueta").val();
			if (valore!=0) {
				$.post(server+'/rmregistries/get_label','eti='+valore+'&token='+rm_token, function(datos){

					if (datos!="None") {
		            	$(contenedor).css("font-family",datos[0].rmFonts.name);
						$(lastObj).css("font-size",datos[0].rmSizes.size+"px");
						$(contenedor).css("color",datos[0].rmLabels.color);
						$(contenedor).css("background-color",datos[0].rmLabels.b_color);
						$(contenedor).attr("name",valore);
						autoSave();
				        // colorMin(contenedor,valore);
		            };
		    	}, 'json').fail(function() {
				    console.log("error de conexion");
				    SOnline=false;
				    errorConexcion();
				});
			}else{
				$(contenedor).css("font-family","Klavika");
				$(lastObj).css("font-size","14px");
				$(contenedor).css("color","#000000");
				$(contenedor).css("background-color","transparent");
				$(contenedor).attr("name",0);
				autoSave();
			};
	    }
	});

	// Eliminar un registro
	$('#registro_maestro').on('click', "#rm_eliminar", function(e){
		if (lastObj) {
			if (confirm("Realmente desea eliminar este registro?")) {
				hiddeTools();
				var tdp=$(lastObj).parent();
				var tdp=$(tdp).parent();
		        var trp=$(tdp).parent();

		        var hijo1 = $(trp).find( ".numerar" );
		        var nuR = $(hijo1).text().trim();
		        var nu = nuR;
		        if (nu!="1") {
		        	/// encontrar el nivel del ultimo numero
			        var nivelR = nu.split(".").length;
			        var nivel = nivelR;
			        var stado=0;
			        //controla el aumento del nivel del numeral
			        var inc=0;
			        //tipo de proceso
			        var tipo=0;
		        	$(".numerar").each(function(index, elemento){
			    		//identificar el elemento
			    		var str = $(elemento).text().trim();
						var res = str.split(".");
						if (stado==1) {
							stado=2;
					        nu = $(elemento).text().trim();
					        /// encontrar el nivel del ultimo numero
					        nivel = nu.split(".").length;
						};
						if (res.length<nivelR && stado==2) {
						 	stado=3;
						}
			    		if (stado==2) {
			    			//almacenar el nuevo numeral
			    			var newnum="";
			    			for (var i = (res.length-1); i >=0 ; i--) {
			    				if (parseInt(i) == (res.length-1)) {
			    					if ((parseInt(res[i])-1)>0) {
			            				newnum=parseInt(res[i])-1;
			    						newnum="."+newnum;
			    					};
			    				}else{
			            			newnum=res[i] + newnum;
			            			newnum="."+newnum;
			    				};
			    			}
			    			newnum=newnum.slice(1,newnum.length);
			    			$(elemento).text(newnum);
			    			var niv = res.length;
							var avan = $(elemento).text().split(".").length-niv;
							var tdpp=$(elemento).parent();
							var tdpp=$(tdpp).parent();
							var trpp=$(tdpp).parent();
							var hijop1 = $(trpp).find( "div[class^='conpizarron']" );
							$(elemento).animate({
								marginLeft: '+='+(12*avan)+'px'
							}, 500);
							var res = $(elemento).text().split(".");
							$(hijop1).css("margin-left", (32+32*(niv-1))+"px");
							$(hijop1).animate({
								marginLeft: '+='+(32*avan)+'px'
							}, 500);
							nivelR=niv;
			    		};
			    		if (str==nuR && stado==0) {
			    			//cambio de estado, apartir de este elemento procesar los numerales.
			    			stado=1;
			    		};
			    	});
					loastObj=null;
					var id = $(trp).attr("id");
				    if (id) {
				    	id = id.slice(3,id.length);
					    $.post(server+'/rmregistries/delete_registry','id='+id+'&token='+rm_token, function(datos){
					    	if (datos=='Other') {
			    	
			    			}
					    }).fail(function() {
						    console.log("error de conexion");
						    SOnline=false;
						    errorConexcion();
						});
				    };
					jQuery(trp).remove();
					OrdenAll();
		        }else{
		        	if ($('#sample >tbody >tr').length == 1){
		        		var id = $(trp).attr("id");
					    if (id) {
					    	id = id.slice(3,id.length);
						    $.post(server+'/sdt_live/eliminarRMLinea','id='+id+'&token='+rm_token).fail(function() {
							    console.log("error de conexion");
							    SOnline=false;
							    errorConexcion();
							});
					    };
		        		jQuery(trp).remove();
		        	}
		        }
			};
		};
	});


	$('#registro_maestro').on('click', "#save", function(e){
		if (time1) {
			clearTimeout(time1);
		};
		salvar=-1;
		OrdenAll();
		$(this).attr("disabled", "disabled");
	});
	
	// Disminuir la sangria
	function rm_upLevel(){
		if (SOnline) {
			var tdp=$(lastObj).parent();
			var tdp=$(tdp).parent();
	        var trp=$(tdp).parent();

	        var hijo1 = $(trp).find( ".numerar" );
	        var nu = $(hijo1).text().trim();
	        /// encontrar el nivel del ultimo numero
	        var nivel = nu.split(".").length;
	        var stado=0;
	        //controla el aumento del nivel del numeral
	        var inc=0;
	        //tipo de proceso
	        var tipo=0;
	        if (nivel>1) {
	        	$(".numerar").each(function(index, elemento){
	        		//identificar el elemento
	        		var str = $(elemento).text().trim();
					var res = str.split(".");
					if (res.length<=(nivel-2) && stado==1) {
	    			 	stado=2;
	    			}
	    			if (res.length<=(nivel-1) && stado==1) {
	    			 	tipo=1;
	    			}
	        		if (str==nu && stado==0) {
	        			// cambio de estado, apartir de este elemento procesar los numerales.
	        			stado=1;
	        		};
	        		if (stado==1) {
	        			// almacenar el nuevo numeral
	        			var newnum="";
	        			
	        			if (res.length==nivel && tipo==0) {
	        				// se detecta que es necesario subir un nivel al numeral
	        				inc+=1;
	        			}
	        			for (var i = 0; i < res.length; i++) {
	        				if (tipo==0) {
	        					if (nivel-1!=i) {
	            					if (i<nivel-2) {
		            					newnum+=res[i];
		            				}else if (i==nivel-2) {
		            					newnum+=parseInt(res[i])+inc;
		            				}else{
		            					newnum+=res[i];
		            				}
		            				newnum+=".";
	        					};
	        				}else{
	        					if (i==nivel-2) {
		            				newnum+=parseInt(res[i])+inc;
		            			}else{
		            				newnum+=res[i];
		            			}
		            			newnum+=".";
	        				};
	        			}
	        			newnum=newnum.slice(0,newnum.length-1);
	        			$(elemento).text(newnum);

	        			var niv = res.length;
						var avan = $(elemento).text().split(".").length-niv;
						

						var tdpp=$(elemento).parent();
						var tdpp=$(tdpp).parent();
						var trpp=$(tdpp).parent();
						var hijop1 = $(trpp).find( "div[class^='conpizarron']" );

						$(elemento).animate({
							marginLeft: '+='+(12*avan)+'px'
						}, 500);
						var res = $(elemento).text().split(".");
						$(hijop1).css("margin-left", (32+32*(niv-1))+"px");
						$(hijop1).animate({
							marginLeft: '+='+(32*avan)+'px'
						}, 500);
	        		};
	        	});
				autoSave();
	        };
		};
		
	}

	// Aumentar la sangria
	function rm_downLevel(){
		if (SOnline) {
			if (lastObj) {
				if ($(lastObj).attr("class").slice(0,8)=="pizarron") {
					//obtener el DOM de la fila
					var tdp=$(lastObj).parent();
					var tdp=$(tdp).parent();
					// showTools(lastObj);

		            var trp=$(tdp).parent();
		            //obtener el numero de la fila

		            var hijo1 = $(tdp).children('div').get(0);
		            var nu = $(hijo1).text().trim();
		            /// encontrar el nivel del ultimo numero
		            var nivel = nu.split(".").length;
		            // ultimo nivel y numeracion
		            var last_nivel = 0;
		            var last_nu = "";

		            var sigui=0;
		            var nume="";
		            //var nu=$(hijo1).text().trim();
		            var nivel;
		            var ante="";
		            var numeral="";
		            var last_str="";
		            $(".numerar").each(function(index, elemento){
		            	var str = $(elemento).text().trim();
		            	var res = str.split(".");

		            	if (sigui==1) {
		            		var newnum ="";
							// nivel de incremento
							if (last_nu!="") {
								if (nu==str.slice(0,nu.length) ) {
									$(elemento).text(last_nu+str.slice(nu.length));
									if ($(elemento).text()==last_str) {
										$(elemento).text(last_nu+"."+1+str.slice(nu.length));
									};
								}else{
									for (var i = 0; i < res.length; i++) {
										if (i==nivel-1) {
											newnum+=(parseInt(res[i])-1);
										}else{
											newnum+=res[i];
										}
										newnum+=".";
										//la cadena debe coincidir para hasta el nivel anterior
										if (i<(nivel-1)) {
											if (newnum!=nu.slice(0,newnum.length)) {
												sigui=2;
												//sino coinciden entonces no debe incrementar y terminar de buscar
												i = res.length;
											};
										};
									};
									if (sigui==1) {
										newnum=newnum.slice(0,newnum.length-1);
										$(elemento).text(newnum);
									};
								}
							};
		            	}
		            	if(sigui==0){
		            		
		                	if (nu==str) {
								sigui=1;
								if (last_nivel!="") {
		                			numeral= last_nu.split(".");
		                			numeral[numeral.length-1]=parseInt(numeral[numeral.length-1])+1;
		                			numeral=numeral.join(".");
		                			$(elemento).text(numeral);
		                			nivel = nu.split(".").length;
		                			last_nu = numeral;
								}else if (nume!="") {
									nivel = nu.split(".").length;
									if (nume+"."+1!=$(elemento).text()) {
										$(elemento).text(nume+"."+1);
									}else{
										sigui=2;
									}
									last_nu=nume;
								};
							}else {
								nume=str;

								// llevar un registro del ultimo registro con un nivel superior
		                		// al que intento agregar mas niveles
		                		if ((nivel+1)==res.length) {
		                			last_nivel=res.length;
		                			last_nu=str;
		                		}else if ( res.length <= nivel) {
		                			last_nivel=0;
		                			last_nu="";
		                		};
							}
						}
						last_str = $(elemento).text();
						var niv = res.length;
						var avan = $(elemento).text().split(".").length-niv;
						

						var tdpp=$(elemento).parent();
						var tdpp=$(tdpp).parent();
						var trpp=$(tdpp).parent();
						var hijop1 = $(trpp).find( "div[class^='conpizarron']" );

						$(elemento).animate({
							marginLeft: '+='+(12*avan)+'px'
						}, 500);
						var res = $(elemento).text().split(".");
						$(hijop1).css("margin-left", (32+32*(niv-1))+"px");
						$(hijop1).animate({
							marginLeft: '+='+(32*avan)+'px'
						}, 500);
		            });
					autoSave();
				};
			}
		};
	}

	// Editar contenido del registro

	$('#registro_maestro').on('keypress', ".conpizarron", function(e){
		var code = e.keyCode || e.which;
		if (e.keyCode == 13 && SOnline )
	    {
	        if (e.shiftKey === false && SOnline)
	        {
	            // new line
	            //obtener el DOM de la fila
	            var tdp=$(this).parent();
                var trp=$(tdp).parent();

	            var hijo1 = (trp).find( ".numerar" );
	            var hijopiz = (trp).find( ".pizarron" );
	            var hijoch = (trp).find( ".chek" );

	            var nume = $(hijo1).text().trim();
	            /// encontrar el nivel del ultimo numero
	            var nivel = nume.split(".").length;

	            /// cambiar todos los numeros de ese nivel desde la fila +1 hasta que encuentre una fila que tenga un numero co  menor nivel
	            newNumber(nume);
	            tr = $('<tr ></tr>');
	            td1 = $('<td></td>');
	            td2 = $('<td></td>');
	            var newnum ="";
	            var res = nume.split(".");
				for (var i = 0; i < res.length; i++) {
					if (i==(nivel-1)) {
						newnum+=(parseInt(res[i])+1);
					}else{
						newnum+=res[i];
					}
					newnum+=".";
				};
				newnum=newnum.slice(0,newnum.length-1);

				input1=$('<div class="btn-group text-center" data-toggle="buttons"><label class="btn btn-default chek" id="checkAll"></label></div>');
				div1 = $('<div style="float:left"></div>');
	            div2 = $('<div class="numerar" style="margin-left:'+(12*nivel)+'px">'+newnum+'</div>');
	            div4 = $('<div class="conpizarron" style="margin-left:'+(32+32*(nivel-1))+'px;border-radius:3px"</div>');
	            div3 = $('<div class="pizarron" name="0" contenteditable="true" style="margin-left:5px;font-weight: bold;word-break: break-all;" ></div>');
	            //div4 = $('<div class="tools"></div>');
	            //botonDel = $('<button type="button" class="btn btn-'+ ruta1 +'"><span class="glyphicon glyphicon-'+ruta2+'"></span></button>');
	            
	            var contenedor = $(lastObj).parent("div:eq(0)");
				var valore = $("#Setiqueta").val();
				

				if (valore!=0 && b_color.indexOf(valore)) {
					$(div4).css("font-family",b_color[valore].rmFonts.name);
					$(div3).css("font-size",b_color[valore].rmSizes.size+"px");
					$(div4).css("color",b_color[valore].rmLabels.color);
					$(div4).css("background-color",b_color[valore].rmLabels.b_color);
					$(div4).attr("name",valore);

				}else{
					div4.css("font-family","Klavika");
					div3.css("font-size",14+"px");
					div4.css("color","#000000");
					div4.css("background-color","transparent");
					div4.attr("name",0);
				};
				

				div1.append(div2);
	            div4.append(div3);
	            td1.append(input1);
	            td2.append(div1);
	            td2.append(div4);
	            div4.attr("name",$(this).attr("name"));

	            tr.append(td1);
	            tr.append(td2);
	            
	            $(trp).after(tr);
	            
	            $(tr).css('display', 'none');
	            $(tr).fadeIn( "slow" );
	            $(div3).focus();
	   			if (lastObj) {
					lastObj.attr('id',"none");
				};
				lastObj=$(div3);
				lastObj.attr("id","myTextArea");
				// autoSave();
	    		// showTools(lastObj);
	            return false;
	        }
	        else
	        {
	            // run your function

	        }
	        //return false;s
	    }
	    autoSave();
	    
	});


	$('#registro_maestro').keydown(function(e) {
		autoSave();
		var keyCode = e.keyCode || e.which;
		if (keyCode==8) {
			
		};
		if (e.shiftKey === true && keyCode == 9 && lastObj){
			e.preventDefault();
			rm_upLevel();
			sanityNumbering();
		}else if (keyCode == 9 && lastObj) { 
		    e.preventDefault(); 
		    rm_downLevel();
		    sanityNumbering();
		}
	});

	// subir de nivel el registro desde las herramientas
	$('#registro_maestro').on('click', '#rm_uplevel', function(){
		rm_upLevel();
	});
	// bajar de nivel el registro desde las herramientas
	$('#registro_maestro').on('click', '#rm_downlevel', function(){
		rm_downLevel();
	});

	// nuevo parrafo en un registro
	$('#registro_maestro').on('click', '#rm_new_parraf', function(){
		$(lastObj).append('<br>');
	});

	// Buscar Palabra
	$('#loading-sowrds-rm').click(function () {
        showSpinner($("#lista-Palabras"));
		$("#lista-Palabras").html('');
		var encabezado = $('<a class="list-group-item active">Lista de palabras encontradas</a>');
		$("#lista-Palabras").append(encabezado);
		$.post(server+'/rmregistries/search_word','word='+$("#sword-to-search").val()+'&token='+rm_token, function(datos){
			if (datos=='Other') {
			    	
			}else{
				if (datos!="None") {
                    hideSpinner($("#lista-Palabras"));
					for (var i = 0; i < datos.length; i++) {
						var lista = $('<a class="list-group-item search-day-rm '+datos[i].day+'" style="cursor:pointer;"><span class="badge">'+datos[i].day+'</span>'+datos[i].html+'</a>');
						$("#lista-Palabras").append(lista);
					}
				};
			}
		}, 'json').fail(function() { 
		    console.log("error de conexion");
		    SOnline=false;
		    errorConexcion();
		});
	});
	$('bt-searchRM').click(function () {
		$("#lista-Palabras").html('');
	});

	// Del registro Maestro al HTD
	$('#registro_maestro').on('click', '#rm_to_htd', function(){
		if ($(lastObj).length) {
			var tdp=$(lastObj).parent();
			var tdp=$(tdp).parent();
	        var trp=$(tdp).parent();
	        var id = trp.attr("id");
	        if (id) {
	        	id = id.split("_");
		        id = id[1];
		        $("#rm-id-rm").val(id);
		        // No mas de 30 caracteres o 4 palabras
		        var texto = $(lastObj).text();
		        texto=texto.trim();
		        arrTexto = texto.split(" ");
		        texto="";
		        for (var i = 0; i < arrTexto.length; i++) {
		        	if (texto.length<=20) {
		        		texto = texto + arrTexto[i] + " ";
		        	};
		        };
		        $("#rm_name_to_htd").val(texto);
		        $("#rm_description_to_htd").val($(lastObj).text());
				$('#myModalRMtoHTD').modal('show');
	        };
		};
	});

    // populate delegates
    $("#rm_project_task").html('');
    $("#rm_project_task").append('<option value="0" >--Sin proyecto--</option>');
    populateDelegates();
    $.post(server+'/projects/getProjects', function(datos){
        for(var i=0; i<datos.length;i++){
            console.log('<option value="'+datos[i].id+'" >'+datos[i].name+'</option>');
            $("#rm_project_task").append('<option value="'+datos[i].id+'" >'+datos[i].name+'</option>');
        }
    }, 'json');

    $("#rm_project_task").change(function(){
        if($("#rm_project_task").val()>0){
            $.post(server+'/users/getUsersProjects','id='+$("#rm_project_task").val(), function(datos){
                var user_id =datos.user_id;
                var users =datos.users;
                $("#rm_delegate").html('');
                if(users.length>0){
                    for(var i=0; i<users.length;i++){
                        var user_a='';
                        if(users[i].id==user_id){
                            user_a='selected';
                        }
                        $("#rm_delegate").append('<option value="'+users[i].id+'" '+user_a+'>'+users[i].username+'</option>')
                    }
                    $("#htd-tarea-tarea").prop("disabled", false);
                }else{
                    $("#rm_delegate").append('<option value="0" >--Sin usuarios--</option>');
                    $("#htd-tarea-tarea").prop("disabled", true);
                }

            }, 'json');
        }else{
            populateDelegates();
        }
    });

	$('#htd-tarea-tarea').click(function(){
		// rm_name_to_htd
		var id_dia = $("#rm-id-rm").val();
		var fecha_tras = $("#rm_date_to_htd").val();
		var nombre = $("#rm_name_to_htd").val();
        var delegate = $("#rm_delegate").val();
        var proyecto = $("#rm_project_task").val();
		$('#htd-tarea-tarea').button('loading');
		$.post(server+'/tasks/traslateRMtoHTD','id_rm='+id_dia+"&date="+fecha_tras+"&name="+nombre+"&description="+$("#rm_description_to_htd").val()+"&priority="+$("#rm_priority_to_htd").val()+'&delegate='+delegate+'&project='+proyecto, function(datos){
			if (datos!="None" && datos!="") {
				$('#myModalRMtoHTD').modal('hide');
				$('#htd-tarea-tarea').button('reset');

	 	    };
	 	}, 'json');
	});

	$('#registro_maestro').on('click', "div[class^='pizarron']", function(){
		
		if (lastObj) {
			lastObj.attr('id',"none");
		};
		lastObj=$(this);
		lastObj.attr("id","myTextArea");
		showTools(lastObj);
		if (time3) {
			clearTimeout(time3);
		};
	});
	$('#registro_maestro').on('focusout', "div[class^='pizarron']", function(){
		time3 = setTimeout(hiddeTools, 200);
	});
	$('#registro_maestro').on('click', "#rm_tool", function(){
		if (time3) {
			clearTimeout(time3);
		};
	});
	$('#registro_maestro').on('click', "div[class^='numerar']", function(){
		var td=$(this).parent();
		var tr=$(td).parent();
		lastObj = $(tr).find( "div[class^='pizarron']" );
		if (lastObj) {
			lastObj.attr('id',"none");
		};
		lastObj.attr("id","myTextArea");
		$(lastObj).focus();
	});
	$('#registro_maestro').on('click', '.chek', function(){
		var td=$(this).parent();
		td=$(td).parent();
		var tr=$(td).parent();
		var hijo1 = $(tr).find( ".conpizarron" );
		var hijo2 = $(tr).find( ".pizarron" );
		var namec = $(hijo1).attr("name");

		var clase = "" + $(this).attr("class") + "";
        var valor = clase;
        if (valor.indexOf("active")==-1) {
        	$(hijo2).css("font-weight", "normal");
			$(tr).css("background-color", "#f1f1f1");
			if (namec!="0") {
				$(hijo1).css("background-color",b_color[namec].rmLabels.b_color_checked);
			};
        }else{
        	$(hijo2).css("font-weight", "bold");
			$(tr).css("background-color", "transparent");
			if (namec!="0") {
				$(hijo1).css("background-color",b_color[namec].rmLabels.b_color);
			};
        };

		autoSave();
	});
	


//	$("#dLabel").tooltip({
//		placement: 'bottom',
//		template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="background-color:#ef0d0a;"></div></div>'
//	});
//	$('#dLabel').tooltip('show');
//
//	$('[data-toggle="tooltip"]').tooltip();
//	// $('#save').tooltip('show');
//	$('#on-off-line').tooltip('show');
	
	$('#rm_searchRM').on('click', '.search-day-rm', function(){
		var clase =  $(this).attr("class");
		var fecha = clase.split(" ");
        showSpinner($("#rm_contenido"));
		$.post(server+'/rmregistries/get_registries', 'date='+fecha[2]+'&token='+rm_token,function(datos){
			if (datos!='Other') {
				if (datos!='None') {
                    hideSpinner($("#rm_contenido"));
					$("#rm_contenido").html(datos);
					inicioRM();
					$('#rm_searchRM').modal('hide');
					fechaG=fecha[2];
					$("#rm_searchdate").val(fecha[2]);
                    $("#sdt-rm-date-string").text(dateToString(fecha[2]));
                    sanityNumbering();
				}
			}else{

			}
 		}).fail(function() {
	   		console.log("error de conexion");
	   		errorConexcion();
		});
	});

    $("#rm_searchdate").change(function(){
        showSpinner($("#rm_contenido"));
        $.post(server+'/rmregistries/get_registries', 'date='+$("#rm_searchdate").val()+'&token='+rm_token,function(datos){
            hideSpinner($("#rm_contenido"));
            $("#rm_contenido").html(datos);
            fechaG=$("#rm_searchdate").val();
            $("#rm_searchdate").val(fechaG);
            $("#sdt-rm-date-string").text(dateToString(fechaG));
            inicioRM();
            $('#rm_searchRM').modal('hide');
            sanityNumbering();
        }).fail(function() {
            console.log("error de conexion");
            errorConexcion();
        });
    });
	$("#rm_left_day").click(function(){
        showSpinner($("#rm_contenido"));
		$.post(server+'/rmregistries/get_registries', 'date='+sumaFecha(-1,$("#rm_searchdate").val())+'&token='+rm_token,function(datos){
			if (datos!='Other') {
				if (datos!='None') {
                    hideSpinner($("#rm_contenido"));
					$("#rm_contenido").html(datos);
					fechaG=sumaFecha(-1,$("#rm_searchdate").val());
					$("#rm_searchdate").val(fechaG);
                    $("#sdt-rm-date-string").text(dateToString(fechaG));
					inicioRM();
					$('#rm_searchRM').modal('hide');
                    sanityNumbering();
				}
			}
 		}).fail(function() {
	   		console.log("error de conexion");
	   		errorConexcion();
		});
	});
	$("#rm_right_day").click(function(){
        showSpinner($("#rm_contenido"));
		$.post(server+'/rmregistries/get_registries', 'date='+sumaFecha(1,$("#rm_searchdate").val())+'&token='+rm_token,function(datos){
			if (datos!='Other') {
				if (datos!='None') {
                    hideSpinner($("#rm_contenido"));
					$("#rm_contenido").html(datos);
					fechaG=sumaFecha(1,$("#rm_searchdate").val());
					$("#rm_searchdate").val(fechaG);
                    $("#sdt-rm-date-string").text(dateToString(fechaG));
					inicioRM();
					$('#rm_searchRM').modal('hide');
                    sanityNumbering();
				}
			}
 		}).fail(function() {
	   		console.log("error de conexion");
	   		errorConexcion();
		});
	});
	$("#rm_btn_hoy").click(function(){
        showSpinner($("#rm_contenido"));
		$.post(server+'/rmregistries/get_registries', 'date='+$("#fecha_hoy").val()+'&token='+rm_token,function(datos){
			if (datos!='Other') {
				if (datos!='None') {
                    hideSpinner($("#rm_contenido"));
					$("#rm_contenido").html(datos);
					fechaG = $("#fecha_hoy").val();
					$("#rm_searchdate").val(fechaG);
                    $("#sdt-rm-date-string").text(dateToString(fechaG));
					inicioRM();
					$('#rm_searchRM').modal('hide');
                    sanityNumbering();
				}
			}
		}).fail(function() {
	   		console.log("error de conexion");
	   		errorConexcion();
		});
	});

	$("#rm_searchdate").click(function(){
		fechaG=null;
	});
	// Renderizar registro via Ajax
    $("#sdt-rm-date-string").text(dateToString($("#rm_searchdate").val()));
    showSpinner($("#rm_contenido"));
	$.post(server+'/rmregistries/get_registries', 'date='+$("#rm_searchdate").val()+'&token='+rm_token,  function(datos){
        hideSpinner($("#rm_contenido"));
        $("#rm_contenido").html(datos);

	    inicioRM();
        sanityNumbering();
	}).fail(function() {
   		console.log("error de conexion");
   		errorConexcion();
	});

	//Obtener todas las etiquetas del usuario
	$.post(server+'/rmregistries/get_labels_b_colors', 'id=my'+'&token='+rm_token,  function(datos){
		if (datos!="None") {
			if (datos=='Other') {

			}else{
				for (var i = 0; i < datos.length; i++) {
					b_color[datos[i].rmLabels.id] = datos[i];
				};

				$(".chek" ).each(function(index, elemento){

					var clase = "" + $(elemento).attr("class") + "";
			        var valor = clase;
			        if (valor.indexOf("active")==-1) {
			        	valor = false;
			        }else{
			        	valor = true;
			        }

			        var td=$(elemento).parent();
			        td=$(td).parent();
					var tr=$(td).parent();

					var hijo1 = $(tr).find(".conpizarron");
					if (valor) {
						$(hijo1).css("font-weight", "normal");
						$(tr).css("background-color", "#f1f1f1");
						colorMin(hijo1,$(elemento).parent().attr("name"));
					}else{
						$(hijo1).css("font-weight", "bold");
						$(tr).css("background-color", "transparent");
						colorMin(hijo1,$(elemento).parent().attr("name"));
					}
			    });

			}
            sanityNumbering();
		};
	}, 'json' ).fail(function() {
   		console.log("error de conexion");
   		errorConexcion();
	});
	// $.post(server+'/rmLabels/get_labels_b_colors', 'id=my'+'&token='+rm_token,  function(datos){
	
	// }).fail(function() {
 //   		console.log("error de conexion");
 //   		errorConexcion();
	// });

	// Activar calendario

   	//$( ".datepicker" ).datepicker();
});

var populateDelegates = function(){
    $.post(server+'/users/getUsers', function(datos){
        var user_id =datos.user_id;
        var users =datos.users;
        $("#rm_delegate").html('');
        for(var i=0; i<users.length;i++){
            var user_a='';
            if(users[i].id==user_id){
                user_a='selected';
            }
            $("#rm_delegate").append('<option value="'+users[i].id+'" '+user_a+'>'+users[i].username+'</option>')
        }
    }, 'json');
}
var errorConexcion = function(){
	$("#on-off-line").attr('class','btn btn-danger');
	$("#rm_progress_success").css("width","0%");
	$("#rm_progress_danger").css("width","100%");
}
var autoSave = function(){
	if (time1) {
		clearTimeout(time1);
	};
	time1 = setTimeout(saveRegistries, 5000);
	btn_save_habilitar();
	salvar=5;
}
var sumaFecha = function(d, fecha)
{
	 var Fecha = new Date();
	 var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
	 var sep = sFecha.indexOf('/') != -1 ? '/' : '-'; 
	 var aFecha = sFecha.split(sep);
	 var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
	 fecha= new Date(fecha);
	 fecha.setDate(fecha.getDate()+parseInt(d));
	 var anno=fecha.getFullYear();
	 var mes= fecha.getMonth()+1;
	 var dia= fecha.getDate();
	 mes = (mes < 10) ? ("0" + mes) : mes;
	 dia = (dia < 10) ? ("0" + dia) : dia;
	 var fechaFinal = dia+sep+mes+sep+anno;
	 return (fechaFinal);
 }

var inicioRM  = function  () {
	
	//darDiaNav($("#rm_left_day"),-1);
	//darDiaNav($("#rm_right_day"),1);

	var today =  new Date();
	var month = today.getMonth();
	var day = today.getDate();
	var year = today.getFullYear();

	var clase =  $("#rm_btn_hoy").attr("class");
	clase = clase.split(" ");
	$("#rm_btn_hoy").attr("class",clase[0]+" "+clase[1]+" "+day+"/"+(month+1)+"/"+year);

	btn_save_deshabilitar();
	
	// time1=setInterval(saveRegistries, 1000);
	time2=setInterval(showDays, 300);
	// var time=setInterval(removeSpanCopy, 50);

	$(".numerar").each(function(index, elemento){
		var str = $(elemento).text().trim();
		var res = str.split(".");
		var niv = res.length;
		var tdpp=$(elemento).parent();
		var trpp=$(tdpp).parent();
		var hijop1 = $(trpp).find("div[class^='conpizarron']");

		$(elemento).animate({
			marginLeft: '+='+(12*(niv-1))+'px'
		}, 500);
		var res = $(elemento).text().split(".");
		$(hijop1).animate({
			marginLeft: '+='+(32*(niv-1))+'px'
		}, 500);
	});
	

    $(".pizarron").bind('paste', function(e) {
		autoSave();
		console.log('se pego');
		obj_clear_html = $(this);
		time4 = setTimeout(function() { clearHtmlTags();}, 500);
	});
}
var darDiaNav = function (obj,dif_dia) {
	var clase =  $(obj).attr("class");
	clase = clase.split(" ");
	$(obj).attr("class",clase[0]+" "+clase[1]+" "+operarFecha($("#rm_searchdate").val(),dif_dia));
}
var operarFecha = function (fech_f,dif_dia) {
	var dtA = fech_f;
	dtA = dtA.split("/");
	dtA = new Date(dtA[1]+'/'+dtA[0]+'/'+dtA[2]);

	tiempo=dtA.getTime();
    milisegundos=parseInt(dif_dia*24*60*60*1000);
    var total=dtA.setTime(tiempo+milisegundos);

	var month = dtA.getMonth();
	var day = dtA.getDate();
	var year = dtA.getFullYear();
	return day+"/"+(month+1)+"/"+year;
}

var newNumber = function(nu){ // VT14 funcion que envia a el controlador ajax y al metodo getCiudades el pais para cargar las ciudades
    var sigui=false;
    var nivel = nu.split(".").length;
    var num_last_level = [];
    var _inc = 0;
    $(".numerar").each(function(index, elemento){
		/// cambiar solo los del mismo nivel en el rango
		var str = $(elemento).text();
		var res = str.split(".");
		var niv = res.length;
		if (sigui) {
			if (res<nivel) {
				sigui=false;
			}
			if (sigui) {
				// sin la numeracion el ultimo nivel no existe, entonces 
				// tomar el ultimo del nival anterior y agregarle uno
				console.log(num_last_level[niv]);
				if (!(num_last_level.hasOwnProperty(niv))) {
				//if ( num_last_level[niv]=='undefined' || num_last_level[niv]=='NaN'){
					num_last_level[niv]=num_last_level[(niv-1)]+'.1';
					$(elemento).text(num_last_level[niv]);
				}else{
					$(elemento).text(incLevel(num_last_level[niv]));
				}
			};
		};

		// almacenar la la ultima numeracion del nivel
		str = $(elemento).text();
		num_last_level[niv]=str;

		if (str==nu) {
			sigui=true;
			// este nivel debe tener un incremento
			_inc=0;
			num_last_level[niv]=incLevel(num_last_level[niv]);
		};
		
		
	});
}

var sanityNumbering = function(){
	// Esta funcion intenta establecer la numeracion
	// segun la sangria que tenga.
	var actual_level = 1;
	var last_nivel = [];
	var primero = true;
	var num_last_level = [];
	$(".numerar").each(function(index, elemento){
		// cambiar solo los del mismo nivel en el rango
		var str = $(elemento).text();
		var res = str.split(".");
		var niv = res.length;
		if (primero) {
			primero=false;
			num_last_level[1]="1";
			$(elemento).text(num_last_level[1]);
			actual_level=1;
		}else{
			var str = $(elemento).text();
			var res = str.split(".");
			var niv = res.length;
			if (!(num_last_level.hasOwnProperty(niv))) {
				num_last_level[niv]=num_last_level[(niv-1)]+'.1';
				$(elemento).text(num_last_level[niv]);
			}else{
				if (niv>actual_level) {
					num_last_level[niv]=num_last_level[(niv-1)]+'.1';
					$(elemento).text(num_last_level[niv]);
				}else{
					$(elemento).text(incLevel(num_last_level[niv]));
				}
			}
			actual_level=niv;
		}
		// almacenar la la ultima numeracion del nivel
		// si dismi
		str = $(elemento).text();
		num_last_level[niv]=str;
	});
}

var incLevel = function(num){
	num = ''+num+'';
	var numarr = num.split(".");
	var nivel = numarr.length;
	var newnum ="";
	// nivel de incremento
	for (var i = 0; i < numarr.length; i++) {
		if (i==nivel-1) {
			newnum+=(parseInt(numarr[i])+1);
		}else{
			newnum+=numarr[i];
		}
		newnum+=".";
	};
	newnum=newnum.slice(0,newnum.length-1);
	return newnum;
}
var LineRM = function(elemento,nume){
		server = "http://"+window.location.hostname;
		var fecha = $("#rm_searchdate").val();
		// var tdp=$(elemento).parent("td:eq(0)");
	    var trp=elemento;
	    var hijo1 = $(trp).find( ".numerar" );
		var numeracion = $(hijo1).text();
		var texto = $(trp).find( ".pizarron" ).html();
		var hijo2 = $(trp).find( ".chek" );
		var hijo3 = $(trp).find( ".conpizarron" );

		var clase = "" + $(hijo2).attr("class") + "";
	    var valor = clase;
	    if (valor.indexOf("active")==-1) {
	    	tachado = 0;
	    }else{
	    	tachado = 1;
	    }
		if (!nume) {
			nume=0;
		};
		var orden = nume;
		// var Lf = $(elemento).css("font-family");
		// var Ls = $(elemento).css("font-size");
		// var Lc = $(elemento).css("color");
		// var Lb = $(elemento).css("background-color");
		var inteme = $(hijo3).attr("name");

		//adecuamos el texto con los caracteres epeciales
		texto=""+texto+"";
		texto = texto.replace(/&nbsp;/g, " ");
		texto = texto.replace(/&/g, "||ans||");
		texto = texto.replace(/=/g, "||igu||");
		texto = texto.replace(/\+/g, '||mas||');

		// for (x in SpecialCh) {
		//     texto= texto.split(x).join(SpecialCh[x]);
		// }
		
		if ($(trp).attr("id")) {
			id = $(trp).attr("id");
	        id = id.slice(3,id.length);
			$.post(server+'/rmregistries/update_registry','id='+id+'&numbering='+numeracion+'&order='+orden+'&registry='+texto+'&checked='+tachado+'&rm_label_id='+inteme+'&token='+rm_token).fail(function(datos) {
			    if (datos=='Other') {

			    }else{
			    	btn_save_habilitar();
			    }
			}, 'json').done(function() {
				SOnline=true;
			    progresoPositivo();
			}).fail(function() {
			    console.log("error de conexion");
			    salvar=5;
			    btn_save_habilitar();
			    SOnline=false;
			    progresoNegativo();
			    errorConexcion();
			});
		}else{
			$.post(server+'/rmregistries/new_registry','date='+fecha+'&numbering='+numeracion+'&order='+orden+'&registry='+texto+'&checked='+tachado +'&rm_label_id='+inteme+'&token='+rm_token, function(datos){
		        if (datos!="None" && datos!="") {
		        	if (datos=='Other') {
			    	
			    	}else{
			        	$(trp).attr('id',"tr_"+datos);
			        	SOnline=true;
			        	progresoPositivo();
			        }
		        }else{
		        	console.log("error de conexion");
				    salvar=5;
				    btn_save_habilitar();
				    SOnline=false;
				    progresoNegativo();
				    errorConexcion();
		        }
		    }, 'json').done(function() {
			    progresoPositivo();
			}).fail(function() {
			    console.log("error de conexion");
			    salvar=5;
			    btn_save_habilitar();
			    SOnline=false;
			    progresoNegativo();
			    errorConexcion();
			});
		}
}
var progresoPositivo = function(){
	numchanged+=1;
	$("#rm_progress_success").css("width",(numchanged/numchanges*100)+"%");
	// conectividad();
	$("#on-off-line").attr('class','btn btn-success');
	btn_save_saved();
}
var progresoNegativo = function(){
	numerror+=1;
	$("#rm_progress_danger").css("width",(numerror/numchanges*100)+"%");
	// conectividad();
	btn_save_saved();
}
var btn_save_saved = function (){
	if (numchanges==(numchanged+numerror)) {
		$("#rm_save_icon").attr("class", "glyphicon glyphicon-saved");
		// Habilitar los botones de navegacion del RM
		btn_nav_rm_habilitar();
		changeTextTooltip("save","Guardado");
	};
}

var btn_nav_rm_deshabilitar = function(){
	$("#rm_left_day").attr("disabled",true);
	$("#rm_searchdate").attr("disabled",true);
	$("#rm_right_day").attr("disabled",true);
}

var btn_nav_rm_habilitar = function(){
	$("#rm_left_day").attr("disabled",false);
	$("#rm_searchdate").attr("disabled",false);
	$("#rm_right_day").attr("disabled",false);
}

var changeTextTooltip = function(id_ele, newValue){
	// $("#"+id_ele).tooltip('hide')
 //          .attr('data-original-title', newValue)
 //          .tooltip('fixTitle');
    $("#"+id_ele).attr('data-original-title', newValue);
    $("#"+id_ele).tooltip('fixTitle');
    $("#"+id_ele).tooltip('show');
}

var OrdenAll = function(){
	numchanges = $("#sample tr").length;
	numchanged=0;
	numerror=0;
	$("#rm_progress_success").css("width","0%");
	$("#rm_progress_danger").css("width","0%");
	btn_nav_rm_deshabilitar();
	changeTextTooltip("save","Guardando...");

	sanityNumbering();
	$("#sample tr").each(function(index, elemento){
		LineRM(elemento,index)
	});
}
var saveRegistries = function(){
	OrdenAll();
	btn_save_deshabilitar();
	clearTimeout(time1);
}
var btn_save_deshabilitar = function(){
	$("#save").attr("disabled", "disabled");
	$("#rm_save_icon").attr("class", "glyphicon glyphicon-saved");
}
var btn_save_habilitar = function(){
	changeTextTooltip("save","Listo para guardar");
	$("#save").removeAttr("disabled");
	$("#rm_save_icon").attr("class", "glyphicon glyphicon-save");
	
}
var showDays = function(){
	var mes = $(".ui-datepicker-month").val();
	if(!(typeof mes == "undefined")){
		mes = parseInt(mes)+1;
		if (mes<10) {
			mes="0"+mes
		};
		var anio=$(".ui-datepicker-year").val();
		var fecha = "01/"+mes+"/"+anio;
		if (fechaG!=fecha) {
			$.post(server+'/rmregistries/get_days','date='+fecha+'&token='+rm_token, function(datos){
	            $("#ui-datepicker-div td").each(function(index, elemento){
	            	hijo = $(elemento).find( "a" );
	            	dia=hijo.text().trim();
	            	if (dia!="") {
	            		if (dia<10) {
							dia="0"+dia
						};
						if (typeof datos.days[dia] != "undefined"){
							hijo.css("color","blue");
							if (datos.days[dia]) {
								hijo.css("color","#20ba13");
							};
						};
	            	};
	            });
		    },'json').fail(function() {
			    console.log("error de conexion");
			    SOnline=false;
			    errorConexcion();
			});
		    fechaG=fecha;
		};
	}
}
var removeSpanCopy = function(){
	//if (reSpan) {
		reSpan=false;
		if (lastObj) {
			var span = $(lastObj).find("span:eq(0)");
			if (span.length) {
				$(lastObj).find("span").each(function(index, elemento){
					$(elemento).removeAttr('style');
				});
				var  var1 = $(lastObj).html();
				var1 = var1.replace("<span>",'');
				var1 = var1.replace("</span>",'');
				$(lastObj).html(var1);
			};
		};
}
var showTools = function(obj){
	var offset = obj.offset();
	offset.top = offset.top + $(obj).height();
	offset.left = offset.left + $(obj).width() - $("#rm_tool").width();
	$("#rm_tool").css("display", "");
	$("#rm_tool").offset({ top: offset.top , left: offset.left });
	$("#Setiqueta").val($(obj).parent().attr("name"));
}
 var hiddeTools = function(){
 	$("#rm_tool").fadeOut();
 }

 var colorToHex = function (color) {
    if (color.substr(0, 1) === '#') {
        return color;
    }
    var digits = /(.*?)rgb\((\d+), (\d+), (\d+)\)/.exec(color);

    var red = parseInt(digits[2]);
    var green = parseInt(digits[3]);
    var blue = parseInt(digits[4]);

    var rgb = blue | (green << 8) | (red << 16);
    return digits[1] + '#' + rgb.toString(16);
};

var colorMin =  function(obj,id_et) {
    if(id_et != 'undefined'){
        var namec = $(obj).attr("name");
        if (namec!="0" && namec) {
            $(obj).css("background-color",b_color[id_et].rmLabels.b_color);
            var tdp=$(obj).parent("td:eq(0)");
            var trp=$(tdp).parent();
            var hijo1 = $(trp).find( ".chek" );
            var clase = "" + $(hijo1).attr("class") + "";
            var valor = clase;
            if (valor.indexOf("active")>=0) {
                $(obj).css("background-color",b_color[id_et].rmLabels.b_color_checked);
            };
        };
    };

};

var messageOtherSession = function(){
	$('#rm_contenido').html('');
	$('#rm_contenido').html('<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span>Se cerro la aplicacion porque su cuenta fue abierta en otro lugar.</div>');
}


var clearHtmlTags = function(){
	if (obj_clear_html!=null) {
		var _html = obj_clear_html.html();
		obj_clear_html.html('');
		obj_clear_html.html(sanitizeString(_html));
		obj_clear_html=null;
	}else{
		console.log('false')
	};
	
	// var strHtmlCode = e.html();
	// console.log(strHtmlCode);
	// strHtmlCode = strHtmlCode.replace(/&(lt|gt);/g,  
 //    function (strMatch, p1) {  
 //        return (p1 == "lt") ? "<" : ">";  
 //    });  
 //    var strTagStrippedText = strHtmlCode.replace(/<\/?[^>]+(>|$)/g, "");  
 //    return strTagStrippedText ;
}

var ALLOWED_TAGS = ["DIV","STRONG", "EM", "BLOCKQUOTE", "Q", "DEL", "INS", "A", "BR"];

var sanitizeString = function (string) {
    var div = document.createElement("div");
    div.innerHTML = string;
    sanitize(div);
    return div.innerHTML;
}

var sanitize = function (el) {
    // "Remove all tags from element `el' that aren't in the ALLOWED_TAGS list."
    var tags = Array.prototype.slice.apply(el.getElementsByTagName("*"), [0]);
    for (var i = 0; i < tags.length; i++) {
        if (ALLOWED_TAGS.indexOf(tags[i].nodeName) == -1) {
            usurp(tags[i]);
        }
    }
}

var usurp = function (p) {
    // "Replace parent `p' with its children.";
    var last = p;
    for (var i = p.childNodes.length - 1; i >= 0; i--) {
        var e = p.removeChild(p.childNodes[i]);
        p.parentNode.insertBefore(e, last);
        last = e;
    }
    p.parentNode.removeChild(p);
}

var myEvent = window.attachEvent || window.addEventListener;
var chkevent = window.attachEvent ? 'onbeforeunload' : 'beforeunload'; /// make IE7, IE8 compitable

myEvent(chkevent, function(e) { // For >=IE7, Chrome, Firefox
    var confirmationMessage = 'Ha intentado salir de esta pagina. Aun hay campos sin almacenar, si no guarda los cambios se perderan. Seguro que desea salir de esta pagina? ';  // a space
    if ($("#save").attr("disabled")=="disabled") {
		return true;
	}else{
		(e || window.event).returnValue = confirmationMessage;
    	return confirmationMessage;
	}
});