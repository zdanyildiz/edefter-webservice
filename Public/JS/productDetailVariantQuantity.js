class ProductQuantity {
    maxQuantity;
    constructor(minQuantity, incrementQuantity, price,currencyCode,discount,campaignData ) {
        //console.log('minQuantity: ' + minQuantity);
        minQuantity = parseFloat(removeTrailingZeros(minQuantity));
        this.maxQuantity = 1;
        incrementQuantity = parseFloat(removeTrailingZeros(incrementQuantity));
        price = price.replace('.', '');
        price = price.replace(',', '.');

        //console.log('minQuantity: ' + minQuantity);
        this.minQuantity = minQuantity;
        this.incrementQuantity = incrementQuantity;
        this.quantity = minQuantity;
        this.price = price;
        this.currencyCode=currencyCode;
        this.totalPriceContainer = document.querySelector('.product-total-price-container');
        this.productQuantityInput = document.getElementById('product-quantity');
        //#product-quantity inputunun data-max değerine göre alacağız
        this.maxQuantity = this.productQuantityInput.getAttribute('data-max');
        this.discount = discount;
        this.campaignData = campaignData;
    }

    increment() {
        var value = parseFloat(this.productQuantityInput.value);
        if (isNaN(value)) {
            this.productQuantityInput.value = this.minQuantity;
        } else {
            this.quantity = value + this.incrementQuantity;
            if(this.quantity > this.maxQuantity)
            {
                this.quantity = this.maxQuantity;
            }
        }
        this.quantity = parseFloat(this.quantity);
    }

    decrement() {
        var value = parseFloat(this.productQuantityInput.value);
        if (isNaN(value)) {
            this.productQuantityInput.value = this.minQuantity;
        } else {
            this.quantity = value - this.incrementQuantity;
        }

        if (this.quantity <= this.minQuantity) {
            this.quantity = this.minQuantity;
        }
        this.quantity = parseFloat(this.quantity);
    }

    validate() {
        var value = parseFloat(this.productQuantityInput.value);

        if (value < this.minQuantity) {
            return false;
        }
        return true;
    }
    
    updatePrice() {
        this.price = document.getElementById('productPriceInput').value;

        if (this.validate()) {
            console.log('price: ' + this.price);
            //this.price = this.price.replace('.', '');
            //console.log('price: ' + this.price);
            //this.price = this.price.replace(',', '.');
            //console.log('price: ' + this.price);
            let totalPrice = parseFloat((this.quantity * this.price).toFixed(2));
            console.log('totalPrice: ' + totalPrice);
            let discount = 0;
            let campaignDiscount = this.getCampaignDiscount(totalPrice);

            // Her iki indirim de varsa (this.discount === 1 ve campaignDiscount), daha yüksek olanını uygula
            discount = Math.max(discount, campaignDiscount);

            // İndirim tutarını ve indirimli fiyatı hesaplayın
            discount = parseFloat(discount.toFixed(2));
            let discountedPrice = totalPrice - discount;

            discountedPrice = formatPrice(discountedPrice);

            totalPrice = formatPrice(totalPrice);

            let priceContent = '';
            if (discount > 0) { // İndirim varsa göster
                priceContent =
                    '<span class="totalPrice old">[_uyelik_sepettoplamtutar_yazi] ' + this.currencyCode + ' ' + totalPrice.toLocaleString() + '</span> ' +
                    '<span class="discount">[_sepet_indirim_tutar_yazi] ' + this.currencyCode + ' ' + discount.toLocaleString() + '</span> ' +
                    '<span class="discountedPrice">[_sepet_indiriml_toplam_tutar] ' + this.currencyCode + ' ' + discountedPrice.toLocaleString() + '</span> ';
            } else {
                // İndirimsiz fiyat gösterimi
                priceContent = '<span class="totalPrice">' + this.currencyCode + ' ' + totalPrice.toLocaleString() + '</span>';
            }

            this.totalPriceContainer.innerHTML = priceContent;
            console.log(typeof this.quantity);
            console.log(this.quantity);
            this.productQuantityInput.value = removeTrailingZeros(this.quantity.toFixed(4));
        } else {
            let totalPrice = (this.minQuantity * this.price).toFixed(2);
            this.totalPriceContainer.innerHTML = totalPrice.toLocaleString() + ' ' + this.currencyCode;
            this.productQuantityInput.value = this.minQuantity;
        }
    }

    // Kampanya indirimini hesaplamak için yardımcı fonksiyon
    getCampaignDiscount(totalPrice) {
        // Miktar sınırlarının bulunduğu bir array oluşturun
        const campaignQuantityLimits = Object.keys(this.campaignData).sort((a, b) => b - a);

        // Miktar sınırları arasında döngü yapın. Miktar, ilgili sınırlar içinde ise indirim hesaplanır
        for (const quantityLimit of campaignQuantityLimits) {
            if (this.quantity >= quantityLimit) {
                return totalPrice * this.campaignData[quantityLimit];
            }
        }

        // Hiçbir miktar sınırına girilmediyse indirim yok
        return 0;
    }

}
// increment button click
let productQuantityIncrementButton = document.querySelector('.product-quantity-increment-button');
if(productQuantityIncrementButton) {
    document.querySelector('.product-quantity-increment-button').addEventListener('click', function () {
        productQuantity.increment();
        productQuantity.updatePrice();
    });
}
// decrement button click
let productQuantityDecrementButton = document.querySelector('.product-quantity-decrement-button');
if(productQuantityDecrementButton) {
    document.querySelector('.product-quantity-decrement-button').addEventListener('click', function () {
        productQuantity.decrement();
        productQuantity.updatePrice();
    });
}
// quantity input change
let productQuantityInput = document.querySelector('#product-quantity');
if(productQuantityInput) {
    document.querySelector('#product-quantity').addEventListener('change', function () {
        let thisValue = parseFloat(this.value);
        if (isNaN(thisValue)) {
            thisValue = productQuantity.incrementQuantity
            productQuantity.quantity = thisValue;
        }
        productQuantity.quantity = thisValue + (productQuantity.incrementQuantity -(thisValue % productQuantity.incrementQuantity));
        productQuantity.updatePrice();
    });
}