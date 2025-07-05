$(function () {
    $('#productImages').FancyFileUpload({
        // Dosya yükleme URL'si
        url: 'upload_file.php',

        // Dosya yükleme yöntemi
        method: 'POST',

        // Dosya yükleme parametreleri
        params: {},

        // Dosya yükleme başına izin verilen maksimum dosya boyutu (byte cinsinden)
        maxfilesize: 2000000, // 2MB

        // Yüklemeye izin verilen maksimum dosya sayısı
        maxfiles: 20,

        // Yüklemeye izin verilen dosya türleri
        accept: ['jpg', 'png', 'jpeg', 'webp'],

        // Dosya yükleme işlemi başladığında tetiklenen olay
        uploadstart: function (obj, files, index) {
            console.log('Upload started for file ' + index);
        },

        // Dosya yükleme işlemi tamamlandığında tetiklenen olay
        uploadcompleted: function (obj, file, json, data) {
            console.log('Upload completed for file ' + file.name);
        },

        // Dosya yükleme işlemi başarısız olduğunda tetiklenen olay
        uploaderror: function (obj, file, json, data) {
            console.log('Upload failed for file ' + file.name);
        }
    });
});
$(function () {
    $('#productFiles').FancyFileUpload({
        // Dosya yükleme URL'si
        url: 'upload_file.php',

        // Dosya yükleme yöntemi
        method: 'POST',

        // Dosya yükleme parametreleri
        params: {},

        // Dosya yükleme başına izin verilen maksimum dosya boyutu (byte cinsinden)
        maxfilesize: 2000000, // 1MB

        // Yüklemeye izin verilen maksimum dosya sayısı
        maxfiles: 20,

        // Yüklemeye izin verilen dosya türleri
        accept: ['jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'],

        // Dosya yükleme işlemi başladığında tetiklenen olay
        uploadstart: function (obj, files, index) {
            console.log('Upload started for file ' + index);
        },

        // Dosya yükleme işlemi tamamlandığında tetiklenen olay
        uploadcompleted: function (obj, file, json, data) {
            console.log('Upload completed for file ' + file.name);
        },

        // Dosya yükleme işlemi başarısız olduğunda tetiklenen olay
        uploaderror: function (obj, file, json, data) {
            console.log('Upload failed for file ' + file.name);
        }
    });
});