var core_mailchimp = function(){

    var self = this;

    this.init = function() {

        this.bind_events();

    }

    this.bind_events = function() {

        jQuery('.mc_embed_signup').on(
            'submit',
            'form',
            function ( event ) {
                event.preventDefault();
                self.submit_form( jQuery( this ) );
            }
        )

    } // End bind_events


    this.submit_form = function( form ) {

        var post_url = form.attr('action');

        var data = form.serialize();

        jQuery.post(
            post_url,
            data,
            function( response ) {
                
                form.find('fieldset').html('<span class="mc-response">Request Submitted!</span>');
            },
            'json'
        );

    }

    this.init();

}

var core_modules_admin = function() {

    this.mailchimp = new core_mailchimp();

    this.init = function(){

    }

    this.init();

}

var core_modules = new core_modules_admin();