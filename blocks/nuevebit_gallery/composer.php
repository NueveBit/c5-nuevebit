<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$slideshowObj=$controller;

?>

<?php  $this->inc('/form_setup_html.php', array("fsIdField" => $this->field("fsID"), "typeField" => $this->field("type"), "imgFIDs" => $this->field("imgFIDs"))); ?> 
