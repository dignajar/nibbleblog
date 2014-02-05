function toggleTags(element) {
var list = element.parentNode.getElementsByClassName('tag_list')[0];
if (element.className == 'tag_button')
    {
        // Show tags
        element.className = 'tag_button_open';
        list.style.height = '100px';
        list.style.overflow = 'vertical';
        list.style.paddingTop = '1.5em';
        list.style.paddingBottom = '1.5em';
    }
else
    {
        // Hide tags
        element.className = 'tag_button';
        list.style.height = '0';
        list.style.overflow = 'hidden';
        list.style.paddingTop = '0';
        list.style.paddingBottom = '0';
    }
}
