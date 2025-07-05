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

include_once MODEL . 'Admin/AdminFile.php';
$fileModel = new AdminFile($db);

if($action == "getFilesBySearch"){

    $searchText = $requestData["searchText"] ?? null;

    if (!isset($searchText)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text error'
        ]);
        exit();
    }


    $files = $fileModel->getFilesBySearch($searchText);
    if (!empty($files)) {
        echo json_encode([
            'status' => 'success',
            'files' => $files
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No file found'
        ]);
        exit();
    }


}
elseif($action == "uploadFile"){
    $folderName = $requestData["fileFolder"] ?? null;
    if (!isset($folderName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Folder name error'
        ]);
        exit();
    }

    $fileName = $requestData["fileName"] ?? uniqid();

    //dropzone'dan birden fazla resim gelebiriği için foreach ile dönüyoruz
    //$_FILES boş ise hata verelim
    if (empty($_FILES)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No file found'
        ]);
        exit();
    }

    $newFileName = $helper->toLowercase($fileName);
    $newFileName = $helper->trToEn($newFileName);
    $newFileName = $helper->cleanString($newFileName);

    $i=0;
    foreach ($_FILES as $file) {
        $i++;
        $newFileName = $newFileName ."_". $i;
        $upload = $fileModel->uploadFile($fileName,$newFileName,$file, $folderName);

        //döngüyü durdurmadan hata ve başarı mesajlarını bir dizide topluyoruz. Hangi resim yüklendi hangileri ne hata verdi bakalım
        $uploadMessages[] = $upload;
    }

    //resim yükleme işlemleri bitince success dönenleri veritabanına kayıt edelim
    $successUploads = array_filter($uploadMessages, function($upload){
        return $upload['status'] == 'success';
    });

    if (!empty($successUploads)) {

        foreach ($successUploads as $successUpload) {
            $fileData = [
                'fileUniqID' => $helper->createPassword(20,2),
                'fileAddDate' => date('Y-m-d H:i:s'),
                'filePath' => $successUpload['filePath'],
                'fileFolderID' => $successUpload['fileFolderID'],
                'fileFolderName' => $successUpload['fileFolderName'],
                'fileName' => $successUpload['fileName'],
                'fileExtension' => $successUpload['fileExtension'],
                'fileSize' => $successUpload['fileSize']
            ];

            $result = $fileModel->addFile($fileData);
            //result lastid olarak dönüyor. Eğer 0'dan büyükse kayıt başarılıdır
            //tüm resimidleri, resim bilgilerini ve sonuçları döndürelim

            $fileData['fileID'] = $result;
            $fileResults[] = [
                'fileData' => $fileData
            ];
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'fileResults' => $fileResults
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