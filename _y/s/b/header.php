<?php
/**
 * @var AdminCasper $adminCasper
 * @var int $adminID
 * @var string $adminName
 * @var string $adminEmail
 * @var string $adminPhone
 * @var string $adminImage
 * @var int $adminAuth
 * @var string $adminType
 * @var string $adminLastLogin
 */

?>
<header id="header" >
	<div class="headerbar">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="headerbar-left">
			<ul class="header-nav header-nav-options">
				<li class="header-nav-brand" >
					<div class="brand-holder">
						<a href="/_y/">
							<span class="text-lg text-bold text-primary"><img src="/_y/m/r/Logo/pozitif-eticaret-logo.png" alt="Pozitif E-Ticaret"></span>
						</a>
					</div>
				</li>
				<li>
					<a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
						<i class="fa fa-bars"></i>
					</a>
				</li>
			</ul>
		</div>
		
		<div class="headerbar-right">
            <ul class="header-nav header-nav-options">
                <li style="padding: 0">
                    <a href="javascript:void(0);" style="text-align: center">
                        <strong id="sessionTimer" class="text-danger" style="font-size: 16px"></strong><br>
                        <small>kalan oturum Süresi</small>
                    </a>
                </li>
            </ul>
			<ul class="header-nav header-nav-profile">
				<li class="dropdown">
					<a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">

						<img src="<?=$adminImage?>" alt="<?=$adminName?>" />

						<span class="profile-info">
							<?=$adminName?>
							<small><?=$adminType?></small>
						</span>

					</a>
					<ul class="dropdown-menu animation-dock">
						<li class="dropdown-header"><?=$adminLastLogin?></li>
						<li class="dropdown-header">Ayarlar</li>
                        <?php if($adminAuth <= 1): ?>
                        <li><a href="/_y/s/s/yoneticiler/AddAdmin.php?adminID=<?=$adminID?>">Bilgilerim</a></li>
                        <?php endif; ?>
						<li><a href="/_y/s/guvenlik/kilit.php?refUrl=<?=urlencode($_SERVER['REQUEST_URI'])?>"><i class="fa fa-fw fa-lock"></i>Ekranı Kilitle</a></li>
						<li><a href="/_y/s/guvenlik/cikis.php"><i class="fa fa-fw fa-power-off text-danger"></i> Çıkış</a></li>
					</ul><!--end .dropdown-menu -->
				</li>
			</ul>
			<ul class="header-nav header-nav-toggle hide" id="sagresimbuton">
				<li>
					<a class="btn btn-icon-toggle btn-default" href="#offcanvas-search" data-toggle="offcanvas" data-backdrop="false">
						<i class="fa fa-ellipsis-v"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
</header>