var checkboxes = document.querySelectorAll('input[name="cartItem[]"]');

checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        var cartUniqID = this.dataset.cartuniqid;
        var cartItem = document.getElementById('cart-item-' + cartUniqID);

        if (this.checked) {
            cartItem.classList.add('cart-checked');
        } else {
            cartItem.classList.remove('cart-checked');
        }

        // Sepet özeti güncelle
        updateCartSummary();
    });
});

function updateCartSummary() {
    var totalModels = 0;
    var totalQuantity = 0;
    var totalPrice = 0;
    var totalDiscount = 0;
    var currencySymbol = '';

    var cartItems = document.querySelectorAll('.cart-checked');
    cartItems.forEach(function (cartItem) {
        var cartTotals = cartItem.querySelector('.cart-totals');
        var quantity = parseFloat(cartTotals.dataset.quantity);
        var price = parseFloat(cartTotals.dataset.totalprice);
        var discount = parseFloat(cartTotals.dataset.discountamount);
        currencySymbol = cartTotals.dataset.currencysymbol;

        totalModels += 1;
        totalQuantity += quantity;
        totalPrice += price;
        totalDiscount += discount;
    });

    var cartSummary = document.querySelector('.cart-summary');
    cartSummary.querySelector('.total-model').textContent = 'Toplam Ürün Modeli: ' + totalModels;
    cartSummary.querySelector('.total-quantity').textContent = 'Toplam Ürün Adeti: ' + totalQuantity;
    cartSummary.querySelector('.total-price').textContent = 'Toplam Tutar: ' + formatPrice(totalPrice) + ' ' + currencySymbol;


    var totalDiscountDiv= document.querySelector('.cart-summary .total-discount');
    //totalDiscountDiv varsa
    if (totalDiscountDiv) {
        totalDiscountDiv.textContent = 'Toplam İndirim Tutarı: ' + formatPrice(totalDiscount) + ' ' + currencySymbol;
        var totalDiscountedPriceDiv= document.querySelector('.cart-summary .total-discounted-price');
        totalDiscountedPriceDiv.textContent = 'Toplam İndirimli Tutar: ' + formatPrice((totalPrice - totalDiscount)) + ' ' + currencySymbol;
    }


    if (totalDiscount > 0) {
        totalDiscountDiv.style.display = 'block';
        totalDiscountedPriceDiv.style.display = 'block';
    } else {
        if (totalDiscountDiv) {
            totalDiscountDiv.style.display = 'none';
            totalDiscountedPriceDiv.style.display = 'none';
        }
    }
}