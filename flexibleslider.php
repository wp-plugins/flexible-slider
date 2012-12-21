<?php
/*
Plugin Name: Flexible Slider
Plugin URI: http://youpick.biz
Description: A jquery responsive touch-enabled slider for Wordpress from YouPick
Version: 1.0
Author: YouPick
Author URI: http://youpick.biz
License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

require_once dirname(__FILE__) . '/view.php';
require_once dirname(__FILE__) . '/file.php';
require_once dirname(__FILE__) . '/html.php';

class flexibleslider
{
	function init_fslider()
	{
		global $wpdb;
		
		$wpdb->flexibleslider = $wpdb->prefix . 'flexibleslider';
		
		add_action('admin_menu', array('flexibleslider', 'fslider_menu'));
		
		add_action( 'admin_enqueue_scripts', array('flexibleslider', 'admin_scripts') );
		
		add_action( 'wp_enqueue_scripts',  array('flexibleslider', 'front_cripts') ); // add plugin css in front
		
		add_shortcode( 'flexibleslider', array('flexibleslider', 'fslider_shortcode') );
		
		register_activation_hook( __FILE__, array('flexibleslider', 'fslider_install') );
		
		register_uninstall_hook( __FILE__, array('flexibleslider', 'fslider_unintasll') );
	}
	
	function fslider_menu()
	{
		add_menu_page( 'Flexible Slider', 'FlexibleSlider', 'manage_options', 'flexibleslider', array('flexibleslider', 'fslider_list') );
		
		add_submenu_page( null, 'Add/Edit slider', 'Add/Edit slider', 'manage_options', 'flexibleslider_edit', array('flexibleslider', 'fslider_edit') );
		
		add_submenu_page( null, 'Delete slider', 'Delete slider', 'manage_options', 'flexibleslider_delete', array('flexibleslider', 'fslider_delete') );
		
		add_submenu_page( null, 'Save slider', 'Save slider', 'manage_options', 'flexibleslider_save', array('flexibleslider', 'fslider_save') );
		
		add_submenu_page( null, 'Upload image', 'Upload image', 'manage_options', 'flexibleslider_upload', array('flexibleslider', 'fslider_upload') );
		
		add_submenu_page( null, 'Delete image', 'Delete image', 'manage_options', 'flexibleslider_delete_image', array('flexibleslider', 'fslider_delete_image') );
	}
	
	/*
	 * list sliders from database
	 * 
	 */	
	function fslider_list()
	{
		$view = new fslider_view();
		
		$view->prepare_items();
		
		echo '<div class="wrap"><h2>Slider Manager <a class="add-new-h2" href="?page=flexibleslider_edit">Add New</a></h2>';
		
		if( $_GET['msg']== 1 ) echo '<p class="fslider-msg">The slider is deleted.</p>';
		else if ($_GET['msg'] == 2 ) echo '<p class="fslider-msg">The slider is saved.</p>';
		
		$view->display();
		
		// show the dialog
		echo '<div id="dialog-confirm" title="Delete the slider?"><p><span class="ui-icon ui-icon-alert"></span>These slider will be permanently deleted and cannot be recovered. Are you sure?</p></div>';
	}
	
	/*
	 * show the add/edit html form
	 */
	function fslider_edit()
	{
		$view = new fslider_view();
		$view->edit_form();
	}
	
	/*
	 * delete a slider
	 */
	function fslider_delete()
	{
		global $wpdb;
		
		$id 	= $_GET['id'];
		
		$file 	= new fslider_file(); // delete its images
		
		$file->deleteDir(dirname(__FILE__) . '/images/' . $id);
		
		$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->flexibleslider WHERE id = %d ", $id)); // delete info in db
		
		//wp_redirect('?page=flexibleslider&msg=' . $_GET['msg']); exit;
		echo "<meta http-equiv='refresh' content='0;url=?page=flexibleslider&msg=".$_GET['msg']."' />";

	}
	
	/*
	 * save a slider
	 */
	function fslider_save()
	{
		global $wpdb;
		
		$id = $_POST['itemid'];
		
		if(!$id) // create new slider
		{
			$data = array('id' 		=> '',
					'title' 		=> $_POST['title'],
					'width' 		=> $_POST['width'],
					'height' 		=> $_POST['height'],
					'drag' 			=> $_POST['drag'],
					'auto' 			=> $_POST['auto'],
					'timer' 		=> $_POST['timer'],
					'infinite' 		=> $_POST['infinite'],
					'keyboard' 		=> $_POST['keyboard'],
					'images' 		=> '',
					'slidetitles' 	=> '',
					'links' 		=> '',
					'targets' 		=> '',
					'descriptions' 	=> '',
					'type'			=> $_POST['type'],
					'titlefont'		=> $_POST['titlefont'],
					'titlecolor'	=> $_POST['titlecolor'],
					'titlesize'		=> $_POST['titlesize'],
					'descfont'		=> $_POST['descfont'],
					'desccolor'		=> $_POST['desccolor'],
					'descsize'		=> $_POST['descsize']
					
			);
		
			$wpdb->insert($wpdb->flexibleslider, $data);
		
			if($_POST['close']) {
				//wp_redirect('?page=flexibleslider&msg=2'); exit;
				echo "<meta http-equiv='refresh' content='0;url=?page=flexibleslider&msg=2' />";
			} else {
				//wp_redirect('?page=flexibleslider_edit&id='.$wpdb->insert_id.'&msg=1'); exit;
				echo "<meta http-equiv='refresh' content='0;url=?page=flexibleslider_edit&id=".$wpdb->insert_id."&msg=1' />";
			}
			
		}
		else // update a slider
		{
			// get current width and height
			$items = $wpdb->get_results("SELECT * FROM $wpdb->flexibleslider WHERE id = $id");
			
			// check if width or height is changed then re-crop the images
			if( $items[0]->width != $_POST['width'] OR $items[0]->height != $_POST['height'])
			{
				if( $items[0]->images != '' )
				{
					// the path
					$target 	= dirname(__FILE__) . '/images/' . $id;
					
					$file 		= new fslider_file();
			
					$images 	= explode('|', $items[0]->images);
					
					for( $i = 0; $i < sizeof($images); $i++)
					{
						$file->delete($target . '/crop/' . $images[$i]); // delete old images
						
						$ext = $file->getExt($images[$i]); // get ext
						
						$file->makethumbimage($target . '/' . $images[$i], $target . '/crop/' . $images[$i], $ext, $_POST['width'], $_POST['height']); // create new crop image
					}
				}
			}
			
			$data = array('title' 	=> $_POST['title'],
					'width' 		=> $_POST['width'],
					'height' 		=> $_POST['height'],
					'drag' 			=> $_POST['drag'],
					'auto' 			=> $_POST['auto'],
					'timer' 		=> $_POST['timer'],
					'infinite' 		=> $_POST['infinite'],
					'keyboard' 		=> $_POST['keyboard'],
					'images' 		=> $_POST['images'],
					'slidetitles' 	=> $_POST['slidetitles'],
					'links' 		=> $_POST['links'],
					'targets' 		=> $_POST['targets'],
					'descriptions' 	=> $_POST['descriptions'],
					'type'			=> $_POST['type'],
					'titlefont'		=> $_POST['titlefont'],
					'titlecolor'	=> $_POST['titlecolor'],
					'titlesize'		=> $_POST['titlesize'],
					'descfont'		=> $_POST['descfont'],
					'desccolor'		=> $_POST['desccolor'],
					'descsize'		=> $_POST['descsize']
			);
			
			$wpdb->update($wpdb->flexibleslider, $data, array('id' => $id));
			
			if($_POST['close']) {
				//wp_redirect('?page=flexibleslider&msg=2'); exit;
				echo "<meta http-equiv='refresh' content='0;url=?page=flexibleslider&msg=2' />";
			} else {
				//wp_redirect('?page=flexibleslider_edit&id='.$id.'&msg=1'); exit;
				echo "<meta http-equiv='refresh' content='0;url=?page=flexibleslider_edit&id=".$id."&msg=1' />";
			}
			
		}
	}
	
	/*
	 * upload a image
	 */
	function fslider_upload()
	{
		global $wpdb;
		
		$id 			= $_GET['itemid'];
		$uniquename 	= $_GET['uniquename'];
		
		$query 			= "SELECT * FROM $wpdb->flexibleslider WHERE id = $id";
		$items 			= $wpdb->get_results($query);
		
		$images 		= $items[0]->images;
		$slidetitles 	= $items[0]->slidetitles;
		$links  		= $items[0]->links ;
		$targets 		= $items[0]->targets;
		$descriptions 	= $items[0]->descriptions;
		
		if(flexibleslider::upload($id, $uniquename, $items[0]->width, $items[0]->height))
		{
			if ($images == '') {
				$images = $uniquename;
			} else {
				$images 		.= '|' . $uniquename;
				$slidetitles 	.= '|' . $slidetitles;
				$links 			.= '|' . $links;
				$targets 		.= '|' . $targets;
				$descriptions 	.= '|' . $descriptions;
			}
			
			$data = array(
					'images' 		=> $images,
					'slidetitles'	=> $slidetitles,
					'links' 		=> $links,
					'targets' 		=> $targets,
					'descriptions' 	=> $descriptions
			);
		
			// update db
			$wpdb->update($wpdb->flexibleslider, $data, array('id' => $id));
		}
	}
	
	/*
	 * delete a image
	 */
	function fslider_delete_image()
	{
		$id 			= $_GET['itemid'];
		$removedImage 	= $_GET['removedImage'];
		$remainedImages = $_GET['remainedImages'];
		$slidetitles 	= $_POST['remainedSlideTitles'];
		$links 			= $_POST['remainedLinks'];
		$targets 		= $_POST['remainedLinkTargets'];
		$descriptions 	= $_POST['remainedDescriptions'];
		
		// path
		$path 			= dirname(__FILE__) . '/images/' . $id . '/' . $removedImage;
		$paththumb 		= dirname(__FILE__) . '/images/' . $id . '/thumb/' . $removedImage;
		$pathcrop 		= dirname(__FILE__) . '/images/' . $id . '/crop/' . $removedImage;
		$pathbutton		= dirname(__FILE__) . '/images/' . $id . '/button/' . $removedImage;
		
		$file 			= new fslider_file();
		
		// delete the image
		if( $file->delete($path) )
		{
			// delete thumb
			$file->delete($paththumb);
			// delete crop
			$file->delete($pathcrop);
			// delete button
			$file->delete($pathbutton);
		
			global $wpdb;
			
			$data = array(
					'images' 		=> $remainedImages,
					'slidetitles'	=> $slidetitles,
					'links' 		=> $links,
					'targets' 		=> $targets,
					'descriptions' 	=> $descriptions
					);
			
			$wpdb->update($wpdb->flexibleslider, $data, array('id' => $id)); // update the remained images
		}
	}
	
	/*
	 * upload utility
	 */
	function upload($id, $name, $width, $height)
	{
		$dirname 	= dirname(__FILE__);
		$target 	= $dirname . '/images/' . $id;
		
		// the destination
		$dest 		= $target . '/' . $name;
		$destthumb 	= $target . '/thumb/' . $name;
		$destcrop 	= $target . '/crop/' . $name;
		$destbutton	= $target . '/button/' . $name;
		
		$file 		= new fslider_file();
		
		$ext 		= $file->getExt($name);
		$allowdExt 	= array('jpg', 'gif', 'png'); // check allowed extension
		
		if( !in_array($ext, $allowdExt) ) $ext = 'jpg'; // set default extension
		
		
		if( !$file->exists($target) ) $file->create($target); // Create target dir: images/$id		
		if( $file->exists($dirname . '/images/index.html') && !$file->exists($target . '/index.html') )
			$file->copy($dirname . '/images/index.html', $target . '/index.html'); // Copy index.html
		
		if( !$file->exists($target . '/thumb/') ) $file->create($target . '/thumb/'); // Create target dir: images/$id/thumb
		if( $file->exists($dirname . '/images/index.html') && !$file->exists($target . '/thumb/index.html') )
			$file->copy($dirname . '/images/index.html', $target . '/thumb/index.html'); // Copy index.html
		
		if( !$file->exists($target . '/crop/') ) $file->create($target . '/crop/'); // Create target dir: images/$id/crop
		if( $file->exists($dirname . '/images/index.html') && !$file->exists($target . '/crop/index.html') )
			$file->copy($dirname . '/images/index.html', $target . '/crop/index.html'); // Copy index.html
		
		if( !$file->exists($target . '/button/') ) $file->create($target . '/button/'); // Create target dir: images/$id/button
		if( $file->exists($dirname . '/images/index.html') && !$file->exists($target . '/button/index.html') )
			$file->copy($dirname . '/images/index.html', $target . '/button/index.html'); // Copy index.html
		
		// upload
		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']))
		{
			$file->upload($_FILES['file']['tmp_name'], $dest);
				
			$file->makethumbimage($dest, $destthumb, $ext); // create thumb
			$file->makethumbimage($dest, $destcrop, $ext, $width, $height); // crop image
			$file->makethumbimage($dest, $destbutton, $ext, 115, 60); // create button width 115px and height 60px
				
			return true;
				
		} 
		
		return false;
	}
	
	/*
	 * add admin scripts
	 */
	function admin_scripts($hook)
	{
		wp_enqueue_style( 'fslider-jquery-ui-style', plugins_url('/assets/jquery-ui.css', __FILE__) );
		wp_enqueue_style( 'fslider-style', plugins_url('/assets/style.css', __FILE__), array('fslider-jquery-ui-style') );
		
		if($hook == 'toplevel_page_flexibleslider')
		{
			wp_enqueue_script( 'fslider-jquery-bgiframe',  plugins_url('/assets/jquery.bgiframe-2.1.2.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog') );
			wp_enqueue_script( 'fslider-list', plugins_url('/assets/list.js', __FILE__), array('jquery') );
		}
		else if($hook == 'admin_page_flexibleslider_edit')
		{
			wp_enqueue_script( 'fslider-jquery-bgiframe',  plugins_url('/assets/jquery.bgiframe-2.1.2.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-sortable') );
			wp_enqueue_script( 'fslider-plupload', plugins_url('/assets/plupload.js', __FILE__), array('jquery') );
			
			if(!$_GET['id'])	{
				wp_enqueue_script( 'fslider-detail', plugins_url('/assets/detail.js', __FILE__), array('jquery', 'fslider-plupload') );
			} else {
				wp_enqueue_script( 'fslider-plupload-html5', plugins_url('/assets/plupload.html5.js', __FILE__), array('jquery', 'fslider-plupload') );
				wp_enqueue_script( 'fslider-plupload-flash', plugins_url('/assets/plupload.flash.js', __FILE__), array('jquery', 'fslider-plupload') );
				wp_enqueue_script( 'fslider-detail', plugins_url('/assets/detail.js', __FILE__), array('jquery', 'fslider-plupload', 'fslider-plupload-html5', 'fslider-plupload-flash') );
			}
			
			wp_enqueue_script( 'farbtastic' );
		}
	}
	
	/*
	 * add front script
	 */
	function front_cripts()
	{
		wp_enqueue_script( 'fslider-jquery-easing', plugins_url('/assets/jquery.easing-1.3.js', __FILE__), array('jquery') );
		wp_enqueue_script( 'fslider-slider', plugins_url('/assets/slider.js', __FILE__), array('jquery', 'fslider-jquery-easing') );
	}
	
	/*
	 * shortcode handler
	 */
	function fslider_shortcode($atts)
	{
		$id = 0; 
		
		extract(shortcode_atts(array('id' => 1), $atts));
		
		$html = new fslider_html();
		
	    return $html->slider($id);
	}
	
	/*
	 * fire intall function to create the table when installed
	 */
	function fslider_install()
	{
		global $wpdb;
		
		$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->flexibleslider . " (
			id int(11) NOT NULL AUTO_INCREMENT,
			title text NOT NULL,
			width smallint(4) NOT NULL,
			height smallint(4) NOT NULL,
			drag tinyint(1) NOT NULL,
			auto smallint(1) NOT NULL,
			timer int(11) NOT NULL,
			infinite tinyint(1) NOT NULL,
			keyboard tinyint(1) NOT NULL,
			images text NOT NULL,
			slidetitles text NOT NULL,
			links text NOT NULL,
			targets text NOT NULL,
			descriptions text NOT NULL,
			type smallint(4) NOT NULL,
			titlefont varchar(256) NOT NULL,
			titlecolor varchar(256) NOT NULL,
			titlesize smallint(4) NOT NULL,
			descfont varchar(256) NOT NULL,
			desccolor varchar(256) NOT NULL,
			descsize smallint(4) NOT NULL,
			PRIMARY KEY (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		dbDelta($sql);
	}
	
	/*
	 * fire uninstall function to drop the table when uninstalled
	 */
	function fslider_unintasll()
	{
		global $wpdb;
		
		$wpdb->query("DROP TABLE " . $wpdb->flexibleslider);
	}
}

flexibleslider::init_fslider();
?>