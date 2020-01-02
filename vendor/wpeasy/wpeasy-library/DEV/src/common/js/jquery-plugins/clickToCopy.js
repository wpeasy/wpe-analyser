import 'jquery'

(function ($) {
    $.fn.clickToCopy = function (options) {

        let $allCopyableElements; //global for all elements to be initialised only once

        //Make sure we have a text element off screen to enable copying from it.
        let $copyTextInput = $('#wpe_copyTextInput')
        if($copyTextInput.length === 0){
            $('body').append('<input type="text" id="wpe_copyTextInput" />')
            $copyTextInput = $('#wpe_copyTextInput')
        }

        //OPTIONS AND SETTINGS
        const defaults = {
            logEvents: false,
            asHtml: false,
            copyableElementsSelector: '.wpe_clickToCopy'
        };

        const settings = {...defaults, ...options}
        const log = settings.logEvents;
        const $self = $(this);

        if( $self.data('asHtml') === 'true'){ settings.asHtml = true }


        //Allow for multiple elements selected
        if (this.length > 1) {
            this.each(function () {
                $(this).clickToCopy(options)
            });
            return this;
        }


        this.initialize = () => {
            if($allCopyableElements === undefined){
                $allCopyableElements = $(settings.copyableElementsSelector);
            }

            $self.on('click', function (e) {
                e.preventDefault()
                let valueToCopy;

                //Update CSS
                $.fn.clickToCopy.clearCopiedFlags(settings.copyableElementsSelector);

                $self.addClass('copied')

                //Allow for input as first child of DIV or SPAN
                if($self.children(":first").prop('nodeName') === 'INPUT'){
                    valueToCopy = $self.children(":first").val()
                }else if(  $self.text() ){
                    //Inner Text of DIV or SPAN
                    valueToCopy = settings.asHtml? $self.html() : $self.text();
                }else{
                    //Directly on an input or text area
                    valueToCopy =  $self.val();
                }
                $copyTextInput.val(valueToCopy)
                $copyTextInput[0].select();
                $copyTextInput[0].setSelectionRange(0, 99999);
                document.execCommand("copy")

                if(settings.logEvents) { console.log('CLICK TO COPY DONE')}
            })
        }

        this.initialize()
    }

    $.fn.clickToCopy.clearCopiedFlags = (selector = '.wpe_clickToCopy' ) => {
        $(selector).removeClass('copied')
    }
})(jQuery)