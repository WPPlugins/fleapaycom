<?php
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) || !class_exists(FLEAPAY_APP_NAME) ) {
	echo "Hey dude!  I'm just a plugin, not much I can do when called directly.";
	exit();
}
?>

<div class="wrap">
	<h2><?php _e(FLEAPAY_APP_NAME . ' Configuration'); ?></h2>
	<div class="narrow">
		<form action="" method="post" id="<?=Fleapay::app_slug()?>-api" style="margin: auto; width: 400px; ">
			<p>
				<a href="<?=FLEAPAY_APP_HOME?>"><?=FLEAPAY_APP_NAME?></a> will make integration of e-commerce much quicker and easier for users, graphic designers and developers.
				Our unique implementation is geared toward reducing development and customization time.
				The aim is to provide affordable e-commerce for businesses who want quick access to their cash.
			</p>
			<p>
				If you don't have an API key yet, you can get one at <a href="<?=FLEAPAY_APP_HOME?>"><?=FLEAPAY_APP_NAME?></a>.
			</p>
			<h3><label for="key"><?_e(FLEAPAY_APP_NAME . ' API Key')?></label></h3>
			<?
				if(isset($fleapay_key)) {
					echo '<p>Active Key: ' . $fleapay_key . '</p>';
				}
			?>

			<p id="api">Please enter an API key. (<a href="<?=FLEAPAY_APP_HOME?>/company/account">Get your key.</a>)</p>
			<p>
				<input id="<?=$key_name?>" name="<?=$key_name?>" type="text" size="30" maxlength="32" value=""> (<a href="<?=FLEAPAY_APP_HELP?>">What is this?</a>)</p>
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="1079367728">
				<input type="hidden" name="_wp_http_referer" value="<?=FLEAPAY_SETTING_URL?>">
			</p>
			<p class="submit"><input type="submit" name="submit" value="Save API Key"></p>
		</form>
		<form action="" method="post" id="<?=Fleapay::app_slug()?>-button" style="margin: auto; width: 400px; ">
			<p id="api">Please enter default text for your cart buttons.</p>
			<p>Your button will appear like this:
				<?=Fleapay::app_cart_html_button()?>
			</p>
			<br />
				<input id="<?=$btn_name?>" name="<?=$btn_name?>" type="text" size="30" maxlength="32" value=""><br />
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="1079367728">
				<input type="hidden" name="_wp_http_referer" value="<?=FLEAPAY_SETTING_URL?>">
			<p class="submit"><input type="submit" name="submit" value="Save Button Text"></p>
		</form>
	</div>
</div>