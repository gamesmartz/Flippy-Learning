$(document).ready(function () {

    function track_card_flip() {

        // For tracking flashcard flips on Mixpanel.
        
        //console.log(source);
        // google tag
        gtag('event', 'Flashcard Flipped', {
            'flashcard_flipped': 'flip'
        });
        // mixpanel tag
        mixpanel.track('Flashcard Flipped', {
            'flashcard_flipped': 'flip',
        });
        //console.log('card_flip');
    }


    // Flip the flashcard and show the image on the back. Do a css transform, then do a hide and show. 
    // If the image has the class, chapter-transform-answer-hidden. Swap it with chapter-transform-answer-shown. Do the opposite if already shown.
    
    $(".show-answer").click(function(){
        
        if ( $(this).parent().siblings().find(".chapter-flip-card-inner").hasClass("chapter-transform-answer-hidden") ) {

            $(this).parent().siblings().find(".chapter-flip-card-inner").removeClass("chapter-transform-answer-hidden");
            $(this).parent().siblings().find(".chapter-flip-card-inner").addClass("chapter-transform-answer-shown");
        }
        else if ( $(this).parent().siblings().find(".chapter-flip-card-inner").hasClass("chapter-transform-answer-shown") ) {

            $(this).parent().siblings().find(".chapter-flip-card-inner").removeClass("chapter-transform-answer-shown");
            $(this).parent().siblings().find(".chapter-flip-card-inner").addClass("chapter-transform-answer-hidden");
        }
        track_card_flip();
    });
   

    // Top button to flip all the flashcards. If the cards have the class of showing the answer, flip it, by preforming a css transform and hiding / showing. 
    $(".hide-all-answers").click(function(){

       // After flipping, remove button and put the one to show all the answers in.
        $(this).removeClass("show").addClass("hide");
        $(".show-all-answers").removeClass("hide").addClass("show");

        // If the cards have the class of show the answer, flip it. 
        if ( $(".chapter-flip-card-inner").hasClass("chapter-transform-answer-shown") ) {
            $(".chapter-flip-card-inner").removeClass("chapter-transform-answer-shown");
            $(".chapter-flip-card-inner").addClass("chapter-transform-answer-hidden");
        }
        track_card_flip();
    });


     // Top button to flip to flip all the flashcards. If the cards have the class of hiding the answer, flip it, by preforming a css transform and hiding / showing. 
     $(".show-all-answers").click(function(){

        // After flipping, remove button and put the one to hide all the answers in.
        $(this).removeClass("show").addClass("hide");
        $(".hide-all-answers").removeClass("hide").addClass("show");

        // If the card has the class of answer showing, flip it.
        if ( $(".chapter-flip-card-inner").hasClass("chapter-transform-answer-hidden") ) {
            $(".chapter-flip-card-inner").removeClass("chapter-transform-answer-hidden");
            $(".chapter-flip-card-inner").addClass("chapter-transform-answer-shown");
        }
        track_card_flip();
    });


});