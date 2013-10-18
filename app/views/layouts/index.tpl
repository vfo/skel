<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title></title>
	<base href="{$config.base_url}" />
	<meta name="viewport" content="width=device-width,maximum-scale=1.0">
	<meta name="description" content="">

	<link rel="stylesheet" href="{$config.base_url}css/bootstrap.min.css">
	<link rel="stylesheet" href="{$config.base_url}css/font-awesome.css">
	<!--[if IE 7]>	<link rel="stylesheet" href="{$config.base-url}css/font-awesome-ie7.min.css"> <![endif]-->
	<link rel="stylesheet" href="{$config.base_url}css/main.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="{$config.base_url}js/vendor/jquery.min.js"><\/script>')</script>
		<script src="{$config.base_url}js/vendor/modernizr.min.js"></script>
		<script src="{$config.base_url}js/vendor/bootstrap.min.js"></script>
		<script src="{$config.base_url}js/plugins.js"></script>
		<script src="{$config.base_url}js/main.js"></script>	
</head>
<body >
		<header >
		</header>
		<div id="content" role="main" >



				{if isset($main_content_tpl)}
					{include $main_content_tpl}
				{else}
					error (index.tpl): main_content_tpl is not set
				{/if}
		</div>
		<div id="mdl-container"></div>
		<footer>
		<div class="container">
		</div> 
		</footer>

		<script>{*
			var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
				g.src='//www.google-analytics.com/ga.js';
				s.parentNode.insertBefore(g,s)}(document,'script'));*}
			</script>	
	</body>
	</html>
