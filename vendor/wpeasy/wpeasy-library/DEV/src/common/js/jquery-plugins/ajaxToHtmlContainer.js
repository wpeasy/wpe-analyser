import 'jquery'

/**
 * If onDoneCallback or onErrorCallback are not defined, returned messages will be added to the $bodyEl
 * Otherwise, it is up to the callback to display the returned data.
 */

(function ($) {
    $.fn.ajaxToHtmlContainer = function (options) {
        const $this = $(this)
        let defaults = {
            url: window.ajaxurl,
            dataProvider: null,
            logEvents: false,
            onTriggerCallback: null,
            onDoneCallback: null,
            onErrorCallback: null,
            onAlwaysCallback: null
        }

        const settings = {...defaults, ...options}
        const log = settings.logEvents;

        if (this.length > 1) {
            this.each(function () {
                $(this).ajaxToHtmlContainer(options)
            });
            return this;
        }

        const $bodyEl = $($this.data('bodySelector'))
        const triggerEvent = $this.data('triggerEvent')
        const triggerSelector = $this.data('triggerSelector')
        const action = $this.data('action')

        this.initialize = function () {
            if (triggerEvent === 'immediate') {
                doAjax().then(function () {
                    if(log) { console.info('doAjax:immediate, action:' + action) }
                })
            } else {
                if(log) { console.info('BOUND: ' + triggerSelector + ' on ' + triggerEvent) }
                $(triggerSelector).on(triggerEvent, function (e) {
                    if(log) { console.log('EVENT:', $(e.currentTarget).data('ajaxAction'), action) }
                    if ($(e.target).data('ajaxAction') !== action) {
                        //console.error('Error. The trigger element action does not match this element.')
                        return;
                    }
                    doAjax().then(function () {
                        if(log) { console.info('doAjax:' + triggerSelector + ' | ' + triggerEvent + ' , action:' + action) }
                    })
                })
            }
            return this
        }


        async function doAjax() {

            //Get data from a provider function if one is declared
            let providerData = {};
            if ( typeof settings.dataProvider === 'function') {
                providerData = settings.dataProvider()
            }

            if(settings.onTriggerCallback){ settings.onTriggerCallback() }

            //Add a progress bar
            $bodyEl.html('<div class="progress" style="position: relative;"><div class="progress-bar progress-bar-striped indeterminate"></div></div>');



            $.post(
                {
                    dataType: 'html',
                    url: settings.url,
                    data: $.extend({}, {action: action},  providerData)
                }
            )
                .done(function (result) {
                    $bodyEl.html('');//Clear current body
                    if(settings.onDoneCallback){
                        settings.onDoneCallback(result)
                    }else{
                        $bodyEl.html(result);
                    }
                })
                .fail(function (err) {
                    if(settings.onErrorCallback){
                        settings.onErrorCallback(err)
                    }else{
                        $bodyEl.html('<div class="alert alert-danger ">An error occurred: ' + err.responseText + " (" + err.status + ")" + '</div>');
                    }
                })
                .always(function () {
                    if(settings.onAlwaysCallback ){ settings.onAlwaysCallback() }
                })
        }

        return this.initialize();
    }
})(jQuery)