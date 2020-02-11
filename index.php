<?php
/**
 * Minimal Calendar
 *
 * Print-optimised minimal calendar layout for any year. Run on any given day of the year,
 * will produce an HTML page with a full year calendar presented in a minimal layout,
 * optimised and ready for printing.
 *
 * Based on an idea by @nickolaspeter
 * @link https://www.kickstarter.com/projects/nickolaspeter/a-hyper-minimal-calendar-for-2020
 *
 * @author @the_chopstik |/ https://chopstik.net
 * @license http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 *
 */

require 'vendor/autoload.php';

use League\Period\Duration;
use League\Period\Period;
use League\Period\Datepoint;

$currentDate = Datepoint::create('now');
$yearPeriod = Period::fromYear($currentDate->format('Y'))->getDatePeriod('1 DAY');
$pageSize = isset($_GET['size']) ? $_GET['size'] : 4; // as in A0: 841mm x 1189mm, A1, A2, A3 or A4: 210mm x 297mm
$baseFontSizes = [
    0 => '30px',
    1 => '25px',
    2 => '20px',
    3 => '15px',
    4 => '10px',
];

?>

<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="UTF-8">
    <title>minimal-calendar-<?= $currentDate->format('Y').'-A'.$pageSize ?></title>
    <meta name="description" content="A print-ready minimal calendar layout">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        html{
            font-size: <?= $baseFontSizes[$pageSize] ?>;
        }
        .minimal-calendar {
            width: 100%;
            font-family: 'Cambay', sans-serif;
            font-weight: 400;
        }
    </style>
    <link href="https://fonts.googleapis.com/css?family=Cambay:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">

</head>
<body>
<table class="minimal-calendar">
    <tbody>
    <tr>
        <td></td>
        <td></td>
        <td class="week-abbr">M</td>
        <td class="week-abbr">T</td>
        <td class="week-abbr">W</td>
        <td class="week-abbr">Th</td>
        <td class="week-abbr">F</td>
        <td class="week-abbr weekend">S</td>
        <td class="week-abbr weekend">S</td>
        <td class="week-abbr">M</td>
        <td class="week-abbr">T</td>
        <td class="week-abbr">W</td>
        <td class="week-abbr">Th</td>
        <td class="week-abbr">F</td>
        <td class="week-abbr">S</td>
        <td class="week-abbr">S</td>
        <td></td>
    </tr>
    <?php

    $columns = 17; // 0 indexed, 17 actual columns
    $row = 1;
    $col = 1;
    $table = [];

    $quarterStartDates = [
        1 => '2020-01-01',
        2 => '2020-04-01',
        3 => '2020-07-01',
        4 => '2020-10-01',
    ];

    foreach ($yearPeriod as $day) {

        /** @var \DateTime $day */

        $currentDayOfYearNumber = (int)$day->format('z');
        $currentDayOfMonth = (int)$day->format('j');
        $currentDayOfWeekNumber = (int)$day->format('N');
        $currentMonthNumber = (int)$day->format('n');
        $isFirstDayYear = ($currentDayOfYearNumber === 0);
        $isLastDayYear = ($currentDayOfYearNumber === 365);
        $isFirstDayMonth = ($currentDayOfMonth === 1);
        $isWeekend = ($currentDayOfWeekNumber >= 6);
        $dateNow = $day->format('Y-m-d');
        $tdClass = 'day';

        if ($isWeekend) {
            $tdClass .= ' weekend';
        };

        // the first cell should be blank or have the quarter
        if ($col == 1) {
            $table[$row][$col] = '<td></td>';
            $col++;
        }

        if (in_array($dateNow, $quarterStartDates)) {
            $table[$row][1] = '<td class="quarter">Q' . (string)array_search($dateNow, $quarterStartDates) . '</td>';
        }

        // the second cell should be blank or have the month
        if ($col == 2) {
            $table[$row][$col] = '<td></td>';
            $col++;
        }
        if ($isFirstDayMonth) {
            $table[$row][2] = '<td class="month">' . $day->format('M') . '</td>';
        }


        // deal with first day of the year
        if ($isFirstDayYear) {
            if ($currentDayOfWeekNumber > 1) { // say 3, a Wednesday
                for ($x = 1; $x < $currentDayOfWeekNumber; $x++) { // loop twice.
                    $table[$row][$col] = '<td class="day"></td>';
                    $col++;
                }
            }
        }

        // add a normal day
        if ($isFirstDayMonth) {
            $table[$row][$col] = '<td class="' . $tdClass . ' first-day-month"><div>' . $day->format('j') . '</div></td>';
        } else {
            $table[$row][$col] = '<td class="' . $tdClass . '">' . $day->format('j') . '</td>';
        }
        $col++;

        if ($col == $columns) {
            $row++; // increment the row count
            $col = 1; // reset columns as we start another row
        }

    }

    foreach ($table as $row => $cells) {
        echo '<tr>';
        foreach ($cells as $cell) {
            echo $cell;
        }
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
<div class="minimal-calendar year-wrapper"><span class="year"><?= $day->format('y') ?></span></div>
<div class="minimal-calendar credit-wrapper"><span class="credit">made by <a href="https://twitter.com/the_chopstik">@the_chopstik</a> |/ <a href="https://github.com/chopstik/minimal-calendar">https://github.com/chopstik/minimal-calendar</a></span></div>
</body>
</html>
