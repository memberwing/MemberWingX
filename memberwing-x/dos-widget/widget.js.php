<?php
$widget_prefix  = @$_REQUEST['prefix']?$_REQUEST['prefix']:substr(md5(microtime()), 0, 4);
$widget_api_url = @$_REQUEST['api_url']?$_REQUEST['api_url']:"???";
?>
// Widget loads jquery by itself (if it is not already loaded) and then issues ajax-call for MWX-DOS API to pull the rest of widget's js/css/html code
(function()
   {
   // Load jQuery if not present
   if (window.jQuery === undefined)
      {
      var script_tag = document.createElement('script');
      script_tag.setAttribute("type",  "text/javascript");
      script_tag.setAttribute("src",   "https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");
      script_tag.onload = scriptLoadHandler;
      script_tag.onreadystatechange = function ()
         { // Same thing but for IE
         if (this.readyState == 'complete' || this.readyState == 'loaded')
            {
            scriptLoadHandler();
            }
         };

      // Try to find the head, otherwise default to the documentElement
      (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
      }
   else
      {
      // The jQuery version on the window is the one we want to use
      jQuery = window.jQuery;
      main();
      }

   // Called once jQuery has loaded
   function scriptLoadHandler()
      {
      // Restore $ and window.jQuery to their previous values and store the
      // new jQuery in our local jQuery variable
      jQuery = window.jQuery.noConflict(true);

      // Call our main function
      main();
      }

   // main function
   function main()
      {
      jQuery(document).ready(function($)
         {
         var widget_params  =  <?php echo "mwx_widget_$widget_prefix"; ?>;

         var api_url        =  widget_params.api_url + "?format=json";
         api_url            += "&use_template="    +  widget_params.t.use_template;
         api_url            += "&regex_include="   +  widget_params.t.regex_include;
         api_url            += "&regex_exclude="   +  widget_params.t.regex_exclude;
         api_url            += "&max_items="       +  widget_params.t.max_items;
         api_url            += "&sort="            +  widget_params.t.sort;

         $.ajax({
            url: api_url + "&jsoncallback=?",
            dataType: 'json',
            success:  function (data)
               {
               $(".mwx_dos_widget_<?php echo $widget_prefix; ?>").html(data['html']);
               },
            error:    function (xml_http_req, textStatus, errorThrown)
               {
               $(".mwx_dos_widget_<?php echo $widget_prefix; ?>").html(textStatus);
               }
            });
         });
      }
   })(); // We call our anonymous function immediately
