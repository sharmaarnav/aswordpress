(function () {
	var root = document.documentElement;
	var l10n = window.schematicL10n || {};

	var themeButton = document.querySelector("[data-theme-toggle]");
	if (themeButton) {
		var syncTheme = function () {
			var dark = root.getAttribute("data-theme") === "dark";
			themeButton.setAttribute("aria-pressed", dark ? "true" : "false");
		};
		syncTheme();
		themeButton.addEventListener("click", function () {
			var next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
			root.setAttribute("data-theme", next);
			try {
				localStorage.setItem("schematic-theme", next);
			} catch (error) {}
			syncTheme();
		});
	}

	var navToggle = document.querySelector("[data-nav-toggle]");
	var nav = document.querySelector("[data-nav]");
	if (navToggle && nav) {
		var label = navToggle.querySelector("[data-nav-label]");
		var setOpen = function (open) {
			nav.classList.toggle("is-open", open);
			navToggle.setAttribute("aria-expanded", open ? "true" : "false");
			if (label) {
				label.textContent = open ? l10n.close || "Close" : l10n.menu || "Menu";
			}
		};
		navToggle.addEventListener("click", function () {
			setOpen(!nav.classList.contains("is-open"));
		});
		document.addEventListener("keydown", function (event) {
			if (event.key === "Escape" && nav.classList.contains("is-open")) {
				setOpen(false);
				navToggle.focus();
			}
		});
	}

	var bar = document.querySelector("[data-progress-bar]");
	var article = document.querySelector(".entry-content");
	if (bar && article) {
		var updateProgress = function () {
			var start = article.getBoundingClientRect().top + window.scrollY;
			var height = article.offsetHeight;
			var traveled = window.scrollY + window.innerHeight * 0.15 - start;
			var total = Math.max(height - window.innerHeight * 0.2, 1);
			var amount = Math.min(1, Math.max(0, traveled / total));
			bar.style.transform = "scaleX(" + amount + ")";
		};
		updateProgress();
		document.addEventListener("scroll", updateProgress, { passive: true });
		window.addEventListener("resize", updateProgress);
	}

	var tocLinks = Array.prototype.slice.call(document.querySelectorAll(".toc a"));
	var headings = tocLinks
		.map(function (link) {
			var id = (link.getAttribute("href") || "").slice(1);
			var el = id ? document.getElementById(id) : null;
			return el ? { el: el, link: link } : null;
		})
		.filter(Boolean);

	if (headings.length && "IntersectionObserver" in window) {
		var current = null;
		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}
					if (current) {
						current.removeAttribute("aria-current");
					}
					var match = headings.find(function (item) {
						return item.el === entry.target;
					});
					if (match) {
						match.link.setAttribute("aria-current", "true");
						current = match.link;
					}
				});
			},
			{ rootMargin: "-20% 0px -65% 0px", threshold: 0 }
		);
		headings.forEach(function (item) {
			observer.observe(item.el);
		});
	}

	var pres = document.querySelectorAll(".entry-content pre");
	Array.prototype.forEach.call(pres, function (pre) {
		if (pre.closest(".code-frame")) {
			return;
		}
		var code = pre.querySelector("code");
		var className = ((code && code.className) || pre.className || "");
		var match = className.match(/language-([\w+-]+)|lang-([\w+-]+)/);
		var lang = match ? match[1] || match[2] : "code";
		var frame = document.createElement("div");
		var chrome = document.createElement("div");
		var name = document.createElement("span");
		var button = document.createElement("button");

		frame.className = "code-frame";
		chrome.className = "code-frame__bar";
		name.textContent = lang;
		button.type = "button";
		button.className = "code-frame__copy";
		button.textContent = l10n.copy || "Copy";
		button.addEventListener("click", function () {
			var text = code ? code.innerText : pre.innerText;
			var done = function (ok) {
				button.textContent = ok ? l10n.copied || "Copied" : l10n.copy || "Copy";
				window.setTimeout(function () {
					button.textContent = l10n.copy || "Copy";
				}, 1600);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(
					function () {
						done(true);
					},
					function () {
						done(false);
					}
				);
			} else {
				done(false);
			}
		});
		chrome.appendChild(name);
		chrome.appendChild(button);
		pre.parentNode.insertBefore(frame, pre);
		frame.appendChild(chrome);
		frame.appendChild(pre);
	});
})();
