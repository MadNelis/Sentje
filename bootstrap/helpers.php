<?php

use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

function decimalSeparatorConverter($decimal)
{
    if (App::isLocale('nl')) {
        $decimal = str_replace('.', '_', $decimal);
        $decimal = str_replace(',', '.', $decimal);
        $decimal = str_replace('_', ',', $decimal);
    }

    return $decimal;
}

function dateConverter($date)
{
    $date = new DateTime($date);
    $date = $date->format('s:i:H d-m-Y');

    return $date;
}

function convertCurrency($amount, $currency)
{
    $exchangeRate = new ExchangeRate();
    if ($currency == 'GBP') {
        $amount *= 100;
        $amount = $exchangeRate->convert($amount, 'GBP', 'EUR', now());
        $amount /= 100;
    }
    if ($currency == 'USD') {
        $amount *= 100;
        $amount = $exchangeRate->convert($amount, 'USD', 'EUR', now());
        $amount /= 100;
    }

    $amount = number_format((float)$amount, 2);
    $amount = str_replace(',', '', $amount);
    return $amount;
}
