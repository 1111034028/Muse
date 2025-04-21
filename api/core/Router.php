<?php

namespace project\core;

class Router
{
    private array $routes = [];
    private array $params = [];

    public function __construct(
        private Request $request,
        private Response $response
    ) {
    }

    public function get(string $path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function patch(string $path, $callback)
    {
        $this->routes['PATCH'][$path] = $callback;
    }

    public function delete(string $path, $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
    }

    private function matchDynamicRoute(string $method, string $path)
    {
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        // Reset params
        $this->params = [];
        
        foreach ($this->routes[$method] as $route => $callback) {
            // Convert route parameters to regex pattern
            $pattern = $this->convertRouteToRegex($route);
            
            if (preg_match($pattern, $path, $matches)) {
                // Extract parameter values
                $paramNames = $this->extractParamNames($route);
                foreach ($paramNames as $index => $name) {
                    $this->params[$name] = $matches[$index + 1];
                }
                
                return $callback;
            }
        }
        
        return null;
    }
    
    private function convertRouteToRegex(string $route)
    {
        // Replace route parameters like :id with regex pattern
        $pattern = preg_replace('/:([^\/]+)/', '([^/]+)', $route);
        
        // Escape forward slashes and add start/end anchors
        return '#^' . $pattern . '$#';
    }
    
    private function extractParamNames(string $route)
    {
        $paramNames = [];
        if (preg_match_all('/:([^\/]+)/', $route, $matches)) {
            $paramNames = $matches[1];
        }
        return $paramNames;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        if ($method === 'OPTIONS') {
            return $this->response->json();
        }

        error_log("Resolving route: Method = $method, Path = $path");
        
        $callback = $this->routes[$method][$path] ?? null;
        
        if (!$callback) {
            $callback = $this->matchDynamicRoute($method, $path);
        }

        if (!$callback) {
            $this->response->setStatusCode(404);
            $this->response->json(['error' => 'Route not found']);
            return;
        }

        if (!empty($this->params)) {
            $this->request->setParams($this->params);
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $method = $callback[1];
            $result = call_user_func([$controller, $method], $this->request);
            $this->response->json($result); 
        } else {
            $result = call_user_func($callback, $this->request);
            $this->response->json($result); 
        }
    }
}

