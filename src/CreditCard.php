<?php
namespace RW\Helpers;

/**
 * Class CreditCard
 * @package RW\Helpers
 */
class CreditCard
{
    /**
     * This check should be eligible for credit cards
     *
     * @param $card
     * @return mixed
     */
    public static function sanityCreditCard(array $card)
    {
        $sanityFields = ['number', 'name', 'expiryMonth', 'expiryYear', 'cvv'];
        foreach ($sanityFields as $sanityField) {
            switch ($sanityField) {
                case "number":
                    if (isset($card["number"])) {
                        $card["number"] = preg_replace("/[^0-9]/", "", $card["number"]);
                    }
                    break;
                case "name":
                    if (isset($card["name"])) {
                        $card['name'] = strip_tags($card['name']);
                    }
                    break;
                case "expiryYear":
                    if (isset($card["expiryYear"])) {
                        $card["expiryYear"] = preg_replace("/[^0-9]/", "", $card["expiryYear"]);
                    }
                    break;
                case "expiryMonth":
                    if (isset($card["expiryMonth"])) {
                        $card["expiryMonth"] = preg_replace("/[^0-9]/", "", $card["expiryMonth"]);
                    }
                    break;
                case "cvv": //cvv is not required.
                    if (isset($card["cvv"])) {
                        $card["cvv"] = preg_replace("/[^0-9]/", "", $card["cvv"]);
                    }
                    break;
            }
        }

        return $card;
    }

    /**
     * maskCardNumber
     *
     * @param $cardNumber
     * @return string
     */
    public static function maskCardNumber(string $cardNumber)
    {
        $firstSix = substr($cardNumber, 0, 6);
        $lastFour = substr($cardNumber, -4);

        return $firstSix . str_repeat("*", strlen($cardNumber) - 10) . $lastFour;
    }

    /**
     * @param string $pan
     * @return mixed
     */
    public static function getCardBrand(string $pan)
    {
        //in case the pan is already masked. strip the masked part and everything after.
        $pan = substr($pan, 0, stripos($pan, "*"));

        //maximum length is not fixed now, there are growing number of CCs has more numbers in length, limiting can give false negatives atm

        //these regexps accept not whole cc numbers too
        //visa
        $visa_regex = "/^4[0-9]{0,}$/";

        // MasterCard
        $mastercard_regex = "/^(5[1-5]|222[1-9]|22[3-9]|2[3-6]|27[01]|2720)[0-9]{0,}$/";
        $maestro_regex = "/^(5[06789]|6)[0-9]{0,}$/";

        // American Express
        $amex_regex = "/^3[47][0-9]{0,}$/";

        // Diners Club
        $diners_regex = "/^3(?:0[0-59]{1}|[689])[0-9]{0,}$/";

        //Discover
        $discover_regex = "/^(6011|65|64[4-9]|62212[6-9]|6221[3-9]|622[2-8]|6229[01]|62292[0-5])[0-9]{0,}$/";

        //JCB
        $jcb_regex = "/^(?:2131|1800|35)[0-9]{0,}$/";

        //ordering matter in detection, otherwise can give false results in rare cases
        if (preg_match($jcb_regex, $pan)) {
            return "JCB";
        }

        if (preg_match($amex_regex, $pan)) {
            return "AMEX";
        }

        if (preg_match($diners_regex, $pan)) {
            return "DINERSCLUB";
        }

        if (preg_match($visa_regex, $pan)) {
            return "VISA";
        }

        if (preg_match($mastercard_regex, $pan)) {
            return "MASTERCARD";
        }

        if (preg_match($discover_regex, $pan)) {
            return "DISCOVER";
        }

        if (preg_match($maestro_regex, $pan)) {
            if ($pan[0] == '5') {//started 5 must be mastercard
                return "MASTERCARD";
            }
            return "MAESTRO"; //maestro is all 60-69 which is not something else, thats why this condition in the end
        }

        return "UNKNOWN"; //unknown for this system
    }
}