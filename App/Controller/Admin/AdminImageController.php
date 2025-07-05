<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var AdminSession $adminSession
 * @var Helper $helper
 */


$requestData = array_merge($_GET, $_POST);

$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

$helper = $config->Helper;

$referrer = $requestData["referrer"] ?? null;

$adminCasper = $adminSession->getAdminCasper();

$config = $adminCasper->getConfig();

$json = $config->Json;

include_once MODEL . 'Admin/AdminImage.php';
$imageModel = new AdminImage($db);

if($action == "getImagesBySearch"){

    $searchText = $requestData["searchText"] ?? null;

    if (!isset($searchText)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text error'
        ]);
        exit();
    }


    $images = $imageModel->getImagesBySearch($searchText);
    if (!empty($images)) {
        echo json_encode([
            'status' => 'success',
            'images' => $images
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No image found'
        ]);
        exit();
    }


}
elseif($action == "uploadImage"){

    $folderName = $requestData["imageFolder"] ?? null;
    if (!isset($folderName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Folder name error'
        ]);
        exit();
    }

    $imageName = $requestData["imageName"] ?? uniqid();
    Log::adminWrite("ImageName: $imageName");

    //dropzone'dan birden fazla resim gelebiriği için foreach ile dönüyoruz
    //$_FILES boş ise hata verelim
    if (empty($_FILES)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No image found'
        ]);
        exit();
    }

    $newImageName = $helper->toLowercase($imageName);
    $newImageName = $helper->trToEn($newImageName);
    $newImageName = $helper->cleanString($newImageName);
    Log::adminWrite("ImageName: $imageName");

    $i=0;
    foreach ($_FILES as $file) {
        $i++;
        $newImageName = $newImageName ."_". $i;
        if($folderName == "Admin"){
            $upload = $imageModel->uploadAdminImage($imageName,$newImageName,$file, $folderName);
        }
        else{
            $upload = $imageModel->uploadImage($imageName,$newImageName,$file, $folderName);
        }


        //döngüyü durdurmadan hata ve başarı mesajlarını bir dizide topluyoruz. Hangi resim yüklendi hangileri ne hata verdi bakalım
        $uploadMessages[] = $upload;
    }

    //resim yükleme işlemleri bitince success dönenleri veritabanına kayıt edelim
    $successUploads = array_filter($uploadMessages, function($upload){
        return $upload['status'] == 'success';
    });

    if (!empty($successUploads)) {

        foreach ($successUploads as $successUpload) {
            $imageUniqID = $helper->createPassword(20, 2);

            if ($folderName == "Favicon"){
                $imageUniqID = "12345678901234567890";
            }
            elseif($folderName == "Logo"){
                include_once Helpers . 'php-ico.php';
                $source = IMG . $successUpload['imageFolderName']."/".$successUpload['imagePath'];
                $ico = new PHP_ICO($source, array( array( 32, 32 ), array( 64, 64 ) ) );
                //IMG altında Favicon klasörü yoksa oluştur
                if(!file_exists(IMG . "Favicon")){
                    mkdir(IMG . "Favicon");
                }
                $ico->save_ico(IMG . "Favicon/favicon.ico");

                $deleteFile = IMG . "Favicon/".$successUpload['imagePath'];
                if(file_exists($deleteFile)){
                    unlink($deleteFile);
                }

                $updateData = [
                    'imageUniqID' => "12345678901234567890",
                    'imageFolderID' => 7,
                    'imageName' => "Favicon",
                    'imagePath' => "favicon.ico",
                    'imageWidth' => 32,
                    'imageHeight' => 32
                ];

                $imageModel->updateImage($updateData);
            }

            $imageData = [
                'imageUniqID' => $imageUniqID,
                'imagePath' => $successUpload['imagePath'],
                'imageFolderID' => $successUpload['imageFolderID'],
                'imageFolderName' => $successUpload['imageFolderName'],
                'imageName' => $successUpload['imageName'],
                'imageWidth' => $successUpload['imageWidth'],
                'imageHeight' => $successUpload['imageHeight']
            ];

            $imageModel->beginTransaction();
            $result = $imageModel->addImage($imageData);
            if(!$result){
                $imageModel->rollback();
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Resim kaydedilemedi'
                ]);
                exit();
            }
            $imageModel->commit();

            $imageData['imageID'] = $result;
            $imageResults[] = [
                'imageData' => $imageData
            ];
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Image uploaded successfully',
            'imageResults' => $imageResults
        ]);

        exit();
    }
    else {
        $errorUploads = array_filter($uploadMessages, function($upload){
            return $upload['status'] == 'error';
        });
        //error mesajlarını alıp yazdıralım
        $errorMessages = array_map(function($errorUpload){
            return $errorUpload['message'];
        }, $errorUploads);

        //mesajları json olarak döndürelim

        echo json_encode([
            'status' => 'error',
            'message' => $errorMessages
        ]);
        exit();
    }

}
else{
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}