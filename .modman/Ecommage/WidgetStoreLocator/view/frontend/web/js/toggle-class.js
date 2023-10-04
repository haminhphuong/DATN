define(["jquery"], (function (e) {
    "use strict";
    return e.widget("mage.ecoToggleClass", {
        options: {
            activate: 0,
            openedState: "active",
            type: '[data-toggle="trigger"]'
        }, _create: function () {
            var t = this, a = t.options, i = a.type, n = a.openedState;
            this.element.on("click", i, (function (a) {
                if(e(this).next().hasClass('store-item-wrap')){
                    let isOpen = e(this).next().css('display') !== 'none';
                    e(this).closest('.locator-tabcontent').find('.store-item-wrap').css('display','none');
                    if(isOpen){
                        e(this).next().slideUp();
                        e(this).removeClass('active');
                    }else{
                        e(this).next().slideDown();
                        e(this).addClass('active');
                    }
                }else{
                    a.preventDefault(), t.element.find("." + n).removeClass(n);
                    let i = t.getTarget(this);
                    e(this).addClass(n), t.element.find(i).addClass(n)
                }
            })), ("number" == typeof a.activate) &&  this.element.closest('.column.main').length && this.element.find(i).eq(a.activate).trigger("click")
        }, getTarget: function (t) {
            let a = e(t).attr("data-target");
            return void 0 !== a && !1 !== a || (a = e(t).attr("href")), a
        }
    }), e.mage.ecoToggleClass
}));
