<?php

require 'PhpParser.php';

/**
 * @author Mateus
 * @todo Read configurations from a configuration file
 */
class CodeGen {

    private $file_ext = 'php';

    /**
     * Enables verbose mode for messages
     * @var Boolean
     */
    private $verbose_mode = false;

    /**
     * Template source folder
     * @var String
     */
    protected $tpl_folder;

    /**
     * Products XML folder
     * @var String
     */
    protected $prd_folder;

    /**
     * Configurations folder
     * @var String
     */
    protected $cfg_folder;

    /**
     * Folder where the final source code is going to be generated
     * @var String
     */
    protected $gen_folder;

    /**
     * SimpleXMLElement array. The products xml set;
     * @var Array
     */
    protected $_prdXML = array();

    /**
     * Template files
     * @var Array
     */
    protected $_tplFiles = array();
    protected $_parser;

    protected function init() {

        $this->tpl_folder = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Templates');
        $this->prd_folder = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Products');
        $this->cfg_folder = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Config');
        $this->gen_folder = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Generated');
        $this->_parser = new PhpParser();
    }

    protected function loadProductsXML() {
        echo "\n---------- Loading products' XML ---------- ";

        try {
            $files = scandir($this->prd_folder);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        foreach ($files as $i => $filename) {
            if ($this->isXML($filename)) {
                $path = $this->prd_folder . DIRECTORY_SEPARATOR . $filename;

                if ($xml = simplexml_load_file($path)) {
                    $attr = (string) $xml->attributes()->id;
                    $this->_prdXML[$attr] = $xml;

                    $this->verbose_mode == true ? print($filename . " loaded OK\n")  : '';
                } else {
                    echo $filename . " loaded FAIL";
                }
            }
        }

        if (count($this->_prdXML) > 0) {
            echo "Successfully loaded " . count($this->_prdXML) . " product(s)";
        } else {
            echo "\nNot a single product was loaded. Please check your Products' Folder\n";
        }
    }

    protected function loadTemplates() {
        echo "\n\n---------- Loading teamplate files ---------- ";

        try {
            $files = scandir($this->tpl_folder);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        foreach ($files as $i => $filename) {
            if ($this->isTPL($filename)) {
                //$path = $this->tpl_folder . DIRECTORY_SEPARATOR . $filename;

                $this->_tplFiles[] = $filename;

                $this->verbose_mode == true ? print($filename . " loaded OK\n")  : '';
            }
        }

        if (count($this->_tplFiles) > 0) {
            echo "Successfully loaded " . count($this->_tplFiles) . " template(s)";
        } else {
            echo "\nNot a single template was loaded. Please check your Templates' Folder\n";
        }
    }

    public function __construct() {
        $this->init();
        $this->loadProductsXML();
        $this->loadTemplates();
    }

    /**
     * @return String Product's folder path
     */
    public function getPrdFolder() {
        return $this->prd_folder;
    }

    /**
     * @return Array An array with the SimpleXMLElements product files loaded as strings. Array('prd_id'=> SimpleXMLElement Object))
     */
    public function getAllProductXML() {
        return $this->_prdXML;
    }

    /**
     * @param String $prd_id Product id
     * @return SimpleXMLElement An array with the XML product files loaded as strings
     */
    public function getProductXML($prd_id) {
        return $this->_prdXML[$prd_id];
    }

    /**
     * Returns a set of one product's features
     *
     * @param String $prd_id Product id
     * @return Array
     */
    public function getProductFeatures($prd_id) {
        
    }

    /**
     * Returns one feature of one product
     *
     * @param String $prd_id Product id
     * @param String $feat_id Feature id
     * @return Array
     */
    public function getProductFeature($prd_id, $feat_id) {
        
    }

    /**
     * Evaluates if a given string is a xml filename
     *
     * @param String $str
     * @return true/false
     */
    protected function isXML($str) {
        if (preg_match('/.*\.xml$/', $str) > 0)
            return true;
        else
            return false;
    }

    /**
     * Evaluates if a given string is a tpl filename
     *
     * @param String $str
     * @return true/false
     */
    protected function isTPL($str) {
        if (preg_match('/.*\.tpl$/', $str) > 0)
            return true;
        else
            return false;
    }

    /**
     * Generates all products in products folder
     */
    public function generateAllProducts() {
        foreach ($this->_prdXML as $prd_id => $xml) {
            if (!$this->generateProduct($prd_id)) {
                echo "\nCouldn't generate product {$prd_id}\n";
                return false;
            }
        }

        return true;
    }

    /**
     * Generates one single product into generated folder
     *
     * @param String $prd_id Product id
     */
    public function generateProduct($prd_id) {
        echo "\n\n---------- Generating product \"{$prd_id}\" ---------- ";

        $genpath = $this->gen_folder . DIRECTORY_SEPARATOR . $prd_id;

        if (!file_exists($genpath)) {
            if (mkdir($genpath)) {
                $this->verbose_mode == true ? print("\nProduct {$prd_id} folder \"{$genpath}\" successfully created\n") : '';
            } else {
                echo "\nCouldn't write into generated's folder. Please verify the permissions of \"{$this->gen_folder}\"\n";
                return false;
            }
        } else {
            $this->verbose_mode == true ? print("\nUsing existent product {$prd_id} folder \"{$genpath}\"\n") : '';
        }

        
        
        $this->_parser->setFeatureSet($this->_prdXML[$prd_id]);
        
        foreach ($this->_tplFiles as $filename) {
            $this->verbose_mode == true ? print("Parsing {$filename} .......... ") : '';

            $path = $this->tpl_folder . DIRECTORY_SEPARATOR . $filename;
            
            $this->_parser->setTemplateFile(file($path));
            $content = $this->_parser->parse();
            
            $newfile = $genpath . DIRECTORY_SEPARATOR . preg_replace('/\.tpl$/', '', $filename) . '.' . $this->file_ext;
            
            if (strlen($content) > 0) {
                if ($handle = fopen($newfile, 'w')) {
                    fwrite($handle, $content);
                    fclose($handle);
                    $this->verbose_mode == true ? print("\nSuccessfully parsed to \"{$newfile}\"\n") : '';
                } else {
                    echo "\nERROR: Couldn't write to \"{$newfile}\"\n";
                    return false;
                }
            } else {
                $this->verbose_mode == true ? print("Doesn't belong to {$prd_id}. Ignoring\n") : '';
            }
        }

        echo "Done!"; 
        return true;
    }

    public function enableVerbose(){
        $this->verbose_mode = true;
    }
}
?> 
