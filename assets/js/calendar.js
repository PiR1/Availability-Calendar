/*
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      calendar.js
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
// let selectYear;
// let selectMonth;

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

function showCalendar(month, year) {
    loadEvents();

    let firstDay = (((new Date(year, month)).getDay() - 1) + 7) % 7;
    let daysInMonth = 32 - new Date(year, month, 32).getDate();

    let tbl = document.getElementById("calendar-body"); // body of the calendar

    // clearing all previous cells
    tbl.innerHTML = "";
    
    // filing data about month and in the page via DOM.
    monthAndYear.innerHTML = months[month] + " " + year;

    
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
                cell.classList.add("d-flex","border", "rounded-0","flex-fill","w-100","justify-content-center","pt-3","pb-3");                

            if (i === 0 && j < firstDay || date>daysInMonth) {
                let cellText = document.createTextNode("");
                cell.appendChild(cellText);
                row.appendChild(cell);
            }
            else {
                let cellText = document.createTextNode(date);
                cell.setAttribute("data-date", ("0" + date).slice(-2)+"/"+("0" + (month + 1)).slice(-2)+"/"+year)
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
    
}
$('.calendar').load(url_ajax_event+'includes/calendar.html', function () {
    // selectYear = document.getElementById("year");
    // selectMonth = document.getElementById("month");

    monthAndYear = document.getElementById("monthAndYear");
    showCalendar(currentMonth, currentYear);
});


