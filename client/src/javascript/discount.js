;(function ($) {
    MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

    var trackChange = function (element) {
        var observer = new MutationObserver(function (mutations, observer) {
            if (mutations[0].attributeName == "value") {
                $(element).trigger("change");
            }
        });
        observer.observe(element, {
            attributes: true
        });
    };

    var optionsTrigger = $('.product-options'),
        trackQuantity = $('input[type="hidden"][name="quantity"]'),
        calculationFields = [
            'select.product-options',
            'input[name="price"]',
            'input[name="h:product_id"]'
        ],
        getQuantityValue = function () {
            return trackQuantity.val().split('||')[0];
        };

    $.each(optionsTrigger, function () {
        trackChange($(this)[0]);
    });
    optionsTrigger.on('change', function () {
        fetchDiscountPrice();
    });

    trackChange(trackQuantity[0]);
    trackQuantity.on('change', function (event) {
        fetchDiscountPrice();
    });

    function fetchDiscountPrice() {
        var data = {};

        $.each(calculationFields, function (k, v) {
            var inputField = $(v),
                name;

            if (inputField.is('input')) {
                name = inputField.attr('name');
                data[name] = inputField.val();
            } else if (inputField.is('select')) {
                name = inputField.attr('name');
                data[name] = inputField.find(':selected').val();
            }//*/
        });
        data.isAjax = 1;
        data.quantity = getQuantityValue();

        $.ajax({
            type: 'GET',
            url: window.location.href + 'fetchprice/',
            data: data
        }).success(function (response) {
            $('.standard-price').html('$' + parseFloat(response).toFixed(2));
        });
    }
}(jQuery));
