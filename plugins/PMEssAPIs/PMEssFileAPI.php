<?php
/* 

File API ( Only for Non-Commercial Use )
By Kevin Wang
From China

Skype: kvwang98
Twitter: KevinWang_China
Youtube: http://www.youtube.com/VanishedKevin
E-Mail: kevin@cnkvha.com

*/
class PMEssFileAPI{
	private $server;
	function __construct(){
		$this->server = ServerAPI::request();
	}
	
	public function init(){
	}
	
	public function SafeCreateFolder($path){
		if (!file_exists($path) and !is_dir($path)){
			mkdir($path);
		} 
	}
    
    public function copyFolder($source, $dest, $permissions = 0755){
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }
    
        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }
    
        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }
    
        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            // Deep copy directories
            $this->copyFolder("$source/$entry", "$dest/$entry");
        }
        // Clean up
        $dir->close();
        return true;
    }
    
    public function deleteFolder($dirPath) {
        if (! is_dir($dirPath)) {
            return(false);
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteFolder($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
    
}

?> 
