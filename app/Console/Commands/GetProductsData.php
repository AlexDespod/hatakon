<?php

namespace App\Console\Commands;

use App\Models\Graph;
use Clue\React\Buzz\Browser;
use Clue\React\Buzz\Message\ResponseException;
use DiDom\Document;
use Exception;
use Illuminate\Console\Command;
use PhpQuery\PhpQuery;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;

class GetProductsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $urls_per_all = array(
            "https://auchan.zakaz.ua/ru/categories/buckwheat-auchan/",
            'https://novus.zakaz.ua/ru/categories/buckwheat/',
            'https://metro.zakaz.ua/ru/categories/buckwheat-metro/',
        );

        $urls_per_one = $this->get_urls($urls_per_all);
        $data         = $this->get_data($urls_per_one);

        foreach ($data as $el) {
            print_r($el);
            if (1 == 2) {
                Graph::create($el);
            }

        }
    }
    public function get_urls(array $urls_per_all)
    {
        $loop   = Factory::create();
        $client = new Browser($loop);

        $urls_per_one = [];

        foreach ($urls_per_all as $url) {

            $client
                ->get($url)
                ->then(function (ResponseInterface $response) use (&$urls_per_one, $url) {

                    $domain  = "https://" . parse_url($url, PHP_URL_HOST);
                    $content = $response->getBody();
                    unset($response);
                    $urls_per_one = array_merge(
                        $urls_per_one,
                        $this->parse_urls(
                            $content,
                            $domain
                        )
                    );
                    unset($content);
                });

        }
        $loop->run();
        // print_r($urls_per_one);
        unset($client, $loop);
        echo " urls got \r\n";

        return $urls_per_one;
    }
    public function parse_urls($text, $domain)
    {
        $pq = new PhpQuery();

        $pq->load_str($text);

        $elems = $pq->query('a.product-tile.jsx-725860710');
        unset($pq);
        unset($text);
        $mass = [];

        foreach ($elems as $el) {
            $mass[] = $domain . $el->getAttribute('href');
            unset($el);
        }
        unset($elems);
        return $mass;

    }
    public function get_data($urls_per_one)
    {
        $loop   = Factory::create();
        $client = new Browser($loop);
        $client = $client
            ->withTimeout(false)
            ->withRejectErrorResponse(true)
            ->withResponseBuffer(5024 * 1024);

        $data  = [];
        $start = microtime(true);
        foreach ($urls_per_one as $url_per_one) {
            // echo "DATA REQUESTED !!!!!! \r\n";

            $client
                ->get($url_per_one)
                ->then(function (ResponseInterface $response) use (&$data, $url_per_one) {
                    $domain = "https://" . parse_url($url_per_one, PHP_URL_HOST);
                    echo "DATA GOT !!!!!! \r\n";
                    $content = $response->getBody();
                    unset($response);
                    $temp = $this->parser($content, $domain);
                    unset($content);
                    // echo memory_get_usage() . "\r\n";

                    // echo "\r\n " . $url_per_one . "\r\n";

                    $data[] = $temp;
                    // echo memory_get_usage() . "\r\n";
                    // dd(1);
                }, function (\Exception $e) {
                    if ($e instanceof ResponseException) {
                        $response = $e->getResponse();
                        var_dump($response->getStatusCode(), $response->getReasonPhrase());
                    } else {
                        var_dump($e->getMessage());
                    }
                });

        }

        $loop->run();
        unset($client, $loop);
        echo " data got in " . (microtime(true) - $start) . "\r\n";
        // echo count($data) . " datas got \r\n";
        // echo (count($urls_per_one) - count($data)) . " datas feiled \r\n";
        return $data;
    }

    public function parser(string $text, string $domain)
    {
        echo "start parse... \r\n" . PHP_EOL;
        $start = microtime(true);

        $document = new Document($text);

        echo "mid parse... " . (microtime(true) - $start) . "\r\n" . PHP_EOL;

        unset($text);
        echo memory_get_usage() . "\r\n";

        $wait_per_one = $document->first('.big-product-card__amount.jsx-3554221871')->text();

        echo memory_get_usage() . "\r\n";

        $mass = [];

        // echo $wait_per_one . "\r\n";
        $koef = 1;
        if (preg_match('/ г/', $wait_per_one)) {

            $number = (int) preg_replace('/[^0-9]/', '', $wait_per_one);
            $koef   = 1000 / $number;
        } else {
            $number = (int) preg_replace('/[^0-9]/', '', $wait_per_one);
            $koef   = 1 / $number;
        }
        unset($wait_per_one);
        $mass['price'] = ((int) $document->first('.Price__value_title.jsx-3642073353')->text()) * $koef;

        $mass['brand'] = trim($document->first('.BigProductCardTrademarkName.jsx-3555213589')->text());

        $mass['store'] = (preg_split('/\./', parse_url($domain, PHP_URL_HOST)))[0];
        // echo $koef . "\r\n";
        // print_r($mass);
        echo memory_get_usage() . "\r\n";

        return $mass;

    }
}
