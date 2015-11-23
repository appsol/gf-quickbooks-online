<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/public/partials
 */
namespace AppSol\GFQuickbooksOnline\Pub;

?>
<div class="ginput_container ginput_container_text <?php echo $class; ?>">
<input type="text" id="customer_select" />
<input type="hidden" class="qb-customer-id" name="input_<?php echo $field->id; ?>" id="input_<?php echo $form_id . '_' . $field->id; ?>" value="<?php echo $value; ?>" <?php echo $tabIndex; ?> />
<script type="text/javascript"><?php echo $qbCustomers; ?></script>
</div>