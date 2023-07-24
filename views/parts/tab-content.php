<script>
    function openTab(evt) {
        console.log(evt);
        var tabId = evt.target.dataset.id;
        var i, tabcontent, tablinks;

        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        document.getElementById(tabId).style.display = "block";
        evt.target.className += " active";
    }

    document.addEventListener("DOMContentLoaded", function () {
        $('[data-id=tab_content]').click();
    });

</script>
<div class="tab">
    <button class="tablinks" data-id="tab_content" onclick="openTab(event)"><?= is_null($model->id) ? "Новая страница" : "Редактировать страницу" ?></button>
    <!--<button class="tablinks" data-id="tab_metadata" onclick="openTab(event)">Метаданные</button>-->
</div>
