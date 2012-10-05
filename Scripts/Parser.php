<?php

abstract class Parser {

    protected $file = array();
    protected $features = array();
    static public $FILE_PATH = 'FP';
    static public $FILE_NAME = 'FN';
    static public $FILE_EXT = 'FE';
    static public $FILE_LANGS = 'FL';
    static public $FEATURES = 'FS';

    /**
     * 
     * @param SimpleXMLElement $xml
     * @param array $tpl
     * @return string 
     */
    abstract public function parse();

    /**
     * @param int $line
     */
    abstract protected function stripUnusedContent($line);

    /**
     * @return string 
     */
    protected function assembleFile() {
        $buffer = '';

        foreach ($this->file as $line) {
            $buffer .= trim($line, "\b");
        }

        return $buffer;
    }

    /**
     *
     * @param SimpleXMLElement $xml 
     */
    public function setFeatureSet(SimpleXMLElement $xml) {
        foreach ($xml->featureset->children() as $feature) {
            $f = (string) $feature->attributes()->id;

            if (!in_array($f, $this->features)) {
                $this->features[] = $f;
            }
        }
    }

    /**
     *
     * @param array $tpl 
     */
    public function setTemplateFile(array $tpl) {
        $this->file = $this->stripEmptyLines($tpl);
    }

    /**
     *
     * @param string $string
     * @return string 
     */
    protected function getFeatures($string) {
        $string = str_replace('@features', '', $string);
        return explode(' ', trim($string));
    }

    /**
     *
     * @param type $string 
     * @return Array
     */
    protected function getAnnotationParameters($string) {
        $string = preg_replace('/@[a-zA-Z0-9_]+\b/', '', $string);
        return explode(' ', trim($string));
    }

    /**
     *
     * @param array $tpl 
     * @return array
     */
    protected function stripEmptyLines(array $tpl) {
        $buffer = $tpl;

        foreach ($buffer as $k => $v) {
            if (strlen(trim($v)) <= 0) {
                unset($buffer[$k]);
            }
        }

        return $buffer;
    }

    /**
     *
     * @param String $str
     * @return Bool
     */
    protected function isDocBlock($str) {
        return preg_match('/\/\*{2}.*(\n.*?)*\//', $str) > 0 ? true : false;
    }

    /**
     *
     * @param String $str
     * @return Bool
     */
    protected function isAnnotation($str) {
        $str = trim($str);

        //return preg_match('/@features\b.*/', $str) > 0 ? true : false;
        return preg_match('/@[a-zA-Z0-9_]+\b.*/', $str) > 0 ? true : false;
    }

    /**
     *
     * @param String $str A annotation line
     * @return String A constant with the type of the annotation
     */
    protected function getAnnotationType($str) {
        $match = array();
        
        preg_match('/@[a-zA-Z0-9_]+\b/', $str, $match);
        
        $annotation = str_replace('@', '', $match[0]);
        
        switch($annotation){
            case 'file_path';
                return self::$FILE_PATH;
                break;
            case 'file_name';
                return self::$FILE_NAME;
                break;
            case 'file_ext';
                return self::$FILE_EXT;
                break;
            case 'file_langs';
                return self::$FILE_LANGS;
                break;
            case 'features';
                return self::$FEATURES;
                break;
        }
    }

    /**
     *
     * @param String $str
     * @return Bool
     */
    protected function isStatementBlock($str) {
        return preg_match('/[_a-zA-Z0-9$()-><!?=*\/ \t\n\'"]+[^{];/', $str) > 0 ? true : false;
    }

    /**
     *
     * @param String $str
     * @return Bool
     */
    protected function isKeyBlock($str) {
        //return preg_match('/[_a-zA-Z0-9$()-><!?=*\/ \t\n\'"]+[^{];/', $str) > 0 ? true : false;
    }

    /**
     * Resets the indexes of the file array 
     */
    protected function resetFileLines() {
        $this->file = array_merge(array(), $this->file);
    }

    /**
     * Embraces a line with commentary tag
     * @param Int $line index of the desired line
     */
    protected function embraceLineWithCommentary($line) {
        $this->file[$line] = "/*" . str_ireplace("\n", '', $this->file[$line]) . "*/\n";
    }

    /**
     * Replaces a line with an empty string
     * @param Int $line index of the desired line
     */
    protected function emptyLine($line) {
        $this->file[$line] = "";
    }

    /**
     * Asserts if a given string is a single line statement: $this->duh();
     * 
     * @param String $str
     * @return Bool
     */
    protected abstract function isSingleLineStatement($str);

    /**
     * Asserts if a given string is a block opening statment: public function duh(){  or public function duh()
     * 
     * @param String $str
     * @return Bool
     */
    protected abstract function isBlockDeclarationOpeningStatement($str);

    /**
     * Asserts if a given string is a block opening statment: }
     * 
     * @param String $str
     * @return Bool
     */
    protected abstract function isBlockDeclarationClosingStatement($str);
}
?> 
