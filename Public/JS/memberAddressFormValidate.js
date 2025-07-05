function validateAddresForm(event) {
    var form = document.querySelector('.member-address-form-container form');
    var inputs = form.querySelectorAll('input');
    var isValid = true;

    inputs.forEach(function(input) {
        if (input.value.trim() === '') {
            //alert(input.name + ' alanı boş olamaz');
            showPopup("error", input.name + ' alanı boş olamaz');
            // göndermeyi durduralım ve ilgili alana gidelim
            input.focus();
            event.preventDefault();
            return;
        }
    });

    var tcKimlik = form.querySelector('#identificationNumber').value;
    if (!validateTCKimlik(tcKimlik)) {
        //alert('Geçersiz TC Kimlik numarası');
        showPopup("error", "Geçersiz TC Kimlik numarası");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#identificationNumber').focus();
        event.preventDefault();
    }

    var telefon = form.querySelector('#addressPhone').value;
    if (!validatePhoneNumber(telefon)) {
        //alert('Geçersiz telefon numarası. Telefon numarası 10 haneli olmalı ve +90, 90 veya 0 ile başlamamalı.');
        showPopup("error", "Geçersiz telefon numarası. Telefon numarası 10 haneli olmalı ve +90, 90 veya 0 ile başlamamalı.");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#addressPhone').focus();
        event.preventDefault();
    }

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
    if (phoneNumber.startsWith("+90") || phoneNumber.startsWith("90") || phoneNumber.startsWith("0")) {
        return false;
    }
    // Telefon numarasının sadece sayılardan oluştuğunu kontrol edin.
    if (!/^\d+$/.test(phoneNumber)) {
        return false;
    }
    // Telefon numarası geçerlidir.
    return true;;
}

document.querySelector('.member-address-form-container form').addEventListener('submit', validateAddresForm);

document.querySelector('#addressCountry').addEventListener('change', function(){
    var countryID = this.value;
    var languageCode = document.querySelector('#languageCode').value;

    var inputs = [
        document.querySelector('input#addressCityName'),
        document.querySelector('input#addressCountyName'),
        document.querySelector('input#addressAreaName'),
        document.querySelector('input#addressNeighborhoodName')
    ];

    var selects = [
        document.querySelector('select#addressCity'),
        document.querySelector('select#addressCounty'),
        document.querySelector('select#addressArea'),
        document.querySelector('select#addressNeighborhood')
    ];

    if(countryID == 212){
        inputs.forEach(input => {
            input.style.display = 'none';
            input.value = '-';
        });
        selects.forEach(select => {
            select.style.display = 'block';
            select.value = '';
        });
        getLocation('addressCity',countryID,languageCode);
    }else{
        inputs.forEach(input => {
            input.style.display = 'block';
            input.value = '';
        });
        selects.forEach(select => {
            select.style.display = 'none';
            select.innerHTML = '<option value="-">-</option>';
        });
    }
});
var selects = document.querySelectorAll('.member-address-form-container select');
selects.forEach(function(select){
    select.addEventListener('change', function(){
        var ID = this.id;
        if(ID === 'addressCountry'){
            return;
        }
        var parentID = this.value;
        var languageCode = document.querySelector('#languageCode').value;
        var locationDetails = {
            'addressCity': { itemName: 'addressCounty' },
            'addressCounty': { itemName: 'addressArea' },
            'addressArea': { itemName: 'addressNeighborhood' },
            'addressNeighborhood': { itemName: 'addressPostalCode'}
        };
        var details = locationDetails[ID];
        getLocation(details.itemName, parentID, languageCode);
    });
});


async function getLocation($locationName, $parentID, $languageCode) {
    try {
        const response = await fetch("/?/control/location/post/getLocation", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-Requested-With": "XMLHttpRequest"
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
    }
    catch (error) {
        console.error(error);
    }
}

var languageCode="";
function showAddressPopup(type, message) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `/?/control/popup/get/deleteAddress&type=${type}&message=${message}&closeButton=true&autoClose=false&confirm=delete&languageCode=${languageCode}&$position=center`);
    xhr.send();

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.body.insertAdjacentHTML("beforeend", xhr.responseText);
        }
    };
}

document.querySelectorAll('.address-card .btn.btn-danger').forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.preventDefault(); // varsayılan eylemi engelle
        //console.log(this);
        languageCode = this.dataset.languagecode;
        //console.log(languageCode);
        // onay popup'ını göster
        showAddressPopup('warning', 'Bu adresi silmek istediğinize emin misiniz?');

        // kullanıcının onayını dinle
        document.addEventListener('click', function(event) {
            if (event.target.matches('.popup .confirm')) {
                // eğer kullanıcı onaylarsa, varsayılan eylemle devam et
                window.location.href = link.href;
            }
        });
    });
});