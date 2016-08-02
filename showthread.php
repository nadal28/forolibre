<?php
session_start();

// Si se intenta buscar un hilo que no aparecio en la página de búsqueda devuelve 403
if(is_null($_SESSION['hilos'][$_GET['t']])){
	header('Location: 403.html');
	return;
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://m.forocoches.com/foro/showthread.php?t='.$_GET['t'].'&page='.$_GET['page']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'login/cookie.txt');
$response = utf8_encode(curl_exec($ch));
curl_close($ch);

preg_match('/(?<=title>)(.*?)(?=<\/title)/', $response, $titulo_hilo);
$titulo_hilo = preg_replace('/\s-.+/', '', trim($titulo_hilo[0]));

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $titulo_hilo ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=yes">
	<meta charset="UTF-8">

	<link rel="icon" type="image/png" href="imagenes/icon.png" />
	<link rel="apple-touch-icon" href="imagenes/icon.png">

<!--	<script type="text/javascript">
		var adfly_id = 7810834;
		var popunder_frequency_delay = 1;
	</script>
	<script src="http://cdn.adf.ly/js/display.js"></script>
-->

	<style type="text/css">
		body {
			font-family: arial;
			padding: 0;
			margin: 0 auto;
			word-wrap: break-word;
		}
		#header {
			background: linear-gradient(#6F8FB2, #436C9A);
			padding: 5px;
			text-align: center;
			position: fixed;
			height: 36px;
			width: 100%;
			top: 0;
		}
		#header a {
			display: inline-block;
			width: 70px;
			position: absolute;
			left: 0px;
		}
		#header span {
			margin-top: 8px;
			display: inline-block;
			color: white;
			font-size: 18px;
			font-weight: 600;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			-o-user-select: none;
			user-select: none;
		}
		#titulo_hilo {
			text-align: center;
			background-color: #FFF7F3;
			border-width: 1px 0;
			border-style: solid;
			border-color: #D3D3D3;
			margin: 56px 0 10px;
			padding: 10px;
		}
		.paginacion {
			text-align: center;
			font-size: 15px;
			background-color: #f6fdff;
			border: 1px #D3D3D3 solid;
			margin: 6px;
			border-radius: 8px;
		}
		.paginacion > div:first-of-type {
			border-bottom: 1px solid #D3D3D3;
			padding: 7px;
		}		
		.paginacion div {
			margin: 5px 0;
			padding: 5px;
		}
		.paginacion span {
			margin: 0 7px;
		}
		a:link,  a:visited {
			color: #cc3300;
			text-decoration: none; 
		}
		a:hover,  a:active {
			color: #002EB1;
			text-decoration: none; 
		}
		strong {
			font-size: 12px;
			margin: 0px 5px;
		}

		#posts {
			margin: 5px;
		}

		.post {
			border: 1px solid gray;
			border-radius: 5px;
			margin: 10px 0;
		}

		.bloque_usuario {
			height: 70px;
			background-color: #E1E1E1;
			border-top-left-radius: 5px;
			border-top-right-radius: 5px;
			padding: 10px 10px 0px 10px;
		}
		.bloque_usuario div {
			display: inline-block;
		}

		.fecha_post{
			color: gray;
			font-size: 13px;
		}

		.avatar {
			float: right;
		}
		.avatar img {
			max-height: 50px;
			max-width: 50px;
		}
		.mensaje {
			padding: 10px;
		}
		.mensaje img, .cita_mensaje img {
			max-height: 200px;
			max-width: 200px;
		}

		.cita {
			margin: 10px;
			border-radius: 10px;
			border: 1px solid gray;
		}
		.cita_usuario {
			background-color: #E1E1E1;
			border-top-left-radius: 10px;
			border-top-right-radius: 10px;
			padding: 10px;
		}
		.cita_mensaje {
			padding: 10px;
		}
		.banner {
			text-align: center;
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
<div id="header">
	<a href="/forolibre"><img height="35" src="imagenes/backbutton.png"></a>
	<span>Forolibre</span>
</div>

<?php

echo '<div id="titulo_hilo">';
echo $titulo_hilo;
echo '</div>';


//----------------------FUNCIONES SANAS-----------------------------------------------------------------------

function paginacion(&$response){
	if(preg_match('/<ul class="ulpagenav">([\s\S]*?)<\/ul>/', $response, $paginacion)){
		preg_match_all('/(?<=href="showthread\.php\?t='.$_GET['t'].'&amp;page=)\d+/', $paginacion[0], $paginacion_paginas);

		$cuenta_paginacion_paginas = count($paginacion_paginas[0]);

		$string_paginacion = '<div class="paginacion">';

		if($cuenta_paginacion_paginas == 1 || $cuenta_paginacion_paginas == 0){ //Estamos en la utlima pagina
			$string_paginacion.= '<div>';

			$string_paginacion.= '<a href="showthread.php?t=';
			$string_paginacion.= $_GET['t'];
			$string_paginacion.= '&page=1"><strong>&#60;&#60;</strong>Primer</a>';

			$string_paginacion.= '<span>Pág ';
			$string_paginacion.= $_GET['page'];
			$string_paginacion.= ' de ';
			$string_paginacion.= $_GET['page'];
			$string_paginacion.= '</span>';

			$string_paginacion.= '</div>';
			$string_paginacion.= '<div>';

			$string_paginacion.= '<a href="showthread.php?t=';
			$string_paginacion.= $_GET['t'];
			$string_paginacion.= '&page=';
			$string_paginacion.= $_GET['page']-1;
			$string_paginacion.= '"><strong>&#60;&#60;</strong>Anterior </a>';
			$string_paginacion.= '|';

			$string_paginacion.= '</div>';
		}
		elseif($_GET['page'] == 1){
			$string_paginacion.= '<div>';

			$string_paginacion.= '<span>Pág ';
			$string_paginacion.= $_GET['page'];
			$string_paginacion.= ' de ';
			$string_paginacion.= $paginacion_paginas[0][0];
			$string_paginacion.= '</span>';

			$string_paginacion.= '<a href="showthread.php?t=';
			$string_paginacion.= $_GET['t'];
			$string_paginacion.= '&page=';
			$string_paginacion.= $paginacion_paginas[0][0];
			$string_paginacion.= '">Último<strong>&#62;&#62;</strong></a>';

			$string_paginacion.= '</div>';
			$string_paginacion.= '<div>';

			$string_paginacion.= '|';

			$string_paginacion.= '<a href="showthread.php?t=';
			$string_paginacion.= $_GET['t'];
			$string_paginacion.= '&page=';
			$string_paginacion.= $_GET['page']+1;
			$string_paginacion.= '"> Siguiente<strong>&#62;&#62;</strong></a>';

			$string_paginacion.= '</div>';
		}
		else{
			$string_paginacion.= '<div>';

			$string_paginacion.= '<a href="showthread.php?t=';
			$string_paginacion.= $_GET['t'];
			$string_paginacion.= '&page=1"><strong>&#60;&#60;</strong>Primer</a>';

			$string_paginacion.= '<span>Pág ';
			$string_paginacion.= $_GET['page'];
			$string_paginacion.= ' de ';
			$string_paginacion.= $paginacion_paginas[0][0];
			$string_paginacion.= '</span>';

			$string_paginacion.= '<a href="showthread.php?t=';
			$string_paginacion.= $_GET['t'];
			$string_paginacion.= '&page=';
			$string_paginacion.= $paginacion_paginas[0][0];
			$string_paginacion.= '">Último<strong>&#62;&#62;</strong></a>';

			$string_paginacion.= '</div>';
			$string_paginacion.= '<div>';

			$string_paginacion.= '<a href="showthread.php?t=';
			$string_paginacion.= $_GET['t'];
			$string_paginacion.= '&page=';
			$string_paginacion.= $_GET['page']-1;
			$string_paginacion.= '"><strong>&#60;&#60;</strong>Anterior </a>';

			$string_paginacion.= '|';

			$string_paginacion.= '<a href="showthread.php?t=';
			$string_paginacion.= $_GET['t'];
			$string_paginacion.= '&page=';
			$string_paginacion.= $_GET['page']+1;
			$string_paginacion.= '"> Siguiente<strong>&#62;&#62;</strong></a>';

			$string_paginacion.= '</div>';

		}
		$string_paginacion.= '</div>';
	}

	return $string_paginacion;
}

function procesar_mensaje(&$mensaje){
	@preg_match_all('/(?<=verVideo\(\')(.*?)(?=\')/', $mensaje, $videos);

	$mierda = array(
		'/(Citar\s\|[\s\S]+)|(Editar\s\|)/i',
		'/<script([\s\S]*?)<\/script>/',
		'/\s((class="(.*?)")|(border="(.*?)")|(alt="(.*?)")|(title="(.*?)"))/',
		'/\srel="(.*?)"/'
		);

	$mensaje = preg_replace($mierda, '', $mensaje);

	$mensaje = preg_replace('/(?<=href=")/', 'http://adf.ly/7810834/', $mensaje);	//Son ADFLYs SANOS

	echo trim(strip_tags($mensaje,'<br><img><a>'));
	echo '<br>';
	foreach($videos[0] as &$video){
		echo '<iframe src="http://www.youtube.com/embed/';
		echo $video;
		echo '"></iframe>';
	}
}

//------------------------------------------------------------------------------------------------------

echo $string_paginacion = paginacion($response);

echo '<div id="posts">';

preg_match_all('/<a name="post([\s\S]*?)<\/ul>/', $response, $posts);

foreach($posts[0] as &$post){

	echo '<div class="post">';


	echo '<div class="bloque_usuario">';

	preg_match('/<a class="ui-link" href="member\.php\?u=([\s\S]*?)<\/a>/', $post, $id_y_nick);
	//preg_match('/(?<=member\.php\?u=)\d+/', $id_y_nick[0], $id_usuario);									//ID PARA FUTURO MEMBER.PHP
	$nick = trim(strip_tags($id_y_nick[0]));
	preg_match('/(?<=<span class="postdate old">)(.*?)(?=<)/', $post, $fecha_post);

	echo '<div class="nick_y_fecha">';
	echo '<a href="#" onclick="return false;">'; //	echo '<a href="member.php?u=';echo $id_usuario[0];echo '">';
	echo $nick;
	echo '</a>';

	echo '<br>';

	echo '<span class="fecha_post">';
	echo $fecha_post[0];
	echo '</span>';

	echo '</div>';

	echo '<div class="avatar">';
	echo '<img src="http://';
	if(preg_match('/(?<=<img src="\/\/st.forocoches.com\/foro\/customavatars\/thumbs\/)(.*?)(?=">)/', $post, $avatar)){
		echo 'st.forocoches.com/foro/customavatars/thumbs/';
		echo $avatar[0];
	}
	else
		echo 'st.forocoches.com/iPhone/noavatar.png';
	echo '">';
	echo '</div>';
	echo '</div>';	//FIN BLOQUE USUARIO


	//--------------------------------CITAS-------------------------------------

	if(@preg_match('/(?<=<div class="pre-quote">)([\s\S]*?)(?=<\/div>)/', $post, $cita_usuario)){
		echo '<div class="cita">';

		echo '<div class="cita_usuario">';
		echo trim(strip_tags($cita_usuario[0]));
		echo '</div>';
		$post = preg_replace('/<div class="pre-quote">([\s\S]*?)<\/div>/', '', $post);

		echo '<div class="cita_mensaje">';
		preg_match('/<div class="post-quote"([\s\S]*?)<\/div>([\s\S]*?)<\/div>/', $post, $cita_mensaje);


		procesar_mensaje($cita_mensaje[0]);

		$post = preg_replace('/<div class="post-quote"([\s\S]*?)<\/div>([\s\S]*?)<\/div>/', '', $post);
		echo '</div>';

		echo '</div>';
	}

	//------------------------------------------------------------------------

	$post = preg_replace('/<em>([\s\S]*?)<\/em>/', '', $post); //Eliminamos ultima edicion

	echo '<div class="mensaje">';
	preg_match_all('/<li([\s\S]*?)<\/li>/', $post, $mensaje);

	procesar_mensaje($mensaje[0][1]);

	echo '</div>'; //FIN MENSAJE

	echo '</div>'; //FIN BLOQUE MENSAJE (BLOQUE USUARIO + MENSAJE)

}

echo '</div>'; //FIN DE LOS POST EN EL HILO

echo $string_paginacion;

?>

</body>
</html>