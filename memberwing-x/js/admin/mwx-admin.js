jQuery(document).ready(function()
    {
    jQuery("#dirs_listing_table tr:first-child").show();

    // Clicking on arrow shows list of child files
    jQuery("div.arrow, div.arrow2").toggle (
        function()
            {
            jQuery("tr."+jQuery(this).attr("id")).show();
            jQuery(this).toggleClass("up");
            },
        function()
            {
            jQuery("tr."+jQuery(this).attr("id")).hide();
            jQuery(this).toggleClass("up");
            }
        );
    });
