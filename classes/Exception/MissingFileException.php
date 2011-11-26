<?php

/**
 * Wyjątek gdy nie ma pliku
 * @author wookieb
 */
class MissingFileException extends RuntimeException {

    public $missedfile;
    public $cwd;

    public function __construct($file, $message, $code = null, $previous = null) {
        $this->missedfile = $file;
        $this->cwd = getcwd();
        $msg = 'Missing file "' . $file . '". Actual cwd: ' . $this->cwd . '. Message: ' . $message;
        parent::__construct($msg, $code, $previous);
    }

}

