$(document).on("click", ".selectImage", function (e) {
    e.preventDefault();
    e.stopPropagation();
    
    let imageTargetVal = $("#imageTarget").val();
    console.log(imageTargetVal);

    $imageID = $(this).data("imageid");
    $imagePath = $(this).data("imagepath");
    $imageName = $(this).data("imagename");
    $imageWidth = $(this).data("imagewidth");
    $imageHeight = $(this).data("imageheight");

    $("#"+imageTargetVal +" img").attr("src", imgRoot + $imagePath );
    $("#"+imageTargetVal +" #bannerImage").val($imagePath);

    $("#selectImageByRightCanvas2").click();
    // Banner önizleme güncellemesi için trigger
    $(document).trigger('bannerImageChanged', [imageTargetVal, $imagePath]);
});

Dropzone.options.imageDropzone = {
    parallelUploads: 1,
    autoProcessQueue: true,
    addRemoveLinks: true,
    maxFiles: 1,
    maxFilesize: 3,
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

        let imageName = $("#imageName");
        let imageNameVal = imageName.val();

        if (imageNameVal === "") {

            $("#runImageDropzoneContainer").removeClass("hidden");
            imageName.parent().addClass("bg-danger");

        } else {

            $("#formImageName").val(imageNameVal);
            done();
        }

        $("#runImageDropzone").on("click", function () {

            if (imageNameVal === "") {

                imageName.focus();

            } else {

                $("#formImageName").val(imageNameVal);

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

            console.log(responseText);

            var responseObject = JSON.parse(responseText);

            $status = responseObject.status;
            //console.log("status:"+$status);

            if ($status === "success") {
                //resim bilgileri imageResults içinde dönüyor, birden fazla olabilir
                $imageResults = responseObject.imageResults;
                //console.log($imageResults);
                imageTargetVal = $("#imageTarget").val();
                console.log(imageTargetVal);

                for ($i = 0; $i < $imageResults.length; $i++) {
                    $imageID = $imageResults[$i].imageData.imageID;
                    $imagePath = $imageResults[$i].imageData.imageFolderName + "/" + $imageResults[$i].imageData.imagePath;
                    $imageName = $imageResults[$i].imageData.imageName;
                    $imageWidth = $imageResults[$i].imageData.imageWidth;
                    $imageHeight = $imageResults[$i].imageData.imageHeight;                    $("#"+imageTargetVal +" img").attr("src", imgRoot + $imagePath );
                    $("#"+imageTargetVal +" #bannerImage").val($imagePath);
                }

                //dropzone'a eklenen resimleri silelim
                this.removeAllFiles();
                //offcanvas kapat
                $("#offcanvas-imageUploadOff").click();
                // Banner önizleme güncellemesi için trigger
                $(document).trigger('bannerImageChanged', [imageTargetVal, $imagePath]);
            }
            else {
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

//#selectImageByRightCanvas tıklandığında data-target değerini alıp #imageTarget'a atayalım
$(document).on("click", ".selectImageByRightCanvas", function (e) {
    e.preventDefault();
    e.stopPropagation();
    
    let imageTargetVal = $(this).data("target");
    $("#imageTarget").val(imageTargetVal);
    console.log("imageTargetVal: " + imageTargetVal);

    // Canvas açmadan önce bir miktar gecikme
    setTimeout(function() {
        $("#selectImageByRightCanvas2").click();
    }, 100);
});

//#uploadImageByLeftCanvas tıklandığında data-uploadtarget değerini alıp #imageFolder'a atayalım
$(document).on("click", ".uploadImageByLeftCanvas", function (e) {
    e.preventDefault();
    e.stopPropagation();
    
    let imageTargetVal = $(this).data("target");
    $("#imageTarget").val(imageTargetVal);
    console.log("imageTargetVal: " + imageTargetVal);

    $uploadTarget = $(this).data("uploadtarget");
    $("#imageFolder").val($uploadTarget);

    // Canvas açmadan önce bir miktar gecikme
    setTimeout(function() {
        $("#uploadImageByLeftCanvas2").click();
    }, 100);
});

//banner görselini sil
$(document).on("click", ".removeBannerImage", function(){
    let dataID = $(this).data("id");
    $("#card-panel-"+dataID+" #bannerImage").val("");
    $("#card-panel-"+dataID+" img").attr("src", "<?=$bannerBaseImage?>");
    console.log("card-panel-"+dataID+" görseli silindi");
});