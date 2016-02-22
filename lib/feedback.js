window.linora = window.linora || {};
window.linora.feedback = function () {

    "use strict";

    var createEntry = function (msg, className) {
            var e = window.document.createElement("p");
            if (className) {
                e.classList.add(className);
            }
            e.classList.add("closable");
            e.innerHTML = msg;

            e.addEventListener(
                "click",
                function (ev) {
                    // var fbz = document.getElementById("feedbackzone");
                    // fbz.classList.add("fedback");
                    // fbz.innerHTML = "";
                    // e.target.parentNode.removeChild(e.target);
                    ev.currentTarget.classList.add("fedback");

                    window.setTimeout(
                        function () {
                            ev.target.parentNode.removeChild(ev.target);
                        },
                        1000
                    );
                }
            );
            return e;
        },

        log = function (msg, className) {
            var fb = window.document.getElementById("feedback");
            fb.appendChild(
                createEntry(msg, className)
            );
        },

        logDebug = function (result) {
            if (result.debug) {
                result.debug.forEach(log, "debug");
            }
        },

        welcome = function () {
            log("Welcome to Linora!  Acknowledge messages like this with a click or tap.");
        };

    return {
        "log": log,
        "logDebug": logDebug,
        "welcome": welcome
    };

}();

// window.addEventListener('load', linora.feedback.welcome);
