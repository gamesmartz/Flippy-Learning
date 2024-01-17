$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE
//// function to remove from que, which is done by updating the que json string in the DB and showing 'Add to que' when compelted
    function remove_que(object, test_id) {
        // console.log(test_id);
        let type = "que";
        $.ajax({
            url: "ajax/async-tests/delete",
            dataType: "json",
            type: "POST",
            data: {type: type, test_id: test_id},
            error: function(xhr) {
                // remove to see any email sending errors
                // console.log(xhr.responseText);
            },
            success: function (data) {

                if (data.result == "success") {
                   // console.log(data.result);
                    $(object).parent(".add-remove-queue-button").html('<a role="button" class="f-size-13 text-decoration-none text-dark" onclick="add_que(this,' + test_id + ')">Add to Game Queue</a>');
                } else if (data.result == "expire") {
                    $('.expire-modal-wrapper').bPopup();
                } else {
                    // console.log(data.result);
                }
            }
        });
    }

///////////////////ADD TO QUE///////////////////ADD TO QUE///////////////////ADD TO QUE///////////////////ADD TO QUE///////////////////ADD TO QUE///////////////////ADD TO QUE
//// function to remove from que, which is done by updating the que json string in the DB and showing 'In Queue' when compelted
    function add_que(object, test_id) {
        //console.log(test_id);

        let type = "que";
        $.ajax({
            url: "ajax/async-tests/add",
            dataType: "json",
            type: "POST",
            data: {type: type, test_id: test_id},
            error: function(xhr) {
                // remove to see any email sending errors
                // console.log(xhr.responseText);
            },
            success: function (data) {

                if (data.result == "success") {
                    // console.log(data.result);
                    $(object).parent(".add-remove-queue-button").html('<a role="button" class="f-size-13 text-decoration-none" style="color: #ffc800;" onclick="remove_que(this,' + test_id + ')">Remove from Game Queue</a>');
                } else if (data.result == "expire") {
                    // console.log(data.result);
                    $('.expire-modal-wrapper').bPopup();
                } else {
                    // console.log(data.result);
                }
            }
        });
}


// Scroll to Top Button on Progress

$(window).scroll(function() {
    if ($(this).scrollTop() >= 50) {        // If page is scrolled more than 50px
        $('.return-to-top').fadeIn(200);    // Fade in the arrow
    } else {
        $('.return-to-top').fadeOut(200);   // Else fade out the arrow
    }
});

$('.return-to-top').click(function() {      // When arrow is clicked
    $('body,html').animate({
        scrollTop : 0                       // Scroll to top of body
    }, 10);
});