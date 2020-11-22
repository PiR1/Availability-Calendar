/*
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      admin.js
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

const today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();
const months = [
    "Janvier",
    "Février",
    "Mars",
    "Avril",
    "Mai",
    "Juin",
    "Juillet",
    "Aout",
    "Septembre",
    "Octobre",
    "Novembre",
    "Decembre"
];
let selectYear;
let selectMonth;

let monthAndYear;

function next() {
    currentYear = (currentMonth === 11) ? currentYear + 1 : currentYear;
    currentMonth = (currentMonth + 1) % 12;
    showCalendar(currentMonth, currentYear);
}
function previous() {
    currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
    currentMonth = (currentMonth === 0) ? 11 : currentMonth - 1;
    showCalendar(currentMonth, currentYear);
}

function jump() {
    currentYear = parseInt(selectYear.value);
    currentMonth = parseInt(selectMonth.value);
    showCalendar(currentMonth, currentYear);
}
function icalsForm() {
    $("#icals form").on("submit", function(event){
        event.preventDefault();
        const btn = $(this).find(".btn");
        buttonLoad(btn);
        let data;
        if ($(this).find("#url").val().includes(".ics")) {
            data = {
                "id": $(this).find(".btn").attr("data-id"),
                "url": $(this).find("#url").val(),
                "type": $(this).find("#desc").val()
            };


            $.ajax({
                method: "POST",
                url: url_ajax_event + "php/ical/update",
                data: JSON.stringify(data),
                contentType: 'application/json',
                dataType: 'json'
            }).done(function (data) {
                showAlert("success", data.message);
                buttonLoad(btn);
                if (data.id) {
                    btn.attr("data-id", data.id);
                }
            }).fail(function (jqXHR) {
                alert(jqXHR.responseJSON["message"]);
                console.error(jqXHR.responseText);
                buttonLoad(btn);
            });
        } else {
            buttonLoad(btn);
            showAlert("danger", "The ical url must ends with '.ics'");
        }
    })
    $(".btn-danger").on("click", function(event){
        event.preventDefault();
        const elt = $(this);
        const btn = $(this).find(".btn");
        buttonLoad(btn);
        if($(this).attr("data-id")){
            $.ajax({
                method: "POST",
                url: url_ajax_event+"php/ical/delete",
                data: $(this).attr("data-id"),
                dataType:'json'
            }).done(function (data) {
                showAlert("success", data.message);
                buttonLoad(btn);
                elt.closest("form").remove();
            }).fail(function (jqXHR) {
                alert(jqXHR.responseJSON["message"]);
                console.error(jqXHR.responseText);
                buttonLoad(btn);
            });

        }
        else{
            buttonLoad(btn);
            elt.closest("form").remove();
        }
    })
}
function showCalendar(month, year) {
    loadEvents();

    let firstDay = (((new Date(year, month)).getDay() - 1) + 7) % 7;
    let daysInMonth = 32 - new Date(year, month, 32).getDate();

    let tbl = document.getElementById("calendar-body"); // body of the calendar

    // clearing all previous cells
    tbl.innerHTML = "";

    // filing data about month and in the page via DOM.
    monthAndYear.innerHTML = months[month] + " " + year;
    // selectYear.value = year;
    // selectMonth.value = month;

    // creating all cells
    let date = 1;
    for (let i = 0; i < 6; i++) {
        if (date > daysInMonth){
            break;
        }
        // creates a table row
        let row = document.createElement("div");
        row.classList.add("d-flex", "w-100");

        //creating individual cells, filing them up with data.
        for (let j = 0; j < 7; j++) {
            let cell = document.createElement("div");
                cell.classList.add("tds","border","d-flex", "rounded-0","flex-fill","w-100","justify-content-center","pt-3","pb-3");
            if (i === 0 && j < firstDay || date>daysInMonth) {
                let cellText = document.createTextNode("");
                // cell.classList.add("border", "border-white");
                cell.appendChild(cellText);
                row.appendChild(cell);
            }

            else {
                let cellText = document.createTextNode(date);
                cell.classList.add("btn", "p-0", "border");
                cell.setAttribute("data-date", year+"-"+("0" + (month + 1)).slice(-2)+"-"+ ("0" + date).slice(-2));
                if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                    cell.classList.add("bg-info");
                } // color today's date
                if (eventOnDay(new Date(year, month, date, 0, UTCOffset, 0), jsonDates)) {
                    cell.classList.add("bg-danger");
                }
                cell.appendChild(cellText);
                row.appendChild(cell);
                date++;
            }
        }

        tbl.appendChild(row); // appending each row into calendar body.
    }
    $('.tds').on('click', function () {
        if($(this).attr("data-date")){
            $.ajax({
                method: "POST",
                context:this,
                url: url_ajax_event+"php/event/changeState",
                data: JSON.stringify({"date":$(this).attr("data-date")}),
                contentType: 'application/json',
                dataType:'json'
            }).done(function(data){
                console.log(data);
                showAlert("success", data.message);
                $(this).toggleClass('bg-danger');
            }).fail(function (jqXHR) {
                showAlert("danger", jqXHR.responseJSON["message"]);
                console.error(jqXHR.responseText);
            })
        }

    });

}

$('.calendar').load(url_ajax_event+'includes/calendar.html', function () {
    selectYear = document.getElementById("year");
    selectMonth = document.getElementById("month");

    monthAndYear = document.getElementById("monthAndYear");
    showCalendar(currentMonth, currentYear);
});

$.ajax({
    method: "GET",
    context:this,
    url: url_ajax_event+"php/ical/getAll",
    contentType: 'application/json',
    dataType:'json'
}).done(function(data){
    let content="";
    $.each(data, function(i, item){
        content+="<form class='mb-4'>\n" +
            "                <div class=\"row\">\n" +
            "                <div class=\"col-5 pr-0\">\n" +
            "                    <input type=\"text\" class=\"form-control\" id=\"url\" value='"+item.url+"' placeholder=\"URL\">\n" +
            "                </div>\n" +
            "                <div class=\"col-5 pl-0\">\n" +
            "                    <input type=\"text\" class=\"form-control\" id=\"desc\" value='"+item.type+"' placeholder=\"Description\">\n" +
            "                </div>\n" +
            "                    <div class=\"col-2 p-0\">\n" +
            "                <button type=\"submit\" data-id='"+item.id+"' class=\"btn btn-primary\" title='Save modifications'><i class=\"fa fa-save\" aria-hidden=\"true\"></i></button>\n" +
            "                <button data-id='"+item.id+"' class='btn btn-danger' title='Remove this ical'><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></button>\n"+
            "                    </div>\n" +
            "                </div><hr>\n" +
            "            </form>";
    })
    $("#icals").append(content);
    icalsForm();
}).fail(function (jqXHR) {
    console.error(jqXHR.responseText);
})

$("#addIcal").on("click", function(){
    $("#icals").append("<form class='mb-4'>\n" +
        "                <div class=\"row\">\n" +
        "                <div class=\"col-5 pr-0\">\n" +
        "                    <input type=\"text\" class=\"form-control\" id=\"url\" placeholder=\"URL\">\n" +
        "                </div>\n" +
        "                <div class=\"col-5 pl-0\">\n" +
        "                    <input type=\"text\" class=\"form-control\" id=\"desc\"  placeholder=\"Description ex: airbnb, Abritel, ...\">\n" +
        "                </div>\n" +
        "                    <div class=\"col-2 p-0\">\n" +
        "                <button type=\"submit\" class=\"btn btn-primary\" title='Save modifications'><i class=\"fa fa-save\" aria-hidden=\"true\"></i></button>\n" +
        "                <button class='btn btn-danger' title='Remove this ical'><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></button>\n"+
        "                    </div>\n" +
        "                </div><hr>\n" +
        "            </form>");
    icalsForm();
})


$("#fetchIcal").on("click", function(){
    $.get({
        url:url_ajax_event+"php/ical/updateCal",
    }).done(function () {
        showCalendar(currentMonth, currentYear);
    })
})
