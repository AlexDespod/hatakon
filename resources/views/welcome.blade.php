

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

<script type="text/javascript">
    function SideBar_ShowHide()
    {
        var _sb = document.getElementById("SideBar");
        var _width = 'auto';
        if(_sb.style.width == _width) {
            _sb.style.width = '0px';
            return;
        }
        _sb.style.width = _width;
    };
</script>

<div id="SideBar">
    <div id="SideBarMenu">
        <form id="params" method="get" class="cl1">
            <div class="row">
                <h5>Сортування:</h5>
                <select id="sort_selector" name="sort" class="bla">
                    <option>не сортувати</option>
                    <option value="1">За зростанням</option>
                    <option value="2">За спаданням</option>
                </select>
            </div>

            <div class="row">
                <h5>Бренд:</h5>
                <select name="brand" id="brand_selector" class="bla">
                    <option value="">Всі</option>
                </select>
            </div>

            <div class="row">
                <h5>Вага:</h5>
                <select name="weight" id="weight_selector" class="bla">
                    <option value="">не обрано</option>
                </select>
            </div>

            <div class="row">
                <h5>Ціна:</h5>
                <div class="col">
                    <input type="text" class="form-control" name="price_from" placeholder="Від">
                </div>
                <div class="col">
                    <input type="text" class="form-control" name="price_to" placeholder="До">
                </div>
            </div>


            <div class="row">
                <h5>Пошук:</h5>
                <input type="text" name="s_title" class="form-control" id="inputSearch" placeholder="Назва товару">
            </div>
            <input type="submit" class="btn btn-success form-control mt-1" value="Відобразити">
        </form>
        <script>
            var products = [];
            var items = document.querySelector('#items');
            var brand_selector = document.querySelector('#brand_selector');
            var weight_selector = document.querySelector('#weight_selector');
            function Compare_desc(a, b){
                if (a.price_per_one === b.price_per_one) return 0;
                if (a.price_per_one > b.price_per_one)
                    return -1;
                else
                    return 1;
            }
            function Compare_asc(a, b){
                if (a.price_per_one === b.price_per_one) return 0;
                if (a.price_per_one < b.price_per_one)
                    return -1;
                else
                    return 1;
            }
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
            function showProducts(params){
                var form_params = new URLSearchParams(params);
                items.innerHTML = '';
                switch (form_params.get('sort')) {
                    case '1':
                        products.data.sort(Compare_asc)
                        break;
                    case '2':
                        products.data.sort(Compare_desc)
                        break;
                    default:
                        break;
                }
                products.data.forEach((product) => {
                    if (form_params.get('brand')){
                        var brand = form_params.get('brand');
                        if (!(product.brand.includes(brand))) return;
                    }

                    if (form_params.get('price_from') || form_params.get('price_to')){
                        var price_from = form_params.get('price_from') ? parseInt(form_params.get('price_from')) : 0;
                        var price_to = form_params.get('price_to') ? parseInt(form_params.get('price_to')) : 9999;
                        var price = product.price_per_one;
                        if (!(price_from <= price && price_to >= price)) return;
                    }
                    if (form_params.get('weight')){
                        var weight = form_params.get('weight');
                        if (!(product.weight_per_one === weight)) return;
                    }
                    if (form_params.get('s_title')){
                        if (!(product.title.includes(form_params.get('s_title')))) return;
                    }
                    addProduct(product);
                });
                if (!items.innerHTML)
                    items.innerHTML = '<div class="preloader"><p>Товарів не знайдено!</p></div>';
            }
            async function getProducts(){
                var preloader = document.querySelector('#items-preloader')
                if (preloader.classList.contains('done'))
                    preloader.classList.remove('done')
                items.innerHTML = '';
                var data = await fetch('api/all');
                products = await data.json();
                console.log(products)
                showProducts(window.location.href.split('?')[1]);
                products.brands.forEach((brand) => {
                    brand_selector.innerHTML += `<option value="${brand.brand}">${brand.brand}</option>`;
                })
                for (var el in products.weights)
                    weight_selector.innerHTML += `<option value="${products.weights[el]}">${products.weights[el]}</option>`;
                if (!preloader.classList.contains('done'))
                    preloader.classList.add('done')
            }
            getProducts();
            $('#params').submit(function (e) {
                var $form = $(this);
                var params = $form.serialize();
                history.pushState(null, null, '?'+params);
                showProducts(params);
                e.preventDefault();
            })
        </script>
    </div>
    <div id="SideBarText" onclick="SideBar_ShowHide();">Фільтри</div>
</div>

</body>

</html>
