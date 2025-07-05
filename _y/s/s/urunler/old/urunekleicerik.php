<div class="row">
	<div class="col-lg-3 col-md-4">
		<article class="margin-bottom-xxl">
			<h4>Ürün İçeriği</h4><p></p>
			<p>
				Ürün adı girin! (örn: Adidas Pace VS Erkek Beyaz Spor Ayakkabı)
			</p>
			<br>
			<p>Ürün içeriğini/özelliklerini girin</p>
		</article>
	</div>
	<div class="col-lg-offset-1 col-md-8">
		<div class="card">
			<div class="card-body">
				<div class="form-group">
					<div class="hbox-column v-top col-md-1">
						<a 
							class="btn btn-floating-action ink-reaction" 
							href="#offcanvas-search" 
							id="shazirekle"
							data-toggle="offcanvas" 
							title="seç">
							<i class="fa fa-file-image-o"></i></a>
						&nbsp;&nbsp;&nbsp;
						<a 
							class="btn btn-floating-action ink-reaction" 
							href="#offcanvas-left" 
							id="syeniekle"
							data-toggle="offcanvas" 
							title="ekle">
							<i class="fa fa-plus"></i></a>
					</div>
				</div>
				<div class="form-group">
					<input 
						type="text" 
						name="sayfaad" 
						id="sayfaad" 
						class="form-control" 
						placeholder="Örn:ÜRÜN BAŞLIĞINI GİRİN" 
						value="<?=$f_sayfaad?>" 
						required aria-required="true">
					<label for="sayfaad">Ürün Adı</label>
				</div>
				<div class="form-group no-padding">
					<textarea 
						id="ckeditor" 
						name="sayfaicerik" 
						rows="40" 
						style="height: 500px"><?=$f_sayfaicerik?></textarea>
				</div>
			</div>
		</div>
		<em class="text-caption">Ürün İçeriği/Açıklama</em>
	</div>
</div>