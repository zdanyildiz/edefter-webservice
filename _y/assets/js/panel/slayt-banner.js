$resimyer=0;
Dropzone.autoProcessQueue= true;
Dropzone.options.myawesomedropzone =
{
	parallelUploads: 10,
	autoProcessQueue: true,
	addRemoveLinks: true,
	maxFiles: 1,
	maxFilesize: 5,
	dictDefaultMessage: "Dosyaları yüklemek için bırakın",
	dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
	dictFallbackText: "Dosyalarınızı eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
	dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
	dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
	dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
	dictCancelUpload: "İptal Et",
	dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
	dictRemoveFile: "Dosya Sil",
	dictRemoveFileConfirmation: null,
	dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
	
	removedfile: function(file)
	{ 
		var _ref;
		return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
	},
	init: function()
    {
        this.on("success", function(file, responseText)
        {
        	$resimadi=responseText.replace('"', '');
            $resimadi=$resimadi.replace('"', '');
            $resimadi=$resimadi.replace("\\", '');
            
            res = $resimadi.split("|");
            $resimadi=res[0];
            $resimid=res[1];

            d=$.now();
            $resim="/m/r/"+$resimadi;
            $("#bannerresim"+$resimyer).text($resim);
            $("#imgbannerresim"+$resimyer).attr("src",$resim+"?"+d);
            $("#banneraktif"+$resimyer).prop('checked', true);
            if($("#textModal").css('display') == 'none')
            {
				$("#resimyuklepencerekapat").click();
				$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleme Başarılı");
				$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
				$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
				this.removeAllFiles();
				$("#resimad").val("");
	        	
	        	//$("#resimhata").val(1);
			}
        });
        this.on("addedfile", function(file)
        {
        	if($("#resimad").val().length<2)
        	{
        		if($("#textModal").css('display') == 'none')
        		{
					$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Adı Girin (En az 3 harf)");
					$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
					$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
	        		$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
	        		$("#resimyuklepencerekapat").click();
	        		this.removeAllFiles();
        		}
        	}
		});
    }
};

$( "a.resimsec" ).on( "click",function()
{
	$resimlink 	=$( this ).data( "link" );
	
	$("#bannerresim"+$resimyer).text($resimlink);
    d=$.now();
    $resim="/m/r/"+$resimlink+"?"+d;
    $("#imgbannerresim"+$resimyer).attr("src",$resim);
    $("#banneraktif"+$resimyer).prop('checked', true);
});

$silid=0;
$(document).ready(function()
{
	$('a#sillink').click(function ()
	{
		//$silid=$(this).data("id");
	});
	$('#silbutton').click(function ()
	{
		$("#resimid").val(0);
		$("#rad").text("Resim Adı");
	    d=$.now();
	    $resim="/_y/assets/img/avatar7.jpg?"+d;
	    $("#ryer").attr("src",$resim);
	    $("#btn-popup-sil-kapat").click();
		//$('#_islem').attr('src', "/_y/s/f/sil.php?sil=resim&id="+$silid);
	});
 });
$(document).on("click",'a#bhazirekle,#byeniekle', function ()
{
	$resimyer=$(this).data("id");
});