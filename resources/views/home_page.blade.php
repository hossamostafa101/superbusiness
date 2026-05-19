<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProGrocery - Fresh Products Delivered</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .search-bar {
            flex: 1;
            max-width: 500px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
        }

        .search-bar i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .cart-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
        }

        .cart-count {
            background: #FF5722;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Banner Styles */
        .banner-section {
            margin: 2rem 0;
        }

        .banner-container {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            height: 250px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .banner-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .banner-slide.active {
            opacity: 1;
        }

        .banner-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .banner-indicators {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ddd;
            cursor: pointer;
            transition: background 0.3s;
        }

        .indicator.active {
            background: #4CAF50;
        }

        /* Section Titles */
        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2E7D32;
            margin: 2rem 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Categories */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .category-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .category-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 0.5rem;
        }

        .category-name {
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            color: #333;
        }

        /* Flash Deals */
        .flash-deals {
            background: linear-gradient(135deg, #F36836, #B63C08);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin: 2rem 0;
        }

        .flash-deals-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .flash-deals-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .flash-timer {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Product Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .products-horizontal {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1rem;
        }

        .products-horizontal .product-card {
            min-width: 250px;
            flex-shrink: 0;
        }

        /* Product Card */
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .product-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #FF5722;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .flash-badge {
            background: #FFC107;
            color: #333;
        }

        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            color: #666;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: color 0.3s;
        }

        .favorite-btn:hover {
            color: #FF5722;
        }

        .product-info {
            padding: 1rem;
        }

        .product-name {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .product-unit {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .current-price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #4CAF50;
        }

        .original-price {
            font-size: 0.9rem;
            color: #999;
            text-decoration: line-through;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.9rem;
            color: #666;
        }

        .rating-star {
            color: #FFC107;
        }

        .add-to-cart {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .add-to-cart:hover {
            background: #45a049;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .view-all-btn {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border: 1px solid #4CAF50;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .view-all-btn:hover {
            background: #4CAF50;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .search-bar {
                max-width: 100%;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }

            .categories-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .flash-deals-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-shopping-cart"></i> ProGrocery
            </div>
            
            <div class="location">
                <i class="fas fa-map-marker-alt"></i>
                <span>El-Matrouh, Egypt</span>
            </div>
            
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for products...">
            </div>
            
            <div class="cart-info">
                <i class="fas fa-shopping-cart"></i>
                <span>Cart</span>
                <div class="cart-count">3</div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Banner Section -->
        <section class="banner-section">
            <div class="banner-container" id="bannerContainer">
                <div class="banner-slide active">
                    <img src="https://mcprod.spinneys-egypt.com/media/offers/175420481198.jpg?format=webp" alt="Fresh Fruits">
                </div>
                <div class="banner-slide">
                    <img src="https://mcprod.spinneys-egypt.com/media/offers/1750333011195.jpg?format=webp" alt="Vegetables">
                </div>
                <div class="banner-slide">
                    <img src="https://mcprod.spinneys-egypt.com/media/offers/1754473880747.png?format=webp" alt="Dairy Products">
                </div>
            </div>
            <div class="banner-indicators">
                <div class="indicator active" onclick="showBanner(0)"></div>
                <div class="indicator" onclick="showBanner(1)"></div>
                <div class="indicator" onclick="showBanner(2)"></div>
            </div>
        </section>

        <!-- Categories Section -->
        <section>
            <h2 class="section-title">
                <i class="fas fa-th-large"></i> Categories
            </h2>
            <div class="categories-grid">
                <div class="category-item">
                    <img src="https://mcprod.spinneys-egypt.com/media/sizechart/category/F_V.png?width=200&format=webp" alt="Fruits">
                    <span class="category-name">Fruits</span>
                </div>
                <div class="category-item">
                    <img src="https://mcprod.spinneys-egypt.com/media/sizechart/category/M_P.png?width=200&format=webp" alt="Vegetables">
                    <span class="category-name">Vegetables</span>
                </div>
                <div class="category-item">
                    <img src="https://mcprod.spinneys-egypt.com/media/sizechart/category/pngtree-grocery-basket-and-a-lis-removebg-preview.png?width=200&format=webp" alt="Dairy">
                    <span class="category-name">Dairy</span>
                </div>
                <div class="category-item">
                    <img src="https://mcprod.spinneys-egypt.com/media/sizechart/category/platter-meats-cheese-vegetables-including-meats-tomatoes.jpeg?width=200&format=webp" alt="Meat">
                    <span class="category-name">Meat</span>
                </div>
                <div class="category-item">
                    <img src="https://mcprod.spinneys-egypt.com/media/sizechart/category/images__2_-removebg-preview.png?width=200&format=webp" alt="Bakery">
                    <span class="category-name">Bakery</span>
                </div>
                <div class="category-item">
                    <img src="https://mcprod.spinneys-egypt.com/media/sizechart/category/a6280874913e2456db00049923f668c2-removebg-preview.png?width=200&format=webp" alt="Beverages">
                    <span class="category-name">Beverages</span>
                </div>
                <div class="category-item">
                    <img src="https://mcprod.spinneys-egypt.com/media/sizechart/category/breadz.png" alt="Snacks">
                    <span class="category-name">Snacks</span>
                </div>
                <div class="category-item">
                    <img src="https://mcprod.spinneys-egypt.com/media/sizechart/category/SeaFood.png" alt="Frozen">
                    <span class="category-name">Frozen</span>
                </div>
            </div>
        </section>

        <!-- Flash Deals Section -->
        <section class="flash-deals">
            <div class="flash-deals-header">
                <div class="flash-deals-title">
                    <i class="fas fa-bolt"></i>
                    Flash Deals
                </div>
                <div class="flash-timer">Ends in 2h 30m</div>
            </div>
            <div class="products-horizontal">
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/1/111989_iqujcro2hazvqkyv.png?width=250&format=webp" alt="Fresh Bananas">
                        <div class="product-badge flash-badge">FLASH</div>
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fresh Bananas</div>
                        <div class="product-unit">kg</div>
                        <div class="product-price">
                            <span class="current-price">$2.99</span>
                            <span class="original-price">$3.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.5</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/1/112212.jpg?width=250&format=webp" alt="Organic Apples">
                        <div class="product-badge flash-badge">FLASH</div>
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Organic Apples</div>
                        <div class="product-unit">kg</div>
                        <div class="product-price">
                            <span class="current-price">$4.99</span>
                            <span class="original-price">$6.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.8</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/1/112081.jpg?width=250&format=webp" alt="Fresh Milk">
                        <div class="product-badge flash-badge">FLASH</div>
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fresh Milk</div>
                        <div class="product-unit">1L</div>
                        <div class="product-price">
                            <span class="current-price">$3.49</span>
                            <span class="original-price">$4.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.7</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Daily Needs Section -->
        <section>
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-calendar-day"></i> Daily Needs
                </h2>
                <a href="#" class="view-all-btn">View All</a>
            </div>
            <div class="products-horizontal">
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/1/112009_0i1h4bmtfsnfcq59.jpg?width=250&format=webp" alt="White Bread">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">White Bread</div>
                        <div class="product-unit">loaf</div>
                        <div class="product-price">
                            <span class="current-price">$1.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.2</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/3/6/360162_hoi67uadbqslwbtm.jpg?width=250&format=webp" alt="Fresh Eggs">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fresh Eggs</div>
                        <div class="product-unit">dozen</div>
                        <div class="product-price">
                            <span class="current-price">$3.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.6</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/5/154265_2eaatnyupbkd3sxm.png?width=250&format=webp" alt="Yogurt">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Yogurt</div>
                        <div class="product-unit">cup</div>
                        <div class="product-price">
                            <span class="current-price">$1.49</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.3</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/i/m/images_9_.jpeg?width=250&format=webp" alt="Chicken Breast">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Chicken Breast</div>
                        <div class="product-unit">kg</div>
                        <div class="product-price">
                            <span class="current-price">$8.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.7</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section>
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-star"></i> Featured Products
                </h2>
                <a href="#" class="view-all-btn">View All</a>
            </div>
            <div class="products-horizontal">
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/r/e/red_chilli.png?width=250&format=webp" alt="Organic Spinach">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Organic Spinach</div>
                        <div class="product-unit">bunch</div>
                        <div class="product-price">
                            <span class="current-price">$2.49</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.4</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/3/4/346218_knm9nqpwhksmdlhn.png?width=250&format=webp" alt="Premium Cheese">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Premium Cheese</div>
                        <div class="product-unit">200g</div>
                        <div class="product-price">
                            <span class="current-price">$5.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.9</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/3/2/328960_1.jpg?width=250&format=webp" alt="Fresh Salmon">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fresh Salmon</div>
                        <div class="product-unit">kg</div>
                        <div class="product-price">
                            <span class="current-price">$12.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.8</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- All Products Section -->
        <section>
            <h2 class="section-title">
                <i class="fas fa-th"></i> All Products
            </h2>
            <div class="products-grid">
                <!-- All products grid -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/1/111989_iqujcro2hazvqkyv.png?width=250&format=webp" alt="Fresh Bananas">
                        <div class="product-badge">SALE</div>
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fresh Bananas</div>
                        <div class="product-unit">kg</div>
                        <div class="product-price">
                            <span class="current-price">$2.99</span>
                            <span class="original-price">$3.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.5</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/1/112212.jpg?width=250&format=webp" alt="Organic Apples">
                        <div class="product-badge">SALE</div>
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Organic Apples</div>
                        <div class="product-unit">kg</div>
                        <div class="product-price">
                            <span class="current-price">$4.99</span>
                            <span class="original-price">$6.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.8</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/1/112081.jpg?width=250&format=webp" alt="Fresh Milk">
                        <div class="product-badge">SALE</div>
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fresh Milk</div>
                        <div class="product-unit">1L</div>
                        <div class="product-price">
                            <span class="current-price">$3.49</span>
                            <span class="original-price">$4.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.7</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/1/112009_0i1h4bmtfsnfcq59.jpg?width=250&format=webp" alt="White Bread">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">White Bread</div>
                        <div class="product-unit">loaf</div>
                        <div class="product-price">
                            <span class="current-price">$1.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.2</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/3/6/360162_hoi67uadbqslwbtm.jpg?width=250&format=webp" alt="Fresh Eggs">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fresh Eggs</div>
                        <div class="product-unit">dozen</div>
                        <div class="product-price">
                            <span class="current-price">$3.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.6</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/1/5/154265_2eaatnyupbkd3sxm.png?width=250&format=webp" alt="Yogurt">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Yogurt</div>
                        <div class="product-unit">cup</div>
                        <div class="product-price">
                            <span class="current-price">$1.49</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.3</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/i/m/images_9_.jpeg?width=250&format=webp" alt="Chicken Breast">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Chicken Breast</div>
                        <div class="product-unit">kg</div>
                        <div class="product-price">
                            <span class="current-price">$8.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.7</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/r/e/red_chilli.png?width=250&format=webp" alt="Organic Spinach">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Organic Spinach</div>
                        <div class="product-unit">bunch</div>
                        <div class="product-price">
                            <span class="current-price">$2.49</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.4</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/3/4/346218_knm9nqpwhksmdlhn.png?width=250&format=webp" alt="Premium Cheese">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Premium Cheese</div>
                        <div class="product-unit">200g</div>
                        <div class="product-price">
                            <span class="current-price">$5.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.9</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="https://mcprod.spinneys-egypt.com/media/catalog/product/cache/36b410f085b47d6b5accd0d7fc6177ea/3/2/328960_1.jpg?width=250&format=webp" alt="Fresh Salmon">
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Fresh Salmon</div>
                        <div class="product-unit">kg</div>
                        <div class="product-price">
                            <span class="current-price">$12.99</span>
                        </div>
                        <div class="product-footer">
                            <div class="product-rating">
                                <i class="fas fa-star rating-star"></i>
                                <span>4.8</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Banner auto-slide functionality
        let currentBanner = 0;
        const banners = document.querySelectorAll('.banner-slide');
        const indicators = document.querySelectorAll('.indicator');
        const totalBanners = banners.length;

        function showBanner(index) {
            // Remove active class from all banners and indicators
            banners.forEach(banner => banner.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));
            
            // Add active class to current banner and indicator
            banners[index].classList.add('active');
            indicators[index].classList.add('active');
            
            currentBanner = index;
        }

        function nextBanner() {
            currentBanner = (currentBanner + 1) % totalBanners;
            showBanner(currentBanner);
        }

        // Auto-slide banners every 3 seconds
        setInterval(nextBanner, 3000);

        // Add to cart functionality
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                // Add animation
                this.style.transform = 'scale(0.95)';
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.style.background = '#45a049';
                
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                    this.innerHTML = '<i class="fas fa-plus"></i>';
                    this.style.background = '#4CAF50';
                }, 1000);
                
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                let count = parseInt(cartCount.textContent);
                cartCount.textContent = count + 1;
                cartCount.style.animation = 'bounce 0.5s';
                setTimeout(() => {
                    cartCount.style.animation = '';
                }, 500);
            });
        });

        // Favorite button functionality
        document.querySelectorAll('.favorite-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const icon = this.querySelector('i');
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.style.color = '#FF5722';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.style.color = '#666';
                }
            });
        });

        // Product card click functionality
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function() {
                // Simulate product details navigation
                console.log('Navigate to product details');
            });
        });

        // Category click functionality
        document.querySelectorAll('.category-item').forEach(category => {
            category.addEventListener('click', function() {
                console.log('Navigate to category:', this.querySelector('.category-name').textContent);
            });
        });

        // Search functionality
        document.querySelector('.search-bar input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('Search for:', this.value);
            }
        });

        // Add bounce animation for cart
        const style = document.createElement('style');
        style.textContent = `
            @keyframes bounce {
                0%, 20%, 60%, 100% { transform: translateY(0); }
                40% { transform: translateY(-10px); }
                80% { transform: translateY(-5px); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>