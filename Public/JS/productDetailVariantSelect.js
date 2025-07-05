/**
 * replaceTurkishChars Fonksiyonu: Bu fonksiyon, Türkçe karakterleri İngilizce karakterlere dönüştürür.
 * replaceSpecialChars Fonksiyonu: Bu fonksiyon, metindeki özel karakterleri alt çizgi ile değiştirir.
 * formElementStatus Fonksiyonu: Bu fonksiyon, belirli form elementlerinin durumunu değiştirir.
 *
 * ProductVariantSelector Sınıfı: Bu sınıf, ürün varyantlarını seçmek için kullanılır.
 *
 * constructor Metodu: Bu metod, sınıfın yapıcı fonksiyonudur. Ürün varyantlarını alır ve gerekli özellikleri ayarlar.
 * VariantGroups Metodu: Bu metod, varyant grup isimlerini alır (Ölçü, renk, beden gibi).
 * filterVariants Metodu: Bu metod, seçimlere göre varyantları filtreler.
 * createSelectBox Metodu: Bu metod, belirli bir varyant grubu için bir selectbox oluşturur.
 * lastChoice Metodu: Bu metod, son varyant seçildiğinde çalışır ve gerekli işlemleri gerçekleştirir.
 */
function replaceTurkishChars(text) {
    var trChars = { "ş": "s", "Ş": "S", "ı": "i", "İ": "I", "ç": "c", "Ç": "C", "ü": "u", "Ü": "U", "ö": "o", "Ö": "O", "ğ": "g", "Ğ": "G" };
    return text.replace(/[şŞıİçÇüÜöÖğĞ]/g, function (char) {
        return trChars[char];
    });
}
function replaceSpecialChars(text) {
    var specialChars = /['"`\\]/g;
    return text.replace(specialChars, '_');
}
function formElementStatus($disable=true){
    let productQuantity = document.getElementById('product-quantity');
    if(!productQuantity){
        return;
    }
    document.getElementById('product-quantity').disabled=$disable;
    document.querySelector('.product-quantity-decrement-button').disabled=$disable;
    document.querySelector('.product-quantity-increment-button').disabled=$disable;
    document.querySelector('#addToCartButton').disabled=$disable;
    document.querySelector('#checkoutButton').disabled=$disable;
    //$disable değişkeni true ise .disabled class'ı atayalım değilse class'ı silelim
    if($disable) {
        document.getElementById('product-quantity').classList.add('disabled');
        document.querySelector('.product-quantity-decrement-button').classList.add('disabled');
        document.querySelector('.product-quantity-increment-button').classList.add('disabled');
        document.querySelector('#addToCartButton').classList.add('disabled');
        document.querySelector('#checkoutButton').classList.add('disabled');
    }
    else {
        document.getElementById('product-quantity').classList.remove('disabled');
        document.querySelector('.product-quantity-decrement-button').classList.remove('disabled');
        document.querySelector('.product-quantity-increment-button').classList.remove('disabled');
        document.querySelector('#addToCartButton').classList.remove('disabled');
        document.querySelector('#checkoutButton').classList.remove('disabled');
    }
}
class ProductVariantSelector {
    constructor(data) {
        //console.log("ProductVariantSelector çalıştı");
        this.variants = data;

        if (this.variants[0].variantProperties.length === 0) {
            this.price = document.getElementById('productPriceInput').value;
            //console.log(this.price);
            //console.log("variant yok");
            if (this.price === '0,00') {
                formElementStatus(true);
                return null;
            }
            else {
                formElementStatus(false);
            }
            return null;
        }

        this.filterVariantGroups = this.VariantGroups(data);
        //console.log(this.filterVariantGroups);
        this.firstVariantGroup = this.filterVariantGroups[0];
        //console.log(this.firstVariantGroup);
        this.lastVariantGroup = this.filterVariantGroups[this.filterVariantGroups.length-1];
        //console.log(this.lastVariantGroup);return;
        this.selections = [];

        this.divProductVariantSelect = document.getElementById('productVariantSelect');
        this.createSelectBox(this.firstVariantGroup,this.variants);


        //varyant özellikleri attribute.value olarak saklanıyor
    }
    //varyant grup isimlerini alıyoruz. Ölçü, renk, beden gibi
    VariantGroups(variants) {
        const variantGroups = [];
        for (const variant of variants) {
            const variantProperties = variant.variantProperties;
            for (const attribute of variantProperties) {
                const variantGroupName = attribute.attribute.name;
                if (!variantGroups.includes(variantGroupName)) {
                    variantGroups.push(variantGroupName);
                }
            }
        }
        return variantGroups;
    }

    filterVariants(variantGroup, selections) {
        let filteredVariants = this.variants;
        for (const [key, value] of Object.entries(selections)) {
            filteredVariants = filteredVariants.filter(variant => variant.variantProperties.find(property => property.attribute.name === key && property.attribute.value === value));
        }
        return filteredVariants.filter(variant => variant.variantProperties.some(property => property.attribute.name === variantGroup));
    }

    //varyant gruplarını gönderdiğimiz grup ismine göre selectbox olarak oluşturuyoruz
    createSelectBox(variantGroup, variants) {
        formElementStatus(true);
        const selectBoxId = replaceTurkishChars(variantGroup);
        const selectBoxLabel = document.createElement('label');
        selectBoxLabel.setAttribute('for', selectBoxId);
        selectBoxLabel.setAttribute('class', 'triangle-right left');
        selectBoxLabel.innerText = variantGroup;

        const selectBox = document.createElement('select');
        selectBox.setAttribute('id', selectBoxId);

        const defaultOption = document.createElement('option');
        defaultOption.setAttribute('value', '');
        defaultOption.setAttribute('selected', 'selected');
        defaultOption.innerText = ` ${variantGroup} `;
        selectBox.appendChild(defaultOption);

        const variantValues = [];
        for (const variant of variants) {
            const variantProperties = variant.variantProperties;
            for (const attribute of variantProperties) {
                if (attribute.attribute.name === variantGroup) {
                    if (!variantValues.includes(attribute.attribute.value)) {
                        variantValues.push(attribute.attribute.value);
                        defaultOption.setAttribute('value', attribute.attribute.value);
                        const option = document.createElement('option');
                        option.setAttribute('value', attribute.attribute.value);
                        option.innerText = attribute.attribute.value;
                        selectBox.appendChild(option);
                    }
                }
            }
        }

        this.divProductVariantSelect.appendChild(selectBoxLabel);
        this.divProductVariantSelect.appendChild(selectBox);

        selectBox.addEventListener('change', (event) => {

            formElementStatus(true);
            this.selections[variantGroup] = event.target.value;

            const selectedIndex = this.filterVariantGroups.indexOf(variantGroup);
            //console.log("selectedIndex: "+selectedIndex);
            const nextVariantGroup = this.filterVariantGroups[selectedIndex + 1];

            // Eğer ilk varyantın seçimi değiştirildiyse, sonraki tüm selectbox'ları temizle ve yeniden oluştur
            if (variantGroup === this.firstVariantGroup) {

                //console.log("ilk seçim değişti");
                var selectedVariantGroup=variantGroup;

                this.divProductVariantSelect.innerHTML = '';
                new ProductVariantSelector(this.variants);

                //ilk varyant grubu oluşturduk, seçilen varyant grubunu seçili hale getirelim
                const selectBox = document.getElementById(selectBoxId);
                selectBox.value = this.selections[selectedVariantGroup];
            }

            if (nextVariantGroup) {
                const nextSelectBoxId = replaceTurkishChars(nextVariantGroup);

                var listRemove=0;
                this.filterVariantGroups.forEach(function(variantGroupName) {
                    var variantGroupID=replaceTurkishChars(variantGroupName);
                    if(document.getElementById(variantGroupID)){
                        if(listRemove > 0 && variantGroupID!==nextSelectBoxId ) {
                            //console.log("deleted: " + variantGroupID)
                            document.getElementById(variantGroupID).remove();
                            const label = document.querySelector(`label[for=${variantGroupID}]`);
                            if(label){
                                label.remove();
                            }
                        }
                    }

                    if (nextSelectBoxId === variantGroupID) {
                        const elementToRemove = document.getElementById(variantGroupID);
                        if (elementToRemove !== null) {
                            elementToRemove.remove();

                            const label = document.querySelector(`label[for=${variantGroupID}]`);
                            if(label){
                                label.remove();
                            }
                        }

                        //console.log("silme döngüsü sonrakiler silinecek: " + variantGroupID)
                        listRemove++;
                    }
                });

                const nextSelectBox = document.getElementById(nextSelectBoxId);


                if (nextSelectBox !== null) {
                    nextSelectBox.innerHTML = '';
                    const defaultOption = document.createElement('option');
                    defaultOption.setAttribute('value', '');
                    defaultOption.innerText = `Lütfen  ${nextVariantGroup} seçin`;
                    nextSelectBox.appendChild(defaultOption);
                }

                const nextVariants = this.filterVariants(nextVariantGroup, this.selections);
                if (nextVariants.length > 0) {
                    this.createSelectBox(nextVariantGroup, nextVariants);
                    // #productVariantSelect altındaki tüm labelları silelim
                    const labels = document.querySelectorAll('#productVariantSelect label');
                    // label.foreach içinde label var mı kontrol edip silelim
                    labels.forEach(function(label) {
                        if(label){
                            label.remove();
                        }
                    });


                }
                else {
                    if(this.filterVariantGroups[selectedIndex]==this.lastVariantGroup){
                        this.lastChoice();
                    }
                }
            }
            else {
                //console.log("select: "+this.filterVariantGroups[selectedIndex]);
                this.lastChoice();
            }

        });
    }

    lastChoice(){
        //console.log("lastChoice çalıştı");
        formElementStatus(false);
        const filteredVariants = this.variants.filter(variant => {
            const attributes = variant.variantProperties;
            const matchingAttributes = attributes.filter(attribute => {
                return this.selections[attribute.attribute.name] === attribute.attribute.value;
            });
            return matchingAttributes.length === attributes.length;
        });
        if (filteredVariants.length > 0) {
            console.log(filteredVariants[0]);
            var variantName = filteredVariants[0].variantName;
            var variantCurrencySymbole = filteredVariants[0].variantCurrencySymbol;
            var variantSellingPrice = filteredVariants[0].variantSellingPrice;
            if(variantSellingPrice===0) variantSellingPrice='0.00';

            // Türk formatını JavaScript'in anlayacağı formata dönüştür
            variantSellingPrice = variantSellingPrice.replace(/\./g, '').replace(',', '.');
            variantSellingPrice = parseFloat(variantSellingPrice);

            var variantStockCode = filteredVariants[0].variantStockCode;
            console.log("variantStockCode: " + variantStockCode);

            document.getElementById('product-subtitle').innerHTML = variantName;
            document.getElementById('productStockCode').innerHTML = variantStockCode;

            // Fiyatı ekrana formatlayarak yazdır
            let productSellingPrice = document.getElementById('productSellingPrice');
            if(productSellingPrice) {
                document.getElementById('productSellingPrice').innerHTML = variantCurrencySymbole + ' ' + formatPrice(variantSellingPrice);

                if (variantSellingPrice !== '0.00' && variantSellingPrice !== 0) {
                    console.log("variantSellingPrice: " + variantSellingPrice);
                    var variantPriceWithoutDiscount = filteredVariants[0].variantPriceWithoutDiscount;
                    console.log("variantPriceWithoutDiscount: " + variantPriceWithoutDiscount);

                    var variantDiscountRate = filteredVariants[0].variantDiscountRate;
                    console.log("variantDiscountRate: " + variantDiscountRate);
                    var variantQuantity = filteredVariants[0].variantQuantity;
                    console.log("variantQuantity: " + variantQuantity);



                    var variantMinQuantity = removeTrailingZeros(filteredVariants[0].variantMinQuantity);
                    console.log("variantMinQuantity: " + variantMinQuantity);

                    var variantMaxQuantity = filteredVariants[0].variantMaxQuantity ? removeTrailingZeros(filteredVariants[0].variantMaxQuantity) : 9999;

                    var variantCoefficient = removeTrailingZeros(filteredVariants[0].variantCoefficient);

                    document.getElementById('productSellingPrice').innerHTML = variantCurrencySymbole + ' ' + formatPrice(variantSellingPrice);
                    document.getElementById('productWithoutDiscountPrice').innerHTML = variantCurrencySymbole + ' ' + variantPriceWithoutDiscount;
                    //document.getElementById('productDiscountRate').innerHTML='- %'+variantDiscountRate;

                    document.getElementById('productPriceInput').value = variantSellingPrice;
                    document.getElementById('productStockCodeInput').value = variantStockCode;
                    document.getElementById('product-quantity').value = variantMinQuantity;

                    let totalPrice = variantMinQuantity * variantSellingPrice
                    console.log("totalPrice: " + totalPrice);
                    totalPrice = formatPrice(totalPrice);
                    //console.log("totalPrice: " + totalPrice);
                    document.querySelector('.product-total-price-container').innerHTML = '<span class="totalPrice">' + variantCurrencySymbole + ' ' +
                        totalPrice + '</span>';
                } else {
                    formElementStatus(true);
                }
            }
        }
    }
}