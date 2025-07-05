<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Material Admin - Compose mail</title>

		<!-- BEGIN META -->
		<meta charset="utf-8">
	</head>
	<body>
		<header>
			<h1>Resim Yükleme Formu</h1>
			<p>test</p>
		</header>
		<main>
			<form action="videoyukle.php" method="post" enctype="multipart/form-data">
				<input type="text" name="videoklasor" value="v">
				<input type="text" name="videoad" value="test">
			   	<input type="file" name="file" multiple />
			   	<input type="submit" value="Gönder" />
			</form>
		</main>
	</body>