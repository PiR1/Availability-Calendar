$("form").on("submit", function (event) {
    // Stop form from submitting normally
    event.preventDefault();
    const btn = $(this).find(".btn");
    buttonLoad(btn);

    $.ajax({
        method: "POST",
        url: url_ajax_event+$("form").attr('action'),
        data: serialize_form(this),
        contentType: 'application/json',
        dataType:'json'
    }).done(function (data) {
        window.location.replace("index.php");
    })
        .fail(function (jqXHR) {
            alert(jqXHR.responseJSON["message"]);
            console.error(jqXHR.responseText);
            buttonLoad(btn);
    });
});

$("#logout").on("click", function () {
    $.ajax({
        method: "POST",
        url: url_ajax_event+"php/user/logout",
        contentType: "application/json"
    }).done(function (data) {
        window.location.replace("login.php");
    }).fail(function (jqXHR) {
            console.error(jqXHR.responseText);
        });
})

const serialize_form = form => JSON.stringify(
    Array.from(new FormData(form).entries())
        .reduce((m, [ key, value ]) => Object.assign(m, { [key]: value }), {})
);

function buttonLoad(btn){
    const loadingText = '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>';
    if (btn.html() !== loadingText) {
        btn.data('original-text', btn.html());
        btn.html(loadingText);
        btn.attr("disabled", true);
    }else{
        btn.html(btn.data('original-text'));
        btn.removeAttr("disabled");
    }
}