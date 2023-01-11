jQuery('document').ready( function(){
    jQuery('.user-email-input input').on('focusout', function(){
        var user_email = jQuery(this).val();
        console.log(jQuery(this).val());
        jQuery('.loading-area').show();
        jQuery.ajax({
            url : admin_script.ajax_url,
            type: 'POST',
            dataType: 'text',
            data: { 
                action: 'check_user_exists',
                email: user_email,
            },
            // then: function (t,n,r){
            //     console.log(t);
            //     console.log(n);
            //     console.log(r);

            // },
            success: function(return_data) {
                var check_element = jQuery('.check-email-exists input');
                console.log(return_data);
                check_element.val(return_data);  
                check_element.trigger('keyup'); 
                jQuery('.loading-area').hide();
            },
            error: function(returnval){
                jQuery('.loading-area').hide();
                console.log("Something went wrong");
                console.log(returnval);
            },
            
        }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            // Request failed. Show error message to user. 
            // errorThrown has error message, or "timeout" in case of timeout.
        })
    })
    
});