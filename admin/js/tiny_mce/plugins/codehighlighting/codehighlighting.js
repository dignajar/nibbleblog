function Save_Button_onclick() {
    if (document.getElementById("CodeArea").value == '') {
        tinyMCEPopup.close();
        return false;
    }
    var code = WrapCode(document.getElementById("ProgrammingLangauges").value, document.getElementById("CodeArea").value);
    tinyMCEPopup.execCommand('mceInsertContent', false, code);
    tinyMCEPopup.close();
}

function WrapCode(lang, content) {
    var options = ";";
    if (document.getElementById("nogutter").checked == true)
        options = " gutter: false;";

    if (document.getElementById("collapse").checked == true)
        options = options + " collapse: true;";

    if (document.getElementById("nocontrols").checked == true)
        options = options + " toolbar: false;";

    if (document.getElementById("htmlscript").checked == true)
        options = options + " html-script: true;";

    content = content.replace(/[<]/g, '&lt;');
    content = content.replace(/[>]/g, '&gt;');
   /* content = content.replace(/[&]/g, '&amp;');*/

    return "<pre class='brush: " + lang + options + "'>" + content + "</pre>";
}

function Cancel_Button_onclick() {
    tinyMCEPopup.close();
    return false;
}