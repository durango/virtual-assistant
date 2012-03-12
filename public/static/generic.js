$(document).ready(function(){
  var name = $('#name');

  if (name.length > 0){
    $('#whoareyou').submit(function(e){
      if ($.trim(name.val()).length < 1) {
        e.preventDefault();
        $('div.control-group').addClass('error');
        $('<div class="alert alert-error">You must enter in your name.</div>').prependTo('#container');
      }
    });
  }

  var msg = $('#msg');

  if (msg.length > 0){
    $('#send').submit(function(e){
      if ($.trim(msg.val()).length < 1) {
        e.preventDefault();
        $('div.control-group').addClass('error');
        $('<div class="alert alert-error">You must enter in a message</div>').prependTo('#container');
      }
    });
  }
});