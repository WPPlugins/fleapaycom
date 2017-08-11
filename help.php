<?php
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) || !class_exists(FLEAPAY_APP_NAME) ) {
    echo "Hey dude!  I'm just a plugin, not much I can do when called directly.";
    exit();
}

?>

<h1>Help</h1>
<p>This section is a work in progress, but don’t worry — we're here to <a href="<?=FLEAPAY_APP_HELP?>">help</a> you.</p>

<p>If you can't find what you're looking for, just <a href="<?=FLEAPAY_APP_HELP?>/contact">ask us</a> for help or feel free to chat us</p>