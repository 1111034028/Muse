<?php

namespace project\core;

class Router
{
    private array $routes = [];

    public function __construct(
        private Request $request,
        private Response $response
    ) {
    }

    public function get(string $path, array $targetMethod)
    {
        $this->routes['GET'][$path] = $targetMethod;
    }

    public function post(string $path, array $targetMethod)
    {
        $this->routes['POST'][$path] = $targetMethod;
    }

    public function delete(string $path, array $targetMethod)
    {
        $this->routes['DELETE'][$path] = $targetMethod;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        if ($method === 'OPTIONS') {
            return $this->response->json();
        }
        // // if not found return 404
        // if (!isset($this->routes[$method][$path])) {
        //     return $this->response->json(null, 404);
        // }
        // $targetMethod = $this->routes[$method][$path];
        // return $this->response->json(
        //     call_user_func(
        //         [new $targetMethod[0](), $targetMethod[1]],
        //         $this->request
        //     )
        // );
        error_log("Resolving route: Method = $method, Path = $path");
        $callback = $this->routes[$method][$path] ?? null;

        if (!$callback) {
            $this->response->setStatusCode(404);
            $this->response->json(['error' => 'Route not found']);
            return;
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