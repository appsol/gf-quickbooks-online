<?php

/**
 * Add the field settings to the Quickbooks Customer Field
 *
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/admin/partials
 */
namespace AppSol\GFQuickbooksOnline\Admin;

?>
<script type="text/javascript">
    (function($)
        {
            $(function()
                {
                    // Copy the settings from a similar field
                    fieldSettings["qbcustomer"] = fieldSettings["text"];
                    // Bind to the load field settings event to initialise the field
                    // $(document).bind('gform_load_field_settings', function(event, field, form)
                    //     {
                            
                    //     });
                });
        })(jQuery);
</script>