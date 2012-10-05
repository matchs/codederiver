<?php
require_once 'CodeGen.php';

echo "\nStarting script execution\n\n";

$codeGen = new CodeGen();

//$codeGen->generateAllProducts();

$codeGen->generateProduct('sm2');
//if($codeGen->generateAllProducts() == true){
//    echo "\nEnd of script execution\nStatus: Success\n";
//}else{
//    echo "\nEnd of script execution\nStatus: Fail\n";
//}


?>
