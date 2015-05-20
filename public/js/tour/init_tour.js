/**
 * Created by nesto_000 on 5/05/15.
 */
$(document).ready(function(){
    var $demo, duration, remaining, tour=null, id_task, id_ut;
    $demo = $("#demo");
    duration = 5000;
    remaining = duration;

    $("#demo").click(function(){
        var tr_obj = $("#ch-body").find('tr:eq(0)');
        id_task = tr_obj.attr('id');
        id_task = id_task.split('_');
        id_task = id_task[1];
        var ut_obj = tr_obj.find('.orange:eq(0)');
        id_ut = ut_obj.parent().attr("id");
        $.localStorage.removeItem('tour_current_step');
        if(!tour){
            tour = new Tour({
                onStart: function() {
                    return $demo.addClass("disabled", true);
                },
                onEnd: function() {
                    return $demo.removeClass("disabled", true);
                    $.localStorage.removeItem('tour_current_step');
                },
                debug: true,
                steps: [
                    {
                        path: "/checklist",
                        element: "#demo",
                        placement: "bottom",
                        title: "Bienvenido al tutorial de Inicio!",
                        content: "Introducción a la Herramienta Checklist paso a paso. Vamos a conocer la ventana principal."
                    }, {
                        path: "/checklist",
                        element: "#ch-title",
                        placement: "bottom",
                        title: "Titulo del Checklist",
                        content: "Este es el titulo del checklist. De esta forma podras saber que informacion esta mostrando según el filtro usado.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#goToType-type0",
                        placement: "bottom",
                        title: "Filtro: Tareas Pendientes",
                        content: "Se mostraran las tareas que tienen alguna actividad pendientes o sin resolver hasta el dia de hoy, .",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#goToType-type1",
                        placement: "bottom",
                        title: "Filtro: Checklist General",
                        content: "Se mostraran todas las tareas, ya sea provenientes de un checklist, proyectos, tareas personales o delegados",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#goToType-type2",
                        placement: "bottom",
                        title: "Filtro: Mis Proyectos",
                        content: "Se mostraran todas las tareas de los proyectos que actualmente lidero.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#goToType-type3",
                        placement: "bottom",
                        title: "Filtro: Proyectos",
                        content: "Se mostraran todas las tareas de los proyectos que tengo alguna participacion, ya sea liderandolo o que fueron asignadas a mi.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#goToType-type4",
                        placement: "bottom",
                        title: "Filtro: Delegadas a Mi",
                        content: "Se mostraran todas las tareas que algún otro usuario me delegó.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#goToType-type5",
                        placement: "bottom",
                        title: "Filtro: Mis delegadas",
                        content: "Se mostraran todas las tareas que yo delegue a otro usuario.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#goToType-type6",
                        placement: "bottom",
                        title: "Filtro: Mis Tareas",
                        content: "Se mostraran todas las tareas que tengo que realizar.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#ch-name-range",
                        placement: "bottom",
                        title: "Rango de Tiempo",
                        content: "Es el rango de tiempo visualizado en el checklist, puede ser el número de la Semana en el año, el nombre del Mes o el numero del Trimestre en el año .",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#rangoVista",
                        placement: "bottom",
                        title: "Cambiar Rango de Tiempo (1)",
                        content: "De click en el selector. Notemos que tenemos las opciones : Semana, Mes y Trimestre.",
                        reflex: true,
                        onShown: function (tour) {
                            $(".popover.tour-tour .popover-navigation .btn-group .btn[data-role=next]").prop("disabled", true);
                        }
                    }, {
                        path: "/checklist",
                        element: "#rangoVista",
                        placement: "top",
                        title: "Cambiar Rango de Tiempo (2)",
                        content: "Se puede selecionar que rango de tiempo queremos ver en nuestro checklist.",
                        backdrop: true

                    }, {
                        path: "/checklist",
                        element: "#ch_btn_left",
                        placement: "bottom",
                        title: "Cambiar Rango de Tiempo Anterior",
                        content: "Este boton permite retroceder en una semana, mes o trimestre anterior el rango de tiempo.",
                        backdrop: true

                    }, {
                        path: "/checklist",
                        element: "#ch_btn_right",
                        placement: "bottom",
                        title: "Cambiar Rango de Tiempo Siguiente",
                        content: "Este boton permite avanzar en una semana, mes o trimestre posterior el rango de tiempo.",
                        backdrop: true

                    }, {
                        path: "/checklist",
                        element: "#ch_btn_hoy",
                        placement: "bottom",
                        title: "Ir a dia de Hoy",
                        content: "Este boton permite ir dia de hoy, el dia estara en el rango de tiempo selecionado en el checklist y se reconoce como una columna gris.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-1",
                        placement: "bottom",
                        title: "Modo : Normal",
                        content: "Cuando el Checklist esta en modo Normal, el comportamiento en las unidades de tiempo al dar click, sera para realizar su respectivo seguimiento.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-2",
                        placement: "bottom",
                        title: "Modo : Agregar (1)",
                        content: "Cuando el Checklist esta en modo Agregar, si da click sobre cualquier recuadro dentro de las celdas de tiempo en el checklist, agregara una unidad de tiempo.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-2",
                        placement: "bottom",
                        title: "Modo : Agregar (2)",
                        content: "De click en el boton Agregar.",
                        reflex: true,
                        onShown: function (tour) {
                            $(".popover.tour-tour .popover-navigation .btn-group .btn[data-role=next]").prop("disabled", true);
                        }
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-2",
                        placement: "bottom",
                        title: "Modo : Agregar (3)",
                        content: "Notara un halo Verde alrededor del cheklist, esta es la forma que indentificará visualmente que el checklist esta en modo Agregar.",
                        onNext: function(){
                            $("#ch_content").removeClass('mode-add');
                            $("#ch_content").removeClass('mode-delete');
                            $("#ch_content").removeClass('mode-move');
                        }
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-3",
                        placement: "bottom",
                        title: "Modo : Eliminar (1)",
                        content: "Cuando el Checklist esta en modo Eliminar, si da click sobre una unidad de tiempo, esta se eliminará.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-3",
                        placement: "bottom",
                        title: "Modo : Eliminar (2)",
                        content: "De click en el boton Eliminar.",
                        reflex: true,
                        onShown: function (tour) {
                            $(".popover.tour-tour .popover-navigation .btn-group .btn[data-role=next]").prop("disabled", true);
                        }
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-3",
                        placement: "bottom",
                        title: "Modo : Eliminar (3)",
                        content: "Notara un halo Rojo alrededor del cheklist, esta es la forma que indentificará visualmente que el checklist esta en modo Eliminar.",
                        onNext: function(){
                            $("#ch_content").removeClass('mode-add');
                            $("#ch_content").removeClass('mode-delete');
                            $("#ch_content").removeClass('mode-move');
                        }
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-4",
                        placement: "bottom",
                        title: "Modo : Mover (1)",
                        content: "Cuando el Checklist esta en modo Mover, si da click sobre una unidad de tiempo, y luego en otra celda del checklist, esta unidad de tiempo se desplazará hacia dicha celda, asi podra cambiar de dia.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-4",
                        placement: "bottom",
                        title: "Modo : Mover (2)",
                        content: "De click en el boton Mover.",
                        reflex: true,
                        onShown: function (tour) {
                            $(".popover.tour-tour .popover-navigation .btn-group .btn[data-role=next]").prop("disabled", true);
                        }
                    }, {
                        path: "/checklist",
                        element: "#ch-mode-4",
                        placement: "bottom",
                        title: "Modo : Mover (3)",
                        content: "Notara un halo Amarillo alrededor del cheklist, esta es la forma que indentificará visualmente que el checklist esta en modo Mover.",
                        onNext: function(){
                            $("#ch_content").removeClass('mode-add');
                            $("#ch_content").removeClass('mode-delete');
                            $("#ch_content").removeClass('mode-move');
                        }
                    }, {
                        path: "/checklist",
                        element: "#btn_add_task",
                        placement: "bottom",
                        title: "Agregar Tarea Dinamicamente",
                        content: "Este boton permite crear una tarea de forma rápida, permitiendo ser asignada a un proyecto y delegar a otro usuario.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#p_sigla",
                        placement: "right",
                        title: "Filtro : Siglas",
                        content: "Permite filtrar por las siglas que aparecen en esta columna.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#p_textTa",
                        placement: "bottom",
                        title: "Filtro : Nombre de la Tarea",
                        content: "Permite filtrar por el nombre de la tarea, tan solo con incluir unas pocas letras buscara las coincidencias.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#filter_priority",
                        placement: "bottom",
                        title: "Filtro : Prioridad",
                        content: "Permite filtrar las tareas según su nivel de prioridad.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#p_d_5",
                        placement: "left",
                        title: "Filtros : Estado del Dia",
                        content: "Permite filtrar todas las tareas en este dia por sus estado o seguimiento. ",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#tr_"+id_task,
                        placement: "bottom",
                        title: "Tarea",
                        content: "Esta fila representa una tarea",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#info_"+id_task,
                        placement: "bottom",
                        title: "Informacion de la Tarea",
                        content: "Se muestra detalles del contenido de la tarea, como su descripcion, proyecto al que pertenece, etc.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#chat_"+id_task,
                        placement: "bottom",
                        title: "Chat de la tarea (1)",
                        content: "Todos los usuarios involucrados en la tarea, podra realizar comentarios, de esta forma se llevara un registro.",
                        backdrop: true
                    }, {
                        path: "/checklist",
                        element: "#chat_"+id_task,
                        placement: "bottom",
                        title: "Chat de la tarea (2)",
                        content: "Cuando el icono toma el color azul, es porque tiene mensajes.",
                        onPrev : function(){
                            $("#chat_"+id_task).removeClass('chat-msgs');
                        },
                        onShow : function(){
                            $("#chat_"+id_task).addClass('chat-msgs');
                        },
                        onNext: function(){
                            $("#chat_"+id_task).removeClass('chat-msgs');
                        }
                    }, {
                        path: "/checklist",
                        element: "#chat_"+id_task,
                        placement: "bottom",
                        title: "Chat de la tarea (3)",
                        content: "Cuando tienes nuevos mensajes el icono se mostrara de esta forma animada y en rojo.",
                        onPrev : function(){
                            $("#chat_"+id_task).removeClass('chat-news');
                        },
                        onShow : function(){
                            $("#chat_"+id_task).addClass('chat-news');
                        },
                        onNext: function(){
                            $("#chat_"+id_task).removeClass('chat-news');
                        }
                    }, {
                        path: "/checklist",
                        element: "#"+id_ut,
                        placement: "top",
                        title: "Unidad de Tiempo",
                        content: "En esta celda se pude ver las unidades de tiempo correspondientes a la tarea (fila) y dia (columna).",
                        backdrop: true
                    }
                ]
            });

            if (tour.ended()) {
                $('<div class="alert alert-info alert-dismissable"><button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You ended the demo tour. <a href="#" data-demo>Restart the demo tour.</a></div>').prependTo(".content").alert();
            }
        }
        // $.localStorage.removeItem('tour_current_step');

        $.localStorage.removeItem('tour_end');
        tour.init();
        tour.start();
    });

});