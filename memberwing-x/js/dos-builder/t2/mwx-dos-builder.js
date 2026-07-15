// Assumes that jquery.js is loaded

this.text_tooltip_t2 = function()
    {
    var xOffset = -22;
    var yOffset = 12;
    jQuery("td.product-name-td-t2,td.product-description-td-t2").hover(function(e)
        {
        var prod_description = jQuery(this).parent().parent().parent(".mwx-dos-product-t2").attr('description');
        if (!prod_description)
            {
            return;
            }

        jQuery("body").append('<div id="text_tooltip_t2">'+ prod_description + '</div>');

        jQuery("#text_tooltip_t2")
            .css("top",(e.pageY - xOffset) + "px")
            .css("left",(e.pageX + yOffset) + "px")
            .fadeIn("fast");
        },
    function(){
        jQuery("#text_tooltip_t2").remove();
        });

   jQuery("td.product-name-td-t2,td.product-description-td-t2").mousemove(function(e){
        jQuery("#text_tooltip_t2")
        .css("top",(e.pageY - xOffset) + "px")
        .css("left",(e.pageX + yOffset) + "px");
        });
    };

this.image_tooltip_t2 = function()
    {
    var xOffset = -22;
    var yOffset = 12;
    jQuery("td.product-image-td-t2 img").hover(function(e)
        {
        var big_img_url = jQuery(this).attr('srcbig');
        if (!big_img_url)
            return;

        jQuery("body").append('<div id="image_tooltip_t2"><img src="'+ big_img_url + '" /></div>');

        jQuery("#image_tooltip_t2")
            .css("top",(e.pageY - xOffset) + "px")
            .css("left",(e.pageX + yOffset) + "px")
            .fadeIn("fast");
        },
    function(){
        jQuery("#image_tooltip_t2").remove();
        });

   jQuery("td.product-image-td-t2 img").mousemove(function(e){
        jQuery("#image_tooltip_t2")
        .css("top",(e.pageY - xOffset) + "px")
        .css("left",(e.pageX + yOffset) + "px");
        });
    };

// starting the script on page load
jQuery(document).ready(function()
    {
    text_tooltip_t2();
    image_tooltip_t2();
    });
