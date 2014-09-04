<?php
/*
Copyright (C) 2014 Sirius Software Ltd.
Our website: http://siriushome.com/
Repository: http://github.com/dele/ipay

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in 
the Software without restriction, including without limitation the rights to 
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies 
of the Software, and to permit persons to whom the Software is furnished to do 
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all 
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.
*/
    class iPay
    {
        public $debug = true;    

        public $privateKeyPath;
        public $publicKeyPath;

        public $language = "EN";
        public $originator = 33;
        public $mid = "000000000000113";
        public $midName = "Test Web Shop";

        public $urlDebug = "https://devs.icards.eu/ipgtest/";
        public $urlRelease = "https://ipg.icard.com"; 

        public $urlOK;
        public $urlCancel;
        public $urlNotify;
        
        private $privateKey;
        private $publicKey;
        
        public function Purchase($orderID, $currency, $cart, $email)
        {
            if(!is_file($this->privateKeyPath))
            {
                throw new Exception("Missing private key file.");
            }

            $post = array();
            $post["IPGmethod"] = "IPGPurchase";
            $post["IPGVersion"] = "3.1";
            $post["KeyIndex"] = 1;
            $post["Language"] = $this->language;
            $post["Originator"] = $this->originator;
            
            $post["MID"] = $this->mid;
            $post["MIDName"] = $this->midName;

            $post["Amount"] = 0;
            $post["Currency"] = $currency;
            $post["OrderID"] = $orderID;
            $post["CustomerIP"] = $_SERVER["REMOTE_ADDR"];
            $post["BannerIndex"] = 1;

            $post["URL_OK"] = $this->urlOK;
            $post["URL_Cancel"] = $this->urlCancel;
            $post["URL_Notify"] = $this->urlNotify;

            $post["CartItems"] = count($cart);

            for($i = 0; $i < count($cart); $i++)
            {    
                $ix = $i + 1;

                $post["Article_" . $ix] = $cart[$i]["Article"];
                $post["Quantity_" . $ix] = $cart[$i]["Quantity"];
                $post["Price_" . $ix] = $cart[$i]["Price"];
                $post["Amount_" . $ix] = $cart[$i]["Quantity"] * $cart[$i]["Price"];
                $post["Currency_" . $ix] = $cart[$i]["Currency"];

                $post["Amount"] += $post["Amount_" . $ix];
            }

            $post["Email"] = $email;
            
            $hash = sha1(urlencode(stripslashes(implode("", $post))));
            $privateKey = openssl_pkey_get_private("file://" . $this->privateKeyPath);

            openssl_sign($hash, $signature, $privateKey);
            $signature = base64_encode($signature);
            openssl_free_key($privateKey);

            $post["Signature"] = $signature;

            return $post;
        }

        public function Verify(&$post)
        {
            if(!is_file($this->publicKeyPath))
            {
                throw new Exception("Missing public key file.");
            }

            $signature = $post["Signature"];
            unset($post["Signature"]);

            $hash = sha1(urlencode(stripslashes(implode("", $post))));

            $publicKey = openssl_pkey_get_public("file://" . $this->publicKeyPath);
            $signature = base64_decode($signature);
            $result = openssl_verify($hash, $signature, $publicKey);
            openssl_free_key($publicKey);

            return $result;
        }

        public function GetURL()
        {
            if($this->debug)
            {
                return $this->urlDebug;
            }
            else
            {
                return $this->urlRelease;
            }
        }
    }
?>