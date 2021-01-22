<?php
namespace App\Classes;

use Illuminate\Support\Facades\Http;
use PhpQuery\PhpQuery;

class Novus
{
    public static function get_grechka()
    {
        $res = Http::get('https://novus.zakaz.ua/ru/categories/buckwheat/');


        $pq = new PhpQuery();

        $pq->load_str($res->body());

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
            $mass[$key]['src']            = 'https://novus.zakaz.ua/' . $src;
            $koef                         = 1;
            if (preg_match('/ г/', $mass[$key]['weight_per_one'])) {
                $entries = preg_split('/ /', $mass[$key]['weight_per_one']);
                $number  = (int) $entries[0];
                $koef    = 1000 / $number;
            }
            $mass[$key]['price_per_one'] = (int) $price;
            $mass[$key]['price_per_kg']  = (int) $price * $koef;
        }
        return $mass;
    }
}
