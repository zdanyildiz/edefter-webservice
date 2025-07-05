function getLocationData(action, locationID, targetSelect) {

    if( action == ""){
        return;
    }

    if(action == "getCity" && locationID != 212)
    {
        return;
    }

    /*console.log("action:" + action);
    console.log("locationID:" + locationID);
    console.log("targetSelect:", targetSelect);*/

    $.ajax({
        url: '/App/Controller/Admin/AdminLocationController.php', // API endpoint URL
        type: 'POST',
        data: {
            action: action,
            id: locationID
        },
        success: function(data) {
            //console.log(data);

            targetSelect.empty();

            var data = JSON.parse(data);

            if(data.status == "success"){
                var locations = data.location;
                var option = '<option value="">Seçiniz</option>';
                targetSelect.append(option);
                for(var i = 0; i < locations.length; i++){
                    var location = locations[i];
                    var locationID = location.id;
                    var locationName = location.name;

                    option = '<option value="'+locationID+'">'+locationName+'</option>';
                    targetSelect.append(option);
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // handle error
            console.error(textStatus, errorThrown);
        }
    });
}

function convertInputSelect(thisContainer, selectNames, countryID) {
    for(var i = 1; i < selectNames.length; i++){
        var selectName = selectNames[i];
        if(countryID !== "212"){
            // Türkiye seçilmemişse diğer selectleri input yapalım
            var select = thisContainer.find("select[name='"+selectName+"']");
            select.replaceWith('<input type="text" class="form-control" name="'+selectName+'" id="'+selectName+'">');
        }
        else{
            // Önce select kontrolü yapalım input olmuş ise tekrar select'e çevirelim
            var select = thisContainer.find("input[name='"+selectName+"']");
            if(select.length > 0){
                select.replaceWith('<select class="form-control" name="'+selectName+'" id="'+selectName+'"><option value="">Seçiniz</option></select>');
            }
        }
    }
}

function getPostalCode(locationID, target, callback){
    console.log(target);
    $.ajax({
        url: '/App/Controller/Admin/AdminLocationController.php', // API endpoint URL
        type: 'POST',
        data: {
            action: "getPostalCode",
            id: locationID
        },
        success: function(data) {
            //console.log(data);
            var data = JSON.parse(data);
            if(data.status == "success"){

                var postalCode = data.postalCode;

                //Hedef input value içinde "PK: 34528" şablona uyan bir değer varsa bulup bizimki ile değiştirelim yoksa value sonuna değeri ekleyelim

                var targetInput = $("#"+target);
                var targetValue = targetInput.val();

                //value içinde "PK: ile başlayan ifade var mı bulalım"
                var pattern = /PK: \d{5}/;
                var match = targetValue.match(pattern);
                if(match !== null){
                    //bulduğumuz değeri değiştirelim
                    targetValue = targetValue.replace(pattern, "PK: " + postalCode);
                }
                else{
                    //bulamadık sonuna ekleyelim
                    targetValue += " PK: " + postalCode;
                }

                targetInput.val(targetValue);

                if (typeof callback === "function") {
                    callback(postalCode);
                }
            }
            else {
                if (typeof callback === "function") {
                    callback("");
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // handle error
            console.error(textStatus, errorThrown);
            if (typeof callback === "function") {
                callback("");
            }
        }
    });
}