<?php

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')) require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/*
 * fslider view handler class
 */ 
class fslider_view extends WP_List_Table 
{

	/**
	 * Constructor, we override the parent to pass our own arguments
	 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	 */
	function __construct() 
	{
		parent::__construct();
	}

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() 
	{
		return array( 'id'=>__('Shortcode'), 'title'=>__('Title') );
	}
	
	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() 
	{
		global $wpdb;
	
		// preparing your query
		$query 		= "SELECT * FROM $wpdb->flexibleslider";
	
		$columns 	= $this->get_columns();
		
		$this->_column_headers = array($columns, array(), array());
	
		// fetch the items
		$this->items = $wpdb->get_results($query);
	}
	
	function column_title($item) 
	{
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&id=%s">Edit</a>','flexibleslider_edit',$item->id),
			'delete' => sprintf('<a class="fslider-delete" href="?page=%s&id=%s">Delete</a>','flexibleslider_delete',$item->id),
		);
		
		($item->title == '') ? $title = 'Slider ' . $item->width . 'px * ' . $item->height . 'px' : $title = $item->title;
		
		return sprintf('%1$s %2$s', $title, $this->row_actions($actions) );
	}
	
	function column_id($item)
	{
		return '[flexibleslider id="' . $item->id . '"]';
	}
	
	/*
	 * show the edit form
	*/
	function edit_form()
	{
		global $wpdb;
		
		$id 	= $_GET['id'];
	
		$query 	= "SELECT * FROM $wpdb->flexibleslider WHERE id = $id";
		
		$item 	= $wpdb->get_results($query);
		
		$item 	= $item[0];
	
		( $id == 0 ) ? $is_new = true : $is_new == false;
		?>
		
		<form action="?page=flexibleslider_save" method="post">
			<div class="wrap">
				<div id="icon-edit-comments" class="icon32"></div><h2><?php if($is_new) echo 'Add slider'; else echo 'Edit slider';?></h2>
				<div class='fslider-msg'><?php if($_GET['msg']) echo 'The slider is saved.' ?> &nbsp;</div>
				
				<input id="save-top" class="button" value="Save" type="submit" />
				<input name="saveclose" id="saveclose-top" class="button" value="Save & Close" type="submit" />
				<a href="?page=flexibleslider" class="button">Cancel</a>
				
				<div class="metabox-holder">
					<div class="stuffbox">
						<h3><label for="title">Slider options</label></h3>
						<table class="form-table editcomment">
							<tbody>
								<tr valign="top">
									<td class="first" width="25%">Title:</td>
									<td><input type="text" name="title" id="title" size="50" value="<?php echo $item->title ?>" /></td>
								</tr>								
								<tr valign="top">
									<td class="first">Width:</td>
									<td><input type="text" name="width" size="10" value="<?php if($is_new) echo '800'; else echo $item->width ?>" /> (Max width of a slide in pixel.)</td>
								</tr>
								<tr valign="top">
									<td class="first">Height:</td>
									<td><input type="text" name="height" size="10" value="<?php if($is_new) echo '480'; else echo $item->height ?>" /> (Max height of a slide in pixel.)</td>
								</tr>
								<tr valign="middle">
									<td class="first">Desktop Click Drag:</td>
									<td>
										<div id="drag-radio">
											<input type="radio" id="drag-yes" name="drag" value="1" <?php if($item->drag || $is_new) echo 'checked' ?> /><label for="drag-yes">Yes</label>
											<input type="radio" id="drag-no"  name="drag" value="0" <?php if(!$item->drag && !$is_new) echo 'checked' ?> /><label for="drag-no">No</label>
											&nbsp; (Desktop click and drag for the desktop slider.)
										</div>
									</td>
								</tr>										
								<tr>
									<td class="first">Auto Slide:</td>
									<td>
										<div id="auto-radio">
											<input type="radio" id="auto-yes" name="auto" value="1" <?php if($item->auto) echo 'checked' ?> /><label for="auto-yes">Yes</label>
											<input type="radio" id="auto-no"  name="auto" value="0" <?php if(!$item->auto) echo 'checked' ?> /><label for="auto-no">No</label>
											&nbsp; (Enables automatic cycling through slides.)
										</div>
									</td>
								</tr>
								<tr>
									<td class="first">Auto Slide Timer:</td>
									<td><input name="timer" size="10" value="<?php if($is_new) echo '5000'; else echo $item->timer ?>" type="text" /> (The time (in milliseconds) that a slide will wait before automatically navigating to the next slide.)</td>
								</tr>								
								<tr valign="middle">	
									<td class="first">Infinite Slider:</td>
									<td>
										<div id="infinite-radio">
											<input type="radio" id="infinite-yes" name="infinite" value="1" <?php if($item->infinite) echo 'checked' ?> /><label for="infinite-yes">Yes</label>
											<input type="radio" id="infinite-no"  name="infinite" value="0" <?php if(!$item->infinite) echo 'checked' ?> /><label for="infinite-no">No</label>
											&nbsp; (Makes the slider loop in both directions infinitely with no end.)
										</div>
									</td>
								</tr>
								<tr valign="middle">	
									<td class="first">Keyboard Controls:</td>
									<td>
										<div id="keyboard-radio">
											<input type="radio" id="keyboard-yes" name="keyboard" value="1" <?php if($item->keyboard) echo 'checked' ?> /><label for="keyboard-yes">Yes</label>
											<input type="radio" id="keyboard-no"  name="keyboard" value="0" <?php if(!$item->keyboard) echo 'checked' ?> /><label for="keyboard-no">No</label>
											&nbsp; (Left/right keyboard arrows can be used to navigate the slider.)
										</div>
									</td>
								</tr>
								<tr valign="top">
									<td class="first">Images:</td>
									<td>
										<?php if($is_new) : ?>
											Upload function will be enabled after this item is saved.
											<input type="hidden" id="uploadfiles" />
											<input type="hidden" id="runtime" />
										<?php else : ?>
											
											<input id="pickfiles" type="button" value="Select Images" class="button" />								                									                	
						                	<input id="uploadfiles" type="button" value="Upload Images" class="button" />
						                	<p>You are using the <span id="runtime">html5</span> uploader.</p>
						                	<p>Allowed extension: .jpg, .png, .gif. Maximum upload file size: 8MB</p>
						                	<div id="filelist"></div>
						                	<div id="ajax-load"></div>
						                	<div class="clr"></div>
											
											<!-- display images -->
                							<ul id="sortable">
											<?php
											if( $item->images != '') : 
												$images 		= explode('|', $item->images);
												$slidetitles 	= explode('|', $item->slidetitles);
												$links 			= explode('|', $item->links);
												$targets 		= explode('|', $item->targets);
												$descriptions 	= explode('|', $item->descriptions);
												
											?>
												<?php 
												for( $i = 0; $i < sizeof($images); $i++) : 
													$image 			= $images[$i]; 
													$slidetitle 	= $slidetitles[$i];
													$link 			= $links[$i];
													$target 		= $targets[$i];
													$description 	= $descriptions[$i];
												?>
													<li class="ui-state-default"> <!-- sortable element -->
														<img src='<?php echo plugins_url('', __FILE__);?>/images/<?php echo $item->id . '/thumb/' . $image?>' />
														
														<a href="#<?php echo $image ?>" class="fslider-image-delete">Delete</a>
														<a href="#<?php echo $image ?>" class="fslider-image-edit">Edit</a>
														
														<input type="hidden" class="slide-title" 	value="<?php echo $slidetitle ?>" />
														<input type="hidden" class="link" 			value="<?php echo $link ?>" />
														<input type="hidden" class="link-target" 	value="<?php echo $target ?>" />
														<input type="hidden" class="description" 	value="<?php echo $description ?>" />
													</li>
												<?php endfor; ?>
											<?php endif ?>
											</ul>
											<!-- end display images -->
											
										<?php endif ?>
									</td>
								</tr>
							</tbody>
						</table>
						
					</div>
				</div><!-- /post-body -->				
				<div class="metabox-holder">
					<div class="stuffbox">
						<h3><label for="title">Layout options</label></h3>
						<table class="form-table editcomment">
							<tbody>
								<tr>
									<td class="first" width="25%">Layout:</td>
									<td>
										<select id="type" name="type">
										<?php for( $i = 1; $i < 7; $i++ ) : ($item->type == $i)? $selected = 'selected' : $selected = ''; ?>
								        	<option value="<?php echo $i ?>" <?php echo $selected ?>>Style <?php echo $i ?></option>
								        <?php endfor; ?>
								        </select>
									</td>
								</tr>
								<tr>
									<td class="first">Title font:</td>
									<td>
										<select id="titlefont" name="titlefont">
											<option value="Oswald" <?php if( $is_new || $item->titlefont == 'Oswald') echo 'selected' ?>>Oswald</option>
											<option value="Arial" <?php if( $item->titlefont == 'Arial') echo 'selected' ?>>Arial</option>
											<option value="Tahoma" <?php if( $item->titlefont == 'Tahoma') echo 'selected' ?>>Tahoma</option>
											<option value="Times New Roman" <?php if( $item->titlefont == 'Times New Roman') echo 'selected' ?>>Times New Roman</option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="first">Title size:</td>
									<td><input type="text" name="titlesize" id="titlesize" size="10" value="<?php if($is_new) echo '20'; else echo $item->titlesize ?>" /> (In pixel.)</td>
								</tr>
								<tr>
									<td class="first">Title color:</td>
									<td>
										<input type="text" name="titlecolor" id="titlecolor" value="<?php if($is_new) echo '#FFFFFF'; else echo $item->titlecolor ?>" />
										<div id="titlecolorpicker"></div>
									</td>
								</tr>
								<tr>
									<td class="first">Description font:</td>
									<td>
										<select id="descfont" name="descfont">
											<option value="Oswald" <?php if( $is_new || $item->titlefont == 'Oswald') echo 'selected' ?>>Oswald</option>
											<option value="Arial" <?php if( $item->titlefont == 'Arial') echo 'selected' ?>>Arial</option>
											<option value="Tahoma"<?php if( $item->descfont == 'Tahoma') echo 'selected' ?>>Tahoma</option>
											<option value="Times New Roman" <?php if( $item->descfont == 'Times New Roman') echo 'selected' ?>>Times New Roman</option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="first">Description size:</td>
									<td><input type="text" name="descsize" id="descsize" size="10" value="<?php if($is_new) echo '11'; else echo $item->descsize ?>" /> (In pixel.)</td>
								</tr>
								<tr>
									<td class="first">Description color:</td>
									<td>
										<input type="text" name="desccolor" id="desccolor" value="<?php if($is_new) echo '#FFFFFF'; else echo $item->desccolor ?>" />
										<div id="desccolorpicker"></div>
									</td>
								</tr>
							</tbody>
						</table>
						<br/>
					</div>
				</div>						
						
				
				<input id="save-bt" class="button" value="Save" type="submit" />
				<input name="saveclose" id="saveclose-bt" class="button" value="Save & Close" type="submit" />
				<a href="?page=flexibleslider" class="button">Cancel</a>
			</div>
			
			<input type="hidden" id="itemid" 		name="itemid" 		value="<?php echo $id ?>" />
			
			<input type="hidden" id="images" 		name="images" 		value="<?php echo $item->images ?>" />
			<input type="hidden" id="slidetitles"  	name="slidetitles" 	value="<?php echo $item->slidetitles ?>" />
			<input type="hidden" id="links"  		name="links" 		value="<?php echo $item->links ?>" />
			<input type="hidden" id="targets" 		name="targets" 		value="<?php echo $item->targets ?>" />
			<input type="hidden" id="descriptions" 	name="descriptions" value="<?php echo $item->descriptions ?>" />
			
			<input type="hidden" id="url"    name="url"    value="<?php echo plugins_url('', __FILE__);?>" />
			<input type="hidden" id="close"  name="close"  value="0" />
		</form>
		
		<div id="dialog-form" title="Add/edit params">
		    <fieldset>
		        <label for="slide-title">Title</label>
		        <input type="text" name="slide-title" id="slide-title" value="" class="text ui-widget-content ui-corner-all" />
		        <label for="link">Link (Don't forget the <code>http://</code>)</label>
		        <input type="text" name="link" id="link" value="" class="text ui-widget-content ui-corner-all" />
		        <label for="link-target">Target</label>
		        <select name="link-target" id="link-target">
		        	<option value="">Current window or tab</option>
		        	<option value="_blank">New window or tab</option>
		        </select>
		        <label for="description">Description</label>
		        <textarea name="description" id="description"></textarea>
		    </fieldset>
		</div>
		
		<?php
	}
	
}
?>