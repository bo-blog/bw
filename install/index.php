<?php 
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2014 bW Development Team
* @license MIT
*/

if (file_exists ('../conf/info.php')) {
	die ('Already installed.');
}

if (!isset ($_COOKIE['bwInstallLang'])) {
	initiate ();
} else {
	output ();
}


function output ()
{
	$PHPver=PHP_VERSION >= '5.3.0' ? 1 : 0;
	$debug=isset ($_REQUEST['debug']) ? 1 : 0;
	$ln='./' . basename ($_COOKIE['bwInstallLang']) . '.lang.php';
	file_exists ($ln) ? include_once ($ln) : include_once ('./en.lang.php');

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
<p class="wL"><span class="icon-newicon"></span> {$l['welcome.msg']}</p>
<p class="wM">{$l['welcome.steps']}</p>
<p class="wM"><br/></p>
<p class="wS">
<ul>
<li>{$l['welcome.chenv']}</li>
<li>{$l['welcome.basicinfo']}</li>
<li>{$l['welcome.install']}</li>
</ul>
</p>
<p class="wM">
<br/><br/><span class="wM wBox" id="btn1">{$l['welcome.btn']}</span>
</p>
</div>


<div id="layer2" class="layer">
<p class="wL"><span class="icon-newicon"></span> {$l['env.title']}</p>
<p class="wM">{$l['env.wait']}</p>
<p class="wM"><br/></p>
<p class="wS">
<ul>
<li>{$l['env.phpver']} &gt; 5.3.0 <span id="rslt1"></span></li>
<li>{$l['env.pdo']} <span id="rslt2"></span></li>
<li>{$l['env.sql']} <span id="rslt5"></span></li>
<li>{$l['env.writable']} <span id="rslt3"></span></li>
<p class="wS"> (conf, storage, extension, theme, update)
</p>
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
<p class="wL"><span class="icon-newicon"></span> {$l['info.title']}</p>
<p class="wM">{$l['info.sure']}</p>
<p class="wM"><br/></p>
<p class="wS">
<input type="text" name="instd[siteAuthor]" id="siteAuthor" placeholder="{$l['info.yourname']}" class="inp inpL" /><br/>
<input type="password" name="instd[siteKey]" id="siteKey" placeholder="{$l['info.yourpsw']}" class="inp inpL" /><br/>
<input type="text" name="instd[dbType]" id="dbType" readonly="readonly" value="SQLite" class="inp inpL" title="{$l['info.dbtype']}"/><br/>
<input type="text" name="instd[dbName]" id="dbName" placeholder="{$l['info.dbname']}" value="" class="inp inpL" /><br/>
<input type="text" name="instd[dbAddr]" id="dbAddr" placeholder="{$l['info.dbaddr']}" value="" class="inp inpL mysql" /><br/>
<input type="text" name="instd[dbUser]" id="dbUser" placeholder="{$l['info.dbuser']}" value="" class="inp inpL mysql" /><br/>
<input type="text" name="instd[dbPass]" id="dbPass" placeholder="{$l['info.dbpsw']}" value="" class="inp inpL mysql" /><br/>
</p>
<p class="wM">
<br/><span class="wM wBox" id="btn3">{$l['info.btn']}</span>
</p>
</form>
</div>

<div id="layer4" class="layer">
<p class="wL"><span class="icon-newicon"></span> {$l['install.title']}</p>
<p class="wM">{$l['install.noclose']} </p>
<p class="wM"><br/></p>
<p class="wS">
<ul>
<li>{$l['install.writeconfig']} <span id="rslt7"></span></li>
<li>{$l['install.createtables']} <span id="rslt8"></span></li>
<li>{$l['install.data']} <span id="rslt9"></span></li>
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
		$('#rslt6').html("{$l['env.failure']}");
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
				$('#rslt6').html("{$l['env.success']}");
				$('#rslt6').fadeIn();
				$('#btn2').html("{$l['env.btn']}");
				$('#btn2').fadeIn();
				$('#btn2').click (function(){
					$.scrollTo('#layer3', 500);
				});
				$('#btn3').show();
			}
			if (data.rslt6 == 0) {
				$('#rslt6').html("{$l['env.failure']}");
				$('#rslt6').fadeIn();
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
		alert ("{$l['info.error']}");
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
			$('#btn4').html("{$l['install.btn']}");
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

function initiate ()
{

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
<link href="../theme/default/font.css" media="all" rel="stylesheet" type="text/css" />
<link href="install.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="layer2" class="layer">
<p class="wL"><span class="icon-newicon"></span></p>
<p class="wS">
<br/><br/><span class="wM wBox btn1" data-lang="en">English</span>&nbsp;&nbsp; 
<span class="wM wBox btn1" data-lang="zh-cn">中文（简体）</span>&nbsp;&nbsp; 
<span class="wM wBox btn1" data-lang="zh-tw">中文（繁體）</span>&nbsp;&nbsp; 
</p>
</div>
<script>
$('.btn1').click (function(){
	setCookie ('bwInstallLang', $(this).data('lang'));
	window.location='index.php';
});
function setCookie (c_name, value) {
	document.cookie=c_name+ "=" +escape(value);
}
</script>
</body>
</html>
eot;
} 
