<?php

class Search
{
    private Database $db;
    private $json;
    private Product $productModel;

    public function __construct($db,$json)
    {
        $this->db = $db;
        $this->json = $json;
        $this->productModel = new Product($db,$json);
    }

    public function search($query)
    {
        //die($query);
        //languageID=2&q=profile&Material=Reinforced Polyamide (PA) + Zinc Plating (ZN)

        //query dizesinde q="arama terimi",$dilid=languageID olarak geliyor. Ayrıca ren, sayfa gibi filtreler de gelebilir. Arama terimini şu sütunlarda aratacağız. seo.baslik,seo.aciklama,seo.kelime,sayfa.sayfaad,sayfa.sayfaicerik ayrıca kategori parametresi varsa kategori.kategori=categoryID, renk parametresi varsa urunozellikler.urunrenkid=colorID
        //query dizesinde hangi parametreler var kontrol edip ayrıştıralım

        $query = str_replace('+', '%2B', $query);
        parse_str($query, $params);

        array_walk_recursive($params, function (&$value) {
            $value = str_replace('%2B', '+', $value);
        });

        //print_r($params);exit();
        //Array ( [languageID] => 2 [q] => profile [Material] => Reinforced Polyamide (PA) Zinc Plating (ZN) )

        $where="";
        $executeParams = [];
        $resultsPerPage = 20;
        $currentPage = 1;


        if(empty($params['q'])) {
            return [];
        }
        else{
            $term = $params['q'];
            $where = "(
                sayfa.sayfaad LIKE :term OR 
                sayfa.sayfaicerik LIKE :term1 OR 
                seo.baslik LIKE :term2 OR 
                seo.aciklama LIKE :term3 OR 
                seo.kelime LIKE :term4 OR 
                urunozellikleri.urunstokkodu LIKE :term5
                )";
            $executeParams = [
                ":term" => "%".$term."%",
                ":term1" => "%".$term."%",
                ":term2" => "%".$term."%",
                ":term3" => "%".$term."%",
                ":term4" => "%".$term."%",
                ":term5" => "%".$term."%"
            ];

            unset($params['q']);
        }

        if(empty($params['dilid']) && empty($params['languageID'])) {
            return [];
        }

        $languageID = $params['dilid'] ?? $params['languageID'];
        $executeParams[":languageID"] = $languageID;

        unset($params['dilid']);
        unset($params['languageID']);

        if(isset($params["sayfa"])||isset($params["page"])){
            $currentPage = $params["sayfa"] ?? $params["page"];
        }

        unset($params["sayfa"]);
        unset($params["page"]);

        if(isset($params["limit"])){
            $resultsPerPage = $params["limit"];
        }

        unset($params["limit"]);

        $i=0;
        foreach ($params as $key => $value) {
            $i++;
            $where .= " AND JSON_SEARCH(variantProperties, 'one', :key$i, NULL, '$[*].variantProperties[*].attribute.name') IS NOT NULL AND JSON_SEARCH(variantProperties, 'one', :value$i, NULL, '$[*].variantProperties[*].attribute.value') IS NOT NULL";
            $executeParams[":key$i"] = $key;
            $executeParams[":value$i"] = $value;
        }


        $startFrom = ($currentPage-1) * $resultsPerPage;

        $sql = "
            SELECT 
                urunozellikleri.sayfaid
            FROM urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE 
                (sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID) AND ".$where."
            GROUP BY 
                urunozellikleri.sayfaid
            LIMIT 
                $startFrom,$resultsPerPage
        ";

        //echo $sql."<br>";print_r($executeParams);exit();

        // önce sql parametremizi, $where ve $executeParams verilerini birleştirip uniq hale getirip /Public/Json/Search altında aratıp daha önce bu arama yapılmış mı bakalım, yapılmış ise sonuçları dönelim

        //$executeParams string yapalım
        $executeParamsJson = json_encode($executeParams);

        $uniqSql = md5($sql.$where.$executeParamsJson);
        //die($uniqSql);
        $searchResults= [
            "searchResultUniqID"=>$uniqSql,
            "searchResultProductIDs"=>[],
            "searchResultProducts"=>[],
            "searchResultTotalPages"=>"",
            "searchResultCurrentPage"=>"",
            "searchTotalResults"=>"0",
            "searchResultsPerPage"=>$resultsPerPage
        ];
        $jsonData = $this->json->readJson(["Search",$uniqSql]);
        if($jsonData!=null){

            $searchResults["searchResultProductIDs"] = $jsonData["searchResultProductIDs"];
            $searchResults['searchResultTotalPages'] = $jsonData["searchResultTotalPages"];
            $searchResults['searchResultCurrentPage'] = $jsonData["searchResultCurrentPage"];
            $searchResults["searchTotalResults"] = $jsonData["searchTotalResults"];

            /*foreach ($jsonData["searchResultProductIDs"] as $searchResultId) {
                $searchResults['searchResultProducts'][] = $this->productModel->getProductByID($searchResultId);
            }*/

            return $searchResults;
        }

        $data =  $this->db->select($sql,$executeParams);
        //print_r($data);exit();
        if(count($data)>0){

            $searchCountSql="
                SELECT 
                    COUNT(DISTINCT urunozellikleri.sayfaid) as total
                FROM urunozellikleri
                    INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                        INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                        INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                            INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
                WHERE (sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID) AND ".$where;
            $stmt = $this->db->prepare($searchCountSql);
            $stmt->execute($executeParams);
            $total = $stmt->fetchColumn();
            //
            $totalPages = ceil($total / $resultsPerPage);
            $searchResults['searchResultTotalPages'] = $totalPages;
            $searchResults['searchResultCurrentPage'] = $currentPage;
            $searchResults['searchTotalResults'] = $total;

            $searchResultIds = array_column($data, "sayfaid");
            $searchResults['searchResultProductIDs'] =$searchResultIds;

            $this->json->createJson(["Search",$uniqSql],$searchResults);

            /*foreach ($searchResultIds as $searchResultId) {
                $searchResults['searchResultProducts'][] = $this->productModel->getProductByID($searchResultId);
            }*/

        }
        return $searchResults;
    }
}
?>