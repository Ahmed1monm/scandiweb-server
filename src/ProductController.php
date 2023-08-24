<?php

class ProductController
{
    private ProductGateway $gateway;
    public function __construct(ProductGateway $gateway)
    {
        $this->gateway = $gateway;
    }
    
    public function processRequest(string $method): void
    {
            $this->processCollectionRequest($method);
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;
                
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                
                $errors = $this->getValidationErrors($data);
                
                if ( ! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                
                $id = $this->gateway->create($data);
                http_response_code(201);
                echo json_encode([
                    "message" => "Product created",
                    "id" => $id
                ]);
                break;

            case "DELETE":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                $response = $this->gateway->massDelete($data['products']);

                if ($response === 0)
                {
                    http_response_code(400);
                    echo json_encode([
                        "message" => "No products deleted, make sure that you sent exists SKU",
                    ]);
                }else{
                    http_response_code(204);
                }
                break;

            default:
                http_response_code(405);
                header("Allow: GET, POST, DELETE");
        }
    }

    private function getValidationErrors(array $data): array
    {
        $errors = [];

        if (empty($data["name"])) {
            $errors[] = "name is required";
        }
        if (empty($data["SKU"])) {
            $errors[] = "SKU is required";
        }
        if (empty($data["price"])) {
            $errors[] = "price is required";
        } elseif (filter_var($data["price"], FILTER_VALIDATE_FLOAT) === false) {
            $errors[] = "price must be a float";
        }

        return $errors;
    }
}









