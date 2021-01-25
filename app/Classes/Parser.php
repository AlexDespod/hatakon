<?php
namespace App\Classes;

use Clue\React\Buzz\Browser;
use DiDom\Document;
use React\EventLoop\Factory;

class Parser
{
    private $data;

    private $urls = array(
        "https://auchan.zakaz.ua/ru/categories/buckwheat-auchan/",
        'https://novus.zakaz.ua/ru/categories/buckwheat/',
        'https://metro.zakaz.ua/ru/categories/buckwheat-metro/',
    );

    public function __construct()
    {

    }
    // public function sort_grechka(array $inputs)
    // {
    //     $data = [];
    //     if ($inputs['store'] != 'all') {
    //         $data = $this->get_by_store($inputs['store']);
    //     } else {
    //         $data = $this->get_grechka($this->urls);
    //     }
    // }

    public function get_top_10_by_price()
    {
        $this->data = $this->get_grechka($this->urls);

        $mass = $this->data['data'];
        $size = count($mass);

        for ($i = 0; $i < $size - 1; $i++) {
            for ($j = 0; $j < $size - $i - 1; $j++) {
                if ($mass[$j]['price_per_kg'] > $mass[$j + 1]['price_per_kg']) {
                    $temp         = $mass[$j];
                    $mass[$j]     = $mass[$j + 1];
                    $mass[$j + 1] = $temp;
                }
            }
        }
        $top = array_slice($mass, 0, 10);
        return $top;
    }

    public function get_by_store(string $store)
    {
        $data = [];
        foreach ($this->urls as $url) {
            if (preg_match('/' . $store . '/', $url)) {
                $data = $this->get_grechka([$url]);
            }
        }
        return $data;
    }

    private function get_grechka($urls)
    {
        $products = ['data' => [], 'brands' => [], 'weights' => []];
        $loop     = Factory::create();
        $client   = new Browser($loop);
        foreach ($urls as $key => $url) {
            $client
                ->get($url)
                ->then(function (\Psr\Http\Message\ResponseInterface $response) use (&$products, $key, $url) {
                    $domain           = "https://" . parse_url($url, PHP_URL_HOST);
                    $temp             = $this->parser($response->getBody()->__toString(), $domain);
                    $products['data'] = array_merge(
                        $products['data'],
                        $temp['data']
                    );
                    $products['brands']  = $this->merge_brand_list($products['brands'], $temp['brands']);
                    $products['weights'] = $this->merge_weight_list($products['weights'], $temp['weights']);

                });

        }
        $loop->run();
        return $products;
    }

    public function parser($text, $domain)
    {
        $mass     = [];
        $brands   = [];
        $weights  = [];
        $document = new Document($text);

        $elems = $document->find('.product-tile');

        foreach ($elems as $key => $el) {

            $title = $el->attr('title');
            $src   = $el->getAttribute('href');
            $price = trim($el->first('.Price__value_caption.jsx-3642073353')->text());

            $weight = trim($el->first('.product-tile__weight.jsx-725860710')->text());
            $img    = trim($el->first('.product-tile__image-i.jsx-725860710')->attr('src'));

            $mass['data'][$key]['title']          = $title;
            $mass['data'][$key]['brand']          = $title;
            $mass['data'][$key]['weight_per_one'] = $weight;
            $weights[]                            = $weight;
            $mass['data'][$key]['img']            = $img;
            $mass['data'][$key]['store']          = preg_split("/\./", parse_url($domain, PHP_URL_HOST))[0];

            $mass['data'][$key]['src'] = $domain . $src;

            $koef = 1;
            if (preg_match('/ г/', $mass['data'][$key]['weight_per_one'])) {

                $number                            = (int) preg_replace('/[^0-9]/', '', $mass['data'][$key]['weight_per_one']);
                $mass['data'][$key]['weight_type'] = 'g';
                $koef                              = 1000 / $number;

            } else {
                $number                            = (int) preg_replace('/[^0-9]/', '', $mass['data'][$key]['weight_per_one']);
                $koef                              = 1 / $number;
                $mass['data'][$key]['weight_type'] = 'kg';
                // ;

            }

            $mass['data'][$key]['price_per_one'] = (int) $price;
            $mass['data'][$key]['price_per_kg']  = (int) $price * $koef;

        }

        $brandElems = $document
            ->first('.catalog-filters__widget.jsx-54661519')
            ->find('.SimpleCheckboxOptionAmounted.jsx-268113897');

        foreach ($brandElems as $key => $el) {
            $name                  = trim($el->first('.SimpleCheckboxOptionAmounted__text.jsx-268113897')->text());
            $count                 = (int) trim($el->first('.SimpleCheckboxOptionAmounted__amount.jsx-268113897')->text());
            $brands[$key]['brand'] = $name;
            $brands[$key]['count'] = $count;
        }
        $mass['brands'] = $brands;

        $mass['weights'] = array_unique($weights);
        // print_r($mass['weights']);
        // echo " aa<br>";

        return $mass;
    }
    public function merge_brand_list($mass1, $mass2)
    {
        $ans   = 1;
        $index = 0;
        if (count($mass1) != 0) {
            foreach ($mass2 as $key2 => $el2) {
                $ans = true;
                foreach ($mass1 as $key1 => $el1) {
                    if ($el1['brand'] == $el2['brand']) {
                        $index = $key1;
                        $ans   = $ans * 0;
                    }
                }
                if (!$ans) {
                    $mass1[$index] = [
                        'brand' => $el2['brand'],
                        'count' => $el2['count'] + $mass1[$index]['count'],
                    ];
                } else {
                    $mass1[] = $el2;
                }
            }
            return $mass1;

        } else {
            return $mass2;
        }
    }
    public function merge_weight_list($mass1, $mass2)
    {
        if (count($mass1) != 0) {
            $arr = array_unique(array_merge($mass1, $mass2));
            return $arr;
        } else {
            return $mass2;
        }
    }
}
