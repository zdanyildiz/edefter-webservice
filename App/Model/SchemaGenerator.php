<?php

class SchemaGenerator
{
    public function generateSchema($contentType, $contentData)
    {
        switch ($contentType) {
            case 'homepage':
                return $this->generateHomepageSchema($contentData);
            case 'category':
                return $this->generateCategorySchema($contentData);
            case 'page':
                return $this->generatePageSchema($contentData);
            case 'search':
                return $this->generateSearchSchema($contentData);
            case 'product':
                return $this->generateProductSchema($contentData);
            default:
                return null;
        }
    }

    /**
     * contentData içeriğininde neler gerekli
     *
     * @param $contentData array()
     * @return string
     */
    private function generateHomepageSchema($contentData)
    {
        $schema = [
            "@context" => "http://schema.org",
            "@type" => "WebSite",
            "name" => $contentData['siteName'],
            "url" => $contentData['url'],
            "description" => $contentData['description'],
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => $contentData['url'],
                "query-input" => "required name=search_term_string"
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => $contentData['siteName'],
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => $contentData['logo']
                ]
            ]
        ];
        return json_encode($schema);
    }

    private function generateCategorySchema($contentData)
    {
        $schema = [
            "@context" => "http://schema.org",
            "@type" => "WebPage",
            "name" => $contentData['title'],
            "description" => $contentData['description'],
            "url" => $contentData['url'],
            "publisher" => [
                "@type" => "Organization",
                "name" => $contentData['siteName'],
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => $contentData['logo']
                ]
            ]
        ];
        return json_encode($schema);
    }

    private function generatePageSchema($contentData)
    {
        $schema = [
            "@context" => "http://schema.org",
            "@type" => "WebPage",
            "name" => $contentData['title'],
            "description" => $contentData['description'],
            "url" => $contentData['url'],
            "publisher" => [
                "@type" => "Organization",
                "name" => $contentData['siteName'],
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => $contentData['logo']
                ]
            ]
        ];
        return json_encode($schema);
    }

    private function generateSearchSchema($contentData)
    {
        $schema = [
            "@context" => "http://schema.org",
            "@type" => "WebSite",
            "name" => $contentData['siteName'],
            "url" => $contentData['url'],
            "description" => $contentData['description'],
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => $contentData['url'] . "?q={search_term_string}",
                "query-input" => "required name=search_term_string"
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => $contentData['siteName'],
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => $contentData['logo']
                ]
            ]
        ];
        return json_encode($schema);
    }

    private function generateProductSchema($contentData)
    {
        $price = $contentData['price'];
        $price = str_replace(".", "", $price);
        $price = str_replace(",", ".", $price);
        $schema = [
            "@context" => "http://schema.org",
            "@type" => "Product",
            "name" => $contentData['title'],
            "description" => $contentData['description'],
            "url" => $contentData['url'],
            "image" => $contentData['image'],
            "brand" => [
                "name" => $contentData['brand']
            ],
            "offers" => [
                "@type" => "Offer",
                "priceCurrency" => $contentData['priceCurrency'],
                "price" => $price,
                "availability" => "http://schema.org/InStock"
            ]
        ];
        return json_encode($schema);
    }
}