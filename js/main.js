$(document).ready(function(){
	$('.datepicker').datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "-80:+0",
		dateFormat: 'dd-mm-yy'
	});

    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            var focused = $(':focus');
            var value = focused.val();


            //sales page
            if (focused.attr('name')=='sale_medicine_code'){
                //event.preventDefault();
                //return false;
            }
            //medicine inpute page
            else if ( value.length>12 && focused.attr('name')!='medicine_code' && focused.attr('name')!='medicine_name'){
                event.preventDefault();
                return false;
            }
        }
    });
    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $('.sales_page input[name="sale_medicine_name"]').on('keyup', function(e) {
        if ($(this).val().length > 3){
            var thisInput = $(this);
            delay(function(){
                $.ajax ({
                    url: '?request=ajax',
                    type: "POST",
                    data: {
                        sale_medicine_name:thisInput.val()
                    },
                    dataType: "json",
                    success: function(data){
                        $('.medicine_list_container .data').remove();
                        $.each( data, function( key, value ) {
                            $('.medicine_list_container table tbody').append(`
                    <tr class="data">
                        <td><a href="?medicine_id=`+value.medicine_id+`">`+value.medicine_code+`</a></td>
                        <td><a href="?medicine_id=`+value.medicine_id+`">`+value.medicine_name+`</a></td>
                        <td><a href="?medicine_id=`+value.medicine_id+`">`+value.medicine_manufacture_name+`</a></td>
                    </tr>
                            `);
                        });

                        $('.medicine_list_container').fadeIn();
                    },
                    error : function (response, status) {
                        console.log('ERROR:' + response+'::'+status);
                    }
                });
            }, 2000 );
        }
        else if ($(this).val().length ==0){
            $('input[name=sale_medicine_name]').val('');
            $('input[name=sale_medicine_code]').val('');
            $('input[name=sale_medicine_id]').val('');
            $('input[name=sale_quantity]').val('');
            $('input[name=sale_price]').val('');

            $('.medicine_list_container').fadeOut('600', function(){
                $('.medicine_list_container .data').remove();
            });
        }
    });
    $('.sales_page input[name=sale_quantity], .sales_page input[name=sale_payment], .sales_page input[name=sale_exchange_rate], .sales_page .balance_exchange').keyup(function(){
        calculateSaleData();
    });

    $('input[name=sale_different_currency], .sales_page .balance_checkbox').click(function(){
        calculateSaleData();
    });

    //cleint: edit balance
    $('body').on('click', '#myModal .yes-button', function(){
        $('.client-balance input').removeAttr('disabled');
    });
    $('body').on('click', '#myModal .no-button', function(){
        
    })

    //save payment
    $('.payment_page input[name=set_balance]').click(function(){
        if($('.payment_page input[name=exchange_checkbox]').is(":checked")) {
            if ($('.payment_page select[name=payment_currency_id]').val()=='' || $('.payment_page input[name=payment]').val()=='' || $('.payment_page select[name=exchange_rate]').val()=='' || $('.payment_page input[name=exchange_currency_id]').val()==''){
                alert('Пожалюйста, выберите валюту, сумму оплаты, курс обмена и валюта обмена');
                return false;
            }
        }
        else{
            if ($('.payment_page select[name=payment_currency_id] option:selected').val()=='' || $('.payment_page input[name=payment]').val()==''){
                alert('Пожалюйста, выберите валюту и сумму оплаты');
                return false;
            }
        }
    });

    //exchange rate
    $('.payment_page input[name=exchange_checkbox]').change(function() {
        if($(this).is(":checked")) {
            $('.payment_page .exchange_currency').show();
        }
        else{
            $('.payment_page .exchange_currency').hide();
        }
    });
});

function calculateSaleData()
{
    $('.label-danger').remove();
    var quantity = $('.sales_page input[name=sale_quantity]');
    var price = $('.sales_page input[name=sale_price]');
    var payment = $('input[name=sale_payment]');
    var balance = 0;
    var debt = 0;
    if (quantity.val()>parseInt($('.max_quantity').html()) || parseInt($('.max_quantity').html())==0){
        quantity.after('<span class="label label-danger">Количество не может превышать количество товара в складе</span>');
    }
    else{
        $('.balance_checkbox').each(function(){
            if ($(this).is(":checked")){
                var currencyID = $(this).attr('class').substr(-1,1);
                balance += $('.balance_currency_id_'+currencyID+' td:nth-child(1) span').html() * $('.balance_exchange_currency_id_'+currencyID).val();
                debt += $('.balance_currency_id_'+currencyID+' td:nth-child(2) span').html() * $('.balance_exchange_currency_id_'+currencyID).val();
            }
        });
        console.log('b:'+balance+' d:'+debt);
        //different Currency
        if ($('.sales_page input[name=sale_different_currency]').is(":checked")){
            var exchange_rate = $('.sales_page input[name=sale_exchange_rate]');
            console.log('different q:'+quantity.val()+' p:'+price.val()+' er:'+exchange_rate.val());
            $('input[name=sale_full_price]').val(quantity.val() * price.val());
            $('input[name=sale_residue]').val((quantity.val() * price.val()*exchange_rate.val()) - - payment.val()*exchange_rate.val() );
            $('input[name=sale_total_residue]').val(((quantity.val() * price.val()) - payment.val()*exchange_rate.val() - balance + debt)*(-1));
        }
        else{
            console.log('same q:'+quantity.val()+' p:'+price.val());
            $('input[name=sale_full_price]').val(quantity.val() * price.val());
            $('input[name=sale_residue]').val((quantity.val() * price.val()) - payment.val() );
            $('input[name=sale_total_residue]').val(((quantity.val() * price.val()) - payment.val() - balance + debt)*(-1));
        }
    }
}

function removeTableRow(e)
{
    $(e).parents('tr').remove();
}

function addCustomFieldRow(e)
{
    $(e).parents('tr').before('<tr><th><input type="text" name="custom_field_name[]" placeholder="Название Поля"/></th><td><input type="text" name="custom_field_value[]" placeholder="Значение Поля"/>&nbsp;<a href="javascript:void(0)" class="btn btn-danger" onclick="removeTableRow(this)">Удалить</a></td></tr>');
}

function paymentPaid(e)
{
    var parameters = {
        'sale_id':$(e).parents('tr').find('input[name=sale_id]').val(),
        'sale_medicine_id':$(e).parents('tr').find('input[name=sale_medicine_id]').val(),
        'action':'payment_paid'
    };
    $.post(
        '?request=ajax',
        {
            data:parameters
        },
        function(data){
            console.log(data);
            if (data=='save'){
                $('form').submit();
            }
        }
    ).fail(function(data){
        alert("error"+JSON.stringify(data, 4));
    });
}

function activateReturn(e)
{
    $(e).parents('tr').find(' input[type=text]').removeAttr('disabled');
    $(e).parents('tr').find('.save_return_button, .cancel_return_button').show();
    $(e).parents('tr').find('.payment_button').hide();
    $(e).hide();
}

function saveReturn(e)
{
    var parameters = {
        'sale_id':$(e).parents('tr').find('input[name=sale_id]').val(),
        'good_quantity':$(e).parents('tr').find('input[name=quantity]').val(),
        'sale_medicine_id':$(e).parents('tr').find('input[name=sale_medicine_id]').val(),
        'action':'good_returned'
    };
    $.post(
        '?request=ajax',
        {
            data:parameters
        },
        function(data){
            alert(data);
            if (data=='returned' || data=='Free item returned'){
                $('form').submit();
            }
        }
    ).fail(function(data){
            alert("error"+JSON.stringify(data, 4));
        });
}

function cancelReturn(e)
{
    $(e).parents('tr').find(' input[type=text]').attr('disabled',true);
    $(e).parents('tr').find('.save_return_button, .cancel_return_button').hide();
    $(e).parents('tr').find('.return_button,.payment_button').show();
}

var makeBalanceEditable = false;

function editableBalance(){
    if (makeBalanceEditable == false){
        $('.popup-button').trigger('click');
    }

}