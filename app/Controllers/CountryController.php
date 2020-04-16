<?php
namespace App\Controllers;

use Goutte\Client;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Config\ValidationRules as ValidationRules;
use \App\Models\Country as Country;

class CountryController {
    private $logger;
    private $validator;
    protected static $crawl_url = 'https://www.worldometers.info/coronavirus/';
    protected static $world_cases_titles = [
        'title', 'cases', 'todayCases', 'deaths',
        'todayDeaths', 'recovered', 'activeCases', 'critical',
    ];
    protected static $country_titles = [
        'country', 'cases', 'todayCases', 'deaths',
        'todayDeaths', 'recovered', 'activeCases', 'critical',
    ];

    // Dependency injection via constructor
    public function __construct($depLogger, $depValidator) {
        $this->logger = $depLogger;
        $this->validator = $depValidator;
    }

    // GET /all-cases
    // Lists world cases
    public function all(Request $request, Response $response)
    {
        $this->logger->addInfo('GET /all-cases');

        $crawler_data = $this->crawler();

        $item = $crawler_data[8];

        $data =array();
        foreach (static::$world_cases_titles as $key => $value) {
            $data[$value] = $value == 'title' ? $item[$key] : intval($item[$key]);
        }

        if ($data) {
            return $response->withJson([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return $response->withJson([
                'success' => false,
                'errors' => 'data not found'
            ], 400);
        }
    }

    // GET /country-cases
    // Lists all country cases
    public function countries(Request $request, Response $response) {
        $this->logger->addInfo('GET /country-cases');

        $crawler_data = $this->crawler();
        $countries_data = Country::country_data($crawler_data);

        if ($countries_data) {
            return $response->withJson([
                'success' => true,
                'data' => $countries_data
            ], 200);
        }else {
            return $response->withJson([
                'success' => false,
                'errors' => 'data not found'
            ], 400);
        }
    }

    // GET /country-cases/{name}
    // Retrieve specific country cases data by NAME
    public function find(Request $request, Response $response, $args) {
        $this->logger->addInfo('GET /country-cases/'.$args['name']);

        $crawler_data = $this->crawler();
        $country_key = array_search(strval($args['name']), array_column($crawler_data, 0));

        $item = $crawler_data[$country_key+1];

        $data =array();
        foreach (static::$country_titles as $key => $value) {
            $data[$value] = $value == 'country' ? $item[$key] : intval($item[$key]);
        }
        $data['emoji'] = Country::generateEmoji($item[0]);

        if ($data) {
            return $response->withJson([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return $response->withJson([
                'success' => false,
                'errors' => 'data not found'
            ], 400);
        }
    }

    // Crawl data from source website
    private function crawler() {
        return (new Client())->request('GET', static::$crawl_url)
            ->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
                return $tr->filter('td')->each(function ($td, $j) {
                    return str_replace(',', '', trim($td->text()));
                });
            });
    }
}