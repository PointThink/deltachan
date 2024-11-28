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
