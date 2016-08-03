tinymce.init({
    mode: "specific_textareas",
    editor_selector: "mceEditor",
    language: 'cs',
    auto_focus: true,
    resize: true,
    convert_urls: false,
    plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace visualblocks visualchars media nonbreaking table contextmenu template paste textcolor'],
    toolbar: "styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l ink image | print preview media forecolor backcolor",
});

tinymce.init({
    mode: "specific_textareas",
    editor_selector: "mceEditorLink",
    language: 'cs',
    auto_focus: true,
    resize: true,
    convert_urls: false,
    plugins: "link",
    menubar: false,
    toolbar: "link"
});

tinymce.init({
    mode: "specific_textareas",
    editor_selector: "mceEditorBasic",
    language: 'cs',
    resize: true,
    convert_urls: false,
    plugins: ['code'],
    menubar: false,
    toolbar: 'undo redo | bold | bullist | indent | outdent | removeformat | code',

});


/* Czech initialisation for the jQuery UI date picker plugin. */
/* Written by Tomas Muller (tomas@tomas-muller.net). */
(function(factory) {
    if (typeof define === "function" && define.amd) {

        // AMD. Register as an anonymous module.
        define(["../widgets/datepicker"], factory);
    } else {

        // Browser globals
        factory(jQuery.datepicker);
    }
}(function(datepicker) {

    datepicker.regional.cs = {
        closeText: "Zavřít",
        prevText: "&#x3C;Dříve",
        nextText: "Později&#x3E;",
        currentText: "Nyní",
        monthNames: ["leden", "únor", "březen", "duben", "květen", "červen",
            "červenec", "srpen", "září", "říjen", "listopad", "prosinec"
        ],
        monthNamesShort: ["led", "úno", "bře", "dub", "kvě", "čer",
            "čvc", "srp", "zář", "říj", "lis", "pro"
        ],
        dayNames: ["neděle", "pondělí", "úterý", "středa", "čtvrtek", "pátek", "sobota"],
        dayNamesShort: ["ne", "po", "út", "st", "čt", "pá", "so"],
        dayNamesMin: ["ne", "po", "út", "st", "čt", "pá", "so"],
        weekHeader: "Týd",
        dateFormat: "dd.mm.yy",
        //dateFormat: "yy-mm-dd",
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ""
    };
    datepicker.setDefaults(datepicker.regional.cs);

    return datepicker.regional.cs;

}));




$(function() {
    $.nette.init();
    $("input.date").datepicker($.datepicker.regional["cs"]);
    $('[data-toggle="tooltip"]').tooltip();

});