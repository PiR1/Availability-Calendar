/*
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      script.js
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

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
    btn.each(function (i) {
        if (btn.eq(i).html() !== loadingText) {
            btn.eq(i).data('original-text', btn.eq(i).html());
            btn.eq(i).html(loadingText);
            btn.eq(i).attr("disabled", true);
        }else{
            btn.eq(i).html(btn.eq(i).data('original-text'));
            btn.eq(i).removeAttr("disabled");
        }
    })

}

function showAlert(type, message) {
    let delay = 500;
    if(type ==="danger"){
        delay = 3000;
    }
    const elt = $("<div class=\"alert alert-dismissible alert-" + type + "\" role=\"alert\">\n" +
        message + "\n" +
        "</div>");
    $("#alerts").append(elt);
    elt.fadeTo(delay, 500).slideUp(500, function() {
        elt.slideUp(500);
    });

}