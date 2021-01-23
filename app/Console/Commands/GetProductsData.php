<?php

namespace App\Console\Commands;

use App\Models\Graph;
use Clue\React\Buzz\Browser;
use Illuminate\Console\Command;
use PhpQuery\PhpQuery;
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
            Graph::create($el);
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
                ->then(function (\Psr\Http\Message\ResponseInterface $response) use (&$urls_per_one, $url) {

                    $domain = "https://" . parse_url($url, PHP_URL_HOST);

                    $urls_per_one = array_merge(
                        $urls_per_one,
                        $this->parse_urls(
                            $response->getBody(),
                            $domain
                        )
                    );

                });

        }
        $loop->run();
        return $urls_per_one;
    }
    public function parse_urls($text, $domain)
    {
        $pq = new PhpQuery();

        $pq->load_str($text);

        $elems = $pq->query('a.product-tile.jsx-725860710');

        $mass = [];

        foreach ($elems as $el) {
            $mass[] = $domain . $el->getAttribute('href');
        }

        return $mass;

    }
    public function get_data($urls_per_one)
    {
        $loop1   = Factory::create();
        $client1 = new Browser($loop1);

        $data = [];

        foreach ($urls_per_one as $url_per_one) {
            $client1
                ->get($url_per_one)
                ->then(function (\Psr\Http\Message\ResponseInterface $response) use (&$data, $url_per_one) {
                    $domain = "https://" . parse_url($url_per_one, PHP_URL_HOST);
                    // print_r($url_per_one);
                    // echo "\r\n";

                    $data[] = $this->parser($response->getBody(), $domain);

                });

        }

        $loop1->run();
        return $data;
    }

    public function parser($text, $domain)
    {
        echo "start parse... \r\n" . PHP_EOL;
        $start = microtime(true);

        $pq = new PhpQuery();

        $pq->load_str($text);

        echo "mid parse... " . (microtime(true) - $start) . "\r\n" . PHP_EOL;

        $mass         = [];
        $wait_per_one = utf8_decode(trim($pq->query('.big-product-card__amount.jsx-3554221871')[0]->nodeValue));
        echo $wait_per_one . "\r\n";
        $koef = 1;
        if (preg_match('/ г/', $wait_per_one)) {
            $entries = preg_split('/ /', $wait_per_one);
            $number  = (int) $entries[0];
            $koef    = 1000 / $number;
        }
        $mass['price'] = ((int) $pq->query('.Price__value_title.jsx-3642073353')[0]->nodeValue) * $koef;
        $mass['brand'] = utf8_decode(trim($pq->query('.BigProductCardTrademarkName.jsx-3555213589')[0]->nodeValue));
        $mass['store'] = (preg_split('/\./', parse_url($domain, PHP_URL_HOST)))[0];
        echo $koef . "\r\n";
        print_r($mass);
        return $mass;

    }
}
