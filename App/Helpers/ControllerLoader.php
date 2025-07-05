<?php

class ControllerLoader
{
    public function loadController($controllerName, $data = [])
    {
        $controllerPath = CONTROLLER . $controllerName . ".php";
        if (file_exists($controllerPath)) {
            // $data dizisini extract methodu ile değişkene dönüştürülür
            if (is_array($data)) {
                extract($data);
            }
            include_once $controllerPath;
        } else {
            $router = $data['router'] ?? null;
            $url = $router->url ?? null;
            //url boşsa sunucudan url alalım
            if (empty($url)) {
                $url = $_SERVER['REQUEST_URI'];
            }
            $ip = $_SERVER['REMOTE_ADDR'];
            //ref url yakalayalım
            $ref = $_SERVER['HTTP_REFERER'] ?? null;
            if (empty($ref)) {
                $ref = "No referrer";
            }
            Log::write("Controller $controllerName not found! [$ip] [$url] [$ref]", "error");
            header("Location: /");
            exit("Controller $controllerName not found!");
        }
    }
}