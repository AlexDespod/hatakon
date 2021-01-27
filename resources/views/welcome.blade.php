
<!DOCTYPE html>
<html lang="ru">

<head>
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
                <a href="#">MAIN</a>
            </li>
            <li>
                <a href="#">TOP 10</a>
            </li>
            <li>
                <a href="#">GRAPHIC</a>
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
            function filter(params){
                var params_arr = new URLSearchParams(params);
                items.innerHTML = '';
                switch (params_arr.get('sort')) {
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
                    if (params_arr.get('brand')){
                        var brand = params_arr.get('brand');
                        if (!(product.brand === brand)) return;
                    }
                    if (params_arr.get('price_from') || params_arr.get('price_to')){
                        var price_from = params_arr.get('price_from') ? parseInt(params_arr.get('price_from')) : 0;
                        var price_to = params_arr.get('price_to') ? parseInt(params_arr.get('price_to')) : 0;
                        var price = product.price_per_one;
                        if (!(price_from <= price && price_to >= price)) return;
                    }
                    if (params_arr.get('weight')){
                        var weight = params_arr.get('weight');
                        if (!(product.weight_per_one === weight)) return;
                    }
                    // if (params_arr.get('weight_from') || params_arr.get('weight_to')){
                    //     var weight_from = params_arr.get('weight_from') ?? 0;
                    //     var weight_to = params_arr.get('weight_to') ?? 9999;
                    //     if (!(product.price_per_one >= weight_from && product.price_per_one <= weight_to)) return;
                    // }
                    if (params_arr.get('s_title')){
                        console.log(product.title);
                        if (!(product.title === params_arr.get('s_title'))) return;
                    }
                    addProduct(product);
                });
            }
            async function getProducts(){
                var preloader = document.querySelector('#items-preloader')
                if (preloader.classList.contains('done'))
                    preloader.classList.remove('done')
                items.innerHTML = '';
                var data = await fetch('/api/all');
                products = await data.json();
                console.log(products)
                products.data.forEach((product) => {
                    addProduct(product);
                })
                products.brands.forEach((brand) => {
                    brand_selector.innerHTML += `<option value="${brand.brand}">${brand.brand}</option>`;
                })

                for(weight in products.weights){
                    weight_selector.innerHTML += `<option value="${products.weights[weight]}">${products.weights[weight]}</option>`;
                }
                if (!preloader.classList.contains('done'))
                    preloader.classList.add('done')
            }
            getProducts();
            $('#params').submit(function (e) {
                var $form = $(this);
                filter($form.serialize());
                e.preventDefault();
            })
        </script>
    </div>
    <div id="SideBarText" onclick="SideBar_ShowHide();">Фільтри</div>
</div>

</body>

</html>
