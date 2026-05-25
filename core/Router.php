<?php
class Router {
    private array $routes = [];

    public function get(string $url, string $controller, string $method): void {
        $this->routes['GET'][$url] = ['controller' => $controller, 'method' => $method];
    }

    public function post(string $url, string $controller, string $method): void {
        $this->routes['POST'][$url] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch(): void {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $url = $_GET['url'] ?? '';
        $url = trim($url, '/');

        $params = [];
        $matched = false;

        if (isset($this->routes[$httpMethod])) {
            foreach ($this->routes[$httpMethod] as $route => $action) {
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $url, $matches)) {
                    array_shift($matches);
                    $params = $matches;
                    $matched = true;

                    $controllerClass = $action['controller'];
                    $controllerMethod = $action['method'];

                    require_once BASE_PATH . '/app/controllers/' . $controllerClass . '.php';
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $controllerMethod], $params);
                    break;
                }
            }
        }

        if (!$matched) {
            http_response_code(404);
            require_once BASE_PATH . '/app/views/layouts/404.php';
        }
    }
}
