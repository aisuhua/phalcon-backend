{% include 'partials/nav.volt' %}

<div id="page-wrapper">
    <div class="container-fluid">
        <div id="pjax-container">
            {{ content() }}
        </div>
    </div>
</div>

{% include 'partials/footer.volt' %}