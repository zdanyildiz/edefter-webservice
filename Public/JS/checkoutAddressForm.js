var languageCode = document.documentElement.lang;
function validatePaymentAddressForm(event) {
    //event.preventDefault();
    var form = document.querySelector('form#paymentAddressForm');
    var inputs = form.querySelectorAll('input');
    languageCode = document.querySelector('#languageCode').value;
    var isValid = true;

    var telefon = form.querySelector('#telephone').value;
    if (!validatePhoneNumber(telefon)) {
        //alert('Geçersiz telefon numarası. Telefon numarası 10 haneli olmalı ve +90, 90 veya 0 ile başlamamalı.');
        showPopupForAddressForm("error", "Geçersiz telefon numarası. Telefon numarası 10 haneli olmalı ve +90, 90 veya 0 ile başlamamalı.");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#telephone').focus();
        event.preventDefault();
        return;
    }

    var email = form.querySelector('#email').value;
    if (!validateEmailAddress(email)) {
        //alert('Geçersiz e-posta adresi');
        showPopupForAddressForm("error", "Geçersiz e-posta adresi");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#email').focus();
        event.preventDefault();
        return;
    }

    var tcKimlik = form.querySelector('#identificationNumber').value;
    if (!validateTCKimlik(tcKimlik)) {
        //alert('Geçersiz TC Kimlik numarası');
        showPopupForAddressForm("error", "Geçersiz TC Kimlik numarası");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#identificationNumber').focus();
        event.preventDefault();
        return;
    }

    var vergiNo = form.querySelector('#invoiceTaxNumber').value;
    if (!validateVergiNo(vergiNo)) {
        //alert('Geçersiz Vergi Numarası');
        showPopupForAddressForm("error", "Geçersiz Vergi Numarası");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#invoiceTaxNumber').focus();
        event.preventDefault();
        return;
    }

    inputs.forEach(function(input) {
        var style = window.getComputedStyle(input);

        if (input.value.trim() === '') {
            var label = document.querySelector('label[for="' + input.id + '"]');
            var labelText = label ? label.innerText : input.name;
            showPopup("error", labelText + ' alanı boş olamaz');
            input.focus();
            event.preventDefault();
            return;
        }
    });
}
function validateTCKimlik(value) {
    value = value.toString();
    if(value==="11111111111")
    {
        return true;
    }
    else if(value.length!== 11)
    {
        return false;
    }
    var isEleven = /^[0-9]{11}$/.test(value);
    var totalX = 0;
    for (var i = 0; i < 10; i++)
    {
        totalX += Number(value.substr(i, 1));
    }
    var isRuleX = totalX % 10 == value.substr(10,1);
    var totalY1 = 0;
    var totalY2 = 0;
    for (var i = 0; i < 10; i+=2)
    {
        totalY1 += Number(value.substr(i, 1));
    }
    for (var i = 1; i < 10; i+=2)
    {
        totalY2 += Number(value.substr(i, 1));
    }
    var isRuleY = ((totalY1 * 7) - totalY2) % 10 == value.substr(9,0);
    return isEleven && isRuleX && isRuleY;
}
function validatePhoneNumber(phoneNumber) {
    // Telefon numarasının 10 haneli olup olmadığını kontrol edin.
    if (phoneNumber.length !== 10) {
        return false;
    }
    // Telefon numarasının +90, 90 veya 0 ile başlamadığını kontrol edin.
    if (phoneNumber.startsWith("+90") || phoneNumber.startsWith("90") || phoneNumber.startsWith("0") || phoneNumber.startsWith("+")) {
        return false;
    }
    // Telefon numarasının sadece sayılardan oluştuğunu kontrol edin.
    if (!/^\d+$/.test(phoneNumber)) {
        return false;
    }
    // Telefon numarası geçerlidir.
    return true;
}

function validateVergiNo(value) {
    if (value.length === 10)
    {
        if(value=="2222222222")
        {
            return true;
        }
        let v = []
        let lastDigit = Number(value.charAt(9))
        for (let i = 0; i < 9; i++) {
            let tmp = (Number(value.charAt(i)) + (9 - i)) % 10
            v[i] = (tmp * 2 ** (9 - i)) % 9
            if (tmp !== 0 && v[i] === 0) v[i] = 9
        }
        let sum = v.reduce((a, b) => a + b, 0) % 10
        return (10 - (sum % 10)) % 10 === lastDigit
    }
    if (value.length === 11){
        return validateTCKimlik(value)
    }
    return false
}

function validateEmailAddress(email) {
    // E-posta adresinin '@' karakteri içerip içermediğini kontrol edin.
    if (!email.includes("@")) {
        return false;
    }

    // E-posta adresinin '.' karakteri içerip içermediğini kontrol edin.
    if (!email.includes(".")) {
        return false;
    }

    // E-posta adresinin geçerli bir formatta olup olmadığını kontrol edin.
    const regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!regex.test(email)) {
        return false;
    }

    // E-posta adresi geçerlidir.
    return true;
}

document.querySelector('#paymentAddressForm').addEventListener('submit', validatePaymentAddressForm);

document.querySelector('#addressCountry').addEventListener('change', function() {
    var countryID = this.value;
    var languageCode = document.querySelector('#languageCode').value;

    var Ids = ['addressCity', 'addressCounty', 'addressArea', 'addressNeighborhood'];

    if (countryID == 212) {
        Ids.forEach(function(id) {
            var input = document.querySelector('input#' + id);
            var select = document.querySelector('select#' + id);

            if (input) {
                input.remove();
            }

            if (!select) {
                var newSelect = document.createElement('select');
                newSelect.id = id;
                newSelect.name = id;
                newSelect.className = 'form-control';
                newSelect.required = true;
                document.querySelector('#'+ id +'Container').appendChild(newSelect);
            }
        });

        getLocation('addressCity', countryID, languageCode);
    } else {
        Ids.forEach(function(id) {
            var select = document.querySelector('select#' + id);
            var input = document.querySelector('input#' + id);

            if (select) {
                select.remove();
            }

            if (!input) {
                var newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.id = id;
                newInput.name = id;
                newInput.className = 'form-control';
                newInput.required = true;
                document.querySelector('#'+ id +'Container').appendChild(newInput);
            }
        });
    }
});
var selects = document.querySelectorAll('.address-form-container select');
document.addEventListener('change', function(event) {
    var target = event.target;
    var Ids = ['addressCity', 'addressCounty', 'addressArea', 'addressNeighborhood'];

    // Eğer hedef bir 'select' elementi ise ve Ids dizisi hedefin id'sini içeriyorsa
    if (target.tagName.toLowerCase() === 'select' && Ids.includes(target.id)) {
        var parentID = target.value;
        var languageCode = document.querySelector('#languageCode').value;
        var locationDetails = {
            'addressCity': { itemName: 'addressCounty' },
            'addressCounty': { itemName: 'addressArea' },
            'addressArea': { itemName: 'addressNeighborhood' },
            'addressNeighborhood': { itemName: 'addressPostalCode'}
        };
        var details = locationDetails[target.id];
        getLocation(details.itemName, parentID, languageCode);
    }
});


async function getLocation($locationName, $parentID, $languageCode) {
    try {
        const response = await fetch("/?/control/location/post/getLocation", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `locationName=${$locationName}&parentID=${$parentID}&action=getLocation&languageCode=${$languageCode}`
        });

        if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
        }

        const data = await response.json();

        if (data.status !== "success") {
            console.error(data.message);
            return;
        }

        const location = data.LocationData;
        const select = document.querySelector(`select#${$locationName}`);
        const locationDetails = {
            'addressCity': { itemID: 'CityID', itemName: 'CityName' },
            'addressCounty': { itemID: 'CountyID', itemName: 'CountyName' },
            'addressArea': { itemID: 'AreaID', itemName: 'AreaName' },
            'addressNeighborhood': { itemID: 'NeighborhoodID', itemName: 'NeighborhoodName' },
            'addressPostalCode': { itemID: 'ZipCode', itemName: 'ZipCode' }
        };

        const details = locationDetails[$locationName];

        if ($locationName !== 'addressPostalCode') {
            select.innerHTML = '<option value="">Seçiniz</option>';
        }

        location.forEach(item => {
            if (details.itemID === 'ZipCode') {
                document.querySelector('input#addressPostalCode').value = item[details.itemID];
                return;
            }
            const option = document.createElement('option');
            option.value = item[details.itemID];
            option.innerHTML = item[details.itemName];
            select.appendChild(option);
        });
    } catch (error) {
        console.error(error);
    }
}

document.querySelector('.addAddress').addEventListener('click', function(){
    document.querySelector('.address-form-modal').classList.add('active');
});

document.querySelector('.close-address-form-modal').addEventListener('click', function(){
    document.querySelector('.address-form-modal').classList.remove('active');
});

function showPopupForAddressForm(type, message) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `/?/control/popup/get/show&type=${type}&message=${message}&&languageCode=${languageCode}&autoClose=false`);
    xhr.send();

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.body.insertAdjacentHTML("beforeend", xhr.responseText);
            //if (!isScriptLoaded('/Public/JS/popup.min.js?v=3')) {
                loadScript('/Public/JS/popup.min.js?v=4');
            //}
        }
    };
}
function loadScript(url) {
    var script = document.createElement('script');
    script.src = url;
    document.body.appendChild(script);
}
/*function isScriptLoaded(url) {
    return !!document.querySelector(`script[src="${url}"]`);
}*/

document.getElementById('email').addEventListener('blur', function() {
    var email = this.value;
    var memberID = document.querySelector('#memberID').value;

    if(memberID === '' || memberID === '0') {
        var url = "/?/control/member/get/checkUser&email=" + email + "&languageCode=${languageCode}";
        var xhr = new XMLHttpRequest();
        xhr.open("GET", url, true);
        xhr.send();
        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                var response = JSON.parse(xhr.responseText);
                if (response.status === "success") {
                    showPopupForAddressForm("error", response.message);
                }
            }
        };
    }
});

document.getElementById('telephone').addEventListener('blur', function() {
    var telephone = this.value;
    var memberID = document.querySelector('#memberID').value;

    if(memberID == '' || memberID == '0') {
        var url = "/?/control/member/get/checkUser&telephone=" + telephone + "&languageCode=${languageCode}";
        var xhr = new XMLHttpRequest();
        xhr.open("GET", url, true);
        xhr.send();
        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status === "success") {
                    showPopupForAddressForm("error", response.message);
                }
            }
        };
    }
});