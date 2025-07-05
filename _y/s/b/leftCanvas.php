<div class="offcanvas">
	<!-- sol popup -->
	<div id="offcanvas-imageUpload" class="offcanvas-pane width-12" >

		<div class="offcanvas-head">
			<header>Yeni Resim Yükleyin</header>
			<div class="offcanvas-tools">
				<a id="offcanvas-imageUploadOff" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
					<i class="md md-close"></i>
				</a>
			</div>
		</div>

		<div class="offcanvas-body">
			<div class="card">				
				<div class="card-body">
                    <div class="form">
                        <div class="form-group">
                            <input type="text" id="imageName" class="form-control" placeholder="" required="" data-rule-minlength="2" aria-required="true" aria-describedby="imageName-error" aria-invalid="true">
                            <label for="imageName">Önce Resim Adı Girin</label>
                        </div>
                        <div class="cart-actionbar-row hidden" id="runImageDropzoneContainer" style="text-align: center">
                            <button id="runImageDropzone" class="btn btn-primary">Resim adı yazıp Yüklemeye devam edin</button>
                        </div>
                    </div>

					<form action="/App/Controller/Admin/AdminImageController.php" class="dropzone dz-clickable form" id="imageDropzone">
						<div class="form-group">
                            <input type="hidden" name="action" value="uploadImage">
                            <input type="hidden" name="imageName" id="formImageName" value="">
                            <input type="hidden" name="imageFolder" id="imageFolder" value="">
							<div class="dz-message">
								<h3>Resmi Sürükleyin ve Bırakın veya Tıklayın.</h3>
								<em>En fazla <strong>10 Adet (50MB Boyutunda)</strong> resim seçin</em>
                                <p>(jpg, jpeg, png, webp)</p>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="force-padding stick-bottom-right">
			<a class="btn btn-floating-action btn-default-dark" href="#offcanvas-imageUpload" data-toggle="offcanvas">
				<i class="md md-arrow-back"></i>
			</a>
		</div>
	</div>
	<div id="offcanvas-fileUpload" class="offcanvas-pane width-12" >

		<div class="offcanvas-head">
			<header>Yeni Dosya Yükleyin</header>
			<div class="offcanvas-tools">
				<a id="offcanvas-fileUploadOff" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
					<i class="md md-close"></i>
				</a>
			</div>
		</div>
		<div class="offcanvas-body">
			<div class="card">				
				<div class="card-body">
                    <div class="form">
                        <div class="form-group">
                            <input type="text" id="fileName" class="form-control" placeholder="" required="" data-rule-minlength="2" aria-required="true" aria-describedby="fileName-error" aria-invalid="true">
                            <label for="fileName">Önce Dosya Adı Girin</label>
                        </div>
                        <div class="cart-actionbar-row hidden" id="runFileDropzoneContainer" style="text-align: center">
                            <button id="runFileDropzone" class="btn btn-primary">Dosya adı yazıp Yüklemeye devam edin</button>
                        </div>
                    </div>
                    <form action="/App/Controller/Admin/AdminFileController.php" class="dropzone dz-clickable" id="fileDropzone">
                        <input type="hidden" name="action" value="uploadFile">
                        <input type="hidden" name="fileName" id="formFileName" value="">
                        <input type="hidden" name="fileFolder" id="fileFolder" value="">
                        <div class="form-group">
                            <div class="dz-message">
                                <h3>Dosyayı Sürükleyin ve Bırakın veya Tıklayın.</h3>
                                <em>En fazla <strong>10 (100MB)</strong> dosya seçin</em>
                            </div>
                        </div>
                    </form>
                </div>
			</div>
		</div>
		<div class="force-padding stick-bottom-right">
			<a class="btn btn-floating-action btn-default-dark" href="#offcanvas-fileUpload" data-toggle="offcanvas">
				<i class="md md-arrow-back"></i>
			</a>
		</div>
	</div>
	<div id="offcanvas-videoUpload" class="offcanvas-pane width-12" >
		<div class="offcanvas-head">
			<header>Yeni Video Yükleyin</header>
			<div class="offcanvas-tools">
				<a id="offcanvas-videoUploadOff" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
					<i class="md md-close"></i>
				</a>
			</div>
		</div>
		<div class="offcanvas-body">
			<div class="card">				
				<div class="card-body">
					<form action="/App/Controller/Admin/AdminVideoController.php" class="dropzone dz-clickable form" id="videoDropzone">
						<div class="form-group">
                            <input type="hidden" name="action" value="uploadVideo">
							<input type="hidden" name="videoName" id="uploadVideoName" value="">
                            <input type="hidden" name="videoFolder" id="uploadVideFolder" value="Video">
							<div class="dz-message">
								<h3>Videoyu Sürükleyin ve Bırakın veya Tıklayın.</h3>
								<em>En fazla <strong>1 (100MB)</strong> video seçin</em>
                                <p>(mp4, webm, mov, avi)</p>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="force-padding stick-bottom-right">
			<a class="btn btn-floating-action btn-default-dark" href="#offcanvas-videoUpload" data-toggle="offcanvas">
				<i class="md md-arrow-back"></i>
			</a>
		</div>
	</div>

    <div id="offcanvas-searchContent" class="offcanvas-pane width-9" >
        <div class="offcanvas-head">
            <header>Kategori/Sayfa Arayın</header>
            <div class="offcanvas-tools">
                <a id="offcanvas-searchContentOff" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
                    <i class="md md-close"></i>
                </a>
            </div>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <select id="searchType" class="form-control" required="" data-rule-required="true" aria-required="true" aria-describedby="searchType-error" aria-invalid="true">
                            <option value="">Kategori/Sayfa Seçin</option>
                            <option value="category">Kategori</option>
                            <option value="page">Sayfa</option>
                            <option value="productCategory">Ürün Kategori</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="hidden" id="targetMenu">
                        <input type="text" id="searchContent" class="form-control" placeholder="Arama yapmak için yazın" required="" data-rule-minlength="2" aria-required="true" aria-describedby="searchContent-error" aria-invalid="true">
                    </div>
                    <div id="searchContentResult" class="dd">
                        <ul class="list"></ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="force-padding stick-bottom-right">
            <a class="btn btn-floating-action btn-default-dark" href="#offcanvas-searchContent" data-toggle="offcanvas">
                <i class="md md-arrow-back"></i>
            </a>
        </div>
    </div>

	<div id="offcanvas-productListUpload" class="offcanvas-pane width-12" >
		<div class="offcanvas-head">
			<header>Ürün Listesi Yükleyin</header>
			<div class="offcanvas-tools">
				<a id="offcanvas-productListUploadOff" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
					<i class="md md-close"></i>
				</a>
			</div>
		</div>
		<div class="offcanvas-body">
			<div class="card">				
				<div class="card-body no-padding">
					<form action="/App/Controller/Admin/AdminProductController.php" class="dropzone dz-clickable" id="productListDropzone">
						<div class="form-group">
							<input type="hidden" name="action" value="uploadProductList">
                            <input type="hidden" id="productListUploadLanguageID" name="languageID" value="">
							<div class="dz-message">
								<h3>Ürün Listesini Sürükleyin ve Bırakın veya Tıklayın.</h3>
								<em>En fazla <strong>1 (10MB)</strong> dosya seçin</em>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="force-padding stick-bottom-right">
			<a class="btn btn-floating-action btn-default-dark" href="#offcanvas-productListUpload" data-toggle="offcanvas">
				<i class="md md-arrow-back"></i>
			</a>
		</div>
	</div>
    <div id="offcanvas-toplufiyat" class="offcanvas-pane width-12" >
        <div class="offcanvas-head">
            <header>Ürün Listesi Yükleyin</header>
            <div class="offcanvas-tools">
                <a id="urunfiyatcanvas" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
                    <i class="md md-close"></i>
                </a>
            </div>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body no-padding">
                    <form action="/_y/s/f/urunfiyatlistesiyukle.php" target="_islem" class="dropzone dz-clickable" id="myawesomedropzoneurunfiyat">
                        <div class="form-group">
                            <input type="hidden" name="dosyaklasor" value="havuz">
                            <div class="dz-message">
                                <h3>Ürün Fiyat Listesini Sürükleyin ve Bırakın veya Tıklayın.</h3>
                                <em>En fazla <strong>1 (10MB)</strong> dosya seçin</em>
                            </div>
                        </div>
                    </form>
                </div><!--end .card-body -->
            </div>
        </div>
        <div class="force-padding stick-bottom-right">
            <a class="btn btn-floating-action btn-default-dark" href="#offcanvas-demo-size3" data-toggle="offcanvas">
                <i class="md md-arrow-back"></i>
            </a>
        </div>
    </div>
    <div id="offcanvas-toplufiyaten" class="offcanvas-pane width-12" >
        <div class="offcanvas-head">
            <header>Ürün Listesi Yükleyin</header>
            <div class="offcanvas-tools">
                <a id="urunfiyatcanvasen" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
                    <i class="md md-close"></i>
                </a>
            </div>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body no-padding">
                    <form action="/_y/s/f/urunfiyatlistesiyukle.php" target="_islem" class="dropzone dz-clickable" id="myawesomedropzoneurunfiyaten">
                        <div class="form-group">
                            <input type="hidden" name="dosyaklasor" value="havuz">
                            <div class="dz-message">
                                <h3>Yurtdışı Ürün Fiyat Listesini Sürükleyin ve Bırakın veya Tıklayın.</h3>
                                <em>En fazla <strong>1 (10MB)</strong> dosya seçin</em>
                            </div>
                        </div>
                    </form>
                </div><!--end .card-body -->
            </div>
        </div>
        <div class="force-padding stick-bottom-right">
            <a class="btn btn-floating-action btn-default-dark" href="#offcanvas" data-toggle="offcanvas">
                <i class="md md-arrow-back"></i>
            </a>
        </div>
    </div>
	<!-- //sol popop-->
</div>