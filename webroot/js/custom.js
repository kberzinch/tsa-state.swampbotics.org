jQuery(function($) {
  $(document).ready(function() {

    function update() {
      $.get('data.php', function(data) {

        $(data).each(function(f) {
          var results = f;
          data[f]['total'] = data[f]['program_score'] + data[f]['driver_score'];
        });

        $('#append').empty();
        $(data).each(function(f) {
          var Html =
            '<tr>' +
            '<th scope="row">' + parseFloat(f + 1) + '</th>' +
            '<td>' + data[f]['vin'] + '</td>' +
            '<td>' + data[f]['program_score'] + '</td>' +
            '<td>' + data[f]['driver_score'] + '</td>' +
            '<td>' + data[f]['total'] + '</td>' +
            '</tr>';

          $('#append').append(Html);
        });

      });
      setTimeout(update, 5000);
    }
    update();

  });
});
