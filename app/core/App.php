<?php
/**
 * Class App
 * 
 * Router utama aplikasi yang mengatur URL routing dan memanggil controller.
 * Menggunakan pattern: /controller/method/param1/param2/...
 */
class App {
    // Default controller, method, dan params
    protected $controller = 'DashboardController';
    protected $method = 'index';
    protected $params = [];

    /**
     * Constructor - Parse URL dan route ke controller
     */
    public function __construct() {
        $url = $this->parseUrl();

        // Cek apakah controller exist
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerFile = ROOT_PATH . '/app/controllers/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        // Require controller file
        require_once ROOT_PATH . '/app/controllers/' . $this->controller . '.php';
        
        // Instantiate controller
        $this->controller = new $this->controller;

        // Cek method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call controller method dengan params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse URL dari REQUEST_URI
     * 
     * @return array Array URL segments
     */
    private function parseUrl() {
        if (isset($_GET['url'])) {
            // Remove trailing slash dan sanitize
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            // Explode menjadi array
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
