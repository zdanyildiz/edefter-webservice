<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[company-name] [subject]</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            background-color: #f3f3f3;
            max-width: 800px;
            float: none;
            margin: 0 auto;
        }
        .header {
            padding: 20px;
            text-align: center; background-color: aliceblue;
        }
        .header img {
            width: 200px; height: auto;
        }
        .content {
            padding: 20px; background-color: #fdfdfd;
        }
        .footer {
            padding: 20px;
            text-align: center; background-color: antiquewhite;
        }
        .footer img {
            width: 150px; height: auto;
        }
        a {
            color: cornflowerblue;
        }
        .content .footer{
            background-color: #f1f1f1;
        }
        h2{
            text-align: left;font-size: 1.2em;
        }
        li{text-align: left; line-height: 1.5;}

        .mycart-container{width:100%;max-width:1200px;margin:0 auto;padding:20px}
        .cart-header{text-align:center;margin-bottom:20px}
        .order{width: 100%;display: inline-block;box-sizing: border-box}


        .cart-items {
            width: 100%;
            padding: 15px;
            box-sizing: border-box;
        }

        .cart-item {
            width: 100%;
            border-bottom: 1px solid #ccc;
            margin-bottom: 30px; display: inline-block;
        }

        .cart-image-container {
            width: 12%;
            height: 170px;
            overflow: hidden;
            position: relative;
            float: left;
            display: inline-block;box-sizing: border-box;
        }

        .cart-item-image-link {
            display: block;
            width: 100%;
            height: 100%;
        }

        .cart-item-image {
            width: 140px;
            height: auto;
            object-fit: cover;
        }

        .cart-item-details {
            width: 52%;
            padding-left: 20px;
            float: left;
            display: inline-block;box-sizing: border-box;
        }

        .cart-item-title {
            font-size: 18px;
            line-height: 24px;
            font-weight: 500;
            color: #000;
            text-decoration: none;
            margin-bottom: 10px;
        }

        .cart-item-variant-text {
            font-size: 14px;
            color: #000;
            padding: 5px 0;
        }

        .cart-item-price,
        .cart-item-quantity {
            width: 12%;
            text-align: center;
            float: left;
            display: inline-block;box-sizing: border-box;
        }

        .cart-item-quantity-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }

        .cart-item-quantity input {
            width: 50px;
            text-align: center;
            line-height: 24px;
        }

        .productUnitName {
            font-size: 12px;
            color: #000;
            margin-top: 5px;
            width: 100px;
            clear: both;
        }

        .cart-totals {
            width: 12%;
            text-align: center;
            float: left;
            display: inline-block;box-sizing: border-box;
        }

        .cart-item-discount-amount,
        .cart-item-discount-description,
        .cart-item-discounted-price,
        .cart-item-total-price {
            display: block;
            margin-bottom: 10px;
        }

        .cart-item-discount-amount i {
            text-decoration: line-through;
        }

        .cart-item-discounted-price i {
            color: red;
        }

        .cart-item-discount-description {
            font-size: 12px;
        }

        .cart-summary {
            margin: 10px auto 30px;
            text-align: right;
            width: 100%;
            min-height: 200px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            box-sizing: border-box;
        }

        .cart-summary ul {
            list-style: none;
            padding: 5px;
            margin: 0;
            float:right;
        }

        .cart-summary ul li {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .cart-summary a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            margin-top: 10px;
        }

        .cart-summary a:hover {
            background-color: #333;
        }

        .cart-summary .total-model,
        .cart-summary .total-quantitiy {
            font-size: 14px;
            color: #242424;
            margin-bottom: 10px;
            padding: 5px 0;
        }

        .cart-summary .total-price {
            font-size: 14px;
            color: #000;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .cart-summary .total-discount {
            font-size: 14px;
            color: #000;
            margin-bottom: 10px;
        }

        .cart-summary .total-discounted-price {
            font-size: 14px;
            color: red;
            padding: 5px 0;
            margin-bottom: 10px;
        }

        @media screen and (max-width: 800px) {
            .cart-image-container,
            .cart-item-details,
            .cart-item-price,
            .cart-item-quantity,
            .cart-totals {
                width: 100%;
                float: none;
                display: block;
                text-align: center;
            }

            .cart-item {
                display: block;
            }

            .cart-item-quantity-wrapper {
                justify-content: center;
            }

            .cart-summary ul {
                float: none;
            }
            .cart-item-quantity-wrapper,.quantity{text-align: center;}
            .productUnitName{width: auto}
            .quantity-input.qty{border:none;width:40px}
            .cart-item-variant-text,.cart-item-price,.cart-item-quantity-wrapper{line-height: 1.5;}
        }
        .footer p{font-size: 12px;}
        .header,.content,.footer{max-width: 800px;float: none;margin: 0 auto;}

    </style>
</head>
<body>
<div class="header">
    <img src="[company-logo]" alt="[company-name]">
    <h1>[company-name]</h1>
</div>
<div class="content">
    <p>Merhaba [member-name-surname],</p>
    <p>[company-name] [_order_siparis_durum_yazi]</p>
    <p><b>[_uyelik_mesajsiparisno_yazi] [orderUniqID]</b></p>
    <a href="[my-order-link]">[_uyelik_siparislerim_yazi]</a>
</div>
<div class="footer">
    <img src="[company-logo]" alt="[company-name]">
    <p>[company-name] | [company-address] | [company-phone] | [company-email]</p>
</div>
</body>
</html>