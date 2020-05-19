const UTCOffset = new Date().getTimezoneOffset();
const DurationString = /** @class */ (function () {
    function DurationString(start, end) {
        this.start = start;
        this.end = end;
    }

    return DurationString;
}());
const Duration = /** @class */ (function () {
    function Duration(start, end) {
        var startDate = new Date(start);
        var endDate = new Date(end);
        this.start = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate(), 0, UTCOffset, 0, 0);
        this.end = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate(), 23, 59 + UTCOffset, 59, 999);
    }

    return Duration;
}());
let jsonStringDates;
let jsonDates;

function  loadEvents() {
    if (typeof url_ajax_event == 'undefined'){
        url_ajax_event="";
    }
    $.get({
        url:url_ajax_event+"php/event/getAll",
        async: false,
    }).done(function (data) {
        jsonStringDates = data;
    }).fail(function () {
        jsonStringDates=[];
    })
    jsonDates= jsonStringDates.map(function (e) { return new Duration(e.start, e.end); });
}



function dayBetween(checkDate, startDate, endDate) {
    return (checkDate >= startDate && checkDate <= endDate);
}
function eventOnDay(day, dates) {
    return dates.some(function (e) {
        const startDate = e.start;
        const endDate = e.end;
        return dayBetween(day, startDate, endDate);
    });
}
loadEvents();