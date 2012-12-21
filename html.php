<?php 
/*
 * fslider front end view class
 */
class fslider_html
{
	/*
	 * show slider
	*/
	function slider($id)
	{
		global $wpdb, $flexibleslider;

		$query = "SELECT * FROM $wpdb->flexibleslider WHERE id = $id";

		$items = $wpdb->get_results($query);

		if( !$items[0] ) return '<p>The slideshow does not exist.</p>';

		$images 		= explode('|', $items[0]->images);
		$slidetitles 	= explode('|', $items[0]->slidetitles);
		$links 			= explode('|', $items[0]->links);
		$targets 		= explode('|', $items[0]->targets);
		$descriptions 	= explode('|', $items[0]->descriptions);

		$slider 		= 'flexibleSlider' . $id;
		$button 		= 'flexibleSliderButtons' . $id;

		$type			= $items[0]->type;

		$flexibleslider[] = array(
				'slider' 		=> $slider,
				'button' 		=> $button,
				'width' 		=> $items[0]->width,
				'height' 		=> $items[0]->height,
				'drag' 			=> $items[0]->drag,
				'auto' 			=> $items[0]->auto,
				'timer' 		=> $items[0]->timer,
				'infinite' 		=> $items[0]->infinite,
				'keyboard' 		=> $items[0]->keyboard,
				'type'			=> $type,
				'images'		=> $images,
				'id'			=> $id,
				'titlefont'		=>$items[0]->titlefont,
				'titlecolor'	=>$items[0]->titlecolor,
				'titlesize'		=>$items[0]->titlesize,
				'descfont'		=>$items[0]->descfont,
				'desccolor'		=>$items[0]->desccolor,
				'descsize'		=>$items[0]->descsize
		);

		add_action('wp_footer', array('fslider_html', 'scripts'));


		switch ($type)
		{
			case 1:
				$html = fslider_html::html_1($slider, $button, $id, $images);
				break;
			case 2:
				$html = fslider_html::html_2($slider, $button, $id, $images);
				break;
			case 3:
				$html = fslider_html::html_3($slider, $button, $id, $images);
				break;
			case 4:
				$html = fslider_html::html_4($slider, $button, $id, $images, $slidetitles, $links, $targets, $descriptions);
				break;
			case 5:
				$html = fslider_html::html_5($slider, $id, $images, $slidetitles, $links, $targets, $descriptions);
				break;
			case 6:
				$html = fslider_html::html_6($slider, $id, $images, $slidetitles, $links, $targets, $descriptions);
				break;
			default:
				$html = '';
				break;
		}

		return $html;
	}
	
	/*
	 * call the function to print scripts
	 */
	function scripts()
	{
		global $flexibleslider;

		foreach ($flexibleslider as $fslider)
		{
			$slider 		= $fslider['slider'];
			$button 		= $fslider['button'];
			$width 			= $fslider['width'];
			$height 		= $fslider['height'];
			$drag			= $fslider['drag'];
			$auto			= $fslider['auto'];
			$timer			= $fslider['timer'];
			$infinite		= $fslider['infinite'];
			$keyboard		= $fslider['keyboard'];
			$type			= $fslider['type'];
			$images			= $fslider['images'];
			$id				= $fslider['id'];
			$titlefont		= $fslider['titlefont'];
			$titlecolor		= $fslider['titlecolor'];
			$titlesize		= $fslider['titlesize'];
			$descfont		= $fslider['descfont'];
			$desccolor		= $fslider['desccolor'];
			$descsize		= $fslider['descsize'];
				
			switch ($type)
			{
				case 1:
					fslider_html::script_1($slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard);
					break;
				case 2:
					fslider_html::script_2($slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard);
					break;
				case 3:
					fslider_html::script_3($slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard, $images, $id);
					break;
				case 4:
					fslider_html::script_4(	$slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard,
											$images, $id, $titlefont, $titlecolor, $titlesize, $descfont, $desccolor, $descsize);
					break;
				case 5:
					fslider_html::script_5(	$slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard,
											$titlefont, $titlecolor, $titlesize, $descfont, $desccolor, $descsize);
					break;
				case 6:
					fslider_html::script_6(	$slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard,
											$titlefont, $titlecolor, $titlesize, $descfont, $desccolor, $descsize);
					break;
				default:
					break;
			}

		}
	}


	/*
	 * print js and css stype 1
	*/
	function script_1($slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard)
	{
		?>
		<script>
		jQuery(document).ready(function($) {
			
			$('#<?php echo $slider ?>').flexibleSlider({		
				desktopClickDrag: <?php echo $drag ?>,
				autoSlide: <?php echo $auto ?>,
				autoSlideTimer: <?php echo $timer ?>,
				infiniteSlider: <?php echo $infinite ?>,
				keyboardControls: <?php echo $keyboard ?>,
				
				navSlideSelector: $('#<?php echo $button ?> .button'),
				
				onSlideChange: slideContentChange_<?php echo $slider ?>,
				onSliderLoaded: slideContentLoaded_<?php echo $slider ?>
			});
			
			function slideContentChange_<?php echo $slider ?>(args) {
				$(args.sliderObject).parent().find('#<?php echo $button ?> .button').removeClass('selected');
				$(args.sliderObject).parent().find('#<?php echo $button ?> .button:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
			}
			
			function slideContentLoaded_<?php echo $slider ?>(args) {
				$(args.sliderObject).parent().find('#<?php echo $button ?> .button').removeClass('selected');
				$(args.sliderObject).parent().find('#<?php echo $button ?> .button:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
			}
			
		});
		</script>
		<style>
			#<?php echo $slider ?> {
				width: <?php echo $width ?>px;
				height: <?php echo $height ?>px;
			}
			
			#<?php echo $slider ?> .slider .item {
				width: <?php echo $width ?>px;
				height: <?php echo $height ?>px;
			}
			
			#<?php echo $slider ?> .slider .item img{
				width: 100%; 
				max-width: 100%;
				height: auto;
			}
			
			#<?php echo $button ?> {
				position: absolute;
				bottom: 10px;
				left: 10px;
				height: 10px;
			}
			
			#<?php echo $button ?> .button {
				float: left;
				width: 10px;
				height: 10px;
				background: #999;
				margin: 0 10px 0 0;
				opacity: 0.25;
				border: 1px solid #000;
			}
			
			#<?php echo $button ?> .selected {
				background: #000;
				opacity: 1;
			}
		</style>
		<?php
	}
	
	/*
	 * print html stype 1
	 */
	function html_1($slider, $button, $id, $images)
	{
		$html = "<div id='$slider'><div class = 'slider'>";
		
		foreach ($images as $image)
		{
			$image = plugins_url('', __FILE__) . '/images/' . $id . '/crop/' . $image;
			$html .= "<div class = 'item'><img src='$image' /></div>";
		}
		
		$html .= "</div><div id='$button'>";
			
		foreach ($images as $image)
		{
			$html .= "<div class = 'button'></div>";
		}
		
		$html .= "</div></div>";
		
		return $html;
	}
	
	/*
	 * print js and css stype 2
	 */
	function script_2($slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard)
	{
		?>
		<script>
		jQuery(document).ready(function($) {
			
			$('#<?php echo $slider ?>').flexibleSlider({		
				desktopClickDrag: <?php echo $drag ?>,
				autoSlide: <?php echo $auto ?>,
				autoSlideTimer: <?php echo $timer ?>,
				infiniteSlider: <?php echo $infinite ?>,
				keyboardControls: <?php echo $keyboard ?>,
				
				navSlideSelector: $('#<?php echo $button ?> .button'),
				
				onSlideChange: slideContentChange_<?php echo $slider ?>,
				onSliderLoaded: slideContentLoaded_<?php echo $slider ?>
			});
			
			function slideContentChange_<?php echo $slider ?>(args) {
				$(args.sliderObject).parent().find('#<?php echo $button ?> .button').removeClass('selected');
				$(args.sliderObject).parent().find('#<?php echo $button ?> .button:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
			}
			
			function slideContentLoaded_<?php echo $slider ?>(args) {
				$(args.sliderObject).parent().find('#<?php echo $button ?> .button').removeClass('selected');
				$(args.sliderObject).parent().find('#<?php echo $button ?> .button:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
			}
			
		});
		</script>
		<style>
			#<?php echo $slider ?> {
				width: <?php echo $width ?>px;
				height: <?php echo $height ?>px;
			}
			
			#<?php echo $slider ?> .slider .item {
				width: <?php echo $width ?>px;
				height: <?php echo $height ?>px;
			}
			
			#<?php echo $slider ?> .slider .item img{
				width: 100%; 
				max-width: 100%;
				height: auto;
			}
			
			#<?php echo $button ?> {
				position: absolute;
				bottom: 10px;
				left: 10px;
				height: 20px;
			}
			
			#<?php echo $button ?> .button {
				background: #999;
				border: 1px solid #000000;
				color: #FFFFFF;
				float: left;
				height: 20px;
				margin: 0 10px 0 0;
				opacity: 0.25;
				text-align: center;
				width: 20px;
				line-height: 20px;;
			}
			
			#<?php echo $button ?> .selected {
				background: #000;
				opacity: 0.75;
			}
		</style>
		<?php
	}
	
	/*
	 * print html stype 2
	 */
	function html_2($slider, $button, $id, $images)
	{
		$html = "<div id='$slider'><div class = 'slider'>";
		
		foreach ($images as $image)
		{
			$image = plugins_url('', __FILE__) . '/images/' . $id . '/crop/' . $image;
			$html .= "<div class = 'item'><img src='$image' /></div>";
		}
		
		$html .= "</div><div id='$button'>";
			
		for ($i = 1; $i <= sizeof($images); $i++)
		{
			$html .= "<div class = 'button'>$i</div>";
		}
		
		$html .= "</div></div>";
		
		return $html;
	}
	
	/*
	 * print js and css style 3
	 */
	function script_3($slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard, $images, $id)
	{
		?>
		<script>
		jQuery(document).ready(function() {
			
			jQuery('#<?php echo $slider ?>').flexibleSlider({
				desktopClickDrag: <?php echo $drag ?>,
				autoSlide: <?php echo $auto ?>,
				autoSlideTimer: <?php echo $timer ?>,
				infiniteSlider: <?php echo $infinite ?>,
				keyboardControls: <?php echo $keyboard ?>,
				
				navSlideSelector: jQuery('#<?php echo $button ?> .button'),
				
				onSlideChange: slideContentChange_<?php echo $slider ?>,
				onSliderLoaded: slideContentChange_<?php echo $slider ?>
				
			});
			
			function slideContentChange_<?php echo $slider ?>(args) {
				jQuery('#<?php echo $button ?> .button').removeClass('selected');
				jQuery('#<?php echo $button ?> .button:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
			}
			
		});
		</script>
		<style>
			#<?php echo $slider ?> {
				width: <?php echo $width ?>px;
				height: <?php echo $height ?>px;
			}
			
			#<?php echo $slider ?> .slider {
				width: 100%;
				height: 100%;
			}
			
			#<?php echo $slider ?> .slider .item {
				position: relative;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				margin: 0;
				background: #aaa;
			}
			
			#<?php echo $slider ?> .slider .item img{
				width: 100%; 
				max-width: 100%;
				height: auto;
			}
			
			#<?php echo $button ?> {
				overflow: hidden;
				position: absolute;
				bottom: 0;
				padding: 10px 0;
				width: 100%;
			}
			
			#<?php echo $button ?> .button {
				float: left;
				margin: 10px 0 0 10px;
				width: 115px; /* fixed: width of the thumb */
				height: 60px; /* fixed: height of the thumb */
				opacity: 0.5;
			}
			
			#<?php echo $button ?> .button .border {
				border: 5px solid #000;
				width: 105px;
				height: 50px;
				opacity: 0.3;
			}
			
				
			<?php for( $i = 0; $i < sizeof($images); $i++ ) : $image = plugins_url('', __FILE__) . '/images/' . $id . '/button/' . $images[$i]; ?>
			#<?php echo $button ?> #<?php echo $button ?>-item<?php echo $i ?> {
				background: url(<?php echo $image ?>) no-repeat 0 0;
			}
			<?php endfor; ?>
			
			#<?php echo $button ?> .selected,
			#<?php echo $button ?> .button:hover {
				opacity: 1;
			}
		</style>
		<?php
	}
	
	/*
	 * print html style 3
	 */
	function html_3($slider, $button, $id, $images)
	{
		$html = "<div id='$slider'><div class = 'slider'>";
		
		foreach ($images as $image)
		{
			$image = plugins_url('', __FILE__) . '/images/' . $id . '/crop/' . $image;
			$html .= "<div class = 'item'><img src='$image' /></div>";
		}
		
		$html .= "</div><div id='$button'>";
		
		for($i = 0; $i < sizeof($images); $i++)
		{
			if( $i == 0 ) $html .= "<div class = 'button first' id = '$button-item$i'><div class = 'border'></div></div>";
			else $html .= "<div class = 'button' id = '$button-item$i'><div class = 'border'></div></div>";
		}
		
		$html .= "</div></div>";
		
		return $html;
	}
	
	/*
	 * print js and css stype 4
	 */
	function script_4(	$slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard, 
						$images, $id, $titlefont, $titlecolor, $titlesize, $descfont, $desccolor, $descsize)
	{
		?>
		<script>
		jQuery(document).ready(function() {
			jQuery('#<?php echo $slider ?>').flexibleSlider({
				desktopClickDrag: <?php echo $drag ?>,
				autoSlide: <?php echo $auto ?>,
				autoSlideTimer: <?php echo $timer ?>,
				infiniteSlider: <?php echo $infinite ?>,
				keyboardControls: <?php echo $keyboard ?>,
				
				navSlideSelector: jQuery('#<?php echo $button ?> .button'),
				
				onSlideChange: slideContentChange_<?php echo $slider ?>,
				onSliderLoaded: slideContentChange_<?php echo $slider ?>
				
			});
			
			function slideContentChange_<?php echo $slider ?>(args) {
				jQuery('#<?php echo $button ?> .button').removeClass('selected');
				jQuery('#<?php echo $button ?> .button:eq(' + (args.currentSlideNumber - 1) + ')').addClass('selected');
			}
			
		});
		</script>
		<style>
			<?php if( $titlefont == 'Oswald') echo '@import url(http://fonts.googleapis.com/css?family=Oswald);' ?>
			
			.clear {
				clear: both;
			}

			#<?php echo $slider ?> {
				width: <?php echo $width ?>px;
				height: <?php echo $height ?>px;
			}
			
			#<?php echo $slider ?> .slider {
				width: 100%;
				height: 100%;
			}
			
			#<?php echo $slider ?> .slider .item {
				position: relative;
				width: 100%;
				height: 100%;
				margin: 0;
				background: #aaa;
			}
			
			#<?php echo $slider ?> .slider .item .caption {
				position: absolute;
				bottom: 0;
				width: 100%;
				padding: 10px 0;
				display: block;
			}
			
			#<?php echo $slider ?> .slider .item .caption .bg {
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				height: 100%;	
				background: #000;
				opacity: 0.5;
				z-index: 0;
			}
			
			#<?php echo $slider ?> .slider .item .caption .title {
				font-size: <?php echo $titlesize ?>px;
				font-family: "<?php echo $titlefont ?>";
				color: <?php echo $titlecolor ?>;
				position: relative;
				top: 0;
				left: 0;
				z-index: 1;
				padding: 0 10px;
				margin: 0 0 10px 0;
			}
			
			#<?php echo $slider ?> .slider .item .caption .title a {
				font-size: <?php echo $titlesize ?>px;
				font-family: "<?php echo $titlefont ?>";
				color: <?php echo $titlecolor ?>;
				text-decoration: none;
			}
			
			#<?php echo $slider ?> .slider .item .caption .description {
				font: normal <?php echo $descsize ?>px/<?php echo $descsize + 5 // line height?>px "<?php echo $descfont // font ?>";
				color: <?php echo $desccolor ?>;
				position: relative;
				top: 0;
				left: 0;
				z-index: 1;
				padding: 0 10px;
				margin: 0;
			}
			
			#<?php echo $slider ?> .slider .item .caption .description a {
				font: normal <?php echo $descsize ?>px/<?php echo $descsize + 5 // line height?>px "<?php echo $descfont // font ?>";
				color: <?php echo $desccolor ?>;
				text-decoration: underline;
			}
				
			#<?php echo $slider ?> .slider .item img{
				width: 100%; 
				height: auto;
				max-width: 100%;
			}
			
			#<?php echo $button ?> {
				height: 60px; /* fixed: height of the thumb */
			}
			
			#<?php echo $button ?> .button {
				float: left;
				margin: 10px 10px 0 0;
				width: 115px; /* fixed: width of the thumb */
				height: 60px; /* fixed: height of the thumb */
				opacity: 0.5;
				filter: alpha(opacity:50);
			}
			
			#<?php echo $button ?> .button .border {
				border: 5px solid #000;
				opacity: 0.5;
				width: 105px;
				height: 50px;
			}
			
			<?php for( $i = 0; $i < sizeof($images); $i++ ) : $image = plugins_url('', __FILE__) . '/images/' . $id . '/button/' . $images[$i]; ?>
			#<?php echo $button ?> #<?php echo $button ?>-item<?php echo $i ?> {
				background: url(<?php echo $image ?>) no-repeat 0 0;
			}
			<?php endfor; ?>
			
			#<?php echo $button ?> .first {
				margin-left: 0;
			}
			
			#<?php echo $button ?> .selected,
			#<?php echo $button ?> .button:hover {
				opacity: 1;
			}
		</style>
		<?php
	}
	
	/*
	 * print html stype 4
	 */
	function html_4($slider, $button, $id, $images, $slidetitles, $links, $targets, $descriptions)
	{
		$html = "<div id='$slider'><div class = 'slider'>";

		for($i = 0; $i < sizeof($images); $i++)
		{
			$image = plugins_url('', __FILE__) . '/images/' . $id . '/crop/' . $images[$i];
			$html .= "<div class = 'item'><img src='$image' /><div class = 'caption'>";
			
			( $targets[$i] == '_blank' )? $target = "target='_blank'" : $target = '';
			
			if( $links[$i] ) $title = "<p class='title'><a href='$links[$i]' $target>".base64_decode($slidetitles[$i])."</a></p>";
			else  $title = "<p class='title'>".base64_decode($slidetitles[$i])."</p>";
			
			if( $slidetitles[$i] ) $html .= $title;
			if( $descriptions[$i] ) $html .= "<p class='description'>".base64_decode($descriptions[$i])."</p>";
			if( $slidetitles[$i] || $descriptions[$i] ) $html .= "<div class = 'bg'></div>";
			
			$html .= "</div></div>";
			
		}
		
		$html .= "</div></div>";
		
		$html .= "<div id='$button'>";
		
		for($i = 0; $i < sizeof($images); $i++)
		{
			if( $i == 0 ) $html .= "<div class = 'button first' id = '$button-item$i'><div class = 'border'></div></div>";
			else $html .= "<div class = 'button' id = '$button-item$i'><div class = 'border'></div></div>";
		}
		
		$html .= "</div><div class='clear'></div>";
		
		return $html;
	}
	
	/*
	 * print js and css style 5
	 */
	function script_5(	$slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard,
						$titlefont, $titlecolor, $titlesize, $descfont, $desccolor, $descsize)
	{
		?>
		<script>
		jQuery(document).ready(function() {
			jQuery('#<?php echo $slider ?>').flexibleSlider({
				desktopClickDrag: <?php echo $drag ?>,
				autoSlide: <?php echo $auto ?>,
				autoSlideTimer: <?php echo $timer ?>,
				infiniteSlider: <?php echo $infinite ?>,
				keyboardControls: <?php echo $keyboard ?>,
				
				navPrevSelector: jQuery('#<?php echo $slider ?> .prevButton'),
				navNextSelector: jQuery('#<?php echo $slider ?> .nextButton')
			});
			
		});
		</script>
		<style>
			<?php if( $titlefont == 'Oswald') echo '@import url(http://fonts.googleapis.com/css?family=Oswald);' ?>
			
			#<?php echo $slider ?> {
				width: <?php echo $width ?>px;
				height: <?php echo $height ?>px;
			}
			
			#<?php echo $slider ?> .slider {
				width: 100%;
				height: 100%;
			}
			
			#<?php echo $slider ?> .slider .item {
				position: relative;
				width: 100%;
				height: 100%;
				margin: 0;
				background: #aaa;
			}
			
			#<?php echo $slider ?> .slider .item .caption {
				position: absolute;
				bottom: 0;
				width: 100%;
				padding: 10px 0;
				display: block;
			}
			
			#<?php echo $slider ?> .slider .item .caption .bg {
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				height: 100%;	
				background: #000;
				opacity: 0.5;
				z-index: 0;
			}
			
			#<?php echo $slider ?> .slider .item .caption .title {
				font-size: <?php echo $titlesize ?>px;
				font-family: "<?php echo $titlefont ?>";
				color: <?php echo $titlecolor ?>;
				position: relative;
				top: 0;
				left: 0;
				z-index: 1;
				padding: 0 10px;
				margin: 0 0 10px 0;
			}
			
			#<?php echo $slider ?> .slider .item .caption .title a {
				font-size: <?php echo $titlesize ?>px;
				font-family: "<?php echo $titlefont ?>";
				color: <?php echo $titlecolor ?>;
				text-decoration: none;
			}
			
			#<?php echo $slider ?> .slider .item .caption .description {
				font: normal <?php echo $descsize ?>px/<?php echo $descsize + 5 // line height?>px "<?php echo $descfont // font ?>";
				color: <?php echo $desccolor ?>;
				position: relative;
				top: 0;
				left: 0;
				z-index: 1;
				padding: 0 10px;
				margin: 0;
			}
			
			#<?php echo $slider ?> .slider .item .caption .description a {
				font: normal <?php echo $descsize ?>px/<?php echo $descsize + 5 // line height?>px "<?php echo $descfont // font ?>";
				color: <?php echo $desccolor ?>;
				text-decoration: underline;
			}
			
			#<?php echo $slider ?> .slider .item img{
				width: 100%; 
				height: auto;
				max-width: 100%;
			}
			
			#<?php echo $button ?> {
				height: 60px; /* fixed: height of the thumb */
			}
			
			#<?php echo $button ?> .button {
				float: left;
				margin: 10px 10px 0 0;
				width: 115px; /* fixed: width of the thumb */
				height: 60px; /* fixed: height of the thumb */
				opacity: 0.5;
				filter: alpha(opacity:50);
			}
			
			#<?php echo $button ?> .button .border {
				border: 5px solid #000;
				opacity: 0.5;
				width: 105px;
				height: 50px;
			}
			
			#<?php echo $slider ?> .prevButton {
				position: absolute;
				top: 50%;
				left: 10px;
				width: 20px;
				height: 40px;
				background: url(<?php echo plugins_url('', __FILE__) ?>/assets/images/slider-buttons.png) no-repeat 0 0;
				margin-top: -20px;
				z-index: 2;
			}
			
			#<?php echo $slider ?> .nextButton {
				position: absolute;
				top: 50%;
				right: 10px;
				width: 20px;
				height: 40px;
				background: url(<?php echo plugins_url('', __FILE__) ?>/assets/images/slider-buttons.png) no-repeat 100% 0;
				margin-top: -20px;
				z-index: 2;
			}
		</style>
		<?php
	}
	
	/*
	 * print html style 5
	 */
	function html_5($slider, $id, $images, $slidetitles, $links, $targets, $descriptions)
	{
		$html = "<div id='$slider'><div class = 'slider'>";
		
		for($i = 0; $i < sizeof($images); $i++)
		{
			$image = plugins_url('', __FILE__) . '/images/' . $id . '/crop/' . $images[$i];
			$html .= "<div class = 'item'><img src='$image' /><div class = 'caption'>";
				
			( $targets[$i] == '_blank' )? $target = "target='_blank'" : $target = '';
				
			if( $links[$i] ) $title = "<p class='title'><a href='$links[$i]' $target>".base64_decode($slidetitles[$i])."</a></p>";
			else  $title = "<p class='title'>".base64_decode($slidetitles[$i])."</p>";
				
			if( $slidetitles[$i] ) $html .= $title;
			if( $descriptions[$i] ) $html .= "<p class='description'>".base64_decode($descriptions[$i])."</p>";
			if( $slidetitles[$i] || $descriptions[$i] ) $html .= "<div class = 'bg'></div>";
				
			$html .= "</div></div>";
		}
		
		$html .= "</div><div class = 'prevButton'></div><div class = 'nextButton'></div></div>";
		
		return $html;
	}
	
	/*
	 * print js and css style 6
	 */
	function script_6(	$slider, $button, $width, $height, $drag, $auto, $timer, $infinite, $keyboard,
											$titlefont, $titlecolor, $titlesize, $descfont, $desccolor, $descsize)
	{
		?>
		<script>
		jQuery(document).ready(function() {
			jQuery('#<?php echo $slider ?>').flexibleSlider({
				desktopClickDrag: <?php echo $drag ?>,
				autoSlide: <?php echo $auto ?>,
				autoSlideTimer: <?php echo $timer ?>,
				infiniteSlider: <?php echo $infinite ?>,
				keyboardControls: <?php echo $keyboard ?>,

				navPrevSelector: jQuery('#<?php echo $slider ?> .prevButton'),
				navNextSelector: jQuery('#<?php echo $slider ?> .nextButton')
			});
				
		});
		</script>
		<style>
		<?php if( $titlefont == 'Oswald') echo '@import url(http://fonts.googleapis.com/css?family=Oswald);' ?>
		
		#<?php echo $slider ?> {
			position: relative;
			top: 0;
			left: 0;
			overflow: hidden;
			width: <?php echo $width ?>px;
			height: <?php echo $height ?>px;
		}
		
		#<?php echo $slider ?> .prevButton {
			position: absolute;
			top: 50%;
			left: 5px;
			width: 20px;
			height: 40px;
			background: url(<?php echo plugins_url('', __FILE__) ?>/assets/images/slider-buttons.png) no-repeat 0 0;
			margin-top: -20px;
			z-index: 2;
		}
		
		#<?php echo $slider ?> .nextButton {
			position: absolute;
			top: 50%;
			right: 5px;
			width: 20px;
			height: 40px;
			background: url(<?php echo plugins_url('', __FILE__) ?>/assets/images/slider-buttons.png) no-repeat 100% 0;
			margin-top: -20px;
			z-index: 2;
		}
		
		#<?php echo $slider ?> .slider {
			width: 100%;
			height: 100%;
		}
		
		#<?php echo $slider ?> .slider .item {
			position: relative;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			margin: 0;
		}
		
		#<?php echo $slider ?> .slider .item .text {
			position: absolute;
			right: 0;
			width: 170px;
			height: 100%;
			padding: 0 30px 0 0;
			bottom: 0;
		}
		
		#<?php echo $slider ?> .slider .item .text .bg {
			position: absolute;
			top: 0;
			right: 0;
			width: 220px;
			height: 100%;
			background: #000;
			opacity: 0.5;
		}
		
		#<?php echo $slider ?> .slider .item img {
			width: 100%;
			max-width: 100%;
			height: auto;
		}
		
		#<?php echo $slider ?> .slider .item .text .title {
			position: relative;
			margin: 20px 0 0 0;
		}
		
		#<?php echo $slider ?> .slider .item .text .title span {
			font-size: <?php echo $titlesize ?>px;
			font-family: "<?php echo $titlefont ?>";
			color: <?php echo $titlecolor ?>;
		}
		
		#<?php echo $slider ?> .slider .item .desc {
			position: relative;
			margin: 10px 0 0 0;
		}
		
		#<?php echo $slider ?> .slider .item .text .desc span {
			font: normal <?php echo $descsize ?>px/<?php echo $descsize + 5 // line height?>px "<?php echo $descfont // font ?>";
			color: <?php echo $desccolor ?>;
		}
		
		#<?php echo $slider ?> .slider .item .text .desc a {
			text-decoration: underline;
			color: #fff;
		}
		
		#<?php echo $slider ?> .slider .item .text .button {
			position: absolute;
			padding: 0 10px 0 10px;
			margin: 10px 0 0 0;
			background: #aaa;
			border: 1px solid #000;
			cursor: pointer;
		}
		
		#<?php echo $slider ?> .slider .item .text .button span {
			color: #000;
			font: normal <?php echo $descsize ?>px/30px <?php echo $descfont // font ?>;
			text-shadow: 0 1px 1px #fff;
		}
		
		#<?php echo $slider ?> .slider .item .text .button span a {
			color: #000;
			text-decoration: none;
		}
		</style>
		<?php
	}
	
	/*
	 * print html style 6
	 */
	function html_6($slider, $id, $images, $slidetitles, $links, $targets, $descriptions)
	{
		$html = "<div id='$slider'><div class = 'slider'>";
		
		for($i = 0; $i < sizeof($images); $i++)
		{
			$image = plugins_url('', __FILE__) . '/images/' . $id . '/crop/' . $images[$i];
			$html .= "<div class = 'item'><img src='$image' />";
			
			if( $slidetitles[$i] || $descriptions[$i] )
			{
				$html .= "<div class = 'text'><div class = 'bg'></div>";
				if( $slidetitles[$i] ) $html .= "<div class = 'title'><span>".base64_decode($slidetitles[$i])."</span></div>";
				if( $descriptions[$i] ) $html .= "<div class = 'desc'><span>".base64_decode($descriptions[$i])."</span></div>";
				( $targets[$i] == '_blank' )? $target = "target='_blank'" : $target = '';
				if( $links[$i] ) $html .= "<div class = 'button'><span><a href='$links[$i]' $target>Read More</a></span></div>";
				$html .= "</div>";
			}
			
			$html .= "</div>";
		}
		
		$html .= "</div><div class = 'prevButton'></div><div class = 'nextButton'></div></div>";
		
		return $html;
	}
}
?>