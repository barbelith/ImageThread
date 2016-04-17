$(document).ready(function(){
    $('.ajax-modal-link').click(function (event) {
        event.preventDefault();
        $.get(this.href, function (html) {
            $(html).appendTo('body').modal();
        });
    });

    $('.updatable-item').each(function(){
        var item = $(this);
        var url = item.data('url');

        setInterval(function () {
            $.ajax({
                url: url,
                success: function (data) {
                    if ('ok' === data.status) {
                        item.html(data.content);
                    }

                },
                dataType: "json"
            });
        }, 15000);
    });

    var showMorePosts = $('#show_more_posts');
    showMorePosts.show();
    showMorePosts.click(function(e){
        e.preventDefault();

        enableInfiniteScroll();

        showMorePosts.fadeOut();
    });


});

function enableInfiniteScroll() {
    $(window).scroll(function () {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            var posts = $('ul.posts');
            
            if (!posts.data('loading')) {
                var url = $('#show_more_posts').attr('href') + '?last_item=' + posts.find('li:last-child').data('id');

                $.ajax({
                    url: url,
                    success: function(data) {
                        console.log(data);
                        posts.append(data);
                    }
                })
            }
        }
    });
}