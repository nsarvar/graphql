<?php
require_once("vendor/autoload.php");

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;

try {

    $merchantType = new ObjectType([
        'name'=>'Merchant',
        'fields' => [
            "_id" => ['type'=>Type::string()],
            "name" => ['type'=>Type::string()],
            "organization" => ['type'=>Type::string()],
            "type" => ['type'=>Type::string()],
            "type" => ['type'=>Type::string()],
            "date" => ['type'=>Type::string()],
            "email" => ['type'=>Type::string()]
        ]
    ]);

    $rootQuery = new ObjectType([
        'name'=>'Root',
        'fields'=>[
            "merchant"=> [
                'type'=>$merchantType,
                'args'=>[
                    "id"=>Type::string()
                ],
                'resolve' => function ($root, $args){
                    $merchants = json_decode(file_get_contents('data.json'), true)['merchants'];

                    $key = array_search($args['id'], array_column($merchants, '_id'));

                    return ($key)?$merchants[$key]:[];
                }
            ],
            "merchants"=> [
                'type'=> Type::listOf($merchantType),
                'args'=>[
                    "page"=>Type::string()
                ],
                'resolve' => function($root, $args){

                    $merchants = json_decode(file_get_contents('data.json'), true);
                    $merchants = $merchants['merchants'];

                    if($args['page'] && $args['page']>0){
                        $perPage = 5;    
                        $startPage = ($args['page']-1)*$perPage;
                        $merchants = array_slice($merchants, $startPage, $perPage);
                    }

                    return $merchants;
                }
            ]
        ]
    ]);

    $queryType = new ObjectType([
        'name' => 'user',
        'fields' => [
            'echo' => [
                'type' => Type::string(),
                'args' => [
                    'message' => ['type' => Type::string()],
                ],
                'resolve' => function ($root, $args) {
                    return $args['message'];
                }
            ],
        ],
    ]);
    // See docs on schema options:
    // http://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
    $schema = new Schema([
        'query' => $rootQuery
    ]);

    $query = file_get_contents('php://input');
    $result = GraphQL::executeQuery($schema, $query);

    $output = $result->toArray();

} catch (\Exception $e) {
    $output = [
        'error' => [
            'message' => $e->getMessage()
        ]
    ];
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($output);
?>
