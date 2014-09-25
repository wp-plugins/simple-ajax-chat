<?php // Simple Ajax Chat > Chat Form

if (!function_exists('add_action')) die();

session_start();
// $_SESSION['user_token'] = uniqid();

function simple_ajax_chat() {
	
	global $wpdb, $table_prefix, $user_level, $user_ID, $sac_path, $sac_number_of_comments, $sac_options; 
	
	$use_url         = $sac_options['sac_use_url'];
	$use_textarea    = $sac_options['sac_use_textarea'];
	$registered_only = $sac_options['sac_registered_only']; 
	$enable_styles   = $sac_options['sac_enable_style'];
	$play_sound      = $sac_options['sac_play_sound'];
	$chat_order      = $sac_options['sac_chat_order'];
	$use_username    = $sac_options['sac_logged_name'];
	
	$display_order = 'DESC';
	if ($chat_order) $display_order = 'ASC';
	
	$custom_styles = '';
	if ($enable_styles) $custom_styles = '<style type="text/css">' . $sac_options['sac_custom_styles'] . '</style>';
	
	$custom_chat_pre = '';
	if ($sac_options['sac_content_chat'] !== '') $custom_chat_pre = $sac_options['sac_content_chat'];
	
	$custom_form_pre = '';
	if ($sac_options['sac_content_form'] !== '') $custom_form_pre = $sac_options['sac_content_form'];
	
	$custom_chat_app = '';
	if ($sac_options['sac_chat_append'] !== '') $custom_chat_app = $sac_options['sac_chat_append'];
	
	$custom_form_app = '';
	if ($sac_options['sac_form_append'] !== '') $custom_form_app = $sac_options['sac_form_append'];
	
	
	if (($registered_only && current_user_can('read')) || (!$registered_only)) {
		
		$current_user = wp_get_current_user();
		$logged_username = sanitize_text_field($current_user->display_name);
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ". $table_prefix ."ajax_chat ORDER BY id ". $display_order ." LIMIT %d", $sac_number_of_comments));
		
		echo '<div id="simple-ajax-chat">';
		
		echo $custom_chat_pre;
		
		echo '<div id="sac-content"></div>';
		echo '<div id="sac-output">';
			
		$sac_first_time = true;
			
		if ($results) {
			foreach($results as $r) { 
				
				$chat_text = sanitize_text_field($r->text);
				$chat_time = sanitize_text_field($r->time);
				$chat_id   = sanitize_text_field($r->id);
				$chat_url  = sanitize_text_field($r->url);
				$chat_name = sanitize_text_field($r->name);
				
				$name_class = preg_replace("/[\s]+/", "-", $chat_name);
				$chat_text  = preg_replace("`(http|ftp)+(s)?:(//)((\w|\.|\-|_)+)(/)?(\S+)?`i", "<a href=\"\\0\" target=\"_blank\" title=\"Open link in new tab\">\\0\</a>", $chat_text);
				
				if ($sac_first_time == true) $lastID = $chat_id;
				
				if ($use_url) $url = (empty($chat_url) && $chat_url = "http://") ? $chat_name : '<a href="'. $chat_url .'" target="_blank">'. $chat_name .'</a>';
				else $url = $chat_name;
					
				if ($sac_first_time == true) : ?>
					
					<div id="sac-latest-message">
						<span><?php _e('Latest Message:', 'sac'); ?></span> <em id="responseTime"><?php echo sac_time_since($chat_time) .' '. __('ago', 'sac'); ?></em>
					</div>
					<ul id="sac-messages">
					
				<?php endif; ?>
						
					<li class="sac-chat-message sac-static sac-user-<?php echo $name_class; ?>">
						<span title="Posted <?php echo sac_time_since($chat_time) .' '. __('ago', 'sac'); ?>"><?php echo $url; ?> : </span> <?php echo convert_smilies(' '. $chat_text); ?>
					</li> 
						
				<?php $sac_first_time = false;
			}
		} else {
			echo '<ul id="sac-messages">';
			echo '<li>You need <strong>at least one entry</strong> in the chat forum!</li>';
		} 
		echo '</ul>';
		echo '</div>';
		
		echo $custom_chat_app;
		echo $custom_form_pre; ?>
		
		
		<div id="sac-panel">
			<form id="sac-form" method="post" action="<?php echo plugins_url(); ?>/<?php echo $sac_path; ?>">
				
				<?php if ($use_username && !empty($logged_username)) : ?>
				
				<fieldset id="sac-user-info">
					<label for="sac_name"><?php _e('Name', 'sac'); ?>: <span><?php echo $logged_username; ?></span></label>
					<input type="hidden" name="sac_name" id="sac_name" value="<?php echo $logged_username; ?>" />
				</fieldset>
				
				<?php else : 
					$cookie_username = '';
					if (isset($_COOKIE['sacUserName']) && !empty($_COOKIE['sacUserName'])) $cookie_username = sanitize_text_field($_COOKIE['sacUserName']); 
				?>
				
				<fieldset id="sac-user-info">
					<label for="sac_name"><?php _e('Name', 'sac'); ?>: </label>
					<input type="text" name="sac_name" id="sac_name" value="<?php echo $cookie_username; ?>" placeholder="<?php _e('Name', 'sac'); ?>" />
				</fieldset>
				
				<?php endif;
				
				$cookie_url = 'http://';
				if (isset($_COOKIE['sacUrl']) && !empty($_COOKIE['sacUrl'])) $cookie_url = sanitize_text_field($_COOKIE['sacUrl']); 
				
				if (!$use_url) echo '<div style="display:none;">'; ?>
				
				<fieldset id="sac-user-url">
					<label for="sac_url"><?php _e('URL', 'sac'); ?>:</label>
					<input type="text" name="sac_url" id="sac_url" value="<?php echo $cookie_url; ?>" placeholder="<?php _e('URL', 'sac'); ?>" />
				</fieldset>
				
				<?php if (!$use_url) echo '</div>'; ?>
				
				<fieldset id="sac-user-chat">
					<label for="sac_chat"><?php _e('Message', 'sac') ?>: </label>
					<?php if ($use_textarea) : ?>
					
					<textarea name="sac_chat" id="sac_chat" rows="3" onkeypress="return pressedEnter(this,event);" placeholder="<?php _e('Message', 'sac') ?>"></textarea>
					<?php else : ?>
					
					<input type="text" name="sac_chat" id="sac_chat" />
					<?php endif; ?>
					
				</fieldset>
				
				<fieldset id="sac_verify" style="display:none;height:0;width:0;">
					<label for="sac_verify"><?php _e('Human verification: leave this field empty.', 'sac'); ?></label>
					<input name="sac_verify" type="text" size="33" maxlength="99" value="" />
				</fieldset>
				
				<div id="sac-user-submit">
					<input type="submit" id="submitchat" name="submit" class="submit" value="<?php _e('Say it', 'sac'); ?>" />
					<input type="hidden" id="sac_lastID" value="<?php echo $lastID + 1; ?>" name="sac_lastID" />
					<input type="hidden" name="sac_no_js" value="true" />
					<input type="hidden" name="PHPSESSID" value="<?php echo session_id(); ?>" />
				</div>
			</form>
			<script>(function(){var e = document.getElementById("sac_verify");e.parentNode.removeChild(e);})();</script>
			<!-- Simple Ajax Chat @ http://perishablepress.com/simple-ajax-chat/ -->
		</div>
		
		
		<?php echo $custom_form_app;
		
		if ($play_sound == true) : 
			$res_path = plugins_url() . '/simple-ajax-chat/resources/'; ?>
			
			<audio id="TheBox">
				<source src="<?php echo $res_path; ?>msg.mp3"></source>
				<source src="<?php echo $res_path; ?>msg.ogg"></source>
				<!-- your browser does not support audio -->
			</audio>
			
		<?php endif;
	
	
	} else { // login required

		echo $custom_form_pre; ?>

		<div id="sac-panel" class="sac-reg-req">
			<p><?php _e('You must be a registered user to participate in this chat.', 'sac'); ?></p>
			<!--p>Please <a href="<?php wp_login_url(get_permalink()); ?>">Log in</a> to chat.</p-->
		</div>

		<?php echo $custom_form_app;
	}
	echo '</div>';
	echo $custom_styles;
}

