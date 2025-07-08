<?php
class AdminChatCompletion
{
    private $apiKey;
    private $apiEndpoint;
    private AdminDatabase$db;
    private int $adminID;

    public function __construct($db, $adminID)
    {
        $this->apiKey = $this->getOpenAIApiKey();
        $this->apiEndpoint = 'https://api.openai.com/v1/chat/completions';
        $this->db = $db;
        $this->adminID = $adminID;
        $this->createChatCompletionTable();
    }

    /**
     * .env dosyasından OPENAI_API_KEY değerini okur
     * @return string API anahtarı
     */
    private function getOpenAIApiKey(): string
    {
        $envFile = ROOT . '.env';
        $openaiApiKey = '';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) {
                    continue;
                }
                if (str_contains($line, 'OPENAI_API_KEY')) {
                    list($name, $value) = explode('=', $line, 2);
                    $openaiApiKey = trim($value);
                    break;
                }
            }
        }
        
        return $openaiApiKey;
    }

    public function createChatCompletionTable(){
        $sql = "CREATE TABLE IF NOT EXISTS `chat_completion` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `chat_id` varchar(255) NOT NULL,
            `prompt` text NOT NULL,
            `response` text NOT NULL,
            promt_tokens int(11) NOT NULL,
            completion_tokens int(11) NOT NULL,
            total_tokens int(11) NOT NULL,
            fingerprint varchar(255) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $this->db->createTable($sql);
    }

    public function addChatLog($chatData)
    {
        $sql = "INSERT INTO chat_completion (user_id, chat_id, prompt, response, promt_tokens, completion_tokens, total_tokens, fingerprint) VALUES (:user_id, :chat_id, :prompt, :response, :promt_tokens, :completion_tokens, :total_tokens, :fingerprint)";

        $params = [
            ':user_id' => $chatData['user_id'],
            ':chat_id' => $chatData['chat_id'],
            ':prompt' => $chatData['prompt'],
            ':response' => $chatData['response'],
            ':promt_tokens' => $chatData['promt_tokens'],
            ':completion_tokens' => $chatData['completion_tokens'],
            ':total_tokens' => $chatData['total_tokens'],
            ':fingerprint' => $chatData['fingerprint']
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Dil çeviri fonksiyonu
     * @param string $text Çevrilecek metin
     * @param string $targetLanguage Hedef dil
     * @return string Çevirilmiş metin
     */
    public function translateConstant(string $text, string $targetLanguage): string
    {
        $prompt = "Translate the following text to {$targetLanguage} in a concise manner, responding only with the translated text in JSON format, without any additional comments or alternatives. The response should be exactly in this format: {\"translation\": \"<translated text>\"}. Here is the text: \"{$text}\"";

        return $this->makeApiRequestForTranslate($prompt);
    }
    
    public function translateHtmlContent(string $htmlContent, string $targetLanguage): string
    {
        $prompt = "Translate the following HTML content into {$targetLanguage}. It is crucial that you preserve all HTML tags (like <p>, <strong>, <ul>, <li>, <br>, etc.) exactly as they are in the original text. Only translate the text content within these tags. Do not add, remove, or alter any HTML tags. Additionally, do not translate any words enclosed in square brackets (e.g., [firmaadres], [firmaunvan]). These are placeholders and should remain unchanged in the output. 

        At the end of the translated content, add a small disclaimer paragraph in {$targetLanguage} indicating that this content was translated using artificial intelligence. Use this format:
        
        <p style=\"margin-top: 20px; padding: 10px; border-top: 1px solid #eee; font-style: italic;\">
        This content has been translated using artificial intelligence technology.
        </p>
        
        Replace 'This content has been translated using artificial intelligence technology.' with the appropriate translation in {$targetLanguage}.
        
        Respond only with the translated HTML content including the disclaimer. Here is the HTML: \"{$htmlContent}\"";

        return $this->makeApiRequestForContent($prompt);
    }

    public function productCategoryContentGenerator(string $categoryDescription, string $language): string
    {
        $prompt = "Generate a detailed and engaging description in {$language} for the following product category: \"{$categoryDescription}\". " .
            "The content should be well-formatted, structured, and user-friendly, using only these HTML tags: <p> for paragraphs, <br> for line breaks, and <strong> for highlighting important features. " .
            "Avoid using <html>, <head>, <title>, <body>, or any other tags not mentioned. " .
            "The text should be specifically tailored for an e-commerce product category page, providing valuable information to the reader. " .
            "Please provide the response strictly in this format, without any additional comments or variations.";

        return $this->makeApiRequestForContent($prompt);
    }

    public function productCategorySeoGenerator(string $title, string $description, string $language): string
    {
        $prompt = "Generate SEO-friendly content for a product category page in {$language}. The category title is \"{$title}\", and the category description is \"{$description}\". " .
            "Respond only in JSON format with exactly three fields: \"seoTitle\", \"seoDescription\", and \"seoKeywords\". " .
            "The \"seoTitle\" should be concise, between 50-60 characters, the \"seoDescription\" should be informative and between 150-160 characters, and the \"seoKeywords\" should be a comma-separated list of 5-10 relevant keywords. " .
            "Provide the response strictly in this JSON format: {\"seoTitle\": \"<title>\", \"seoDescription\": \"<description>\", \"seoKeywords\": \"<keywords>\"}.";

        return $this->makeApiRequestForSeo($prompt);
    }

    public function productContentGenerator(string $contentDescription, string $language): string
    {
        $prompt = "Generate an engaging and informative product description in {$language} based on the following brief: \"{$contentDescription}\". " .
            "The content should be well-structured and user-friendly, using only the following HTML tags: <p> for paragraphs, <br> for line breaks, and <strong> for highlighting important features. " .
            "Avoid using <html>, <head>, <title>, <body>, or any other tags not listed. " .
            "The text should be specifically tailored for an e-commerce product page. " .
            "Please provide the response strictly in this format, without additional comments or variations.";

        return $this->makeApiRequestForContent($prompt);
    }

    public function productSeoGenerator(string $category, string $title, string $description, string $language): string
    {
        $prompt = "Generate SEO-friendly content for a product page in {$language}. The product category is \"{$category}\", the product title is \"{$title}\", and the product description is \"{$description}\". " .
            "Respond only in JSON format with exactly three fields: \"seoTitle\", \"seoDescription\", and \"seoKeywords\". " .
            "Ensure that the \"seoTitle\" is concise and between 50-60 characters, the \"seoDescription\" is informative and between 150-160 characters, and \"seoKeywords\" is a comma-separated list of 5-10 relevant keywords. " .
            "Provide the response strictly in this JSON format: {\"seoTitle\": \"<title>\", \"seoDescription\": \"<description>\", \"seoKeywords\": \"<keywords>\"}.";

        return $this->makeApiRequestForSeo($prompt);
    }

    public function generalCategoryContentGenerator(string $categoryDescription, string $language): string
    {
        $prompt = "Generate an informative and engaging description in {$language} for the following general category: \"{$categoryDescription}\". " .
            "The content should be reader-friendly, well-structured, and formatted using only the following HTML tags: <p> for paragraphs, <br> for line breaks, and <strong> for emphasizing key points. " .
            "Avoid using <html>, <head>, <title>, <body>, or any other tags not mentioned. " .
            "This content should be suitable for an informational category page, providing valuable information to the reader. " .
            "Please respond strictly in this format, without any additional comments or variations.";

        return $this->makeApiRequestForContent($prompt);
    }

    public function generalCategorySeoGenerator(string $title, string $description, string $language): string
    {
        $prompt = "Generate SEO-friendly content for a general category page in {$language}. The category title is \"{$title}\", and the category description is \"{$description}\". " .
            "Respond only in JSON format with exactly three fields: \"seoTitle\", \"seoDescription\", and \"seoKeywords\". " .
            "The \"seoTitle\" should be concise, between 50-60 characters, \"seoDescription\" should be informative and between 150-160 characters, and \"seoKeywords\" should be a comma-separated list of 5-10 relevant keywords. " .
            "Please provide the response strictly in this JSON format: {\"seoTitle\": \"<title>\", \"seoDescription\": \"<description>\", \"seoKeywords\": \"<keywords>\"}.";

        return $this->makeApiRequestForSeo($prompt);
    }

    public function generalPageContentGenerator(string $contentDescription, string $language): string
    {
        $prompt = "Generate content for a general page in {$language} based on the following brief: \"{$contentDescription}\". " .
            "The content should be structured and reader-friendly, using only these HTML tags: <p> for paragraphs, <br> for line breaks, <strong> for highlighting key points, <ul> for unordered lists, and <li> for list items. " .
            "Do not use <html>, <head>, <title>, <body>, or any other tags not mentioned. " .
            "The response should strictly follow these guidelines without additional comments or variations.";

        return $this->makeApiRequestForContent($prompt);
    }

    public function generalPageSeoGenerator(string $category, string $title, string $description, string $language): string
    {
        $prompt = "Generate SEO-friendly content for a general page in {$language}. The content category is \"{$category}\", the content title is \"{$title}\", and the content description is \"{$description}\". " .
            "Respond only in JSON format with exactly three fields: \"seoTitle\", \"seoDescription\", and \"seoKeywords\". " .
            "The \"seoTitle\" should be concise and between 50-60 characters, \"seoDescription\" should be informative and between 150-160 characters, and \"seoKeywords\" should be a comma-separated list of 5-10 relevant keywords. " .
            "Please provide the response in this strict format: {\"seoTitle\": \"<title>\", \"seoDescription\": \"<description>\", \"seoKeywords\": \"<keywords>\"}.";

        return $this->makeApiRequestForSeo($prompt);
    }

    private function makeApiRequestForContent(string $prompt): string
    {
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $data = [
            'model' => 'gpt-4.1-nano',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt]
            ]
        ];

        $ch = curl_init($this->apiEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return false;
        }

        $responseData = json_decode($response, true);

        if (isset($responseData['choices'][0]['message']['content'])) {
            return $responseData['choices'][0]['message']['content'];
        }

        return false;
    }
    /**
     * Çeviri API isteği
     * @param string $prompt API'ye gönderilecek çeviri prompt'u
     * @return string Çevirilmiş metin
     */
    private function makeApiRequestForTranslate(string $prompt): string
    {
        return $this->sendApiRequest($prompt, 'translation');
    }

    /**
     * SEO API isteği
     * @param string $prompt API'ye gönderilecek SEO prompt'u
     * @return string SEO uyumlu içerik JSON formatında
     */
    private function makeApiRequestForSeo(string $prompt): string
    {
        return $this->sendApiRequest($prompt, 'seo');
    }

    /**
     * Haber içeriği için çeviri ve SEO üretimi.
     *
     * @param string $newsTitle Orijinal haber başlığı
     * @param string $newsSpot Orijinal spot başlığı
     * @param string $newsContent Orijinal haber içeriği
     * @return string API'den gelen yanıt (JSON formatında)
     */
    public function newsContentSeoGenerator(string $newsTitle, string $newsSpot, string $newsContent): string
    {
        $prompt = "For a news website, please translate the following news details into English and generate SEO-friendly content as well. " .
            "The details are:\n" .
            "Title: \"{$newsTitle}\"\n" .
            "Spot: \"{$newsSpot}\"\n" .
            "Content: \"{$newsContent}\"\n\n" .
            "Translation: Provide the translated title, spot, and content in English. " .
            "SEO: Additionally, generate an SEO title (50-60 characters), an SEO description (150-160 characters), and a comma-separated list of 5-10 relevant SEO keywords. " .
            "Respond strictly in JSON format with the following keys: \"translatedTitle\", \"translatedSpot\", \"translatedContent\", \"seoTitle\", \"seoDescription\", \"seoKeywords\". " .
            "Do not include any extra commentary or text.";

        return $this->sendApiRequest($prompt, 'news');
    }


    /**
     * API'ye istek gönderme fonksiyonu
     * @param string $prompt Gönderilecek prompt
     * @param string $type İstek türü (translation veya seo)
     * @return string API yanıtı
     */
    private function sendApiRequest(string $prompt, string $type): mixed
    {
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $data = [
            'model' => 'gpt-4.1-nano',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt]
            ]
        ];

        $ch = curl_init($this->apiEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return false;
        }

        $responseData = json_decode($response, true);
        Log::adminWrite("ChatCompletionController: sendApiRequest - API response: " . json_encode($responseData), "info", "assistant");

        $chatID = $responseData['id'] ?? null;
        $userID = $this->adminID;
        $fingerprint = $responseData['system_fingerprint'] ?? null;
        $promptTokens = $responseData['usage']['prompt_tokens'] ?? 0;
        $completionTokens = $responseData['usage']['completion_tokens'] ?? 0;
        $totalTokens = $responseData['usage']['total_tokens'] ?? 0;

        if (isset($responseData['choices'][0]['message']['content'])) {
            $content = $responseData['choices'][0]['message']['content'];
            $jsonContent = json_decode($content, true);

            $chatData = [
                'user_id' => $userID,
                'chat_id' => $chatID,
                'prompt' => $prompt,
                'response' => $content,
                'promt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $totalTokens,
                'fingerprint' => $fingerprint
            ];
            $addChatResponse = $this->addChatLog($chatData);
            if (!$addChatResponse) {
                Log::adminWrite("ChatCompletionController: sendApiRequest - Chat log add error: " . json_encode($chatData), "error", "assistant");
            } else {
                Log::adminWrite("ChatCompletionController: sendApiRequest - Chat log added: " . json_encode($chatData), "info", "assistant");
            }

            if (json_last_error() === JSON_ERROR_NONE) {
                if ($type === 'translation' && isset($jsonContent['translation'])) {
                    return $jsonContent['translation'];
                } elseif ($type === 'seo' &&
                    isset($jsonContent['seoTitle'], $jsonContent['seoDescription'], $jsonContent['seoKeywords'])) {
                    return json_encode($jsonContent);
                } elseif ($type === 'news' &&
                    isset($jsonContent['translatedTitle'], $jsonContent['translatedSpot'], $jsonContent['translatedContent'],
                        $jsonContent['seoTitle'], $jsonContent['seoDescription'], $jsonContent['seoKeywords'])) {
                    return json_encode($jsonContent);
                }
            }
            Log::adminWrite("ChatCompletionController: sendApiRequest - API response parsing error: jsonContent: " . json_encode($jsonContent), "error", "assistant");
        }

        return false;
    }

}
