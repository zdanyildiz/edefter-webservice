<?php
class ViewLoader
{
    public function loadView($viewName, $data = [])
    {
        // yol kontrolü ve dosya varsa yükleme
        $path = VIEW . $viewName . '.php';

        if (file_exists($path)) {
            // $data dizisini extract methodu ile değişkene dönüştürülür
            if (is_array($data)) {
                extract($data);
            }
            require_once $path;
        } else {
            // hata mesajı
            Log::write("View $viewName not found!", "error");
            throw new Exception("View $viewName not found!");
        }
    }
}