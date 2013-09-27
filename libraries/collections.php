<?php

/**
 * Interfaz principal para declarar colecciones. Una colección representa
 * un grupo de elementos.
 *
 * @author emerino
 */
interface ICollection extends ArrayAccess, IteratorAggregate, Countable {
    
    /**
     * Añade un elemento a la colección
     * 
     * @param mixed $element 
     */
    function add($element);

    /**
     * Verifica si el elemento dado se encuentra en esta colección
     *
     * @param mixed $element
     * @return boolean True si el elemento se encuentra en la colección,
     *                  False de otro modo
     */
    function contains($element);

    /**
     * Verifica si esta colección se encuentra vacía
     *
     * @return boolean True si se encuentra vacía, False de otro modo
     */
    function isEmpty();

    /**
     * Elimina el elemento dado de la colección
     *
     * @param mixed $element
     */
    function remove($element);

    /**
     * Elimina todos los elementos que contenga la colección
     *
     */
    function clear();

    /**
     * Devuelve el número total de elementos en esta colección
     *
     * @return int El tamaño de esta colección
     */
    function getLength();

    function getNativeArray();

}

/**
 * Un Sequence contiene una secuencia ordenada de elementos.
 *
 * @author emerino
 */
interface Sequence extends ICollection {

    function get($index);

    function insert($index, $element, $swap = true);
}

/**
 * Un mapa (arreglo asociativo) es una estructura que utiliza colecciones para
 * guardar su contenido mediante llaves asociadas a objetos.
 *
 * @author emerino
 */
interface Map extends ArrayAccess, IteratorAggregate, Countable {

    /**
     * Agrega un par id/objeto al mapa
     *
     * @param mixed $key id
     * @param mixed $value objeto
     */
    function put($key, $value);
    
    /**
     * Devuelve el objeto asociado a la id dada
     *  
     * @param mixed $key
     * @return mixed
     */
    function get($key);
    
    /**
     * Elimina el objeto dado de este mapa
     * 
     * @param mixed $value
     */
    function remove($value);
    
    /**
     * Devuelve un Set con las ids de este mapa
     * 
     * TODO: Implementar un Set para atrapar el arreglo
     * 
     * @return array
     */
    function keySet();

    /**
     * Devuelve una Sequence con los objetos que contiene este mapa
     *
     * @return Sequence La colección con los valores de este mapa
     */
    function values();

    /**
     * Verifica si el id dado existe en este mapa
     *
     * @param mixed $key
     * @return boolean True si está, False de otro modo
     */
    function containsKey($key);

    /**
     * Verfica si el valor existe en este mapa
     * @param mixed $value
     * @return boolean True si está, False de otro modo
     */
    function containsValue($value);

    /**
     * Verifica si el contenido de este mapa está vacío
     *
     * @return boolean True si está vacío, False de otro modo
     */
    function isEmpty();

    /**
     * Devuelve el tamaño de este mapa
     *
     * @return int El tamaño del mapa
     */
    function getLength();

    function getNativeArray();

}

/**
 * Implementación concreta de un Sequence.
 *
 * @author emerino
 */
class ArrayList implements Sequence {

    /**
     * Elementos de la secuencia.
     *
     * @var array
     */
    private $elements;

    function __construct(array &$elements = NULL) {
        if (isset ($elements)) {
            $this->elements = $elements;
        } else {
            $this->elements = array();
        }
    }

    public final function add($element) {
        $index = $this->getLength();
        $this->insert($index, $element);

        return $this;
    }

    public function insert($index, $element, $swap = true) {
        if (!isset ($element)) {
            throw new Exception("Element is not set");
        }

        if ($index < 0) {
            throw new Exception("Index must be greater than 0");
        }

        if ($index > $this->getLength()) {
            $index = $this->getLength();
        } elseif ($index < $this->getLength() && $swap) {
            $this->swap($index, $this->getLength());
        }

        $this->elements[$index] = $element;
    }

    private function swap($start, $end) {
        $aux = array_slice($this->elements, $start, $end);
        array_splice($this->elements, $start, $end);
        $this->elements[$start] = NULL;

        foreach ($aux as $e) {
            $this->elements[] = $e;
        }
    }

    public function get($index) {
        if ($index > $this->getLength() || $index < 0) {
            throw new Exception("Invalid index given");
        }

        return $this->elements[$index];
    }

    public function contains($element) {
        if (!isset ($element)) {
            throw new Exception("Element is not set");
        }

        return in_array($element, $this->elements, true);
    }

    public function clear() {
		$this->elements = array();
    }

    public function isEmpty() {
        return $this->getLength() == 0;
    }

    public function remove($element) {
        $index = array_search($element, $this->elements);

        if ($index !== false) {
            array_splice($this->elements, $index, 1);
        }

        return $index;
    }

    public function getNativeArray() {
        return $this->elements;
    }

    public function getLength() {
        return count($this->elements);
    }

	public function offsetSet($offset, $value) {
		$this->elements[$offset] = $value;
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetExists($offset) {
		return isset ($this->elements[$offset]);
	}

	public function offsetUnset($offset) {
		$this->remove($this->elements[$offset]);
	}

    public function count() {
        return $this->getLength();
    }

    public function getIterator() {
        return new ArrayIterator($this->elements);
    }

}

/**
 * Description of HashMap
 *
 * @author emerino
 */
class HashMap implements Map {
    
    /**
     * Arreglo asociativo 
     * 
     * @var array
     */
    private $map;

    function __construct(array &$map = NULL) {
        if (isset ($map)) {
            $this->map = $map;
        } else {
            $this->map = array();
        }
    }

    public function put($key, $value) {
        $this->map[$key] = $value;
    }

    public function get($key) {
        $object = NULL;

        if (array_key_exists($key, $this->map)) {
            $object = $this->map[$key];
        }

        return $object;
    }

    public function keySet() {
        return array_keys($this->map);
    }

    public function values() {
        return new ArrayList(array_values($this->map));
    }

    public function containsKey($key) {
        return array_key_exists($key, $this->map);
    }

    public function containsValue($value) {
        return array_search($value, $this->map);
    }

    public function getNativeArray() {
        return $this->map;
    }

    public function getLength() {
        return count($this->map);
    }

    public function count() {
        return $this->getLength();
    }

    public function isEmpty() {
        return (count($this->map) == 0);
    }

	public function offsetSet($offset, $value) {
		$this->map[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset ($this->map[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->map[$offset]);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

    public function getIterator() {
        return new ArrayIterator($this->map);
    }

    public function remove($key) {
        if (array_key_exists($key, $this->map)) {
            unset($this->map[$key]);
        }
        /*
        $key = array_search($value, $this->map);

        if ($key != NULL) {
            unset($this->map[$key]);
        }
         * 
         */
    }

	public function clear() {
		$this->map = array();
	}
}

?>
