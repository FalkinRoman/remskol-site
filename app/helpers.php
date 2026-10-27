<?php

if (! function_exists('nb')) {
    /**
     * Неразрывные пробелы по правилам русской типографики:
     * короткие слова (предлоги, союзы, частицы) не остаются в конце строки,
     * число не отрывается от единицы, тире не переносится в начало строки.
     */
    function nb(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $nbsp = "\u{00A0}";

        $text = preg_replace('/(?<=^|[\s«(])([а-яёА-ЯЁa-zA-Z]{1,3}) (?=\S)/u', '$1'.$nbsp, $text);
        $text = preg_replace('/(?<=^|[\s«(])([а-яёА-ЯЁa-zA-Z]{1,3}) (?=\S)/u', '$1'.$nbsp, $text);
        $text = preg_replace('/(\d) (?=[а-яёА-ЯЁ%₽]|руб|мм|см|мин|лет|год)/u', '$1'.$nbsp, $text);
        $text = preg_replace('/(\d) (?=\d{3}\b)/u', '$1'.$nbsp, $text);
        $text = preg_replace('/ ([—–])/u', $nbsp.'$1', $text);
        $text = preg_replace('/ (же|ли|бы|ведь)(?=[\s,.!?]|$)/u', $nbsp.'$1', $text);

        return $text;
    }
}
