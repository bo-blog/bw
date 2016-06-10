<?php
$tp = file_get_contents ('template-manual.html');
$contents = file_get_contents ('./src/manual/contents.md');

require_once './src/Parser.php';

$parser = new HyperDown\Parser;
$html = $parser -> makeHtml ($contents);

$tp = str_replace ('<!--CONTENTS-->', $html, $tp);
$dir = './src/manual';
$openDir = opendir($dir);
while($readDir = @readdir ($openDir))
{
	if($readDir != "." && $readDir != ".." && $readDir != "contents.md") { 
		$content = $parser -> makeHtml (file_get_contents ("$dir/$readDir"));
		$tp1 = str_replace ('<!--DOCUMENT-->', $content, $tp);
		$fileName = substr ($readDir, 0, -3);
		file_put_contents ("./manual/$fileName.html", $tp1);
	} 
}

die ("FINISHED");