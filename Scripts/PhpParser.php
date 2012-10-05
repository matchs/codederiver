<?php

require 'Parser.php';

/**
 * @author Mateus Chagas contato@mateuschagas.com.br
 * @see http://www.phpcompiler.org/doc/latest/grammar.html 
 * @todo Parse commentary blocks
 */
class PhpParser extends Parser {

    protected $_phpBlock = '/(<\?php.*(\n.*?)*\?>)/';

    /**
     * Parses a file previously initialized
     *
     * @param SimpleXMLElement $xml
     * @param array $tpl 
     * @return String
     */
    public function parse() {
        $this->resetFileLines();
        foreach ($this->file as $line => $content) {
            if ($this->isAnotation($content)) {

                $this->emptyLine($line);

                $features = $this->getFeatures($content);


                $belongsToProduct = false;

                foreach ($features as $feature) {
                    if (array_search($feature, $this->features) === false) {
                        //echo "\n{$feature} doesn't belong to feature set";

                        $belongsToProduct = false;
                    } else {
                        //echo "\n{$feature} belongs to feature set";

                        $belongsToProduct = true;
                    }
                }

                if (!$belongsToProduct) {
                    $newline = $line + 1;
                    $this->stripUnusedContent($newline);
                    $this->resetFileLines();
                }
            }
        }

        return $this->assembleFile();
    }

    /**
     * Removes from file lines from the next statement block
     * 
     * @param int $line
     * @return bool
     */
    protected function stripUnusedContent($line) {
        
        /* Removing between php tags: <?php ?> */
        if ($this->isPhpOpeningTag($this->file[$line])) {
            for ($i = $line; isset($this->file[$i]); $i++) {
                if ($this->isPhpClosingTag($this->file[$i])) {
                    $this->emptyLine($i);
                    return true;
                } else {
                    $this->emptyLine($i);
                }
            }
        /* Remving a single line statement: $var = 'foo'; */
        } else if ($this->isSingleLineStatement($this->file[$line])) {
            $this->emptyLine($line);
            return true;
            
        /* Removing an entire block declaration statement. For example: A class declaration, a function declaration, a while block */    
        } else if ($this->isBlockDeclarationOpeningStatement($this->file[$line])) {
            
            /* Looking for block declaration brackets at the end of the current line or the beginning of the next line */
            if ((preg_match('/{$/', $this->file[$line]) > 0) || (preg_match('/^{/', $this->file[$line + 1]) > 0)) {
                return $this->removeBlock($line);

            /* Looking forward for a single line statement */
            } else if ($this->isSingleLineStatement($this->file[$line+1])) {
                $this->emptyLine($line);
                $this->emptyLine($line+1);
                return true;
            }
        }

        return false;
    }

    /**
     * Removes an entire block declaration
     *
     * @param int $line the block 1st line
     * @return bool
     */
    protected function removeBlock($line){
        /**
         *@var int $o_stack Openning brackets stack 
         */
        $o_stack = 0;
        
        /**
         *@var int $o_stack Closing brackets stack 
         */
        $c_stack = 0;
        
        for($i = $line; isset($this->file[$i]); $i++){
            $o_stack += $this->hasBlockOpeningBrackets($this->file[$i]);
            $c_stack += $this->hasBlockClosingBrackets($this->file[$i]);
            //echo "\n $o_stack : $c_stack " . $this->file[$i];
            $this->emptyLine($i);
            
            if(($o_stack == $c_stack) && ($o_stack > 0)){
                return true;
            }
        }
        return false;
        
    }
    
    /**
     * Asserts if a given string is a php closing tag : ?>
     * 
     * @param String $str
     * @return Bool
     */
    protected function isPhpClosingTag($str) {
        return preg_match('/\?>/', $str) > 0 ? true : false;
    }

    /**
     * Asserts if a given string is a php opening tag : <?php or <?
     * 
     * @param String $str
     * @return bool
     */
    protected function isPhpOpeningTag($str) {
        /* return preg_match('/(<\?php.*(\n.*?)*\?>)/', $str) > 0 ? true : false; */
        return preg_match('/(<\?php)|(<\?)/', $str) > 0 ? true : false;
    }

    protected function isSingleLineStatement($str) {
        return preg_match('/.*;\n/', $str) > 0 ? true : false;
    }

    protected function isBlockDeclarationOpeningStatement($str) {
        return preg_match('/.*[^;]\n/', $str) > 0 ? true : false;
    }

    protected function isBlockDeclarationClosingStatement($str) {
        return preg_match('/.*}\n/', $str) > 0 ? true : false;
    }

    /**
     * Counts the number of block opening brackets: {
     * 
     * @param type $str 
     * @return int The number of { 
     */
    protected function hasBlockOpeningBrackets($str){
        $x = preg_match('/{/', $str);
        return $x > 0 ? $x : 0;
    }
    
    /**
     * Counts the number of block opening brackets: }
     * 
     * @param type $str 
     * @return int The number of }
     */
    protected function hasBlockClosingBrackets($str){
        $x = preg_match('/}/', $str);
        return $x > 0 ? $x : 0;
    }
}
?> 
