<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define("VARIABLE_TYPE_INT", "integer");
define("VARIABLE_TYPE_FLOAT", "float");
define("VARIABLE_TYPE_STRING", "string");

class ActionVariable {

    private $type;
    private $name;
    private $optional;
    public  $value = NULL;
    private $sanitized = FALSE;
    
    public function __construct($type, $name, $optional = FALSE) {
        $this->type = $type;
        $this->name = $name;
        $this->optional = $optional;
    }
    
    private function sanitizeValue() {
        switch ($this->type) {
            case VARIABLE_TYPE_INT:
                $this->value = intval(filter_var($this->value, FILTER_SANITIZE_NUMBER_INT));
                break;
            case VARIABLE_TYPE_FLOAT:
                $this->value = floatval(filter_var($this->value, FILTER_SANITIZE_NUMBER_FLOAT));
                break;
            case VARIABLE_TYPE_STRING:
                $this->value = filter_var($this->value, FILTER_SANITIZE_STRING);
                break;

            default:
                //
        }

        $this->sanitized = TRUE;
        return $this->value;
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            if ($name == "value") {
                if (!$this->sanitized) {
                    $this->value = $this->sanitizeValue();
                }
            }

            return $this->$name;
        }
    }

}

abstract class Action {

    private $id;
    private $map;
    private $variables;
    private $controller;

    private $variablesMap = array(); // for fast access

    public function __construct($map) {
//        $this->id = $id;
        $this->map = $map;
        $this->variables = $this->getVariables();
    }

    public function getVariables() {
        return array();
    }

    public function setController($controller) {
        $this->controller = $controller;
    }

    public abstract function execute();

    public function matches() {
        $match = TRUE;

        foreach ($this->variables as $variable) {
            $this->variablesMap[$variable->name] = $variable;
            
            $param = $this->map[$variable->name];
            $optional = $variable->optional;

            if (isset($param) && $param != "") {
                $variable->value = $param;
            } else if (!$optional) {
                $match = FALSE;
                break;
            }
        }

        return $match;
    }

    public function getRequestMap() {
        return $this->map;
    }

    public function __get($name) {
        if (isset($this->variablesMap[$name])) {
            return $this->variablesMap[$name]->value;
        }
    }

    public function __isset($name) {
        return isset($this->variablesMap[$name]->value);
    }

}

abstract class PostAction extends Action {

    public function __construct() {
        parent::__construct($_POST);
    }

}

abstract class GetAction extends Action {

    public function __construct() {
        parent::__construct($_GET);
    }
}

abstract class RequestAction extends Action {

    public function __construct() {
        parent::__construct($_REQUEST);
    }
}

class ActionController {

    private $actions = array();
    private $currentActionId = NULL;

    public function addAction($id, $action) {
        $this->actions[$id] = $action;
        $action->setController($this);
    }

    public function execute() {
        $result = NULL;

        foreach ($this->actions as $id => $action) {
            if ($this->isActionRequested($id, $action) && $action->matches()) {
                $this->currentActionId = $id;
                $result = $action->execute();
                break;
            }
        }

        return $result;
    }

    private function isActionRequested($id, $action) {
        $actionMap = $action->getRequestMap();
        $requested = isset($actionMap["action"]) && $actionMap["action"] == $id;

//        Log::addEntry("action = " . $id);
//        Log::addEntry("requested: " . $requested);

        return $requested;

    }

    public function __get($name) {
        if ($name == "currentAction" && isset($this->currentActionId)) {
            return $this->actions[$this->currentActionId];
        } else if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

}

?>
