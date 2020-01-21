
class _domEventsHandler {
    constructor() {
        this.events = {}
    }

    attachEvent (object, event, callback) {
        var k = this, dispatch = function (b) {
            callback.call(object, k.getEvent(b))
        };
        object.attachEvent("on" + event, dispatch);
        return dispatch
    };

    unlisten(eventName) {
        var presentEvent;
        this.events[eventName] && (presentEvent = this.events[eventName], this.events[eventName] = null, this.removeEventHandler(presentEvent.element, presentEvent.eventName, presentEvent.eventListener));
    }

    listen(object, event, callback, eventName) {
        var presentEvent;
        if (eventName) {
            this.events[eventName] && (presentEvent = this.events[eventName], this.events[eventName] = null, this.removeEventHandler(presentEvent.element, presentEvent.eventName, presentEvent.eventListener));

            if (object.addEventListener) {
                object.addEventListener(event, callback, !1);
            } else if (document.attachEvent) {
                callback = this.attachEvent(object, event, callback);
            } else {
                return null;
            }

            this.events[eventName] = {element: object, eventName: event, eventListener: callback};

            return callback
        }
    };

    removeEventHandler(object, event, listener) {
        document.removeEventListener ? object.removeEventListener(event, listener, !1) : object.detachEvent("on" + event, listener)
    };

    getEvent(event) {
        var eventInstance = event || _this.event;
        if (!eventInstance) {
            for (event = this.getEvent.caller; event && (!(eventInstance = event.arguments[0]) || Event != eventInstance.constructor);) {
                event = event.caller
            };
        }
        return eventInstance
    };
}

const domEventsHandler = new _domEventsHandler();
export { domEventsHandler };