;(function ($) {
    var optionsTrigger = $('select.product-options'),
        quantityTrigger = $('button.changeAmount'),
        calculationFields = [
            'select.product-options',
            'input[name="price"]',
            'input[name="h:product_id"]'
        ],
        getVisibleQuantityField = function (element) {
            return (element !== undefined)
                ? element.parent().parent().find("input[name='x:visibleQuantity']")
                : $('#amountPlus').parent().parent().find("input[name='x:visibleQuantity']");
        };

    optionsTrigger.bind('change', function () {
        fetchDiscountPrice();
    });

    quantityTrigger.bind('click', function () {
        fetchDiscountPrice($(this));
    });

    function fetchDiscountPrice(element) {
        var data = {};

        $.each(calculationFields, function (k, v) {
            var inputField = $(v),
                name = undefined;

            if (inputField.is('input')) {
                name = inputField.attr('name');
                data[name] = inputField.val();
            } else if (inputField.is('select')) {
                name = inputField.attr('name');
                data[name] = inputField.find(':selected').val();
            }//*/
        });
        data.isAjax = 1;
        data['x:visibleQuantity'] = (element !== undefined)
        ? getVisibleQuantityField(element).val()
        : getVisibleQuantityField().val();

        console.log(data);

        $.ajax({
            type: 'GET',
            url: window.location.href + '/fetchprice/',
            data: data
        }).success(function (response) {
            $('.standard-price').html('$' + response);
        });
    }
}(jQuery));
