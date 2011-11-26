<?php

/**
 * Autoloader klas
 *
 * @package Core
 * @author Wookieb
 */
class Autoloader {

    /**
     * Tablica ścieżek aktualnie znajdujących się w include_path
     *
     * @var array
     */
    private static $_includePaths = array();

    public static function init() {
        self::$_includePaths = explode(PATH_SEPARATOR, get_include_path());
    }

    /**
     * Sprawdza czy klasa o podanej nazwie istnieje.
     * Jeżeli tak zwracana jest ścieżka do klasy
     *
     * @param string $name nazwa klasy
     * @return string null gdy nie znaleziono pliku
     */
    public static function classExists($name) {
        $classFilename = str_replace('_', '/', $name) . '.php';
        foreach (self::$_includePaths as $path) {
            if (file_exists($path . '/' . $classFilename))
                return $path . '/' . $classFilename;
        }
    }

    /**
     * Ładuje klasę o podanej nazwie jeżeli istnieje
     *
     * @param string $name nazwa klasy
     */
    public static function load($name) {
        $path = self::classExists($name);
        if (!$path) {
            throw new MissingFileException($name . '.php', 'No class "' . $name . '" file');
        }
        require_once $path;
    }

}