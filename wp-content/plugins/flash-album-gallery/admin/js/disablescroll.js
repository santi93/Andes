function hookMouseWheel(){
    if (window.addEventListener)
        window.addEventListener('DOMMouseScroll', onMouseWheel, false);
    window.onmousewheel = document.onmousewheel = onMouseWheel;
}

function isOverSwf(mEvent)
{
    var elem;
    if (mEvent.srcElement) {
        elem = mEvent.srcElement.nodeName;
    } else if (mEvent.target) {
        elem = mEvent.target.nodeName;
    }

    if (elem.toLowerCase() == "object" || elem.toLowerCase() == "embed") {
//    if (jQuery(elem).hasClass('flashalbum')) {
        return true;
    }
    return false;
}

function onMouseWheel(event)
{
    var delta = 0;
    if (!event)
        event = window.event;
    if (event.wheelDelta) {
        delta = event.wheelDelta/120;
        if (window.opera) delta = -delta;
    } else if (event.detail) {
        delta = -event.detail/3;
    }

    if (isOverSwf(event)) {
        return cancelEvent(event);
    }

    return true;
}

function cancelEvent(e)
{
    e = e ? e : window.event;
    if (e.stopPropagation)
        e.stopPropagation();
    if (e.preventDefault)
        e.preventDefault();
    e.cancelBubble = true;
    e.cancel = true;
    e.returnValue = false;
    return false;
}

hookMouseWheel();
