window.linora = window.linora || {};
window.linora.ui = (function () {

    "use strict";

    var

        page_sections = ["links", "add", "help"],
        n = {},
        url = new URL(window.location),

		tagClicked = function (e) {
			clearSearch(e.target.innerHTML);
		},

		/*
		 takes a positive or negative number of steps
		 and activates the appropriate ui panel.
		 */
		panelAdvance = function(steps) {
			var i, r, x=0;

            for (i = 0; i < page_sections.length; i = i + 1) {
                r = document.getElementById(page_sections[i] + ".flip");
                if (r.checked) {
                    x=(Math.abs(i+steps+page_sections.length)) % page_sections.length;
                }
            }

			document.getElementById(page_sections[x] + ".flip").checked = true;
			flip();
		},

        renderUpdatedEntry = function (parent, entry) {
        	var anchor, div, parts, i, p, sp, currentTags, edit;
        	// get handles on what has to change
        	anchor = parent.querySelector("a");
        	div = parent.querySelector("div");

        	// update the link
        	anchor.href=entry.url;
        	anchor.innerText=entry.cap;
        	
			currentTags = div.querySelectorAll(".tag");
			for (i = currentTags.length - 1; i >= 0; i--) {
				currentTags[i].remove();
			}

			edit = div.querySelector(".edit");

        	// update the tags
            parts = entry.cat.split(",");
            for (i = 0; i < parts.length; i = i + 1) {
                p = parts[i].trim();
                if (p.length > 0) {
                    sp = document.createElement("span");
                    sp.classList.add("tag");
                    sp.appendChild(document.createTextNode(p));
                    sp.addEventListener("click", tagClicked );
                    div.insertBefore(sp, edit);
                }
            }
        },

        renderEntry = function (parent, entry, top, selected) {

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
                    sp.addEventListener("click", tagClicked );
                    div.appendChild(sp);
                }
            }

            addEditButton(div, entry);

            if (top && parent.firstChild) {
                parent.insertBefore(li, parent.firstChild);
            } else {
                parent.appendChild(li);
            }

            setTimeout(
                function () {
                    li.classList.remove("switchingoff");
                    if (selected) {
                         selectTopElem();
                    }
                },
                1
            );

        },


		/*
		if no element is selected, select the first visible element, otherwise
		if next is true, select the next element, if false select the previous one.
		*/
		selectElem = function (next) {
			var currentSelection, nextSelection, originalSelection;

			originalSelection = currentSelection = document.querySelector(".links li.selected");
			if (currentSelection) {
				currentSelection.classList.remove("selected");

				// find next visble sibling
				do {
					if (next) {
						nextSelection = currentSelection.nextSibling;
					} else {
						nextSelection = currentSelection.previousSibling;
					}
					currentSelection = nextSelection;
				} while (nextSelection && nextSelection.classList.contains("switchingoff"));

			} else {
				nextSelection = document.querySelector(".links li:not([class='switchingoff'])");
			}

			if (nextSelection) {
				nextSelection.classList.add("selected");
				var rect = nextSelection.getBoundingClientRect();
				window.scrollTo(0,window.scrollY + rect.top - (window.innerHeight/2));
			} else {
				originalSelection.classList.add("selected");				
			}
		},


		/* 
		remove the current selection, then 
		select the top element (which relies on the default)
		behaviour of selectElem)
		*/
		selectTopElem = function () {
			var currentSelection = document.querySelector(".links li.selected");
			if (currentSelection) {
				currentSelection.classList.toggle("selected");
			}
			selectElem(true);
		},

		removeOldResults = function (e, result) {
			if (result.rows) {

				var i, j, found, li = e.querySelectorAll("li");

				for (i=0; i<li.length; i=i+1) {

					found = false;

					for (j=0; j<result.rows.length; j=j+1) {
						if (li[i].id === "db" + result.rows[j].id) {
							found = true;
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
            var i, exists, needSelect=false;

            if (result.rows) {
	            for (i = 0; i < result.rows.length; i = i + 1) {
	                if (result.rows.hasOwnProperty(i)) {
	                    exists = e.querySelector("#db" + result.rows[i].id);
	                    if (exists) {
	                    	if (result.meta.action=="update") {
		                        renderUpdatedEntry(e, result.rows[i]);
	                    	} else {
	                    		needSelect = true;
	                    	}
	                    } else {
	                    	// doesn't exist so add
	                        renderEntry(e, result.rows[i], top, selected);
	                    }
	                }
	            }            	
            } else {

                if (result.meta && result.meta.feedback) {
                    window.linora.feedback.log(result.meta.feedback);
                } else {
                    // generic feedback message
                    window.linora.feedback.log("Sorry, no data to show!");
                }

            }

            if (needSelect) {
 				selectTopElem();
            }
        },


        injectResults = function (data, e, top, selected) {
            var ul;

            ul = e.querySelector("ul.links");
            if (!ul) {
                ul = document.createElement("ul");
                ul.classList.add("links");
                e.appendChild(ul);
            }
            renderResults(ul, data, top, selected);
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

        goToLinksPanel = function() {
			resetAddForm( document.getElementById("postage") );
			var lf = document.getElementById( "links.flip" );
			if (lf.checked) {
				// already on the right panel
			} else {
				lf.checked=true;
				flip();
			}
			document.getElementById("search").focus();
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

        injectAndUpdate = function (e) {
        	var result = JSON.parse(e.target.responseText);
            if (window.linora.feedback) {
                window.linora.feedback.logDebug(result);
            }
            injectResults(
                result,
                n.links.panel,
                null,
                true
            );
            updateSearch();
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

        updateSearch = function() {
        	var s = document.getElementById("search");
        	// todo improve!
        	s.value = url.search.substring(1).split('&')[0].substring(2);
        },

        refreshLinksPanel = function () {
            window.linora.xhr.load(
                {
                    "url": "api/2/links",
                    "callbacks": {
                        "load": injectAndUpdate,
                        "error": failedToLoad
                    }
                }
            );
        },

        clearSearch = function(x) {
			var s, ev;
			s = document.getElementById("search");
			ev = new Event("input");
        	s.value = x ? x : "";
			s.dispatchEvent(ev);
			goToLinksPanel();
        },

        elementAdded = function (e) {
			var result, isUpdate, existing;

			result = JSON.parse(e.target.responseText);

			isUpdate = (result.meta.action == "update");

			if (isUpdate) {
                existing = document.querySelector("#db" + result.rows[0].id);
				renderUpdatedEntry(existing, result.rows[0]);
			} else {
				// ensure no searches active 
				// when it's not an update
				// so new entry will be visible
				clearSearch();

				// add results to page
				injectResults(
					result,
	                n.links.panel,
	                true,
	                true,
	                isUpdate
	            );
			}

			// activate links panel
			goToLinksPanel();

        },

		populateAddFields = function (entry) {

			var elem;

			// flip resets the content so do it first
			elem = document.getElementById("add.flip");
			elem.checked = true;
			flip();

			// set the field contents
			elem = document.getElementById("postage");
			elem.cat.value = entry.cat;
			elem.cap.value = entry.cap;
			elem.url.value = entry.url;
			elem.xid.value = entry.id;

		},

		addEditButton = function (target, entry) {
			var sp = document.createElement("span");
			sp.classList.add("edit");
			sp.appendChild(document.createTextNode("Edit"));
			sp.addEventListener("click",
				function () {
					populateAddFields(entry);
				}
			);
			target.appendChild(sp);
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

        /*
        return the value of a form field or an empty string
         */
        val = function(x, n, defaultValue) {
            if (postage[n] && postage[n].value != "") {
                    x[n] = postage[n].value;
            } else {
                if (defaultValue) {
                    x[n] = defaultValue;
                }
            }
        },

        insertEntry = function () {
            var url,
                x = {};
            val(x,"cat", "?");
            val(x,"cap", "?");
            val(x,"url", "");
            val(x,"parent");
            val(x,"xid");

            // this id the difference between an update and a create
            // if there is an xid, then it should be an update, in which
            // case the URI of the resource being updated should be used
            url = "api/2/links";
            if (x.xid && x.xid != "") {
                url += "/"+ x.xid;
            }

            window.linora.xhr.load(
                {
                    "method": "POST",
                    "url": url,
                    "query": x,
                    "callbacks": {
                        "load": elementAdded,
                        "error": failedToLoad
                    }
                }
            );

            // reset the form
            resetAddForm(postage);

        },

        resetAddForm = function(f) {
            f.cat.value = f.url.value = f.cap.value = "";
        },

		replaceResults = function (data, target, top, selected) {
			var e = document.querySelector(target);

			removeOldResults(e, data);
			renderResults(e, data, top, selected);
		},

		doSearch = function (e) {

			window.linora.xhr.load(
				{
					"url": "api/1/links/?filter="+e.currentTarget.value,
					"callbacks": {
						"load": function(e) {
							var result = JSON.parse(e.target.responseText); 
							replaceResults( result, "#links", true, true );
						},
						"error": failedToLoad
					}
				}
			);

			url.search = "?q="+e.currentTarget.value;
			console.log(url.toString());
			console.log(url);

			// we just replace the state and not push it, or every time
			// a key is pressed we'd fill up the history
			history.pushState(null, null, url.toString());

			// flip to the links page if necessary
			goToLinksPanel();
		}, 

        setup = function () {
            prepPanelHandles();
            attachFlipListeners();
            disableFormSubmit();
            refreshLinksPanel();

			document.getElementById("search").addEventListener( "input", doSearch );

            // Set up the "submit to add an entry" event handler
            document.getElementById("postagebut").addEventListener("click", insertEntry);

            window.linora.xhr.load(
                {
                    "url": "help.html",
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
        "populateFromDoc": populateFromDoc,
        "clearSearch": clearSearch,
        "selectElem": selectElem,
        "panelAdvance": panelAdvance
    };

}());

window.addEventListener("load", window.linora.ui.setup);
