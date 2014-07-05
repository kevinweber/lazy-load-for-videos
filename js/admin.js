(function( incom, $, undefined ) {

  $(document).ready(function() {
  	init();
  });

  var init = function() {
    $( "#tabs" ).tabs();
    addColourPicker();
  };

  var addColourPicker = function() {
    $('#llv_picker_player_colour').farbtastic('#llv_picker_input_player_colour');
// Picker No 2:    $('#incom_picker_bgcolor').farbtastic('#incom_picker_input_bgcolor');
  };

}( window.incom = window.incom || {}, jQuery ));
