<iframe class="pay_frame" id="pay_frame" width="100%" src="<?php _e($options['checkout_page_link']."?level=".$options['paid_level_id']."&quantity={$_POST['quantity']}"); ?>" frameborder="0"></iframe>
<script>

    jQuery(document).ready(function($) {

        $(".first").css("display", "none");

    })
</script>