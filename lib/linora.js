window.linora = window.linora || {};
window.linora.ui = (function () {

    "use strict";

    var

        page_sections = ['links', 'help', 'add'],
        n = {},

        renderEntry = function (parent, entry, top, selected) {

            console.log("renderEntry is adding ", entry);

            var li, a, div, parts, i, p, sp;

            li = document.createElement("li");
            li.setAttribute("id", "db" + entry.id);
            li.classList.add("switchingoff");

            a = document.createElement("a");
            li.appendChild(a);
            a.setAttribute("href", entry.url);
            a.appendChild(document.createTextNode(entry.cap));

            div = document.createElement("div");
            div.classList.add("tags");
            li.appendChild(div);

            parts = entry.cat.split(",");
            for (i = 0; i < parts.length; i = i + 1) {
                p = parts[i].trim();
                if (p.length > 0) {
                    sp = document.createElement("span");
                    sp.classList.add("tag");
                    sp.appendChild(document.createTextNode(p));
                    //sp.addEventListener("click", tagClicked );
                    div.appendChild(sp);
                }
            }

            // addEditButton(div, entry);

            if (top && parent.firstChild) {
                parent.insertBefore(li, parent.firstChild);
            } else {
                parent.appendChild(li);
            }

            setTimeout(
                function () {
                    li.classList.remove("switchingoff");
                    //if (selected) {
                    //     selectTopElem();
                    //}
                },
                1
            );

        },

		removeOldResults = function (e, result) {
			if (result.data) {

				var i, j, found, li = e.querySelectorAll("li");

				for (i=0; i<li.length; i=i+1) {

					found = false;

					for (j=0; j<result.data.length; j=j+1) {
						if (li[i].id === "db" + result.data[j].id) {
							found = true;
							console.log("match");
						}
					}

					if (li[i].classList) {
						if (found) {
							li[i].classList.remove("switchingoff");
						} else {
							li[i].classList.add("switchingoff");
						}
					}
				}

			}
		},

        renderResults = function (e, result, top, selected) {
            var i, exists;

            for (i = 0; i < result.data.length; i = i + 1) {
                if (result.data.hasOwnProperty(i)) {
                    exists = e.querySelector("#db" + result.data[i].id);
                    if (!exists) {
                        renderEntry(e, result.data[i], top, selected);
                    }
                }
            }
        },


        injectResults = function (data, e, top, selected) {
            var ul;
            console.log("injectResults ", top, data, selected);

            ul = e.querySelector("ul.links");
            if (!ul) {
                ul = document.createElement("ul");
                ul.classList.add("links");
//				e.insertBefore(ul, e.firstChild);
                e.appendChild(ul);
            }
            renderResults(ul, data, top, selected);
            window.linora.feedback.logDebug(data);

        },


    /*
     This is a Currying closure that creates a new function whose
     id is stored and used when the function is later invoked.
     */
        saveResponseTextTo = function (id) {
            return function (xhr) {
                document.getElementById(id).innerHTML = xhr.target.responseText;
            };
        },

        flip = function () {
            console.log("flip");
            var r, e, i;

            for (i = 0; i < page_sections.length; i = i + 1) {
                r = document.getElementById(page_sections[i] + ".flip");
                e = document.getElementById(page_sections[i]);
                if (r.checked) {
                    e.classList.remove("stealth");
                } else {
                    e.classList.add("stealth");
                }
            }
        },

        attachFlipListeners = function () {
            page_sections.forEach(
                function (s) {
                    n[s].radio.addEventListener("change", flip);
                }
            );
        },

        failedToLoad = function (e) {
            if (window.linora.feedback) {
                window.linora.feedback.log("Failed to load", e);
            }
        },

        prepPanelHandles = function () {
            var i;
            // automatically set up n.links.panel and n.links.radio (etc)
            // so we don't have to repeatedly grab references.
            for (i = 0; i < page_sections.length; i = i + 1) {
                n[page_sections[i]] = {
                    "panel": document.getElementById(page_sections[i]),
                    "radio": document.getElementById(page_sections[i] + ".flip")
                };
            }
        },


        disableFormSubmit = function () {
            var i,
                f = function (e) {
                    e.stopPropagation();
                    return false;
                };

            // ensure all forms do nothing.
            for (i = 0; i < document.forms.length; i += 1) {
                document.forms[i].addEventListener("submit", f, false);
            }
        },


        refreshLinksPanel = function () {
            window.linora.xhr.load(
                {
                    "url": "api/1/links/",
                    "callbacks": {
                        "load": function (e) {
                            injectResults(
                                JSON.parse(e.target.responseText),
                                n.links.panel
                            );
                        },
                        "error": failedToLoad
                    }
                }
            );
        },

        feedbackAndSwitch = function (e) {
            var result = JSON.parse(e.target.responseText);
            window.linora.feedback.logDebug(result);
            refreshLinksPanel();
        },

        elementAdded = function (e) {
			document.getElementById( "links.flip" ).checked=true;
			flip();
			injectResults(
            	JSON.parse(e.target.responseText),
                n.links.panel,
                true,
                true
            );
        },

		populateFromDoc = function () {
			var 

			url = "/api/1/proxy/?url="+ encodeURI( document.getElementById("postageurl").value ),

			docloaded = function(e) {
				var x = JSON.parse(e.target.responseText);
				if (x.title) {
					document.getElementById("postagecap").value = x.title;
					document.getElementById("postagecat").value = x.title;
				}
				if (x.keywords) {
					document.getElementById("postagecat").value = x.keywords;
				}
			};

			window.linora.xhr.load(
				{
					"url": url,
					"callbacks": {
						"load": docloaded,
						"error": failedToLoad
					}
				}
			);

		},

        insertEntry = function () {

            console.log("insertEntry");

            var
                p = document.getElementById("postage"),
                x = {
                    "cat": p.cat.value,
                    "cap": p.cap.value,
                    "url": p.url.value,
                    "xid": p.xid.value
                };

            console.log("Inserting", x);

            if (x.cat === "") {
                x.cat = "?";
            }
            if (x.cap === "") {
                x.cap = x.url;
            }

            window.linora.xhr.load(
                {
                    "method": "POST",
                    "url": "api/1/links/add/",
                    "query": x,
                    "callbacks": {
                        "load": elementAdded,
                        "error": failedToLoad
                    }
                }
            );

            // reset the form
            p.cat.value = p.url.value = p.cap.value = "";

        },

		replaceResults = function (data, target) {
			console.log("replaceResults", data);
			var e = document.querySelector(target);

			renderResults(e, data);
			removeOldResults(e, data);
		},

		dosearch = function (e) {

			var text = document.getElementById("search").value;

			window.linora.xhr.load(
				{
					"url": "api/1/links/?filter="+text,
					"callbacks": {
						"load": function(e) {
							var result = JSON.parse(e.target.responseText); 
							replaceResults( result, "#links" );
							// selectTopElem();
						},
						"error": failedToLoad
					}
				}
			);

			// flip to the links page if necessary
			var lf = document.getElementById( "links.flip" );
			if (lf.checked) {
				// already on the right panel
			} else {
				lf.checked=true;
				flip();
			}

		}, 

        setup = function () {
            console.log("setup");
            prepPanelHandles();
            attachFlipListeners();
            disableFormSubmit();
            refreshLinksPanel();

			document.getElementById("search").addEventListener( "input", dosearch );

            // Set up the "submit to add an entry" event handler
            document.getElementById("postagebut").addEventListener("click", insertEntry);

            window.linora.xhr.load(
                {
                    "url": "help.get.html",
                    "callbacks": {
                        "load": saveResponseTextTo("help")
                    }
                }
            );
            flip();
        };


    return {
        "setup": setup,
        "flip": flip,
        "populateFromDoc": populateFromDoc
    };

}());

window.addEventListener('load', window.linora.ui.setup);
