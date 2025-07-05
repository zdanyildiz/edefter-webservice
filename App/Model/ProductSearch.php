<?php
class ProductSearch {
    private $db;
    private $json;

    public function __construct($db, $json) {
        $this->db = $db;
        $this->json = $json;
    }

    public function productSearch($query) {
        Log::write("ProductSearch::productSearch - parseQuery", "info");
        $params = $this->parseQuery($query);

        if (empty($params['q']) || empty($params['languageID'])) {
            return [];
        }

        $term = $params['q'];
        $languageID = intval($params['languageID']);
        unset($params['q'], $params['languageID']);

        if ($languageID === 0) {
            return [];
        }

        //Log::write("ProductSearch::productSearch - getPagination", "info");
        $pagination = $this->getPagination($params);

        //Log::write("ProductSearch::productSearch - createWhereClause", "info");
        $whereClause = $this->createWhereClause($term, $params);

        $startFrom = ($pagination['currentPage'] - 1) * $pagination['resultsPerPage'];

        //Log::write("ProductSearch::productSearch - buildSearchSql", "info");
        $sql = $this->buildSearchSql($whereClause['where'], $startFrom, $pagination['resultsPerPage']);
        //Log::write("ProductSearch::productSearch - buildSearchSql - sql: " . $sql, "info");

        $executeParams = array_merge($whereClause['executeParams'], [":languageID" => $languageID]);
        //Log::write("ProductSearch::productSearch - buildSearchSql - executeParams: " . json_encode($executeParams), "info");

        $cacheKey = md5($sql . json_encode($executeParams));

        //Log::write("ProductSearch::productSearch - getCachedResult", "info");
        $cachedResult = $this->getCachedResult($cacheKey);

        if ($cachedResult) {
            return $cachedResult;
        }

        //Log::write("ProductSearch::productSearch - fetchAndCacheResults", "info");
        return $this->fetchAndCacheResults($sql, $executeParams, $pagination, $cacheKey, $term, $params);
    }

    private function parseQuery($query)
    {
        //Log::write("parseQuery - Gelen veri: " . var_export($query, true), "info");

        // Eğer $query bir array ise, stringe dönüştürün veya uygun şekilde işleyin
        if (is_array($query)) {
            // Array ise, uygun şekilde işleyin veya hata verin
            //Log::write("parseQuery - Hata: Beklenen string, gelen array.", "error");
            // Gerekirse array'i query string'e dönüştürün
            $query = http_build_query($query);
            //Log::write("parseQuery - Array stringe dönüştürüldü: $query", "info");
        }

        // Hataları yakalamak için set_error_handler kullanın
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        });

        try {
            $query = str_replace('+', '%2B', $query);
            //Log::write("parseQuery - Artı işaretleri çevrildi: $query", "info");

            parse_str($query, $params);
            //Log::write("parseQuery - Parametrelere ayrıldı: " . json_encode($params), "info");

            if (empty($params)) {
                Log::write("parseQuery - Boş değer döndü: $query", "warning");
                return [];
            }

            array_walk_recursive($params, function (&$value) {
                $value = str_replace('%2B', '+', $value);
            });
            //Log::write("parseQuery - Artı işaretleri geri çevrildi: " . json_encode($params), "info");

            //Log::write("parseQuery - Sonuç: " . json_encode($params), "info");
            return $params;
        } catch (Exception $e) {
            //Log::write("parseQuery - Exception: " . $e->getMessage(), "error");
            return [];
        } catch (Error $e) {
            //Log::write("parseQuery - Error: " . $e->getMessage(), "error");
            return [];
        } finally {
            restore_error_handler();
        }
    }

    private function getPagination(&$params) {
        $resultsPerPage = $params['limit'] ?? 20;
        $currentPage = $params['page'] ?? 1;

        unset($params['limit'], $params['page']);

        return [
            'resultsPerPage' => intval($resultsPerPage),
            'currentPage' => intval($currentPage)
        ];
    }

    private function createWhereClause(string $term, $params) {

        $where = "(
        sayfa.sayfaad LIKE :term OR 
        sayfa.sayfaicerik LIKE :term1 OR 
        seo.baslik LIKE :term2 OR 
        seo.aciklama LIKE :term3 OR 
        seo.kelime LIKE :term4 OR 
        urunozellikleri.urunstokkodu LIKE :term5
    )";

        $executeParams = [
            ":term" => "%" . $term . "%",
            ":term1" => "%" . $term . "%",
            ":term2" => "%" . $term . "%",
            ":term3" => "%" . $term . "%",
            ":term4" => "%" . $term . "%",
            ":term5" => "%" . $term . "%"
        ];

        if (!empty($params)) {
            $i = 0;
            foreach ($params as $key => $value) {
                // Eğer fiyat filtreleri varsa bunları variant_properties tablosundan sorgulayacağız
                if ($key === "maxPrice") {
                    $where .= " AND EXISTS (
                    SELECT 1
                    FROM variant_properties vp_price
                    WHERE vp_price.variant_id = urunozellikleri.sayfaid
                    AND vp_price.variant_selling_price <= :maxPrice
                )";
                    $executeParams[":maxPrice"] = $value;
                    continue;
                } elseif ($key === "priceRange" && is_array($value)) {
                    if (!empty($value["min"])) {
                        $where .= " AND EXISTS (
                        SELECT 1
                        FROM variant_properties vp_price
                        WHERE vp_price.variant_id = urunozellikleri.sayfaid
                        AND vp_price.variant_selling_price >= :minPrice
                    )";
                        $executeParams[":minPrice"] = $value["min"];
                    }
                    if (!empty($value["max"])) {
                        $where .= " AND EXISTS (
                        SELECT 1
                        FROM variant_properties vp_price
                        WHERE vp_price.variant_id = urunozellikleri.sayfaid
                        AND vp_price.variant_selling_price <= :maxPrice
                    )";
                        $executeParams[":maxPrice"] = $value["max"];
                    }
                    continue;
                }

                $i++;
                // Diğer varyant filtrelerini variant_properties tablosu üzerinden eşleştiriyoruz
                if ($i == 1) {
                    $where .= " AND EXISTS (
                        SELECT 1
                        FROM variant_properties vp
                        WHERE vp.variant_id = urunozellikleri.sayfaid
                        AND vp.attribute_name = :attribute_name$i
                        AND vp.attribute_value = :attribute_value$i
                    )";
                } else {
                    $where .= " AND EXISTS (
                        SELECT 1
                        FROM variant_properties vp_$i
                        WHERE vp_$i.variant_id = urunozellikleri.sayfaid
                        AND vp_$i.attribute_name = :attribute_name$i
                        AND vp_$i.attribute_value = :attribute_value$i
                    )";
                }

                $executeParams[":attribute_name$i"] = $key;
                $executeParams[":attribute_value$i"] = $value;
            }
        }

        return ['where' => $where, 'executeParams' => $executeParams];
    }


    private function buildSearchSql($where, $startFrom, $resultsPerPage) {
        return "
            SELECT 
                urunozellikleri.sayfaid
            FROM urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE 
                (sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID) AND $where
            GROUP BY 
                urunozellikleri.sayfaid
            LIMIT 
                $startFrom, $resultsPerPage
        ";
    }

    private function getCachedResult($cacheKey) {
        $jsonData = $this->json->readJson(["Search", $cacheKey]);
        if ($jsonData != null) {
            return [
                "searchResultUniqID" => $cacheKey,
                "searchResultProductIDs" => $jsonData["searchResultProductIDs"],
                'searchResultTotalPages' => $jsonData["searchResultTotalPages"],
                'searchResultCurrentPage' => $jsonData["searchResultCurrentPage"],
                "searchTotalResults" => $jsonData["searchTotalResults"],
                "searchResultsPerPage" => $jsonData["searchResultsPerPage"]
            ];
        }
        return null;
    }

    private function fetchAndCacheResults($sql, $executeParams, $pagination, $cacheKey, $term,$params) {
        $data = $this->db->select($sql, $executeParams);
        $searchResults = [
            "searchResultUniqID" => $cacheKey,
            "searchResultProductIDs" => [],
            'searchResultTotalPages' => 0,
            'searchResultCurrentPage' => $pagination['currentPage'],
            "searchTotalResults" => 0,
            "searchResultsPerPage" => $pagination['resultsPerPage']
        ];

        if (count($data) > 0) {
            $searchResults = $this->calculateTotalPages($term, $pagination, $executeParams, $searchResults, $params);
            $searchResults['searchResultProductIDs'] = array_column($data, "sayfaid");
            $this->json->createJson(["Search", $cacheKey], $searchResults);
        }

        return $searchResults;
    }

    private function calculateTotalPages($term, $pagination, $executeParams, $searchResults, $params) {
        $whereClause = $this->createWhereClause($term, $params);
        $searchCountSql = "
            SELECT 
                COUNT(DISTINCT urunozellikleri.sayfaid) as total
            FROM urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE (sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID) AND {$whereClause['where']}";

        $stmt = $this->db->prepare($searchCountSql);
        $stmt->execute($executeParams);
        $total = $stmt->fetchColumn();

        $totalPages = ceil($total / $pagination['resultsPerPage']);
        $searchResults['searchResultTotalPages'] = $totalPages;
        $searchResults['searchTotalResults'] = $total;

        return $searchResults;
    }
}
