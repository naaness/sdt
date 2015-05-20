
<?php if (isset($name_rm)) { ?>
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $name_rm; ?></h4>
    <span>Desde esta caja modal no es posible modificar el registro</span>
    </div>
<?php } ?>
<table id="sample" class="table table-hover" width="100%">
    <tbody>
    <?php if ($cont > 0) { ?>
        <?php foreach ($registries as $registry) { ?>
            <tr id="tr_<?php echo $registry->rmRegistries->id; ?>"  style="<?php if (($registry->rmRegistries->checked == 1)) { ?>background-color:#f1f1f1<?php } ?>">
                <td width="10px">
                    <div class="btn-group text-center" data-toggle="buttons">
                        <?php $color_fondo = $registry->rmLabels->b_color; ?>
                        <?php $active = ''; ?>
                        <?php if (($registry->rmRegistries->checked == 1)) { ?>
                            <?php $color_fondo = $registry->rmLabels->b_color_checked; ?>
                            <?php $active = 'active'; ?>
                        <?php } ?>
                        <label class="btn btn-default <?php echo $active; ?> chek">
                        </label>
                    </div>
                </td>
                <td>
                    <div style="float:left;">
                        <div class="numerar" style="margin-left:12px"><?php echo $registry->rmRegistries->numbering; ?></div>
                    </div>
                    <?php if (($registry->rmRegistries->rm_label_id == 0)) { ?>
                        <div class="conpizarron" style="margin-left:32px;color:#000000;background-color:transparent;border-radius:3px" name="0">
                            <div class="pizarron" style="margin-left:5px;font-family:Klavika;font-size:14px;word-break: break-all;" contenteditable="true"><?php echo $registry->rmRegistries->registry; ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="conpizarron" style="margin-left:32px;color:<?php echo $registry->rmLabels->color; ?>;background-color:<?php echo $color_fondo; ?>;border-radius:3px" name="<?php echo $registry->rmRegistries->rm_label_id; ?>">
                            <div class="pizarron" style="margin-left:5px;font-family:Klavika;font-size:14px;word-break: break-all;" contenteditable="true"><?php echo str_replace('||mas||', '+', str_replace('||igu||', '=', str_replace('||ans||', '&', $registry->rmRegistries->registry))); ?>
                            </div>
                        </div>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td width="10px">
                <div class="btn-group text-center" data-toggle="buttons">
                    <label class="btn btn-default chek">
                    </label>
                </div>
            </td>
            <td>
                <div style="float:left">
                    <div class="numerar" style="margin-left:12px">1</div>
                </div>
                <div class="conpizarron" style="margin-left:32px;border-radius:3px;color:#000000" name="0" >
                    <div class="pizarron" style="margin-left:5px;font-family:Klavika;font-size:14;font-weight:bold" contenteditable="true">
                    </div>
                </div>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div class="btn-group" id="rm_tool" style="display: none;">
    <button type="button" class="btn btn-default" id="rm_uplevel" >
        <span class="glyphicon glyphicon-chevron-left" ></span>
    </button>
    <button type="button" class="btn btn-default" id="rm_downlevel" >
        <span class="glyphicon glyphicon-chevron-right" ></span>
    </button>

    <button type="button" class="btn btn-default" id="rm_new_parraf" >
        <span class="glyphicon glyphicon-text-height" ></span>
    </button>
    <button type="button" class="btn btn-default" id="rm_to_htd" >
        <span class="glyphicon glyphicon-share-alt" ></span>
    </button>
    <select id="Setiqueta" class="btn btn-default">
        <option value="0">Por defecto</option>
        <?php foreach ($labels as $label) { ?>
            <option value="<?php echo $label->id; ?>"><?php echo $label->name; ?></option>
        <?php } ?>
    </select>
    <button type="button" class="btn btn-default" id="rm_eliminar" >
        <span class="glyphicon glyphicon-trash" ></span>
    </button>
</div>
