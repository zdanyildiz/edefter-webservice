<?php
/**
 * @var Database $db
 * @var Helper $helper
 * @var Session $session
 * @var array $requestData
 */

$casper = $session->getCasper();

if (!$casper instanceof Casper) {
    echo "Casper is not here - ImageController:14";exit();
}

$config = $casper->getConfig();
$helper = $config->Helper;

$action = $requestData['action'] ?? null;

if(!empty($action)){
    if($action == 'rename'){

        $oldName = $requestData['oldName'] ?? null;
        $newName = $requestData['newName'] ?? null;

        if(!empty($oldName) && !empty($newName)){
            $sql = "UPDATE resim SET resim = :newName WHERE resim = :oldName";
            $params = array(
                ':newName' => $newName,
                ':oldName' => $oldName
            );
            $db->beginTransaction();
            $imageUpdate = $db->update($sql, $params);
            if($imageUpdate) {
                $db->commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Image name updated successfully'
                ]);
                exit();
            }
            $db->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Image name could not be updated'
            ]);
            exit;

        }else{
            echo json_encode([
                'status' => 'error',
                'message' => 'Old name and new name are required'
            ]);
            exit;
        }
    }
    elseif($action == 'renameAll'){
        $sql = "
            SELECT 
                resimid as image_id,resim.resim as image_name,resimad as image_title,
                CONCAT(resimklasor.resimklasorad,'/',resim.resim) as image_path
            FROM 
                resim
                INNER JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
        ";
        $images = $db->select($sql);
        foreach($images as $image){

            $imageID = $image['image_id'];
            $imageName = $image['image_name'];
            //.jpg son noktadan sonrasını uzantı olarak alalım
            $imageExtension = explode('.', $imageName);
            $imageExtension = end($imageExtension);
            $imageExtension = '.' . $imageExtension;

            $imagePath = $image['image_path'];
            $imageTitle = $image['image_title'];

            $imageFullPath = IMG . $imagePath;

            $newName="";

             if (!file_exists($imageFullPath)) {
                $image1=mb_convert_encoding($imageFullPath,"UTF-8");
                if (!file_exists($image1))
                {
                    $image2=iconv("UTF-8", "windows-1254", $imageFullPath);
                    if (!file_exists( $image2))
                    {
                        $image3=iconv("windows-1254", 'utf-8//TRANSLIT', $image2);
                        if (file_exists( $image3))
                        {
                            $newName = $helper->trToEn($imageTitle);
                            $newName = $helper->sanitizeImageName($newName)."-".$imageID.$imageExtension;
                            copy($image3, $newName);
                        }
                        else{
                            Log::write('Image not found: ' . $imageFullPath,'special');
                        }
                    }
                    else {
                        $newName = $helper->trToEn($imageTitle);
                        $newName = $helper->sanitizeImageName($newName)."-".$imageID.$imageExtension;
                        copy($image2, $newName);
                    }
                }
                else
                {
                    $newName = $helper->trToEn($imageTitle);
                    $newName = $helper->sanitizeImageName($newName)."-".$imageID.$imageExtension;
                    copy($imageFullPath, $newName);
                }

                if(!empty($newName))
                {
                    $sql = "UPDATE resim SET resim = :newName WHERE resimid = :imageID";
                    $params = array(
                        ':newName' => $newName,
                        ':imageID' => $imageID
                    );
                    $db->beginTransaction();
                    $imageUpdate = $db->update($sql, $params);
                    if($imageUpdate) {
                        $db->commit();
                    }
                    else{
                        $db->rollBack();
                    }
                }

            }

        }
        echo json_encode([
            'status' => 'success',
            'message' => 'All image names updated successfully'
        ]);
        exit();
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
        exit;
    }
}

