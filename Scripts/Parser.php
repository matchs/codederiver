<?php

abstract class Parser {

    protected $file = array();    
    protected $features = array();
    

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
    protected function assembleFile(){
        $buffer = '';
        
        foreach($this->file as $line){
            $buffer .= trim($line,"\b");
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
    protected function getFeatures($string){
        $string = str_replace('@features', '', $string);
        return explode(' ', trim($string));
    }
    
    /**
     *
     * @param array $tpl 
     * @return array
     */
    protected function stripEmptyLines(array $tpl){
        $buffer = $tpl;
        
        foreach ($buffer as $k => $v){
            if(strlen(trim($v)) <= 0){
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
    protected function isAnotation($str) {
        return preg_match('/@features\b.*/', $str) > 0 ? true : false;
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
    protected function resetFileLines(){
        $this->file = array_merge(array(), $this->file);
    }
    
    /**
     * Embraces a line with commentary tag
     * @param Int $line index of the desired line
     */
    protected function embraceLineWithCommentary($line){
        $this->file[$line] = "/*" . str_ireplace("\n", '', $this->file[$line]). "*/\n";
    }
    
    /**
     * Replaces a line with an empty string
     * @param Int $line index of the desired line
     */
    protected function emptyLine($line){
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
