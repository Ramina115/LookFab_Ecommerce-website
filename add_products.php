<?php
include 'config/database.php';

// List of available images (from assets/images/products/)
$available_images = [
    'top-women.jpg', 'gown.jpg', 'women-footwear.jpg', 'sneaker.jpg', 'kurthi-set.jpg', 'lehenga.jpg',
    'kids-indianset.jpg', 'kid-suit.jpg', 'kid-dress.jpg', 'black-shirt.jpg', 'women-top.jpg', 'dress.jpg',
    'formalset.jpg', 'stripe_skirt.jpg', 'jeansxx.jpg', 'black_dress.jpg', 'formal.jpg', 'top.jpg',
    'plazo.jpg', 'menshorts.jpg', 'mentshirt.jpg', 'mens-shirt.jpg', 'pink_set.jpg', 'kidg.jpg',
    'kidset.jpg', 'kid.jpg', 'kurtha.jpg', 'kids-set.jpg', "kids'.jpg", 'model2.jpg', 'womens-dress.jpg'
];

$products = [
    [
        'name' => 'Classic White Shirt',
        'description' => 'Premium cotton shirt. Colors: White, Blue, Black',
        'price' => 1299.00,
        'category' => 'men',
        'image' => 'mentshirt.jpg',
        'stock' => 50,
        'colors' => 'White,Blue,Black',
        'rating' => 4.5
    ],
    [
        'name' => 'Black Formal Shirt',
        'description' => 'Elegant black formal shirt. Colors: Black, White',
        'price' => 1499.00,
        'category' => 'men',
        'image' => 'black-shirt.jpg',
        'stock' => 35,
        'colors' => 'Black,White',
        'rating' => 4.6
    ],
    [
        'name' => 'Men\'s Casual Shirt',
        'description' => 'Comfortable casual shirt. Colors: Blue, Grey',
        'price' => 1199.00,
        'category' => 'men',
        'image' => 'mens-shirt.jpg',
        'stock' => 45,
        'colors' => 'Blue,Grey',
        'rating' => 4.3
    ],
    [
        'name' => 'Slim Fit Jeans',
        'description' => 'Stretchable denim jeans. Colors: Blue, Black',
        'price' => 1599.00,
        'category' => 'men',
        'image' => 'jeansxx.jpg',
        'stock' => 40,
        'colors' => 'Blue,Black',
        'rating' => 4.2
    ],
    [
        'name' => 'Formal Blazer',
        'description' => 'Elegant formal blazer. Colors: Navy, Grey',
        'price' => 3499.00,
        'category' => 'men',
        'image' => 'formal.jpg',
        'stock' => 20,
        'colors' => 'Navy,Grey',
        'rating' => 4.7
    ],
    [
        'name' => 'Summer Shorts',
        'description' => 'Lightweight shorts for summer. Colors: Khaki, Blue',
        'price' => 899.00,
        'category' => 'men',
        'image' => 'menshorts.jpg',
        'stock' => 60,
        'colors' => 'Khaki,Blue',
        'rating' => 4.1
    ],
    [
        'name' => 'Kurtha Set',
        'description' => 'Traditional kurtha set. Colors: White, Maroon',
        'price' => 1799.00,
        'category' => 'men',
        'image' => 'kurtha.jpg',
        'stock' => 30,
        'colors' => 'White,Maroon',
        'rating' => 4.3
    ],
    [
        'name' => 'Elegant Dress',
        'description' => 'Chic dress for all occasions. Colors: Red, Black, Blue',
        'price' => 2499.00,
        'category' => 'women',
        'image' => 'womens-dress.jpg',
        'stock' => 35,
        'colors' => 'Red,Black,Blue',
        'rating' => 4.8
    ],
    [
        'name' => 'Black Evening Dress',
        'description' => 'Stunning black evening dress. Colors: Black, Red',
        'price' => 2999.00,
        'category' => 'women',
        'image' => 'black_dress.jpg',
        'stock' => 25,
        'colors' => 'Black,Red',
        'rating' => 4.9
    ],
    [
        'name' => 'Casual Dress',
        'description' => 'Comfortable casual dress. Colors: Blue, Pink',
        'price' => 1899.00,
        'category' => 'women',
        'image' => 'dress.jpg',
        'stock' => 40,
        'colors' => 'Blue,Pink',
        'rating' => 4.4
    ],
    [
        'name' => 'Plazo Pants',
        'description' => 'Comfortable plazo pants. Colors: Beige, Pink',
        'price' => 1199.00,
        'category' => 'women',
        'image' => 'plazo.jpg',
        'stock' => 45,
        'colors' => 'Beige,Pink',
        'rating' => 4.0
    ],
    [
        'name' => 'Designer Top',
        'description' => 'Trendy designer top. Colors: Yellow, White',
        'price' => 999.00,
        'category' => 'women',
        'image' => 'top.jpg',
        'stock' => 55,
        'colors' => 'Yellow,White',
        'rating' => 4.4
    ],
    [
        'name' => 'Women\'s Top',
        'description' => 'Stylish women\'s top. Colors: Pink, White',
        'price' => 899.00,
        'category' => 'women',
        'image' => 'women-top.jpg',
        'stock' => 50,
        'colors' => 'Pink,White',
        'rating' => 4.3
    ],
    [
        'name' => 'Casual Women\'s Top',
        'description' => 'Comfortable casual top. Colors: Blue, Grey',
        'price' => 799.00,
        'category' => 'women',
        'image' => 'top-women.jpg',
        'stock' => 60,
        'colors' => 'Blue,Grey',
        'rating' => 4.2
    ],
    [
        'name' => 'Stripe Skirt',
        'description' => 'Elegant stripe skirt. Colors: Black, White',
        'price' => 1399.00,
        'category' => 'women',
        'image' => 'stripe_skirt.jpg',
        'stock' => 30,
        'colors' => 'Black,White',
        'rating' => 4.5
    ],
    [
        'name' => 'Kurthi Set',
        'description' => 'Traditional kurthi set. Colors: Green, Blue',
        'price' => 1699.00,
        'category' => 'women',
        'image' => 'kurthi-set.jpg',
        'stock' => 25,
        'colors' => 'Green,Blue',
        'rating' => 4.6
    ],
    [
        'name' => 'Lehenga Set',
        'description' => 'Beautiful lehenga set. Colors: Red, Pink',
        'price' => 3999.00,
        'category' => 'women',
        'image' => 'lehenga.jpg',
        'stock' => 15,
        'colors' => 'Red,Pink',
        'rating' => 4.8
    ],
    [
        'name' => 'Evening Gown',
        'description' => 'Stunning evening gown. Colors: Black, Red',
        'price' => 4999.00,
        'category' => 'women',
        'image' => 'gown.jpg',
        'stock' => 10,
        'colors' => 'Black,Red',
        'rating' => 4.9
    ],
    [
        'name' => 'Pink Ethnic Set',
        'description' => 'Beautiful pink ethnic set. Colors: Pink, White',
        'price' => 2199.00,
        'category' => 'women',
        'image' => 'pink_set.jpg',
        'stock' => 20,
        'colors' => 'Pink,White',
        'rating' => 4.7
    ],
    [
        'name' => 'Women\'s Footwear',
        'description' => 'Elegant women\'s footwear. Colors: Black, Brown',
        'price' => 1299.00,
        'category' => 'women',
        'image' => 'women-footwear.jpg',
        'stock' => 35,
        'colors' => 'Black,Brown',
        'rating' => 4.4
    ],
    [
        'name' => 'Kids Indian Set',
        'description' => 'Traditional kids Indian set. Colors: Red, Blue',
        'price' => 1499.00,
        'category' => 'children',
        'image' => 'kids-indianset.jpg',
        'stock' => 30,
        'colors' => 'Red,Blue',
        'rating' => 4.6
    ],
    [
        'name' => 'Kids Suit',
        'description' => 'Formal kids suit. Colors: Black, Navy',
        'price' => 1799.00,
        'category' => 'children',
        'image' => 'kid-suit.jpg',
        'stock' => 25,
        'colors' => 'Black,Navy',
        'rating' => 4.5
    ],
    [
        'name' => 'Kids Dress',
        'description' => 'Cute kids dress. Colors: Pink, Purple',
        'price' => 1299.00,
        'category' => 'children',
        'image' => 'kid-dress.jpg',
        'stock' => 35,
        'colors' => 'Pink,Purple',
        'rating' => 4.7
    ],
    [
        'name' => 'Kids Set',
        'description' => 'Playful kids set. Colors: Orange, Green',
        'price' => 899.00,
        'category' => 'children',
        'image' => 'kidset.jpg',
        'stock' => 50,
        'colors' => 'Orange,Green',
        'rating' => 4.3
    ],
    [
        'name' => 'Kids Ethnic Set',
        'description' => 'Traditional kids ethnic set. Colors: Yellow, Blue',
        'price' => 1099.00,
        'category' => 'children',
        'image' => 'kids-set.jpg',
        'stock' => 40,
        'colors' => 'Yellow,Blue',
        'rating' => 4.4
    ],
    [
        'name' => 'Girls Dress',
        'description' => 'Cute girls dress. Colors: Pink, Purple',
        'price' => 1299.00,
        'category' => 'children',
        'image' => 'kidg.jpg',
        'stock' => 35,
        'colors' => 'Pink,Purple',
        'rating' => 4.5
    ],
    [
        'name' => 'Boys Formal',
        'description' => 'Formal set for boys. Colors: White, Navy',
        'price' => 1499.00,
        'category' => 'children',
        'image' => 'formalset.jpg',
        'stock' => 30,
        'colors' => 'White,Navy',
        'rating' => 4.4
    ],
    [
        'name' => 'Boys Jeans',
        'description' => 'Durable boys jeans. Colors: Blue, Grey',
        'price' => 1099.00,
        'category' => 'children',
        'image' => 'kid.jpg',
        'stock' => 40,
        'colors' => 'Blue,Grey',
        'rating' => 4.1
    ],
    [
        'name' => 'Girls Skirt',
        'description' => 'Stylish girls skirt. Colors: Pink, White',
        'price' => 999.00,
        'category' => 'children',
        'image' => 'kids\'.jpg',
        'stock' => 30,
        'colors' => 'Pink,White',
        'rating' => 4.3
    ],
    [
        'name' => 'Model Dress',
        'description' => 'Trendy model dress. Colors: Black, Red',
        'price' => 2199.00,
        'category' => 'women',
        'image' => 'model2.jpg',
        'stock' => 20,
        'colors' => 'Black,Red',
        'rating' => 4.6
    ],
    [
        'name' => 'Men Formal Set',
        'description' => 'Complete formal set. Colors: Black, Grey',
        'price' => 2999.00,
        'category' => 'men',
        'image' => 'formalset.jpg',
        'stock' => 15,
        'colors' => 'Black,Grey',
        'rating' => 4.5
    ],
    [
        'name' => 'Casual Sneakers',
        'description' => 'Comfortable casual sneakers. Colors: White, Black',
        'price' => 899.00,
        'category' => 'men',
        'image' => 'sneaker.jpg',
        'stock' => 45,
        'colors' => 'White,Black',
        'rating' => 4.2
    ]
];

// Assign unique images to products
$image_index = 0;
$total_images = count($available_images);
foreach ($products as $i => &$product) {
    // If the image does not exist in the available images, assign a unique one
    if (!in_array($product['image'], $available_images)) {
        $product['image'] = $available_images[$image_index % $total_images];
        $image_index++;
    } else {
        // Remove the image from available_images to avoid reuse
        $img_key = array_search($product['image'], $available_images);
        if ($img_key !== false) {
            unset($available_images[$img_key]);
            $available_images = array_values($available_images); // reindex
            $total_images = count($available_images);
        }
    }
}

foreach ($products as $product) {
    $query = "INSERT INTO products (name, description, price, category, image, stock, colors, rating) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssdssisd', 
        $product['name'],
        $product['description'],
        $product['price'],
        $product['category'],
        $product['image'],
        $product['stock'],
        $product['colors'],
        $product['rating']
    );
    mysqli_stmt_execute($stmt);
}

echo "Products added successfully!";
?>