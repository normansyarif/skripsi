<?php

function randColor() {
    $colArray = ["78909C", "FDD835", "C0CA33", "689F38", "009688", "00ACC1", "03A9F4", "1976D2", "5C6BC0", "9C27B0"];
    return "#" . $colArray[array_rand($colArray)];
}
