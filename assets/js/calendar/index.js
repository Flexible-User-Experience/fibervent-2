import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import bootstrapPlugin from "@fullcalendar/bootstrap";
import googleCalendarPlugin from '@fullcalendar/google-calendar';

import "@fullcalendar/core/main.css";
import "@fullcalendar/daygrid/main.css";
import "@fullcalendar/timegrid/main.css";
import "@fullcalendar/bootstrap/main.css";

import "./index.css"; // this will create a calendar.css file reachable to 'encore_entry_link_tags'

document.addEventListener("DOMContentLoaded", () => {
    var calendarEl = document.getElementById("calendar-holder");

    var eventsUrl = calendarEl.dataset.eventsUrl;

    var calendar = new Calendar(calendarEl, {
        themeSystem: "standard",
        defaultView: "timeGridWeek",
        nowIndicator: true,
        editable: true,
        locale: "ca",
        firstDay: 1,
        height: "auto",
        minTime: "08:00:00",
        googleCalendarApiKey: "AIzaSyB332MhD5g142kIo79ZagVcXUidQwHbWwk",
        buttonText: {
            today: "avui",
            month: "mes",
            week:  "setmana",
            day:   "dia",
            list:  "llistat"
        },
        eventSources: [
            {
                url: eventsUrl,
                method: "POST",
                extraParams: {
                    filters: JSON.stringify({})
                }
            },
            {
                googleCalendarId: "es.spain#holiday@group.v.calendar.google.com",
                backgroundColor: "#FED3D7",
                textColor: "#FF0000",
                color: "#FED3D7"
            }
        ],
        header: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
        plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, bootstrapPlugin, googleCalendarPlugin], // https://fullcalendar.io/docs/plugin-index
        timeZone: "UTC"
    });
    calendar.render();
});
