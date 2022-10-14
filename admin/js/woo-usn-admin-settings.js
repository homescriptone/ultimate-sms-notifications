(function ($) {
  "use strict";
  $(document).ready(function () {
    var woo_usn_btn_testing = $("input#woo_usn_testing");
    var woo_usn_messages_fields = $("#woo_usn_messages_to_send");
    var woo_usn_submit_sms = $("#woo_usn_sms_submit");
    var woo_usn_load_defaults_message = $(
      "input#woo_usn_load_default_messages"
    );
    var woo_usn_phone_numbers = $("textarea#phone_number");
    var woo_usn_order_status = woo_usn_phone_numbers.attr("order_status");
    var woo_usn_ajax_loading = $("div.woousn-cl-loader");
    var woo_usn_order_id = woo_usn_phone_numbers.attr("order_id");
    var woo_usn_return_modal = $("div.woo_usn_modal_body");
    var woo_usn_return_modal_status = $("div.woousn-body-cl-status");
    var woo_usn_btn_saving_creds = $("input#woo_usn_saving");
    var woo_usn_modal = $("div.woousn-cl-loader");
    var woo_usn_modal_status = $("div.woousn-cl-status");
    var woo_usn_api_choosed = $("select#woo_usn_api_to_choose").val();
    $("select#woo_usn_api_to_choose").change(function () {
      woo_usn_api_choosed = this.value;
    });
    var woo_usn_first_api_key;
    var woo_usn_second_api_key;
    var woo_usn_btn_deleting = $("input#woo_usn_deleting");

    var woousn_phone_number_validator = document.querySelector("#woo_usn_testing_numbers");  
    try{
      var iti =  window.intlTelInput(woousn_phone_number_validator,{
        initialCountry: "us",
        utilsScript : woo_usn_ajax_object.woo_usn_phone_utils_path
      });
    }  catch(e){
      //console.info(e);
    }

    $('span.woo-usn-qs-class.woo-usn-use-phone-number' ).show();


    // show/hide based on the API
    $("select#woo_usn_api_to_choose").change(function () {
      var api = $(this).children(":selected").val();
      $('div[id^="woo_usn_api"]').hide();
      $('div[id="woo_usn_api_'+api.replace(" ",'').toLowerCase()+'"]').show();
      wp.hooks.doAction( 'woo_usn_settings_api_choosed' , api );
    });

 
    $('div[id^="woo_usn_api"]').hide();
    $('div[id="woo_usn_api_'+woo_usn_ajax_object.woo_usn_sms_api_used.replace(" ",'').toLowerCase()+'"]').show();
    wp.hooks.doAction( 'woo_usn_settings_api_choosed' , woo_usn_ajax_object.woo_usn_sms_api_used );

    // count numbers of characters
    $(".woousn-textarea").on("keyup", function () {
      $("span.woousn-textcount").empty();
      var limit = $(this).empty().val().length;
      $(
        '<strong><span class="woousn-textcount" style="color : red;">' +
          limit +
          " characters typed </span></strong>"
      ).insertAfter($(this));
    });

    // submit sms from orders
    woo_usn_submit_sms.on("click", function (e) {
      e.preventDefault();
      var woo_usn_messages_to_send = $(
        "#woo_usn_messages_to_send.input-text "
      ).val();
      var woo_usn_phone_numbers = $("#woo_usn_phone_numbers").val();
      var data = {
        "messages-to-send": woo_usn_messages_to_send,
        "phone-number": woo_usn_phone_numbers,
        "order-id": woo_usn_order_id,
        "order-status": woo_usn_order_status,
      };
      woo_usn_ajax_loading.show();
      $.post(
        woo_usn_ajax_object.woo_usn_ajax_url,
        {
          action: "woo_usn_send-messages-manually-from-orders",
          data: data,
          security: woo_usn_ajax_object.woo_usn_ajax_security,
        },
        function (response) {
          woo_usn_ajax_loading.hide();
          $("textarea#phone_number").show().append(response);
        }
      );
      $("textarea#phone_number").hide().empty();
    });

    // test sms sending from general settings
    woo_usn_btn_testing.on("click submit", function (e) {
      e.preventDefault();

      // if( woo_usn_api_choosed == "SendChamp" ){
      //   if ( woo_usn_testing_numbers.slice(0, 1) == 0 ){
      //     woo_usn_testing_numbers = woo_usn_testing_numbers.slice(1).split(' ').join('')
      //   }
      // }
      //woo_usn_testing_numbers = countryData + woo_usn_testing_numbers;
    
      var woo_usn_testing_messages = $("textarea#woo_usn_testing_messages").val();

      var recipient_selection_mode = $('input[name="woo_usn_qs_pn"]:checked').val();
    
      // this code runs if you're trying to send custom sms.
      if ( 'use-phone-number' == recipient_selection_mode ) {
        var isValid = iti.isValidNumber();
        if ( !isValid ){
          woo_usn_return_modal_status
          .show().empty()
          .append("<strong>The phone number is not valid.</strong>");
          return;
        }
        var countryData = iti.getSelectedCountryData();
        countryData = countryData.dialCode;

        var woo_usn_testing_numbers = $(".woo-usn-testing-numbers").val();
        var data = {
          "testing-numbers": woo_usn_testing_numbers,
          "testing-messages": woo_usn_testing_messages,
          'country_code' : countryData,
        };
        woo_usn_modal_status.show();
        woo_usn_return_modal_status.empty();
        $.post(
          woo_usn_ajax_object.woo_usn_ajax_url,
          {
            action: "woo_usn-get-api-response-code",
            data: data,
            security: woo_usn_ajax_object.woo_usn_ajax_security,
          },
          function (response) {
            woo_usn_modal_status.hide();
            woo_usn_return_modal_status
              .show()
              .append("<strong>" + response + "</strong>");
          }
        );

      } else if ( 'use-contact-list' == recipient_selection_mode ) {
        var data = {
          "contact-list": $('select#woo_usn_qs_cl').val(),
          "testing-messages": woo_usn_testing_messages,
        };
        woo_usn_modal_status.show();
        woo_usn_return_modal_status.empty();
        $.post(
          woo_usn_ajax_object.woo_usn_ajax_url,
          {
            action: "woo_usn-send-sms-to-contacts",
            data: data,
            security: woo_usn_ajax_object.woo_usn_ajax_security,
          },
          function (response) {
            woo_usn_modal_status.hide();
            woo_usn_return_modal_status
              .show()
              .append("<strong>" + response + "</strong>");
          }
        );
      }

   
    });

    woo_usn_load_defaults_message.on("click submit", function (e) {
      e.preventDefault();
      woo_usn_ajax_loading.show();
      $.post(
        woo_usn_ajax_object.woo_usn_ajax_url,
        {
          action: "get-orders-defaults-messages",
          security: woo_usn_ajax_object.woo_usn_ajax_security,
        },
        function (response) {
          woo_usn_ajax_loading.hide();
          woo_usn_messages_fields.show().append(response);
        }
      );
      woo_usn_messages_fields.empty().hide();
    });

    //Saving the SMS API Key to the db.
    woo_usn_btn_saving_creds.on("click submit", function (e) {
      e.preventDefault();
      var data = {};
      var woo_usn_telesign_first_keys = $(
        'input[name="woo_usn_telesign_custom_id"]'
      );
      var woo_usn_telesign_second_keys = $(
        'input[name="woo_usn_telesign_api_key"]'
      );
      var woo_usn_twilio_first_keys = $(
        'input[name="woo_usn_twilio_account_sid"]'
      );
      var woo_usn_twilio_second_keys = $(
        'input[name="woo_usn_twilio_auth_token"]'
      );
      var woo_usn_twilio_number_from = $(
        'input[name="woo_usn_twilio_phone_number"]'
      );

      var woo_usn_twilio_whatsapp_number_from = $(
        'input[name="woo_usn_twilio_whatsapp_phone_number"]'
      );
      
      if (woo_usn_api_choosed == "Twilio") {
        woo_usn_first_api_key = woo_usn_twilio_first_keys.val();
        woo_usn_second_api_key = woo_usn_twilio_second_keys.val();
        data.woo_usn_twilio_phone_number = woo_usn_twilio_number_from.val();
      } else if (woo_usn_api_choosed == "Telesign") {
        woo_usn_first_api_key = woo_usn_telesign_first_keys.val();
        woo_usn_second_api_key = woo_usn_telesign_second_keys.val();
      } else if (woo_usn_api_choosed == "Kivalo"){
        woo_usn_first_api_key = $('input#woo_usn_kivalo_from_number').val();
        woo_usn_second_api_key = $('input#woo_usn_kivalo_api_key').val();
      } else if ( woo_usn_api_choosed == "WA API" ){
        woo_usn_first_api_key = $('input#woo_usn_waapi_client_id').val();
        woo_usn_second_api_key = $('input#woo_usn_waapi_instance_id').val();
        data.api_domain       = $('input#woo_usn_waapi_domain_url').val();
        data.whatsapp_phone_number = $('input#woo_usn_waapi_whatsapp_phone_number').val();
      } else if ( woo_usn_api_choosed == "Message Bird" ){
        woo_usn_first_api_key = $('input#woo_usn_message_bird_from_number').val();
        woo_usn_second_api_key = $('input#woo_usn_message_bird_api_key').val();
      } else if ( woo_usn_api_choosed == "SendChamp" ){
        woo_usn_first_api_key = $('input#woo_usn_sendchamp_from_number').val();
        woo_usn_second_api_key = $('input#woo_usn_sendchamp_api_key').val();
        data.api_domain       = $('input#woo_usn_sendchamp_domain_url').val();
      } else  if (woo_usn_api_choosed == "twilio_whatsapp") {
        woo_usn_first_api_key = woo_usn_twilio_first_keys.val();
        woo_usn_second_api_key = woo_usn_twilio_second_keys.val();
        data.woo_usn_twilio_whatsapp_phone_number = woo_usn_twilio_whatsapp_number_from.val();
      } else if ( woo_usn_api_choosed == "eBulkSMS"){
        woo_usn_first_api_key = $('input#woo_usn_ebulksms_username').val();
        woo_usn_second_api_key = $('input#woo_usn_ebulksms_api_key').val();
        data.woo_usn_ebulksms_phone_number = $('input#woo_usn_ebulksms_from_number').val();
      }
      data.api_choosed = woo_usn_api_choosed;
      data.first_api_key = woo_usn_first_api_key;
      data.second_api_key = woo_usn_second_api_key;
      data = wp.hooks.applyFilters( 'woo_usn_save_gateways_data',  data );
      woo_usn_return_modal.empty().hide();
      woo_usn_modal.show();
      $.post(
        woo_usn_ajax_object.woo_usn_ajax_url,
        {
          action: "woo_usn_save-api-credentials",
          data: data,
          security: woo_usn_ajax_object.woo_usn_ajax_security,
        },
        function (response) {
          woo_usn_modal.hide();
          var json_decode = JSON.parse(response);
          if (json_decode.status === 1) {
            response =
              "Congratulations the credentials have been saved.";
          } else {
            response =
              "Unfortunately your operation is not successfully.Please fill fields and try again! ";
          }
          woo_usn_return_modal
            .show()
            .append("<strong>" + response + "</strong>");
        }
      );
    });

    // delete SMS API from database
    woo_usn_btn_deleting.on("click", function (e) {
      e.preventDefault();
      var data = {
        api_choosed: woo_usn_api_choosed,
      };
      woo_usn_return_modal.empty().hide();
      woo_usn_modal.show();
      data = wp.hooks.applyFilters( 'woo_usn_delete_gateways_data',  data );
      $.post(
        woo_usn_ajax_object.woo_usn_ajax_url,
        {
          action: "woo_usn_delete-api-credentials",
          data: data,
          security: woo_usn_ajax_object.woo_usn_ajax_security,
        },
        function (response) {
          woo_usn_modal.hide();
          var json_decode = JSON.parse(response);
          if (json_decode.status === 1) {
            response =
              "Congratulations, you have successfully delete api credentials .";
          } else {
            response =
              "Unfortunately your operation is not successfully.Please try again!";
          }
          woo_usn_return_modal
            .show()
            .append("<strong>" + response + "</strong>");
        }
      );
    });

    //display settings updated
    var woo_usn_display_settings = function () {
      hs_toggle_display(
        'input[name="woo_usn_options[woo_usn_message_after_customer_purchase]"]',
        "tr.woo-usn-defaults-messages"
      );
      hs_toggle_display(
        'input[name="woo_usn_options[woo_usn_message_after_order_changed]"]',
        "tr.woo-usn-completed-messages"
      );
      hs_toggle_display(
        'input[name="woo_usn_options[woo_usn_message_after_order_changed]"]',
        "tr.woo-usn-completed-messages"
      );
      hs_toggle_display(
        'input[name="woo_usn_options[woo_usn_sms_to_admin]"]',
        "tr.woo-usn-admin-completed-messages"
      );
      hs_toggle_display(
        'input[name="woo_usn_options[woo_usn_sms_to_vendors]"]',
        "tr.woo-usn-vendor-completed-messages"
      );
      hs_toggle_display(
        'input[name="woo_usn_options[woo_usn_sms_consent]"]',
        "tr.woo-usn-customer-consent"
      );
      hs_toggle_display(
        'input[name="woo_usn_options[woo_usn_messages_after_customer_signup]"]',
        "tr.woo-usn-signup-defaults-messages"
      );
    };


    // show/hide fields from the settings page.
    woo_usn_display_settings();
    $("body").on("change", function () {
      woo_usn_display_settings();
    }); 
    $('div#wpfooter').hide();

    $('select#woo_usn_api_to_choose').select2();
    $('select#woo_usn_qs_cl').select2();

   
    $('input[name="woo_usn_qs_pn"]').on('change', function(){
      var recipient_selection_mode = $(this).val();
      $('span.woo-usn-qs-class').hide();
      $('span.woo-usn-qs-class.woo-usn-'+recipient_selection_mode ).show();
    });
    
    // whatsapp.
    var whatsapp_id_choosed = $("select#woo_usn_api_to_choose").val();
    $("div#woo_usn_whatsapp_api_"+whatsapp_id_choosed).show();

    $("select#woo_usn_api_to_choose").on('change', function(){
      $("div.woo_usn_wha_api").hide();
      whatsapp_id_choosed = $(this).val();
      $("div#woo_usn_whatsapp_api_"+whatsapp_id_choosed).show();
    });


    $("input#woo_usn_wha_creds_saving").on("click submit", function (e) {
      e.preventDefault();
      var data = $("form").serializeJSON();

      woo_usn_ajax_loading.show();

      $.post(
        woo_usn_ajax_object.woo_usn_ajax_url,
        {
          action: "woo_usn_save_whatsapp_creds",
          data: data,
          security: woo_usn_ajax_object.woo_usn_ajax_security,
        },
        function (response) {
          woo_usn_modal.hide();
          var json_decode = JSON.parse(response);
          woo_usn_ajax_loading.hide();
          if (json_decode.status == '1') {
            response =
              "Congratulations the credentials have been saved.";
          } else {
            response =
              "Unfortunately your operation is not successfully.Please fill fields and try again! ";
          }
          woo_usn_return_modal.show().html('').append("<strong>" + response + "</strong>");
        }
      );

    });

    $("input#woo_usn_wha_creds_deleting").on("click submit", function (e) {
      e.preventDefault();
      var data = $("form").serializeJSON();

      woo_usn_ajax_loading.show();

      $.post(
        woo_usn_ajax_object.woo_usn_ajax_url,
        {
          action: "woo_usn_delete_whatsapp_creds",
          data: data,
          security: woo_usn_ajax_object.woo_usn_ajax_security,
        },
        function (response) {
          woo_usn_modal.hide();
          var json_decode = JSON.parse(response);
          woo_usn_ajax_loading.hide();
          if (json_decode.status == '1') {
            response = "Congratulations the credentials have been deleted.";
          } else {
            response =
              "Unfortunately your operation is not successfully.Please fill fields and try again! ";
          }
          woo_usn_return_modal.show().html('').append("<strong>" + response + "</strong>");
          window.location.reload();
        }
      );

    });

  });
})(jQuery);
