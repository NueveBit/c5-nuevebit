<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AttributesRenderHelper {

    public function renderSelect($label, $attributeKey) {
        $akController = $attributeKey->getController();
        $options = $akController->getOptions();

        $selectedOptions = $akController->request('atSelectOptionID');
        if (!is_array($selectedOptions)) {
            $selectedOptions = array();
        }

        $field = $akController->field("atSelectOptionID") . "[]";

        echo "<div class='select-wrapper'>";
        echo "<span class='choose'>$label</span>";
        echo "<ul>";

        foreach ($options as $opt) {
            $id = $opt->getSelectAttributeOptionID();
            $value = $opt->getSelectAttributeOptionValue();
            $selected = in_array($id, $selectedOptions) ? "checked" : "";
            $markupId = "attribute-select-id-" . $id;

            echo "<li>";
            echo "<input type='radio' name='$field' value='$id' id='$markupId' $selected/>";
            echo "<label for='$markupId'>$value</label>";
            echo "</li>";
        }

        echo "</ul>";
        echo "</div>";
        
    }

}

?>
