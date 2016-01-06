<?php
if (isset ($_GET['xmID']))
{
	die ("<body><script type=\"text/javascript\" src=\"http://www.xiami.com/widget/player-single?uid=0&sid=".floor($_GET['xmID'])."&mode=js\"></script></body>");
}