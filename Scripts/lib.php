<?php 

require '';

/**
 * @author Mateus
 */
class CodeGen {

	/**
	 * Enables verbose mode for messages
	 * @var Boolean
         */	
	private $verbose_mode = true;

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


	public function init(){

		$this->tpl_folder = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Templates');
		$this->prd_folder = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Products');
		$this->cfg_folder = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Config');
		$this->gen_folder = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Generated');

	}

	public function __construct(){
		$this->init();
	
		try {
	            $files = scandir($this->prd_folder);
	        } catch (Exception $e) {
	            echo $e->getMessage();
	        }

		foreach($files as $i => $filename){
			if($this->isXML($filename)){
				$path = $this->prd_folder . DIRECTORY_SEPARATOR . $filename;

				if($xml = simplexml_load_file($path)){
					$attr = (string) $xml->attributes()->id;
					$this->_prdXML[$attr] = $xml;

					$this->verbose_mode == true ? print($filename . " loaded OK\n") : '';
				} else {
				  echo $filename . " loaded FAIL";
				}

			}
		}

		if(count($this->_prdXML) > 0){
			echo "\nSuccessfully loaded " . count($this->_prdXML) . " product(s)\n";
		}else{
			echo "\nNothing was loaded. Please check your Products Folder\n";
		}

		
	}

	public function getPrdFolder(){
		return $this->prd_folder;
	}
	

	/**
	 * @return Array An array with the SimpleXMLElements product files loaded as strings. Array('prd_id'=> SimpleXMLElement Object))
	 */
	public function getAllProductXML(){
		return $this->_prdXML;
	}
	
	/**
	 * @param String $prd_id Product id
	 * @return SimpleXMLElement An array with the XML product files loaded as strings
	 */
	public function getProductXML($prd_id){
		return $this->_prdXML[$prd_id];
	}

	/**
	 * Returns a set of one product's features
	 *
 	 * @param String $prd_id Product id
         * @return Array
         */
	public function getProductFeatures($prd_id){

	}

	/**
	 * Returns one feature of one product
	 *
 	 * @param String $prd_id Product id
 	 * @param String $feat_id Feature id
         * @return Array
         */
	public function getProductFeature($prd_id, $feat_id){

	}
	
	/**
	 * Evaluates if a given string is a xml filename
         *
	 * @param String $str
	 * @return True/False
	 */
	protected function isXML($str){
		if(preg_match('/.*\.xml$/',$str) > 0)
			return true;
		else
			return false;
	}

	/**
	 * Generates all products in the generated folder
	 */
	public function generateAllProducts(){

	}

	/**
	 * Generates one single product into generated folder
         *
 	 * @param String $prd_id Product id
	 */
	public function generateProduct($prd_id){
		
	}

}


?> 
