<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
include_once MODEL . 'Admin/AdminDynamicFormBuilder.php';

/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Session $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */

$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

// Initialize the AdminDynamicFormBuilder class
$formBuilder = new AdminDynamicFormBuilder($db);

if ($action == "createForm") {
    $formName = $requestData['formName'] ?? null;
    $formDescription = $requestData['formDescription'] ?? null;

    if (!$formName) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Form name is required.'
        ]);
        exit();
    }

    // Create form using the AdminDynamicFormBuilder class
    $formId = $formBuilder->createForm($formName, $formDescription);

    if ($formId) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Form created successfully.',
            'form_id' => $formId
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create form.'
        ]);
    }
    exit();
}

if ($action == "updateForm") {
    $formId = $requestData['formId'] ?? null;
    $formName = $requestData['formName'] ?? null;
    $formDescription = $requestData['formDescription'] ?? null;

    if (!$formId || !$formName) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Form ID and name are required.'
        ]);
        exit();
    }

    // Update form using the AdminDynamicFormBuilder class
    $updateResult = $formBuilder->updateForm($formId, $formName, $formDescription);

    if ($updateResult) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Form updated successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update form.'
        ]);
    }
    exit();
}

if ($action == "deleteForm") {
    $formId = $requestData['formId'] ?? null;

    if (!$formId) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Form ID is required.'
        ]);
        exit();
    }

    // Delete form using the AdminDynamicFormBuilder class
    $deleteResult = $formBuilder->deleteForm($formId);

    if ($deleteResult) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Form deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete form.'
        ]);
    }
    exit();
}

if ($action == "addFormField") {
    $formId = $requestData['formId'] ?? null;
    $fieldName = $requestData['fieldName'] ?? null;
    $fieldType = $requestData['fieldType'] ?? null;
    $isRequired = $requestData['isRequired'] ?? false;
    $defaultValue = $requestData['defaultValue'] ?? null;

    if (!$formId || !$fieldName || !$fieldType) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Form ID, field name, and field type are required.'
        ]);
        exit();
    }

    // Add form field using the AdminDynamicFormBuilder class
    $fieldId = $formBuilder->createFormField($formId, $fieldName, $fieldType, $isRequired, $defaultValue);

    if ($fieldId) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Form field added successfully.',
            'field_id' => $fieldId
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add form field.'
        ]);
    }
    exit();
}

if ($action == "updateFormField") {
    $fieldId = $requestData['fieldId'] ?? null;
    $fieldName = $requestData['fieldName'] ?? null;
    $fieldType = $requestData['fieldType'] ?? null;
    $isRequired = $requestData['isRequired'] ?? false;
    $defaultValue = $requestData['defaultValue'] ?? null;

    if (!$fieldId || !$fieldName || !$fieldType) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Field ID, field name, and field type are required.'
        ]);
        exit();
    }

    // Update form field using the AdminDynamicFormBuilder class
    $updateResult = $formBuilder->updateFormField($fieldId, $fieldName, $fieldType, $isRequired, $defaultValue);

    if ($updateResult) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Form field updated successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update form field.'
        ]);
    }
    exit();
}

if ($action == "deleteFormField") {
    $fieldId = $requestData['fieldId'] ?? null;

    if (!$fieldId) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Field ID is required.'
        ]);
        exit();
    }

    // Delete form field using the AdminDynamicFormBuilder class
    $deleteResult = $formBuilder->deleteFormField($fieldId);

    if ($deleteResult) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Form field deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete form field.'
        ]);
    }
    exit();
}

// Default response if action is not recognized
echo json_encode([
    'status' => 'error',
    'message' => 'Invalid action.'
]);
exit();
