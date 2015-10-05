var In_Block_Mode=false;

function scrollToTop () {
	$('html,body').animate({scrollTop: '0px'}, 400);
}

if (location.href.indexOf('?cleartoken')!=-1)
{
	if (window.localStorage)
	{
		localStorage.clear();
	}
}


function lightboxImage (imgSrc) {
	$("#UI-lightbox").fadeIn();
	var winH=$(window).height();
	var img=new Image;
	img.src=imgSrc;
	$("#UI-lightbox").append( "<div id='lightbox-image'><img id='lightboxImage' src='"+imgSrc+"' alt='' /></div>" );
	$('#lightboxImage').load(function(){
		var windowWidth=$(window).width();
		var windowHeight=winH;
		var picWidth=img.width;
		var picHeight=img.height;

		var finalWidth;
		var finalHeight;

		var idealWidth=Math.floor(windowWidth*0.8)-20;
		var idealHeight=Math.floor(windowHeight*0.8)-20;


		if (picWidth<idealWidth && picHeight<idealHeight)
		{
			finalWidth=picWidth;
			finalHeight=picHeight;
		}

		else if (picWidth<idealWidth && picHeight>idealHeight)
		{
			finalHeight=idealHeight;
			finalWidth=Math.floor((picWidth/picHeight)*idealHeight);
		}

		else if (picWidth>idealWidth && picHeight<idealHeight)
		{
			finalWidth=idealWidth;
			finalHeight=Math.floor((picHeight/picWidth)*idealWidth);
		}


		else {
			if (picWidth>=picHeight)
			{
				finalWidth=idealWidth;
				finalHeight=Math.floor((picHeight/picWidth)*idealWidth);
				if (finalHeight>idealHeight)
				{
					finalHeight=idealHeight;
					finalWidth=Math.floor((picWidth/picHeight)*idealHeight);		
				}
			}
			if (picHeight>picWidth)
			{
				finalHeight=idealHeight;
				finalWidth=Math.floor((picWidth/picHeight)*idealHeight);
				if (finalWidth>idealWidth)
				{
					finalWidth=idealWidth;
					finalHeight=Math.floor((picHeight/picWidth)*idealWidth);
				}
			}
		}

		$("#lightboxImage").css("width", finalWidth+"px");
		$("#lightboxImage").css("height", finalHeight+"px");
		$("#lightbox-image").css("position", "fixed");
		$("#lightbox-image").css("top", Math.floor((windowHeight-finalHeight)/2));
		$("#lightbox-image").css("left", Math.floor((windowWidth-finalWidth)/2));
	});
	$('#lightboxImage').css("cursor", "pointer");
	$('#lightboxImage').click(function (){
		if (imgSrc.indexOf ('?imageView2/')!=-1)
		{
			var imgSrcs=imgSrc.split('?imageView2/');
			imgSrc=imgSrcs[0];
		}
		window.open(imgSrc);
	});
	$('#UI-lightbox').click(function (){
		$('#UI-lightbox').fadeOut();
		$("#UI-lightbox").html('');
		$("#UI-lightbox").unbind();
	});
}


function checkLogin (oj) {
	if(window !=parent) {
		parent.location=window.location.href;
		return false;
	}
	var rootURL= $('.'+oj).data('adminurl');
	var targetURL = rootURL+"?ajax=1";

	if (window.localStorage)
	{
		targetURL=targetURL+"&mobileToken="+localStorage.mobileToken;
	}

	$.ajax({
		type: "GET",
		url: targetURL,
		success: function (data)
		{
			if (data.error==1) { //Not log in
				lightboxLoader (rootURL+"/login/");
			}
			else {
				var cact=location.pathname;
				if (cact.indexOf("post/")!=-1 || cact.indexOf("read.php/")!=-1)
				{
					var aID=$('.'+oj).data('adminid');
					window.location=rootURL+"/articles/modify/?aID="+aID+"&CSRFCode="+data.returnMsg;
				}
				else
				{
					window.location=rootURL+"/dashboard/?CSRFCode="+data.returnMsg;
				}
			}
		},
		dataType: "json"
	});
}

function doLogin (oj, rootURL) {
	var s_token= $('#'+oj).val();
	var isRem = $('#'+oj+'-rem').is(':checked');

	if (s_token=='')
	{
		promptLoginError (oj);
		return false;
	}
	targetURL = rootURL+"/login/verify/?ajax=1";

	if (isRem)
	{
		try {
			localStorage.test=1;
		} catch (e) {
			alert (lng['RememberFail']);
			isRem=false;
		}
		targetURL=targetURL+'&isRem=1';
	}
	else
	{
		targetURL=targetURL+'&isRem=0';
	}
	$.get(targetURL, { s_token: s_token }, function (data) {
		if (data.error==1) { //Wrong token
			promptLoginError (oj);
			return false;
		}
		else {
			var plusCode = data.returnMsg.split ('-');
			if (isRem && window.localStorage)
			{
				localStorage.clear();
				localStorage.mobileToken=plusCode[0];
			}

			if (!In_Block_Mode)
			{
				var cact=location.pathname;
				if (cact.indexOf("post/")!=-1 || cact.indexOf("read.php/")!=-1)
				{
					var aID=$('.adminSign').data('adminid');
					window.location=rootURL+"/articles/modify/?aID="+aID+"&CSRFCode="+plusCode[1];
				}
				else
				{
					window.location=rootURL+"/dashboard/?CSRFCode="+plusCode[1];
				}
			}
			else {
				lightboxLoaderDestroy ();
				blockComment (In_Block_Mode[0], In_Block_Mode[1], In_Block_Mode[2], In_Block_Mode[3]);
				In_Block_Mode=false;
			}

		}
	}, "json");
}

function promptLoginError (oj) {
	$('#'+oj).val('');
	$('#'+oj).addClass('inputLineWarn');
	$('#'+oj+'-failure').fadeIn();
	$('#'+oj).click(function (){
		$('#'+oj).removeClass('inputLineWarn');
		$('#'+oj+'-failure').fadeOut();
	});
}

function lightboxCore (VAction, URLorHTML) {
	$("#UI-lightbox").fadeIn(500);
	$("#UI-lightbox").append( "<div id='lightbox-message'></div>" );
	var windowWidth=$(window).width();
	var windowHeight=$(window).height();

	var finalWidth=$("#lightbox-message").width();
	var finalHeight=$("#lightbox-message").height();

	$("#lightbox-message").css("position", "fixed");
	$("#lightbox-message").css("top", Math.floor((windowHeight-finalHeight)/2));
	$("#lightbox-message").css("left", Math.floor((windowWidth-finalWidth)/2));

	if (VAction == "loader")
	{
		$("#lightbox-message").load(URLorHTML);
	} else {
		$("#lightbox-message").html(URLorHTML);
	}
}

function lightboxLoader (loadURL) {
	$(".commentArea").hide();
	lightboxCore ('loader', loadURL);
}

function lightboxContent (contentHTML) {
	lightboxCore ('HTML', contentHTML);
}

function lightboxLoaderDestroy () {
	$(".commentArea").show();
	$('#UI-lightbox').fadeOut();
	$("#UI-lightbox").html('');
	$("#UI-lightbox").unbind();
}

function changeNav ()
{
	var aPos=$(window).scrollTop();
	var bPos=$("#mainArea").position().top;
	if (aPos>bPos)
	{
		$("header").addClass('headerShrink');
	}
	else {
		$("header").removeClass('headerShrink');
	}
	t=setTimeout ("changeNav()", 600);
}

function makeComUserLink ()
{
	$(".comName h6").each (function () {
		var comSource=$(this).data ('usersource');
		if (comSource) {
			$(this).append( "<span class='comSrc icon-comSrc-"+comSource+"'></span>" );
		}
	});
	$(".comName h6 a").each (function () {
		var comURL=$(this).attr ('href');
		if (!comURL) {
			$(this).removeAttr ('href');
		}
	});
}

function blockComment (oj, mode, comID, aID) {
	if(window !=parent) {
		parent.location=window.location.href;
		return false;
	}

	if (mode=='blockip')
	{
		if (!confirm(lng['BlockIP']))
		{
			return false;
		}
	}

	var rootURL= $('#'+oj).data('adminurl');
	var targetURL = rootURL+"/comments/"+mode+"/?ajax=1";

	targetURL=targetURL+"&comID="+comID+"&aID="+aID;


	$.ajax({
		type: "GET",
		url: targetURL,
		success: function (data)
		{
			if (data.error==1) { //Not log in
				In_Block_Mode=new Array(oj, mode, comID, aID);
				lightboxLoader (rootURL+"/login/");
			}
			else {
				if (mode=="blockitem")
				{
					$("#comWrap-"+comID).fadeOut(400);
				}
				if (mode=="blockip")
				{
					window.location.reload();
				}
			}
		},
		dataType: "json"
	});
}

function commentBatches (rootURL, aID) {
	var currentbatch = $("#comLoadMoreA").data('currentbatch');
	var totalbatches = $("#comLoadMoreA").data('totalbatches');
	if (currentbatch < totalbatches)
	{
		$("#comLoadMoreA").click(function (){
			$("#UI-loading").fadeIn(500);
			$.get (rootURL+(currentbatch+1)+'/?ajax=1', {aID: aID}, function (data) 	{
				$("#UI-loading").fadeOut(200);
				if (data.error==0)
				{
					$("#comLoadMore").remove();
					$("#comInsertOld").append(data.returnMsg);
					commentBatches (rootURL, aID);
				}
			}, 'json');
		});
	}
	else {
		$("#comLoadMoreA").remove();
	}
}