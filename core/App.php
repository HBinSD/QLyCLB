<?php
class App {
    protected $controller = 'HomeController';
    protected $action     = 'index';
    protected $params     = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Xử lý Controller
        if (isset($url[0]) && file_exists(__DIR__ . '/../app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        require_once __DIR__ . '/../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // 2. Xử lý Action (Method)
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
                unset($url[1]);
            }
        }

        // 3. Xử lý Params (Tham số)
        $this->params = $url ? array_values($url) : [];

        // 4. Khởi chạy Controller và Method kèm tham số
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    // Hàm phân tích URL từ query string ?url=...
    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
?>