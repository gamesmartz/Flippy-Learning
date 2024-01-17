$(document).ready(function() {

    let answer_shown_flag = '';


    // run on page load, set current answer_shown_flag state

    if ( !$( ".chapter-flip-card-inner" ).hasClass( "chapter-transform-rotate" ) ) {

        answer_shown_flag = 'true';

        //$( ".definitions-flip-flash-btn-all" ).text( 'Turn Off Answers' );

        //console.log( 'answer_shown_flag: ' + answer_shown_flag );

        //answer is currently not shown on page load
    } else if ( $( ".chapter-flip-card-inner" ).hasClass( "chapter-transform-rotate" ) ) {

        answer_shown_flag = 'false';

        //$( ".definitions-flip-flash-btn-all" ).text( 'Turn On Answers' );

        //console.log( 'answer_shown_flag: ' + answer_shown_flag );

    }



    // on button click, rotate card, update flag state
    $('.definitions-flip-flash-btn').click(function () {

        if ( answer_shown_flag == 'true' ) {

            // flips card
            $(".chapter-flip-card-inner").addClass('chapter-transform-rotate');

            // changes flag
            answer_shown_flag = 'false';

            gtag_report_conversion();
            //console.log('flip-card gtag_report_conversion');

        } else if ( answer_shown_flag == 'false' ) {

            // flips card
            $(".chapter-flip-card-inner").removeClass('chapter-transform-rotate');

            // changes flag
            answer_shown_flag = 'true';

        }
        
        // change background color
        //$(this).toggleClass('hide-answer-txt');

        //console.log( 'answer_shown_flag: ' + answer_shown_flag );

    });


    $(".copy-link-to-clipboard").click( function () {

        // show hide button itself
        $(".copy-link-to-clipboard").removeClass("show").addClass("hide");
        $(".copied-link-to-clipboard").removeClass("hide").addClass("show");

        // Async copy to clipboard
        // supported here: https://caniuse.com/#feat=clipboard
        // canonicalURL defined in header
        navigator.clipboard.writeText('https://gamesmartz.com/' + png_800_file_path).then(function() {
              // console.log ('Async: Copying ' + 'https://gamesmartz.com/' + png_800_file_path + ' to clipboard was successful');
            },
            function(err) {
               // console.error('Async: Could not copy new_URL: ', err);
            });

    });


    $(".copy-link-to-clipboard-no-answer").click( function () {

        // show hide button itself
        $(".copy-link-to-clipboard-no-answer").removeClass("show").addClass("hide");
        $(".copied-link-to-clipboard-no-answer").removeClass("hide").addClass("show");

        // Async copy to clipboard
        // supported here: https://caniuse.com/#feat=clipboard
        // canonicalURL defined in header
        navigator.clipboard.writeText(new_URL_used_without_answer).then(function() {
                // console.log ('Async: Copying ' + new_URL + ' to clipboard was successful');
            },
            function(err) {
                // console.error('Async: Could not copy new_URL: ', err);
            });

    });



    if ( !(typeof correct_note_audio === 'undefined') ) {
        // set one Audio instance per page
        let keyWordAudio = new Audio(correct_note_audio);

        // play on click
        $('#play_audio_id').click(function () {
            play_audio(keyWordAudio);
        });
    }

    // on save state to session button
    // $('.definitions-flip-flash-btn-all').click(function () {
    //
    //
    //     if ( answer_shown_flag == 'true' ) {
    //
    //         answer_shown_flag = 'false'
    //
    //         // flips card
    //         $(".chapter-flip-card-inner").toggleClass('chapter-transform-rotate');
    //
    //         saveAnswerShownToSession(answer_shown_flag);
    //
    //
    //     } else if ( answer_shown_flag == 'false' ) {
    //
    //         answer_shown_flag = 'true'
    //
    //         // flips card
    //         $(".chapter-flip-card-inner").toggleClass('chapter-transform-rotate');
    //
    //         saveAnswerShownToSession(answer_shown_flag);
    //     }
    //
    // });


});


function play_audio(keyWordAudio) {
    if (keyWordAudio) {
        keyWordAudio.play();
    }
}



// function saveAnswerShownToSession ( answer_shown_flag ) {
//
//     $.ajax({
//         url: "ajax/async-definitions.php",
//         dataType: "json",
//         type: "POST",
//         data: { answer_shown_flag: answer_shown_flag },
//         error: function (xhr) {
//             ////console.log(xhr.responseText);
//         },
//         success: function (data) {
//
//
//             if ( data.answer_shown_flag ) {
//
//
//                 if ( data.answer_shown_flag == 'true' ) {
//
//                     $( ".definitions-flip-flash-btn-all" ).text( 'Turn Off Answers' );
//
//                     ////console.log( 'answer_shown_flag: ' + data.answer_shown_flag);
//
//
//                 } else if ( data.answer_shown_flag == 'false' ) {
//
//
//                     $( ".definitions-flip-flash-btn-all" ).text( 'Turn On Answers' );
//
//                     ////console.log( 'answer_shown_flag: ' + data.answer_shown_flag);
//
//                 }
//
//             }
//         }
//
//     });
//
// }


