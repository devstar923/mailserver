@extends('layouts.app')
<title>ACCOUNT : EDIT TEMPLATE</title>
@section('customStyle')
<link rel="stylesheet" href="{{ URL::asset('public/assets/libs/dist/builder.css') }}">
@endsection
@section('content')
<div class="content">
    <div class="sub-header">
        Edit Template
    </div>
    <!-- <div class="content-tool mt-3 mb-4">
        <a href="{{ url('contact/import') }}">
            <button class="btn-form-primary me-4">
                Import Contact
            </button>
        </a>
        <a href="{{ url('contact/create') }}">
            <button class="btn-form-danger text-white">
                + Add Contact
            </button>
        </a>
    </div> -->

    <div class="contact-content mt-2">
        <div style="text-align: center;
            height: 100vh;
            vertical-align: middle;
            padding: auto;
            display: flex;">
            <div style="margin:auto" class="lds-dual-ring"></div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    var editor;
    var params = new URLSearchParams(window.location.search);
    var templates = [{
            "name": "Blank",
            "url": "design.php?id=6037a0a8583a7&type=default",
            "thumbnail": "templates\/default\/6037a0a8583a7\/thumb.png"
        },
        {
            "name": "Pricing Table",
            "url": "design.php?id=6037a2135b974&type=default",
            "thumbnail": "templates\/default\/6037a2135b974\/thumb.png"
        },
        {
            "name": "Listing & Tables",
            "url": "design.php?id=6037a2250a3a3&type=default",
            "thumbnail": "templates\/default\/6037a2250a3a3\/thumb.png"
        },
        {
            "name": "Forms Building",
            "url": "design.php?id=6037a23568208&type=default",
            "thumbnail": "templates\/default\/6037a23568208\/thumb.png"
        },
        {
            "name": "1-2-1 column layout",
            "url": "design.php?id=6037a2401b055&type=default",
            "thumbnail": "templates\/default\/6037a2401b055\/thumb.png"
        },
        {
            "name": "1-2 column layout",
            "url": "design.php?id=6037a24ebdbd6&type=default",
            "thumbnail": "templates\/default\/6037a24ebdbd6\/thumb.png"
        },
        {
            "name": "1-3-1 column layout",
            "url": "design.php?id=6037a25ddce80&type=default",
            "thumbnail": "templates\/default\/6037a25ddce80\/thumb.png"
        },
        {
            "name": "1-3-2 column layout",
            "url": "design.php?id=6037a26b0a286&type=default",
            "thumbnail": "templates\/default\/6037a26b0a286\/thumb.png"
        },
        {
            "name": "1-3 column layout",
            "url": "design.php?id=6037a275bf375&type=default",
            "thumbnail": "templates\/default\/6037a275bf375\/thumb.png"
        },
        {
            "name": "One column layout",
            "url": "design.php?id=6037a28418c95&type=default",
            "thumbnail": "templates\/default\/6037a28418c95\/thumb.png"
        },
        {
            "name": "2-1-2 column layout",
            "url": "design.php?id=6037a29a35e05&type=default",
            "thumbnail": "templates\/default\/6037a29a35e05\/thumb.png"
        },
        {
            "name": "2-1 column layout",
            "url": "design.php?id=6037a2aa315d4&type=default",
            "thumbnail": "templates\/default\/6037a2aa315d4\/thumb.png"
        },
        {
            "name": "Two columns layout",
            "url": "design.php?id=6037a2b67ed27&type=default",
            "thumbnail": "templates\/default\/6037a2b67ed27\/thumb.png"
        },
        {
            "name": "3-1-3 column layout",
            "url": "design.php?id=6037a2c3d7fa1&type=default",
            "thumbnail": "templates\/default\/6037a2c3d7fa1\/thumb.png"
        },
        {
            "name": "Three columns layout",
            "url": "design.php?id=6037a2dcb6c56&type=default",
            "thumbnail": "templates\/default\/6037a2dcb6c56\/thumb.png"
        }
    ];

    var tags = [{
            type: 'label',
            tag: '{CONTACT_FIRST_NAME}'
        },
        {
            type: 'label',
            tag: '{CONTACT_LAST_NAME}'
        },
        {
            type: 'label',
            tag: '{CONTACT_FULL_NAME}'
        },
        {
            type: 'label',
            tag: '{CONTACT_EMAIL}'
        },
        {
            type: 'label',
            tag: '{CONTACT_PHONE}'
        },
        {
            type: 'label',
            tag: '{CONTACT_ADDRESS}'
        },
        {
            type: 'label',
            tag: '{ORDER_ID}'
        },
        {
            type: 'label',
            tag: '{ORDER_DUE}'
        },
        {
            type: 'label',
            tag: '{ORDER_TAX}'
        },
        {
            type: 'label',
            tag: '{PRODUCT_NAME}'
        },
        {
            type: 'label',
            tag: '{PRODUCT_PRICE}'
        },
        {
            type: 'label',
            tag: '{PRODUCT_QTY}'
        },
        {
            type: 'label',
            tag: '{PRODUCT_SKU}'
        },
        {
            type: 'label',
            tag: '{AGENT_NAME}'
        },
        {
            type: 'label',
            tag: '{AGENT_SIGNATURE}'
        },
        {
            type: 'label',
            tag: '{AGENT_MOBILE_PHONE}'
        },
        {
            type: 'label',
            tag: '{AGENT_ADDRESS}'
        },
        {
            type: 'label',
            tag: '{AGENT_WEBSITE}'
        },
        {
            type: 'label',
            tag: '{AGENT_DISCLAIMER}'
        },
        {
            type: 'label',
            tag: '{CURRENT_DATE}'
        },
        {
            type: 'label',
            tag: '{CURRENT_MONTH}'
        },
        {
            type: 'label',
            tag: '{CURRENT_YEAR}'
        },
        {
            type: 'button',
            tag: '{PERFORM_CHECKOUT}',
            'text': 'Checkout'
        },
        {
            type: 'button',
            tag: '{PERFORM_OPTIN}',
            'text': 'Subscribe'
        }
    ];

    $(document).ready(function () {
        var strict = true;

        if (params.get('type') == 'custom') {
            strict = false;
        }

        editor = new Editor({
            strict: strict, // default == true
            showInlineToolbar: true, // default == true
            root: "{{ URL::asset('public/assets/libs/dist') }}",
            url: "{{ 'public/templates/'. $type. '/'. $id }}",
            urlBack: "{{ url('template') }}",
            uploadAssetUrl: 'asset.php',
            uploadAssetMethod: 'POST',
            uploadTemplateUrl: 'upload.php',
            uploadTemplateCallback: function (response) {
                window.location = response.url;
            },
            saveUrl: 'save.php',
            saveMethod: 'POST',
            data: {
                _token: 'CSRF_TOKEN',
                type: "{{ $type }}",
                template_id: "{{ $id }}"
            },
            templates: templates,
            tags: tags,
            changeTemplateCallback: function (url) {
                window.location = url;
            },

            /*
                Disable features: 
                change_template|export|save_close|footer_exit|help
            */
            // disableFeatures: [ 'change_template', 'export', 'save_close', 'footer_exit', 'help' ], 

            // disableWidgets: [ 'HeaderBlockWidget' ], // disable widgets
            export: {
                url: 'export.php'
            },
            backgrounds: [
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images1.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images2.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images3.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images4.png') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images5.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images6.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images9.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images11.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images12.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images13.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images14.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images15.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images16.jpg') }}",
                "{{ URL::asset('public/assets/builder/assets/image/backgrounds/images17.png') }}",
            ]
        });

        editor.init();
    });

</script>
@endsection
