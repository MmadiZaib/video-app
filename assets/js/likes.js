const $ = require('jquery');

$(document).ready(function() {

   $('.userLikesVideo').show();
    $('.userDoesNotLikeVideo').show();
    $('.noActionYet').show();

    $('.toggle-likes').on('click', function(e) {
        e.preventDefault();
        var $link = $(e.currentTarget);

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function (data) {
            switch (data.action) {
                case 'liked':
                    var numberOfLikesStr = $('.number-of-likes-' + data.id);
                    var numberOfLikes = parseInt(numberOfLikesStr.html().replace(/\D/g, '')) + 1;
                    numberOfLikesStr.html('(' + numberOfLikes + ')');

                    $('.likes-video-id-' + data.id).show();
                    $('.dislikes-video-id-' + data.id).hide();
                    $('.video-id-' + data.id).hide();

                    break;

                case 'disliked':
                    var numberOfDislikesStr = $('.number-of-dislikes-' + data.id);
                    var numberOfDislikes = parseInt(numberOfDislikesStr.html().replace(/\D/g, '')) + 1;
                    numberOfDislikesStr.html('(' + numberOfDislikes + ')');

                    $('.video-id-' + data.id).show();
                    $('.dislikes-video-id-' + data.id).hide();
                    $('.likes-video-id-' + data.id).hide();

                    break;

                case 'undo liked':
                    var numberOfLikesStr = $('.number-of-likes-' + data.id);
                    var numberOfLikes = parseInt(numberOfLikesStr.html().replace(/\D/g, '')) - 1;
                    numberOfLikesStr.html('(' + numberOfLikes + ')');

                    $('.video-id-' + data.id).show();
                    $('.likes-video-id-' + data.id).hide();
                    $('.dislikes-video-id-' + data.id).hide();

                    break;

                case 'undo disliked':
                    var numberOfDislikesStr = $('.number-of-dislikes-' + data.id);
                    var numberOfDislikes = parseInt(numberOfDislikesStr.html().replace(/\D/g, '')) - 1;
                    numberOfDislikesStr.html('(' + numberOfDislikes + ')');

                    $('.video-id-' + data.id).show();
                    $('.dislikes-video-id-' + data.id).hide();
                    $('.likes-video-id-' + data.id).hide();

                    break;
            }
        });

    });
});
