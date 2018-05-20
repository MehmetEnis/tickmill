( function( $ ) {

  $( document ).ready( function(e) {

    $( '#request_download ' ).on( 'submit', function( event ) {
      // Cache form instance
      var form = $(this);
      // Stop the normal action event triggering 
      event.preventDefault();
      // Disable the button so that it cannot be constantly clicked on, to prevent multiple request and therefore multiple counter increments
      $(form).find('button#download_button').prop('disabled', true).html('Downloading.....');

      // Serialise post data
      $data = form.serialize();

      // AJAX call to the function which will check, validate and return download url
      $.post(settings.ajaxurl, $data, function (response,$form) {
          if (response.success) {
            window.location.href = response.data.file_url;
          } else {
            // Throw a new exception
            // This should be handled better but this is just to drive point home
            alert('There has been an error. Please try again later');
            $(form).find('button#download_button').prop('disabled', false).html('Try Again');

          }
        }, 'json');

    } );

  } );

} )( jQuery );