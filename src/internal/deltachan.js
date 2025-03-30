function selectThemeFromPicker() {
    setTheme(document.getElementById("theme_selector").value);
}

function setTheme(themeName) {
    document.cookie = "theme=" + themeName + "; path=/; SameSite=Strict";

    location.reload();
}

function highlight(id)
{
    post = document.getElementById("post_" + id);
    post.classList.add("post_highlighted");
}

function unhighlight(id)
{
    post = document.getElementById("post_" + id);

    if (!post.classList.contains("post_highlighted_by_click"))
        post.classList.remove("post_highlighted");
}

function scroll_to_post(id)
{
    post = document.getElementById("post_" + id);
    post.scrollIntoView();
}

function clear_file_upload()
{
    file = document.querySelector(".file_upload");
    file.value = null;
}

function expand_image(id)
{
    image = document.getElementById("post_image_" + id);

    full_image = image.getAttribute("full_size_image");
    thumbnail = image.getAttribute("thumbnail_image");

    console.log(image.src)
    console.log(thumbnail)
    
    if (image.src.endsWith(thumbnail))
    {
        image.src = full_image;
        image.classList.remove("post_attachment");
        image.classList.add("post_attachment_expand");
    }
    else
    {   
        image.src = thumbnail;
        image.classList.add("post_attachment");
        image.classList.remove("post_attachment_expand");
    }
}

function hide_thread(id)
{
    post = document.querySelector("#post_" + id + " .post_lower");
    button = document.querySelector("#post_" + id + " button");

    if (post.style.display == "none")
    {
        button.innerText = "[–]"
        post.style.display = "block";
    }
    else
    {
        button.innerText = "[+]";
        post.style.display = "none";
    }
}

function expand_post_field(replyId)
{
    form = document.getElementsByClassName("post_form")[0];
    form.style.display = "block";
}

function hide_post_field(replyId)
{
    let form = document.getElementsByClassName("post_form")[0];
    form.style.display = "none";
}

function reply(id)
{
    expand_post_field();
    let textbox = document.querySelector(".post_form textarea");
    textbox.innerHTML += ">>" + id + "\n";
}

document.getElementsByClassName("post_form")[0].style.display = "none";
document.getElementsByClassName("post_form")[0].classList.add("thread_form");

jsOnly = document.getElementsByClassName("js_only");

for (let item of jsOnly)
{
    item.style.display = "inline-block";
}

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get("reply_field_content") != undefined)
    expand_post_field();