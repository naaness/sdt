/**
 * Created by nesto_000 on 3/05/15.
 */
$(document).ready(function() {
    $('#users_ids').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        buttonWidth: '100%',
        maxHeight: 200,
        selectAllText: 'Seleccionar todos',
        filterPlaceholder: 'Buscar usuario...',
        buttonText: function(options, select) {
            if (options.length === 0) {
                return 'Ningun usuario seleccionado...';
            }
            else if (options.length > 4) {
                return 'Mas de 4 usuarios selecionados';
            }
            else {
                var labels = [];
                options.each(function() {
                    if ($(this).attr('label') !== undefined) {
                        labels.push($(this).attr('label'));
                    }
                    else {
                        labels.push($(this).html());
                    }
                });
                return labels.join(', ') + '';
            }
        }
    });
});