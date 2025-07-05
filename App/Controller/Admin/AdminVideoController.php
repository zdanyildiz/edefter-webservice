<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Session $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */
/**
 * @var adminSession $adminSession
 * @var AdminDatabase $db
 * @var Router $router
 */


$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

include_once MODEL.'/Admin/AdminVideo.php';
$adminVideoModel = new AdminVideo($db);

switch ($action) {
    case 'getAllVideos':
        $videos = $adminVideoModel->getVideos();
        echo json_encode([
            'status' => 'success',
            'data' => $videos
        ]);
        break;

    case 'getVideo':
        $videoId = $requestData['videoId'] ?? null;
        if (!isset($videoId)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Video ID is required'
            ]);
            exit();
        }
        $video = $adminVideoModel->getVideoById($videoId);
        if(empty($video)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Video not found'
            ]);
            exit();
        }
        echo json_encode([
            'status' => 'success',
            'message' => 'Video found',
            'video' => $video
        ]);
        break;

    case 'addVideo':

        $videoName = $requestData['videoName'] ?? null;
        $videoFile = $requestData['videoFile'] ?? null;
        $videoExtension = $requestData['videoExtension'] ?? null;
        $videoSize = $requestData['videoSize'] ?? null;
        $videoWidth = $requestData['videoWidth'] ?? null;
        $videoHeight = $requestData['videoHeight'] ?? null;
        $videoUniqID = $helper->generateUniqID();
        $videoIframe = $requestData['videoIframe'] ?? null;
        $videoDescription = $requestData['videoDescription'] ?? null;

        if(!isset($videoName)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Video Name is required'
            ]);
            exit();
        }

        //iframe ve file aynı anda boş olamaz
        if(!isset($videoFile) && !isset($videoIframe)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Video File or Video Iframe is required'
            ]);
            exit();
        }


        $addVideoData = [
            'video_name' => $videoName,
            'video_file' => $videoFile,
            'video_extension' => $videoExtension,
            'video_size' => $videoSize,
            'video_width' => $videoWidth,
            'video_height' => $videoHeight,
            'unique_id' => $videoUniqID,
            'video_iframe' => $videoIframe,
            'description' => $videoDescription,
            'is_deleted' => 0
        ];

        $adminVideoModel->beginTransaction();

        $result = $adminVideoModel->addVideo($addVideoData);

        if(!$result){
            $adminVideoModel->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Video add failed'
            ]);
            exit();
        }

        $adminVideoModel->commit();

        $videoID = $result;
        $addVideoData['video_id'] = $videoID;
        echo json_encode([
            'status' => 'success',
            'message' => 'Video added successfully',
            'videoData' => $addVideoData
        ]);

        break;

    case 'updateVideo':
        $videoID = $requestData['videoID'] ?? null;
        $videoName = $requestData['videoName'] ?? null;
        $videoFile = $requestData['videoFile'] ?? null;
        $videoExtension = $requestData['videoExtension'] ?? null;
        $videoSize = $requestData['videoSize'] ?? null;
        $videoWidth = $requestData['videoWidth'] ?? null;
        $videoHeight = $requestData['videoHeight'] ?? null;
        $videoUniqID = $helper->generateUniqID();
        $videoIframe = $requestData['videoIframe'] ?? null;
        $videoDescription = $requestData['videoDescription'] ?? null;

        if(!isset($videoName)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Video Name is required'
            ]);
            exit();
        }

        //iframe ve file aynı anda boş olamaz
        if(!isset($videoFile) && !isset($videoIframe)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Video File or Video Iframe is required'
            ]);
            exit();
        }


        $updateVideoData = [
            'video_name' => $videoName,
            'video_file' => $videoFile,
            'video_extension' => $videoExtension,
            'video_size' => $videoSize,
            'video_width' => $videoWidth,
            'video_height' => $videoHeight,
            'video_iframe' => $videoIframe,
            'description' => $videoDescription,
            'video_id' => $videoID
        ];

        $adminVideoModel->beginTransaction();

        $result = $adminVideoModel->updateVideo($updateVideoData);

        if(!$result){
            $adminVideoModel->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Video update failed'
            ]);
            exit();
        }

        $adminVideoModel->commit();

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Video updated successfully' : 'Failed to update video'
        ]);
        break;

    case 'deleteVideo':
        $videoId = $requestData['videoID'] ?? null;
        if (!isset($videoId)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Video ID is required'
            ]);
            exit();
        }
        $adminVideoModel->beginTransaction();
        $result = $adminVideoModel->deleteVideo($videoId);
        if(!$result){
            $adminVideoModel->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Video delete failed'
            ]);
            exit();
        }
        $adminVideoModel->commit();
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Video deleted successfully' : 'Failed to delete video'
        ]);
        break;

    case 'uploadVideo':
        $videoName = $requestData['videoName'] ?? null;
        $videoFolder = $requestData['videoFolder'] ?? null;
        $videoFile = $_FILES['file'] ?? null;

        if (!isset($videoName, $videoFolder, $videoFile)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Video Name, Video Folder and Video File are required'
            ]);
            exit();
        }

        $newVideoName = $helper->toLowercase($videoName);
        $newVideoName = $helper->trToEn($newVideoName);

        $videoFileResult = $adminVideoModel->uploadVideo($newVideoName, $videoFile, $videoFolder);
        echo json_encode($videoFileResult);
        break;

    case 'searchVideo':
        $searchText = $requestData['searchText'] ?? null;
        if (empty($searchText)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Search text is empty'
            ]);
            exit();
        }
        $videoResult = $adminVideoModel->searchVideo($searchText);
        if (empty($videoResult)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Search video error'
            ]);
            exit();
        }
        echo json_encode([
            'status' => 'success',
            'data' => $videoResult
        ]);
        break;
    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
        break;
}