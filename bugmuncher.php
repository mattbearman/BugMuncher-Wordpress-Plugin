<?php
/*
Plugin Name: BugMuncher for WordPress
Plugin URI: http://bugmuncher.com/wordpress.html
Description: BugMuncher is a website feedback widget that allows your users to create screenshots of your website and send them directly to you.
Version: 1.0
Author: Matt Bearman
Author URI: http://bugmuncher.com
License: MIT
*/

// register the admin page hook
add_action('admin_menu', 'bugmuncher_menu');

// register the blog display hook
add_action('wp_footer', 'bugmuncher_inject');

function bugmuncher_menu() {
	add_menu_page('BugMuncher Options', 'BugMuncher', 'manage_options', 'bugmuncher', 'bugmuncher_options' );
}

function bugmuncher_options() {
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	
	// Array of options
	$options = array(
		'api_key'=>array(
			'label'=>'API Key',
			'type'=>'text',
			'default'=>''
		),
		'label_text'=>array(
			'label'=>'Label Text',
			'type'=>'text',
			'default'=>'Feedback'
		),
		'language'=>array(
			'label'=>'Language',
			'type'=>'select',
			'options'=>array(
				'English'=>'en',
				'Svenska'=>'se'
			)
		),
		'position'=>array(
			'label'=>'Position',
			'type'=>'select',
			'options'=>array(
				'Right'=>'right',
				'Left'=>'left'
			)
		),
		'show_intro'=>array(
			'label'=>'Show Intro',
			'type'=>'select',
			'options'=>array(
				'Yes'=>'true',
				'No'=>'false'
			)
		),
		'show_preview'=>array(
			'label'=>'Show Preview',
			'type'=>'select',
			'options'=>array(
				'Yes'=>'true',
				'No'=>'false'
			)
		)
	);
	 
	// See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if(isset($_POST['submit'])) {
    	foreach($options as $name=>$details) {
       		// Save the posted value in the database
        	update_option('bugmuncher_'.$name, $_POST['bugmuncher_'.$name]);
		}

        // Put an settings updated message on the screen
		?>
		<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
		<?php

    }
	
	?>
	<div class="wrap">
		<h2>BugMuncher Options</h2>
		<h3>You need a BugMuncher account to use this plug in - <a href="https://app.bugmuncher.com/account/sign_up" target="_blank">Get your account now</a></h3>
		<form method="post">
			<?foreach($options as $name=>$details):?>
			<p>
				<label style="width: 90px; display: inline-block;"><?=$details['label']?>:</label>
				<?if($details['type']=='text'):?>
				<input type="text" name="bugmuncher_<?=$name?>" value="<?=get_option('bugmuncher_'.$name, $details['default'])?>" size="40">
				<?elseif($details['type'] == 'select'):?>
				<select name="bugmuncher_<?=$name?>">
					<?foreach($details['options'] as $opt_name=>$opt_value):?>
					<option value="<?=$opt_value?>"<?=(get_option('bugmuncher_'.$name) == $opt_value ? ' selected="selected"' : '')?>><?=$opt_name?></option>
					<?endforeach?>
				</select>
				<?endif?>
			</p>
			<?endforeach?>
			
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
		
		</form>
	</div>
	<?
}

function bugmuncher_inject() {
	?>
	<script type="text/javascript"> 
		(function(){ 
			var node = document.createElement("script"); 
			node.setAttribute("type", "text/javascript"); 
			node.setAttribute("src", "https://app.bugmuncher.com/js/bugMuncher.min.js"); 
			document.getElementsByTagName("head")[0].appendChild(node); 
		})(); 
	
		var bugmuncher_options = {
			language:'<?=get_option('bugmuncher_language')?>',
			position:'<?=get_option('bugmuncher_position')?>',
			show_intro:<?=get_option('bugmuncher_show_intro')?>,
			show_preview:<?=get_option('bugmuncher_show_preview')?>,
			label_text:'<?=get_option('bugmuncher_label_text')?>',
			api_key:'<?=get_option('bugmuncher_api_key')?>'
		}
	</script>
	<?php
}
?>