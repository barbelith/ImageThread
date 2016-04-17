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
                url: url, success: function (data) {
                    if ('ok' === data.status) {
                        item.html(data.content);
                    }

                }, dataType: "json"
            });
        }, 15000);
    });
});