

var inicioFiltroCheckList  = function (){
	// Crear filtro para el checklist
	var FProyectos = [];
	var FResponsables = [];
	var FEstados = [];
    var p_siglas = $("#p_sigla");
    var p_priority = $("#filter_priority");
    var p_respon = $("#p_respon");
    var p_estado = $("#p_estado");
    var p_direct = $("#p_direct");
    var p_textTa = $("#p_textTa");

    $(p_respon).tooltip({
		template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	});
	$(p_estado).tooltip({
		template: '<div class="tooltip" style="width: 120px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	});
	$(p_direct).tooltip({
		template: '<div class="tooltip" style="width: 120px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	});
	$(p_textTa).tooltip({
		template: '<div class="tooltip" style="width: 125px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	});
	$("#tbl").find("tr").each(function (index, element) {
		// Crear un listado proyectos
		var dragRow = $(element).find(".row_ch");
		if (dragRow.length==0) {
			dragRow = $(element).find("div[class^='nodrag row']");
		};
		var colum1 = dragRow.text();
		if (jQuery.inArray(colum1, FProyectos)==-1) {
			FProyectos.push(colum1);
			var opcion = $('<option value="'+colum1+'">'+colum1+'</option>');
			p_siglas.append(opcion);
		};
		// Crear Lista de responsables
		var colum2 = $(element).find("span[id^='re_']");
		//  tooltio

		var colum2_t = $(colum2).attr("title");
		var valorTitle ="";
		// For some browsers, `attr` is undefined; for others,
		// `attr` is false.  Check for both.
		if (typeof colum2_t !== typeof undefined && colum2_t !== false) {
		    valorTitle=colum2_t.trim();
		}

		if (jQuery.inArray(valorTitle, FResponsables)==-1) {
			FResponsables.push(valorTitle);
			var opcion = $('<option value="'+FResponsables.length+'">'+valorTitle+'</option>');
			p_respon.append(opcion);
		}
		$(colum2).tooltip();
		// Crear Lista de estados de la tarea
		var clase_2 = $(colum2).attr("class");
		var valor="";
		var valorText="";
		if (clase_2) {
			if (clase_2.indexOf("success")>-1) {
				valor="success";
				valorText="Activo";
			}else if (clase_2.indexOf("primary")>-1) {
				valor="primary";
				valorText="Espera";
			}else if (clase_2.indexOf("warning")>-1) {
				valor="warning";
				valorText="Vista";
			}
			if (jQuery.inArray(valor.trim(), FEstados)==-1) {
				FEstados.push(valor.trim());
				var opcion = $('<option value="'+valor+'">'+valorText+'</option>');
				p_estado.append(opcion);
			}
		};
	});
	ordenarSelect("p_sigla");

	// Filtrar en la columna siglas del proyecto
	p_siglas.change(function(){
        console.log('aaaaaaaaaaaaa');
        doFilterChecklist();
	});

    p_priority.change(function(){
        console.log('asdasdasdasd');
        doFilterChecklist();
    });
	p_respon.change(function(){
        doFilterChecklist();
	});
	p_estado.change(function(){
        doFilterChecklist();
	});
	p_direct.change(function(){
        doFilterChecklist();
	});
	p_textTa.keyup(function(){
        doFilterChecklist();
	});
	
	$('#ch_content').on('change', ".filtro_dia", function(e){
        doFilterChecklist();
		$(this).attr("data-original-title","Has filtrado por " + $(this).find("option:selected").text());
	});

}
var doFilterChecklist = function(){
    var p_siglas = $("#p_sigla");
    var p_priority = $("#filter_priority");
    var p_respon = $("#p_respon");
    var p_estado = $("#p_estado");
    var p_direct = $("#p_direct");
    var p_textTa = $("#p_textTa");
    var texto_c1 = $(p_siglas).find("option:selected").text();
    var texto_c2 = $(p_respon).find("option:selected").text();
    var texto_c3 = $(p_estado).val();
    var texto_c4 = $(p_direct).find("option:selected").text();
    var texto_c5 = $(p_textTa).val();
    var texto_c6 = $(p_priority).val();
    console.log(texto_c6)
    doFiltrer(texto_c1,texto_c2,texto_c3,texto_c4,texto_c5,texto_c6);
}
var ordenarSelect = function (id_componente)
{
  var selectToSort = jQuery('#' + id_componente);
  var optionActual = selectToSort.val();
  selectToSort.html(selectToSort.children('option').sort(function (a, b) {
    return a.text === b.text ? 0 : a.text < b.text ? -1 : 1;
  })).val(optionActual);
}
var doFiltrer = function (t_c1,t_c2,t_c3,t_c4,t_c5,t_c6){
	t_c2="Todo";
	t_c3="0";
	t_c4="Todo";
	t_c5= t_c5.toLowerCase();
	$("#tbl").find("tr").each(function (index, element) {
		var visible=true;
		if (t_c1=="Todo") {
		}else{
			var dragRow = $(element).find(".row_ch");
			if (dragRow.length==0) {
				dragRow = $(element).find("div[class^='nodrag row']");
			};
			var colum1 = dragRow.text();
			if (colum1==t_c1) {
			}else{
				visible=false;
			}
		};
        if (t_c6=="Todo") {
        }else{
            var dragRow = $(element).find(".fijar3");
            var class_cell = dragRow.attr("class");
            if(class_cell.indexOf(class_follow_up[t_c6])==-1){
                visible=false;
            }
        };
		
		var colum2 = $(element).find("span[id^='re_']");
		var colum2_t = $(colum2).attr("data-original-title");
		var valorTitle ="";
		// For some browsers, `attr` is undefined; for others,
		// `attr` is false.  Check for both.
		if (typeof colum2_t !== typeof undefined && colum2_t !== false) {
		    valorTitle=colum2_t.trim();
		}
		if (t_c2=="Todo") {
		}else{
			if (valorTitle==t_c2) {
			}else{
				visible=false;
			}
		};
		if (t_c3=="0") {
		}else{
			if ($(colum2).attr("class")) {
				if ($(colum2).attr("class").indexOf(t_c3)>-1) {
				}else{
					visible=false;
				}
			}else{
				visible=false;
			}
		};
		if (t_c4=="Todo") {
		}else{
			var estrella = $(element).find("div[class^='glyphicon glyphicon-star']");
			if ($(estrella).length) {
			}else{
				visible=false;
			}
		}
		if (t_c5=="") {
		}else{
			var texto = $(element).find(".ch-name-task").text().toLowerCase();
			if (texto.indexOf(t_c5)>-1) {
			}else{
				visible=false;
			}
		}
		var id = $(element).attr("id");
	    id = id.slice(3,id.length);
	    $(element).find(".filter-day").each(function (index2, element2) {
	    	var id2 = $(element2).attr("id");
	    	id2 = id2.split("_");
	    	var valor = $("#p_d_"+id2[2]).val();
	    	if (valor=="Todo") {
			}else{
				var tiempo = $(element2).find("span[class^='glyphicon glyphicon']");
				if ($(tiempo).length) {
					tiempo = tiempo.attr("class");
					if (tiempo.indexOf(valor)>-1) {
					}else{
						visible=false;
					}
				}else{
					visible=false;
				};
			};
	    });
		if (visible) {
            $(element).css('display','table-row');
		}else{
			$(element).css('display','none');
		}
	});
}