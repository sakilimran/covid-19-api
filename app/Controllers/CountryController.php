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
    protected static $timeline_data_url = 'https://dashboards-dev.sprinklr.com/data/9043/global-covid19-who-gis.json';
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

    // GET /all
    // Lists world cases
    public function all(Request $request, Response $response)
    {
        $this->logger->addInfo('GET /all');

        $crawler_data = $this->crawler();

        $item = $crawler_data[8];

        $data =array();
        foreach (static::$world_cases_titles as $key => $value) {
            $data[$value] = $value == 'title' ? $item[$key+1] : intval($item[$key+1]);
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

    // GET /countries
    // Lists all country cases
    public function countries(Request $request, Response $response) {
        $this->logger->addInfo('GET /countries');

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

    // GET /countries/{name}
    // Retrieve specific country cases data by NAME
    public function find(Request $request, Response $response, $args) {
        $this->logger->addInfo('GET /countries/'.$args['name']);

        $crawler_data = $this->crawler();
        $country_key = array_search(strval($args['name']), array_column($crawler_data, 1));

        $item = $crawler_data[$country_key+1];

        $data =array();
        foreach (static::$country_titles as $key => $value) {
            $data[$value] = $value == 'country' ? $item[$key+1] : intval($item[$key+1]);
        }
        $data['flag_emoji'] = Country::generateEmoji($item[1]);

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

    // GET /timeline
    // Lists timeline of cases
    public function timeline(Request $request, Response $response) {
        $this->logger->addInfo('GET /timeline');

        // Get timeline data from WHO source by curl call
        $source_data = $this->who_source_curl_call();
        $totalDataPerDay = array();

        if($source_data) {
            $json_response = json_decode($source_data);

            // All timeline data from the json file
            $timeline_data = $json_response->rows;

            // Group data by timestamp (Date-wise)
            $groupedByTimestamp = array();
            foreach($timeline_data as $timeline_item)
            {
                $groupedByTimestamp[$timeline_item[0]][] = $timeline_item;
            }

            // Calculate Date-wise (timestamp) total data for 'todayDeath', 'totalDeath', 'todayCases', 'totalCases'
            foreach($groupedByTimestamp as $key => $value) {
                $totalDataPerDay[$key]['todayDeaths'] = array_sum(array_column($value, 3));
                $totalDataPerDay[$key]['totalDeaths'] = array_sum(array_column($value, 4));
                $totalDataPerDay[$key]['todayCases'] = array_sum(array_column($value, 5));
                $totalDataPerDay[$key]['totalCases'] = array_sum(array_column($value, 6));
            }

            // Sort final data by timestamp (Date)
            ksort($totalDataPerDay);
        }

        // Output data as JSON
        if ($totalDataPerDay) {
            return $response->withJson([
                'success' => true,
                'data' => $totalDataPerDay
            ], 200);
        }else {
            return $response->withJson([
                'success' => false,
                'errors' => 'data not found'
            ], 400);
        }
    }

    // GET /timeline{country_code}
    // Lists timeline of cases for specific country by country_code
    public function country_timeline(Request $request, Response $response, $args) {
        $this->logger->addInfo('GET /timeline/'.$args['country_code']);

        // Get timeline data from WHO source by curl call
        $source_data = $this->who_source_curl_call();
        $totalDataPerDay = array();

        if($source_data) {
            $json_response = json_decode($source_data);

            // All timeline data from the json file
            $timeline_data = $json_response->rows;

            // Filter data by country code
            $filterBy = $args['country_code'];

            $data_by_country_code = array_filter($timeline_data, function ($var) use ($filterBy) {
                return ($var[1] == $filterBy);
            });

            // Group data by timestamp (Date-wise)
            $groupedByTimestamp = array();
            foreach($data_by_country_code as $timeline_item)
            {
                $groupedByTimestamp[$timeline_item[0]] = array(
                    'todayDeaths' => $timeline_item[3],
                    'totalDeaths' => $timeline_item[4],
                    'todayCases' => $timeline_item[5],
                    'totalCases' => $timeline_item[6],
                );
            }

            // Sort final data by timestamp (Date)
            ksort($groupedByTimestamp);

            $data['country'] = Country::generateCountryName($args['country_code']);
            $data['flag_emoji'] = Country::generateEmoji($data['country']);
            $data['cases'] = $groupedByTimestamp;
        }

        // Output data as JSON
        if ($data) {
            return $response->withJson([
                'success' => true,
                'data' => $data
            ], 200);
        }else {
            return $response->withJson([
                'success' => false,
                'errors' => 'data not found'
            ], 400);
        }
    }

    // Get timeline data from WHO source by curl call
    private function who_source_curl_call() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => static::$timeline_data_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $curl_response = curl_exec($curl);
        curl_close($curl);

        if (curl_errno($curl)) {
            return false;
        } else {
            return $curl_response;
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