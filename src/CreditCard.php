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
}