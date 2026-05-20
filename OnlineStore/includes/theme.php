<?php
function get_pref($name, $default)
{
    return $_COOKIE[$name] ?? $default;
}

function set_pref_cookie($name, $value)
{
    setcookie($name, $value, time() + (86400 * 30), "/");
    $_COOKIE[$name] = $value;
}

function get_theme_settings()
{
    $theme = get_pref("theme_mode", "light");
    $fontSize = get_pref("font_size", "17");

    $palettes = array(
        "light" => array("bg" => "#e8ede1", "panel" => "#fbfcf7", "text" => "#273126", "accent" => "#6b7d3a", "soft" => "#ccd6b8"),
        "dark" => array("bg" => "#20261b", "panel" => "#2c3526", "text" => "#edf2e5", "accent" => "#a8bc6a", "soft" => "#4e5d42"),
        "sage" => array("bg" => "#eef1e6", "panel" => "#fcfdf8", "text" => "#2b3528", "accent" => "#7b8f49", "soft" => "#d8e0c6")
    );

    if (!isset($palettes[$theme])) {
        $theme = "light";
    }

    return array(
        "theme" => $theme,
        "font_size" => max(14, min(22, (int) $fontSize)),
        "palette" => $palettes[$theme]
    );
}
?>
