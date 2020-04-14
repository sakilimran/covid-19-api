<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Config\ValidationRules as ValidationRules;

class CountryController {
    private $logger;
    private $validator;

    // Dependency injection via constructor
    public function __construct($depLogger, $depValidator) {
        $this->logger = $depLogger;
        $this->validator = $depValidator;
    }

    // GET /country-cases
    // Lists all country cases
    public function all(Request $request, Response $response) {
        $this->logger->addInfo('GET /country-cases');
        $sourceFile = "https://raw.githubusercontent.com/CSSEGISandData/COVID-19/web-data/data/cases_country.csv";
        $countryCases = $this->csvToArray($sourceFile);
        if ($countryCases) {
            return $response->withJson([
                'success' => true,
                'data' => $countryCases
            ], 200);
        }else {
            return $response->withJson([
                'success' => false,
                'errors' => 'data not found'
            ], 400);
        }
    }

    // GET /country-cases/{name}
    // Retrieve country cases data by NAME
    public function find(Request $request, Response $response, $args) {
        $this->logger->addInfo('GET /country-cases/'.$args['name']);
        $user = $request->getAttribute('user');
        $category = $user->categories()->withCount('todos')->find($args['id']);
        if ($category) {
            return $response->withJson([
                'success' => true,
                'data' => $category
            ], 200);
        } else {
            return $response->withJson([
                'success' => false,
                'errors' => 'Category not found'
            ], 400);
        }
    }

    // php function to convert csv to array format
    private function csvToArray($sourceFile) {
        // open csv file
        if (!($fp = fopen($sourceFile, 'r'))) {
            die("Can't open file...");
        }

        //read csv headers
        $key = fgetcsv($fp,"1024",",");

        // parse csv rows into array
        $arr = array();
        while ($row = fgetcsv($fp,"1024",",")) {
            $arr[] = array_combine($key, $row);
        }

        // release file handle
        fclose($fp);

        // encode array to json
        return $arr;
    }
}