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

class bwCategory {
	private $cacheClear;

	public function __construct ()
	{
		$this -> cacheClear = true;
	} 

	public function getCategories ()
	{
		bw :: initCategories ();
	} 

	public function addCategories ($smt, $smt2 = array ())
	{
		if (is_array ($smt)) {
			$dataLine = array();
			foreach ($smt as $aCateURLName => $aCateDispName) {
				$aCateURLName = urlencode ($aCateURLName);
				$aCateDispName = htmlspecialchars ($aCateDispName, ENT_QUOTES, 'UTF-8');
				if (array_key_exists ($aCateURLName, bw :: $cateData)) {
					stopError (bw :: $conf['l']['admin:msg:Existed']);
				} else {
					$dataLine[] = array(':aCateURLName' => $aCateURLName, ':aCateDispName' => $aCateDispName, ':aCateTheme' => isset ($smt2[$aCateURLName]) ? $smt2[$aCateURLName] : null);
				} 
			} 
			$dataLineCounter = count ($dataLine);
			if ($dataLineCounter > 0) {
				bw :: $db -> dbExecBatch ("INSERT INTO categories (aCateURLName, aCateDispName, aCateCount, aCateOrder, aCateTheme) VALUES (:aCateURLName, :aCateDispName, 0, {$dataLineCounter}, :aCateTheme)", $dataLine);
				if ($this -> cacheClear) {
					$this -> getCategories (); //Refresh immediately
					clearCache (); //Clear all cache
				} 
			} 
			hook ('addCategories', 'Execute', $smt);
		} 
	} 

	public function deleteCategories ($deletedCates)
	{
		foreach ($deletedCates as $delCate => $delCateName) {
			$delLine = bw :: $db -> getSingleRow ('SELECT * FROM categories WHERE aCateURLName=:delCate', array(':delCate' => $delCate));
			if ($delLine['aCateCount'] == 0) {
				bw :: $db -> dbExec ('DELETE FROM categories WHERE aCateURLName=:delCate', array(':delCate' => $delCate));
			} else {
				stopError (bw :: $conf['l']['admin:msg:CategoryNotEmpty']);
			} 
		} 
		if ($this -> cacheClear) {
			$this -> getCategories (); //Refresh immediately
			clearCache (); //Clear all cache
		} 
		hook ('deleteCategories', 'Execute', $deletedCates);
	} 

	public function orderCategories ($arrayOrder)
	{
		$dataLine = array();
		$i = count ($arrayOrder)-1;
		foreach ($arrayOrder as $order) {
			$dataLine[$i][':aCateOrder'] = $i;
			$dataLine[$i][':aCateURLName'] = $order;
			$i -= 1;
		} 
		bw :: $db -> dbExecBatch ('UPDATE categories SET aCateOrder=:aCateOrder WHERE aCateURLName=:aCateURLName', $dataLine);
		if ($this -> cacheClear) {
			$this -> getCategories (); //Refresh immediately
			clearCache (); //Clear all cache
		} 
		hook ('orderCategories', 'Execute', $arrayOrder);
	} 

	public function renameCategories ($arrayCateID, $arrayOldNames, $arrayNewNames)
	{
		if (count ($arrayCateID) <> count ($arrayOldNames) || count ($arrayCateID) <> count ($arrayNewNames)) {
			return;
		} 
		for ($i = 0; $i < count ($arrayCateID); $i++) {
			$arrayNewNames[$i] = htmlspecialchars ($arrayNewNames[$i], ENT_QUOTES, 'UTF-8');
			if ($arrayOldNames[$i] <> $arrayNewNames[$i]) {
				bw :: $db -> dbExec ('UPDATE categories SET aCateDispName=? WHERE aCateURLName=?', array ($arrayNewNames[$i], $arrayCateID[$i]));
			} 
		} 
		if ($this -> cacheClear) {
			$this -> getCategories (); //Refresh immediately
			clearCache (); //Clear all cache
		} 
		hook ('renameCategories', 'Execute', array ($arrayCateID, $arrayOldNames, $arrayNewNames));
	} 

	public function updateCategoryThemes ($arrayCateID, $arrayNewThemes)
	{
		if (count ($arrayCateID) <> count ($arrayNewThemes)) {
			return;
		} 
		for ($i = 0; $i < count ($arrayCateID); $i++) {
			if ($arrayNewThemes[$i] <> bw :: $cateList[$arrayCateID[$i]]['aCateTheme']) {
				bw :: $db -> dbExec ('UPDATE categories SET aCateTheme=? WHERE aCateURLName=?', array ($arrayNewThemes[$i], $arrayCateID[$i]));
			} 
		} 
		if ($this -> cacheClear) {
			$this -> getCategories (); //Refresh immediately
			clearCache (); //Clear all cache
		} 
		hook ('updateCategoryThemes', 'Execute', array ($arrayCateID, $arrayNewThemes));
	} 

	public function bufferCacheClear ()
	{
		$this -> cacheClear = false;
	} 

	public function endBufferCache ()
	{
		$this -> getCategories (); //Refresh immediately
		clearCache (); //Clear all cache
		$this -> cacheClear = true;
	} 
} 

