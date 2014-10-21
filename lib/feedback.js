window.linora = window.linora || {};
window.linora.feedback = function (createEntry) {

    "use strict";

    var createEntry = function (msg, className) {
            var e = document.createElement("p");
            if (className) {
                e.classList.add(className);
            }
            e.classList.add('closable');
            e.innerHTML = msg;

            e.addEventListener(
                "click",
                function (e) {
                    // var fbz = document.getElementById("feedbackzone");
                    // fbz.classList.add("fedback");
                    // fbz.innerHTML = "";
                    // e.target.parentNode.removeChild(e.target);
                    e.currentTarget.classList.add("fedback");
                    setTimeout(
                        function () {
                            e.target.parentNode.removeChild(e.target);
                        },
                        1000
                    );
                }
            );
            return e;
        },

        log = function (msg, className) {
            var fb = document.getElementById("feedback");
            console.log("LOG", msg);
            fb.appendChild(
                createEntry(msg, className)
            );
        },

        logDebug = function (result) {
            // result.meta.forEach(log, "meta");
            result.debug.forEach(log, "debug");
        },

        welcome = function () {
            log("Welcome to Linora!  Acknowledge messages like this with a click or tap.");
        };

    return {
        "log": log,
        "logDebug": logDebug,
        "welcome": welcome
    }

}();

// window.addEventListener('load', linora.feedback.welcome);

