function selectThemeFromPicker() {
    setTheme(document.getElementById("theme_selector").value);
}

function setTheme(themeName) {
    document.cookie = "theme=" + themeName + "; path=/; SameSite=Strict";

    location.reload();
}
