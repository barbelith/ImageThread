$(document).ready(function(){
    $('.ajax-modal-link').click(function (event) {
        event.preventDefault();
        $.get(this.href, function (html) {
            $(html).appendTo('body').modal();
        });
    });
});