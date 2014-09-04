<?php
    require_once("iPay.class.php");
    
    $iPay = new iPay();
    $iPay->privateKeyPath = getcwd() . "/private_key.pem";
    
    $cart = array
    (
        array
        ( 
            "Article" => "Sword",
            "Quantity" => 3,
            "Price" => 2.50,
            "Currency" => 975
        ),
        array
        ( 
            "Article" => "Shield",
            "Quantity" => 2,
            "Price" => 1.75,
            "Currency" => 975
        )
    );
    
    $post = $iPay->Purchase(rand(99999, 99999999), 975, $cart, "plamen@siriushome.com");
    
    $form = "<form id=\"form\" action=\"{$iPay->GetURL()}\" method=\"POST\">";

    foreach($post as $k => $v)
    {
        $form .= "<div><div style=\"width:128px;float:left;\">{$k}</div> <div style=\"float:left;\"><input type=\"text\" value=\"{$v}\" name=\"{$k}\"/></div></div>";
        $form .= "<div style=\"clear:both;\"></div>";
    }

    $form .= "<input type=\"button\" value=\"Submit\" onclick=\"document.getElementById('form').submit();\"/><br>";
    $form .= "</form>";
            
    echo $form;
?>