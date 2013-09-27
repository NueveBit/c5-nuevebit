<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$form = Loader::helper("form");
$dt = Loader::helper("form/date_time");
$html = Loader::helper("html");
?>
<style type="text/css">
.file-set-row {margin-bottom:16px;clear:both;padding:7px;background-color:#eee}
</style>

<script id="file-set-row-template" type="text/x-handlebars-template">
<?php 
    $this->inc('fileset_row_include.php', array(
        'id' => "{{id}}", "name" => "{{name}}", "showThumbnail" => "{{showThumbnail}}", "date" => "{{date}}")
    ); 
?>
</script>

<?php echo $html->javascript("handlebars.js", "nuevebit"); ?>

<script type="text/javascript">
    $(function() {
        nuevebit.GalleryBlock.init();

        var dateInput = $("#file-sets-date");
        dateInput.datepicker("option", "changeMonth", true);
        dateInput.datepicker("option", "changeYear", true);
        dateInput.datepicker("option", "showButtonPanel", true);
        dateInput.datepicker("option", "dateFormat", "MM yy");
        dateInput.datepicker("option", "onClose", function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        });
    });

</script>

<style type="text/css">
    .ui-datepicker-calendar {
        display: none;
    }
</style>

<div id="file-sets-add">
    <?php
    $sets= array(0 => "Seleccionar...");
    foreach ($fileSets as $set) {
        $sets[$set->fsID] = $set->fsName;
    }
    
    echo $form->label("file-sets-select", t("File set: "));
    echo $form->select("file-sets-select", $sets, 0);
    
    echo $form->label("file-sets-date", "Fecha: ");
    echo $dt->date("file-sets-date", date("m/d/Y", time()), true);
    
    echo $form->label("file-sets-show-thumbnail", "Mostrar miniatura: ");
    echo $form->checkbox("file-sets-show-thumbnail", 1, true);
    ?>

    <button id="file-sets-add-button" type="button" value="Agregar"></button>
</div>

<div id="file-sets-container">
<?php  
foreach($selectedFileSets as $set){ 
    $this->inc('fileset_row_include.php', array(
        'id' => $set["fsID"], "name" => $set["fsName"],
        "showThumbnail" => $set["showThumbnail"] == 1 ? "true" : "false", "date" => $set["sdate"]
    ));
}
?>
</div>