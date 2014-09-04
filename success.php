<?php
    require_once("iPay.class.php");

    if($_POST['Signature'])
    {
        $iPay = new iPay();
        $iPay->publicKeyPath = getcwd() . "/public_key.pem";
        $iPay->urlOK = "http://siriushome.com/success.php";
        $iPay->urlCancel = "http://siriushome.com/cancel.php";
        $iPay->urlNotify = "http://siriushome.com/notify.php";
        
        $result = $iPay->Verify($_POST);

        if($result)
        {
            if($_POST['IPGmethod'] == "IPGPurchaseOK" || $_POST['IPGmethod'] == "IPGPurchaseIPAY")
            {
                // TODO: Handle OK methods
                echo "Payment success";
            }
            elseif($_POST['IPGmethod'] == "IPGPurchaseRollback" || $_POST['IPGmethod'] == "IPGPurchaseCancel" || $_POST['IPGmethod'] == "IPGRefund" || $_POST['IPGmethod'] == "IPGReversal")
            {
                // TODO: Handle cancel methods
                echo "Payment cancel";
            }
        }
        else
        {
            echo "ERROR";
        }
    }
?>