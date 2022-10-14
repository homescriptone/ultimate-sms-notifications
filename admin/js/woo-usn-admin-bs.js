(function ($) {
    'use strict';

   

    //$('select.woousn-select').select2();
    $(document).on('keyup', 'textarea.woo_usn_textarea_text', function (evt) {
        evt.stopPropagation();
        evt.preventDefault();
        $('div.woousn-textcount').remove();
        let limit = $(this).empty().val().length;
        // if (limit >= parseInt(160)) {
        //     $('div.woousn-textcount').append(woo_usn_ajax_object.warning_message);
        // }
        $('<div class="woousn-textcount" style="color : red;"> <strong>' + limit + ' ' + woo_usn_ajax_object.count_characters_typed + '</strong> <br/></div>').insertAfter($(this));
    });

    $(document).on('click dbclick', '.woo-usn-add-more-message', function (evt) {
        evt.stopPropagation();
        evt.preventDefault();
        
        let nextValue = HSGenerateRandom(5);
        let html = '<span class="woo-usn-bulk-sms-index" style="display: grid;" data-id="' + nextValue + '">\n' +
            '\t\t            <textarea class="woo-usn-textarea-' + nextValue + ' woo_usn_textarea_text" rows="5" style="width: 500px;" name="woo-usn[messages-to-send][' + nextValue + ']"></textarea>\n' +
            '\t                <button class="button button-primary woo-usn-remove-prev-block" data-id="' + nextValue + '" style="width: 40% !important;">Remove message block</button>\n' +
            '\t                <br>\n' +
            '</span>';
        
        $(html).insertBefore('span.woo-usn-bulk-sms-add-more-message');
    });


    $(document).on('click dbclick', 'button.woousn-remove', function (evt) {
        evt.stopPropagation();
        evt.preventDefault();
        let block_id = $(this).data('id');
        $('textarea.woo_usn_textarea_messages[data-id=' + block_id + ']').remove();
    });

    $(document).on('click dbclick', '.woo-usn-remove-prev-block', function (e) {
        e.stopPropagation();
        e.preventDefault();
        let blockID = $(this).data('id');
        let blockQty = $('span.woo-usn-bulk-sms-index').length;
       
        $('span.woo-usn-bulk-sms-index[data-id=' + blockID + ']').remove();
    });


    

    $(document).on('keyup', 'textarea.woo_usn_textarea_text', function (evt) {
        evt.stopPropagation();
        evt.preventDefault();
        $('div.woousn-textcount').remove();
        let limit = $(this).empty().val().length;
        // if (limit >= parseInt(160)) {
        //     $('div.woousn-textcount').append(woo_usn_ajax_object.warning_message);
        // }
        $('<div class="woousn-textcount" style="color : red;"> <strong>' + limit + ' ' + woo_usn_ajax_object.count_characters_typed + '</strong> <br/></div>').insertAfter($(this));
    });
    
    $(document).on('ready', function(){
        //Load and display the datetimepicker of the plugin.
        //$('.woo-usn-datetime-local').appendDtpicker();
        $.woousn_datetimepicker.setDateFormatter('moment');
        setTimeout(function(){
            $('#woo-usn-start-date').woousn_datetimepicker({ formatDate: 'Y-m-d', timepicker: true});
            $('#woo-usn-end-date').woousn_datetimepicker({ formatDate: 'Y-m-d', timepicker: true});
        }, 3000);
       
        if ( jQuery('select[name="woo-usn[bulk-sms-receivers]"]').val() == null ){
             jQuery('<p>'+woo_usn_ajax_object.warning_cl_message+'</p>').insertAfter( 'select[name="woo-usn[bulk-sms-receivers]').css('color', 'red');
	}
    });

})(jQuery);
