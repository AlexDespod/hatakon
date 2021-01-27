<!DOCTYPE html>
<html lang="ru">

<head>
    <title>Hakaton 2021</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

    <link rel="stylesheet" href="{{asset('css/flex.css')}}">
    <link rel="stylesheet" href="{{asset('css/preloader.css')}}">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
<header>
    <h1>L.A.MASTERS</h1>
    <nav>
        <ul class="nav_links">
            <li>
                <a href="/index">MAIN</a>
            </li>
            <li>
                <a href="/top">TOP 10</a>
            </li>
            <li>
                <a href="/graph">GRAPHIC</a>
            </li>
        </ul>
    </nav>
</header>
<div class="wrapper">
    <div id="items-preloader" class="preloader">
        <div class="loader"></div>
        <p>Обробка результатів...</p>
    </div>
    <div id="items" class="cards_wrap">

    </div>
</div>




        <script>
            // let products = [];
            let items = document.querySelector('#items');
            let preloader = document.querySelector('#items-preloader')
            // var brand_selector = document.querySelector('#brand_selector');
            // var weight_selector = document.querySelector('#weight_selector');

            function addProduct(product){
                items.innerHTML += `
                            <div class="card_item">
                                <div class="card_inner">
                                    <div class="card_top">
                                        <img src="${product.img}" alt="car" />
                                    </div>
                                    <div class="card_bottom">
                                        <div class="card_category">
                                            ${product.brand}
                                        </div>
                                        <div class="card_category">
                                            <p>${product.price_per_one} грн <i>${product.weight_per_one}</i></p>
                                        </div>
                                        <div class="card_info">
                                            <a href="${product.src}" class="title">${product.title}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
            }
            function showProducts(products){

                products.forEach((product) => {
                    addProduct(product);
                });
                if (!items.innerHTML)
                    items.innerHTML = '<div class="preloader"><p>Товарів не знайдено!</p></div>';
            }
            async function getProducts(){

                if (preloader.classList.contains('done'))
                    preloader.classList.remove('done')
                items.innerHTML = '';
                const data = await fetch('api/top');
                const products = await data.json();
                console.log(products)
                showProducts(products);
                // products.brands.forEach((brand) => {
                //     brand_selector.innerHTML += `<option value="${brand.brand}">${brand.brand}</option>`;
                // })
                // for (var el in products.weights)
                //     weight_selector.innerHTML += `<option value="${products.weights[el]}">${products.weights[el]}</option>`;
                if (!preloader.classList.contains('done'))
                    preloader.classList.add('done')
            }
            getProducts();
            // $('#params').submit(function (e) {
            //     var $form = $(this);
            //     var params = $form.serialize();
            //     history.pushState(null, null, '?'+params);
            //     showProducts(params);
            //     e.preventDefault();
            // })
        </script>


</body>

</html>
