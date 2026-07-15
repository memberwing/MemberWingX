// Assumes that jquery.js is loaded

this.text_tooltip = function()
    {
    var xOffset = -22;
    var yOffset = 12;
    jQuery("div.product-name,div.product-description").hover(function(e)
        {
        var prod_description = jQuery(this).parent(".product-wrapper").attr('description');
        if (!prod_description)
            return;

        jQuery("body").append('<div id="text_tooltip">'+ prod_description + '</div>');

        jQuery("#text_tooltip")
            .css("top",(e.pageY - xOffset) + "px")
            .css("left",(e.pageX + yOffset) + "px")
            .fadeIn("fast");
        },
    function(){
        jQuery("#text_tooltip").remove();
        });

   jQuery("div.product-name,div.product-description").mousemove(function(e){
        jQuery("#text_tooltip")
        .css("top",(e.pageY - xOffset) + "px")
        .css("left",(e.pageX + yOffset) + "px");
        });
    };

this.image_tooltip = function()
    {
    var xOffset = -22;
    var yOffset = 12;
    jQuery(".product-images img").hover(function(e)
        {
        var big_img_url = jQuery(this).attr('srcbig');
        if (!big_img_url)
            return;

        jQuery("body").append('<div id="image_tooltip"><img src="'+ big_img_url + '" /></div>');

        jQuery("#image_tooltip")
            .css("top",(e.pageY - xOffset) + "px")
            .css("left",(e.pageX + yOffset) + "px")
            .fadeIn("fast");
        },
    function(){
        jQuery("#image_tooltip").remove();
        });

   jQuery(".product-images img").mousemove(function(e){
        jQuery("#image_tooltip")
        .css("top",(e.pageY - xOffset) + "px")
        .css("left",(e.pageX + yOffset) + "px");
        });
    };

// starting the script on page load
jQuery(document).ready(function()
    {
    text_tooltip();
    image_tooltip();
    });
