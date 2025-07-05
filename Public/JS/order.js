document.querySelectorAll('.cancellationRefundExchangeResponseButton').forEach(button => {
    button.addEventListener('click', function () {
        const orderID = this.dataset.orderuniqid;
        document.getElementById('orderUniqID').value = orderID;
        let csrfToken = document.getElementById('csrf-token-return-form').value;
        // Ürünleri yükle
        fetch(`/?/control/member/get/getOrderProducts&orderUniqID=${orderID}&csrf_token=${csrfToken}`)
            .then(response => response.json())
            .then(data => {
                console.log(data); // Gelen veriyi kontrol edin
                const productList = document.getElementById('return-form-product-list');
                productList.innerHTML = '';

                // Gelen verinin yapısını kontrol edin
                if (data && data.data && Array.isArray(data.data)) {
                    data.data.forEach(product => {
                        productList.innerHTML += `
                                <div>
                                    <input type="checkbox" name="products[]" value="${product.productID}">
                                    <label>${product.productName}</label>
                                </div>
                            `;
                    });
                } else {
                    console.error("Beklenmeyen veri formatı:", data);
                }
            })
            .catch(error => {
                console.error("Bir hata oluştu:", error);
            });

        document.getElementById('return-form-popup').classList.remove('hidden');
    });
});

document.querySelector('.close-popup').addEventListener('click', function () {
    document.getElementById('return-form-popup').classList.add('hidden');
});

document.getElementById('return-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const selectedProducts = document.querySelectorAll('#return-form-product-list input[type=checkbox]:checked');
    if (selectedProducts.length === 0) {
        alert('Lütfen en az bir ürün seçiniz.');
        return;
    }

    const formData = new FormData(this);

    fetch('/?/control/member/post/addCancellationRefundExchange', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.status === 'success') {
                alert('Talebiniz başarıyla alınmıştır.');
                document.getElementById('return-form-popup').classList.add('hidden');
                // Formu sıfırla
                document.getElementById('return-form').reset();
            } else {
                alert('Bir hata oluştu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Bir hata oluştu:', error);
            alert('İşlem sırasında bir hata oluştu.');
        });
});