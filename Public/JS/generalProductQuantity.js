function getQuantityInput(cartID) {
    return document.getElementById(`quantity-${cartID}`);
}
function getQuantity(cartID) {
    const quantityInput = getQuantityInput(cartID);
    //const coefficient = quantityInput.dataset.productcoefficient;
    return quantityInput.value;
}
function formatPrice(num){
    return num.toLocaleString('tr-TR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
function updateCartTotal(){
    const cartTotals = document.querySelectorAll(".cart-totals");
    const totalModel = cartTotals.length
    const totalQuantity = Array.from(document.querySelectorAll(".quantity-input.qty")).reduce((acc, el) => acc + parseInt(el.value), 0);

    let totalPrice = 0;
    let totalDiscount = 0;
    cartTotals.forEach((el) => {
        totalPrice += parseFloat(el.dataset.totalprice);
        totalDiscount += parseFloat(el.dataset.discountamount);
    });

    const cartSummary = document.querySelector(".cart-summary ul");
    if(totalDiscount === 0){
        cartSummary.innerHTML = `
            <li class="total-model">[_toplam_urun_modeli]: ${totalModel}</li>
            <li class="total-quantitiy">[_toplam_urun_adedi]: ${totalQuantity}</li>
            <li class="total-price">[_uyelik_sepettoplamtutar_yazi]: ${formatPrice(totalPrice)} ₺</li>
        `;
        return;
    }
    cartSummary.innerHTML = `
            <li class="total-model">[_toplam_urun_modeli]: ${totalModel}</li>
            <li class="total-quantitiy">[_toplam_urun_adedi]: ${totalQuantity}</li>
            <li class="total-price">[_uyelik_sepettoplamtutar_yazi] ${formatPrice(totalPrice)} ₺</li>
            <li class="total-discount">[_sepet_indirim_toplam_tutar_yazi] <i>${formatPrice(totalDiscount)} ₺</i></li>
            <li class="total-discounted-price">[_sepet_indiriml_toplam_tutar] ${formatPrice(totalPrice - totalDiscount)} ₺</li>
    `;

}
function updateCartUI(cartData,cartID){
    const cartTotals = document.querySelector("#cart-totals-"+cartID);

    var cartProductPrice = cartData.urunfiyat;

    var cartDiscountPrice = cartData.indirimmiktari*1;
    var cartDiscountDescription = cartData.indirimaciklamasi;
    var cartCurrencySymbol = cartData.parabirimsimge;
    var cartQuantity = getQuantity(cartID);


    var cartTotalPrice = cartProductPrice * cartQuantity;

    cartTotals.setAttribute("data-totalprice", cartTotalPrice);
    cartTotals.setAttribute("data-discountamount", cartDiscountPrice);

    var cartCartTotalHtml = `<span class="cart-item-total-price">${cartCurrencySymbol} ${formatPrice(cartTotalPrice)}</span>`;
    if(cartDiscountPrice > 0){
        cartCartTotalHtml += `<span class="cart-item-discount-description">${cartDiscountDescription}</span>`;
        cartCartTotalHtml += `<span class="cart-item-discount-amount">${cartCurrencySymbol} ${formatPrice(cartDiscountPrice)}</span>`;
        var cartDiscountedPrice = cartTotalPrice - cartDiscountPrice;
        cartCartTotalHtml += `<span class="cart-item-discounted-price"><i>${cartCurrencySymbol} ${formatPrice(cartDiscountedPrice)}</i></span>`;
    }
    cartTotals.innerHTML = cartCartTotalHtml;
    updateCartTotal();
}
function updateCart(cartID, quantity) {
    quantity = parseFloat(quantity);
    getQuantityInput(cartID).value = removeTrailingZeros(quantity.toFixed(4));
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/?/control/cart/post/update");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(`cartID=${cartID}&cartQuantity=${quantity}&action=update`);

    xhr.onload = function () {
        var status;
        var message;
        var cartData;
        if (xhr.status === 200) {
            //console.log(xhr.responseText);
            const response = JSON.parse(xhr.responseText);

            type = response.status;
            message = response.message;
            cartData = response.cartData;
            if(type !==""&& message !==""){
                showPopup(type, message);
            }
            if(cartData !==""){
                updateCartUI(cartData,cartID);
            }
        }
        else
        {
            showPopup("error", "Bir hata oluştu. Lütfen tekrar deneyin.");
        }
    };
}
const minusButtons = document.querySelectorAll(".qtyBtn.minus");
const plusButtons = document.querySelectorAll(".qtyBtn.plus");
const removeButtons = document.querySelectorAll(".removeMb");
const quantityInputs = document.querySelectorAll(".quantity-input.qty");
quantityInputs.forEach((quantityInput) => {
    quantityInput.addEventListener("change", function () {
        const cartID = this.id.split("-")[1];
        console.log("cartID: " + cartID);
        const value = parseFloat(this.value);
        const coefficient = parseFloat(this.dataset.productcoefficient);
        const min = parseFloat(this.min);
        const max = parseFloat(this.max);
        if (value < min) {
            this.value = min;
            document.getElementById("minus-"+cartID).disabled = true;
        }
        if (value > max) {
            this.value = max;
            document.getElementById("plus-"+cartID).disabled = true;
        }
        this.value = value + (coefficient -(value % coefficient));
        updateCart(cartID, this.value);
    });
});

for (const minusButton of minusButtons) {
    minusButton.addEventListener("click", function () {
        const cartID = this.dataset.cartid;
        const quantityInput = getQuantityInput(cartID);
        const coefficient = parseFloat(quantityInput.dataset.productcoefficient);
        /*console.log("coefficient: " + coefficient);*/

        let value = parseFloat(quantityInput.value); // Ürünün gerçek değerini alır

        value -= coefficient; // Katsayı ile eksiltme işlemi yapar

        // Min sınırını kontrol eder
        if (value < quantityInput.min) {
            value = quantityInput.min;
            document.getElementById("minus-"+cartID).disabled = true;
        }
        document.getElementById("plus-"+cartID).disabled = false;
        updateCart(cartID, value);
    });
}
for (const plusButton of plusButtons) {
    plusButton.addEventListener("click", function () {
        const cartID = this.dataset.cartid;
        const quantityInput = getQuantityInput(cartID);
        const coefficient = parseFloat(quantityInput.dataset.productcoefficient);
        /*console.log("coefficient: " + coefficient);*/

        let value = parseFloat(quantityInput.value); // Ürünün gerçek değerini alır
        /*console.log("value: " + value);*/

        value += coefficient; // Katsayı ile arttırma işlemi yapar
        /*console.log("value: " + value);*/
        // Max sınırını kontrol eder
        if (value > quantityInput.max) {
            value = quantityInput.max;
            document.getElementById("plus-"+cartID).disabled = true;
        }
        document.getElementById("minus-"+cartID).disabled = false;
        updateCart(cartID, value);
    });
}

for (const removeButton of removeButtons) {
    removeButton.addEventListener("click", function () {
        const cartID = this.id.split("-")[1];
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/?/control/cart/post/remove");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.send(`cartID=${cartID}&action=remove`);

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                showPopup(response.status, response.message);
                if(response.status === "success"){
                    document.getElementById("cart-item-"+cartID).remove();
                    updateCartTotal();
                }
            }
            else
            {
                showPopup("error", "Bir hata oluştu. Lütfen tekrar deneyin.");
            }
        };
    });
}