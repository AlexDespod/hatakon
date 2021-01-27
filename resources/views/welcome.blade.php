<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

    <link rel="stylesheet" href="{{asset('assets/css/flex.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/preloader.css')}}">
    <script src="{{asset('js/jquery.min.js')}}"></script>
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
{{--        <div class="row">--}}
{{--            <div class="col text-white">--}}
{{--                <form method="get">--}}
{{--                    <h5>sdf</h5>--}}
{{--                    <input type="text" class="form-control">--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
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

{{--            <div class="row">--}}
{{--                <h5>Вага:</h5>--}}
{{--                <div class="col">--}}
{{--                    <input type="text" class="form-control" name="weight_from" placeholder="Від">--}}
{{--                </div>--}}
{{--                <div class="col">--}}
{{--                    <input type="text" class="form-control" name="weight_to" placeholder="До">--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="row">
                <h5>Пошук:</h5>
                <input type="text" name="s_title" class="form-control" id="inputSearch" placeholder="Назва товару">
            </div>
            <input type="submit" class="btn btn-success form-control mt-1" value="Відобразити">
        </form>
        <script>
            let products = [];
            const items = document.querySelector('#items');
            const brand_selector = document.querySelector('#brand_selector');
            function Compare_desc(a, b){
                // if (a.price_per_one === b.price_per_one) return 0;
                // if (a.price_per_one > b.price_per_one)
                //     return -1;
                // else
                //     return 1;
                return a.price_per_one - b.price_per_one;
            }
            function Compare_asc(a, b){
                return d.price_per_one - a.price_per_one;
                // if (a.price_per_one === b.price_per_one) return 0;
                // if (a.price_per_one < b.price_per_one)
                //     return -1;
                // else
                //     return 1;
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
                let params_arr = new URLSearchParams(params);
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
                // console.log(params_arr)
                products.data.forEach((product) => {
                    if (params_arr.get('brand')){
                        let brand = params_arr.get('brand');
                        if (!(product.brand === brand)) return;
                    }
                    if (params_arr.get('price_from') || params_arr.get('price_to')){
                        let price_from = params_arr.get('price_from');
                        let price_to = params_arr.get('price_to');
                        if (!(price_from >= product.price_per_one && price_to <= product.price_per_one)) return;
                    }
                    if (params_arr.get('weight')){
                        let weight = params_arr.get('weight');
                        if (!(product.weight_per_one === weight)) return;
                    }
                    // if (params_arr.get('weight_from') || params_arr.get('weight_to')){
                    //     let weight_from = params_arr.get('weight_from') ?? 0;
                    //     let weight_to = params_arr.get('weight_to') ?? 9999;
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
                let preloader = document.querySelector('#items-preloader')
                if (preloader.classList.contains('done'))
                    preloader.classList.remove('done')
                items.innerHTML = '';
                let data = await fetch('/api/all');
                products = await data.json();
                products.data.forEach((product) => {
                    addProduct(product);
                })
                products.brands.forEach((brand) => {
                    brand_selector.innerHTML += `<option value="${brand.brand}">${brand.brand}</option>`;
                })
                if (!preloader.classList.contains('done'))
                    preloader.classList.add('done')
            }
            getProducts();
            $('#params').submit(function (e) {
                let $form = $(this);
                filter($form.serialize());
                e.preventDefault();
            })
        </script>
    </div>
    <div id="SideBarText" onclick="SideBar_ShowHide();">Фільтри</div>
</div>

</body>

</html>
