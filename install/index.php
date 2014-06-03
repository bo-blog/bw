<?php 
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2014 bW Development Team
* @license MIT
*/
define ('P', '../');

if (file_exists ('../conf/info.php')) {
	die ('Already installed.');
}


output ();


function output ()
{
	$PHPver=PHP_VERSION >= '5.3.0' ? 1 : 0;
	$debug=isset ($_REQUEST['debug']) ? 1 : 0;

	print<<<eot
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<meta name="robots" content="none" />
<title>Welcome to bW</title>
<script src="../inc/script/jquery.min.js"></script>
<script src="jquery.scrollTo.min.js"></script>
<link href="../theme/default/font.css" media="all" rel="stylesheet" type="text/css" />
<link href="install.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="layer1" class="layer">
<p class="wL"><span class="icon-newicon"></span> Welcome.</p>
<p class="wM">Only 3 steps to go.</p>
<p class="wM"><br/></p>
<p class="wS">
<ul>
<li>Check the environment.</li>
<li>Provide basic information.</li>
<li>Installation.</li>
</ul>
</p>
<p class="wM">
<br/><br/><span class="wM wBox" id="btn1">START</span>
</p>
</div>


<div id="layer2" class="layer">
<p class="wL"><span class="icon-newicon"></span> Environment</p>
<p class="wM">Just a moment. </p>
<p class="wM"><br/></p>
<p class="wS">
<ul>
<li>PHP version &gt; 5.3.0 <span id="rslt1"></span></li>
<li>PDO extension <span id="rslt2"></span></li>
<li>Zlib extension <span id="rslt3"></span></li>
<li>cURL extension <span id="rslt4"></span></li>
<li>PDO SQLite or PDO MySQL<span id="rslt5"></span></li>
</ul>
</p>
<p class="wS" id="rslt6" style="display: none;">
</p>
<p class="wM">
<br/><br/><span class="wM wBox" id="btn2"></span>
</p>
</div>


<div id="layer3" class="layer">
<form id="smt">
<p class="wL"><span class="icon-newicon"></span> Basic Info</p>
<p class="wM">Make sure all settings are correct. </p>
<p class="wM"><br/></p>
<p class="wS">
<input type="text" name="instd[siteAuthor]" id="siteAuthor" placeholder="Your name" class="inp inpL" /><br/>
<input type="password" name="instd[siteKey]" id="siteKey" placeholder="Your password" class="inp inpL" /><br/>
<input type="text" name="instd[dbType]" id="dbType" readonly="readonly" value="SQLite" class="inp inpL" title="Click to switch database type."/><br/>
<input type="text" name="instd[dbName]" id="dbName" placeholder="DB Name" value="" class="inp inpL" /><br/>
<input type="text" name="instd[dbAddr]" id="dbAddr" placeholder="DB Address" value="" class="inp inpL mysql" /><br/>
<input type="text" name="instd[dbUser]" id="dbUser" placeholder="DB User Name" value="" class="inp inpL mysql" /><br/>
<input type="text" name="instd[dbPass]" id="dbPass" placeholder="DB Password" value="" class="inp inpL mysql" /><br/>
</p>
<p class="wM">
<br/><span class="wM wBox" id="btn3">CONTINUE</span>
</p>
</form>
</div>

<div id="layer4" class="layer">
<p class="wL"><span class="icon-newicon"></span> Installation</p>
<p class="wM">Just a moment. </p>
<p class="wM"><br/></p>
<p class="wS">
<ul>
<li>Write DB Config File <span id="rslt7"></span></li>
<li>Create tables <span id="rslt8"></span></li>
<li>Initialize data <span id="rslt9"></span></li>
</ul>
</p>
<p class="wS" id="rslt10" style="display: none;">
</p>
<p class="wM">
<br/><br/><span class="wM wBox" id="btn4"></span>
</p>
</div>

<script>
$('#btn2, #btn3, #btn4').hide();
$('#btn1').click (function(){
	$.scrollTo('#layer2', 500, {onAfter: function(){
		setTimeout(function(){	checkEnv ();}, 1200);
	}});
});

function checkEnv ()
{
	var rPass="<span class='icon-checkmark wGreen'></span>";
	var rFail="<span class='icon-cross wRed'></span>";
	if ({$PHPver}==0) {
		$('#rslt1').html (rFail);
		$('#rslt6').html('Environment check failed. bW cannot run with the current server configuration.');
		$('#rslt6').fadeIn();
		return false;
	}
	else {
		$('#rslt1').html (rPass);
		$.get ('install_action.php', {step : 1}, function (data){
			var rslt2 = data.rslt2 == 1 ? rPass : rFail;
			var rslt3 = data.rslt3 == 1 ? rPass : rFail;
			var rslt4 = data.rslt4 == 1 ? rPass : rFail;
			var rslt5 = data.rslt5 == 1 ? rPass : rFail;
			$('#rslt2').html (rslt2);
			$('#rslt3').html (rslt3);
			$('#rslt4').html (rslt4);
			$('#rslt5').html (rslt5);
			if (data.rslt6 == 1) {
				$('#rslt6').html('Congratulations! Environment check passed.');
				$('#rslt6').fadeIn();
				$('#btn2').html('CONTINUE');
				$('#btn2').fadeIn();
				$('#btn2').click (function(){
					$.scrollTo('#layer3', 500);
				});
				$('#btn3').show();
			}
		}, 'JSON');
	}
}

$('#dbType').click (function(){
	$('#dbType').val($('#dbType').val()=='SQLite' ? 'MySQL' : 'SQLite');
	checkDB ();
});
checkDB ();

function checkDB ()
{
	if ($('#dbType').val()=='SQLite') {
		$('.mysql').hide();
	} else {
		$('.mysql').show();
	}
}

$('#btn3').click (function(){
	if ($('#siteAuthor').val()=='' || $('#siteKey').val()=='' || $('#dbName').val()=='') {
		alert ("Please input all necessary data.");
		return false;
	} else {
		$.scrollTo('#layer4', 500, {onAfter: function(){
			setTimeout(function(){	doSetup ();}, 1200);
		}});
	}
});

function doSetup ()
{
	var rPass="<span class='icon-checkmark wGreen'></span>";
	var rFail="<span class='icon-cross wRed'></span>";
	if ({$debug}==1) {
		$.post ('install_action.php?step=2', $('#smt').serialize(), function (data){
			$('#layer4').html (data);
		});
		return false;
	}

	$.post ('install_action.php?step=2', $('#smt').serialize(), function (data){
		var rslt7 = data.rslt7 == 1 ? rPass : rFail;
		var rslt8 = data.rslt8 == 1 ? rPass : rFail;
		var rslt9 = data.rslt9 == 1 ? rPass : rFail;
		var rslt10 = data.rslt10;
		$('#rslt7').html (rslt7);
		$('#rslt8').html (rslt8);
		$('#rslt9').html(rslt9);
		$('#rslt10').html(rslt10);
		$('#rslt10').fadeIn();
		if (data.error==0) {
			$('#btn4').html('ENTER SITE');
			$('#btn4').fadeIn();
			$('#btn4').click (function(){
				window.location="../index.php";
			});
		}
	}, 'JSON');
}

</script>
</body>
</html>
eot;
} 
