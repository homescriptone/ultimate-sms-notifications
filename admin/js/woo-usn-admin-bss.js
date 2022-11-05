(function ($) {
    'use strict';

    var woo_usn_filter = function( input, table ){
        var filter, tr, td, i, txtValue;
        filter = input.value.toUpperCase();
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0] ;
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    td = tr[i].getElementsByTagName("td")[1] ;
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        }else{
                            tr[i].style.display = "none";
                        }
                    }
                }
            }
        }
    }

    $(document).ready(function(){
        $(document).on('keyup', '#woo-usn-filter-input', function(){
            var input = document.getElementById("woo-usn-filter-input");
            var table = document.getElementById("woo-usn-bs-tables");
            woo_usn_filter( input, table );
            if ( $('#woo-usn-filter-input').val() == "" ){
                $('a[data-page="0"]').click()
            } 
        });
          
        $(document).on('click dbclick submit', 'button.woo-usn-bs-details', function(e){
            e.preventDefault();
            $.blockUI({'message' : 'Loading .... '});
            $.post(woo_usn_ajax_object.woo_usn_ajax_url, {
                action : 'stored-bulk-sms',
                order_id : $(this).data('analyticId'),
                bulk_id : woo_usn_ajax_object.bs_id,
                bss_id : woo_usn_ajax_object.bss_id
            }, function(response){
                $.unblockUI();
                $('div.modal').empty().append(response);
                $('div.modal').modal();
                $('#woo-usn-bss-results-tables').paging({limit:6, activePage : 1});
                $('a[data-page="0"]').click();
            });
        });
        
        $('#woo-usn-bs-tables').paging({limit:6, activePage : 1});
        $('a[data-page="0"]').click()
    });
})(jQuery);

