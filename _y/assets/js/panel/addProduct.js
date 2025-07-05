
async function loadCategories(selectedElement) {
    const layer = selectedElement.data('layer');
    const categoryID = selectedElement.val();
    const action = "getSubCategories";

    if (categoryID > 0) {
        try {
            const response = await $.ajax({
                url: '/App/Controller/Admin/AdminProductController.php',
                type: 'POST',
                data: {
                    categoryID: categoryID,
                    action: action
                }
            });

            const data = JSON.parse(response);
            const { status, subCategories } = data;

            if (status === "success") {
                const categoryContainer = $('#categoryContainer');
                const newLayer = layer + 1;
                const categoryListId = 'categoryList' + newLayer;
                const categoryListSelector = '#' + categoryListId;

                if (!$(categoryListSelector).length) {
                    const newCategoryList = `
                        <div id="${categoryListId}" class="categoryList col-sm-6 form-group floating-label">
                            <select data-layer="${newLayer}" class="col-sm-12 form-control"></select>
                            <p class="help-block">Alt kategori Seçin</p>
                        </div>`;
                    categoryContainer.append(newCategoryList);
                }

                const categoryList = $(categoryListSelector + ' select');
                categoryList.empty();
                categoryList.append('<option value="0" selected>Alt Kategori Seçin</option>');
                $.each(subCategories, function (index, category) {
                    categoryList.append(`<option value="${category.productCategoryID}">${category.productCategoryName}</option>`);
                });

            } else {
                $(`#categoryList${layer + 1}`).remove();
            }
        } catch (error) {
            console.error(`Error loading categories: ${error.message}`);
        }
    }
}

async function changeLanguageID() {
    const languageID = $('#languageID').val();
    const action = "getProductCategories";
    const categoryContainer = $('#categoryContainer select');
    const modal = $('#alertModal');
    const categoryList = $('#categoryList0 select');

    categoryContainer.empty();

    if (languageID > 0) {
        try {
            const response = await $.ajax({
                url: '/App/Controller/Admin/AdminProductController.php',
                type: 'POST',
                data: {
                    languageID: languageID,
                    action: action
                }
            });

            const { status, message, productCategories: categories } = JSON.parse(response);

            if (status === "success") {
                $("#productCategoryID").val(0);
                categoryList.empty();
                categoryList.append('<option value="0" selected>Kategori Seçin</option>');
                categories.forEach(({ productCategoryID, productCategoryName }) => {
                    categoryList.append(`<option value="${productCategoryID}">${productCategoryName}</option>`);
                });
                console.log("Dil değişikliği başarılı, kategoriler yüklendi");
            } else {
                modal.find('#alertMessage').text(message);
            }
        } catch (error) {
            console.error(error);
        }
    }
}

async function selectCategory(i) {
    console.log("Kategori seçiliyor: " + categoryHierarchy[i]);
    const categoryID = categoryHierarchy[i].categoryID;
    console.log("Kategori seçiliyor: " + categoryID);

    const categoryListSelector = `#categoryList${i} select`;
    const selectedElement = $(categoryListSelector);

    $("#productCategoryID").val(categoryID);
    selectedElement.val(categoryID);

    await loadCategories(selectedElement);
}

async function selectCategories() {
    const categoryHierarchyLength = categoryHierarchy.length;
    for(let i = 0; i < categoryHierarchyLength; i++){
        console.log("Kategori seçiliyor: " + `#categoryList${i} select`);
        await selectCategory(i);
    }
    console.log("Kategoriler seçildi");
}

async function getVariantGroups() {
    const action = "getVariantGroups";
    const languageCode = $('#languageID option:selected').data('languagecode');
    console.log("Dil kodu: " + languageCode);

    try {
        const response = await $.ajax({
            url: '/App/Controller/Admin/AdminProductVariantController.php',
            type: 'POST',
            data: {
                languageCode: languageCode,
                action: action
            }
        });

        const parsedResponse = JSON.parse(response);
        console.log("getVariantGroups Yanıtı:", parsedResponse);

        const { status, data: variantGroups, message } = parsedResponse;
        const variantGroupContainer = $('#variantGroupContainer');
        variantGroupContainer.empty();

        if (status === "success") {
            if (Array.isArray(variantGroups) && variantGroups.length > 0) {
                variantGroups.forEach(variantGroup => {
                    const variantGroupDiv = `
                        <div class="col-md-6 variantGroupDiv">
                            <div class="form-group">
                                <div class="checkbox checkbox-styled">
                                    <label for="variantGroupCheckBox-${variantGroup.variantGroupUniqID}" class="col-sm-12 control-label opacity-100">
                                        <input class="variantGroupSelect" name="variantGroupName" id="variantGroupCheckBox-${variantGroup.variantGroupUniqID}" data-uniqid="${variantGroup.variantGroupUniqID}" data-id="${variantGroup.variantGroupID}" type="checkbox" value="${variantGroup.variantGroupName}">
                                        <span class="variantGroupName">${variantGroup.variantGroupName} değerlerini seçin</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="text" id="searchVariant-${variantGroup.variantGroupUniqID}" data-id="${variantGroup.variantGroupUniqID}" class="searchVariant form-control disabled" disabled placeholder="Varyant Ara">
                                </div>
                                <div class="variantContainer" id="variantContainer-${variantGroup.variantGroupUniqID}">
                                </div>
                            </div>
                        </div>`;
                    variantGroupContainer.append(variantGroupDiv);
                });
                console.log("Varyant grupları başarıyla yüklendi.");
            } else {
                console.warn("Varyant grupları boş veya geçersiz formatta.");
                $('#alertModal').find('#alertMessage').text("Varyant grupları bulunamadı.");
                $('#alertModal').modal("show");
            }
        } else {
            console.error("getVariantGroups Hatası:", message);
            if(localStorage.getItem("variantWarningClosed")!=="true"){
                $('#alertModal').find('#alertMessage').text(message);
                $('#alertModal').modal("show");
                setTimeout(function(){
                    $('#alertModal').modal("hide");
                },1000);
            }

        }
    } catch (error) {
        console.error("getVariantGroups AJAX Hatası:", error);
        $('#alertModal').find('#alertMessage').text("Varyant grupları yüklenirken bir hata oluştu.");
        $('#alertModal').modal("show");
    }
}


async function selectVariantGroups() {
    if (productVariants.length === 0) {
        return;
    }

    const variantGroups = new Set();

    productVariants.forEach(variant => {
        const variantIDArray = variant.variantID.split("_");
        variantIDArray.forEach(id => {
            const variantGroupID = id.split("-")[0];
            variantGroups.add(variantGroupID);
        });
    });

    for (const groupID of variantGroups) {
        const variantGroupCheckBox = $(`input[name="variantGroupName"][data-id="${groupID}"]`);
        console.log(`seçilen varyant grupid: ${groupID}`);
        variantGroupCheckBox.prop('checked', true).trigger('change');
        await new Promise(resolve => setTimeout(resolve, 500)); // wait for 500ms to ensure the AJAX request in the change event handler is completed
    }
}

async function selectVariantValues() {
    if (productVariants.length === 0) {
        return;
    }

    const variantGroupsWithValues = new Set();

    productVariants.forEach(variant => {
        variant.variantProperties.forEach(property => {
            const groupWithValue = {
                groupName: property.attribute.name,
                value: property.attribute.value
            };
            variantGroupsWithValues.add(JSON.stringify(groupWithValue));
        });
    });

    const variantGroupsWithValuesArray = Array.from(variantGroupsWithValues);

    const promises = variantGroupsWithValuesArray.map(async (item, i) => {
        await new Promise(resolve => setTimeout(resolve, i * 500));
        const groupWithValue = JSON.parse(item);
        const valueCheckbox = $(`input[name="variantName"][value="${groupWithValue.value}"]`);
        valueCheckbox.attr('checked', true);
    });

    console.log("Varyant değerleri işaretlendi");
    await Promise.all(promises);
}

function moveSelectedCheckboxes() {
    $('.variantContainer').each(function() {
        const container = $(this);
        const selectedElements = container.find('input[name="variantName"]:checked').closest('.form-group');

        if (container.children().length > 0 && selectedElements.length > 0) {
            selectedElements.prependTo(container);
        }
    });
}

async function setLanguageWithProduct() {
    const loader = $("#loader");
    loader.removeClass("hidden");

    /*await changeLanguageID();
    await selectCategories();
    await getVariantGroups();
    await selectVariantGroups();
    await selectVariantValues();
    moveSelectedCheckboxes();*/

    changeLanguageID().then(() => {
        selectCategories().then(() => {
            getVariantGroups().then(() => {
                selectVariantGroups().then(() => {
                    loader.addClass("hidden");
                    selectVariantValues().then(() => {
                        moveSelectedCheckboxes();
                    });
                });
            });
        });
    });
}

function categoriesMobileShow(){
    //ekran çözünürlüğü 1024'ten küçükse
    if($(window).width() <= 1024){
        $("#submit,#submitAndCopy").addClass("btn-sm");

        $("#imageButtonContainer,#fileButtonContainer,#showVariantGroup").css("width","100%");
        $("#addImageByLeftCanvas,#addImageByRightCanvas,#addFileByLeftCanvas,#addFileByRightCanvas").addClass("btn-xs");
        $("#addImageByRightCanvas,#addFileByRightCanvas").css("float","right");

        $("#tabProductVariant h4,#offcanvas-variant h4").addClass("small");
    }
}

const fields = [
    { labelName: 'Stok Kodu', inputName: 'StockCode' },
    { labelName: 'GTIN', inputName: 'GTIN' },
    { labelName: 'MPN', inputName: 'MPN' },
    { labelName: 'Barkod', inputName: 'Barcode' },
    { labelName: 'OEM', inputName: 'OEM' },
    { labelName: 'Stok Adeti', inputName: 'Stock' },
    { labelName: 'Satış Fiyatı', inputName: 'SalePrice' },
    { labelName: 'İndirimsiz Satış Fiyatı', inputName: 'DiscountPrice' },
    { labelName: 'Bayi Fiyatı', inputName: 'DealerPrice' },
    { labelName: 'Alış Fiyatı', inputName: 'PurchasePrice' }
];

function createFormGroup(labelName, inputName, inputId) {
    if (!labelName || !inputName || !inputId) {
        console.error("createFormGroup fonksiyonuna geçersiz parametreler verildi.");
        return '';
    }

    return `
        <div class="col-sm-2 form-group">
            <label for="${inputId}">${labelName}</label>
            <input type="text" name="${inputName}[]" id="${inputId}" class="form-control" required>
        </div>`;
}


//################ variant oluşturma

function collectVariants() {
    var variantGroups = [];

    $(".variantGroupSelect:checked").each(function() {
        var variantGroupID = $(this).data("id");
        var variantGroupUniqID = $(this).data("uniqid");
        var variantGroupName = $(this).val();

        console.log(`Toplanıyor: Varyant Grubu - ID: ${variantGroupID}, Unique ID: ${variantGroupUniqID}, Adı: ${variantGroupName}`);

        let variantGeneralIds = [];
        let currentVariantGroup = [];

        $("#variantContainer-" + variantGroupUniqID).find(".variantSelect:checked").each(function() {
            var variantID = $(this).data("id");
            var attributeID = `${variantGroupID}-${variantID}`;
            variantGeneralIds.push(attributeID);

            currentVariantGroup.push({
                attributeID: attributeID,
                variantName: variantGroupName,
                variantValue: $(this).val()
            });

            console.log(`Toplanıyor: Varyant - ID: ${variantID}, Attribute ID: ${attributeID}, Değer: ${$(this).val()}`);
        });

        if (variantGeneralIds.length > 0) {
            variantGeneralIds.sort();
            const sortedVariantIDs = variantGeneralIds.join("|");

            variantGroups.push({
                variantGroupID: variantGroupID,
                variantGroupName: variantGroupName,
                variantIDs: sortedVariantIDs,
                variants: currentVariantGroup.slice() // Derin kopyalama
            });

            console.log(`Eklenen Varyant Grubu: ${variantGroupName}, Varyant Sayısı: ${currentVariantGroup.length}`);
        } else {
            console.warn(`Varyant Grubu (${variantGroupName}) seçili ancak varyant seçilmemiş.`);
        }
    });

    console.log("Toplanan Seçilen Varyant Grupları:", variantGroups);

    return variantGroups;
}


function cartesianProduct(arr) {
    if (!Array.isArray(arr) || arr.length === 0) {
        console.warn("cartesianProduct fonksiyonuna boş veya geçersiz bir dizi verildi.");
        return [];
    }

    // Boş iç dizileri filtrele
    const filteredArr = arr.filter(group => Array.isArray(group) && group.length > 0);
    if (filteredArr.length === 0) {
        console.warn("cartesianProduct fonksiyonuna boş varyant grubu dizileri verildi.");
        return [];
    }

    const result = filteredArr.reduce(function(a, b) {
        return a.map(function(x) {
            return b.map(function(y) {
                return x.concat(y);
            });
        }).reduce(function(a, b) { return a.concat(b) }, [])
    }, [[]]);

    console.log("cartesianProduct Sonucu:", result);
    return result;
}


//################ variant oluşturma

function processProductProperties(){
    let productProperties = [];
    let $productSeoKeywords = $("#productSeoKeywords");

    $(".getProductProperties").each(function(){
        let productProperty = $(this).val();
        productProperties.push(productProperty);
    });

    if(productProperties.length > 0){
        console.log('Ürün özellikleri:', productProperties);

        let seoKeywords = $productSeoKeywords.val();

        productProperties.forEach(function(productProperty){

            if(seoKeywords.indexOf(productProperty) === -1){
                seoKeywords += ", "+productProperty;
            }

        });

        seoKeywords = seoKeywords.replace(/^,|,$/g, "");
        $productSeoKeywords.val(seoKeywords);
    }
}

function createSeoLink($string){
    $string = $string.toLowerCase();
    $string = $string.replace(/ğ/g, "g");
    $string = $string.replace(/ü/g, "u");
    $string = $string.replace(/ş/g, "s");
    $string = $string.replace(/ı/g, "i");
    $string = $string.replace(/ö/g, "o");
    $string = $string.replace(/ç/g, "c");
    $string = $string.replace(/ /g, "-");
    $string = $string.replace(/[^a-z0-9-]/g, "");

    //yanyana gelmiş birden fazla - karakterini tek - karakterine dönüştürelim
    $string = $string.replace(/-+/g, "-");
    return $string;
}

function createSeoDescription() {
    const productSeoDescription = $("#productSeoDescription");
    if(productSeoDescription.val() === "") {
        const categoryContainer = $("#categoryContainer select:last option:selected");
        const productName = $("#productName").val();
        let productProperties = $("#productSeoKeywords").val();
        const languageCode = $("#languageID option:selected").data("languagecode").toUpperCase();

        const categoryName = categoryContainer.text();
        productProperties = productProperties.replace(`${categoryName}, `, "");
        productProperties = productProperties.replace(`${productName}, `, "");
        productProperties = productProperties.replace(`${languageCode}, `, "");

        let seoDescription;
        if ($("#languageID").val() == 1) {
            seoDescription = `${categoryName} kategorisindeki ${productName}, ${productProperties} özelliklerine sahiptir.`;
        } else {
            seoDescription = `The ${productName} in the ${categoryName} category has the following features: ${productProperties}.`;
        }

        productSeoDescription.val(seoDescription);
    }
}

function processVariantGroupNames() {
    const productSeoKeywords = $("#productSeoKeywords");
    if(productSeoKeywords.val() === "") {
        const variantGroupNames = [];
        $(".getVariantGroupName").each(function () {
            variantGroupNames.push($(this).text());
        });

        let seoKeywords = productSeoKeywords.val();
        const languageCode = $("#languageID option:selected").data("languagecode").toUpperCase();
        const categoryName = $("#categoryContainer select:last option:selected").text();
        const productName = $("#productName").val();

        const keywordsToAdd = [languageCode, categoryName, productName, ...variantGroupNames];
        keywordsToAdd.forEach(keyword => {
            if (!seoKeywords.includes(keyword)) {
                seoKeywords += `, ${keyword}`;
            }
        });

        seoKeywords = seoKeywords.replace(/^,|,$/g, "");
        productSeoKeywords.val(seoKeywords);
    }
}

$(document).ready(function() {
    $('.datepicker').datepicker({autoclose: true, todayHighlight: true, format: "yyyy-mm-dd"});

    $(document).on('change', '#categoryContainer select', async function () {
        let selectedElement = $(this);
        await loadCategories(selectedElement);
    });

    $(document).on("change", "#languageID", function () {
        changeLanguageID().then(() => {
            console.log("Dil değişikliği başarılı");
        });
    });

    $(document).on("change", "#isVariant", function () {
        if ($(this).is(":checked")) {
            $("#variantGroupContainer").removeClass("hidden");
            $("#createVariant").removeClass("hidden");
            $("#showVariantGroup").removeClass("disabled");

            if ($('#productID').val() === 0) {
                getVariantGroups().then(() => {
                    console.log("Varyant grupları yüklendi");
                });
            }
        } else {
            $("#variantGroupContainer").addClass("hidden");
            $("#createVariant").addClass("hidden");
            $("#showVariantGroup").addClass("disabled");
        }
    });


    $(document).on("click", "#createVariant", async function () {
        $(".closeCanvas").click();

        // "variant-no-variant" elemanını kaldır
        $("#variant-no-variant").remove();

        const variantGroups = collectVariants();
        console.log("Seçilen Varyant Grupları:", variantGroups);

        if (variantGroups.length === 0) {
            $("#alertMessage").html("Lütfen en az bir varyant grubu seçiniz");
            $("#alertModal").modal("show");
            return false;
        }

        // Her varyant grubu için varyantların doğruluğunu kontrol edin
        for (let group of variantGroups) {
            if (!Array.isArray(group.variants) || group.variants.length === 0) {
                console.warn(`Varyant Grubu (${group.variantGroupName}) için varyant bulunamadı.`);
                $("#alertMessage").html(`"${group.variantGroupName}" grubu için varyant bulunamadı.`);
                $("#alertModal").modal("show");
                return false;
            }
        }

        const variants = cartesianProduct(variantGroups.map(group => group.variants));
        console.log("Tüm Varyant Kombinasyonları:", variants);

        const variantContainer = $('#variantContainer');

        for (let variant of variants) {
            let generalVariantName = "";
            let generalVariantIDs = [];

            variant.forEach(item => {
                const attributeID = item.attributeID;
                generalVariantIDs.push(attributeID);
                generalVariantName += `${item.variantName}: ${item.variantValue} | `;
            });

            generalVariantIDs.sort();
            const variantID = generalVariantIDs.join("_");
            generalVariantName = generalVariantName.slice(0, -3); // Son ' | ' karakterlerini kaldır

            const existingVariant = $(`#variant-${variantID}`);
            console.log(`Mevcut Varyant (${variantID}) Durumu:`, existingVariant.length > 0 ? "Var" : "Yok");

            if (existingVariant.length === 0) {
                let html = `<div class="row" id="variant-${variantID}">`;

                variant.forEach(item => {
                    html += `<input type="hidden" id="${item.attributeID}" name="variantProperties[${variantID}]" value="${item.variantName}|${item.variantValue}">`;
                });

                html += `<div class="getVariantGroupName col-sm-8 form-group text-bold text-primary">${generalVariantName}</div>`;
                html += '<div class="col-sm-4 form-group text-right">';
                html += `<a class="btn btn-floating-action ink-reaction dragDropVariant" data-variantid="${variantID}" title="Sırala"><i class="fa fa-arrows"></i></a>`;
                html += `<a class="btn btn-floating-action ink-reaction removeVariant" data-variantid="${variantID}" title="Kaldır"><i class="fa fa-trash"></i></a>`;
                html += '</div>';

                // 'fields' değişkeninin tanımlı olduğundan emin olun
                if (Array.isArray(fields)) {
                    fields.forEach(field => {
                        html += createFormGroup(field.labelName, `product${field.inputName}`, `${field.inputName}-${variantID}`);
                    });
                } else {
                    console.error("'fields' değişkeni tanımlı değil veya geçersiz formatta.");
                }

                html += `<input type="hidden" name="variantID[]" value="${variantID}">`;

                variant.forEach(item => {
                    html += `<input type="hidden" name="variantProperties[${variantID}]" value="${item.variantName}|${item.variantValue}">`;
                });

                html += '</div>';
                variantContainer.append(html);
                console.log(`Varyant (${variantID}) başarıyla oluşturuldu.`);
            } else {
                console.warn(`Varyant (${variantID}) zaten mevcut, atlanıyor.`);
            }
        }

        // Belirli bir ID'ye sahip öğeyi kaldırmaya çalışıyorsunuz ancak muhtemelen yanlış ID kullanıyorsunuz
        // $("#variant-").remove(); // Bu satır hatalı olabilir, gerekirse düzeltin veya kaldırın
    });


    $('#variantContainer').sortable({
        handle: '.dragDropVariant', // Sürükleme işlemi için kullanılacak eleman
        axis: 'y', // Y ekseni boyunca sıralama
        update: function (event, ui) {
            // Sıralama güncellendiğinde yapılacak işlemler
            console.log("Öğeler sıralandı.");
        }
    });

    //.bootstrap-tagsinput altındaki lanel tıklanınca inputa yazalım
    $(document).on("click", ".bootstrap-tagsinput .tag", function () {
        $tagText = $(this).text();
        let $currentVal = $("#productSeoKeywords").val();
        if ($currentVal.indexOf($tagText) == -1) {
            //tagsinput fonksiyonumuz olmadığı için elle ekleyelim mevcut değerlere virgül ile ekleyelim
            $currentVal += ", " + $tagText;
            $("#productSeoKeywords").val($currentVal);
            $(this).remove();
        }
    });

    //resim arama #imageName klavyeden 3 harf yazılırsa arama başlatalım
    $(document).on('keyup', '#searchImageName', function () {
        $imageName = $(this).val();
        if ($imageName.length > 2) {
            $.ajax({
                type: 'GET',
                url: "/App/Controller/Admin/AdminImageController.php?action=getImagesBySearch&searchText=" + $imageName,
                dataType: 'json',
                success: function (data) {
                    $data = data;
                    if ($data.status === "success") {
                        $html = "";
                        for ($i = 0; $i < $data.images.length; $i++) {
                            $imageID = $data.images[$i].imageID;
                            $imagePath = $data.images[$i].imagePath;
                            $imageName = $data.images[$i].imageName;
                            $imageWidth = $data.images[$i].imageWidth;
                            $imageHeight = $data.images[$i].imageHeight;
                            $imageFolderName = $data.images[$i].imageFolderName;

                            $html += '<li class="tile">' +
                                '<a class="tile-content ink-reaction selectImage"' +
                                'data-imageid="' + $imageID + '"' +
                                'data-imagepath="' + $imageFolderName + '/' + $imagePath + '"' +
                                'data-imagename="' + $imageName + '"' +
                                'data-imagewidth="' + $imageWidth + '"' +
                                'data-imageheight="' + $imageHeight + '"' +
                                'data-backdrop="false" style="cursor:pointer;">' +
                                '<div class="tile-icon">' +
                                '<img src="' + imgRoot + '?imagePath=' + $imageFolderName + '/' + $imagePath + '&width=100&height=100" alt="" />' +
                                '</div>' +
                                '<div class="tile-text">' +
                                $imageName +
                                '<small>' + $imageFolderName + '</small>' +
                                '</div>' +
                                '</a>' +
                                '</li>';

                        }
                        $("#rightImageListContainer").html($html);
                    }
                }
            });
        }
    });

    //dosya arama #fileName klavyeden 3 harf yazılırsa arama başlatalım
    $(document).on('keyup', '#searchFileName', function () {
        $fileName = $(this).val();
        if ($fileName.length > 2) {
            $.ajax({
                type: 'GET',
                url: "/App/Controller/Admin/AdminFileController.php?action=getFilesBySearch&searchText=" + $fileName,
                dataType: 'json',
                success: function (data) {
                    $data = data;
                    if ($data.status === "success") {
                        $html = "";
                        for ($i = 0; $i < $data.files.length; $i++) {
                            $fileID = $data.files[$i].fileID;
                            $filePath = $data.files[$i].filePath;
                            $fileName = $data.files[$i].fileName;
                            $fileExtension = $data.files[$i].fileExtension;
                            $fileFolderName = $data.files[$i].fileFolderName;
                            $fileImage = fileRoot + "?fileExtension=" + $fileExtension + ".png";

                            $html += '<li class="tile">' +
                                '<a class="tile-content ink-reaction selectFile"' +
                                'data-fileid="' + $fileID + '"' +
                                'data-filepath="' + $filePath + '"' +
                                'data-filename="' + $fileName + '"' +
                                'data-fileextension="' + $fileExtension + '"' +
                                'data-backdrop="false" style="cursor:pointer;">' +
                                '<div class="tile-icon">' +
                                '<img src="' + $fileImage + '.png" alt="' + $fileName + '" />' +
                                '</div>' +
                                '<div class="tile-text">' +
                                $fileName +
                                '<small>' + $fileExtension + '</small>' +
                                '</div>' +
                                '</a>' +
                                '</li>';

                        }
                        $("#rightFileListContainer").html($html);
                    }
                }
            });
        }
    });

    //#selectImageByRightCanvas tıklandığında data-target değerini alıp #imageTarget'a atayalım
    $(document).on("click", "#selectImageByRightCanvas, #addImageByRightCanvas", function () {
        $imageTarget = $(this).data("target");

        $("#imageTarget").val($imageTarget);
    });

    //#uploadImageByLeftCanvas tıklandığında data-uploadtarget değerini alıp #imageFolder'a atayalım
    $(document).on("click", "#uploadImageByLeftCanvas, #addImageByLeftCanvas", function () {
        $imageTarget = $(this).data("target");

        $("#imageTarget").val($imageTarget);

        $uploadTarget = $(this).data("uploadtarget");

        $("#imageFolder").val($uploadTarget);
    });

    //imageBox
    const $imageBox = '<div class="col-md-1 text-center imageBox" style="cursor:grab" id="imageBox_[imageID]">' +
        '<input type="hidden" name="imageID[]" value="[imageID]">' +
        '<div class="tile-icond">' +
        '<img id="image_[imageID]" class="size-2" src="' + imgRoot + '?imagePath=[imagePath]&width=100&height=100" alt="[imageName]">' +
        '</div>' +
        '<div class="tile-text">' +
        '<a class="btn btn-floating-action ink-reaction removeImage" data-imageBox="imageBox_[imageID]" data-id="[imageID]" data-toggle="modal" data-target="#removeImageModal" title="Kaldır">' +
        '<i class="fa fa-trash"></i>' +
        '</a>' +
        '</div>' +
        '</div>';

    $(document).on("click", ".selectImage", function () {

        $imageTarget = $("#imageTarget").val();

        $imageID = $(this).data("imageid");
        $imagePath = $(this).data("imagepath");
        $imageName = $(this).data("imagename");
        $imageWidth = $(this).data("imagewidth");
        $imageHeight = $(this).data("imageheight");

        if ($imageTarget === "productContent") {

            //genişliğe göre yükseklik ayarlayalım
            $imageNewWidth = 300;
            $imageNewHeight = Math.round($imageHeight / $imageWidth * $imageNewWidth);

            let summernote = $('#productContent').summernote();
            let editorData = summernote.code();
            summernote.code(editorData + '<img src="' + imgRoot + '?imagePath=' + $imagePath + '&width=' + $imageNewWidth + '&height=' + $imageNewHeight + '" title="' + $imageName + '" width="' + $imageNewWidth + '" height="' + $imageNewHeight + '" >');


        } else {

            $html = $imageBox;
            $html = $html.replaceAll("[imageID]", $imageID);
            $html = $html.replaceAll("[imagePath]", $imagePath);
            $html = $html.replaceAll("[imageName]", $imageName);

            $("#imageContainer").append($html);
        }
    });

    Dropzone.options.imageDropzone = {
        parallelUploads: 10,
        autoProcessQueue: true,
        addRemoveLinks: true,
        maxFiles: 10,
        maxFilesize: 150,
        dictDefaultMessage: "Resimleri yüklemek için bırakın",
        dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
        dictFallbackText: "Resimleri eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
        dictFileTooBig: "Resim çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
        dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
        dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
        dictCancelUpload: "İptal Et",
        dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
        dictRemoveFile: "Resim Sil",
        dictRemoveFileConfirmation: null,
        dictMaxFilesExceeded: "Daha fazla resim yükleyemezsiniz.",
        acceptedFiles: ".jpeg,.jpg,.png,.webp",
        //resimler adı imageName inputu boşsa yükleme yapmayalım
        accept: function (file, done) {

            var imageName = $("#imageName").val();

            if (imageName === "") {

                $("#runImageDropzoneContainer").removeClass("hidden");
                $("#imageName").parent().addClass("bg-danger");

            } else {

                $("#formImageName").val(imageName);
                done();
            }

            $("#runImageDropzone").on("click", function (e) {

                var imageName = $("#imageName").val();
                if (imageName === "") {

                    $("#imageName").focus();

                } else {

                    $("#formImageName").val(imageName);

                    done();
                }
            });


        },
        removedfile: function (file) {
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
        init: function () {

            this.on("success", function (file, responseText) {

                //console.log(responseText);

                var responseObject = JSON.parse(responseText);

                $status = responseObject.status;
                //console.log("status:"+$status);

                if ($status === "success") {
                    //resim bilgileri imageResults içinde dönüyor, birden fazla olabilir
                    $imageResults = responseObject.imageResults;
                    //console.log($imageResults);

                    $imageTarget = $("#imageTarget").val();

                    for ($i = 0; $i < $imageResults.length; $i++) {
                        $imageID = $imageResults[$i].imageData.imageID;
                        $imagePath = $imageResults[$i].imageData.imageFolderName + "/" + $imageResults[$i].imageData.imagePath;
                        $imageName = $imageResults[$i].imageData.imageName;
                        $imageWidth = $imageResults[$i].imageData.imageWidth;
                        $imageHeight = $imageResults[$i].imageData.imageHeight;

                        if ($imageTarget === "productContent") {

                            //genişliğe göre yükseklik ayarlayalım
                            $imageNewWidth = 300;
                            $imageNewHeight = Math.round($imageHeight / $imageWidth * $imageNewWidth);

                            let summernote = $('#productContent').summernote();
                            let editorData = summernote.code();
                            summernote.code(editorData + '<img src="' + imgRoot + '?imagePath=' + $imagePath + '&width=' + $imageNewWidth + '&height=' + $imageNewHeight + '" title="' + $imageName + '" width="' + $imageNewWidth + '" height="' + $imageNewHeight + '" >');

                        } else {

                            $html = $imageBox;
                            $html = $html.replaceAll("[imageID]", $imageID);
                            $html = $html.replaceAll("[imagePath]", $imagePath);
                            $html = $html.replaceAll("[imageName]", $imageName);

                            $("#imageContainer").append($html);
                        }
                    }

                    //dropzone'a eklenen resimleri silelim
                    this.removeAllFiles();
                    //offcanvas kapat
                    $("#offcanvas-imageUploadOff").click();
                } else {
                    //hata mesajını burada işleyebilirsiniz
                    console.log(responseText);
                }

            });
            this.on("error", function (file, responseText) {
                // Hata mesajını burada işleyebilirsiniz
                console.log(responseText);
            });
        }
    };

    //.removeImage linkini dinleyelim
    $(document).on("click", ".removeImage", function () {
        var targetImageBox = $(this).data("imagebox");
        console.log("remove target: " + targetImageBox);

        // removeImageButton tıklanınca targetImageBox'ı silelim
        $(document).on('click', '#removeImageButton', function () {
            console.log("remove: " + targetImageBox);
            $("#" + targetImageBox).remove();
            $("#removeImageModal").modal("hide");
        });
    });

    //#removeAllImages tıklanınca tüm resimleri silelim
    $(document).on("click", "#removeAllImages", function () {
        $("#removeAllImageModal").modal("show");
    });

    //#removeAllImageButton tıklanınca tüm resimleri silelim
    $(document).on("click", "#removeAllImageButton", function () {
        $(".imageBox").remove();
        $("#removeAllImageModal").modal("hide");
    });

    //fileBox
    $fileBox = '<div class="col-md-1 text-center fileBox" style="cursor:grab" id="fileBox_[fileID]">' +
        '<input type="hidden" name="fileID[]" value="[fileID]">' +
        '<div class="tile-icond">' +
        '<img id="file_[fileID]" class="size-2" src="[fileImage]" alt="[fileName]">' +
        '</div>' +
        '<div class="tile-text"> [fileName] ' +
        '<a class="btn btn-floating-action ink-reaction removeFile" data-fileBox="fileBox_[fileID]" data-id="[fileID]" data-toggle="modal" data-target="#removeFileModal" title="Kaldır">' +
        '<i class="fa fa-trash"></i>' +
        '</a>' +
        '</div>' +
        '</div>';

    //#selectImageByRightCanvas tıklandığında data-target değerini alıp #imageTarget'a atayalım
    $(document).on("click", "#selectFileByRightCanvas, #addFileByRightCanvas", function () {
        $fileTarget = $(this).data("target");

        $("#fileTarget").val($fileTarget);
    });

    $(document).on("click", "#uploadFileByLeftCanvas, #addFileByLeftCanvas", function () {
        $fileTarget = $(this).data("target");

        $("#fileTarget").val($fileTarget);

        $uploadTarget = $(this).data("uploadtarget");

        $("#fileFolder").val($uploadTarget);
    });

    $(document).on("click", ".selectFile", function () {

        $fileTarget = $("#fileTarget").val();

        $fileID = $(this).data("fileid");
        $filePath = $(this).data("filepath");
        $fileName = $(this).data("filename");
        $fileExtension = $(this).data("fileextension");
        $fileImage = fileRoot + '?fileExtension=' + $fileExtension;

        if ($fileTarget === "productContent") {

            //dosyayı uzantısına göre görsele çevirelim ve bağlantı oluşturalım
            $fileHtml = '<a href="' + $fileImage + '" class="fileLink" target="_blank">' +
                '<img src="' + $fileImage + '" alt="' + $fileName + '" title="' + $fileName + '">' +
                '</a>';

            let summernote = $("#productContent").summernote();
            let editorData = summernote.code();
            summernote.code(editorData + $fileHtml);

        } else {

            $html = $fileBox;
            $html = $html.replaceAll("[fileID]", $fileID);
            $html = $html.replaceAll("[filePath]", $filePath);
            $html = $html.replaceAll("[fileName]", $fileName);
            $html = $html.replaceAll("[fileExtension]", $fileExtension);
            $html = $html.replaceAll("[fileImage]", $fileImage);

            $("#fileContainer").append($html);
        }
    });

    Dropzone.options.fileDropzone = {
        parallelUploads: 10,
        autoProcessQueue: true,
        addRemoveLinks: true,
        maxFiles: 10,
        maxFilesize: 150,
        dictDefaultMessage: "Dosyaları yüklemek için bırakın",
        dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
        dictFallbackText: "Dosyaları eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
        dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
        dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
        dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
        dictCancelUpload: "İptal Et",
        dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
        dictRemoveFile: "Dosya Sil",
        dictRemoveFileConfirmation: null,
        dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
        acceptedFiles: ".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.csv,.xml,.excel,.odf,.odp",
        //dosyalar adı fileName inputu boşsa yükleme yapmayalım
        accept: function (file, done) {

            var fileName = $("#fileName").val();

            if (fileName === "") {

                $("#runFileDropzoneContainer").removeClass("hidden");
                $("#fileName").parent().addClass("bg-danger");
                //done("Dosya adını giriniz");
            } else {

                $("#formFileName").val(fileName);
                done();
            }

            $("#runFileDropzone").on("click", function (e) {

                var fileName = $("#fileName").val()

                if (fileName === "") {

                    $("#fileName").focus();

                    console.log("Dosya adını giriniz");
                } else {

                    $("#formFileName").val(fileName);
                    console.log("Dosya adı girildi");
                    done();
                }
            });


        },
        removedfile: function (file) {
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
        init: function () {

            this.on("success", function (file, responseText) {

                //console.log(responseText);

                var responseObject = JSON.parse(responseText);

                $status = responseObject.status;
                //console.log("status:"+$status);

                if ($status === "success") {
                    //dosya bilgileri fileResults içinde dönüyor, birden fazla olabilir
                    $fileResults = responseObject.fileResults;
                    //console.log($fileResults);

                    $fileTarget = $("#fileTarget").val();

                    for ($i = 0; $i < $fileResults.length; $i++) {
                        $fileID = $fileResults[$i].fileData.fileID;
                        $fileName = $fileResults[$i].fileData.fileName;
                        $fileExtension = $fileResults[$i].fileData.fileExtension;
                        $fileFolderName = $fileResults[$i].fileData.fileFolderName;
                        $fileImage = fileRoot + "?fileExtension=" + $fileExtension;
                        $filePath = $fileFolderName + "/" + $fileResults[$i].fileData.filePath;


                        if ($fileTarget === "productContent") {

                            //dosyayı uzantısına göre görsele çevirelim ve bağlantı oluşturalım
                            $fileHtml = '<a href="' + fileRoot + $filePath + '" class="fileLink" target="_blank">' +
                                '<img src="' + $fileImage + '" alt="' + $fileName + '" title="' + $fileName + '">' +
                                '</a>';

                            let summernote = $("#productContent").summernote();
                            let editorData = summernote.code();
                            summernote.code(editorData + $fileHtml);

                        } else {

                            $html = $fileBox;
                            $html = $html.replaceAll("[fileID]", $fileID);
                            $html = $html.replaceAll("[filePath]", $filePath);
                            $html = $html.replaceAll("[fileName]", $fileName);
                            $html = $html.replaceAll("[fileExtension]", $fileExtension);
                            $html = $html.replaceAll("[fileImage]", $fileImage);

                            $("#fileContainer").append($html);
                        }
                    }

                    //dropzone'a eklenen dosyaları silelim
                    this.removeAllFiles();
                    //offcanvas kapat
                    $("#offcanvas-fileUploadOff").click();
                } else {
                    //hata mesajını burada işleyebilirsiniz
                    console.log(responseText);
                }

            });
            this.on("error", function (file, responseText) {
                // Hata mesajını burada işleyebilirsiniz
                console.log(responseText);
            });
        }
    };

    //.variantGroupSelect checkbox değişimini izleyelim. true olursa val değerini alıp ilgili gruba bağlı varyantları getirelim
    $(document).on("change", ".variantGroupSelect", async function () {

        $languageCode = $("#languageID option:selected").data("languagecode");

        $variantGroupID = $(this).data("id");

        $variantGroupChecked = $(this).is(":checked");

        $variantUniqID = $(this).data("uniqid");

        $searchVariantInput = $("#searchVariant-" + $variantUniqID)

        if ($variantGroupChecked) {
            await new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: "/App/Controller/Admin/AdminProductVariantController.php?action=getVariantsByGroupID&variantGroupID=" + $variantGroupID + "&languageCode=" + $languageCode,
                    dataType: 'json',
                    success: function (data) {
                        $data = data;
                        if ($data.status === "success") {
                            $variants = $data.data;
                            $searchVariantInput.removeClass("disabled");
                            $searchVariantInput.prop("disabled", false);
                            $("#variantContainer-" + $variantUniqID).html("");
                            $("#variantContainer-" + $variantUniqID).removeClass("hidden");

                            $html = "";
                            for ($i = 0; $i < $variants.length; $i++) {
                                $variantID = $variants[$i].variantID;
                                $variantName = $variants[$i].variantName;
                                $variantGroupID = $variants[$i].variantGroupID;

                                $html = '<div class="form-group">' +
                                    '<div class="checkbox checkbox-styled">' +
                                    '<label for="variantCheckBox-' + $variantID + '" class="col-sm-12 control-label opacity-100">' +
                                    '<input class="variantSelect" name="variantName" id="variantCheckBox-' + $variantID + '" data-id="' + $variantID + '" type="checkbox" value="' + $variantName + '">' +
                                    '<span class="variantName">' + $variantName + '</span>' +
                                    '</label>' +
                                    '</div>' +
                                    '</div>';

                                $("#variantContainer-" + $variantUniqID).append($html);
                            }
                            resolve();
                        }
                    },
                    error: function (error) {
                        reject(error);
                    }
                });
            });
        } else {
            //$("#variantContainer-" + $variantUniqID) içinde ne kadar checkbox varsa hepsini unchecked yapalım
            $("#variantContainer-" + $variantUniqID + " input[type='checkbox']").prop("checked", false);
            $("#variantContainer-" + $variantUniqID).html("");
        }

    });

    //.searchVariant dinleyelim data-id'sini alalım.variantContainer-id içinde arama yapalım
    $(document).on("keyup", ".searchVariant", function () {
        $searchText = $(this).val();
        $variantUniqID = $(this).data("id");

        $variantContainer = $("#variantContainer-" + $variantUniqID);

        $variantContainer.find(".variantSelect").each(function () {
            $variantName = $(this).next().text();
            $variantID = $(this).val();

            if ($variantName.toLowerCase().indexOf($searchText.toLowerCase()) > -1) {
                $(this).parent().parent().parent().removeClass("hidden");
            } else {
                $(this).parent().parent().parent().addClass("hidden");
            }
        });
    });

    $(".variantContainerContainer .ui-sortable").sortable({

        start: function (event, ui) {
            let variantID = $(this).data("variantid");
            let variantBox = $("#variant-" + variantID);
            variantBox.addClass("hidden");
            ui.item.css({
                "background-color": "rgba(255,255,0,0)", // Sürüklenmeye başlandığında arka plan rengini kırmızı yap
                "height": "50px", // Sürüklenmeye başlandığında yüksekliği değiştir
                "overflow": "hidden", // Sürüklenmeye başlandığında taşan kısımları gizle
                "boxShadow": "0 0 10px 0 rgba(0,0,0,.5)" // Sürüklenmeye başlandığında gölge ekle
            });
        },
        stop: function (event, ui) {
            ui.item.css({
                "background-color": "", // Sürüklenme bittiğinde arka plan rengini orijinal haline döndür
                "height": "", // Sürüklenme bittiğinde yüksekliği orijinal haline döndür
                "overflow": "", // Sürüklenme bittiğinde taşan kısımları göster
                "boxShadow": "0 0 0 0 rgba(0,0,0,0)"
            });
        }
    });

    $(document).on("click", ".selectProperty", function () {
        $propertyID = $(this).data("id");
        $propertyName = $(this).data("name");
        $propertyValue = $(this).data("value");

        $html = '<li class="tile" id="property_' + $propertyID + '">' +
            '<a class="tile-content ink-reaction removeProperty" data-id="' + $propertyID + '">' +
            '<div class="tile-icon">' +
            '<i class="fa fa-trash text-danger"></i>' +
            '</div>' +
            '<div class="tile-text">' +
            $propertyName +
            '<small>' + $propertyValue + '</small>' +
            '</div>' +
            '</a>' +
            '</li>';

        $("#addedProperties").append($html);
    });

    $(document).on("click", ".removeProperty", function () {
        $propertyID = $(this).data("id");
        $("#property_" + $propertyID).remove();
    });
    /*
    $(document).on("click", ".dragDropVariant", function () {
        $variantID = $(this).data("variantid");
        $variant = $("#variant-" + $variantID);
        $variant.toggleClass("bg-success");
    });
    */
    $(document).on("click", ".removeVariant", function () {
        $variantID = $(this).data("variantid");
        //varyan divinin etrafında kırmızı çizgi oluşturalım, 100 milisaniye sonra da silelim
        $("#variant-" + $variantID).css("border", "1px solid red");

        $("#variant-" + $variantID).css("zoom", "0.85");
        $("#variant-" + $variantID).css("opacity", "0.55");

        setTimeout(function () {
            $("#variant-" + $variantID).remove();
            if ($("#variantContainer").children().length == 0) {
                $("#isVariant").prop("checked", false).trigger("change");
                $("#variantContainer").append(variantDiv);
            }
        }, 100);
        //eğer son div de silinmiş ise #isVariant checkbox'ını unchecked yapalım

    });

    $(document).on("keyup", "#addProductProperties", function (e) {

        if (e.which === 13) {
            //console.log("enter");
            $(".addProductPropertiesButton").click();
        }

        $searchText = $(this).val();

        if ($searchText.length > 2) {
            $languageCode = $("#languageID option:selected").data("languagecode");
            //console.log($searchText);
            $.ajax({
                type: 'GET',
                url: "/App/Controller/Admin/AdminProductPropertiesController.php?action=searchProductProperty&searchText=" + $searchText + "&languageCode=" + $languageCode,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    $data = data;
                    if ($data.status === "success") {
                        $properties = $data.data;
                        $("#productPropertiesResults").html("");
                        $("#productPropertiesResults").removeClass("hidden");
                        //arama kapatma butonu ekleyelim
                        $html = '<li class="tile">' +
                            '<a class="tile-content ink-reaction closeProductPropertiesResults" style="cursor:pointer">' +
                            '<div class="tile-icon">' +
                            '<i class="fa fa-times"></i>' +
                            '</div>' +
                            '</a>' +
                            '</li>';
                        $("#productPropertiesResults").append($html);
                        for ($i = 0; $i < $properties.length; $i++) {
                            $propertyID = $properties[$i].productPropertyID;
                            $propertyName = $properties[$i].productPropertyName;
                            $propertyValue = $properties[$i].productPropertyValue;

                            $html = '<li class="tile li" id="property_' + $propertyID + '">' +
                                '<input type="hidden" name="productProperties[]" class="getProductProperties" value="' + $propertyName + ':' + $propertyValue + '">' +
                                '<div class="col-sm-8">' +
                                '<div class="tile-text">' + $propertyName + ':' + $propertyValue + '</div>' +
                                '</div>' +
                                '<div class="col-sm-4">' +
                                '<a class="tile-content ink-reaction dragDropProperty" style="cursor:grab">' +
                                '<div class="tile-icon">' +
                                '<i class="fa fa-arrows"></i>' +
                                '</div>' +
                                '</a>' +
                                '<a class="tile-content ink-reaction removeProperty" style="cursor: pointer" data-id="' + $propertyID + '">' +
                                '<div class="tile-icon">' +
                                '<i class="fa fa-trash text-danger"></i>' +
                                '</div>' +
                                '</a>' +
                                '</div>' +
                                '</li>';


                            $("#productPropertiesResults").append($html);
                        }
                    }
                }
            });
        }
    });

    //closeProductPropertiesResults
    $(document).on("click", ".closeProductPropertiesResults", function () {
        $("#productPropertiesResults").html("");
        $("#productPropertiesResults").addClass("hidden");
        $("#addProductProperties").val("");
    });

    //addProductProperties arama sonucu li tıklandığında
    $(document).on("click", "#productPropertiesResults li.tile.li", function () {
       //li'yi doğrudan #addedProperties içine appen edelim. Arama sonucunu temizleyip hidden yapalım. value'yi temizleyelim
        var li = $(this).clone();
        $("#addedProperties").append(li);
        $("#productPropertiesResults").html("");
        $("#productPropertiesResults").addClass("hidden");
        $("#addProductProperties").val("");
    });

    $(document).on("click", ".addProductPropertiesButton", function () {
        $property = $("#addProductProperties").val();
        //console.log($property);
        if ($property.indexOf(":") > -1) {
            //console.log($property);
            $propertyArray = $property.split(":");
            $propertyName = $propertyArray[0];
            $propertyValue = $propertyArray[1];
            //5 haneli random sayı
            $propertyID = Math.floor(Math.random() * 90000) + 10000;

            $html = '<li class="tile" id="property_' + $propertyID + '">' +
                '<input type="hidden" name="productProperties[]" class="getProductProperties" value="' + $propertyName + ':' + $propertyValue + '">' +
                '<div class="col-sm-8">' +
                '<div class="tile-text">' + $propertyName + ':' + $propertyValue + '</div>' +
                '</div>' +
                '<div class="col-sm-4">' +
                '<a class="tile-content ink-reaction dragDropProperty" style="cursor:grab">' +
                '<div class="tile-icon">' +
                '<i class="fa fa-arrows"></i>' +
                '</div>' +
                '</a>' +
                '<a class="tile-content ink-reaction removeProperty" style="cursor: pointer" data-id="' + $propertyID + '">' +
                '<div class="tile-icon">' +
                '<i class="fa fa-trash text-danger"></i>' +
                '</div>' +
                '</a>' +
                '</div>' +
                '</li>';

            $("#addedProperties").append($html);
            $("#addProductProperties").val("");
        }
    });

    //$("#productModel") dinleyelim
    $(document).on("keyup", "#productModel", function () {
        $searchText = $(this).val();

        if ($searchText.length > 0) {
            //ajax ile ürün model kontrolü yapalım
            var action = "getProductModels";
            $.ajax({
                url: "/App/Controller/Admin/AdminProductController.php",
                type: "POST",
                data: {searchText: $searchText, action: action},
                success: function (data) {
                    console.log(data);
                    //json gelecek: status,message, models
                    $data = JSON.parse(data);

                    if ($data.status == "success") {

                        //{"status":"success","message":"Models found","models":[{"urunmodel":"6396"},{"urunmodel":"797"},
                        var models = $data.models;
                        var modelRow = '';
                        models.forEach(function (model) {
                            modelRow += '<li class="tile"><div class="tile-text"><a class="tile-content ink-reaction">' + model.urunmodel + '</a></div></li>';
                        });

                        $("#modelSearchResult").html('<ul class="list">' + modelRow + '</ul>');
                        $("#modelSearchResult").removeClass("hidden");
                    } else {
                        $("#modelSearchResult").html("");
                        $("#modelSearchResult").addClass("hidden");
                    }
                }
            });
        }
    });

    //modelSearchResult a click dinleyelim tıklanan metni model alanına yazdırıp arama sonucunu temizleyip gizleyelim

    $(document).on("click", "#modelSearchResult .tile", function () {
        $model = $(this).find(".tile-content").text();
        $("#productModel").val($model);
        $("#modelSearchResult").html("");
        $("#modelSearchResult").addClass("hidden");
    });

    //ürün adını keyup ile dinleyelim, seoTitle'ı dolduralım
    $(document).on("keyup", "#productName", function () {
        $productName = $(this).val();
        //$seoTitle = $productName.toLowerCase().replace(/ /g, "-");
        $("#productSeoTitle").val($productName);

        //ürün adından productLink'i oluşturalım örn: ürün adı: "Kırmızı Elbise" ise link: "/kirmizi-elbise" seo ile uyumlu hale getirmek için createSeoLink fonksiyonunu kullanalım
        $productLink = "/" + createSeoLink($productName);
        $("#productLink").val($productLink);

    });

    $(document).on("click", "a[href='#tabSeoSettings']", function () {
        //ürün adını keyup ile dinleyelim, seoTitle'ı dolduralım
        processVariantGroupNames();
        processProductProperties();
        createSeoDescription();

        var productLink = $("#productLink").val();
        if (productLink == "") {
            $("#productName").trigger("keyup");
        }

    });

    $(document).on('change', '.variantContainer input[type="checkbox"]', function () {
        moveSelectedCheckboxes();
    });

    let variantDiv = `
        <div class="row" id="variant-no-variant">
            <div class="getVariantGroupName col-sm-8 form-group text-bold text-primary"></div>
            <div class="col-sm-4 form-group text-right">
                <a class="btn btn-floating-action ink-reaction dragDropVariant ui-sortable-handle" data-variantid="no-variant" title="Sırala">
                    <i class="fa fa-arrows"></i>
                </a>
                <a class="btn btn-floating-action ink-reaction removeVariant" data-variantid="no-variant" title="Kaldır">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
            <div class="col-sm-2 form-group">
                <label for="StockCode-no-variant">Stok Kodu</label>
                <input type="text" name="productStockCode[]" id="StockCode-no-variant" class="form-control" required="" value="">
            </div>
            <div class="col-sm-2 form-group">
                <label for="GTIN-no-variant">GTIN</label>
                <input type="text" name="productGTIN[]" id="GTIN-no-variant" class="form-control" required="" value="">
            </div>
            <div class="col-sm-2 form-group">
                <label for="MPN-no-variant">MPN</label>
                <input type="text" name="productMPN[]" id="MPN-no-variant" class="form-control" required="" value="">
            </div>
            <div class="col-sm-2 form-group">
                <label for="Barcode-no-variant">Barkod</label>
                <input type="text" name="productBarcode[]" id="Barcode-no-variant" class="form-control" required="" value="">
            </div>
            <div class="col-sm-2 form-group">
                <label for="OEM-no-variant">OEM</label>
                <input type="text" name="productOEM[]" id="OEM-no-variant" class="form-control" required="" value="">
            </div>
            <div class="col-sm-2 form-group">
                <label for="Stock-no-variant">Stok Adeti</label>
                <input type="text" name="productStock[]" id="Stock-no-variant" class="form-control" required="" value="0">
            </div>
            <div class="col-sm-2 form-group">
                <label for="SalePrice-no-variant">Satış Fiyatı</label>
                <input type="text" name="productSalePrice[]" id="SalePrice-no-variant" class="form-control" required="" value="0">
            </div>
            <div class="col-sm-2 form-group">
                <label for="DiscountPrice-no-variant">İndirimsiz Satış Fiyatı</label>
                <input type="text" name="productDiscountPrice[]" id="DiscountPrice-no-variant" class="form-control" required="" value="0">
            </div>
            <div class="col-sm-2 form-group">
                <label for="DealerPrice-no-variant">Bayi Fiyatı</label>
                <input type="text" name="productDealerPrice[]" id="DealerPrice-no-variant" class="form-control" required="" value="0">
            </div>
            <div class="col-sm-2 form-group">
                <label for="PurchasePrice-no-variant">Alış Fiyatı</label>
                <input type="text" name="productPurchasePrice[]" id="PurchasePrice-no-variant" class="form-control" required="" value="0">
            </div>
        </div>
        `;

    //productGroupID değiştiğinde grup özelliklerini getir
    $(document).on("change", "#productGroupID", function () {
        $productGroupID = $(this).val();
        if($productGroupID == ""){
            return;
        }

        $.ajax({
            type: 'GET',
            url: "/App/Controller/Admin/AdminProductGroupController.php?action=getProductGroup&groupID=" + $productGroupID,
            dataType: 'json',
            success: function (data) {
                //console.log(data);

                $data = data;
                if ($data.status === "success") {
                    $group = $data.data;

                    let productDiscountRate = $group.productDiscountRate;
                    $("#productDiscountRate").val(productDiscountRate);

                    let productGroupDeliveryTime = $group.productGroupDeliveryTime;
                    $("#productCargoTime").val(productGroupDeliveryTime);

                    let productGroupProductDescription = $group.productGroupProductDescription;
                    $("#productDescription").val(productGroupProductDescription);

                    let productShortDesc = $group.productShortDesc;
                    $("#productShortDesc").val(productShortDesc);

                    let productTaxRate = $group.productTaxRate;
                    $("#productTax").val(productTaxRate);
                }
            }
        });
    });
    
});