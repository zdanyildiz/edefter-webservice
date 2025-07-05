// Seçilen kargo ve fatura adreslerini tutacak değişkenler
var selectedCargo = 0;
var selectedInvoice = 0;

// Tüm radyo düğmelerini seç
var radios = document.querySelectorAll('.address-card input[type="radio"]');

// Her bir radyo düğmesine bir 'change' event listener ekleyin
radios.forEach(function(radio) {
    radio.addEventListener('change', function() {
        // Seçilen radyo düğmesinin ismine ve değerine göre seçilen kargo veya fatura değişkenlerini güncelle
        if (this.name === 'cargoAddressID') {
            selectedCargo = this.value;
        } else if (this.name === 'invoiceAddressID') {
            selectedInvoice = this.value;
        }

        // Tüm div'lerin sınıflarını kaldır
        document.querySelectorAll('.address-card').forEach(function(card) {
            card.classList.remove('selected-cargo', 'selected-invoice', 'selected-both');
        });

        // Seçilen kargo ve fatura adreslerine göre sınıfları atayın
        if (selectedCargo) {
            document.querySelector('#address-' + selectedCargo).classList.add('selected-cargo');
        }
        if (selectedInvoice) {
            document.querySelector('#address-' + selectedInvoice).classList.add('selected-invoice');
        }
        if (selectedCargo && selectedInvoice && selectedCargo === selectedInvoice) {
            document.querySelector('#address-' + selectedCargo).classList.add('selected-both');
        }
    });
});