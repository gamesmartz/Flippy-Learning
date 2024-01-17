// Used:
// any modals with embedded <iframe> we can assume need reset
// <https://stackoverflow.com/a/52315492>
$('body').on('hide.bs.modal', '.modal', function() {
    const $modal = $(this);
    // return early if there were no embedded YouTube videos
    if ($modal.find('iframe').length === 0) return;
    const html = $modal.html();
    $modal.html(html);
});

// Not Used:
// bPopup js based on class name passed in, targets pre-made modals below header
// function open_video_modal (video_name) {
//     if (video_name) {
//        // video_name is a class name
//        // console.log(video_name);
//         $(video_name).bPopup({
//             // onOpen: function() { console.log('onOpen fired'); },
//             onClose: function() {
//                 // console.log('onClose fired');
//                 // https://stackoverflow.com/questions/15164942/stop-embedded-youtube-iframe
//
//                 // make a unique class identifier to close the iframe cleanly
//                 video_name = video_name + '-iframe';
//                 //console.log(video_name);
//                 $(video_name)[0].contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*');
//             }
//         });
//     }
// }