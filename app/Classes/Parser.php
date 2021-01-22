<?php
namespace App\Classes;

use Clue\React\Buzz\Browser;
use PhpQuery\PhpQuery;
use React\EventLoop\Factory;

class Parser
{
    public function get_grechka()
    {
        $products = [];
        $str      = '';
        $start    = microtime(true);
        echo "Fetching..." . "<br>";
        $count = 0;

        $loop   = Factory::create();
        $client = new Browser($loop);
        $mass   = array(
            "https://auchan.zakaz.ua/ru/categories/buckwheat-auchan/",
            'https://novus.zakaz.ua/ru/categories/buckwheat/',
            'https://metro.zakaz.ua/ru/categories/buckwheat-metro/',
        );

        foreach ($mass as $url) {
            // dd($url);
            $client
                ->get($url)
                ->then(function (\Psr\Http\Message\ResponseInterface $response) use (&$products, &$url) {
                    // dd($products);
                    // echo $url;
                    $domain = preg_split('/\//', $url);

                    $products = array_merge(
                        $products,
                        $this->parser(
                            $response->getBody(),
                            $domain[0] . $domain[1] . $domain[2]
                        )
                    );

                });

        }
        $loop->run();
        echo "...done in " . (microtime(true) - $start) .
        '<br> ' .
        'count :' . $count .
        '<br>'
        . ' arry size : ' . strlen($str) . PHP_EOL;

        dd($products);

        // $rollingCurl = new RollingCurl();

        // $rollingCurl
        // ->get('https://auchan.zakaz.ua/ru/categories/buckwheat-auchan/')
        // ->get('https://novus.zakaz.ua/ru/categories/buckwheat/')
        // ->get('https://metro.zakaz.ua/ru/categories/buckwheat-metro/')

        // ->setCallback(function (Request $request, RollingCurl $rollingCurl) use (&$products, &$str) {
        // dd(__DIR__);
        // $fp = fopen('D:\OpenServer\domains\hatakon/\testcache.txt', 'w');
        // fwrite($fp, $request->getResponseText());
        // dd(md5_file('D:\OpenServer\domains\hatakon/\testcache.txt'), md5($request->getResponseText()));
        // $text = file_get_contents('D:\OpenServer\domains\hatakon/\testcache.txt');
        // dd($text == $request->getResponseText());
        // if (md5_file('D:\OpenServer\domains\hatakon/\testcache.txt') == md5($request->getResponseText())) {
        //     echo "нутро одинаково";
        // } else {
        //     echo "два разных файла";
        // }

        // $domain = preg_split('/\//', $request->getUrl());

        // $products = array_merge(
        //     $products,
        //     $this->parser(
        //         $request->getResponseText(),
        //         $domain[0] . $domain[1] . $domain[2]
        //     )
        // );
        // $str = $str . $request->getResponseText();
        //     $rollingCurl->clearCompleted();
        //     $rollingCurl->prunePendingRequestQueue();

        // })
        // ->setSimultaneousLimit(300)
        // ->execute();
        // $products = $this->parser($str, 'https: //metro.zakaz.ua');

    }

    public function parser($text, $domain)
    {
        echo "start parse... <br>" . PHP_EOL;
        $start = microtime(true);

        $pq = new PhpQuery();

        $pq->load_str($text);

        $elems = $pq->query('a.product-tile.jsx-725860710');

        $mass = [];

        foreach ($elems as $key => $el) {
            $new_html = $pq->outerHTML($el);
            $pq->load_str($new_html);
            $title                        = $el->getAttribute('title');
            $src                          = $el->getAttribute('href');
            $price                        = trim($pq->query('.Price__value_caption.jsx-3642073353')[0]->nodeValue);
            $weight                       = trim($pq->query('.product-tile__weight.jsx-725860710')[0]->nodeValue);
            $img                          = trim($pq->query('.product-tile__image-i.jsx-725860710')[0]->getAttribute('src'));
            $mass[$key]['title']          = utf8_decode($title);
            $mass[$key]['brand']          = utf8_decode($title);
            $mass[$key]['weight_per_one'] = utf8_decode(utf8_decode($weight));
            $mass[$key]['img']            = $img;
            $mass[$key]['src']            = $domain . $src;
            $koef                         = 1;
            if (preg_match('/ г/', $mass[$key]['weight_per_one'])) {
                $entries = preg_split('/ /', $mass[$key]['weight_per_one']);
                $number  = (int) $entries[0];
                $koef    = 1000 / $number;
            }
            $mass[$key]['price_per_one'] = (int) $price;
            $mass[$key]['price_per_kg']  = (int) $price * $koef;
        }
        echo "end parse... " . (microtime(true) - $start) . "<br>" . PHP_EOL;

        return $mass;

    }
}