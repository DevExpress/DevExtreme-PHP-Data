<?php
namespace DevExtreme;
class LoadHelper {
    public static function LoadModule($className) {
        $namespaceNamePos = strpos($className, __NAMESPACE__);        
        if ($namespaceNamePos === 0) {
            $subFolderPath = substr($className, $namespaceNamePos + strlen(__NAMESPACE__));
            $filePath = __DIR__.str_replace("\\", DIRECTORY_SEPARATOR, $subFolderPath).".php";
            require_once($filePath); 
        }
    }
}