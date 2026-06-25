<?php

function arabicLongDate($date = null)
{
    $date = $date ? strtotime($date) : time();

    $day = (int) date('j', $date);
    $month = (int) date('n', $date);
    $year = date('Y', $date);


    return ar_day($day) . ' من شهر ' . ar_month($month) . ' ' . $year;
}

function arabicShortDate($date = null)
{
    $date = $date ? strtotime($date) : time();

    $day = (int) date('j', $date);
    $month = (int) date('n', $date);
    $year = date('Y', $date);


    return $day . ' ' . ar_month($month) . ' ' . $year;
}

function arabicMY($date = null)
{
    $date = $date ? strtotime($date) : time();

    $month = (int) date('n', $date);
    $year = date('Y', $date);

    return ar_month($month) . ' ' . $year;
}

function ar_day($day)
{
    if ($day == 1) {
        return "الأول";
    } elseif ($day == 2) {
        return "الثاني";
    } elseif ($day == 3) {
        return "الثالث";
    } elseif ($day == 4) {
        return "الرابع";
    } elseif ($day == 5) {
        return "الخامس";
    } elseif ($day == 6) {
        return "السادس";
    } elseif ($day == 7) {
        return "السابع";
    } elseif ($day == 8) {
        return "الثامن";
    } elseif ($day == 9) {
        return "التاسع";
    } elseif ($day == 10) {
        return "العاشر";
    } elseif ($day == 11) {
        return "الحادي عشر";
    } elseif ($day == 12) {
        return "الثاني عشر";
    } elseif ($day == 13) {
        return "الثالث عشر";
    } elseif ($day == 14) {
        return "الرابع عشر";
    } elseif ($day == 15) {
        return "الخامس عشر";
    } elseif ($day == 16) {
        return "السادس عشر";
    } elseif ($day == 17) {
        return "السابع عشر";
    } elseif ($day == 18) {
        return "الثامن عشر";
    } elseif ($day == 19) {
        return "التاسع عشر";
    } elseif ($day == 20) {
        return "العشرون";
    } elseif ($day == 21) {
        return "الواحد والعشرون";
    } elseif ($day == 22) {
        return "الثاني والعشرون";
    } elseif ($day == 23) {
        return "الثالث والعشرون";
    } elseif ($day == 24) {
        return "الرابع والعشرون";
    } elseif ($day == 25) {
        return "الخامس والعشرون";
    } elseif ($day == 26) {
        return "السادس والعشرون";
    } elseif ($day == 27) {
        return "السابع والعشرون";
    } elseif ($day == 28) {
        return "الثامن والعشرون";
    } elseif ($day == 29) {
        return "التاسع والعشرون";
    } elseif ($day == 30) {
        return "الثلاثون";
    } elseif ($day == 31) {
        return "الواحد والثلاثون";
    }
}

function ar_month($month)
{

    if ($month == 1) {
        return "يناير";
    } elseif ($month == 2) {
        return "فبراير";
    } elseif ($month == 3) {
        return "مارس";
    } elseif ($month == 4) {
        return "ابريل";
    } elseif ($month == 5) {
        return "مايو";
    } elseif ($month == 6) {
        return "يونيو";
    } elseif ($month == 7) {
        return "يوليو";
    } elseif ($month == 8) {
        return "أغسطس";
    } elseif ($month == 9) {
        return "سبتمبر";
    } elseif ($month == 10) {
        return "أكتوبر";
    } elseif ($month == 11) {
        return "نوفمبر";
    } elseif ($month == 12) {
        return "ديسمبر";
    }
}

use NumberToWords\NumberToWords;

function numberToArabicWords($number)
{
    $numberToWords = new NumberToWords();

    $transformer = $numberToWords->getNumberTransformer('ar');

    return $transformer->toWords($number);
}

function numberToEnglishWords($number)
{
    $numberToWords = new NumberToWords();

    $transformer = $numberToWords->getNumberTransformer('en');

    return Str::title($transformer->toWords($number));
}

function toRoman(int $number): string
{
    $map = [
        'M'  => 1000,
        'CM' => 900,
        'D'  => 500,
        'CD' => 400,
        'C'  => 100,
        'XC' => 90,
        'L'  => 50,
        'XL' => 40,
        'X'  => 10,
        'IX' => 9,
        'V'  => 5,
        'IV' => 4,
        'I'  => 1,
    ];

    $result = '';

    foreach ($map as $roman => $value) {
        while ($number >= $value) {
            $result .= $roman;
            $number -= $value;
        }
    }

    return $result;
}


function toarabicLetterNumber(int $number): string
{
    $letters = [
        1 => 'أ',
        2 => 'ب',
        3 => 'ج',
        4 => 'د',
        5 => 'هـ',
        6 => 'و',
        7 => 'ز',
        8 => 'ح',
        9 => 'ط',
        10 => 'ي',
        11 => 'ك',
        12 => 'ل',
        13 => 'م',
        14 => 'ن',
        15 => 'س',
        16 => 'ع',
        17 => 'ف',
        18 => 'ص',
        19 => 'ق',
        20 => 'ر',
    ];

    return $letters[$number] ?? $number;
}
