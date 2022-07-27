<?php

// Youtube API calls
function sna_youtube_last_video($api_key, $channel_id) {
	$json_url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=" . $channel_id . "&key=" . $api_key;
	$json = file_get_contents($json_url);
	$listFromYouTube = json_decode($json);
	$id = $listFromYouTube->items[0]->snippet->resourceId->videoId;
	return $id;
}

function sna_youtube_title($api_key, $video_id) {
	$video_title = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=" . $video_id . "&key=" . $api_key . "&part=snippet,statistics");
	if ($video_title) {
		$json = json_decode($video_title, true);
		return [$json['items'][0]['snippet']['title'], $json['items'][0]['snippet']['publishedAt']];
	} else {
		return false;
	}
}

function noticias_e_videos_shortcode()
{
	$retorno = '';

	// Fetch youtube content
	$api_key = '';
	$channel_id = '';
	$video_id = sna_youtube_last_video($api_key, $channel_id);
	$video = sna_youtube_title($api_key, $video_id);

	// Original news shortcode
	$id = 45;
	$args = array('category' => $id, 'numberposts' => 8);
	$categories = get_posts($args);
	$retorno .= '<div class="noticias">';
		$retorno .= '<div class="noticias-grupo-grupo efeito-landing-page-primeiro" style="flex-grow: 1;">';
	$retorno .= '<div class="widget widget_block"><h2 id="noticias-div-titulo" style="color: #1a447a">NOTÍCIAS</h2></div>';
	$retorno .= '<div class="widget widget_block"><hr class="wp-block-separator has-alpha-channel-opacity"></div>';
		$retorno .= '<div style="margin-top: 0;">';
	
		ob_start();
			dynamic_sidebar( 'sidebar-3' );
			$contents = ob_get_clean();
			$retorno .= $contents;
 		$retorno .= '<div class="widget widget_block" style="margin-bottom: 25px"><hr class="wp-block-separator has-alpha-channel-opacity"></div>';

	$retorno .= '</div>';

	$retorno .= '<div class="noticias-grupo">';
	foreach ($categories as $i => $category) {
		if ($i >= 0) {
			$retorno .= '<ul class="noticia-grupo">';
			$retorno .= '<li><a href="' . get_permalink($category->ID) . '">' . $category->post_title . '</a></li>';
			$data = new IntlDateFormatter('pt_BR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
			$data = $data->format(strtotime($category->post_date));
			$retorno .= '<p>' . $data . ' - ' . get_the_category($category->ID)[0]->name . '</p>';
			$retorno .= '</ul>';
		}
	}
	$retorno .= '</div>';
	$retorno .= '<div id="lives-e-videos-button" class="wp-block-button has-custom-font-size botao-home has-small-font-size noticias-div-titulo">';
	$retorno .= '<a class="wp-block-button__link has-white-color has-text-color has-background" href="https://projetos.aeronautas.org.br/category/noticias/" style="border-radius:10px;background-color:#1768b1;">+ notícias</a>';
	$retorno .= '</div>';
	$retorno .= '</div>'; // noticias grupo grupo 


	$retorno .= '<div class="noticia">';
	// Left-side div Video
	$retorno .= '<div id="lives-e-videos-container" class="efeito-landing-page-primeiro">';
	$retorno .= '<div id="add-separator">';
	// QUICK FIX SEPARATOR MOBILE NOTICIAS E VIDEOS 
	$retorno .= '<script>
		if (window.matchMedia("(max-width: 780px)").matches) {
			document.getElementById("add-separator").innerHTML = "<div class=\"widget widget_block\" style=\"margin-top: 25px;\"><hr class=\"wp-block-separator has-alpha-channel-opacity\"></div>" + document.getElementById("add-separator").innerHTML;
		}
	</script>';
	$retorno .= '<div class="widget widget_block" style="color: #1a447a"><h2>LIVES E VÍDEOS</h2></div>';
	$retorno .= '<div class="widget widget_block"><hr class="wp-block-separator has-alpha-channel-opacity"></div>';

	$retorno .= '<p id="lives-e-videos-title" style="font-size: var(--wp--preset--font-size--medium)">' . $video[0] . '</p>';
	$retorno .= '<p id="lives-e-videos-date" style="font-size: var(--wp--preset--font-size--medium); color: #1768b1;">' . date("d M, Y", strtotime(date(substr($video[1], 0, strpos($video[1], "T"))))) . '</p>';

	$retorno .= '</div>';
	$retorno .= '<div id="lives-e-videos-iframe" style="width: 100%">';
	$retorno .= '<iframe src="https://www.youtube.com/embed/' . $video_id . '" scrolling="no"></iframe>';
	$retorno .= '</div>';
	$retorno .= '<div id="lives-e-videos-button" class="wp-block-button has-custom-font-size botao-home has-small-font-size videos-button">';
	$retorno .= '<a class="wp-block-button__link has-white-color has-text-color has-background" href="https://projetos.aeronautas.org.br/videos/" style="border-radius:10px;background-color:#1768b1;">+ vídeos</a>';
	$retorno .= '</div>';
	$retorno .= '</div>';

	$retorno .= '</div>';
	// Right-side div News
	$retorno .= '</div>'; // noticias 

	$retorno .= '
    <style>
		.videos-button {
			display: flex;
			align-items: flex-end;
			height: -webkit-fill-available;
			margin-top: 20px;
		}
		@media (max-width: 780px) {
			.videos-button {
				display: block;
				height: auto;
			}
		}
		a {
			text-decoration: none !Important;
			color: #666;
		}
        .noticias {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }	
		@media (min-width: 800px) {
		    .noticias > div:first-child {
				margin-bottom: 0px;
				width: 40%;
			}
			.noticia-grupo {
				flex: none !important;
				width: 46% !important;
			}
		}
        .noticia {
            display: flex;
            flex-direction: column;
			margin-left: 20px;
        }
		@media (max-width: 1283px) {
			.noticia {
				margin-left: 0px;
				flex-grow: 1;
			}
			#lives-e-videos-iframe {
				height: calc(100vw / .3) !important;
			}
		}
        #noticia-title {
            font-size: var(--wp--preset--font-size--medium) !important;
        }
        #noticia-data  {
            font-size: var(--wp--preset--font-size--medium);
            color: #1768b1;
        }
		@media (min-width: 800px) {
			.noticia-grupo:nth-last-of-type(-n+2) {
				border-bottom: none;
			}
		}
        .noticias-grupo {
            display: flex;
            flex-direction: row;
			flex: 1;
            flex-wrap: wrap;
			min-width: 250px;
			justify-content: space-around;
        }
        .noticia-grupo {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 200px;
			border-bottom: solid #e5e5e5 1px;
			margin-bottom: 15px;
			margin-top: 0;
			list-style-type: disc;
        }
		.noticia-grupo:last-child { 
			border-bottom: none;
		}
        .noticia-grupo > a {
            font-size: var(--wp--preset--font-size--small);
        }
        .noticia-grupo > p {
            font-size: var(--wp--preset--font-size--small);
            color: #1768b1;
        }
    </style>
    ';

	// Youtube styling
	$retorno .= '
	<style>
		#lives-e-videos-container {
			display: flex;
			flex-direction: column;
			height: 100%;
		}
		#lives-e-videos-container > div {
			width: 100%;
		}
		#lives-e-videos-iframe {
			height: calc(100vw / .9);
			max-height: 436px;
		}
		@media (max-width: 780px) {
			#noticias-div-titulo {
				text-align: center;
			}
			.noticias-div-titulo {
				text-align: center;
			}
			#lives-e-videos-container {
				flex-direction: column;
				text-align: center;
			}
			#lives-e-videos-container > div {
				width: 100%;
			}
			#lives-e-videos-iframe {
				height: calc(100vw / 1.8) !Important;
			}
		}
	</style>
	';



	return $retorno;
}
