jQuery(document).ready(function($) {

    var loadJS = {

        init : function() {
            console.log("dsf");
          //  this.users_search_page();
            this.iframe_adjust();
            this.hide_menus();
        },

        users_search_page: function () {

            $(".search-box").parent("form").attr("action", "users.php?page=users-custom");
        },

        iframe_adjust: function () {
            $(".pay_frame").height($("#wpbody").height());
            console.log($( ".pay_frame" ).contents());

            var $iframe = $("#pay_frame");

            $iframe.ready(function(){
                console.log($iframe);
                $iframe.contents().find( "body" ).css( "display", "none" );
            });

        },

        hide_menus: function () {

            if (mu_custom.is_main_site) return;

            $("#toplevel_page_pmpro-membershiplevels").css("display", "none");

        }



    };


    loadJS.init();



});
