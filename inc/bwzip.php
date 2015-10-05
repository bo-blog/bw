<?php
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2015 bW Development Team
* @license MIT
*/
if (!defined ('P')) {
	die ('Access Denied.');
} 

class bwZip {
	private static $folderFiles;

	public function __construct () {
		self :: $folderFiles = array ();
	}

	public static function zipRead ($fileName, $writeNow = false, $overwrite = false) {
		$output = array ();
		$allFiles = @explode ('/#FILE#/', @file_get_contents ($fileName));
		for ($i = 0; $i < count ($allFiles); $i++) {
			@list ($filePath, $fileZipContent) = @explode ('/#ZIP#/', $allFiles[$i]);
			$fileUnzipContent = gzuncompress ($fileZipContent);
			$output[$i] = array ('path' => $filePath, 'content' => $fileUnzipContent);
			if ($writeNow) {
				if ($overwrite || !file_exists ($filePath)) {
					@file_put_contents ($filePath, $fileUnzipContent);
				}
			}
		}
		return $output;
	}

	public static function zipWrite ($fileNames, $zipFilePath = false) {
		$output = array ();
		foreach ($fileNames as $filePath) {
			$output[] = $filePath.'/#ZIP#/'.gzcompress (@file_get_contents ($filePath));
		}
		$outputContent = @implode ('/#FILE#/', $output);
		if ($zipFilePath) {
			@file_put_contents ($zipFilePath, $outputContent);
		}
		return $outputContent;
	}

	public static function zipFolder ($folderPath, $zipFilePath = false) {
		if (!is_dir ($folderPath)) {
			return false;
		}
		if ($handle = opendir ($folderPath)) {
			while (false !== ($file = readdir ($handle))) {
				if (!is_dir ($folderPath . $file)) {
					self :: $folderFiles[] = $folderPath . $file;
				} 
				elseif ($file != '.' && $file != '..') {
					self :: zipFolder ($folderPath . $file . '/');
				}
			} 
		} 
		if ($zipFilePath) {
			return self :: zipWrite (self :: $folderFiles, $zipFilePath);
		} 
		else {
			return self :: $folderFiles;
		}
	}
}