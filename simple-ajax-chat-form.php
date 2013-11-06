<?php // Simple Ajax Chat > Chat Form

if (!function_exists('add_action')) die();

session_start();
//$_SESSION['user_token'] = uniqid();

// output form markup
function simple_ajax_chat() {
	global $wpdb, $table_prefix, $user_level, $user_nickname, $user_ID, $sac_path, $sac_user_url, $sac_number_of_comments, $sac_options; 

	$use_url         = $sac_options['sac_use_url'];
	$use_textarea    = $sac_options['sac_use_textarea'];
	$registered_only = $sac_options['sac_registered_only']; 
	$enable_styles   = $sac_options['sac_enable_style'];
	$play_sound      = $sac_options['sac_play_sound'];

	if ($enable_styles) {
		$custom_styles = '<style type="text/css">' . $sac_options['sac_custom_styles'] . '</style>';
	}
	$custom_chat_pre = '';
	if ($sac_options['sac_content_chat'] !== '') {
		$custom_chat_pre = $sac_options['sac_content_chat'];
	}
	$custom_form_pre = '';
	if ($sac_options['sac_content_form'] !== '') {
		$custom_form_pre = $sac_options['sac_content_form'];
	}
	$custom_chat_app = '';
	if ($sac_options['sac_chat_append'] !== '') {
		$custom_chat_app = $sac_options['sac_chat_append'];
	}
	$custom_form_app = '';
	if ($sac_options['sac_form_append'] !== '') {
		$custom_form_app = $sac_options['sac_form_append'];
	}
	if (($registered_only && current_user_can('read')) || (!$registered_only)) { ?>

	<div id="simple-ajax-chat">
		<?php echo $custom_chat_pre; ?>

		<div id="sac-content"></div>
		<div id="sac-output">

			<?php $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_prefix . "ajax_chat ORDER BY id DESC LIMIT %d", $sac_number_of_comments));
			

			// only add last message <div> if looping for first time
			$sac_first_time = true;
			if ($results) {
				foreach($results as $r) { 
					$r->text = preg_replace("`(http|ftp)+(s)?:(//)((\w|\.|\-|_)+)(/)?(\S+)?`i", "<a href=\"\\0\" target=\"_blank\" title=\"Open link in new tab\">\\0\</a>", $r->text);
					if ($sac_first_time == true) { ?>

						<div id="sac-latest-message"><span><?php _e('Latest Message:', 'sac'); ?></span> <em id="responseTime"><?php echo sac_time_since($r->time) . ' ' . __('ago', 'sac'); ?></em></div>
						<ul id="sac-messages">

					<?php }

					if ($sac_first_time == true) $lastID = $r->id;
					if ($use_url) {
						$url = (empty($r->url) && $r->url = "http://") ? $r->name : '<a href="' . $r->url . '" target="_blank">' . $r->name . '</a>';
					} else {
						$url = $r->name;
					} ?>
							<li><span title="Posted <?php echo sac_time_since($r->time) . ' ' . __('ago', 'sac'); ?>"><?php echo stripslashes($url); ?> : </span> <?php echo convert_smilies(" " . stripslashes($r->text)); ?></li> 

					<?php $sac_first_time = false;
				}
			} else {
				echo '<ul id="sac-messages">';
				echo '<li>You need <strong>at least one entry</strong> in the chat forum!</li>';
			} ?>

			</ul>
		</div>

		<?php echo $custom_chat_app; ?>

		<?php get_currentuserinfo();
			echo $custom_form_pre; ?>

		<div id="sac-panel">
			<form id="sac-form" method="post" action="<?php echo site_url(); ?>/wp-content/plugins/<?php echo $sac_path; ?>">

				<?php if (!empty($user_nickname)) { ?>

				<fieldset id="sac-user-info">
					<label for="sac_name"><?php _e('Name', 'sac'); ?>: <span><?php echo $user_nickname ?></span></label>
					<input type="hidden" name="sac_name" id="sac_name" value="<?php echo $user_nickname; ?>" />
					<input type="hidden" name="sac_url" id="sac_url" value="<?php if($use_url) { echo $user_url; } ?>" />
				</fieldset>

				<?php } else { ?>

				<fieldset id="sac-user-info">
					<label for="sac_name"><?php _e('Name', 'sac'); ?>:</label>
					<input type="text" name="sac_name" id="sac_name" value="<?php if ($_COOKIE['sacUserName']) { echo htmlentities($_COOKIE['sacUserName']); } ?>" placeholder="Name" />
				</fieldset>

				<?php } if (!$use_url) { echo '<div style="display:none;">'; } ?>

				<fieldset id="sac-user-url">
					<label for="sac_url"><?php _e('URL', 'sac'); ?>:</label>
					<input type="text" name="sac_url" id="sac_url" value="<?php if ($_COOKIE['sacUrl']) { echo htmlentities($_COOKIE['sacUrl']); } else { echo 'http://'; } ?>" placeholder="URL" />
				</fieldset>
				<?php if (!$use_url) { echo '</div>'; } ?>

				<fieldset id="sac-user-chat">
					<label for="sac_chat"><?php _e('Message', 'sac') ?>:</label>

				<?php if ($use_textarea) { ?>

					<textarea name="sac_chat" id="sac_chat" rows="3" onkeypress="return pressedEnter(this,event);" placeholder="Message"></textarea>

				<?php } else { ?>

					<input type="text" name="sac_chat" id="sac_chat" />

				<?php } ?>

				</fieldset>
				<fieldset id="sac_verify" style="display:none;height:0;width:0;">
					<label for="sac_verify">Human verification: leave this field empty.</label>
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
		<?php echo $custom_form_app; ?>

		<?php if ($play_sound == true) { 
			$res_path = site_url() . '/wp-content/plugins/simple-ajax-chat/resources/'; ?>
			<audio id="TheBox">
				<source src="<?php echo $res_path; ?>msg.mp3"></source>
				<source src="<?php echo $res_path; ?>msg.ogg"></source>
				<!-- your browser does not support audio -->
			</audio>
		<?php } ?>

	<?php } else { ?>

		<?php echo $custom_form_pre; ?>

		<div id="sac-panel" class="sac-reg-req">
			<p>You must be a registered user to participate in this chat.</p>
			<!--p>Please <a href="<?php wp_login_url(get_permalink()); ?>">Log in</a> to chat.</p-->
		</div>

		<?php echo $custom_form_app; ?>

	<?php } ?>

	</div>
	<?php echo $custom_styles; ?>

<?php }
