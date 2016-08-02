<html>
<head>

	<title>Forolibre</title>
	<meta name="robots" content="noindex" />

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">

	<link rel="icon" type="image/png" href="imagenes/icon.png" />
	<link rel="apple-touch-icon" href="imagenes/icon.png">

	<style type="text/css">

		body {
			margin: 0 auto;
			max-width: 1024px;
			font-family: arial;
		}
		#header {
			background: linear-gradient(#6F8FB2, #436C9A);
			padding: 5px;
			text-align: center;
			position: relative;
			height: 36px;
			font-size: 18px;
			font-weight: 600;
		}
		#header a {
			margin: auto;
			display: block;
			width: 90px;
			padding-top: 9px;
		}

		#header a:active, #header a:link, #header a:hover, #header a:visited {
			color: white;
			text-decoration: none;
		}

		.bloque_hilo {
			background-color: #F1F1F1;
			padding: 10px;
			border-color: black;
			border-style: solid; 
			border-width: 1px 1px 0 1px;
		}
		.bloque_hilo div {
			display: inline-block;
		}

		.titulo_y_multipage {
			width: 85%;
			vertical-align: middle;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			-o-user-select: none;
			user-select: none;
		}

		a:visited, a:link {
			color: #cc3300;
			text-decoration: none;
		}
		a:active, a:hover, .titulo_hilo:hover,.titulo_hilo:active {
			color: #002EB1;
			text-decoration: underline;
		}
		#hilos {
			margin: 5px;
			border-color: black;
			border-style: solid; 
			border-width: 0 0 1px;
		}
		#hilos span {
			color: #cc3300;
			font-weight: 600;
		}
		.titulo_hilo {
			position: relative;
			color: #cc3300;
			text-decoration: none;
			margin-right: 5px;
			font-size: 14px;
		}
		.numero_respuestas_hilo {
			width: 15%;
			text-align: right;
			vertical-align: middle;
		}
	</style>

	<!---Analytics-->
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-80564017-1', 'auto');
		ga('send', 'pageview');

	</script>
</head>
<body>
<div id="main">
	<div id="header">
		<a href="">Forolibre</a>
	</div>
		<?php
		session_start();	//['cookie_lifetime' => 1200]

		//----------LOG EN DB-----------
		if(!isset($_SESSION['logueado'])){
			$_SESSION['logueado'] = true;
			include 'log_stats.php';
		}
		//------------------------------

		$_SESSION['hilos'] = array();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://www.forocoches.com/foro/forumdisplay.php?f=2');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'login/cookie.txt');

		$response = curl_exec($ch);
		if($response === false)
			exit('<h1 style="text-align:center;">Lo sentimos, actualmente existen problemas en el servidor, vuelve más tarde.</h1>');

		$response = utf8_encode($response);
		curl_close($ch);

		if(!preg_match('/<tbody id="threadbits_forum_2">([\s\S]*?)<\/tbody>/', $response, $tabla))
			exit('<h1 style="text-align:center;">Lo sentimos, actualmente existen problemas en el servidor, vuelve más tarde.</h1>');

		preg_match_all('/<tr>([\s\S]*?)<\/tr>/', $tabla[0], $hilos);
		$hilos = preg_replace('/<span style="float:right([\s\S]*?)<\/span>/', '', $hilos[0]);

		?>
		<div id="hilos">
		<?php

		foreach($hilos as &$hilo){
			preg_match_all('/<a(.*?)<\/a>/', $hilo, $bloque_titulo);

			preg_match('/(?<=thread_title_)\d+/', $bloque_titulo[0][1], $id_hilo);
			preg_match('/(?<=>)(.*?)(?=<\/a>)/', $bloque_titulo[0][1], $titulo_hilo);
			preg_match('/(?<=<strong>).+(?=<\/strong>)/', $hilo, $numero_respuestas_hilo);
			$numero_respuestas_hilo = str_replace('.', '', $numero_respuestas_hilo);

			echo '<div class="bloque_hilo">';

			echo '<div class="titulo_y_multipage">';
			echo '<span class="titulo_hilo">';

			echo '<a href="showthread.php?t=';
			echo $id_hilo[0];
			echo '&page=1';
			echo '">';
			echo $titulo_hilo[0];
			echo '</a>';

			echo '</span>';

			if($numero_respuestas_hilo[0] > 30){
				echo '<span class="multipage">( ';

				$numero_paginas = ceil($numero_respuestas_hilo[0]/30);
				for($i=0;$i<$numero_paginas;++$i){
					if($i==8){
						echo '... ';
						echo '<a href="showthread.php?t=';
						echo $id_hilo[0];
						echo '&page=';
						echo $numero_paginas;
						echo '">';
						echo 'Última Página';
						echo '</a>';
						break;
					}
					echo '<a href="showthread.php?t=';
					echo $id_hilo[0];
					echo '&page=';
					echo $i+1;
					echo '">';
					echo $i+1;
					echo '</a>';
					echo "\n";
				}

				echo ' )</span>';
			}

			echo '</div>';

			echo '<div class="numero_respuestas_hilo">';
			echo '<span>';
			echo $numero_respuestas_hilo[0];
			echo '</span>';
			echo '</div>';

			echo '</div>';

			$_SESSION['hilos'][$id_hilo[0]] = 0;
		}

		?>
	</div>
</div>
</body>
</html>