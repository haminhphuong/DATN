!function (l) {
    "use strict";
    var t = tinymce.util.Tools.resolve("tinymce.PluginManager"), n = tinymce.util.Tools.resolve("tinymce.util.VK"),
        e = function (t) {
            return t.target_list
        }, o = function (t) {
            return t.rel_list
        }, i = function (t) {
            return t.link_class_list
        }, p = function (t) {
            return "boolean" == typeof t.link_assume_external_targets && t.link_assume_external_targets
        }, a = function (t) {
            return "boolean" == typeof t.link_context_toolbar && t.link_context_toolbar
        }, r = function (t) {
            return t.link_list
        }, k = function (t) {
            return "string" == typeof t.default_link_target
        }, y = function (t) {
            return t.default_link_target
        }, b = e, _ = function (t, e) {
            t.settings.target_list = e
        }, w = function (t) {
            return !1 !== e(t)
        }, T = o, C = function (t) {
            return o(t) !== undefined
        }, M = i, O = function (t) {
            return i(t) !== undefined
        }, R = function (t) {
            return !1 !== t.link_title
        }, REL = function (t) {
            return !1 !== t.link_rel
        }, N = function (t) {
            return "boolean" == typeof t.allow_unsafe_link_target && t.allow_unsafe_link_target
        }, u = tinymce.util.Tools.resolve("tinymce.dom.DOMUtils"), c = tinymce.util.Tools.resolve("tinymce.Env"),
        s = function (t) {
            if (!c.ie || 10 < c.ie) {
                var e = l.document.createElement("a");
                e.target = "_blank", e.href = t, e.rel = "noreferrer noopener";
                var n = l.document.createEvent("MouseEvents");
                n.initMouseEvent("click", !0, !0, l.window, 0, 0, 0, 0, 0, !1, !1, !1, !1, 0, null), r = e, a = n, l.document.body.appendChild(r), r.dispatchEvent(a), l.document.body.removeChild(r)
            } else {
                var o = l.window.open("", "_blank");
                if (o) {
                    o.opener = null;
                    var i = o.document;
                    i.open(), i.write('<meta http-equiv="refresh" content="0; url=' + u.DOM.encode(t) + '">'), i.close()
                }
            }
            var r, a
        }, A = tinymce.util.Tools.resolve("tinymce.util.Tools"), f = function (t, e) {
            var n, o, i = ["noopener"], r = t ? t.split(/\s+/) : [], a = function (t) {
                return t.filter(function (t) {
                    return -1 === A.inArray(i, t)
                })
            };
            return (r = e ? (n = a(n = r)).length ? n.concat(i) : i : a(r)).length ? (o = r, A.trim(o.sort().join(" "))) : null
        }, d = function (t, e) {
            return e = e || t.selection.getNode(), v(e) ? t.dom.select("a[href]", e)[0] : t.dom.getParent(e, "a[href]")
        }, m = function (t) {
            return t && "A" === t.nodeName && t.href
        }, v = function (t) {
            return t && "FIGURE" === t.nodeName && /\bimage\b/i.test(t.className)
        }, g = function (t, e) {
            var n, o;
            (o = t.dom.select("img", e)[0]) && (n = t.dom.getParents(o, "a[href]", e)[0]) && (n.parentNode.insertBefore(o, n), t.dom.remove(n))
        }, h = function (t, e, n) {
            var o, i;
            (i = t.dom.select("img", e)[0]) && (o = t.dom.create("a", n), i.parentNode.insertBefore(o, i), o.appendChild(i))
        }, L = function (i, r) {
            return function (o) {
                i.undoManager.transact(function () {
                    var t = i.selection.getNode(), e = d(i, t), n = {
                        href: o.href,
                        target: o.target ? o.target : null,
                        rel: o.rel ? o.rel : null,
                        "class": o["class"] ? o["class"] : null,
                        title: o.title ? o.title : null
                    };
                    C(i.settings) || !1 !== N(i.settings) || (n.rel = f(n.rel, "_blank" === n.target)), o.href === r.href && (r.attach(), r = {}), e ? (i.focus(), o.hasOwnProperty("text") && ("innerText" in e ? e.innerText = o.text : e.textContent = o.text), i.dom.setAttribs(e, n), i.selection.select(e), i.undoManager.add()) : v(t) ? h(i, t, n) : o.hasOwnProperty("text") ? i.insertContent(i.dom.createHTML("a", n, i.dom.encode(o.text))) : i.execCommand("mceInsertLink", !1, n)
                })
            }
        }, P = function (e) {
            return function () {
                e.undoManager.transact(function () {
                    var t = e.selection.getNode();
                    v(t) ? g(e, t) : e.execCommand("unlink")
                })
            }
        }, x = m, E = function (t) {
            return 0 < A.grep(t, m).length
        }, S = function (t) {
            return !(/</.test(t) && (!/^<a [^>]+>[^<]+<\/a>$/.test(t) || -1 === t.indexOf("href=")))
        }, I = d, K = function (t, e) {
            var n = e ? e.innerText || e.textContent : t.getContent({format: "text"});
            return n.replace(/\uFEFF/g, "")
        }, U = f, D = tinymce.util.Tools.resolve("tinymce.util.Delay"), B = tinymce.util.Tools.resolve("tinymce.util.XHR"),
        F = {}, q = function (t, o, e) {
            var i = function (t, n) {
                return n = n || [], A.each(t, function (t) {
                    var e = {text: t.text || t.title};
                    t.menu ? e.menu = i(t.menu) : (e.value = t.value, o && o(e)), n.push(e)
                }), n
            };
            return i(t, e || [])
        }, V = function (e, t, n) {
            var o = e.selection.getRng();
            D.setEditorTimeout(e, function () {
                e.windowManager.confirm(t, function (t) {
                    e.selection.setRng(o), n(t)
                })
            })
        }, z = function (a, t) {
            var e, l, o, u, n, i, r, c, s, f, z, d, m = {}, v = a.selection, g = a.dom, h = function (t) {
                var e = o.find("#text");
                (!e.value() || t.lastControl && e.value() === t.lastControl.text()) && e.value(t.control.text()), o.find("#href").value(t.control.value())
            }, x = function () {
                l || !u || m.text || this.parent().parent().find("#text")[0].value(this.value())
            };
            u = S(v.getContent()), e = I(a), m.text = l = K(a.selection, e), m.href = e ? g.getAttrib(e, "href") : "", e ? m.target = g.getAttrib(e, "target") : k(a.settings) && (m.target = y(a.settings)), (d = g.getAttrib(e, "rel")) && (m.rel = d), (d = g.getAttrib(e, "class")) && (m["class"] = d), (d = g.getAttrib(e, "title")) && (m.title = d), u && (n = {
                name: "text",
                type: "textbox",
                size: 40,
                label: "Text to display",
                onchange: function () {
                    m.text = this.value()
                }
            }), t && (i = {
                type: "listbox",
                label: "Link list",
                values: q(t, function (t) {
                    t.value = a.convertURL(t.value || t.url, "href")
                }, [{text: "None", value: ""}]),
                onselect: h,
                value: a.convertURL(m.href, "href"),
                onPostRender: function () {
                    i = this
                }
            }), w(a.settings) && (b(a.settings) === undefined && _(a, [{text: "None", value: ""}, {
                text: "New window",
                value: "_blank"
            }]), c = {
                name: "target",
                type: "listbox",
                label: "Target",
                values: q(b(a.settings))
            }), C(a.settings) && (r = {
                name: "rel", type: "listbox", label: "Rel", values: q(T(a.settings), function (t) {
                    !1 === N(a.settings) && (t.value = U(t.value, "_blank" === m.target))
                })
            }), O(a.settings) && (s = {
                name: "class",
                type: "listbox",
                label: "Class",
                values: q(M(a.settings), function (t) {
                    t.value && (t.textStyle = function () {
                        return a.formatter.getCssText({inline: "a", classes: [t.value]})
                    })
                })
            }), R(a.settings) && (f = {
                name: "title",
                type: "textbox",
                label: "Title",
                value: m.title
            }), REL(a.settings) && (z = {
                name: "rel",
                type: "listbox",
                label: "No Follow",
                values: [{text: "No", value: ""}, {text: "Yes", value: "nofollow"}]
            }), o = a.windowManager.open({
                title: "Insert link",
                data: m,
                body: [{
                    name: "href",
                    type: "filepicker",
                    filetype: "file",
                    size: 40,
                    autofocus: !0,
                    label: "Url",
                    onchange: function (t) {
                        var e = t.meta || {};
                        i && i.value(a.convertURL(this.value(), "href")), A.each(t.meta, function (t, e) {
                            var n = o.find("#" + e);
                            "text" === e ? 0 === l.length && (n.value(t), m.text = t) : n.value(t)
                        }), e.attach && (F = {href: this.value(), attach: e.attach}), e.text || x.call(this)
                    },
                    onkeyup: x,
                    onpaste: x,
                    onbeforecall: function (t) {
                        t.meta = o.toJSON()
                    }
                }, n, f, z, function (n) {
                    var o = [];
                    if (A.each(a.dom.select("a:not([href])"), function (t) {
                        var e = t.name || t.id;
                        e && o.push({text: e, value: "#" + e, selected: -1 !== n.indexOf("#" + e)})
                    }), o.length) return o.unshift({text: "None", value: ""}), {
                        name: "anchor",
                        type: "listbox",
                        label: "Anchors",
                        values: o,
                        onselect: h
                    }
                }(m.href), i, r, c, s],
                onSubmit: function (t) {
                    var e = p(a.settings), n = L(a, F), o = P(a), i = A.extend({}, m, t.data), r = i.href;
                    r ? (u && i.text !== l || delete i.text, 0 < r.indexOf("@") && -1 === r.indexOf("//") && -1 === r.indexOf("mailto:") ? V(a, "The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?", function (t) {
                        t && (i.href = "mailto:" + r), n(i)
                    }) : !0 === e && !/^\w+:/i.test(r) || !1 === e && /^\s*www[\.|\d\.]/i.test(r) ? V(a, "The URL you entered seems to be an external link. Do you want to add the required http:// prefix?", function (t) {
                        t && (i.href = "http://" + r), n(i)
                    }) : n(i)) : o()
                }
            })
        }, H = function (t) {
            var e, n, o;
            n = z, "string" == typeof (o = r((e = t).settings)) ? B.send({
                url: o, success: function (t) {
                    n(e, JSON.parse(t))
                }
            }) : "function" == typeof o ? o(function (t) {
                n(e, t)
            }) : n(e, o)
        }, J = function (t, e) {
            return t.dom.getParent(e, "a[href]")
        }, $ = function (t) {
            return J(t, t.selection.getStart())
        }, j = function (t, e) {
            if (e) {
                var n = (i = e).getAttribute("data-mce-href") || i.getAttribute("href");
                if (/^#/.test(n)) {
                    var o = t.$(n);
                    o.length && t.selection.scrollIntoView(o[0], !0)
                } else s(e.href)
            }
            var i
        }, G = function (t) {
            return function () {
                H(t)
            }
        }, X = function (t) {
            return function () {
                j(t, $(t))
            }
        }, Q = function (r) {
            return function (t) {
                var e, n, o, i;
                return !!(a(r.settings) && (!(i = r.plugins.contextmenu) || !i.isContextMenuVisible()) && x(t) && 3 === (o = (n = (e = r.selection).getRng()).startContainer).nodeType && e.isCollapsed() && 0 < n.startOffset && n.startOffset < o.data.length)
            }
        }, W = function (o) {
            o.on("click", function (t) {
                var e = J(o, t.target);
                e && n.metaKeyPressed(t) && (t.preventDefault(), j(o, e))
            }), o.on("keydown", function (t) {
                var e, n = $(o);
                n && 13 === t.keyCode && !0 === (e = t).altKey && !1 === e.shiftKey && !1 === e.ctrlKey && !1 === e.metaKey && (t.preventDefault(), j(o, n))
            })
        }, Y = function (n) {
            return function () {
                var e = this;
                n.on("nodechange", function (t) {
                    e.active(!n.readonly && !!I(n, t.element))
                })
            }
        }, Z = function (n) {
            return function () {
                var e = this, t = function (t) {
                    E(t.parents) ? e.show() : e.hide()
                };
                E(n.dom.getParents(n.selection.getStart())) || e.hide(), n.on("nodechange", t), e.on("remove", function () {
                    n.off("nodechange", t)
                })
            }
        }, tt = function (t) {
            t.addCommand("mceLink", G(t))
        }, et = function (t) {
            t.addShortcut("Meta+K", "", G(t))
        }, nt = function (t) {
            t.addButton("link", {
                active: !1,
                icon: "link",
                tooltip: "Insert/edit link",
                onclick: G(t),
                onpostrender: Y(t)
            }), t.addButton("unlink", {
                active: !1,
                icon: "unlink",
                tooltip: "Remove link",
                onclick: P(t),
                onpostrender: Y(t)
            }), t.addContextToolbar && t.addButton("openlink", {icon: "newtab", tooltip: "Open link", onclick: X(t)})
        }, ot = function (t) {
            t.addMenuItem("openlink", {
                text: "Open link",
                icon: "newtab",
                onclick: X(t),
                onPostRender: Z(t),
                prependToContext: !0
            }), t.addMenuItem("link", {
                icon: "link",
                text: "Link",
                shortcut: "Meta+K",
                onclick: G(t),
                stateSelector: "a[href]",
                context: "insert",
                prependToContext: !0
            }), t.addMenuItem("unlink", {icon: "unlink", text: "Remove link", onclick: P(t), stateSelector: "a[href]"})
        }, it = function (t) {
            t.addContextToolbar && t.addContextToolbar(Q(t), "openlink | link unlink")
        };
    t.add("link", function (t) {
        nt(t), ot(t), it(t), W(t), tt(t), et(t)
    })
}(window);
